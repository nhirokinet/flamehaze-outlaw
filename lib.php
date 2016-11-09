<?php

require(dirname(__FILE__) . '/config.php');

// connect to mysql and find the suitable problem and return as json.
$pdo = new PDO($pdo_database, $pdo_username, $pdo_password);

function authFlamehaze ($username, $passwd) {
	global $flamehaze_username;
	global $flamehaze_password;

	return ($username===$flamehaze_username && $passwd===$flamehaze_password);
}

function authWebUI ($username, $passwd) {
	global $webui_username;
	global $webui_password;

	return ($username===$webui_username && $passwd===$webui_password);
}

function authWebUIAdmin ($username, $passwd) {
	return false;
}

function normalizeOutputText ($in) {
	$newlines = array("\r\n", "\r");

	$in = str_replace($newlines, "\n", $in);
	return $in;
}

function get_user ($user_id) {
	global $pdo;

	$stmt = $pdo->prepare('SELECT * FROM users WHERE id = :user_id');
	$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->execute();

	$item = $stmt->fetch(PDO::FETCH_ASSOC);

	if (! $item) {
		return false;
	}

	$user = array();
	$user['id'] = (int) $item['id'];
	$user['screen_name'] = $item['screen_name'];
	$user['email'] = $item['email'];

	return $user;
}

function session_user ($session_id) {
	global $pdo;

	$stmt = $pdo->prepare('SELECT * FROM sessions WHERE session_id = :session_id AND expire_at > NOW()');
	$stmt->bindValue(':session_id', $session_id, PDO::PARAM_STR);
	$stmt->execute();

	$item = $stmt->fetch(PDO::FETCH_ASSOC);

	if (! $item) {
		return null;
	}

	return get_user($item['user_id']);
}

function auth_as_role ($role) {
	if(!isset($_SERVER["PHP_AUTH_USER"])) {
		header('WWW-Authenticate: Basic realm="auth"');
		header('401 Unauthorized');

		echo '<h1>Authorization Required</h1>';
		// TODO: Must close connection
		exit;
	} else {
		if ($role === 'flamehaze') {
			if(authFlamehaze($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
				return true;
			}else{
				header('WWW-Authenticate: Basic realm="auth"');
				header('401 Unauthorized');

				echo '<h1>Authorization Required</h1>';
				// TODO: Must close connection
				exit;
			}
		} else if ($role === 'webui') {
			if(authWebUI($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
				return true;
			}else{
				header('WWW-Authenticate: Basic realm="auth"');
				header('401 Unauthorized');

				echo '<h1>Authorization Required</h1>';
				// TODO: Must close connection
				exit;
			}
		} else {
			exit;
		}
	}
	exit;
}

function role ($role) {
	return auth_as_role ($role);
}

// this function generates random function.
// cryptgraphical security is considered to be
// 4 bits per length
// $len should be even number
function random_string ($len) {
	return bin2hex(openssl_random_pseudo_bytes($len/2));
}

function signup ($screen_name, $password, $email) {
	global $pdo;

	if ($screen_name == null) {
		return array('status' => 'error', 'message' => 'screen name is empty');
	}
	if ($password == null) {
		return array('status' => 'error', 'message' => 'password is empty');
	}
	if(strlen($password) < 6) {
		return array('status' => 'error', 'message' => 'password is too short');

	}
	if ($email == null) {
		return array('status' => 'error', 'message' => 'email address is empty');
	}

	$stmt = $pdo->prepare('START TRANSACTION');
	$stmt->execute();

	$stmt = $pdo->prepare('SELECT * FROM users WHERE screen_name = :screen_name FOR UPDATE');
	$stmt->bindValue(':screen_name', $screen_name, PDO::PARAM_STR);
	$stmt->execute();

	if ($stmt->fetch(PDO::FETCH_ASSOC)) {
		// user already exist
		// TODO: proper transaction
		return array('status' => 'error', 'message' => 'screen_name already exists');
	}

	$stmt = $pdo->prepare('INSERT INTO users SET screen_name = :screen_name , email = :email ,
			passwd = :passwd , created_at = NOW()');
	$stmt->bindValue(':screen_name', $screen_name, PDO::PARAM_STR);
	$stmt->bindValue(':passwd', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
	$stmt->bindValue(':email', $email, PDO::PARAM_STR);
	$stmt->execute();

	$stmt = $pdo->prepare('COMMIT');
	$stmt->execute();

	return array('status' => 'success');
}

function login ($screen_name, $password) {
	global $pdo;

	$stmt = $pdo->prepare('SELECT * FROM users WHERE screen_name = :screen_name');
	$stmt->bindValue(':screen_name', $screen_name, PDO::PARAM_STR);
	$stmt->execute();

	$user = array();

	while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$user = $item;
	}

	if ($user == array()) {
		return false;
	}

	if (password_verify($password, $user['passwd'])) {
		$session_id = random_string(96);

		$stmt = $pdo->prepare('INSERT INTO sessions SET session_id = :session_id , user_id = :user_id,
				created_at = NOW(),  expire_at = NOW() + INTERVAL 2 WEEK');
		$stmt->bindValue(':session_id', $session_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_STR);
		$stmt->execute();

		return array('session_id' => $session_id);
	}
	return false;
}

function logout ($session_id) {
	global $pdo;
	$stmt = $pdo->prepare('DELETE FROM sessions WHERE session_id = :session_id');
	$stmt->bindValue(':session_id', $session_id, PDO::PARAM_STR);
	$stmt->execute();
}

function submit_code($user, $problem_id, $language, $source_code) {
	// TODO; unexisting problem_id
	global $pdo, $contest_start_time, $contest_end_time, $source_code_length_limit;

	if (time() < $contest_start_time || $contest_end_time <= time()) {
		return array('status' => 'error', 'message' => 'Out of contest time');
	}

	if ($language === '') {
		return array('status' => 'error', 'message' => 'Language not selected');
	}

	if ($source_code === '') {
		return array('status' => 'error', 'message' => 'Source code is empty');
	}

	if (strlen($source_code) > 100000 ) {
		return array('status' => 'error', 'message' => 'Source code must be ' . number_format($source_code_length_limit) . ' bytes or less.');
	}

	$stmt = $pdo->prepare('INSERT INTO submissions SET user_id = :user_id , problem_id = :problem_id, language = :language,
			source_code = :source_code, judge_status = \'waiting\',
			created_at = NOW()');
	$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_STR);
	$stmt->bindValue(':problem_id', $problem_id, PDO::PARAM_STR);
	$stmt->bindValue(':language', $language, PDO::PARAM_STR);
	$stmt->bindValue(':source_code', $source_code, PDO::PARAM_STR);
	$stmt->execute();
	return array('status'=>'success', 'user'=>$user['id'], 'submission'=>array('problem_id'=>$problem_id, 'language'=>$language, 'source_code'=>$source_code));
}

function get_user_submissions($user) {
	global $pdo;
	$ret = array();

	$stmt = $pdo->prepare('SELECT id, problem_id, user_id, language, source_code,
			IF(judge_status LIKE "soft_%" OR judge_status LIKE "judging_%", "judging", judge_status) AS judge_status, execution_time, memory_used_in_kb, build_time, error_message, created_at,
			(SELECT title FROM problems WHERE problems.id=submissions.problem_id) AS problem_title
			FROM submissions
			WHERE user_id = :user_id
			ORDER BY id DESC');
	$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);

	$stmt->execute();

	while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
		array_push($ret, $item);
	}
	return $ret;
}

function get_problems_for_user ($user) {
	global $pdo;
	$problem = array();

	$stmt = $pdo->prepare('SELECT *, (SELECT if(count(*)>0, 1, 0) AS accepted_judge FROM submissions WHERE judge_status=\'accepted\' AND problem_id=problems.id AND user_id = :user_id ) AS accepted_judge FROM problems');
	$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);

	$stmt->execute();

	while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
		array_push($problem, $item);
	}

	return $problem;
}
