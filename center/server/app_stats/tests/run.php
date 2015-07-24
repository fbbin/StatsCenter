<?php
require dirname(__DIR__) . '/_init.php';

$json = file_get_contents('./test');
$svr = new StatsCenter\AppStatsServer();
$svr->insertToDb($json);
