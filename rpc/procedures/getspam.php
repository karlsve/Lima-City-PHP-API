<?php

function rpc_getSpam($xml, $result, $args) {
	global $url_spam;
	$cookie = "auth_token_session={$args->sid}";
	$doc = phpQuery::newDocument(get_request_cookie($url_spam, $cookie));
	addToCache($url_spam, $doc, $cookie);
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	foreach($doc->find('tr.spam_first_row') as $row) {
		$row = pq($row);
		$spam = $xml->createElement('spam');
		$link = $row->find('a[href^="/board/action"]');
		$postid = substr($link->attr('href'), 19);
		$spam->appendChild($xml->createElement('postid', $postid));
		$spam->appendChild($xml->createElement('thread', $link->html()));
		$spam->appendChild($xml->createElement('boardname', $row->find('td:nth-child(2)')->html()));
		$spam->appendChild($xml->createElement('author', $row->find('td:nth-child(3)')->html()));
		$spam->appendChild($xml->createElement('date', $row->find('td:nth-child(4)')->html()));
		$spamid = substr($row->find('a[href^="/spam_hints"]')->attr('href'), 12);
		$part2 = $row->next('tr');
		$reporter = substr($part2->find('td:first-child()')->html(), 18);
		$reporter = trim(substr($reporter, 0, strpos($reporter, '<p>')));
		$message = $part2->find('p')->html();
		$spam->appendChild($xml->createElement('spamid', $spamid));
		$spam->appendChild($xml->createElement('reporter', $reporter));
		$spam->appendChild($xml->createElement('message', $message));
		$result->appendChild($spam);
	}
	return $result;
}

xmlrpc_register_function('getSpam', array('sid'), 'rpc_getSpam');
