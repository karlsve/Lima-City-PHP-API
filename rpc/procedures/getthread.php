<?php

function rpc_getThread($xml, $result, $args) {
	global $url_thread;
	$url = "$url_thread/{$args->url}/page%3A0/perpage%3A100";
	$doc = phpQuery::newDocument(get_request_cookie($url, "sid={$args->sid}"));
	addToCache($url, $doc, "sid={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$posts = $doc->find('ol.posts li:has(.author)');

	$writable = false;
	foreach($doc->find('ul.actions li img[src="/images/layout/icons/page_add.png"]') as $img) {
		$writable = true;
		break;
	}
	$result->appendChild($xml->createElement('writable', $writable ? 'true' : 'false'));

	$closed = $doc->find('ul.actions li img[src="/images/layout/icons/lock.png"]')->count() != 0;
	$result->appendChild($xml->createElement('closed', $closed ? 'true' : 'false'));

	$name = $doc->find('h2')->get(0)->nodeValue;
	$result->appendChild($xml->createElement('name', $name));

	$result->appendChild($xml->createElement('url', $args->url));

	$postsxml = $xml->createElement('posts');
	foreach($posts as $post)
		$postsxml->appendChild(getPost($xml, pq($post)));
	$result->appendChild($postsxml);
	return $result;
}

function getPost($xml, $post) {
	$authordiv = $post->find('div.author');
	$type = $post->attr('class');
	$dateelement = $authordiv->find('small:nth-child(1)');
	$date = $dateelement->find('a')->html();
	$isauthor = $authordiv->find('small br')->count() == 1;
	$postid = $dateelement->find('a')->attr('name');
	$userelement = $authordiv->find('p.un');
	$postauthor = $userelement->find('a')->html();
	$authoronline = $userelement->find('img')->attr('alt') == 'Online';
	$postauthordeleted = 'false';
	if(strlen($postauthor) == 0) {
		$postauthordeleted = 'true';
		$postauthor = $userelement->find('del')->html();
	}
	$avatar = '';
	$role = 'deleted';
	$rank = 'deleted';
	$starcount = 0;
	$gulden = 0;
	if($postauthordeleted == 'false') {
		$avatarurl = $authordiv->find('p.avatar img')->attr('src');
		if($avatarurl != '') {
			preg_match('|/avatar/([^.]+\..+)$|', $avatarurl, $match);
			$avatar = $match[1];
		}
		$role = $authordiv->find('p.avatar em')->html();
		if($role == '')
			$role = 'Benutzer';
		$authordiv->find('p.avatar em')->remove(); // Moderator | Co-Administrator | ...
		$rank = $authordiv->find('p em')->html();
		$n = 3;
		if($avatar != '')
			$n = 4;
		if($rank == 'Niemand')
			$n--;
		$starcount = 0;
		if($rank != 'Niemand') {
			$stars = pq($authordiv->find('p')->get($n - 1))->find('img');
			$starcount = $stars->count();
		}
		$guldenraw = explode(' ', pq($authordiv->find('p')->get($n))->html());
		$gulden = $guldenraw[0];
	}

	$contentdiv = $post->find('div.content');
	$contentdiv->find('ul.actions')->remove();
	$contentdiv->find('p.clearing')->remove();
	$contentdiv->find('div.signature')->remove();

	$contentdiv->find('h3')->remove();
	$contentdiv->find('table')->remove();

	$content = trim($contentdiv->html());
	$contentxml = parsePostContent($xml, $content);

	if($type == '')
		$type = 'normal';

	$typexml = $xml->createElement('type', $type);
	$datexml = $xml->createElement('date', $date);
	$postidxml = $xml->createElement('id', $postid);
	$userxml = $xml->createElement('user', $postauthor);
	$del = $xml->createAttribute('deleted');
	$del->appendChild($xml->createTextNode($postauthordeleted));
	$userxml->appendChild($del);
	$te = $xml->createAttribute('author');
	$te->appendChild($xml->createTextNode($isauthor ? 'true' : 'false'));
	$userxml->appendChild($te);
	$online = $xml->createAttribute('online');
	$online->appendChild($xml->createTextNode($authoronline ? 'true' : 'false'));
	$userxml->appendChild($online);
	if($postauthordeleted == 'false') {
		$avatarxml = $xml->createAttribute('avatar');
		$avatarxml->appendChild($xml->createTextNode($avatar));
		$userxml->appendChild($avatarxml);
		$rankxml = $xml->createAttribute('rank');
		$rankxml->appendChild($xml->createTextNode($rank));
		$userxml->appendChild($rankxml);
		$guldenxml = $xml->createAttribute('gulden');
		$guldenxml->appendChild($xml->createTextNode($gulden));
		$userxml->appendChild($guldenxml);
		$rolexml = $xml->createAttribute('role');
		$rolexml->appendChild($xml->createTextNode($role));
		$userxml->appendChild($rolexml);
		$starcountxml = $xml->createAttribute('starcount');
		$starcountxml->appendChild($xml->createTextNode($starcount));
		$userxml->appendChild($starcountxml);
	}

	$root = $xml->createElement('post');
	$root->appendChild($typexml);
	$root->appendChild($datexml);
	$root->appendChild($postidxml);
	$root->appendChild($userxml);
	$root->appendChild($contentxml);

	return($root);
}

xmlrpc_register_function('getThread', array('sid', 'url'), 'rpc_getThread');
