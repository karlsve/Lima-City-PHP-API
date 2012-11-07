<?php

function rpc_logout($xml, $result, $args) {
	global $url_logout;
	get_request_cookie($url_logout, "sid={$args->sid}");
	return $result;
}

xmlrpc_register_function(
	'logout',
	array(
		'sid'
	),
	'rpc_logout'
);
