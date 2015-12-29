<?php

define('DB_STATUS_DISABLED', 0);
define('DB_STATUS_ENABLED', 1);
define('DB_STATUS_DELETED', 2);

define('APP_STATUS_ENABLED', 1);
define('APP_STATUS_DISABLED', 2);
define('APP_OS_UNKNOWN', 0);

// 强制更新
define('APP_FORCE_UPGRADE_ENABLED', 1);
define('APP_FORCE_UPGRADE_DISABLED', 0);
// 更新策略
// 所有版本强制更新到这个版本
define('APP_FORCE_UPGRADE_STRATEGY_ALL', 0);
// 上个版本强制更新到这个版本
define('APP_FORCE_UPGRADE_STRATEGY_PREVIOUS', 1);
// 指定版本强制更新到这个版本
define('APP_FORCE_UPGRADE_STRATEGY_SPECIFIC', 2);
// 不强制更新
define('APP_FORCE_UPGRADE_STRATEGY_OPTIONAL', 3);
