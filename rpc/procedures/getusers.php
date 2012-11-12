<?php

function rpc_getUsers($xml, $result, $args) {
	global $url_profiles;
	$f = '';
	if(isset($args->filter)) {
		switch($args->filter) {
			case 'admin':
			case 'mod':
			case 'user':
			case 'online':
				$f = "/filter%3A{$args->filter}/show%3A{$args->filter}/page%3A0/perpage%3A100";
				break;
		}
	}
	$url = "$url_profiles$f";
	$doc = phpQuery::newDocument(get_request($url));
	addToCache($url, $doc);

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
}

xmlrpc_register_function('getUsers', array('o:filter'), 'rpc_getUsers');
