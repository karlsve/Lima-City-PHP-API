<?php

function xml_to_json($xml, $rootname = 'lima') {
	$root = $xml->getElementsByTagName($rootname)->item(0);
	$result = element_to_json($root);
	return $result;
}

function element_to_json($xml) {
	$result = '';
	if($xml->hasChildNodes()) {
		$childNodes = $xml->childNodes;
		foreach($childNodes as $childNode)
			$result .= element_to_json($childNode);
	} else
		$result .= "\"{$xml->tagName}\" : \"{$xml->nodeValue}\"\n";
		//$result .= "\"{$xml->tagName}\" : \"" . utf8_decode($xml->nodeValue) . "\"\n";
	return $result;
}


function format_xml_tag($value) {
	$value = strtolower(utf8_decode($value));
	$value = str_replace(' ', '-', $value);
	$value = str_replace(':', '', $value);
	$value = htmlentities($value);
	$value = str_replace('&auml;', 'ae', $value);
	$value = str_replace('&ouml;', 'oe', $value);
	$value = str_replace('&uuml;', 'ue', $value);
	$value = str_replace('&szlig;', 'ss', $value);
	return utf8_encode(html_entity_decode($value));
}

function xml_format_content($value) {
	return str_replace('&', '&amp;', $value);
}

function dom2plaintext($doc) {
	$doc->find('img')->remove();
	foreach($doc->find('a') as $link) {
		$link = pq($link);
		$link->replaceWith($link->html());
	}
}
