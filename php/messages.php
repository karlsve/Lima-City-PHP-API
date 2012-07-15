<?php

function getMessages($xml, $sid) {
	global $url_messages;
	$doc = phpQuery::newDocument(get_request_cookie($url_messages, "sid=$sid"));

	$mailsform = $doc->find('form[name=mails]');
	$messages = $mailsform->find('tbody');

	$messagesxml = $xml->createElement('messages');

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
		//foreach($message->find('td')->filter(':has(input)') as $part) {
		foreach($message->find('td') as $part) {
			if($messagepieces[$i] == 'none') {
				$i++;
				continue;
			}
			$part = pq($part);
			$piece = $xml->createElement($messagepieces[$i], trim(strip_tags($part->html())));
			if($messagepieces[$i] == 'title') {
				$a = $part->find('a');
				foreach($a as $node)
					$jumpurl = $node->attributes->getNamedItem('href')->nodeValue;
				$msgid = str_replace('/messages/action%3Aread/id%3A', '', $jumpurl);
				$id = $xml->createAttribute('id');
				$id->appendChild($xml->createTextNode($msgid));
				$piece->appendChild($id);
			}
			$messagexml->appendChild($piece);
			$i++;
		}
		$messagesxml->appendChild($messagexml);
	}
	return $messagesxml;
}

function getMessage($xml, $sid, $id) {
	global $url_messages;
	$doc = phpQuery::newDocument(get_request_cookie("$url_messages/action:read/id:$id", "sid=$sid"));
	$title = $doc->find('h2')->contents();
	$content = $doc->find('div.tabPage');

	$fields = $content->find('dd');
	$data = array();
	foreach($fields as $field)
		$data[] = $field->nodeValue;

	$content->find('dl')->remove();
	$contentxml = parsePostContent($xml, trim($content->html()));

	$messagetitle = $xml->createElement('title', $title);
	$messageid = $xml->createAttribute('id');
	$messageid->appendChild($xml->createTextNode($id));
	$messagetitle->appendChild($messageid);

	$message = $xml->createElement('message');
	$message->appendChild($messagetitle);

	$fieldnames = array('from', 'to', 'date');
	for($i = 0; $i < count($data); $i++)
		$message->appendChild($xml->createElement($fieldnames[$i], $data[$i]));

	$message->appendChild($contentxml);
	return($message);
}

?>