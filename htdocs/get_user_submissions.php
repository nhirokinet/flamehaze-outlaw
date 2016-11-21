<?php

require_once('./lib.php');
role('webui');

header('Content-Type: application/json');

$user = session_user($_GET['session_id']);
echo json_encode(get_user_submissions($user));
