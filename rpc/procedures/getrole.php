<?php

function rpc_getRole($xml, $result, $args) {
	global $url_homepage;
	$doc = get_cached_any_cookie($url_homepage, "sid={$args->cookie}");
	$node = $doc->find('h3.user + ul li a[href="/usercp/page%3Agulden"] img')->get(0);
	$result->appendChild($xml->createTextNode($node->attributes->getNamedItem('alt')->nodeValue));
	return $result;
}

xmlrpc_register_function(
	'getRole',
	array(
		'sid'
	),
	'rpc_getRole'
);
