<?php

function rpc_setSignature($xml, $result, $args) {
	global $url_usercp;
	$response = get_request_raw($url_usercp, "auth_token_session={$args->sid}");

	$pattern_sid = '|Set-Cookie: sid=(.*?); path=/|i';
	if(!preg_match($pattern_sid, $response['header'], $match))
		return false;

	$sid = $match[1];

	$doc = phpQuery::newDocument($response['content']);
	addToCache($url_usercp, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$code = $doc->find('form[name="signature"] input[name="code"]')->attr('value');
	$data = 'signature=' . urlencode($args->signature) . "&code=$code";
	$doc = post_request_cookie("$url_usercp/page%3Asignature", $data, "auth_token_session={$args->sid};sid=$sid");
	$result->appendChild($xml->createTextNode('OK'));
	return $result;
}

xmlrpc_register_function('setSignature', array('sid', 'signature'), 'rpc_setSignature');
