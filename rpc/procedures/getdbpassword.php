<?php

function rpc_getDBPassword($xml, $result, $args) {
	global $url_databases;

	$doc = phpQuery::newDocument(get_request_cookie($url_databases, "auth_token_session={$args->sid}"));
	addToCache($url_databases, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$username = pq($doc->find('dl.well dd code')->get(2))->text();
	$password = $doc->find('dl.well dd #database_password')->text();
	$result->appendChild($xml->createElement('username', $username));
	$result->appendChild($xml->createElement('password', $password));
	return $result;
}

xmlrpc_register_function('getDBPassword', array('sid'), 'rpc_getDBPassword');
