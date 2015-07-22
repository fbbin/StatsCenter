<?php
$client = new swoole_client(SWOOLE_TCP);
$client->connect('127.0.0.1', 8501);
$client->send("POST /app/stats/ HTTP/1.1\r\nHost: app-dev.eclicks.cn:8501\r\n");

//$body = json_encode(['hello' => 'world', 'test' => 'rango', 'vlaue' => 1234]);
$body = file_get_contents('test');
$_body = gzencode($body);
$client->send("Content-Length: ".strlen($_body)."\r\n\r\n");
$client->send($_body);
echo $client->recv();
