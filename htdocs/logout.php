<?php
require_once('./lib.php');
role('webui');

header('Content-Type: application/json');

logout($_POST['session_id']);
