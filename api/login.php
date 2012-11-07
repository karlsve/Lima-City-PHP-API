<?php

function login($user, $pass) {
	global $url_login;
	$post = array(
		'form_username'	=> $user,
		'form_password' => $pass
	);

	$response = post_request_raw($url_login, $post);

	$pattern_sid = '|Set-Cookie: sid=(.*?); path=/|i';
	if(!preg_match($pattern_sid, $response['header'], $match))
		return false;

	$cookie = $match[1];
	$doc = phpQuery::newDocument($response['content']);

	$isloggedin = false;
	foreach($doc->find('a[href=/usercp]') as $logout)
		$isloggedin = true;

	return $isloggedin ? $cookie : 0;
}

function logout($sid) {
	global $url_logout;
	get_request_cookie($url_logout, 'sid=' . $sid);
}

function isLoggedin($sid) {
	global $url_homepage;
	$doc = phpQuery::newDocument(get_request_cookie($url_homepage, "sid=$sid"));
	$isloggedin = false;
	foreach($doc->find('a[href=/usercp]') as $logout)
		$isloggedin = true;
	return $isloggedin;
}

function getUsername($sid) {
	global $url_homepage;
	$doc = phpQuery::newDocument(get_request_cookie($url_homepage, "sid=$sid"));
	foreach($doc->find('h3.user') as $node)
		return $node->nodeValue;
	return false;
}

function getRole($sid) {
	global $url_homepage;
	$doc = phpQuery::newDocument(get_request_cookie($url_homepage, "sid=$sid"));
	$node = $doc->find('h3.user + ul li a[href="/usercp/page%3Agulden"] img')->get(0);
	return $node->attributes->getNamedItem('alt')->nodeValue;
}

function getUserID($user) {
	return post_request($url_getuid, array('username' => $user));
}
