<?php

function rpc_getFriendListe($xml, $result, $args) {
	global $url_homepage;
	$doc = phpQuery::newDocument(get_request_cookie($url_homepage, "auth_token_session={$args->sid}"));
	addToCache($url_homepage, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	$onlineelements = $doc->find('a:has(img[alt=Online])');
	$onlinenum = $onlineelements->length();
	$onlinexml = $xml->createElement('online');
	$onlinexml->appendChild($xml->createElement('number', $onlinenum));
	foreach($onlineelements as $onlineelement)
	{
		$onlineelement = pq($onlineelement);
		$onlineelement->remove('img');
		$user = trim($onlineelement->text());
		$link = $onlineelement->attr('href');
		$userxml = $xml->createElement('user');
		$userxml->appendChild($xml->createElement('name', $user));
		$userxml->appendChild($xml->createElement('link', $link));
		$onlinexml->appendChild($userxml);
	}
	$result->appendChild($onlinexml);
	$offlineelements = $doc->find('a:has(img[alt=Offline])');
	$offlinenum = $offlineelements->length();
	$offlinexml = $xml->createElement('offline');
	$offlinexml->appendChild($xml->createElement('number', $offlinenum));
	foreach($offlineelements as $offlineelement)
	{
		$offlineelement = pq($offlineelement);
		$offlineelement->remove('img');
		$user = trim($offlineelement->text());
		$link = $offlineelement->attr('href');
		$userxml = $xml->createElement('user');
		$userxml->appendChild($xml->createElement('name', $user));
		$userxml->appendChild($xml->createElement('link', $link));
		$offlinexml->appendChild($userxml);
	}
	$result->appendChild($offlinexml);
	return $result;
}

xmlrpc_register_function('getFriendList', array('sid'), 'rpc_getFriendListe');
