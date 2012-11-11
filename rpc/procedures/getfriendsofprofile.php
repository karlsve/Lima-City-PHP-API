<?php

function rpc_getFriendsOfProfile($xml, $result, $args) {
	global $url_profile;
	
	$url = "{$url_profile}/{$args->user}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	
	$friends = $doc->find('div.tabPage ul li:has(a[href^="/profile/"])');
	foreach($friends as $friend) {
		$friend = pq($friend);
		$friendxml = $xml->createElement('friend');
		$name = $friend->find('a')->html();
		$friendxml->appendChild($xml->createElement('name', $name));
		$type = explode(", ", $friend->find('img')->attr('alt'));
		$friendxml->appendChild($xml->createElement('type', $type[0]));
		$result->appendChild($friendxml);
	}
	return $result;
}

xmlrpc_register_function('getFriendsOfProfile', array('sid', 'user'), 'rpc_getFriendsOfProfile');