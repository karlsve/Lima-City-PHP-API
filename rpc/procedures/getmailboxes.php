<?php

function rpc_getMailboxes($xml, $result, $args) {
	global $url_messages;
	$doc = phpQuery::newDocument(get_request_cookie($url_messages, "sid={$args->sid}"));
	addToCache($url_messages, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	foreach($doc->find('ul.linkList li a') as $mailbox) {
		$mailbox = pq($mailbox);
		$mailboxxml = $xml->createElement('mailbox');
		$mailboxxml->appendChild($xml->createElement('title', trim(strip_tags($mailbox->html()))));
		$jumpurl = $mailbox->attr('href');
		$mailboxid = str_replace('/messages/box%3A', '', $jumpurl);
		$mailboxxml->appendChild($xml->createElement('id', $mailboxid));
		$result->appendChild($mailboxxml);
	}

	return $result;
}

xmlrpc_register_function('getMailboxes', array('sid'), 'rpc_getMailboxes');
