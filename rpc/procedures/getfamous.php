<?php

function rpc_getFamous($xml, $result, $args) {
	global $url_famous;

	$doc = phpQuery::newDocument(get_request_cookie($url_famous, "auth_token_session={$args->sid}"));
	addToCache($url_famous, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$users = $xml->createElement('users');
	$doc->find('#mainContent td.lastViewer img.profileViewer')->remove();
	foreach($doc->find('#mainContent td.lastViewer') as $user) {
		$user = pq($user);
		$name = $user->find('a[rel="nofollow"]')->html();
		$stars = $user->find('img');
		$role = pq($stars->get(0))->attr('alt');
		$role = substr($role, 0, strpos($role, ',') !== false ? strpos($role, ',') : strlen($role));
		$node = $xml->createElement('user');
		$node->appendChild($xml->createElement('name', $name));
		$node->appendChild($xml->createElement('role', $role));
		if($role == 'Benutzer') {
			$starcount = $stars->count();
			$type = pq($stars->get(0))->attr('src');
			preg_match('|_([a-z]+)\.[a-z]+$|', $type, $match);
			$type = $match[1];
			$s = $xml->createElement('stars');
			$s->appendChild($xml->createElement('count', $starcount));
			$s->appendChild($xml->createElement('type', $type));
			$node->appendChild($s);
		}
		$users->appendChild($node);
	}
	$result->appendChild($users);

	$groups = $xml->createElement('groups');
	foreach($doc->find('#mainContent table tbody tr td:nth-child(2)') as $group) {
		$group = pq($group);
		$name = $group->find('a')->html();
		$url = substr($group->find('a')->attr('href'), 8);
		$tokens = explode(' ', $group->find('span small')->text());
		$members = $tokens[3];
		$node = $xml->createElement('group');
		$node->appendChild($xml->createElement('name', $name));
		$node->appendChild($xml->createElement('url', $url));
		$node->appendChild($xml->createElement('members', $members));
		$groups->appendChild($node);
	}
	$result->appendChild($groups);

	$domains = $xml->createElement('domains');
	foreach($doc->find('#mainContent table tbody tr td:nth-child(3)') as $domain) {
		$domain = pq($domain);
		$name = $domain->find('> a')->html();
		$owner = $domain->find('span small a')->html();
		$node = $xml->createElement('domain');
		$node->appendChild($xml->createElement('domain', $name));
		$node->appendChild($xml->createElement('owner', $owner));
		$domains->appendChild($node);
	}
	$result->appendChild($domains);

	return $result;
}

xmlrpc_register_function('getFamous', array('sid'), 'rpc_getFamous');
