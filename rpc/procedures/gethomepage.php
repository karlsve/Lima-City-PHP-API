<?php

function rpc_getHomepage($xml, $result, $args) {
	global $url_homepage;
	$homepage = get_request_cookie($url_homepage, "auth_token_session={$args->sid}");
	$doc = phpQuery::newDocument($homepage);
	addToCache($url_homepage, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	$config = getHomepageConfiguration($homepage);

	$modules = $xml->createElement('modules');
	foreach($config as $module)
		$modules->appendChild($xml->createElement('module', $module));
	$result->appendChild($modules);

	if(in_array('newest', $config) || in_array('noreply', $config))
		getHomepageThreads($xml, $result, $doc);

	if(in_array('famous', $config))
		getFamous($xml, $result, $doc);

	if(in_array('statistics', $config))
		getStatistics($xml, $result, $doc);

	if(in_array('visits', $config))
		getLastVisits($xml, $result, $doc);

	if(in_array('registrations', $config))
		getRegistrations($xml, $result, $doc);

	return $result;
}

function getHomepageConfiguration($homepage) {
	$elements = array();
	if(strpos($homepage, '<h3 class="lastposts">Neueste Beitr&auml;ge</h3>') !== false)
		$elements[] = 'newest';
	if(strpos($homepage, '<h3 class="lastposts">Neueste unbeantwortete Themen</h3>') !== false)
		$elements[] = 'noreply';
	if(strpos($homepage, '<h3 class="lastvisits">Letzte Besucher meines Profils</h3>') !== false)
		$elements[] = 'visits';
	if(strpos($homepage, '<h3>Ber&uuml;hmt f&uuml;r 15 Minuten <a href="/famous" title="Liste der Ber&uuml;hmten"><img src="images/layout/icons/table.png" alt="Liste der Ber&uuml;hmten" /></a></h3>') !== false)
		$elements[] = 'famous';
	if(strpos($homepage, '<h3>Meine Statistik</h3>') !== false)
		$elements[] = 'statistics';
	if(strpos($homepage, '<h3 class="birthdays">Geburtstage</h3>') !== false)
		$elements[] = 'birthdays';
	if(strpos($homepage, '<h3>Freundehistory <a href="/feeds/friends-') !== false)
		$elements[] = 'friendhistory';
	if(strpos($homepage, '<h3>Meine privaten Notizen</h3>') !== false)
		$elements[] = 'notices';
	if(strpos($homepage, '<h3 class="boxCaption">Neueste Anmeldungen</h3>') !== false)
		$elements[] = 'registrations';
	return($elements);
}

function getHomepageThreads($xml, $result, $doc) {
	foreach($doc->find('h3.lastposts') as $h3) {
		$type = 'unknown';
		switch(utf8_decode($h3->nodeValue)) {
			case 'Neueste Beiträge':
				$type = 'newest';
				break;
			case 'Neueste unbeantwortete Themen':
				$type = 'noreply';
				break;
		}
		if($type == 'unknown')
			continue;
		$node = $xml->createElement($type);

		$content = $h3->nextSibling;
		while(($content->nodeType == XML_TEXT_NODE) && ($content->nextSibling !== NULL))
			$content = $content->nextSibling;

		$content = pq($content);

		foreach($content->find('li') as $entry) {
			$entry = pq($entry);
			$threadlink = $entry->find('h5 a');
			$name = $threadlink->html();
			if($name == '')
				continue;
			$url = substr($threadlink->attr('href'), 8);
			$date = $entry->find('small')->html();
			$board = $entry->find('a[href^="/board/"]:not([href^="/board/action:jump/"])')->html();
			$boardurl = substr($entry->find('a[href^="/board/"]:not([href^="/board/action:jump/"])')->attr('href'), 7);
			$postid = substr($entry->find('a[href^="/board/action:jump/"]')->attr('href'), 19);
			$user = $entry->find('a[href^="/profile/"]')->html();
			$closed = false;
			$fixed = false;
			$important = false;
			foreach($entry->find('ul.badges img') as $img) {
				$alt = pq($img)->attr('alt');
				switch($alt) {
					case 'geschlossen':
						$closed = true;
						break;
					case 'fixiert':
						$fixed = true;
						break;
					case 'wichtig':
						$important = true;
						break;
				}
			}

			$thread = $xml->createElement('thread');

			$flags = $xml->createElement('flags');
			$xmlimportant = $xml->createElement('important');
			$xmlfixed = $xml->createElement('fixed');
			$xmlclosed = $xml->createElement('closed');
			$xmlimportant->appendChild($xml->createTextNode($important ? 'true' : 'false'));
			$xmlfixed->appendChild($xml->createTextNode($fixed ? 'true' : 'false'));
			$xmlclosed->appendChild($xml->createTextNode($closed ? 'true' : 'false'));
			$flags->appendChild($xmlimportant);
			$flags->appendChild($xmlfixed);
			$flags->appendChild($xmlclosed);
			$thread->appendChild($flags);

			$forum = $xml->createElement('forum', $board);
			$forumurl = $xml->createAttribute('url');
			$forumurl->appendChild($xml->createTextNode($boardurl));
			$forum->appendChild($forumurl);

			$thread->appendChild($xml->createElement('name', $name));
			$thread->appendChild($xml->createElement('url', $url));
			$thread->appendChild($xml->createElement('postid', $postid));
			$thread->appendChild($xml->createElement('date', $date));
			$thread->appendChild($forum);
			$thread->appendChild($xml->createElement('user', $user));
			$node->appendChild($thread);
		}

		$result->appendChild($node);
	}
	return $result;
}

function getFamous($xml, $result, $doc) {
	$box = $doc->find('h3:has(a[href="/famous"])')->next('ul.boxes:first-child-of-type');
	$user = $box->find('li:nth-child(1)');
	$group = $box->find('li:nth-child(2)');
	$domain = $box->find('li:nth-child(3)');

	$username = $user->find('a')->html();
	$stars = $user->find('img:not(.profileViewer)');
	$userrole = getRole(pq($stars->get(0))->attr('alt'));
	if($userrole == 'Benutzer')
		$userstars = getStars($stars);
	$tokens = explode(' ', $user->find('span small'));
	$gulden = $tokens[3];

	$groupname = $group->find('a')->html();
	$groupurl = substr($group->find('a')->attr('href'), 8);
	$tokens = explode(' ', $group->find('span small'));
	$groupmembers = $tokens[3];

	$domainname = $domain->find('> a')->html();
	$domainowner = $domain->find('span small a')->html();

	$node = $xml->createElement('famous');

	$usernode = $xml->createElement('user');
	$usernode->appendChild($xml->createElement('name', $username));
	$usernode->appendchild($xml->createElement('role', $userrole));
	$usernode->appendChild($xml->createElement('gulden', $gulden));
	if($userrole == 'Benutzer') {
		$s = $xml->createElement('stars');
		$s->appendChild($xml->createElement('count', $userstars->count));
		$s->appendChild($xml->createElement('color', $userstars->type));
		$usernode->appendChild($s);
	}
	$node->appendChild($usernode);

	$groupnode = $xml->createElement('group');
	$groupnode->appendChild($xml->createElement('name', $groupname));
	$groupnode->appendChild($xml->createElement('url', $groupurl));
	$groupnode->appendChild($xml->createElement('members', $groupmembers));
	$node->appendChild($groupnode);

	$domainnode = $xml->createElement('domain');
	$domainnode->appendChild($xml->createElement('name', $domainname));
	$domainnode->appendChild($xml->createElement('owner', $domainowner));
	$node->appendChild($domainnode);

	$result->appendChild($node);
	return $result;
}

function getStatistics($xml, $result, $doc) {
	$box = $doc->find('h3:contains("Meine Statistik")')->next('ul.boxes.myStats:first-child-of-type');
	$gulden_total = $box->find('li:nth-child(1) div.myStat')->text();
	$gulden_today = $box->find('li:nth-child(2) div.myStat')->text();
	$gulden_avail = $box->find('li:nth-child(3) div.myStat')->text();
	$count_posts = $box->find('li:nth-child(4) div.myStat')->text();
	$count_threads = $box->find('li:nth-child(5) div.myStat')->text();
	$count_friends = $box->find('li:nth-child(6) div.myStat')->text();
	$count_groups = $box->find('li:nth-child(7) div.myStat')->text();
	$count_gb_received = $box->find('li:nth-child(8) div.myStat')->text();
	$count_gb_written = $box->find('li:nth-child(9) div.myStat')->text();

	$node = $xml->createElement('statistics');
	$g = $xml->createElement('gulden');
	$g->appendChild($xml->createElement('total', $gulden_total));
	$g->appendChild($xml->createElement('today', $gulden_today));
	$g->appendChild($xml->createElement('available', $gulden_avail));
	$node->appendChild($g);
	$c = $xml->createElement('counts');
	$c->appendChild($xml->createElement('posts', $count_posts));
	$c->appendChild($xml->createElement('threads', $count_threads));
	$c->appendChild($xml->createElement('friends', $count_friends));
	$c->appendChild($xml->createElement('groups', $count_groups));
	$c->appendChild($xml->createElement('guestbook-received', $count_gb_received));
	$c->appendChild($xml->createElement('guestbook-written', $count_gb_written));
	$node->appendChild($c);
	$result->appendChild($node);
	return $result;
}

function getLastVisits($xml, $result, $doc) {
	$box = $doc->find('h3.lastvisits:contains("Letzte Besucher meines Profils")')->next('ul.boxes:first-child-of-type');
	$visits = $xml->createElement('last-visits');
	foreach($box->find('li.lastViewer') as $viewer) {
		$viewer = pq($viewer);
		$name = $viewer->find('a')->html();
		$stars = $viewer->find('img:not([alt="Avatar"])');
		$time = $viewer->find('span small')->html();
		$role = $stars->count() == 0 ? 'Benutzer' : getRole(pq($stars->get(0))->attr('alt'));
		$node = $xml->createElement('user');
		$node->appendChild($xml->createElement('name', $name));
		$node->appendChild($xml->createElement('role', $role));
		if(($stars->count() != 0) && ($role == 'Benutzer')) {
			$info = getStars($stars);
			$s = $xml->createElement('stars');
			$s->appendChild($xml->createElement('count', $info->count));
			$s->appendChild($xml->createElement('color', $info->type));
			$node->appendChild($s);
		}
		$node->appendChild($xml->createElement('time', $time));
		$visits->appendChild($node);
	}
	$result->appendChild($visits);
	return $result;
}

function getRegistrations($xml, $result, $doc) {
	$box = $doc->find('h3.boxCaption:contains("Neueste Anmeldungen")')->next('ul.boxes.lastRegisters:first-child-of-type');
	$registrations = $xml->createElement('registrations');
	foreach($box->find('li') as $user) {
		$user = pq($user);
		$name = $user->find('a')->html();
		$date = $user->find('div')->html();
		$node = $xml->createElement('user');
		$node->appendChild($xml->createElement('name', $name));
		$node->appendChild($xml->createElement('date', $date));
		$registrations->appendChild($node);
	}
	$result->appendChild($registrations);
	return $result;
}

xmlrpc_register_function('getHomepage', array('sid'), 'rpc_getHomepage');
