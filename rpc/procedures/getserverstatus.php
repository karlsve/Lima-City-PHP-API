<?php

function rpc_getServerStatus($xml, $result) {
	global $url_status;

	$content = get_request($url_status);
	$doc = phpQuery::newDocument($content, 'text/xml');

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
			$result->appendChild($info);
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
			$result->appendChild($info);
		}
	}

	return $result;
}

xmlrpc_register_function('getServerStatus', false, 'rpc_getServerStatus');
