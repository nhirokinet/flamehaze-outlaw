<?php

require_once('./lib.php');


//role('webui');

$problem = array();

$stmt = $pdo->prepare('SELECT screen_name, ifnull((SELECT SUM(point) FROM problems LEFT JOIN (SELECT * FROM submissions WHERE judge_status = \'accepted\' GROUP BY user_id, problem_id) s ON s.problem_id = problems.id WHERE user_id = users.id), 0) AS score,  ifnull((SELECT count(*) AS c FROM submissions WHERE user_id = users.id AND (judge_status = \'tle\' OR judge_status = \'wrong_answer\') AND problem_id IN (SELECT DISTINCT problem_id FROM submissions WHERE user_id = users.id AND judge_status = \'accepted\')), 0) AS miss_count, ifnull((SELECT count(*) AS c FROM submissions WHERE user_id = users.id AND (judge_status = \'tle\' OR judge_status = \'wrong_answer\') AND problem_id IN (SELECT DISTINCT problem_id FROM submissions WHERE user_id = users.id AND judge_status = \'accepted\')), 0) * :penalty_ratio + (SELECT unix_timestamp(max(created_at)) - :contest_start_time FROM submissions WHERE user_id = users.id AND judge_status = \'accepted\') as penalty FROM users ORDER BY score DESC, penalty;');

$stmt->bindValue(':contest_start_time', $contest_start_time, PDO::PARAM_INT);
$stmt->bindValue(':penalty_ratio', $penalty_ratio, PDO::PARAM_INT);
$stmt->execute();

while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
	array_push($problem, $item);
}

header('Content-Type: application/json');

echo json_encode($problem);
