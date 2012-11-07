<?php

function lima_loggedin($sid) {
	global $url_homepage;
	$doc = get_cached_any_cookie($url_homepage, "sid=$sid");
	foreach($doc->find('a[href=/usercp]') as $logout)
		return true;
	return false;
}

function lima_checklogin($xml, $result, $sid) {
	global $url_homepage;
	if(lima_loggedin($sid))
		return true;
	$result->appendChild($xml->createElement('notloggedin'));
	return false;
}
