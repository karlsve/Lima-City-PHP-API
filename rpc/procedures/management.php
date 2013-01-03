<?php

function rpc_getDomains($xml, $result, $args)
{
	global $url_domains;
	
	$url = "{$url_domains}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
		
	$domains = $doc->find('div.content:has(h4:contains("Domains")) ul.extraList > li');
	foreach($domains as $domain)
	{
		$domain = pq($domain);
		$domainxml = $xml->createElement('domain');
		$domainurl = strip_tags($domain->find('h4')->html());
		$domainxml->appendChild($xml->createElement('url', $domainurl));
		$actionsxml = $xml->createElement('actions');
		$actions = $domain->find('ul.actions li');
		foreach($actions as $action)
		{
			$action = pq($action);
			$actionxml = $xml->createElement('action');
			$actionelement = $action->find('a');
			if($actionelement->length > 0)
			{
				$actiontype = $actionelement->attr('title');
				$actionxml->appendChild($xml->createElement('type', $actiontype));
				$actionurl = $actionelement->attr('href');
				$actionxml->appendChild($xml->createElement('url', $actionurl));
			}
			else
			{
				$actionelement = $action->find('img');
				$actiontype = $actionelement->attr('title');
				$actionxml->appendChild($xml->createElement('type', $actiontype));
			}
			$actionsxml->appendChild($actionxml);
		}
		$domainxml->appendChild($actionsxml);
		$result->appendChild($domainxml);
	}
	return $result;
}

xmlrpc_register_function('getDomains', array('sid'), 'rpc_getDomains');