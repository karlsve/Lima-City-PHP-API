<?php

function post_request($url, $data) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	$data = curl_exec($curl);
	curl_close($curl);
	return($data);
}

function get_request($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	return($data);
}

function get_request_cookie($url, $cookie) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	return($data);
}

function post_request_cookie($url, $data, $cookie) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect: "));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$data = curl_exec($curl);
	curl_close($curl);
	return($data);
}

function post_request_raw($url, $data, $cookie = '') {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect: "));
	if($cookie != '')
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	$data = curl_exec($curl);
	curl_close($curl);

	// split header and contents
	$endofheader = strpos($data, "\r\n\r\n");
	$size = 4;
	if($endofheader === false) {
		$endofheader = strpos($data, "\n\n");
		$size = 2;
	}
	$header = substr($data, 0, $endofheader);
	$data = substr($data, $endofheader + $size);

	return(array('header' => $header, 'content' => $data));
}

function get_request_raw($url, $cookie = '') {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	curl_setopt($curl, CURLOPT_HEADER, true);
	if($cookie != '')
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	$data = curl_exec($curl);
	curl_close($curl);

	// split header and contents
	$endofheader = strpos($data, "\r\n\r\n");
	$size = 4;
	if($endofheader === false) {
		$endofheader = strpos($data, "\n\n");
		$size = 2;
	}
	$header = substr($data, 0, $endofheader);
	$data = substr($data, $endofheader + $size);

	return(array('header' => $header, 'content' => $data));
}

?>