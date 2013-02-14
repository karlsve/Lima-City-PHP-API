<?php

function rpc_getFTPPassword($xml, $result, $args) {
	global $url_ftp;

	$doc = phpQuery::newDocument(get_request_cookie($url_ftp, "auth_token_session={$args->sid}"));
	addToCache($url_ftp, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$code = $doc->find('div.content table input[name="code"]')->attr('value');
	$meet = $doc->find('div.content table input[name="meet"]')->attr('value');

	$doc = phpQuery::newDocument(post_request_cookie("$url_ftp/action%3Ashow", "code=$code&meet=$meet", "auth_token_session={$args->sid}"), $url_ftp);
	$password = $doc->find('div.content table tr:has(td strong:contains("Kennwort:")) td:nth-child(2) pre')->text();
	$result->appendChild($xml->createElement('password', $password));
	return $result;
}

xmlrpc_register_function('getFTPPassword', array('sid'), 'rpc_getFTPPassword');
