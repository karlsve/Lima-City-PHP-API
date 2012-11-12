<?php

function rpc_getGuestbookOfProfile($xml, $result, $args) {
	global $url_profile;
	$perpage = 100;
	$page = isset($args->page) ? intVal($args->page) : 0;
	$pagelink = "/page%3A$page/perpage%3A$perpage";
	$url = "{$url_profile}/{$args->user}{$pagelink}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	
	$entries = $doc->find('ol#guestbook > li');
	foreach($entries as $entry) {
		$entry = pq($entry);
		$entryxml = $xml->createElement('entry');
		$author = $entry->find('div.author p.un a')->html();
		$date = $entry->find('div.author small a')->html();
		$entryxml->appendChild($xml->createElement('author', $author));
		$entryxml->appendChild($xml->createElement('date', $date));
		$entrycontent = $entry->find('div.content');
		$pieces = $entrycontent->children();
		foreach($pieces as $piece) {
			$piece = pq($piece);
			if($piece->is('div.signature')) {
				$signaturexml = $xml->createElement('signature');
				
				$sigpieces = $piece->find('div');
				foreach($sigpieces as $sigpiece) {
					$sigpiece = pq($sigpiece);
					$sigpiecexml = $xml->createElement($sigpiece->attr('class'));
					$signature = $sigpiece->find('a')->html();
					$goto = preg_replace('|\/.*\/|', '', $sigpiece->find('a')->attr('href'));
					$sigpiecexml->appendChild($xml->createElement('text', $signature));
					$sigpiecexml->appendChild($xml->createElement('goto', $goto));
					$signaturexml->appendChild($sigpiecexml);
				}
				$entryxml->appendChild($signaturexml);
				$piece->remove();
			} elseif($piece->is('img')) {
				$piece->after($piece->attr('alt'));
				$piece->remove();
			} elseif($piece->is('p.successBox')) {
				$piece->remove();
			} elseif($piece->is('ul')) {
				$piece->remove();
			} elseif($piece->is('div')) {
				$piece->remove();
			} elseif($piece->is('br')) {
				$piece->after('\n');
				$piece->remove();
			}
		}
		$content = trim(preg_replace('!<.*?>!', "", $entrycontent->html()));
		$entryxml->appendChild($xml->createElement('content', $content));
		$result->appendChild($entryxml);
	}
	return $result;
}

xmlrpc_register_function('getGuestbookOfProfile', array('sid', 'user', 'o:page'), 'rpc_getGuestbookOfProfile');