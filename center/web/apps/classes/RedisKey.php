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
     * 用来村Service分类标签
     */
    const CLUSTER_SERVICE_PROJECTS = self::PREFIX.':setting:cluster:service:projects';
}