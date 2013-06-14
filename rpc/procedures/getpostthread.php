<?php

function rpc_getPostThread($xml, $result, $args) {
	global $url_board;
	global $url_origin;
	$location = get_location("$url_board/action:jump/{$args->id}");
	preg_match("|/thread/(.+)/page%3A([0-9]+)/perpage%3A([0-9]+)|", $location, $match);
	$name = $match[1];
	$page = $match[2];
	$perpage = $match[3];
	$result->appendChild($xml->createElement('location', $url_origin . $location));
	$result->appendChild($xml->createElement('name', $name));
	$result->appendChild($xml->createElement('page', $page));
	$result->appendChild($xml->createElement('perpage', $perpage));
	return $result;
}

xmlrpc_register_function('getPostThread', array('id'), 'rpc_getPostThread');
