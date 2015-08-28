<?php
require __DIR__.'/mail.php';

$m = new \Apps\Mail();
$content = "<div><span style='color:red'>just for test</span></div>";
$res = $m->mail('shiguangqi@chelun.com','test',$content);
var_dump($res);