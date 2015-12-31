<?php
namespace App;

/**
 * 在这里声明redis的KEY
 * @package App
 */
class RedisKey
{
    const PREFIX = 'mostat:';
    /**
     * 用来存Service分类标签
     */
    const CLUSTER_SERVICE_PROJECTS = self::PREFIX.':setting:cluster:service:projects';
    const APP_RELEASE_LINK = 'app-release:release-link';
    const APP_HOST_LIST = 'app-host:list';
}
