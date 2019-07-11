/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50725
 Source Host           : localhost
 Source Database       : jmembers

 Target Server Type    : MySQL
 Target Server Version : 50725
 File Encoding         : utf-8

 Date: 07/11/2019 23:04:27 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `jm_area`
-- ----------------------------
DROP TABLE IF EXISTS `jm_area`;
CREATE TABLE `jm_area` (
  `area_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `area_name` varchar(50) NOT NULL COMMENT '地区名称',
  `area_parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区父ID',
  `area_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `area_deep` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '地区深度，从1开始',
  PRIMARY KEY (`area_id`),
  KEY `area_parent_id` (`area_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='地区表';

-- ----------------------------
--  Table structure for `jm_logs`
-- ----------------------------
DROP TABLE IF EXISTS `jm_logs`;
CREATE TABLE `jm_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(20) NOT NULL,
  `file` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `querystring` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=' 日志表';

-- ----------------------------
--  Table structure for `jm_menu`
-- ----------------------------
DROP TABLE IF EXISTS `jm_menu`;
CREATE TABLE `jm_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL DEFAULT '',
  `parentid` smallint(6) NOT NULL DEFAULT '0',
  `m` char(20) NOT NULL DEFAULT '',
  `c` char(20) NOT NULL DEFAULT '',
  `a` char(30) NOT NULL DEFAULT '',
  `data` char(100) NOT NULL DEFAULT '',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '99',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `style` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `listorder` (`listorder`),
  KEY `parentid` (`parentid`),
  KEY `module` (`m`,`c`,`a`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Records of `jm_menu`
-- ----------------------------
BEGIN;
INSERT INTO `jm_menu` VALUES ('1', '设置', '0', 'Admin', 'setting', 'index', '', '99', '0', '&#xe61d;'), ('2', '菜单', '1', 'Admin', 'menu', 'index', '', '99', '0', 'Hui-iconfont-menu'), ('3', '添加', '2', 'Admin', 'menu', 'add', '', '99', '0', ''), ('4', '编辑', '2', 'Admin', 'menu', 'edit', '', '99', '0', ''), ('5', '删除', '2', 'Admin', 'menu', 'ajax_delete', '', '99', '1', ''), ('6', '用户管理', '0', 'Admin', 'User', 'p', '', '99', '0', 'Hui-iconfont-user-group'), ('7', '用户列表', '6', 'Admin', 'User', 'index', '', '99', '0', '&#xe611;'), ('8', '添加', '7', 'Admin', 'User', 'add', '', '99', '0', ''), ('9', '编辑', '7', 'Admin', 'User', 'edit', '', '99', '0', ''), ('10', '删除', '7', 'Admin', 'User', 'ajax_delete', '', '99', '1', ''), ('12', '角色列表', '6', 'Admin', 'Role', 'index', '', '99', '0', 'Hui-iconfont-user'), ('13', '添加', '12', 'Admin', 'Role', 'add', '', '99', '0', ''), ('14', '编辑', '12', 'Admin', 'Role', 'edit', '', '99', '0', ''), ('15', '删除', '12', 'Admin', 'Role', 'ajax_delete', '', '99', '1', ''), ('16', '权限设置', '12', 'Admin', 'Role', 'priv', '', '99', '1', '');
COMMIT;

-- ----------------------------
--  Table structure for `jm_role_priv`
-- ----------------------------
DROP TABLE IF EXISTS `jm_role_priv`;
CREATE TABLE `jm_role_priv` (
  `role_id` smallint(5) unsigned NOT NULL,
  `m` char(20) NOT NULL COMMENT '模块',
  `c` char(20) NOT NULL COMMENT '控制器',
  `a` char(20) NOT NULL COMMENT '方法',
  `data` char(30) NOT NULL DEFAULT '' COMMENT '附件属性'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
--  Table structure for `jm_roles`
-- ----------------------------
DROP TABLE IF EXISTS `jm_roles`;
CREATE TABLE `jm_roles` (
  `role_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL COMMENT '角色名称',
  `role_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态,0--有效,1--无效',
  `add_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
--  Records of `jm_roles`
-- ----------------------------
BEGIN;
INSERT INTO `jm_roles` VALUES ('1', '超级管理员', '0', '0');
COMMIT;

-- ----------------------------
--  Table structure for `jm_users`
-- ----------------------------
DROP TABLE IF EXISTS `jm_users`;
CREATE TABLE `jm_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号-即为用户账号',
  `user_passwd` varchar(50) NOT NULL DEFAULT '' COMMENT '密码',
  `role_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0--超级管理员,1--院长,2--组长,3--教师,4--学生/家长',
  `user_nickname` varchar(100) NOT NULL COMMENT '昵称',
  `user_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态,0--正常,1--禁止登陆',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(128) NOT NULL DEFAULT '' COMMENT 'IP地址包括ip6',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
--  Records of `jm_users`
-- ----------------------------
BEGIN;
INSERT INTO `jm_users` VALUES ('1', 'admin', '76f100364d895ceef7b39300060a3f9a', '1', 'admin', '0', '1562857370', '127.0.0.1', '0');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
