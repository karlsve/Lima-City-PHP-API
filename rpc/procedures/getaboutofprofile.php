<?php

function rpc_getAboutOfProfile($xml, $result, $args) {
	global $url_profile;
	
	$url = "{$url_profile}/{$args->user}";
	$doc = get_cached_cookie($url, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;
	$elements = $doc->find('div#tabAbout dl dt');
	foreach($elements as $element) {
		$element = pq($element);
		$contentelement = $element->next('dd');
		$name = preg_replace('|:|', '', $element->html());
		$elementxml = $xml->createElement('element');
		$elementxml->appendChild($xml->createElement('name', $name));
		$type = 'text';
		switch($name) {
		case 'Messenger':
			$elementxml = getMessenger($xml, $elementxml, $contentelement);
			$type = 'messenger';
			break;
		case 'Über mich':
			$elementxml->appendChild(parsePostContent($xml, trim($contentelement->html())));
			$type = 'forum';
			break;
		case 'Moderation in':
			$boards = $xml->createElement('boards');
			foreach($contentelement->find('ul li') as $board) {
				$board = pq($board)->find('a');
				$boardurl = substr($board->attr('href'), 7);
				$boardname = $board->html();
				$boardxml = $xml->createElement('board');
				$boardxml->appendChild($xml->createElement('name', $boardname));
				$boardxml->appendChild($xml->createElement('url', $boardurl));
				$boards->appendChild($boardxml);
			}
			$elementxml->appendChild($boards);
			$type = 'boards';
			break;
		default:
			$content = strip_tags($contentelement->html());
			$elementxml->appendChild($xml->createElement('content', trim($content)));
			$type = 'text';
			break;
		}
		$typexml = $xml->createAttribute('type');
		$typexml->appendChild($xml->createTextNode($type));
		$elementxml->appendChild($typexml);
		$result->appendChild($elementxml);
	}
	$element = $doc->find('div#avatarBox p.avatar img')->eq(0);
	$content = $element->attr('src');
	$typexml = $xml->createAttribute('type');
	$typexml->appendChild($xml->createTextNode('avatar'));
	$elementxml = $xml->createElement('element');
	$elementxml->appendChild($xml->createElement('name', 'Avatar'));
	$elementxml->appendChild($xml->createElement('src', $content));
	$elementxml->appendChild($typexml);
	$result->appendChild($elementxml);
	return $result;
}

function getMessenger($xml, $result, $element) {
	$messengerxml = $xml->createElement('messenger');
	$messenger = $element->find('a');
	foreach($messenger as $m) {
		$m = pq($m);
		$url = $m->attr('href');
		if(strpos($url, 'icq.com') !== false) {
			$link =  preg_replace('!\D!', '', $url);
			$messengerxml->appendChild($xml->createElement('icq', $link));
		}
		elseif(strpos($url, 'aim') !== false) {
			$link =  preg_replace('!aim:GoIM\?screenname=!', '', $url);
			$messengerxml->appendChild($xml->createElement('aim', $link));
		}
		elseif(strpos($url, 'skype') !== false) {
			$link =  preg_replace('!(\w+?\:)|(\?\w+)!', '', $url);
			$messengerxml->appendChild($xml->createElement('skype', $link));
		}
	}
	$result->appendChild($messengerxml);
	return $result;
}

xmlrpc_register_function('getAboutOfProfile', array('sid', 'user'), 'rpc_getAboutOfProfile');
