<?php

function rpc_getNoticeText($xml, $result, $args) {
	global $url_homepage;
	$doc = phpQuery::newDocument(get_request_cookie($url_homepage, "sid={$args->sid}"));
	addToCache($url_homepage, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$notice = base64_decode($doc->find('#noticetext1.inner')->html());
	$result->appendChild($xml->createElement('notice', $notice));
	return $result;
}

function getNoticeText($sid) {
	global $url_homepage;
	$doc = get_cache_cookie($url_homepage, "sid=$sid");
	$notice = base64_decode($doc->find('p#noticetext1.inner')->html());
	return $notice;
}

xmlrpc_register_function('getNoticeText', array('sid'), 'rpc_getNoticeText');
