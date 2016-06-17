ALTER TABLE `app_release_link` ADD `package_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0：下载包，1：补丁包' AFTER `channel_id`;

ALTER TABLE `app_release` ADD `version_code` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Android版本code' AFTER `version_number`;

ALTER TABLE `app_release_link` ADD `remarks` TEXT NOT NULL DEFAULT '' COMMENT '备注' AFTER `fallback_link`;

ALTER TABLE app_release_link DROP INDEX app_channel_release;

ALTER TABLE `app_release_link` ADD UNIQUE( `app_id`, `channel_id`, `package_type`, `release_id`);

ALTER TABLE `app_release_link` ADD `md5` CHAR(32) NOT NULL DEFAULT '' AFTER `fallback_link`; 
