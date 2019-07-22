/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : 127.0.0.1:3306
 Source Schema         : xiaoai

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : 65001

 Date: 13/05/2019 15:40:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for xiaoai_wx_config
-- ----------------------------
DROP TABLE IF EXISTS `xiaoai_wx_config`;
CREATE TABLE `xiaoai_wx_config` (
  `id` int(11) NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of xiaoai_wx_config
-- ----------------------------
BEGIN;
INSERT INTO `xiaoai_wx_config` VALUES (1, 'wx_access_token', NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
