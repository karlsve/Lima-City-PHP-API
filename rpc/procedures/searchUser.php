<?php

function rpc_searchUser($xml, $result, $args) {
	global $url_usersearch;
	$data = post_request_cookie($url_usersearch, array('username' => $args->username), "sid={$args->sid}");
	if(strlen($data) > 0) {
		$users = explode("\n", $data);
		foreach($users as $user) {
			if(strlen(trim($user)) == 0)
				continue;
			$result->appendChild($xml->createElement('user', trim($user)));
		}
	}
	return $result;
}

xmlrpc_register_function('searchUser', array('username', 'sid'), 'rpc_searchUser');
