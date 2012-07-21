<?php

function getUser($xml, $sid, $username, $exclude = array()) {
	global $url_profile;

	$doc = phpQuery::newDocument(get_request_cookie($url_profile . '/' . urlencode($username) . '/page%3A0/perpage%3A100', "sid=$sid"));
	$doc->find('script')->remove();

	$user = $xml->createElement('user');
	$user->appendChild($xml->createElement('name', $username));

	if(!in_array('profile', $exclude))
		$user->appendChild(getProfile($xml, $doc));
	if(!in_array('friends', $exclude))
		$user->appendChild(getFriends($xml, $doc));
	if(!in_array('groups', $exclude))
		$user->appendChild(getGroups($xml, $doc));
	if(!in_array('guestbook', $exclude))
		$user->appendChild(getGuestbook($xml, $doc));

	return $user;
}

function getProfile($xml, $doc) {
	$profile = $xml->createElement('profile');

	$about = $doc->find('div#tabAbout');
	dom2plaintext($about);
	foreach($about->find('dt') as $aboutDT) {
		$aboutDT = pq($aboutDT);
		$aboutDD = pq($aboutDT->next('dd'));
		$key = format_xml_tag($aboutDT->html());
		$content = trim($aboutDD->html());
		if(!empty($content)) {
			$xmlcontent = $xml->createElement($key, $content);
			$profile->appendChild($xmlcontent);
		}
	}
	return $profile;
}

function getFriends($xml, $doc) {
	$buddies = $doc->find('div#tabBuddies ul:not(.actions)');
	dom2plaintext($buddies);
	$friends = $xml->createElement('friends');
	foreach($buddies->find('li') as $buddy) {
		$buddy = pq($buddy);
		$content = trim($buddy->html());
		$friend = $xml->createElement('friend', $content);
		$friends->appendChild($friend);
	}
	return $friends;
}

function getGroups($xml, $doc) {
	$groups = $doc->find('div#tabGroups');
	dom2plaintext($groups);
	$groupsxml = $xml->createElement('groups');
	foreach($groups->find('li') as $group) {
		$group = pq($group);
		$content = trim($group->html());
		$groupxml = $xml->createElement('group', $content);
		$groupsxml->appendChild($groupxml);
	}
	return $groupsxml;
}

function getGuestbook($xml, $doc) {
	$guestbookxml = $xml->createElement('guestbook');
	$guestbook = $doc->find('ol#guestbook');
	$pieces = array(
		0 => 'name',
		1 => 'none',
		2 => 'gulden',
		3 => 'none'
		);

	$guestbook->find('ul.actions')->remove();
	$guestbook->find('div.replyBox')->remove();
	$guestbook->find('p.successBox')->remove();

	foreach($guestbook->find('li') as $entry) {
		$guestbookentry = $xml->createElement('entry');
		$guestbookxml->appendChild($guestbookentry);

		$entry = pq($entry);

		$authordiv = $entry->find('div.author');
		$author = $authordiv->find('p.un');
		$date = $authordiv->find('small');
		dom2plaintext($date);
		$datexml = $xml->createElement('date', trim($date->html()));
		$guestbookentry->appendChild($datexml);

		$authorxml = $xml->createElement('author');
		$guestbookentry->appendChild($authorxml);

		$signature = pq($entry->find('div.signature'));
		$left = $signature->find('div.left');
		$right = $signature->find('div.right');
		$sig = '';
		if($left)
			$sig = $left->html();
		if($right)
			$sig .= ' ' . $right->html();
		$sig = trim($sig);

		$signature->remove();

		$j = 0;
		foreach($entry->find('p:not(.sucessBox)') as $piece) {
			$piece = pq($piece);
			dom2plaintext($piece);
			if($pieces[$j] != 'none') {
				if($pieces[$j] == 'name') {
					$name = trim($piece->html());
					$deleted = 'false';
					if(preg_match('|<del>(.*?)</del>|', $name, $match)) {
						$name = $match[1];
						$deleted = 'true';
					}
					$piece = $xml->createElement($pieces[$j], $name);
					$del = $xml->createAttribute('deleted');
					$del->appendChild($xml->createTextNode($deleted));
					$authorxml->appendChild($del);
				} else
					$piece = $xml->createElement($pieces[$j], trim($piece->html()));
				$authorxml->appendChild($piece);
			}
			$j++;
		}
		$content = $entry->find('div.content');
		$data = trim($content->html());
		if(strlen($sig) != 0)
			$data .= '<br>' . $sig;
		$contentxml = parsePostContent($xml, trim($data));

		//$contentxml = $xml->createElement("content", trim($content->html()));
		$guestbookentry->appendChild($contentxml);
		$guestbookxml->appendChild($guestbookentry);
	}
	return $guestbookxml;
}

function getProfiles($xml, $sid, $filter = 'filter%3Aonline/show%3Aonline/page%3A0/perpage%3A100') {
	global $url_profiles;
	$doc = phpQuery::newDocument(get_request_cookie("$url_profiles/$filter", "sid=$sid"));

	$data = $doc->find('tbody');

	$profiles = $xml->createElement('profiles');

	foreach($data->find('tr') as $row) {
		$row = pq($row);

		$content = array();
		foreach($row->find('td') as $cell)
			$content[] = pq($cell)->html();

		$name = $row->find('a')->html();
		$gulden = trim($content[1]);
		$rang = trim($content[2]);
		$login = trim($content[5]);

		$profile = $xml->createElement('profile');
		$profile->appendChild($xml->createElement('name', $name));
		$profile->appendChild($xml->createElement('gulden', $gulden));
		$profile->appendChild($xml->createElement('rang', $rang));
		$profile->appendChild($xml->createElement('last-login', $login));
		$profiles->appendChild($profile);
	}

	return $profiles;
}

function dom2plaintext($doc) {
	$doc->find('img')->remove();
	foreach($doc->find('a') as $link) {
		$link = pq($link);
		$link->replaceWith($link->html());
	}
}

?>