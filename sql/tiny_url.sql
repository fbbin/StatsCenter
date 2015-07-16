CREATE TABLE `tiny_url` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL COMMENT '给短网址起个名称',
	`category_id` SMALLINT(6) UNSIGNED NOT NULL,
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1：启用，2：删除',
	`add_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)