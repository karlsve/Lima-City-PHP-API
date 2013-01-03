<?php

function lima_login($username, $password) {
	global $url_login;
	$post = array(
		'form_username' => $username,
		'form_password' => $password
	);
	$response = post_request_raw($url_login, $post);
	$pattern_sid = '|Set-Cookie: sid=(.*?); path=/|i';
	// 250835-tLEQpqK3Tp250835RIyPaSgevoWX4VN
	$pattern_auth = '|Set-Cookie: session_auth_token=([-0-9a-zA-Z]*)|i';
	if(!preg_match($pattern_sid, $response['header'], $match))
		return false;

	$cookie = $match[1];

	$auth = false;
	if(preg_match($pattern_auth, $response['header'], $match))
		$auth = $match[1];

	$doc = phpQuery::newDocument($response['content']);
	$isloggedin = false;
	foreach($doc->find('a[href=/usercp]') as $logout) {
		$isloggedin = true;
		break;
	}

	$return = new stdClass();
	$return->success = $isloggedin;
	$return->sid = $cookie;
	$return->auth = $auth;
	return $return;
}

function rpc_login($xml, $result, $args) {
	$login = lima_login($args->username, $args->password);
	// error code
	$output = 'true';
	$error = 'unknown';
	if($login->success === false)
		$output = 'false';
	if($login === 0) {
		$output = 'false';
		$error = 'password';
	}
	$result->appendChild($xml->createElement('loggedin', $output));
	// session identifier
	if($output == 'true') {
		$result->appendChild($xml->createElement('session', $login->sid));
		if($login->auth !== false)
			$result->appendChild($xml->createElement('auth', $login->auth));
	} else
		$result->appendChild($xml->createElement('error', $error));
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
