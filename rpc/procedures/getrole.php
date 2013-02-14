<?php

function rpc_getRole($xml, $result, $args) {
	global $url_homepage;
	$doc = get_cached_any_cookie($url_homepage, "auth_token_session={$args->sid}");
	$role = $doc->find('h3.user + ul li a[href="/usercp/page%3Agulden"] img')->get(0);
	$role = pq($role)->attr('alt');
	$role = substr($role, 0, strpos($role, ',') !== false ? strpos($role, ',') : strlen($role));
	$result->appendChild($xml->createTextNode($role));
	return $result;
}

xmlrpc_register_function('getRole', array('sid'), 'rpc_getRole');
