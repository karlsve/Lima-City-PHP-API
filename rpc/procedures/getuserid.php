<?php

function rpc_getUserID($xml, $result, $args) {
	global $url_getuserid;
	$uid = post_request($url_getuserid, array('username' => $args->username));
	$result->appendChild($xml->createTextNode(trim($uid)));
	return $result;
}

xmlrpc_register_function(
	'getUserID',
	array(
		'username'
	),
	'rpc_getUserID'
);
