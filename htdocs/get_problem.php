<?php

require_once('./lib.php');

role('webui');

if (time() < $contest_start_time) {
	header('Content-Type: application/json');
	echo json_encode(array('status' => 'error', 'message' => 'Contest not started yet.'));
	exit;
}

$id = (array_key_exists('problem_id', $GLOBALS)) ? $GLOBALS['problem_id'] : (int)$_GET['id'];

$problem = array();

$stmt = $pdo->prepare('SELECT * FROM problems WHERE id = :id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$problem = $item;
}

header('Content-Type: application/json');

echo json_encode($problem);
