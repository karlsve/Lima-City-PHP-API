<?php

function rpc_getBoard($xml, $result, $args) {
	global $url_board;
	$url = "$url_board/{$args->name}";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$content = $doc->find('#content');
	$threadlist = $content->find('tr:has(td)');

	$name = $doc->find('h2')->contents();
	$result->appendChild($xml->createElement('name', $name));

	foreach($threadlist as $thread) {
		$thread = pq($thread);
		$threadxml = $xml->createElement('thread');
		$threadname = $thread->find('td:nth-child(1) > a');
		$name = $threadname->html();
		$url = preg_replace('#.*/#', '', $threadname->attr('href'));
		$views = trim($thread->find('td:nth-child(2)')->html());
		$replies = trim($thread->find('td:nth-child(3)')->html());
		$author = $thread->find('td:nth-child(4) > a')->html();
		$date = $thread->find('td:nth-child(5) > small')->html();
		$deleted = 'false';
		if(preg_match('|<del>(.*?)</del>|', $author, $match)) {
			$author = $match[1];
			$deleted = 'true';
		}
		$threadauthor = $xml->createElement('author', $author);
		$del = $xml->createAttribute('deleted');
		$del->appendChild($xml->createTextNode($deleted));
		$threadauthor->appendChild($del);
		$threadxml->appendChild($xml->createElement('name', $name));
		$threadxml->appendChild($xml->createElement('url', $url));
		$threadxml->appendChild($xml->createElement('views', $views));
		$threadxml->appendChild($xml->createElement('replies', $replies));
		$threadxml->appendChild($threadauthor));
		$threadxml->appendChild($xml->createElement('date', $date));
		$result->appendChild($threadxml);
	}

	return $result;
}

xmlrpc_register_function('getBoard', array('sid', 'name'), 'rpc_getBoard');
