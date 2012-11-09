<?php

function rpc_getMessages($xml, $result, $args) {
	global $url_messages;
	$doc = phpQuery::newDocument(get_request_cookie($url_messages, "sid={$args->sid}"));
	addToCache($url_messages, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$messages = $doc->find('form[name=mails] tbody');

	$messagepieces = array(
		0 => 'none',
		1 => 'title',
		2 => 'date',
		3 => 'from'
	);

	foreach($messages->find('tr') as $message) {
		$message = pq($message);
		$messagexml = $xml->createElement('message');
		$i = 0;

		$unread = $message->find('img[src="/images/layout/icons/mail_unread.png"]')->count() == 1;
		$messagexml->appendChild($xml->createElement('unread', $unread ? 'true' : 'false'));

		foreach($message->find('td') as $part) {
			if($messagepieces[$i] == 'none') {
				$i++;
				continue;
			}
			$part = pq($part);
			$piece = $xml->createElement($messagepieces[$i], trim(strip_tags($part->html())));
			if($messagepieces[$i] == 'title') {
				$jumpurl = $part->find('a')->get(0)->attributes->getNamedItem('href')->nodeValue;
				$msgid = str_replace('/messages/action%3Aread/id%3A', '', $jumpurl);
				$messagexml->appendChild($xml->createElement('id', $msgid));
			}
			$messagexml->appendChild($piece);
			$i++;
		}
		$result->appendChild($messagexml);
	}

	return $result;
}

xmlrpc_register_function('getMessages', array('sid'), 'rpc_getMessages');
