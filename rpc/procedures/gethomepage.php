<?php

function rpc_getHomepage($xml, $result, $args) {
	global $url_homepage;
	$homepage = get_request_cookie($url_homepage, "sid={$args->sid}");
	$doc = phpQuery::newDocument($homepage);
	addToCache($url_homepage, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	$config = getHomepageConfiguration($homepage);
	$data = array(
		'content'	=> $homepage,
		'config'	=> $config,
		'hasthreads'	=> false
	);

	$modules = $xml->createElement('modules');
	foreach($config as $module)
		$modules->appendChild($xml->createElement('module', $module));
	$result->appendChild($modules);

	if(
	    (array_search('newsest', $config) !== false)
	    || (array_search('noreply', $config) !== false)) {
		$data['hasthreads'] = true;
		$data['threads'] = getHomepageThreads($xml, $result, $doc);
	}

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
	if(strpos($homepage, '<h3>Ber&uuml;hmt f&uuml;r 15 Minuten</h3>') !== false)
		$elements[] = 'famous';
	if(strpos($homepage, '<h3>Meine Statistik</h3>') !== false)
		$elements[] = 'statistic';
	if(strpos($homepage, '<h3 class="birthdays">Geburtstage</h3>') !== false)
		$elements[] = 'birthdays';
	if(strpos($homepage, '<h3>Freundehistory <a href="/feeds/friends-') !== false)
		$elements[] = 'friendhistory';
	if(strpos($homepage, '<h3>Meine privaten Notizen</h3>') !== false)
		$elements[] = 'notices';
	if(strpos($homepage, '<h3 class="boxCaption">Neueste Anmeldungen</h3>') !== false)
		$elements[] = 'lastregistrations';
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

xmlrpc_register_function(
	'getHomepage',
	array('sid'),
	'rpc_getHomepage'
);
