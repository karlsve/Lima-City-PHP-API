<?php

function rpc_getGroupsOfProfile($xml, $result, $args) {
	global $url_profile;
	
	$url = "{$url_profile}/{$args->user}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	
	$groups = $doc->find('div#tabGroups ul li:has(a[href^="/groups/"])');
	foreach($groups as $group) {
		$group = pq($group);
		$groupxml = $xml->createElement('group');
		$name = $group->find('a')->html();
		$groupxml->appendChild($xml->createElement('name', $name));
		$link = preg_replace("|/groups/|", "", $group->find("a")->attr('href'));
		$groupxml->appendChild($xml->createElement('link', $link));
		$group->find('a')->remove();
		$member = preg_replace("|\D|", "", $group->html());
		$groupxml->appendChild($xml->createElement('member', $member));
		$result->appendChild($groupxml);
	}
	return $result;
}

xmlrpc_register_function('getGroupsOfProfile', array('sid', 'user'), 'rpc_getGroupsOfProfile');