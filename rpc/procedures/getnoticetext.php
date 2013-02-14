<?php

function rpc_getNoticeText($xml, $result, $args) {
	global $url_homepage;
	$doc = phpQuery::newDocument(get_request_cookie($url_homepage, "auth_token_session={$args->sid}"));
	addToCache($url_homepage, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$notice = base64_decode(substr($doc->find('#noticetext1.inner')->html(), 4));
	$noticexml = $xml->createElement('notice');
	$noticexml->appendChild($xml->createCDATASection($notice));
	$result->appendChild($noticexml);
	return $result;
}

function getNoticeText($sid) {
	global $url_homepage;
	$doc = get_cache_cookie($url_homepage, "auth_token_session=$sid");
	$notice = base64_decode(substr($doc->find('p#noticetext1.inner')->html(), 4));
	return $notice;
}

xmlrpc_register_function('getNoticeText', array('sid'), 'rpc_getNoticeText');
