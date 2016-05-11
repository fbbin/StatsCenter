<?php
require_once '/data/www/public/sdk/StatsCenter.php';

\StatsCenter::$module_id = 1000338;
\StatsCenter::$log_svr_ip = '127.0.0.1';
\StatsCenter::log(3, 'test', 'hello', "world");

