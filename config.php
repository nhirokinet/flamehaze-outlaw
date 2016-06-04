<?php

date_default_timezone_set('Asia/Tokyo');

$pdo_database = 'mysql:host=localhost;dbname=outlaw;charset=utf8';
$pdo_username = 'outlaw';
$pdo_password = '';

$contest_start_time = strtotime('2016/06/04 21:00:00');
$contest_end_time   = strtotime('2099/12/31 23:59:59');

$source_code_length_limit = 100000;

$penalty_ratio = 900;
		
// below are accounts to accept
// currently, only one account for each role.

$flamehaze_username = 'hoge';
$flamehaze_password = 'fuga';

$webui_username = 'hoge2';
$webui_password = 'fuga2';
