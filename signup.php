<?php
require_once('./lib.php');
role('webui');

header('Content-Type: application/json');

$res = signup($_POST['screen_name'], $_POST['passwd'], $_POST['email']);

echo json_encode($res);
