<?php

function array_rebuild($array, $key, $value = '')
{
    $r = array();

    foreach ($array as $k => $v)
    {
        $r[$v[$key]] = $value ? $v[$value] : $v;
    }

    return $r;
}

function get_post($key)
{
    if (isset($_POST[$key]))
    {
        return $_POST[$key];
    }
    elseif (isset($_GET[$key]))
    {
        return $_GET[$key];
    }
    else
    {
        return false;
    }
}

function is_valid_url($url)
{
    // return (bool) preg_match('@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $url);
    // TODO: 拆分两个方法判断
    return (bool) preg_match('@^(https?|ftp|chelun|autopaiwz|chelunwelfare|chelunkjz|drivingcoach)://[^\s/$.?#].[^\s]*$@iS', $url);
}

function array_get($array, $key, $default = '')
{
    return isset($array[$key]) ? $array[$key] : $default;
}

function filter_value($value, $trim = false, $escape = true)
{
    if ($trim)
    {
        $value = trim($value);
    }
    if ($escape)
    {
        $value = htmlspecialchars($value);
    }
    return $value;
}

function array_filter_value($array, $trim = false, $escape = true, $flag = null)
{
    $new_array = [];
    foreach ($array as $key => $value)
    {
        if (($flag === ARRAY_FILTER_USE_BOTH) || ($flag === ARRAY_FILTER_USE_KEY))
        {
            $key = filter_value($key, $trim, $escape);
        }
        if (($flag === ARRAY_FILTER_USE_BOTH) || (is_null($flag)))
        {
            $value = filter_value($value, $trim, $escape);
        }
        $new_array[$key] = $value;
    }
    return $new_array;
}

function version_string_to_int($version)
{
    $segments = explode('.', $version);
    if (count($segments) !== 3)
    {
        return false;
    }
    $segments = array_map('intval', $segments);
    return $segments[0] * 256 * 256 + $segments[1] * 256 + $segments[2];
}

function version_int_to_string($num)
{
    $list = [];
    while ($num !== 0)
    {
        $list[] = $num % 256;
        $num = intval($num / 256);
    }
    return implode('.', array_reverse($list));
}
