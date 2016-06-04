<?php

require_once('./lib.php');

role('webui');

if (time() < $contest_start_time) {
	header('Content-Type: application/json');
	echo json_encode(array('status' => 'error', 'message' => 'Contest not started yet.'));
	exit;
}

$user = session_user($_GET['session_id']);
$problem = get_problems_for_user($user);

header('Content-Type: application/json');

echo json_encode($problem);
