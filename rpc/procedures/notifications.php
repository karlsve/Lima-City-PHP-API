<?php

function rpc_getNotifications($xml, $result, $args) {
	global $url_homepage;
	$doc = get_cached_any_cookie($url_homepage, "auth_token_session={$args->sid}");
	foreach($doc->find('h3.important + ul li a') as $notification) {
		//$tokens = explode(' ', $notification->nodeValue);
		$tokens = explode(' ', trim(preg_replace('|<img (.*?)>|', '', pq($notification)->html())));
		$count = $tokens[0];
		$type = 'unknown';
		$url = pq($notification)->attr('href');
		switch($url) {
			case '/messages':
				$type = 'messages';
				break;
			case '/spam_hints':
				$type = 'spam';
				break;
			case '/messages/box%3A4':
				$type = 'notification';
				break;
			case '/profile/hackyourlife#guestbook':
				$type = 'guestbook';
				break;
			case '/promowall':
				$type = 'promowall';
				break;
			case '/buddies/#tabAnnehmen':
				$type = 'friendship';
				break;
		}
		if($type == 'unknown')
			continue;
		$n = $xml->createElement('notification');
		$n->appendChild($xml->createElement('type', $type));
		$n->appendChild($xml->createElement('count', $count));
		$result->appendChild($n);
	}
	foreach($doc->find('div[id^="notice_"]:has(h3.important)') as $notification) {
		$notification = pq($notification);
		$title = $notification->find('h3.important')->html();
		$date = trim(str_replace('ausblenden', '', $notification->find('> p > small')->text()));
		$content = trim($notification->find(':nth-child(3)')->html());
		$n = $xml->createElement('important');
		$n->appendChild($xml->createElement('title', $title));
		$n->appendChild($xml->createElement('date', $date));
		$n->appendChild(parsePostContent($xml, $content));
		$result->appendChild($n);
	}
	return $result;
}

xmlrpc_register_function('getNotifications', array('sid'), 'rpc_getNotifications');
