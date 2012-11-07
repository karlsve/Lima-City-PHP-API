<?php

$cached_pages = array();

class CachedPage {
	public $url;
	public $content;
	public $cookie;
	public function __construct($url, $content = null, $cookie = null) {
		$this->url = $url;
		$this->content = $content;
		$this->cookie = $cookie;
	}
}

function findCached($url) {
	global $cached_pages;
	if(!isset($cached_pages[$url]))
		return false;
	return $cached_pages[$url];
}

function addToCache($url, $content, $cookie = false) {
	global $cached_pages;
	$cached_pages[$url] = new CachedPage($url, $content, $cookie);
}

function get_cached($url) {
	$c = findCached($url);
	if($c !== false)
		return $c->content;
	$doc = phpQuery::newDocument(get_request($url));
	addToCache($url, $doc);
	return $doc;
}

function get_cached_cookie($url, $cookie) {
	$c = findCached($url);
	if(($c !== false) && ($c->cookie === $cookie))
		return $c->content;
	$doc = phpQuery::newDocument(get_request_cookie($url, $cookie));
	addToCache($url, $doc, $cookie);
	return $doc;
}

function get_cached_any_cookie($url, $cookie) {
	global $cached_pages;
	foreach($cached_pages as $page)
		if($page->cookie == $cookie)
			return $page->content;
	$doc = phpQuery::newDocument(get_request_cookie($url, $cookie));
	addToCache($url, $doc, $cookie);
	return $doc;
}
