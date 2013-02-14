<?php

function rpc_getMessage($xml, $result, $args) {
	global $url_messages;
	$url = "$url_messages/action:read/id:{$args->id}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "auth_token_session={$args->sid}"));
	addToCache($url, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$title = $doc->find('h2')->contents();
	$content = $doc->find('div.tabPage');

	$data = array();
	foreach($content->find('dd') as $field)
		$data[] = $field->nodeValue;

	$content->find('dl')->remove();
	$contentxml = parsePostContent($xml, trim($content->html()));

	$result->appendChild($xml->createElement('title', $title));
	$result->appendChild($xml->createElement('id', $args->id));

	$fieldnames = array('from', 'to', 'date');
	for($i = 0; $i < count($data); $i++)
		$result->appendChild($xml->createElement($fieldnames[$i], trim($data[$i])));

	$result->appendChild($contentxml);
	return $result;
}

xmlrpc_register_function('getMessage', array('sid', 'id'), 'rpc_getMessage');
