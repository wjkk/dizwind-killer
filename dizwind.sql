/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2012-12-25 18:12:44
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `dizwind`
-- ----------------------------
DROP TABLE IF EXISTS `dizwind`;
CREATE TABLE `dizwind` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) DEFAULT NULL,
  `site` varchar(40) DEFAULT NULL,
  `site_id` int(10) unsigned DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(40) DEFAULT NULL,
  `action` varchar(40) DEFAULT NULL,
  `thread_id` bigint(40) DEFAULT NULL,
  `reply_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `content` longtext,
  `status` tinyint(4) DEFAULT '1' COMMENT '0:完成 1:待补充 2:隐藏 4:待处理',
  `mag` varchar(500) DEFAULT NULL,
  `ed2k` varchar(500) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `file_size` varchar(100) DEFAULT NULL,
  `retry_time` int(11) DEFAULT '0',
  `total` bigint(20) unsigned DEFAULT NULL,
  `numtop` int(10) unsigned DEFAULT NULL,
  `serie_title` varchar(200) DEFAULT NULL,
  `forum` varchar(50) DEFAULT NULL,
  `channel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gid` (`gid`) USING BTREE,
  KEY `title` (`title`,`create_time`) USING BTREE,
  KEY `site_id` (`site_id`) USING BTREE,
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=178894 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dizwind
-- ----------------------------
