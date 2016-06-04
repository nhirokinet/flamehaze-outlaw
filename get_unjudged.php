<?php

require_once('./lib.php');

role('flamehaze');

// get arguments from get parameter

$hostname = 'none';
$new_judge_status = 'judging';

if (array_key_exists('hostname', $_GET)) {
	$hostname = $_GET['hostname'];
}

// fetch the oldest unjudged problem
$stmt = $pdo->prepare('START TRANSACTION');
$stmt->execute();

$stmt = $pdo->prepare('SELECT * FROM submissions WHERE (
			(
			    judge_status = "waiting"
			) OR
			(
			    judge_status LIKE "soft_%" AND judge_server != :hostname
			) OR
			(
			    judge_status LIKE "judging%" AND
			    judge_start_time < DATE_SUB(NOW(), INTERVAL :timelimit SECOND) AND
			    judge_server != :hostname
			)
		       ) ORDER BY id LIMIT 1 FOR UPDATE');


$stmt->bindValue(':hostname', $hostname, PDO::PARAM_STR);
$stmt->bindValue(':timelimit', 120, PDO::PARAM_INT); // hard coded here....
$stmt->execute();

$problem = array('status'=>'empty');
$await = true;

while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$test_cases = array();

	if (substr($item['judge_status'], 0, 5) === 'soft_') {
		$new_judge_status = 'judging_again';
	}

	$tstmt = $pdo->prepare('SELECT * FROM problem_test_cases WHERE problem_id = :problem_id ORDER BY id');
	$tstmt->bindValue(':problem_id', $item['problem_id'], PDO::PARAM_INT);
	$tstmt->execute();

	while ($i = $tstmt->fetch(PDO::FETCH_ASSOC)) {
		array_push($test_cases, $i['input_text']);
	}

	$problem = array(
			'status' => 'success',
			'submission_id' => intval($item['id']),
			'source_code' => $item['source_code'],
			'language' => $item['language'],
			'input_list' => $test_cases,
		       );
	$await = false;
}

header('Content-Type: application/json');
if ($await) {
	echo json_encode($problem);
	exit(0);
}

$stmt = $pdo->prepare('UPDATE submissions SET judge_status = :judge_status , judge_server = :hostname ,judge_start_time = NOW() WHERE id = :id');
$stmt->bindValue(':hostname', $hostname, PDO::PARAM_STR);
$stmt->bindValue(':id', $problem['submission_id'], PDO::PARAM_INT);
$stmt->bindValue(':judge_status', $new_judge_status, PDO::PARAM_STR);
$stmt->execute();

$stmt = $pdo->prepare('COMMIT');
$stmt->execute();

echo json_encode($problem);
