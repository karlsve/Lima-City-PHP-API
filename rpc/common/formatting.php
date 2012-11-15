<?php

function parseContent($xml, $doc) {
	if($doc->nodeType == XML_TEXT_NODE)
		return($xml->createElement('text', xml_format_content($doc->wholeText)));
	if($doc->tagName == 'br')
		return($xml->createElement('br'));
	if($doc->tagName == 'a') {
		$url = $doc->attributes->getNamedItem('href')->value;

		if(preg_match('|^/([a-z]*?)/action:jump/([0-9]*)$|', $url, $match) != 0) {
			$type = $match[1];
			$id = $match[2];

			$result = $xml->createElement('goto');
			$idxml = $xml->createAttribute('id');
			$idxml->appendChild($xml->createTextNode($id));
			$result->appendChild($idxml);

			$typexml = $xml->createAttribute('type');
			$typexml->appendChild($xml->createTextNode($type));
			$result->appendChild($typexml);
		} else if(preg_match('|^/thread/([^/]+)|', $url, $match) != 0) {
			$url = $match[1];
			$result = $xml->createElement('goto');
			$typexml = $xml->createAttribute('type');
			$typexml->appendChild($xml->createTextNode('thread'));
			$urlxml = $xml->createAttribute('url');
			$urlxml->appendChild($xml->createTextNode($url));
			$result->appendChild($typexml);
			$result->appendChild($urlxml);
		} else {
			if(preg_match('#^(http|https|ftp|ftps|sftp)://#', $url) == 0)
				$url = ($url[0] == '/') ? "https://www.lima-city.de$url" : "https://www.lima-city.de/$url";
			$result = $xml->createElement('link');
			$urlxml = $xml->createAttribute('url');
			$urlxml->appendChild($xml->createTextNode($url));
			$result->appendChild($urlxml);
		}

		if($doc->hasChildNodes()) {
			foreach($doc->childNodes as $node)
				$result->appendChild(parseContent($xml, $node));
		} else
			$result->appendChild($xml->createTextNode($doc->nodeValue));
		return($result);
	}
	if($doc->tagName == 'img') {
		$src = $doc->attributes->getNamedItem('src')->value;
		$alt = $doc->attributes->getNamedItem('alt')->value;
		if(preg_match('#^(http|https|ftp|ftps|sftp)://#', $src) == 0) {
			if($src[0] != '/')
				$src = '/' . $src;
			if((strlen($src) > 7) && (substr($src, 0, 7) == '/math/?')) {
				$result = $xml->createElement('math');
				$result->appendChild($xml->createElement('url', substr($src, 7)));
				$result->appendChild($xml->createElement('raw', $alt));
				return($result);
			}
			$src = "https://www.lima-city.de$src";
		}
		$result = $xml->createElement('img');

		$srcxml = $xml->createAttribute('src');
		$srcxml->appendChild($xml->createTextNode($src));
		$result->appendChild($srcxml);

		$altxml = $xml->createAttribute('alt');
		$altxml->appendChild($xml->createTextNode($alt));
		$result->appendChild($altxml);

		return($result);
	}
	if($doc->tagName == 'iframe') {
		preg_match('|^https?://www\.youtube\.com/embed/([^?]+)|', $doc->attributes->getNamedItem('src')->value, $match);
		$src = $match[1];
		$result = $xml->createElement('youtube', $src);

		return($result);
	}


	$result = $xml->createElement($doc->tagName);
	if($doc->hasChildNodes()) {
		foreach($doc->childNodes as $node)
			$result->appendChild(parseContent($xml, $node));
	} else
		$result->appendChild($xml->createTextNode($doc->nodeValue));
	if($doc->hasAttributes()) {
		foreach($doc->attributes as $attrib) {
			$a = $xml->createAttribute($attrib->name);
			$a->appendChild($xml->createTextNode($attrib->value));
			$result->appendChild($a);
		}
	}
	return($result);
}

function parsePostContent($xml, $content) {
	$content = preg_replace('/<pre><code>(.*?)<\\/code><\\/pre>/is', '<code>\\1</code>', $content);
	$content = preg_replace('/<pre style="display:inline;"><code>(.*?)<\\/code><\\/pre>/is', '<code display="inline">\\1</code>', $content);
	$content = preg_replace('/<pre class="brush: ([a-zA-Z]*?)">(.*?)<\\/pre>/is', '<code language="\\1">\\2</code>', $content);
	$content = preg_replace('/<img ([^>]*?)>/is', '<img \\1 />', $content);
	$content = str_replace('<br>', '<br />', $content);
	$content = str_replace('allowfullscreen></iframe>', 'allowfullscreen="allowfullscreen"></iframe>', $content);

	$doc = new DOMDocument('1.0', 'utf-8');
	$doc->loadXML("<content>$content</content>");

	return(parseContent($xml, $doc->getElementsByTagName('content')->item(0)));
}
