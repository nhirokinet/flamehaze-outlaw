<?php

if (count(get_included_files()) === 1) { exit(0); }

//role('webui');

$problem = array();
$stmt = $pdo->prepare('SELECT screen_name, ifnull((SELECT SUM(point) FROM problems LEFT JOIN (SELECT * FROM submissions WHERE judge_status = \'accepted\' GROUP BY user_id, problem_id) s ON s.problem_id = problems.id WHERE user_id = users.id), 0) AS score,  ifnull((SELECT count(*) AS c FROM submissions WHERE user_id = users.id AND (judge_status = \'tle\' OR judge_status = \'wrong_answer\') AND problem_id IN (SELECT DISTINCT problem_id FROM submissions WHERE user_id = users.id AND judge_status = \'accepted\')), 0) AS miss_count, ifnull((SELECT count(*) AS c FROM submissions WHERE user_id = users.id AND (judge_status = \'tle\' OR judge_status = \'wrong_answer\') AND problem_id IN (SELECT DISTINCT problem_id FROM submissions WHERE user_id = users.id AND judge_status = \'accepted\')), 0) * :penalty_ratio + (SELECT MAX(consumed_time) FROM (SELECT user_id, MIN(unix_timestamp(submissions.created_at)) - :contest_start_time AS consumed_time FROM submissions WHERE judge_status = \'accepted\' GROUP BY user_id, problem_id) tmp_table WHERE user_id = users.id) as penalty FROM users ORDER BY score DESC, penalty;');

$stmt->bindValue(':contest_start_time', $contest_start_time, PDO::PARAM_INT);
$stmt->bindValue(':penalty_ratio', $penalty_ratio, PDO::PARAM_INT);
$stmt->execute();

while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
	array_push($problem, $item);
}

header('Content-Type: application/json');

echo json_encode($problem);
