<?php

function getForum($xml, $sid) {
	global $url_board;
	$doc = phpQuery::newDocument(get_request_cookie($url_board, "sid=$sid"));
	$doc->find('img')->remove();
	$doc->find('script')->remove();
	return getBoards($xml, $doc);
}

function getBoards($xml, $doc) {
	$entries = $doc->find('tbody');
	$forum = $xml->createElement('forum');
	foreach($entries->find('tr') as $entry) {
		$entry = pq($entry);
		$key = $entry->attr('class');
		$td = $entry->find('td:nth-child(1)');
		$board = $xml->createElement('board');
		$namelink = $td->find('strong')->find('a');
		$name = $xml->createElement('name', trim($namelink->html()));
		$name_attr = $xml->createAttribute('url');
		$name_attr->value = preg_replace('#^.*/#', '', $namelink->attr('href'));
		$name->appendChild($name_attr);
		$description = $xml->createElement('description', trim($td->find('em')->html()));
		$moderatoren = $td->find('small')->find('a');
		$moderatorenxml = $xml->createElement('moderatoren');
		foreach($moderatoren as $mod) {
			$mod = pq($mod);
			$moderatorxml = $xml->createElement('moderator', $mod->html());
			$moderatorenxml->appendChild($moderatorxml);
		}
		$td = $entry->find('td:nth-child(2)');
		$topics = $xml->createElement('topics', trim($td->html()));
		$td = $entry->find('td:nth-child(3)');
		$answers = $xml->createElement('answers', trim($td->html()));
		$td = $entry->find('td:nth-child(4)');
		$newestThread = $td;
		$newestThreadxml = $xml->createElement('newestThread');
		$threadtitle = $newestThread->find('a:nth-child(1)');
		$title = $xml->createElement('title', $threadtitle->html());
		$titlelink = $xml->createAttribute('url');
		$titlelink->value = preg_replace('#^.*/#', '', $threadtitle->attr('href'));
		$title->appendChild($titlelink);
		$author = $xml->createElement('author', $newestThread->find('a:nth-child(4)')->html());
		$date = $xml->createElement('date', $newestThread->find('small')->html());
		$newestThreadxml->appendChild($title);
		$newestThreadxml->appendChild($author);
		$newestThreadxml->appendChild($date);

		$board->appendChild($name);
		$board->appendChild($description);
		$board->appendChild($moderatorenxml);
		$board->appendChild($topics);
		$board->appendChild($answers);
		$board->appendChild($newestThreadxml);
		$forum->appendChild($board);
	}
	return($forum);
}

function getBoard($xml, $sid, $board) {
	global $url_board;
	$doc = phpQuery::newDocument(get_request_cookie("$url_board/$board", "sid=$sid"));
	$content = $doc->find('#content');
	$threadlist = $content->find('tr:has(td)');
	$boardxml = $xml->createElement('board');

	$name = $doc->find('h2')->contents();

	$boardname = $xml->createAttribute('name');
	$boardname->appendChild($xml->createTextNode($name));
	$boardxml->appendChild($boardname);
	foreach($threadlist as $thread) {
		$thread = pq($thread);
		$threadxml = $xml->createElement('thread');
		$threadname = $thread->find('td:nth-child(1) > a');
		$name = $threadname->html();
		$url = preg_replace('#.*/#', '', $threadname->attr('href'));
		$threadviews = $thread->find('td:nth-child(2)');
		$views = trim($threadviews->html());
		$threadreplies = $thread->find('td:nth-child(3)');
		$replies = trim($threadreplies->html());
		$threadauthor = $thread->find('td:nth-child(4) > a');
		$author = $threadauthor->html();
		$threaddate = $thread->find('td:nth-child(5) > small');
		$date = $threaddate->html();
		$threadnamexml = $xml->createElement('name', $name);
		$threadurlxml = $xml->createElement('url', $url);
		$threadviewsxml = $xml->createElement('views', $views);
		$threadrepliesxml = $xml->createElement('replies', $replies);
		//$threadauthorxml = $xml->createElement('author', $author);
		$deleted = 'false';
		if(preg_match('|<del>(.*?)</del>|', $author, $match)) {
			$author = $match[1];
			$deleted = 'true';
		}
		$threadauthorxml = $xml->createElement('author', $author);
		$del = $xml->createAttribute('deleted');
		$del->appendChild($xml->createTextNode($deleted));
		$threadauthorxml->appendChild($del);
		$threaddatexml = $xml->createElement('date', $date);
		$threadxml->appendChild($threadnamexml);
		$threadxml->appendChild($threadurlxml);
		$threadxml->appendChild($threadviewsxml);
		$threadxml->appendChild($threadrepliesxml);
		$threadxml->appendChild($threadauthorxml);
		$threadxml->appendChild($threaddatexml);
		$boardxml->appendChild($threadxml);
	}
	return($boardxml);
}

function getThread($xml, $sid, $url) {
	global $url_thread;
	$doc = phpQuery::newDocument(get_request_cookie("$url_thread/$url/page%3A0/perpage%3A100", "sid=$sid"));
	$postlist = $doc->find('ol.posts');
	$posts = $postlist->find('li:has(.author)');
	$thread = $xml->createElement('thread');

	$name = $doc->find('h2')->contents();
	$threadname = $xml->createElement('name');
	$threadname->appendChild($xml->createTextNode($name));
	$thread->appendChild($threadname);

	$threadurl = $xml->createElement('url');
	$threadurl->appendChild($xml->createTextNode($url));
	$thread->appendChild($threadurl);

	foreach($posts as $post) {
		$post = pq($post);
		$thread->appendChild(getPost($xml, $post));
	}
	return($thread);
}

function getPost($xml, $post) {
	$authordiv = $post->find('div.author');
	$type = $post->attr('class');
	$dateelement = $authordiv->find('small:nth-child(1)');
	$date = $dateelement->find('a')->html();
	$postid = $dateelement->find('a')->attr('name');
	$userelement = $authordiv->find('p.un');
	$postauthor = $userelement->find('a')->html();
	$postauthordeleted = 'false';
	if(strlen($postauthor) == 0) {
		$postauthordeleted = 'true';
		$postauthor = $userelement->find('del')->html();
	}

	$contentdiv = $post->find('div.content');
	//$contentdiv->find(':not(a)')->remove();
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
	//$userxml = $xml->createElement('user', $postauthor);
	//$contentxml = $xml->createElement('content', $content);
	$userxml = $xml->createElement('user', $postauthor);
	$del = $xml->createAttribute('deleted');
	$del->appendChild($xml->createTextNode($postauthordeleted));
	$userxml->appendChild($del);



	$root = $xml->createElement('post');
	$root->appendChild($typexml);
	$root->appendChild($datexml);
	$root->appendChild($postidxml);
	$root->appendChild($userxml);
	$root->appendChild($contentxml);

	return($root);
}

function parseContent($xml, $doc) {
	if($doc->nodeType == XML_TEXT_NODE)
		return($xml->createElement('text', xml_format_content(trim($doc->wholeText))));
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
		} else {
			if(preg_match('#(http|https|ftp|ftps|sftp)://#', $url) == 0)
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
		if(preg_match('#(http|https|ftp|ftps|sftp)://#', $src) == 0)
			$src = "https://www.lima-city.de/$src";
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
		$src = $doc->attributes->getNamedItem('src')->value;
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


///////////////////////////////////////////////////////////////////////////////////////////
function postInThread($xml, $sid, $thread, $content, $quotes) {
	global $url_thread;
	$doc = phpQuery::newDocument(get_request_cookie("$url_thread/$thread/action%3Apost", "sid=$sid"));
	$code = $doc->find('input[name="code"]')->attr('value');
	$threadid = $doc->find('input[id="threadId"]')->attr('value');
	$time = $doc->find('input[name="time"]')->attr('value');

	$requestdata = array(
		'time'		=> $time,
		'id	'	=> $threadid,
		'count'		=> '1'
	);
	$result = post_request_cookie("https://www.lima-city.de/ajax_replyeditor", $requestdata, "sid=$sid");
	if($result > 0) {
		// FIXXME:
		// >> new posts cannot be recognized since the time and id is fetched shortly
		// >> before this status is queried
	}

	$requestdata = array(
		'code'		=> $code,
		'quoteIds'	=> $quotes,
		'text'		=> $content,
		'secSave'	=> '1',
		'save'		=> 'save',
		'favourite'	=> 'none',
		'count'		=> '1'
	);

	$result = post_request_cookie("$url_thread/$thread/action%3Apost", $requestdata, "sid=$sid");

	return($xml->createElement('result', 'ok'));
}


?>