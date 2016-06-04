<?php

require_once('./lib.php');
role('webui_admin');

$problem = array();

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();

while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
	array_push($problem, $item);
}

header('Content-Type: application/json');

echo json_encode($problem);
