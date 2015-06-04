/*
Navicat MySQL Data Transfer

Source Server         : Mark
Source Server Version : 50522
Source Host           : 112.121.158.92:6606
Source Database       : vpay

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-06-04 19:17:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `product_bundle_tab`
-- ----------------------------
DROP TABLE IF EXISTS `product_bundle_tab`;
CREATE TABLE `product_bundle_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sequence` int(10) unsigned NOT NULL,
  `internal_name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `shell_quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`sequence`),
  CONSTRAINT `___product_id_refs_id_33475f2b` FOREIGN KEY (`product_id`) REFERENCES `product_tab` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product_bundle_tab
-- ----------------------------
INSERT INTO `product_bundle_tab` VALUES ('1', '65', '1', 'PPC', 'Garena Prepaid Card', '450', '', '');
INSERT INTO `product_bundle_tab` VALUES ('2', '65', '2', 'TAUNT', 'Taunt', '0', '', '');
INSERT INTO `product_bundle_tab` VALUES ('3', '65', '3', 'SUPER_TAUNT', 'Super Taunt', '0', '', '');
INSERT INTO `product_bundle_tab` VALUES ('4', '66', '1', 'PPC', 'Garena Prepaid Card', '450', '', '');
INSERT INTO `product_bundle_tab` VALUES ('5', '66', '2', 'DJ_RAP_CITY', 'DJ Rap City', '0', '', '');
INSERT INTO `product_bundle_tab` VALUES ('6', '67', '1', 'PPC', 'Garena Prepaid Card', '450', '', '');
INSERT INTO `product_bundle_tab` VALUES ('7', '67', '2', 'QUEEN_OF_FOOLS', 'Queen of Fools', '0', '', '');
INSERT INTO `product_bundle_tab` VALUES ('8', '325', '1', 'PPC', 'บัตรเติมเงิน Garena', '75', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('9', '325', '2', 'WARRING_KINGDOM_NIDALEE', 'ไอเทมโค้ดเกม LoL', '0', 'ชื่อไอเทม: Warring Kingdom Nidalee', 'Call Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('10', '328', '1', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('11', '328', '2', 'HON_ITEM', 'ไอเทมโค้ดเกม  HON ', '0', 'ชื่อไอเทม: เสียงพากย์น้าค่อม', 'เติมไอเทมโค้ดได้ที่: \r\nhttp://honredeem.garena.com\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุไอเทมโค้ด: 30 มิถุนายน 2015\r\nCall Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('12', '423', '0', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('13', '423', '1', 'PB_ITEM', 'ไอเทมโค้ดเกม PB', '0', 'Item name: Zombie Slayer 30 Days', 'เติมไอเทมโค้ดได้ที่:\r\nhttp://pb.garena.in.th/refill/\r\nkeyredeem\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุไอเทมโค้ด:\r\n30/04/2015\r\nCall Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('14', '424', '1', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('15', '424', '2', 'PB_ITEM', 'ไอเทมโค้ดเกม PB', '0', 'Item name: Death Scythe 30 Days', 'เติมไอเทมโค้ดได้ที่:\r\nhttp://pb.garena.in.th/refill/\r\nkeyredeem\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุไอเทมโค้ด:\r\n30/04/2015\r\nCall Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('16', '525', '1', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('17', '525', '2', 'HON_Behemoth', 'ไอเทมโค้ดเกม HON', '0', 'Item name: Basher Behemoth', 'เติมไอเทมโค้ดได้ที่: \r\nhttp://honredeem.garena.com\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุไอเทมโค้ด: 30 กันยายน 2015\r\nCall Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('18', '526', '1', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('19', '526', '2', 'HON_Beardicus', 'ไอเทมโค้ดเกม HON ', '0', 'Item name: Gladius Beardicus', 'เติมไอเทมโค้ดได้ที่: \r\nhttp://honredeem.garena.com\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุไอเทมโค้ด: 30 กันยายน 2015\r\nCall Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('20', '597', '1', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('21', '597', '2', 'FORSAKEN_STRIDER', 'ไอเทมโค้ดเกม  HON ', '0', 'Item name: Forsaken Strider', 'เติมไอเทมโค้ดได้ที่: \r\nhttp://honredeem.garena.com\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุ:30Sep2015\r\nCall Center 02 1189 118');
INSERT INTO `product_bundle_tab` VALUES ('22', '598', '1', 'PPC', 'บัตรเติมเงิน Garena', '150', '', 'เติมเงินได้ที่ : pay.garena.in.th');
INSERT INTO `product_bundle_tab` VALUES ('23', '598', '2', 'SILEENA_SILHOUETTE', 'ไอเทมโค้ดเกม  HON ', '0', 'Item name: Sileena Silhouette', 'เติมไอเทมโค้ดได้ที่: \r\nhttp://honredeem.garena.com\r\n(เปิดให้บริการ 9.00-23.00 น.)\r\nวันหมดอายุ:30Sep2015\r\nCall Center 02 1189 118');
