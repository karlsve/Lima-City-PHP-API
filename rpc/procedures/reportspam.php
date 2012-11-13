<?php

function rpc_reportSpam($xml, $result, $args) {
	global $url_reportspam;
	$raw = get_request_cookie("$url_reportspam/{$args->id}", "sid={$args->sid}");
	$doc = phpQuery::newDocument($raw);
	if(strpos($raw, 'window.location.href = \'/login/goto:__board\';') !== false) {
		$result->appendChild($xml->createElement('notloggedin'));
		return $result;
	}
	if($doc->find('html > head > title')->html() == 'Nochmals Danke!') {
		$result->appendChild($xml->createElement('result', 'reported'));
		return $result;
	}

	$comment = isset($args->comment) ? $args->comment : '';
	$code = $doc->find('input[name="code"]')->attr('value');
	$data = array(
		'comment'	=> $comment,
		'code'		=> $code,
		'0'		=> $args->id,
		'save'		=> '1'
	);
	$post = '';
	foreach($data as $name => $value)
		$post .= '&' . urlencode($name) . '=' . urlencode($value);
	$post = substr($post, 1);
	$r = phpQuery::newDocument(post_request_cookie($url_reportspam, $post, "sid={$args->sid}", "$url_reportspam/{$args->id}"));
	$c = 'error';
	if($r->find('html > head > title')->html() == 'Danke!')
		$c = 'OK';
	$result->appendChild($xml->createElement('result', $c));
	return $result;
}

xmlrpc_register_function('reportSpam', array('sid', 'id', 'o:comment'), 'rpc_reportSpam');
