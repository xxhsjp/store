/*
Navicat MySQL Data Transfer

Source Server         : 172.23.179.35
Source Server Version : 50135
Source Host           : 172.23.179.35:3306
Source Database       : sml_account

Target Server Type    : MYSQL
Target Server Version : 50135
File Encoding         : 65001

Date: 2016-04-06 11:42:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `account`
-- ----------------------------
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` bigint(32) NOT NULL AUTO_INCREMENT,
  `account` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `level` int(2) DEFAULT '0',
  `status` int(2) DEFAULT '0',
  `type` int(2) DEFAULT '0',
  `register_time` bigint(32) DEFAULT '0',
  `expired_time` bigint(32) DEFAULT '0',
  `freeze_time` bigint(32) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of account
-- ----------------------------

-- ----------------------------
-- Table structure for `account_info`
-- ----------------------------
DROP TABLE IF EXISTS `account_info`;
CREATE TABLE `account_info` (
  `id` bigint(32) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(32) NOT NULL,
  `username` varchar(128) COLLATE utf8_unicode_ci DEFAULT '',
  `email` varchar(128) COLLATE utf8_unicode_ci DEFAULT '',
  `language` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
  `industry` varchar(128) COLLATE utf8_unicode_ci DEFAULT '',
  `country` varchar(128) COLLATE utf8_unicode_ci DEFAULT '',
  `city` varchar(128) COLLATE utf8_unicode_ci DEFAULT '',
  `address` varchar(256) COLLATE utf8_unicode_ci DEFAULT '',
  `telephone` varchar(64) COLLATE utf8_unicode_ci DEFAULT '',
  `zipcode` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
  `discription` varchar(2000) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of account_info
-- ----------------------------

-- ----------------------------
-- Table structure for `account_setting`
-- ----------------------------
DROP TABLE IF EXISTS `account_setting`;
CREATE TABLE `account_setting` (
  `id` bigint(32) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of account_setting
-- ----------------------------
