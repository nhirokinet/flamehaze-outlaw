<?php

require_once('./lib.php');

role('webui');

echo json_encode(session_user($_GET['session_id']));
