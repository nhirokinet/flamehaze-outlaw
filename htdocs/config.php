<?php

date_default_timezone_set('Asia/Tokyo');

$pdo_database = 'mysql:host=localhost;dbname=outlaw;charset=utf8';
$pdo_username = 'outlaw';
$pdo_password = '';

$url_root = '/flamehaze-outlaw';

$contest_start_time = strtotime('1970/01/02 00:00:00');
$contest_end_time   = strtotime('2099/12/31 00:00:00');

$source_code_length_limit = 100000;

$penalty_ratio = 240;
		
// below are accounts to accept
// currently, only one account for each role.

$flamehaze_username = 'hoge';
$flamehaze_password = 'fuga';

$webui_username = 'hoge2';
$webui_password = 'fuga2';
