<?php

function getHomepage($xml, $sid) {
	global $url_homepage;
	$homepage = get_request_cookie($url_homepage, "sid=$sid");
	$doc = phpQuery::newDocument($homepage);
	$config = getHomepageConfiguration($homepage);
	$data = array(
		'content'	=> $homepage,
		'config'	=> $config,
		'hasthreads'	=> false
	);

	if(
	    (array_search('newest', $config) !== false)
	    || (array_search('noreply', $config) !== false)) {
		$data['hasthreads'] = true;
		$data['threads'] = getHomepageThreads($xml, $doc);
	}

	return($data);
}

function getHomepageConfiguration($homepage) {
	$elements = array();
	if(strpos($homepage, '<h3 class="lastposts">Neueste Beitr&auml;ge</h3>') !== false)
		$elements[] = 'newest';
	if(strpos($homepage, '<h3 class="lastposts">Neueste unbeantwortete Themen</h3>') !== false)
		$elements[] = 'noreply';
	if(strpos($homepage, '<h3 class="lastvisits">Letzte Besucher meines Profils</h3>') !== false)
		$elements[] = 'visits';
	if(strpos($homepage, '<h3>Ber&uuml;hmt f&uuml;r 15 Minuten</h3>') !== false)
		$elements[] = 'famous';
	return($elements);
}

function getHomepageThreads($xml, $doc) {
	$threads = $xml->createElement('homepage');
	foreach($doc->find('h3.lastposts') as $h3) {
		$type = 'unknown';
		switch(utf8_decode($h3->nodeValue)) {
			case 'Neueste Beiträge':
				$type = 'newest';
				break;
			case 'Neueste unbeantwortete Themen':
				$type = 'noreply';
				break;
		}
		$node = $xml->createElement($type);

		$content = $h3->nextSibling;
		while(($content->nodeType == XML_TEXT_NODE) && ($content->nextSibling !== NULL))
			$content = $content->nextSibling;

		$content = pq($content);

		foreach($content->find('li') as $entry) {
			$entry = pq($entry);
			$threadlink = $entry->find('h5 a');
			$name = $threadlink->html();
			if($name == '')
				continue;
			$url = substr($threadlink->attr('href'), 8);
			$date = $entry->find('small')->html();
			$board = $entry->find('a[href^="/board/"]:not([href^="/board/action:jump/"])')->html();
			$closed = false;
			$fixed = false;
			$important = false;
			foreach($entry->find('ul.badges img') as $img) {
				$alt = pq($img)->attr('alt');
				switch($alt) {
					case 'geschlossen':
						$closed = true;
						break;
					case 'fixiert':
						$fixed = true;
						break;
					case 'wichtig':
						$important = true;
						break;
				}
			}

			$thread = $xml->createElement('thread');

			$flags = $xml->createElement('flags');
			$xmlimportant = $xml->createAttribute('important');
			$xmlimportant->appendChild($xml->createTextNode($important ? 'true' : 'false'));
			$xmlfixed = $xml->createAttribute('fixed');
			$xmlfixed->appendChild($xml->createTextNode($fixed ? 'true' : 'false'));
			$xmlclosed = $xml->createAttribute('closed');
			$xmlclosed->appendChild($xml->createTextNode($closed ? 'true' : 'false'));
			$flags->appendChild($xmlimportant);
			$flags->appendChild($xmlfixed);
			$flags->appendChild($xmlclosed);
			$thread->appendChild($flags);

			$thread->appendChild($xml->createElement('name', $name));
			$thread->appendChild($xml->createElement('url', $url));
			$thread->appendChild($xml->createElement('date', $date));
			$thread->appendChild($xml->createElement('forum', $board));
			$node->appendChild($thread);
		}

		$threads->appendChild($node);
	}
	return($threads);
}

?>