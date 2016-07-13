ALTER TABLE `app_channel` ADD `channel_key_lowercase` VARCHAR(50) NOT NULL AFTER `channel_key`; 
ALTER TABLE `app_channel` ADD UNIQUE( `channel_key_lowercase`);
ALTER TABLE `app_release` ADD `status` TINYINT(1) NOT NULL COMMENT '0禁用，1启用，2删除' AFTER `prompt_cancel_button_text`;
ALTER TABLE `app_release_link` ADD `fallback_link` TINYINT(1) UNSIGNED NOT NULL COMMENT '1缺省下载地址，0非缺省下载地址' AFTER `release_link`; 
ALTER TABLE `app_release_link` CHANGE `fallback_link` `fallback_link` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1缺省下载地址，0非缺省下载地址'; 
