ALTER TABLE `app_release_link` ADD `custom_data` TEXT NOT NULL DEFAULT '' COMMENT '自定义下发的内容' AFTER `md5`;
ALTER TABLE `app_release_link` CHANGE `package_type` `package_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0：下载包，1：补丁包，2：so文件'; 
