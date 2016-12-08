<?php

if (count(get_included_files()) === 1) { exit(0); }

role('webui');

echo json_encode(session_user($_GET['session_id']));
