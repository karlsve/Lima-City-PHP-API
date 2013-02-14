<?php

function rpc_getGuestbookOfProfile($xml, $result, $args) {
	global $url_profile;
	$perpage = 100;
	$page = isset($args->page) ? intVal($args->page) : 0;
	$pagelink = "/page%3A$page/perpage%3A$perpage";
	$url = "{$url_profile}/{$args->user}{$pagelink}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "auth_token_session={$args->sid}"));
	addToCache($url, $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$guestbook = $doc->find('ol#guestbook');
	$pieces = array(
		0 => 'name',
		1 => 'none',
		2 => 'gulden',
		3 => 'none'
		);

	$guestbook->find('ul.actions')->remove();
	$guestbook->find('div.replyBox')->remove();
	$guestbook->find('p.successBox')->remove();

	foreach($guestbook->find('li') as $entry) {
		$guestbookentry = $xml->createElement('entry');

		$entry = pq($entry);

		$authordiv = $entry->find('div.author');
		$author = $authordiv->find('p.un');
		$date = $authordiv->find('small');
		dom2plaintext($date);
		$datexml = $xml->createElement('date', trim($date->html()));
		$guestbookentry->appendChild($datexml);

		$authorxml = $xml->createElement('author');
		$guestbookentry->appendChild($authorxml);

		$signature = pq($entry->find('div.signature'));
		$left = $signature->find('div.left');
		$right = $signature->find('div.right');
		$sig = '';
		if($left)
			$sig = $left->html();
		if($right)
			$sig .= ' ' . $right->html();
		$sig = trim($sig);

		$signature->remove();

		$j = 0;
		foreach($entry->find('p:not(.sucessBox)') as $piece) {
			$piece = pq($piece);
			dom2plaintext($piece);
			if($pieces[$j] != 'none') {
				if($pieces[$j] == 'name') {
					$name = trim($piece->html());
					$deleted = 'false';
					if(preg_match('|<del>(.*?)</del>|', $name, $match)) {
						$name = $match[1];
						$deleted = 'true';
					}
					$piece = $xml->createElement($pieces[$j], $name);
					$del = $xml->createAttribute('deleted');
					$del->appendChild($xml->createTextNode($deleted));
					$authorxml->appendChild($del);
				} else
					$piece = $xml->createElement($pieces[$j], trim($piece->html()));
				$authorxml->appendChild($piece);
			}
			$j++;
		}
		$content = $entry->find('div.content');
		$data = trim($content->html());
		if(strlen($sig) != 0)
			$data .= '<br>' . $sig;
		$contentxml = parsePostContent($xml, trim($data));

		//$contentxml = $xml->createElement("content", trim($content->html()));
		$guestbookentry->appendChild($contentxml);
		$result->appendChild($guestbookentry);
	}

	return $result;
}

xmlrpc_register_function('getGuestbookOfProfile', array('sid', 'user', 'o:page'), 'rpc_getGuestbookOfProfile');
