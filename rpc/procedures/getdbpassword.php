<?php

function rpc_getDBPassword($xml, $result, $args) {
	global $url_databases;

	$doc = phpQuery::newDocument(get_request_cookie($url_databases, "auth_token_session={$args->sid}"));
	addToCache($url_databases, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$code = $doc->find('div.content table input[name="code"]')->attr('value');
	$meet = $doc->find('div.content table input[name="meet"]')->attr('value');

	$doc = phpQuery::newDocument(post_request_cookie("$url_databases/action%3Ashow", "code=$code&meet=$meet", "auth_token_session={$args->sid}"), $url_databases);
	$username = $doc->find('div.content table tr:has(td strong:contains("Benutzername:")) td:nth-child(2) pre')->text();
	$password = $doc->find('div.content table tr:has(td strong:contains("Kennwort:")) td:nth-child(2) pre')->text();
	$result->appendChild($xml->createElement('username', $username));
	$result->appendChild($xml->createElement('password', $password));
	return $result;
}

xmlrpc_register_function('getDBPassword', array('sid'), 'rpc_getDBPassword');
