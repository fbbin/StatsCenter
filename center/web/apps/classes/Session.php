<?php
namespace App;

class Session
{
    private static $flash_data_key = '__APP_FLASH_DATA__';

    static function get($name)
    {
        $value = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
        if (self::existsFlashData($name))
        {
            self::delFlashData($name);
            self::del($name);
        }
        return $value;
    }

    static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    static function flash($name, $value)
    {
        self::setFlashData($name, $value);
        self::set($name, $value);
    }

    static function del($name)
    {
        if (isset($_SESSION[$name]))
        {
            unset($_SESSION[$name]);
        }
    }

    static function exists($name)
    {
        return isset($_SESSION[$name]);
    }

    private static function existsFlashData($name)
    {
        return isset($_SESSION[self::$flash_data_key][$name]);
    }

    private static function setFlashData($name, $value)
    {
        if (!isset($_SESSION[self::$flash_data_key]))
        {
            $_SESSION[self::$flash_data_key] = [];
        }
        $_SESSION[self::$flash_data_key][$name] = $value;
    }

    private static function delFlashData($name)
    {
        unset($_SESSION[self::$flash_data_key][$name]);
    }
}
