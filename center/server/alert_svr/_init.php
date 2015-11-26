<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);
require dirname(__DIR__) . '/config.php';

Swoole\Error::$stop = false;
Swoole\Error::$echo_html = false;
Swoole\Error::$display = false;
