<?php

if (count(get_included_files()) === 1) { exit(0); }

role('flamehaze');

$result = json_decode(file_get_contents("php://input"), TRUE);

header('Content-Type: application/json');

// fetch information

$stmt = $pdo->prepare('SELECT * FROM submissions WHERE (
			(
			    judge_status LIKE "judging%" AND judge_server = :hostname AND id = :submission_id
			)
		       )');

$stmt->bindValue(':hostname', $result['judger_hostname'], PDO::PARAM_STR);
$stmt->bindValue(':submission_id', $result['submission_id'], PDO::PARAM_INT);
$stmt->execute();


$item = NULL;

if (! $item = $stmt->fetch(PDO::FETCH_ASSOC)) {
	echo json_encode(array('result'=>'failure'));
	exit(1);
}


$stmt = $pdo->prepare('SELECT * FROM problem_test_cases WHERE problem_id = :problem_id ORDER BY id');
$stmt->bindValue(':problem_id', $item['problem_id'], PDO::PARAM_INT);
$stmt->execute();

if ($result['status'] !== 'success') {
	$item_status = $result['status'];

	if ($item['judge_status'] === 'judging_again' && substr($item_status, 0, 5) === 'soft_') {
		$item_status = str_replace('soft_', '', $item_status);
	}

	// TODO: soft status and hard status
	// Note: important in case $result['status'] is soft_*
	$stmt = $pdo->prepare('UPDATE submissions SET error_message = :errormsg , judge_status = :status , build_time = :buildtime , execution_time = :exetime , memory_used_in_kb = :memory_used_in_kb, judge_end_time = NOW() WHERE id = :id');

	$stmt->bindValue(':errormsg', array_key_exists('error_message', $result) ? substr($result['error_message'], 0, 1000) : '', PDO::PARAM_STR);
	$stmt->bindValue(':buildtime', floor($result['build_time'] * 1000.0 + 0.5), PDO::PARAM_INT);

	$longest_exec_time = 0;
	$largest_mem_used  = -1;

	foreach ($result['output_list'] as $output) {
		$longest_exec_time = max($lognest_exec-time, $output['execution_time']);
		$largest_mem_used  = max($largest_mem_used , $output['used_mem_kb']);
	}
	$stmt->bindValue(':exetime', floor($longest_exec_time * 1000.0 + 0.5), PDO::PARAM_INT);
	$stmt->bindValue(':memory_used_in_kb', $largest_mem_used, PDO::PARAM_INT);
	$stmt->bindValue(':status', $item_status, PDO::PARAM_STR);
	$stmt->bindValue(':id', $result['submission_id'], PDO::PARAM_INT);
	$stmt->execute();

	echo json_encode(array('result'=>$status, 'execution_time'=>$longest_exec_time));
	exit(0);
}

// after here, assumes that the judger returned successs
$test_cases = array();

while ($i = $stmt->fetch(PDO::FETCH_ASSOC)) {
	array_push($test_cases, $i['output_text']);
}

$longest_exec_time = 0.0; // unit is second and type is float 
$largest_mem_used  = 0;   // unit is kilobytes according to GNU time

$status = 'accepted';

$outputlist_index = -1;
foreach ($test_cases as $test_case) {
	$outputlist_index++;

	if (count($result['output_list']) <= $outputlist_index) {
		$status = 'error'; // TODO: rather alert
		break;
	}

	$output = $result['output_list'][$outputlist_index];

	$out = $output['output'];
	$exetime = $output['execution_time'];
	$staus_in_exe = $output['status']; // TODO: assuming 'success'

	$longest_exec_time = max($longest_exec_time, $exetime);
	$largest_mem_used  = max($largest_mem_used , $output['used_mem_kb']);

	if ($exetime > 5000) { // TODO: Here should not be hard-coded
		$status = 'tle';
		break;
	}

	if (normalizeOutputText($out) !== normalizeOutputText($test_case)) {
		$status = 'wrong_answer';
	}
}

$stmt = $pdo->prepare('UPDATE submissions SET error_message = :errormsg , judge_status = :status , build_time = :buildtime , execution_time = :exetime, memory_used_in_kb = :memory_used_in_kb, judge_end_time = NOW() WHERE id = :id');

$stmt->bindValue(':errormsg', array_key_exists('error_message', $result) ? substr($result['error_message'], 0, 1000) : '', PDO::PARAM_STR);
$stmt->bindValue(':buildtime', floor($result['build_time'] * 1000.0 + 0.5), PDO::PARAM_INT);
$stmt->bindValue(':memory_used_in_kb', $largest_mem_used, PDO::PARAM_INT);
$stmt->bindValue(':exetime', floor($longest_exec_time * 1000.0 + 0.5), PDO::PARAM_INT);
$stmt->bindValue(':status', $status, PDO::PARAM_STR);
$stmt->bindValue(':id', $result['submission_id'], PDO::PARAM_INT);
$stmt->execute();


echo json_encode(array('result'=>$status, 'execution_time'=>$longest_exec_time));
