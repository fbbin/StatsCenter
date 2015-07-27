<?php
$json = file_get_contents('s1.log');
$list = json_decode($json, true);


foreach($list as $li)
{
    if (empty($li['http']['url']) or empty($li['http']['method']))
    {
        var_dump($li);
    }
}
