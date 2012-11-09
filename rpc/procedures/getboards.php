<?php

function rpc_getBoards($xml, $result, $args) {
	global $url_board;
	$doc = phpQuery::newDocument(get_request_cookie($url_board, "sid={$args->sid}"));
	addToCache($url_board, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	foreach($doc->find('tbody tr') as $entry) {
		$entry = pq($entry);
		$key = $entry->attr('class');
		$td = $entry->find('td:nth-child(1)');
		$board = $xml->createElement('board');
		$namelink = $td->find('strong a');
		$name = $xml->createElement('name', trim($namelink->html()));
		$url = $xml->createElement('url', preg_replace('|^.*/|', '', $namelink->attr('href')));
		$description = $xml->createElement('description', trim($td->find('em')->html()));
		$moderatoren = $xml->createElement('moderatoren');
		foreach($td->find('small a') as $mod)
			$moderatoren->appendChild($xml->createElement('moderator', pq($mod)->html()));
		$td = $entry->find('td:nth-child(2)');
		$topics = $xml->createElement('topics', trim($td->html()));
		$td = $entry->find('td:nth-child(3)');
		$replies = $xml->createElement('replies', trim($td->html()));
		$td = $entry->find('td:nth-child(4)');
		$newestThread = $td;
		$newestThreadxml = $xml->createElement('newest-thread');
		$threadtitle = $newestThread->find('a:nth-child(1)');
		$title = $xml->createElement('title', $threadtitle->html());
		$threadurl = $xml->createElement('url', preg_replace('|^.*/|', '', $threadtitle->attr('href')));
		$author = $xml->createElement('author', $newestThread->find('a:nth-child(4)')->html());
		$date = $xml->createElement('date', $newestThread->find('small')->html());
		$newestThreadxml->appendChild($threadurl);
		$newestThreadxml->appendChild($title);
		$newestThreadxml->appendChild($author);
		$newestThreadxml->appendChild($date);

		$board->appendChild($name);
		$board->appendChild($url);
		$board->appendChild($description);
		$board->appendChild($moderatoren);
		$board->appendChild($topics);
		$board->appendChild($replies);
		$board->appendChild($newestThreadxml);

		$result->appendChild($board);
	}

	return $result;
}

xmlrpc_register_function('getBoards', array('sid'), 'rpc_getBoards');
