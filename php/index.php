<?php

require_once('libs.php');

//header('Content-type: text/xml; charset=ISO-8859-1');
header('Content-type: text/xml; charset=utf-8');

$dom = new DOMImplementation();

$dtd = '/xml/lima.dtd';
$xsl = '/xml/lima.xsl';
$namespace = 'http://www.lima-city.de/xml/';
$qualifiedname = 'lima-city';
//$doctype = $dom->createDocumentType($qualifiedname, '', $dtd);

$xml = new DOMDocument('1.0', 'utf-8');
//$xml = $dom->createDocument('', '', $doctype);
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;
$dom->encoding = 'utf-8';
$xslt = $xml->createProcessingInstruction('xml-stylesheet', "type=\"text/xsl\" href=\"$xsl\"");
$xml->appendChild($xslt);

$user = null;

if(isset($_GET['sid'])) {
	$sid = $_GET['sid'];
	$root = $xml->createElementNS($namespace, 'lima');

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
					$goto = "location: ?sid=$sid&action=thread&name={$_REQUEST['name']}"; 
					break;
				case 'homepage':
					//header('content-type: text/plain; charset=utf-8');
					$homepage = getHomepage($xml, $sid);
					$root->appendChild($homepage['threads']);
					//exit();
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
			$root->appendChild($actions);
		}

		$username = $xml->createElement('username', $user);
		$root->appendChild($username);

	}

	$loggedin = $xml->createElement('loggedin');
	$loggedintext = $xml->createTextNode($isloggedin ? 'yes' : 'no');
	$loggedin->appendChild($loggedintext);
	$root->appendChild($loggedin);

	$xml->appendChild($root);

	if(!empty($goto))
		header($goto);
} else {
	if(isset($_POST['action']) && ($_POST['action'] == 'login')) {
		$result = login($_POST['user'], $_POST['pass']);

		$root = $xml->createElementNS($namespace, 'lima');

		$statuscodetext = 'ok';
		if($result === false)
			$statuscodetext = 'error';
		if($result === 0)
			$statuscodetext = 'passwd';
		$status = $xml->createElement('errorcode', $statuscodetext);
		$root->appendChild($status);

		if($result != false) {
			header('location: ?sid=' . urlencode($result));
			$session = $xml->createElement('session');
			$sid = $xml->createTextNode($result);
			$session->appendChild($sid);
			$root->appendChild($session);
		}

		$loggedin = $xml->createElement('loggedin', $result != false ? 'yes' : 'no');
		$root->appendChild($loggedin);

		$xml->appendChild($root);
	} else {
		$root = $xml->createElementNS($namespace, 'lima');
		$loggedin = $xml->createElement('loggedin', 'no');
		$root->appendChild($loggedin);
		$xml->appendChild($root);
	}
}

echo($xml->saveXML());

?>