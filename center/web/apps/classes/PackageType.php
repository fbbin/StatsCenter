<?php

namespace App;

class PackageType
{
    // 下载包
    const INSTALL = 0;
    // 补丁包
    const PATCH = 1;
    // .so文件
    const SHARED_OBJECT = 2;

    private static $package_type_name_list = [
        self::INSTALL => '下载包',
        self::PATCH => '补丁包',
        self::SHARED_OBJECT => '动态链接库',
    ];

    public static function exists($package_type)
    {
        return isset(self::$package_type_name_list[$package_type]);
    }

    public static function getPackageTypeName($package_type)
    {
        return isset(self::$package_type_name_list[$package_type])
            ? self::$package_type_name_list[$package_type]
            : '';
    }
}
