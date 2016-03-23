<?php
namespace App;

class StatsData
{
    static function fillZero4Time($s)
    {
        $s = intval($s);
        if ($s < 10)
        {
            return '0' . $s;
        }
        else
        {
            return $s;
        }
    }

    /**
     * 将时间数值转为字符串
     * @param $time_key
     * @return string
     */
    static function getTimerStr($time_key)
    {
        $_h = $time_key / 12.0;
        $h = intval($_h);
        $_m = round(((($_h - $h) * 60) / 5) * 5);
        return self::fillZero4Time($h) . ':' . self::fillZero4Time($_m);
    }
}