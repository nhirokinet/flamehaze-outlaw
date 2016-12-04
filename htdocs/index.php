<?php

require_once(__DIR__ . '/config.php');

$url = $_SERVER['PATH_INFO'];

if (strpos($url, $url_root) === 0) {
	$url = substr($url, strlen($url_root));
}

switch ($url) {
	case '/get_task_to_do.json':
		require_once(__DIR__ . '/get_unjudged.php');
		break;

	case '/put_task_result.json':
		require_once(__DIR__ . '/put_result.php');
		break;

	default:
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		break;
}
