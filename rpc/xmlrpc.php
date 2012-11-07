<?php

header('content-type: text/xml; charset=utf-8');
header('cache-control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('pragma: no-cache');
header('expires: Thu, 19 Nov 1981 08:52:00 GMT');

$xmlrpc_functions = array();

function xmlrpc_register_function($name, $args, $fname) {
	global $xmlrpc_functions;
	$xmlrpc_functions[$name] = array('args' => $args, 'fname' => $fname);
}


function xmlrpc_call($xml, $result, $name, $args) {
	global $xmlrpc_functions;
	if(!isset($xmlrpc_functions[$name]))
		return false;
	$f = $xmlrpc_functions[$name];
	if(($f['args'] === false) && ($args !== false)) // no args required but given
		return false;

	if($f['args'] !== false) // missing arguments
		foreach($f['args'] as $arg)
			if(!isset($args->{$arg}))
				return false;

	if($f['args'] !== false) {
		if(count(get_object_vars($args)) != count($f['args']))
			return false;
		$f['fname']($xml, $result, $args);
	} else
		$f['fname']($xml, $result);

	return $result;
}

function xmlrpc_directory($xml, $result) {
	global $xmlrpc_functions;
	ksort($xmlrpc_functions);
	foreach($xmlrpc_functions as $name => $f) {
		$procedure = $xml->createElement('procedure');
		$procedure->appendChild($xml->createElement('name', $name));
		$arguments = $xml->createElement('arguments');
		if($f['args'] !== false)
			foreach($f['args'] as $arg)
				$arguments->appendChild($xml->createElement('argument', $arg));
		$procedure->appendChild($arguments);
		$result->appendChild($procedure);
	}
	return $result;
}
xmlrpc_register_function('rpc_directory', false, 'xmlrpc_directory');

// load common functions and procedures available as rpc
$includesdir = 'common';
$proceduresdir = 'procedures';

$dir = opendir($includesdir);
while($file = readdir($dir)) {
	if(($file == '.') || ($file == '..'))
		continue;
	$pathinfo = pathinfo($file);
	if($pathinfo['extension'] != 'php')
		continue;
	include("$includesdir/$file");
}
closedir($dir);

$dir = opendir($proceduresdir);
while($file = readdir($dir)) {
	if(($file == '.') || ($file == '..'))
		continue;
	$pathinfo = pathinfo($file);
	if($pathinfo['extension'] != 'php')
		continue;
	include("$proceduresdir/$file");
}
closedir($dir);


$namespace = 'http://www.lima-city.de/xml/';
$rootname = 'lima';
$xml = new DOMDocument('1.0', 'utf-8');
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;
$root = $xml->createElementNS($namespace, $rootname);

if(isset($_REQUEST['type'])) {
	$type = $_REQUEST['type'];

	if($type == 'single') {
		$result = false;
		if(isset($_REQUEST['proc'])) {
			$proc = $_REQUEST['proc'];
			$args = false;
			if(isset($_REQUEST['args']))
				$args = json_decode($_REQUEST['args']);
			$resultxml = $xml->createElement('result');
			$result = xmlrpc_call($xml, $resultxml, $proc, $args);
		}
		if($result === false)
			$root->appendChild($xml->createElement('fail'));
		else
			$root->appendChild($result);
	} else if($type == 'multi') {
		if(isset($_REQUEST['data'])) {
			$requests = json_decode($_REQUEST['data']);
			foreach($requests as $request) {
				$resultxml = $xml->createElement($request->ref);
				$result = xmlrpc_call($xml, $resultxml, $request->proc, $request->args == null ? false : $request->args);
				if($result === false)
					$root->appendChild($xml->createElement('fail', $request->ref));
				else
					$root->appendChild($result);
			}
		} else {
			$root->appendChild($xml->createElement('fail'));
		}
	}
} else {
	$root->appendChild($xml->createElement('fail'));
}

$xml->appendChild($root);
echo($xml->saveXML());
exit();
