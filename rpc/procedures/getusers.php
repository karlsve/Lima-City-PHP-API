<?php

function rpc_getUsers($xml, $result, $args) {
	global $url_profiles;
	$perpage = 100;
	$page = isset($args->page) ? intVal($args->page) : 0;
	$f = "/page%3A$page/perpage%3A$page";
	if(isset($args->filter)) {
		switch($args->filter) {
			case 'admin':
			case 'mod':
			case 'user':
			case 'online':
				$f = "/filter%3A{$args->filter}/show%3A{$args->filter}/page%3A$page/perpage%3A100";
				break;
		}
	}
	$url = "$url_profiles$f";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");

	$pages = 1;
	$a = $doc->find('ol.pageNav li:last-child a');
	if($a->count() != 0) {
		preg_match('|/page%3A([0-9]+)/perpage%3A[0-9]+$|', $a->attr('href'), $match);
		$pages = $match[1];
	}
	$result->appendChild($xml->createElement('pages', $pages));
	foreach($doc->find('tbody tr') as $row) {
		$row = pq($row);
		$content = array();
		foreach($row->find('td') as $cell)
			$content[] = pq($cell)->html();

		$name = $row->find('a')->html();
		$gulden = trim($content[1]);
		$rang = trim($content[2]);
		$login = trim($content[5]);

		$user = $xml->createElement('user');
		$user->appendChild($xml->createElement('name', $name));
		$user->appendChild($xml->createElement('gulden', $gulden));
		$user->appendChild($xml->createElement('rang', $rang));
		$user->appendChild($xml->createElement('last-login', $login));
		$result->appendChild($user);
	}
	return $result;
}

xmlrpc_register_function('getUsers', array('sid', 'o:filter', 'o:page'), 'rpc_getUsers');
