SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `title` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `location` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime(0) NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `author`(`author`) USING BTREE,
  INDEX `approved_by`(`approved_by`) USING BTREE,
  INDEX `location`(`location`) USING BTREE,
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`location`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `articles_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for boxes
-- ----------------------------
DROP TABLE IF EXISTS `boxes`;
CREATE TABLE `boxes`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ord` int(2) NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `access` int(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ord`(`ord`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of boxes
-- ----------------------------
INSERT INTO `boxes` VALUES (1, 1, '%usermenu%', 1, 0);

-- ----------------------------
-- Table structure for flags
-- ----------------------------
DROP TABLE IF EXISTS `flags`;
CREATE TABLE `flags`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user`) USING BTREE,
  INDEX `post_id`(`post`) USING BTREE,
  INDEX `post_id_2`(`post`) USING BTREE,
  CONSTRAINT `flags_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `flags_ibfk_2` FOREIGN KEY (`post`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pages
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages`  (
  `id` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` int(2) NOT NULL,
  `ord` int(4) NOT NULL,
  `parent` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `access` int(4) NOT NULL DEFAULT 0,
  `param1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `param2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `ord`(`ord`) USING BTREE,
  INDEX `author`(`author`) USING BTREE,
  INDEX `parent`(`parent`) USING BTREE,
  CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `pages` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pages
-- ----------------------------
INSERT INTO `pages` VALUES ('home', 'Vítejte', '<p>Vítejte na vašem novém webu. Uživatel je admin, heslo je websys123</p><p>Hodně štěstí při budování vašeho webu!</p>', '', 1, 1, NULL, 0, 1, 0, NULL, NULL);

-- ----------------------------
-- Table structure for phistory
-- ----------------------------
DROP TABLE IF EXISTS `phistory`;
CREATE TABLE `phistory`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` int(11) NOT NULL,
  `time` datetime(0) NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `author`(`author`) USING BTREE,
  INDEX `time`(`time`) USING BTREE,
  INDEX `page`(`page`) USING BTREE,
  CONSTRAINT `phistory_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phistory_ibfk_2` FOREIGN KEY (`page`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts`  (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) DEFAULT NULL,
  `anon_author` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `anon_ip` varchar(46) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sublocation` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` datetime(0) NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`post_id`) USING BTREE,
  INDEX `author`(`author`) USING BTREE,
  INDEX `deleted`(`deleted`) USING BTREE,
  INDEX `deleted_by`(`deleted_by`) USING BTREE,
  INDEX `sublocation`(`sublocation`) USING BTREE,
  INDEX `location`(`location`) USING BTREE,
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `posts_ibfk_5` FOREIGN KEY (`location`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `level` int(4) NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `access_addpost` tinyint(1) NOT NULL DEFAULT 0,
  `access_flag` tinyint(1) NOT NULL DEFAULT 0,
  `access_nocaptcha` tinyint(1) NOT NULL DEFAULT 0,
  `access_posts_edit` tinyint(1) NOT NULL DEFAULT 0,
  `access_posts_delete` tinyint(1) NOT NULL DEFAULT 0,
  `access_posts_delete_permanent` tinyint(1) NOT NULL DEFAULT 0,
  `access_posts_showip` tinyint(1) NOT NULL DEFAULT 0,
  `access_thread_create` tinyint(1) NOT NULL DEFAULT 0,
  `access_thread_edit` tinyint(1) NOT NULL DEFAULT 0,
  `access_thread_delete` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_edit` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_edit_all` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_changeeditor` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_delete` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_delete_all` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_sethome` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_edit` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_edit_all` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_edit_autoapprove` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_edit_approved` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_changeeditor` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_delete` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_articles_delete_all` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_boxes` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_upload` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_review` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_review_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_review_flags` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_content_review_articles` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_users` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_users_view` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_users_add` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_users_edit` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_users_pass` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_users_delete` tinyint(1) NOT NULL DEFAULT 0,
  `access_admin_roles` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`role_id`) USING BTREE,
  INDEX `level`(`level`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (0, 'Uživatel', 1, '#006000', 1, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `roles` VALUES (1, 'Administrátor', 1000, '#ee0000', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings`  (
  `setting` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`setting`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES ('anonymousposts', '0');
INSERT INTO `settings` VALUES ('author', 'Pepa Omáčka');
INSERT INTO `settings` VALUES ('authoredittime', '300');
INSERT INTO `settings` VALUES ('bodybackground', '#d1f9c4');
INSERT INTO `settings` VALUES ('bodytxtcolor', 'black');
INSERT INTO `settings` VALUES ('flags', '0');
INSERT INTO `settings` VALUES ('frameheader', '0');
INSERT INTO `settings` VALUES ('headercolorbottom', 'yellow');
INSERT INTO `settings` VALUES ('headercolortop', 'orange');
INSERT INTO `settings` VALUES ('homepage', 'home');
INSERT INTO `settings` VALUES ('hrcolor', 'darkgreen');
INSERT INTO `settings` VALUES ('license', '0');
INSERT INTO `settings` VALUES ('lostpass', '0');
INSERT INTO `settings` VALUES ('navactivecolor', 'yellow');
INSERT INTO `settings` VALUES ('navcolor', '#008bdc');
INSERT INTO `settings` VALUES ('navtextcolor', 'white');
INSERT INTO `settings` VALUES ('paging', '10');
INSERT INTO `settings` VALUES ('regallowed', '0');
INSERT INTO `settings` VALUES ('reggroup', '0');
INSERT INTO `settings` VALUES ('restrictorigin', '0');
INSERT INTO `settings` VALUES ('stricthttps', '0');
INSERT INTO `settings` VALUES ('submenucaretcolor', 'yellow');
INSERT INTO `settings` VALUES ('title', 'WebSys CMS');
INSERT INTO `settings` VALUES ('titlecolor', 'darkgreen');
INSERT INTO `settings` VALUES ('twofactor_gauth', '1');
INSERT INTO `settings` VALUES ('twofactor_yubi', '0');
INSERT INTO `settings` VALUES ('wellborder', 'lightgrey');
INSERT INTO `settings` VALUES ('wellcolor', '#fafcfd');
INSERT INTO `settings` VALUES ('whitelabel', '1');
INSERT INTO `settings` VALUES ('yubi_id', '');
INSERT INTO `settings` VALUES ('yubi_key', '');
INSERT INTO `settings` VALUES ('yubi_url', 'https://api.yubico.com/wsapi/2.0/verify');
-- Added in 1.1 Boreas
INSERT INTO `settings` VALUES ('linkcolor', 'blue');

-- ----------------------------
-- Table structure for topics
-- ----------------------------
DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lastact` datetime(0) NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `location`(`location`) USING BTREE,
  INDEX `deleted_by`(`deleted_by`) USING BTREE,
  INDEX `deleted`(`deleted`) USING BTREE,
  INDEX `lastact`(`lastact`) USING BTREE,
  CONSTRAINT `topics_ibfk_2` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `topics_ibfk_3` FOREIGN KEY (`location`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for userfields
-- ----------------------------
DROP TABLE IF EXISTS `userfields`;
CREATE TABLE `userfields`  (
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `minlength` int(11) NOT NULL,
  `maxlength` int(11) NOT NULL,
  `regattr` tinyint(1) NOT NULL,
  `usereditable` tinyint(1) NOT NULL DEFAULT 1,
  `internalonly` tinyint(1) NOT NULL DEFAULT 0,
  `public` int(11) NOT NULL,
  `ord` int(11) NOT NULL,
  PRIMARY KEY (`name`) USING BTREE,
  INDEX `ord`(`ord`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loginname` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emailvalid` tinyint(1) NOT NULL DEFAULT 0,
  `hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `2fa_gauth` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `2fa_yubi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `role` int(11) NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `lastact` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `role`(`role`) USING BTREE,
  INDEX `loginname`(`loginname`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`role_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 0 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (0, 'admin', 'Admin', 'admin@example.net', 1, 'sha512:20001:295hu7LaN6j6FDfS/zl9nPwRmG/omK8m:XIsJX+ZabRH6rGHDUkGviX1mbNK/Ic/eoyXr0lx9oX5ksj/PFBPfDLlNJ9ra8toMNbqHSxG6V4Z+jpbUbDfeBA==', NULL, NULL, 1, 0, NOW());
UPDATE `users` SET `id` = 0;

SET FOREIGN_KEY_CHECKS = 1;
