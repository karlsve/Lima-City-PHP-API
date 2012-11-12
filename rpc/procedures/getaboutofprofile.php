<?php

function rpc_getAboutOfProfile($xml, $result, $args) {
	global $url_profile;
	
	$url = "{$url_profile}/{$args->user}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	
	$elements = $doc->find("div#tabAbout dl dt");
	foreach($elements as $element) {
		$element = pq($element);
		$content = preg_replace('|\s\s|', '', (preg_replace('|<.*?>|', '', $element->next('dd')->html())));
		if(empty($content))
			$content = 'empty';
		$elementxml = $xml->createElement('element');
		$name = preg_replace('|:|', '', $element->html());
		$elementxml->appendChild($xml->createElement('name', $name));
		$elementxml->appendChild($xml->createElement('content', $content));
		$result->appendChild($elementxml);
	}
	return $result;
}

xmlrpc_register_function('getAboutOfProfile', array('sid', 'user'), 'rpc_getAboutOfProfile');