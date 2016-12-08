<?php

if (count(get_included_files()) === 1) { exit(0); }

role('webui');

header('Content-Type: application/json');

logout($_POST['session_id']);
