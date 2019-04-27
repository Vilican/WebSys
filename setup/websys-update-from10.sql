SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Records of settings
-- ----------------------------
DELETE FROM `settings` WHERE `setting` = 'slidecolor';
DELETE FROM `settings` WHERE `setting` = 'slidewidth';
INSERT INTO `settings` VALUES ('linkcolor', 'blue');

SET FOREIGN_KEY_CHECKS = 1;