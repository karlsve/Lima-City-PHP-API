<?php

function rpc_getUsername($xml, $result, $args) {
	global $url_homepage;
	$doc = get_cached_any_cookie($url_homepage, "auth_token_session={$args->sid}");
	$username = false;
	foreach($doc->find('h3.user') as $node) {
		$username = $node->nodeValue;
		break;
	}
	if($username !== false)
		$result->appendChild($xml->createTextNode($username));
	return $result;
}

xmlrpc_register_function('getUsername', array('sid'), 'rpc_getUsername');
