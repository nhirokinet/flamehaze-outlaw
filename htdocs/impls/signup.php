<?php

if (count(get_included_files()) === 1) { exit(0); }

role('webui');

header('Content-Type: application/json');

$res = signup($_POST['screen_name'], $_POST['passwd'], $_POST['email']);

echo json_encode($res);
