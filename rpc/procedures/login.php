<?php

function lima_login($username, $password) {
	global $url_login;
	$post = array(
		'form_username' => $username,
		'form_password' => $password
	);
	$response = post_request_raw($url_login, $post);
	$pattern_sid = '|Set-Cookie: sid=(.*?); path=/|i';
	if(!preg_match($pattern_sid, $response['header'], $match))
		return false;

	$cookie = $match[1];
	$doc = phpQuery::newDocument($response['content']);
	$isloggedin = false;
	foreach($doc->find('a[href=/usercp]') as $logout) {
		$isloggedin = true;
		break;
	}

	return $isloggedin ? $cookie : 0;
}

function rpc_login($xml, $result, $args) {
	$login = lima_login($args->username, $args->password);
	// error code
	$error = 'OK';
	if($login === false)
		$error = 'error';
	if($login === 0)
		$error = 'password';
	$result->appendChild($xml->createElement('result', $error));
	// session identifier
	if($error === 'OK')
		$result->appendChild($xml->createElement('session', $login));
	return $result;
}

xmlrpc_register_function(
	'login',
	array(
		'username',
		'password'
	),
	'rpc_login'
);
