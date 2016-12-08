<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/lib.php');

$url = $_SERVER['PATH_INFO'];

if (strpos($url, $url_root) === 0) {
	$url = substr($url, strlen($url_root));
}

// TODO: these should be called as function or something else, not by require

switch ($url) {
	case '/get_task_to_do.json':
		require_once(__DIR__ . '/impls/get_unjudged.php');
		break;

	case '/put_task_result.json':
		require_once(__DIR__ . '/impls/put_result.php');
		break;

	case '/submit_code.json':
		require_once(__DIR__. '/impls/submit_code.php');
		break;

	case '/standings.json':
		require_once(__DIR__ . '/impls/get_standings.php');
		break;

	case '/submit_code.json':
		require_once(__DIR__ . '/impls/submit_code.php');
		break;

	case '/accounts/login.json':
		require_once(__DIR__. '/impls/login.php');
		break;

	case '/accounts/logout.json':
		require_once(__DIR__. '/impls/login.php');
		break;

	case '/accounts/signup.json':
		require_once(__DIR__. '/impls/signup.php');
		break;

	case '/accounts/self/whoami.json':
		require_once(__DIR__. '/impls/whoami.php');
		break;

	case '/accounts/self/submissions.json':
		require_once(__DIR__ . '/impls/get_user_submissions.php');
		break;

	case '/problems/list.json':
		require_once(__DIR__. '/impls/get_problems.php');
		break;

	default:
		// case which cannnot be routed by equal

		// /problems/123.json

		if (preg_match('/\A(\\/problems\\/)([0-9]*)\\.json\z/', $url, $match)) {
			$GLOBALS['problem_id'] = (int)($match[2]);
			require_once(__DIR__. '/impls/get_problem.php');
			break;
		}

		// No matching URL

		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		break;
}
