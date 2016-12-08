<?php

if (count(get_included_files()) === 1) { exit(0); }

role('webui');

header('Content-Type: application/json');

$user = session_user($_GET['session_id']);
echo json_encode(get_user_submissions($user));
