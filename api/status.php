<?php

function serverstatus($xml) {
	global $url_status;

	$content = get_request($url_status);
	$doc = phpQuery::newDocument($content, 'text/xml');

	$serverstatus = $xml->createElement('serverstatus');

	$tables = $doc->find('table[style="float:left; margin-left:70px;"]');

	foreach($tables as $table) {
		foreach($tables->find('tr') as $row) {
			$name = $row->firstChild->nodeValue;
			$value  = $row->childNodes->item(1)->nodeValue;

			$info = $xml->createElement('info');
			$infoname = $xml->createAttribute('name');
			$infoname->appendChild($xml->createTextNode($name));
			$infovalue = $xml->createAttribute('time');
			$infovalue->appendChild($xml->createTextNode($value));
			$info->appendChild($infoname);
			$info->appendChild($infovalue);
			$serverstatus->appendChild($info);
		}
	}

	$tables = $doc->find('table[style="float:right; margin-right:70px;"]');

	foreach($tables as $table) {
		foreach($tables->find('tr') as $row) {
			$name = $row->firstChild->nodeValue;
			$value  = $row->childNodes->item(1)->nodeValue;

			$info = $xml->createElement('info');
			$infoname = $xml->createAttribute('name');
			$infoname->appendChild($xml->createTextNode($name));
			$infovalue = $xml->createAttribute('time');
			$infovalue->appendChild($xml->createTextNode($value));
			$info->appendChild($infoname);
			$info->appendChild($infovalue);
			$serverstatus->appendChild($info);
		}
	}

	return $serverstatus;
}
