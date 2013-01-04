<?php

function rpc_getDomains($xml, $result, $args) {
	global $url_domains;

	$types = array(
		'Aufgeschaltet'	=> 'overplugged',
		'Bearbeiten'	=> 'edit',
		'Löschen'	=> 'delete'
	);
	
	$doc = phpQuery::newDocument(get_request_cookie($url_domains, "sid={$args->sid}"));
	addToCache($url_domains, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
		
	$domains = $doc->find('div.content:has(h4:contains("Domains")) ul.extraList > li');
	foreach($domains as $domain) {
		$domain = pq($domain);
		$domainxml = $xml->createElement('domain');
		$domainname = $domain->find('h4')->text();
		$domainxml->appendChild($xml->createElement('name', $domainname));
		$actionsxml = $xml->createElement('actions');
		$actions = $domain->find('ul.actions li');

		$actionurl = $domain->find('ul.actions li:first a')->attr('href');
		preg_match('|^/domains/([0-9]+)/|', $actionurl, $match);
		$domainxml->appendChild($xml->createElement('id', $match[1]));
		foreach($actions as $action) {
			$action = pq($action);
			$actionxml = $xml->createElement('action');
			$actionelement = $action->find('a');
			if($actionelement->length > 0) {
				$actiontype = $actionelement->attr('title');
				$actionxml->appendChild($xml->createTextNode($types[$actiontype]));
			} else {
				$actiontype = $action->find('img')->attr('title');
				$actionxml->appendChild($xml->createTextNode($types[$actiontype]));
			}
			$actionsxml->appendChild($actionxml);
		}
		$domainxml->appendChild($actionsxml);
		$result->appendChild($domainxml);
	}
	return $result;
}

xmlrpc_register_function('getDomains', array('sid'), 'rpc_getDomains');
