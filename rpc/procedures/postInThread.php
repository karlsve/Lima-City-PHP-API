<?php

function rpc_postInThread($xml, $result, $args) {
	global $url_origin;
	global $url_thread;

	$doc = phpQuery::newDocument(get_request_cookie("$url_thread/{$args->thread}/action%3Apost", "auth_token_session={$args->sid}"));
	addToCache("$url_thread/$thread/action%3Apost", $doc, "auth_token_session={$args->sid}");
	if(!lima_checklogin($xml, $result, $args->sid))
		return $result;

	$code = $doc->find('input[name="code"]')->attr('value');
	$threadid = $doc->find('input[id="threadId"]')->attr('value');
	$time = $doc->find('input[id="loadTime"]')->attr('value');

	$requestdata = array(
		'time'		=> $time,
		'id	'	=> $threadid,
		'count'		=> '1'
	);
	$response = post_request_cookie("$url_origin/ajax_replyeditor", $requestdata, "auth_token_session={$args->sid}");
	if($response > 0) {
		// FIXXME:
		// >> new posts cannot be recognized since the time and id is fetched shortly
		// >> before this status is queried
	}

	$requestdata = array(
		'code'		=> $code,
		'quoteIds'	=> isset($args->quotes) ? $args->quotes : '',
		'text'		=> $args->content,
		'secSave'	=> '1',
		'save'		=> 'save',
		'favourite'	=> '',
		'count'		=> '1'
	);

	$response = post_request_cookie("$url_thread/{$args->thread}/action%3Apost", $requestdata, "auth_token_session={$args->sid}");
	$result->appendChild($xml->createElement('response', 'ok'));

	return $result;
}
xmlrpc_register_function('postInThread', array('sid', 'thread', 'content', 'o:quotes'), 'rpc_postInThread');
