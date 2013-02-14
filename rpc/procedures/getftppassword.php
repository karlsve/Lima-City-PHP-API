<?php

function rpc_getFTPPassword($xml, $result, $args) {
	global $url_ftp;

	$response = get_request_raw($url_ftp, "auth_token_session={$args->sid}");

	$pattern_sid = '|Set-Cookie: sid=(.*?); path=/|i';
	if(!preg_match($pattern_sid, $response['header'], $match))
		return false;

	$sid = $match[1];

	$doc = phpQuery::newDocument($response['content']);
	addToCache($url_ftp, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$code = $doc->find('div.content table input[name="code"]')->attr('value');
	$meet = $doc->find('div.content table input[name="meet"]')->attr('value');

	if(empty($code) && empty($meet)) {
		$result->appendChild($xml->createElement('error'));
		return $result;
	}

	$doc = phpQuery::newDocument(post_request_cookie("$url_ftp/action%3Ashow", "code=$code&meet=$meet", "auth_token_session={$args->sid};sid=$sid"));
	$password = $doc->find('div.content table tr:has(td strong:contains("Kennwort:")) td:nth-child(2) pre')->text();
	$result->appendChild($xml->createElement('password', $password));
	return $result;
}

xmlrpc_register_function('getFTPPassword', array('sid'), 'rpc_getFTPPassword');
