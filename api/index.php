<?php

require_once('libs.php');

header('content-type: text/xml; charset=utf-8');
header('cache-control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('pragma: no-cache');
header('expires: Thu, 19 Nov 1981 08:52:00 GMT');

$dom = new DOMImplementation();

$dtd = '/xml/lima.dtd';
//$xsl = '/xml/lima.xsl';
$xsl = '../xml/lima.xsl';
$namespace = 'http://www.lima-city.de/xml/';
$qualifiedname = 'lima-city';
//$doctype = $dom->createDocumentType($qualifiedname, '', $dtd);

if(isset($_REQUEST['style']))
	$xsl = htmlspecialchars($_REQUEST['style']);

$xml = new DOMDocument('1.0', 'utf-8');
//$xml = $dom->createDocument('', '', $doctype);
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;
$dom->encoding = 'utf-8';
$xslt = $xml->createProcessingInstruction('xml-stylesheet', "type=\"text/xsl\" href=\"$xsl\"");
$xml->appendChild($xslt);

$user = null;

if(isset($_REQUEST['sid']) || isset($_COOKIE['sid'])) {
	$sid = NULL;

	if(isset($_COOKIE['sid']))
		$sid = $_COOKIE['sid'];
	if(isset($_REQUEST['sid']))
		$sid = $_REQUEST['sid'];

	$usecookie = isset($_COOKIE['sid']);

	$root = $xml->createElementNS($namespace, 'lima');
	$root->appendChild($xml->createElement('usecookie', $usecookie ? 'true' : 'false'));

	$goto = '';

	$isloggedin = isLoggedin($sid);
	if($isloggedin) {
		$session = $xml->createElement('session');
		$session->appendChild($xml->createTextNode($sid));
		$root->appendChild($session);

		$user = getUsername($sid);

		if(isset($_REQUEST['action'])) {
			switch($_REQUEST['action']) {
				case 'logout':
					logout($sid);
					if(isset($_COOKIE['sid']))
						setcookie('sid', NULL, -1);
					header('location: .');
					exit();
					break;
				case 'serverstatus':
					$root->appendChild(serverstatus($xml));
					break;
				case 'myprofile':
					$root->appendChild(getUser($xml, $sid, $user));
					break;
				case 'profile':
					$root->appendChild(getUser($xml, $sid, $_REQUEST['user']));
					break;
				case 'profiles':
					$root->appendChild(getProfiles($xml, $sid));
					break;
				case 'messages':
					$root->appendChild(getMessages($xml, $sid));
					break;
				case 'forumlist':
					$root->appendChild(getForum($xml, $sid));
					break;
				case 'board':
					$root->appendChild(getBoard($xml, $sid, $_REQUEST['name']));
					break;
				case 'thread':
					$root->appendChild(getThread($xml, $sid, $_REQUEST['name']));
					break;
				case 'message':
					$root->appendChild(getMessage($xml, $sid, $_REQUEST['id']));
					break;
				case 'post':
					$root->appendChild(postInThread($xml, $sid, $_REQUEST['name'], $_REQUEST['text'], $_REQUEST['quotes']));
					$goto = $usecookie ? "location: ?action=thread&name={$_REQUEST['name']}" : "location: ?sid=$sid&action=thread&name={$_REQUEST['name']}";
					break;
				case 'homepage':
					$homepage = getHomepage($xml, $sid);
					$root->appendChild($homepage['threads']);
					break;
				case 'keepalive':
					get_request_cookie($url_keepalive, "sid=$sid");
					break;
			}
		} else {
			$actions = $xml->createElement('actions');
			$homepage = getHomepage($xml, $sid);
			if($homepage['hasthreads'])
				$actions->appendChild($xml->createElement('action', 'homepage'));
			$actions->appendChild($xml->createElement('action', 'forumlist'));
			$actions->appendChild($xml->createElement('action', 'messages'));
			$actions->appendChild($xml->createElement('action', 'serverstatus'));
			$actions->appendChild($xml->createElement('action', 'myprofile'));
			$actions->appendChild($xml->createElement('action', 'profiles'));
			$actions->appendChild($xml->createElement('action', 'keepalive'));
			$root->appendChild($actions);
		}

		$root->appendChild($xml->createElement('username', $user));
		$root->appendChild($xml->createElement('role', getRole($sid)));
	} else {
		if(isset($_COOKIE['sid']))
			setcookie('sid', NULL, -1);
		header('location: .');
		exit();
	}

	$loggedin = $xml->createElement('loggedin');
	$loggedintext = $xml->createTextNode($isloggedin ? 'yes' : 'no');
	$loggedin->appendChild($loggedintext);
	$root->appendChild($loggedin);

	$xml->appendChild($root);

	if(!empty($goto))
		header($goto);
} else {
	if(isset($_REQUEST['action'])) {
		$root = $xml->createElementNS($namespace, 'lima');
		switch($_REQUEST['action']) {
			case 'login':
				$result = login($_REQUEST['user'], $_REQUEST['pass']);

				$statuscodetext = 'ok';
				if($result === false)
					$statuscodetext = 'error';
				if($result === 0)
					$statuscodetext = 'passwd';
				$status = $xml->createElement('errorcode', $statuscodetext);
				$root->appendChild($status);

				if($result != false) {
					/*
					if(isset($_REQUEST['usecookie']) && ($_REQUEST['usecookie'] == 'true')) {
						setcookie('sid', $result, 0);
						header('location: .');
					} else
						header('location: ?sid=' . urlencode($result));
					*/
					if(isset($_REQUEST['usecookie']) && ($_REQUEST['usecookie'] == 'true'))
						setcookie('sid', $result, 0);
					$session = $xml->createElement('session', $result);
					$root->appendChild($session);
				}

				$loggedin = $xml->createElement('loggedin', $result != false ? 'yes' : 'no');
				$root->appendChild($loggedin);
				break;
			case 'getuid':
				$name = $_REQUEST['user'];
				$uid = getUserID($name);
				$userinfo = $xml->createElement('userinfo');
				$userinfo->appendChild($xml->createElement('name', $name));
				$userinfo->appendChild($xml->createElement('uid', $uid));
				$root->appendChild($userinfo);
				$root->appendChild($xml->createElement('loggedin', 'no'));
				break;
			default:
				$loggedin = $xml->createElement('loggedin', 'no');
				$root->appendChild($loggedin);
				break;
		}
		$xml->appendChild($root);
	} else {
		$root = $xml->createElementNS($namespace, 'lima');
		$loggedin = $xml->createElement('loggedin', 'no');
		$root->appendChild($loggedin);
		$xml->appendChild($root);
	}
}

echo($xml->saveXML());
exit();
