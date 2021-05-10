-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- SIAM_ADMIN_DATABASE
-- 生成日期： 2021-05-10 14:09:31
-- 服务器版本： 5.7.26


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `siam_admin`
--

-- --------------------------------------------------------

--
-- 表的结构 `siam_auths`
--

CREATE TABLE `siam_auths` (
  `auth_id` int(11) NOT NULL COMMENT '权限id',
  `auth_name` varchar(40) NOT NULL COMMENT '权限名',
  `auth_rules` varchar(80) DEFAULT NULL COMMENT '路由地址',
  `auth_icon` varchar(255) DEFAULT NULL COMMENT '路由图标',
  `auth_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '权限类型 0菜单1按钮',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `siam_auths`
--

INSERT INTO `siam_auths` (`auth_id`, `auth_name`, `auth_rules`, `auth_icon`, `auth_type`, `create_time`, `update_time`) VALUES
(1, '管理列表', NULL, 'fa fa-home', 0, '2021-04-29 18:08:58', '2021-04-29 18:15:20'),
(2, '权限', 'page/auths/lists.html', '', 0, '2021-04-29 18:09:24', '2021-04-29 18:10:46'),
(3, '角色', 'page/roles/lists.html', '', 0, '2021-04-29 18:09:39', '2021-04-29 18:11:00'),
(4, '用户', 'page/users/lists.html', '', 0, '2021-04-29 18:09:51', '2021-04-29 18:11:14');

-- --------------------------------------------------------

--
-- 表的结构 `siam_configs`
--

CREATE TABLE `siam_configs` (
  `config_id` int(10) UNSIGNED NOT NULL,
  `config_name` varchar(255) NOT NULL,
  `config_value` text NOT NULL,
  `u_id` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置表';

--
-- 转存表中的数据 `siam_configs`
--

INSERT INTO `siam_configs` (`config_id`, `config_name`, `config_value`, `u_id`, `create_time`, `update_time`) VALUES
(1, 'next_user_id', '1001', 0, '2021-04-16 08:55:18', '2021-04-16 08:55:18'),
(2, 'auth_order', '[{\"id\":1,\"child\":[{\"id\":2},{\"id\":3},{\"id\":4}]}]', 0, '2021-04-16 08:55:33', '2021-04-16 08:55:34');

-- --------------------------------------------------------

--
-- 表的结构 `siam_plugs_status`
--

CREATE TABLE `siam_plugs_status` (
  `plugs_name` varchar(255) NOT NULL,
  `plugs_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '插件开启状态 0停用 | 1启用',
  `create_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件状态表';

--
-- 转存表中的数据 `siam_plugs_status`
--

INSERT INTO `siam_plugs_status` (`plugs_name`, `plugs_status`, `create_time`) VALUES
('base', 1, '2021-05-10 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `siam_roles`
--

CREATE TABLE `siam_roles` (
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `role_name` varchar(40) NOT NULL COMMENT '角色名',
  `role_auth` varchar(255) NOT NULL DEFAULT '0' COMMENT '角色权限',
  `role_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '角色状态 0正常1禁用',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '角色级别 越小权限越高',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `siam_roles`
--

INSERT INTO `siam_roles` (`role_id`, `role_name`, `role_auth`, `role_status`, `level`, `create_time`, `update_time`) VALUES
(1, '管理员', '15,16,17,19', 0, 0, 0, 1620397393);

-- --------------------------------------------------------

--
-- 表的结构 `siam_users`
--

CREATE TABLE `siam_users` (
  `u_id` int(11) NOT NULL,
  `u_password` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT 'e10adc3949ba59abbe56e057f20f883e' COMMENT '用户密码',
  `u_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '用户名',
  `u_account` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户登录名',
  `p_u_id` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '上级u_id',
  `role_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `u_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态 -1删除 0禁用 1正常',
  `u_level_line` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0-' COMMENT '用户层级链',
  `last_login_ip` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `u_auth` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `siam_users`
--

INSERT INTO `siam_users` (`u_id`, `u_password`, `u_name`, `u_account`, `p_u_id`, `role_id`, `u_status`, `u_level_line`, `last_login_ip`, `last_login_time`, `create_time`, `update_time`, `u_auth`) VALUES
(1, 'e10adc3949ba59abbe56e057f20f883e', 'siam', '1001', '0', '1', 1, '0-1', 0, 13, NULL, '2021-05-10 00:52:53', '15,16,17,19');

--
-- 转储表的索引
--

--
-- 表的索引 `siam_auths`
--
ALTER TABLE `siam_auths`
  ADD PRIMARY KEY (`auth_id`) USING BTREE;

--
-- 表的索引 `siam_configs`
--
ALTER TABLE `siam_configs`
  ADD PRIMARY KEY (`config_id`);

--
-- 表的索引 `siam_plugs_status`
--
ALTER TABLE `siam_plugs_status`
  ADD PRIMARY KEY (`plugs_name`);

--
-- 表的索引 `siam_roles`
--
ALTER TABLE `siam_roles`
  ADD PRIMARY KEY (`role_id`) USING BTREE;

--
-- 表的索引 `siam_users`
--
ALTER TABLE `siam_users`
  ADD PRIMARY KEY (`u_id`) USING BTREE,
  ADD UNIQUE KEY `u_id` (`u_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `siam_auths`
--
ALTER TABLE `siam_auths`
  MODIFY `auth_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `siam_configs`
--
ALTER TABLE `siam_configs`
  MODIFY `config_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `siam_roles`
--
ALTER TABLE `siam_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `siam_users`
--
ALTER TABLE `siam_users`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
