<?php

function rpc_isLoggedin($xml, $result, $args) {
	$result->appendChild($xml->createTextNode(lima_loggedin($args->sid) ? 'true' : 'false'));
	return $result;
}

xmlrpc_register_function('isLoggedin', array('sid'), 'rpc_isLoggedin');
