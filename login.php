<?php
require_once('./lib.php');
role('webui');

header('Content-Type: application/json');

$res = login($_POST['screen_name'], $_POST['passwd']);

if ($res === false) {
	echo json_encode(array('status'=>'failure', 'error'=>'auth fail'));
} else {
	echo json_encode(array('status'=>'success', 'session_id'=>$res['session_id']));
}
