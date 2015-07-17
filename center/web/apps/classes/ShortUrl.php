<?php
namespace App;

class ShortUrl
{
    // 53 个字母数字乱序
    const ALPHABET = '4sF8y2KPuzRHixUtfGX3gcCTLhnASMe65NjpBw9YWDqbamEkQrd7J';
    const BASE = 53; // strlen(self::ALPHABET)
    const OFFSET = 10000;
    const PREFIX_LEN = 3;

    public static function encode($num)
    {
        $num = $num + self::OFFSET;
        $str = '';
        while ($num > 0)
        {
            $str = substr(self::ALPHABET, ($num % self::BASE), 1) . $str;
            $num = floor($num / self::BASE);
        }
        return $str;
    }
    public static function decode($str)
    {
        $num = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++)
        {
            $num = $num * self::BASE + strpos(self::ALPHABET, $str[$i]);
        }
        $num = $num - self::OFFSET;
        return $num;
    }

    public static function gen_prefix_str()
    {
        $pool = self::ALPHABET;
        $len = self::PREFIX_LEN;
        $str = '';
        for ($i = 0; $i < $len; $i++)
        {
            $str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
        }
        return $str;
    }
}
