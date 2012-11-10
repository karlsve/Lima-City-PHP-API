<?php

function rpc_getInboxes($xml, $result, $args)
{
	global $url_messages;
	$doc = phpQuery::newDocument(get_request_cookie($url_messages, "sid={$args->sid}"));
	addToCache($url_messages, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$inboxes = $doc->find('ul#linkList');

	foreach($inboxes->find('li a') as $inbox) {
		$inbox = pq($inbox);
		$inboxxml = $xml->createElement('inbox');
		$inboxxml->appendChild($inboxxml->createElement("title", trim(strip_tags($inbox->html()))));
		$jumpurl = $inbox->attributes->getNamedItem('href')->nodeValue;
		$inboxid = str_replace('/messages/box%3A', '', $jumpurl);
		$inboxxml->appendChild($inboxxml->createElement("id", $inboxid));
		$result->appendChild($inboxxml);
	}

	return $result;
}

xmlrpc_register_function('getInboxes', array('sid'), 'rpc_getInboxes');

?>