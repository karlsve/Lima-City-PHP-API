<?php

function rpc_getDatabases($xml, $result, $args) {
	global $url_databases;

	$doc = phpQuery::newDocument(get_request_cookie($url_databases, "sid={$args->sid}"));
	addToCache($url_databases, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$databases = $doc->find('div.content > ul.extraList li:has(h4)');
	if($databases->length > 0) {
		$url = $doc->find('div.content > ul.extraList li:has(h4):first ul.actions a')->attr('href');
		preg_match('|^/databases/action:clear/id:([0-9]+)/code:([0-9a-zA-Z]+)$|', $url, $match);
		$clearcode = $match[2];
		$result->appendChild($xml->createElement('clear-code', $clearcode));
	}

	foreach($databases as $db) {
		$db = pq($db);
		$name = $db->find('h4 > span')->html();
		$comment = $db->find('h4 em span')->html();
		preg_match('|^/databases/action:clear/id:([0-9]+)/code:([0-9a-zA-Z]+)$|', $db->find('ul.actions a')->attr('href'), $match);
		$id = $match[1];

		$database = $xml->createElement('database');
		$database->appendChild($xml->createElement('name', $name));
		$database->appendChild($xml->createElement('comment', $comment));
		$database->appendChild($xml->createElement('id', $id));
		$result->appendChild($database);
	}
	return $result;
}

xmlrpc_register_function('getDatabases', array('sid'), 'rpc_getDatabases');
