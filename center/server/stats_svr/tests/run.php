<?php
require dirname(__DIR__).'/_init.php';

$svr = new StatsCenter\StatsServer();
$logger = new Swoole\Log\EchoLog(__DIR__ . '/logs/server.log');
$svr = new StatsCenter\StatsServer();
$svr->setLogger($logger);

$data = file_get_contents(WEBPATH.'/logs/data.log');
$_data = unserialize($data);
$svr->insertToDB($_data);

