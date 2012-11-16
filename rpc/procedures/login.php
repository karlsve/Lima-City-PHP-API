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
	$output = 'true';
	$error = 'unknown'
	if($login === false)
		$output = 'false';
	if($login === 0)
	{
		$output = 'false';
		$error = 'password';
	}
	$result->appendChild($xml->createElement('loggedin', $output));
	// session identifier
	if($output == 'true')
		$result->appendChild($xml->createElement('session', $login));
	else
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
