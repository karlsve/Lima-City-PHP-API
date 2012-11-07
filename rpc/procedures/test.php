<?php

function rpc_test($xml, $result, $args) {
	$text = $xml->createTextNode("arg1 = {$args->arg1}");
	$result->appendChild($text);
	return $result;
}

xmlrpc_register_function(
	'test',
	array(
		'arg1',
		'arg2'
	),
	'rpc_test'
);
