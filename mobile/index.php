<?php

$url = 'http://limaapi.dauerstoned-clan.de/php/';
$xslpath = '../xml';

$stylesheet = file_get_contents("$xslpath/lima.xsl");
preg_match_all('|<xsl:include href="(.*?)" />|', $stylesheet, $includes);
foreach($includes[1] as $include) {
	$xsl = file_get_contents("$xslpath/$include");
	$xsl = preg_replace('|<\\?xml .*?\\?>|', '', $xsl);
	$xsl = preg_replace('|<xsl:stylesheet(.*?)>|s', '', $xsl);
	$xsl = str_replace('</xsl:stylesheet>', '', $xsl);
	$stylesheet = str_replace("<xsl:include href=\"$include\" />", $xsl, $stylesheet);
}


$xsl = new DOMDocument();
$xsl->loadXML($stylesheet);

$xslt = new XSLTProcessor();
$xslt->importStylesheet($xsl);



if(strlen($_SERVER['QUERY_STRING']) != 0)
	$url .= '?' . $_SERVER['QUERY_STRING'];

// build request headers
$requestheaders = apache_request_headers();
$curlheaders = array();
foreach($requestheaders as $name => $value) {
	// filter headers
	switch(strtolower($name)) {
		case 'accept-encoding':
		case 'connection':
		case 'content-length':
		case 'content-encoding':
		case 'content-type':
		case 'host':
		case 'keep-alive':
		case 'server':
		case 'user-agent':
			break;
		default:
			$curlheaders[] = "$name: $value";
	}
}

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
curl_setopt($curl, CURLOPT_REFERER, $url);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheaders);
curl_setopt($curl, CURLOPT_HEADER, true);
if(count($_POST) != 0) {
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $_POST);
}

$data = curl_exec($curl);
$type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
curl_close($curl);

// skip all '100 Continue' Headers
while(true) {
	// split header and contents
	$endofheader = strpos($data, "\r\n\r\n");
	$size = 4;
	if($endofheader === false) {
		$endofheader = strpos($data, "\n\n");
		$size = 2;
	}
	$header = substr($data, 0, $endofheader);
	$data = substr($data, $endofheader + $size);

	$headerfields = explode("\n", $header);
	$status = trim($headerfields[0]);
	$parts = explode(' ', $status);
	if($parts[1] == '100')
		continue;
	break;
}

header('Content-Type: text/html; charset=utf-8');

// set response headers
header($status);
for($i = 1; $i < count($headerfields); $i++) {
	$pos = strpos($headerfields[$i], ':');
	$name = trim(substr($headerfields[$i], 0, $pos));
	$value = trim(substr($headerfields[$i], $pos + 1));
	// filter headers
	switch(strtolower($name)) {
		case 'connection':
		case 'content-length':
		case 'content-encoding':
		case 'content-type':
		case 'keep-alive':
		case 'transfer-encoding':
		case 'server':
			break;
		default:
			header("$name: $value");
	}
}

$xml = new DOMDocument('1.0', 'utf-8');
$xml->loadXML($data);

$doctype = '<?xml version="1.0"?>' . "\n" .
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

$xhtml = $xslt->transformToXML($xml);
$xhtml = str_replace('<?xml version="1.0"?>', $doctype, $xhtml);
$xhtml = preg_replace('|<script ([^>]*?)/>|is', '<script \\1></script>', $xhtml);
$xhtml = preg_replace('|<textarea ([^>]*?)/>|is', '<textarea \\1></textarea>', $xhtml);
$xhtml = preg_replace('|<div ([^>]*?)/>|is', '<div \\1></div>', $xhtml);
$xhtml = preg_replace('|<iframe ([^>]*?)/>|is', '<iframe \\1></iframe>', $xhtml);
$xhtml = str_replace('<div/>', '<div></div>', $xhtml);

echo($xhtml);

?>