<?php

if (count(get_included_files()) === 1) { exit(0); }

role('webui');

$user = session_user($_GET['session_id']);

echo json_encode(submit_code($user, $_POST['problem_id'], $_POST['language'], $_POST['source_code']));
