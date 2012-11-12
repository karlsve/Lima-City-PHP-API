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
		$contentelement = $element->next('dd');
		$name = preg_replace('|:|', '', $element->html());
		$elementxml = $xml->createElement('element');
		$elementxml->appendChild($xml->createElement('name', $name));
		if($name == 'Messenger') {
			$elementxml = getMessenger($xml, $elementxml, $contentelement);
		} else {
			$content = strip_tags($contentelement->html());
			$elementxml->appendChild($xml->createElement('content', $content));
		}
		$result->appendChild($elementxml);
	}
	return $result;
}

function getMessenger($xml, $result, $element) {
	$messengerxml = $xml->createElement('messenger');
	$messenger = $element->find('a');
	foreach($messenger as $m) {
		$m = pq($m);
		$url = $m->attr('href');
		if(strpos($url, 'icq.com') !== false) {
			$link =  preg_replace('!\D!', "", $url);
			$messengerxml->appendChild($xml->createElement('icq', $link));
		}
		elseif(strpos($url, 'aim') !== false) {
			$link =  preg_replace('!aim:GoIM\?screenname=!', '', $url);
			$messengerxml->appendChild($xml->createElement('aim', $link));
		}
		elseif(strpos($url, 'skype') !== false) {
			$link =  preg_replace('!(\w+?\:)|(\?\w+)!', '', $url);
			$messengerxml->appendChild($xml->createElement('skype', $link));
		}
	}
	$result->appendChild($messengerxml);
	return $result;
}

xmlrpc_register_function('getAboutOfProfile', array('sid', 'user'), 'rpc_getAboutOfProfile');