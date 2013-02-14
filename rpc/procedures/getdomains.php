<?php

function rpc_getDomains($xml, $result, $args) {
	global $url_domains;

	$types = array(
		'Aufgeschaltet'		=> 'overplugged',
		'Bearbeiten'		=> 'edit',
		'Löschen'		=> 'delete'
	);

	$infotypes = array(
		'Pfad:'			=> 'path',
		'Document Root:'	=> 'docroot',
		'Register globals:'	=> 'register-globals',
		'Datum:'		=> 'date'
	);

	$onoffreplacement = array(
		'an'			=> 'on',
		'aus'			=> 'off'
	);

	$doc = phpQuery::newDocument(get_request_cookie($url_domains, "auth_token_session={$args->sid}"));
	addToCache($url_domains, $doc, "auth_token_session={$args->sid}");
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

		$domaininfo = $domain->find('ul.actions')->next('ul');
		foreach($domaininfo->find('li') as $info) {
			$info = pq($info);
			$caption = $info->find('strong')->text();
			$text = trim(preg_replace('|<strong>.+?</strong>|', '', $info->html()));
			if(isset($onoffreplacement[$text]))
				$text = $onoffreplacement[$text];
			if(isset($infotypes[$caption]))
				$domainxml->appendChild($xml->createElement($infotypes[$caption], $text));
		}

		$result->appendChild($domainxml);
	}
	return $result;
}

xmlrpc_register_function('getDomains', array('sid'), 'rpc_getDomains');
