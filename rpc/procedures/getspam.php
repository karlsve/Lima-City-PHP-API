<?php

function rpc_getSpam($xml, $result, $args) {
	global $url_spam;
	$cookie = "sid={$args->sid};session_auth_token={$args->auth}";
	$doc = phpQuery::newDocument(get_request_cookie($url_spam, $cookie));
	addToCache($url_spam, $doc, $cookie);
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	$result->appendChild($xml->createElement('html', $doc->html()));
	return $result;
}

xmlrpc_register_function('getSpam', array('sid', 'auth'), 'rpc_getSpam');
