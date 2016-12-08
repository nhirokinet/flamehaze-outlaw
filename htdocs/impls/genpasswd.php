<?php
if (count(get_included_files()) === 1) { exit(0); }

echo password_hash($_GET['passwd'], PASSWORD_DEFAULT);
