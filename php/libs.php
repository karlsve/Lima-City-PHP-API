<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL ^ E_STRICT);

$url_login	= 'https://www.lima-city.de/login';
$url_logout	= 'https://www.lima-city.de/logout';
$url_homepage	= 'https://www.lima-city.de/homepage';
$url_profile	= 'https://www.lima-city.de/profile';
$url_profiles	= 'https://www.lima-city.de/profiles';
$url_messages	= 'https://www.lima-city.de/messages';
$url_board	= 'https://www.lima-city.de/board';
$url_thread	= 'https://www.lima-city.de/thread';
$url_homepage	= 'https://www.lima-city.de/homepage';
$url_status	= 'http://lima-status.de/';

require_once('phpQuery/phpQuery.php');
require_once('curl.php');
require_once('xml.php');
require_once('login.php');
require_once('status.php');
require_once('profile.php');
require_once('messages.php');
require_once('forum.php');
require_once('homepage.php');

?>