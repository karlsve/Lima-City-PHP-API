<?php

function rpc_getProfile($xml, $result, $args) {
	
	$about = $xml->createElement('about');
	$about = rpc_getAboutOfProfile($xml, $about, $args);
	$result->appendChild($about);
	$friends = $xml->createElement('friends');
	$friends = rpc_getFriendsOfProfile($xml, $friends, $args);
	$result->appendChild($friends);
	$groups = $xml->createElement('groups');
	$groups = rpc_getGroupsOfProfile($xml, $groups, $args);
	$result->appendChild($groups);
	return $result;
}

xmlrpc_register_function('getProfile', array('sid', 'user'), 'rpc_getProfile');