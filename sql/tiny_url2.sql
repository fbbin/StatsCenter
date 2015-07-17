ALTER TABLE `tiny_url`
	ADD COLUMN `prefix` CHAR(3) NOT NULL DEFAULT '' COMMENT '示例：短网址“http://chelun.com/url/mer8M9”中的“mer”' AFTER `id`;
