<?php

function rpc_getFriendsOfProfile($xml, $result, $args) {
	global $url_profile;
	
	$url = $url_profile."/".$args->user;
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	
	$friends = $doc->find('div.tabPage ul li:has(a[href^="/profile/"])');
	foreach($friends as $friend) {
		$friend = pq($friend);
		$friendxml = $xml->createElement('friend');
		$name = $friend->find('a')->html();
		$namexml = $xml->createElement('name', $name);
		$friendxml->appendChild($namexml);
		$type = $friend->find('img')->attr('alt');
		$typexml = $xml->createElement('type', $type);
		$friendxml->appendChild($typexml);
		$result->appendChild($friendxml);
	}
	return $result;
}

xmlrpc_register_function('getFriendsOfProfile', array('sid', 'user'), 'rpc_getFriendsOfProfile');