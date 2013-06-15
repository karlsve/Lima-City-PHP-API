<?php

function rpc_getFTPPassword($xml, $result, $args) {
	global $url_ftp;

	$doc = phpQuery::newDocument(get_request_cookie($url_ftp, "auth_token_session={$args->sid}"));
	addToCache($url_ftp, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$password = $doc->find('#ftp_password')->text();
	$result->appendChild($xml->createElement('password', $password));
	return $result;
}

xmlrpc_register_function('getFTPPassword', array('sid'), 'rpc_getFTPPassword');
