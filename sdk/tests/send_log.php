<?php
require __DIR__.'/../api/php/StatsCenter.php';
StatsCenter::$log_svr_ip = '127.0.0.1';
StatsCenter::$module_id = 1000242;

for ($i = 0; $i < 1; $i++)
{
    $level = rand(0, 4);
    $userid = rand(10000, 99999);
    $now = time();
    $msg = "#$i 时间- $now -;我来自模块, 接口--ErrorLog Test. Line=" . __LINE__ . " File=" . __FILE__;
    StatsCenter::log($level, 'hello', 'world', $msg, $userid);
    usleep(10000);
}
