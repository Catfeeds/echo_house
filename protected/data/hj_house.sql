/*
Navicat MySQL Data Transfer

Source Server         : 61.160.251.70
Source Server Version : 50519
Source Host           : 61.160.251.70:3306
Source Database       : hj_house

Target Server Type    : MYSQL
Target Server Version : 50519
File Encoding         : 65001

Date: 2015-11-23 18:25:38
*/

SET FOREIGN_KEY_CHECKS=0;


CREATE TABLE `tbl_migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
CREATE TABLE `admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(16) NOT NULL COMMENT '用户名',
  `password` varchar(100) NOT NULL COMMENT '用户密码',
  `avatar` varchar(100) NOT NULL DEFAULT '' COMMENT '头像',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态',
  `deleted` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `login_time` int(10) NOT NULL DEFAULT '0' COMMENT '上一次登录时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT=' 工作人员表';

-- ----------------------------
-- Table structure for area
-- ----------------------------
CREATE TABLE `area` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent` int(10) DEFAULT '0' COMMENT '父级ID',
  `name` varchar(16) NOT NULL COMMENT '区域名',
  `pinyin` varchar(25) NOT NULL DEFAULT '' COMMENT '拼音',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `map_lng` decimal(60,6) NOT NULL DEFAULT '0.000000' COMMENT '坐标经度',
  `map_lat` decimal(60,6) NOT NULL DEFAULT '0.000000' COMMENT '坐标纬度',
  `map_zoom` tinyint(3) NOT NULL DEFAULT '12' COMMENT '地图缩放层级',
  `deleted` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧数据id',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='区域表';

-- ----------------------------
-- Table structure for article
-- ----------------------------
CREATE TABLE `article` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cid` int(10) NOT NULL COMMENT '分类id',
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `s_title` varchar(25) NOT NULL DEFAULT '' COMMENT '短标题',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `seo` varchar(100) NOT NULL DEFAULT '' COMMENT '文章SEO',
  `tag` varchar(100) NOT NULL DEFAULT '' COMMENT '标签',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` text COMMENT '文章内容',
  `author_id` int(10) NOT NULL DEFAULT '0' COMMENT '发布者id',
  `author` varchar(16) NOT NULL DEFAULT '' COMMENT '发布者用户名',
  `editor_id` int(10) NOT NULL DEFAULT '0' COMMENT '修改人用户id',
  `editor` varchar(16) NOT NULL DEFAULT '' COMMENT '修改人用户名',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '封面图片',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '外链',
  `source` varchar(15) NOT NULL DEFAULT '' COMMENT '文章来源',
  `hits` int(6) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `show_time` int(10) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `keywords_switch` tinyint(1) NOT NULL DEFAULT '1' COMMENT '关键词替换开关',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `hid` (`status`) USING BTREE,
  KEY `IDX_CID_SHOW_TIME` (`cid`,`show_time`),
  KEY `IDX_SHOW_TIME` (`show_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Table structure for article_cate
-- ----------------------------
CREATE TABLE `article_cate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(10) NOT NULL COMMENT '分类名',
  `pinyin` varchar(255) NOT NULL COMMENT '拼音缩写',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `parent` int(10) NOT NULL DEFAULT '0' COMMENT '父类id',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `seo_data` text COMMENT 'SEO 数据',
  `config` tinyint(3) NOT NULL DEFAULT '0' COMMENT '咨询配置',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- ----------------------------
-- Table structure for article_plot_rel
-- ----------------------------
CREATE TABLE `article_plot_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `aid` int(10) NOT NULL COMMENT '文章id',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ask
-- ----------------------------
CREATE TABLE `ask` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `question` text NOT NULL COMMENT '问题',
  `answer` text COMMENT '回答',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户论坛id',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户论坛帐号',
  `cid` int(10) NOT NULL DEFAULT '0' COMMENT '分类',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `hid` int(10) NOT NULL DEFAULT '0' COMMENT '楼盘id',
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '称呼',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `reply_time` int(10) NOT NULL DEFAULT '0' COMMENT '回复时间',
  `views` int(10) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `created` int(10) NOT NULL COMMENT '提问时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧id',
  PRIMARY KEY (`id`),
  KEY `cid_status_replytime_ind` (`cid`,`status`,`reply_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ask_cate
-- ----------------------------
CREATE TABLE `ask_cate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(20) NOT NULL COMMENT '分类名称',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父类',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for auth_assignment
-- ----------------------------
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for auth_item
-- ----------------------------
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL COMMENT '授权项标识',
  `chinese` varchar(64) NOT NULL COMMENT '授权项中文名称',
  `type` int(11) NOT NULL COMMENT '授权项类别',
  `description` text COMMENT '授权项描述',
  `bizrule` text COMMENT '规则',
  `data` text,
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `user_id` int(10) unsigned NOT NULL COMMENT '操作人id',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for auth_item_child
-- ----------------------------
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`) USING BTREE,
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for baidu_ad
-- ----------------------------
CREATE TABLE `baidu_ad` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `position` varchar(255) NOT NULL COMMENT '广告位置',
  `size` varchar(255) NOT NULL COMMENT '广告尺寸',
  `bd_id` int(10) NOT NULL COMMENT '广告id',
  `substation_id` int(10) NOT NULL DEFAULT '0' COMMENT '分站区域id，默认0主站',
  `swf_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'flash广告地址',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义代码',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `note` varchar(255) DEFAULT '' COMMENT '广告位备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order
-- ----------------------------
CREATE TABLE `order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(11) NOT NULL COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `spm_a` varchar(20) NOT NULL COMMENT 'spm来源',
  `spm_b` varchar(20) NOT NULL COMMENT 'spm类型',
  `spm_c` int(10) NOT NULL DEFAULT '0' COMMENT 'spm额外信息',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `created_ymd` int(8) NOT NULL COMMENT 'ymd格式日期',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧数据id',
  PRIMARY KEY (`id`),
  KEY `phone_ind` (`phone`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot
-- ----------------------------
CREATE TABLE `plot` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `title` varchar(50) NOT NULL COMMENT '楼盘名称',
  `pinyin` varchar(100) NOT NULL COMMENT '拼音',
  `fcode` varchar(1) NOT NULL DEFAULT '' COMMENT '首字母',
  `sale_status` int(10) NOT NULL DEFAULT '0' COMMENT '销售状态',
  `tag_id` int(10) NOT NULL DEFAULT '0' COMMENT '对应论坛标签id',
  `is_new` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否新盘',
  `recommend` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `is_coop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否电商',
  `tuan_id` int(10) NOT NULL DEFAULT '0' COMMENT '特惠团id',
  `kan_id` int(10) NOT NULL DEFAULT '0' COMMENT '看房团id',
  `area` int(10) NOT NULL COMMENT '所在区域',
  `street` int(10) NOT NULL COMMENT '所在商圈',
  `open_time` int(10) NOT NULL DEFAULT '0' COMMENT '开盘时间',
  `delivery_time` int(10) NOT NULL DEFAULT '0' COMMENT '最新交付时间',
  `address` varchar(150) NOT NULL DEFAULT '' COMMENT '楼盘地址',
  `sale_addr` varchar(150) NOT NULL DEFAULT '' COMMENT '售楼地址',
  `sale_tel` varchar(100) NOT NULL DEFAULT '' COMMENT '售楼电话',
  `map_lng` decimal(60,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度',
  `map_lat` decimal(60,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度',
  `map_zoom` tinyint(3) NOT NULL DEFAULT '12' COMMENT '地图放大',
  `image` varchar(150) NOT NULL DEFAULT '' COMMENT '配图',
  `price` int(5) NOT NULL DEFAULT '0' COMMENT '价格',
  `unit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '单位',
  `price_mark` tinyint(1) NOT NULL DEFAULT '1' COMMENT '价格标识',
  `data_conf` longtext NOT NULL COMMENT '项目信息存储',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `views` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
  `star` tinyint(1) NOT NULL DEFAULT '0' COMMENT '合作星级',
  `deleted` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧数据id',
  PRIMARY KEY (`id`),
  KEY `isnew_status_ind` (`is_new`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_delivery
-- ----------------------------
CREATE TABLE `plot_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `hid` int(11) NOT NULL COMMENT '关联楼盘id',
  `delivery_time` int(10) NOT NULL COMMENT '交付时间',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '交房详情',
  `created` int(10) NOT NULL COMMENT '创建时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_discount
-- ----------------------------
CREATE TABLE `plot_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `hid` int(11) NOT NULL COMMENT '关联楼盘id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `start` int(10) NOT NULL COMMENT '开始时间',
  `expire` int(10) NOT NULL COMMENT '结束时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `hid_ind` (`hid`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='楼盘优惠信息表';

-- ----------------------------
-- Table structure for plot_img
-- ----------------------------
CREATE TABLE `plot_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `hid` int(11) NOT NULL COMMENT '楼盘id',
  `type` tinyint(2) unsigned NOT NULL COMMENT '图片类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '图片名称',
  `url` varchar(150) NOT NULL DEFAULT '' COMMENT '图片地址',
  `is_cover` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否封面图',
  `room` tinyint(2) NOT NULL DEFAULT '0' COMMENT '几室',
  `size` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '面积',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `hid_type_iscover_ind` (`hid`,`type`,`is_cover`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_kan
-- ----------------------------
CREATE TABLE `plot_kan` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '看房团ID',
  `hids` varchar(64) NOT NULL COMMENT '楼ID',
  `hid_num` tinyint(2) NOT NULL DEFAULT '0' COMMENT '看房团楼盘数量',
  `title` varchar(32) NOT NULL COMMENT '标题',
  `gather_time` int(10) NOT NULL COMMENT '集合时间',
  `location` varchar(32) NOT NULL DEFAULT '' COMMENT '集合地点',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '看房团回顾链接',
  `stat` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '默认统计',
  `expire` int(10) NOT NULL COMMENT '截至时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧数据id',
  PRIMARY KEY (`id`),
  KEY `status_isrecommend_ind` (`status`,`is_recommend`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='看房团';

-- ----------------------------
-- Table structure for plot_price
-- ----------------------------
CREATE TABLE `plot_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `hid` int(11) NOT NULL COMMENT '关联楼盘id',
  `jglb` int(10) NOT NULL COMMENT '价格类别',
  `price` mediumint(8) NOT NULL COMMENT '均价',
  `mark` tinyint(1) NOT NULL DEFAULT '1' COMMENT '价格标识',
  `unit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '单位',
  `description` text COMMENT '描述',
  `year` smallint(4) NOT NULL COMMENT '年份',
  `month` tinyint(3) NOT NULL,
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `hid_type_ind` (`hid`,`jglb`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_pricetag
-- ----------------------------
CREATE TABLE `plot_pricetag` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(10) NOT NULL,
  `min` int(10) NOT NULL DEFAULT '0' COMMENT '最小价',
  `max` int(10) NOT NULL DEFAULT '0' COMMENT '最高价',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0正常，1以下，2以上',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created` int(10) NOT NULL DEFAULT '0',
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_pricetrend
-- ----------------------------
CREATE TABLE `plot_pricetrend` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `data` tinytext NOT NULL COMMENT '数据',
  `time` int(10) NOT NULL COMMENT '房价时间',
  `year` smallint(4) NOT NULL COMMENT '年份',
  `month` tinyint(3) NOT NULL COMMENT '月份',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_special
-- ----------------------------
CREATE TABLE `plot_special` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `price_old` float(10,2) NOT NULL COMMENT '原价',
  `price_new` float(10,2) NOT NULL COMMENT '新价',
  `end_time` int(10) NOT NULL COMMENT '截止时间',
  `image` varchar(100) DEFAULT '' COMMENT '封面图',
  `housetype_img` text COMMENT '户型图',
  `room` varchar(32) NOT NULL DEFAULT '' COMMENT '房号',
  `bed_room` varchar(50) NOT NULL DEFAULT '' COMMENT '居室',
  `size` varchar(10) NOT NULL DEFAULT '' COMMENT '面积',
  `recommend` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plot_tuan
-- ----------------------------
CREATE TABLE `plot_tuan` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '关联楼盘',
  `title` varchar(25) NOT NULL COMMENT '标题',
  `s_title` varchar(25) NOT NULL DEFAULT '' COMMENT '副标题',
  `end_time` int(10) NOT NULL COMMENT '截止时间',
  `pc_img` varchar(100) NOT NULL COMMENT 'PC封面图',
  `wap_img` varchar(100) NOT NULL COMMENT 'wap封面图',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `stat` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '默认统计人数',
  `hongbao` varchar(25) NOT NULL DEFAULT '' COMMENT '红包特惠价格',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `recommend` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `hid_status_ind` (`hid`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for recom
-- ----------------------------
CREATE TABLE `recom` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cid` int(10) NOT NULL COMMENT '分类id',
  `hid` int(10) NOT NULL DEFAULT '0' COMMENT '关联楼盘ID',
  `title` varchar(50) NOT NULL COMMENT '主标题',
  `s_title` varchar(25) NOT NULL DEFAULT '' COMMENT '短标题',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `author_id` int(10) NOT NULL COMMENT '推荐人id',
  `author` varchar(16) NOT NULL COMMENT '推荐人用户名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `substation_id` int(10) NOT NULL DEFAULT '0' COMMENT '分站区域id，主站默认为0',
  `config` text NOT NULL COMMENT '推荐配置',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cms推荐表';

-- ----------------------------
-- Table structure for recom_cate
-- ----------------------------
CREATE TABLE `recom_cate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL COMMENT '推荐位名',
  `pinyin` varchar(20) NOT NULL COMMENT '推荐位拼音缩写',
  `parent` int(10) NOT NULL DEFAULT '0' COMMENT '父类',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `pinyin` (`pinyin`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='推荐分类表';

-- ----------------------------
-- Table structure for school
-- ----------------------------
CREATE TABLE `school` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(25) NOT NULL COMMENT '学校名称',
  `pinyin` varchar(100) NOT NULL DEFAULT '' COMMENT '学校拼音',
  `area` int(10) NOT NULL COMMENT '地区',
  `street` int(10) NOT NULL DEFAULT '0' COMMENT '街道id',
  `type` tinyint(1) NOT NULL COMMENT '学校类型',
  `phone` varchar(100) NOT NULL DEFAULT '' COMMENT '学校电话',
  `scope` text COMMENT '学区范围',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '学校地址',
  `image` varchar(100) DEFAULT '' COMMENT '封面图',
  `description` text COMMENT '学校简介',
  `pic` text COMMENT '图片集',
  `map_lng` decimal(60,6) NOT NULL DEFAULT '0.000000' COMMENT '经度',
  `map_lat` decimal(60,6) NOT NULL DEFAULT '0.000000' COMMENT '纬度',
  `map_zoom` tinyint(2) NOT NULL DEFAULT '12' COMMENT '地图缩放层级',
  `important` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否重点学校',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `deleted` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `recommend` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `house_num` tinyint(3) NOT NULL DEFAULT '0' COMMENT '关联楼盘数',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `old_id` int(10) NOT NULL DEFAULT '0' COMMENT '旧id',
  PRIMARY KEY (`id`),
  KEY `status_area_type_recommend_ind` (`area`,`type`,`status`,`recommend`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for school_area
-- ----------------------------
CREATE TABLE `school_area` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `area` int(10) NOT NULL COMMENT '区域id',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '政策摘要',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '政策链接',
  `xx_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '小学学区示意图',
  `zx_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '中学学区示意图',
  `sort` tinyint(1) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for school_plot_rel
-- ----------------------------
CREATE TABLE `school_plot_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `sid` int(10) NOT NULL COMMENT '学校id',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `hname` varchar(50) NOT NULL COMMENT '楼盘名称',
  `sname` varchar(50) NOT NULL COMMENT '学校名称',
  `recommend` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `area` int(10) NOT NULL COMMENT '区域id',
  `distance` int(5) NOT NULL DEFAULT '0' COMMENT '楼盘与学校距离',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `sid_area_ind` (`sid`,`area`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for site_config
-- ----------------------------
CREATE TABLE `site_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL COMMENT '配置项名称',
  `config` text NOT NULL COMMENT 'json数据',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='站点配置表';

-- ----------------------------
-- Table structure for staff
-- ----------------------------
CREATE TABLE `staff` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `username` varchar(20) NOT NULL COMMENT '登陆帐号',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `recommend` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT 'QQ号码',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `wx_image` varchar(255) NOT NULL DEFAULT '' COMMENT '微信二维码',
  `wx_name` varchar(255) NOT NULL DEFAULT '' COMMENT '微信名',
  `avatar` varchar(150) NOT NULL DEFAULT '' COMMENT '买房顾问头像',
  `work_time` int(2) NOT NULL DEFAULT '0' COMMENT '工作年限',
  `idea` varchar(255) NOT NULL DEFAULT '' COMMENT '服务理念',
  `introduction` varchar(255) NOT NULL DEFAULT '' COMMENT '自我介绍',
  `deleted` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `login_time` int(10) NOT NULL DEFAULT '0' COMMENT '上次登陆时间',
  `login_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次登录ip',
  `job` varchar(20) NOT NULL DEFAULT '' COMMENT '职位',
  `praise` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for staff_check
-- ----------------------------
CREATE TABLE `staff_check` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `phone` varchar(11) NOT NULL COMMENT '用户手机',
  `sid` int(10) NOT NULL COMMENT '管家id',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '截止时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `phone_sid_ind` (`phone`,`sid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管家登记楼盘check in';

-- ----------------------------
-- Table structure for tag
-- ----------------------------
CREATE TABLE `tag` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) NOT NULL COMMENT '标签名称',
  `cate` varchar(20) NOT NULL COMMENT '分类标识',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `parent` (`status`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tag_rel
-- ----------------------------
CREATE TABLE `tag_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `tag_id` int(10) NOT NULL COMMENT '标签id',
  `cate` varchar(20) NOT NULL COMMENT '标签类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user
-- ----------------------------
CREATE TABLE `user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(11) NOT NULL COMMENT '电话',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT 'QQ',
  `address` varchar(64) NOT NULL DEFAULT '' COMMENT '住址',
  `staff_id` int(10) NOT NULL DEFAULT '0' COMMENT '管家id',
  `staff_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '管家状态',
  `visit_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '回访状态',
  `assign_time` int(10) NOT NULL DEFAULT '0' COMMENT '分配时间',
  `progress` tinyint(1) NOT NULL DEFAULT '1' COMMENT '购房进度',
  `buy_time` int(10) NOT NULL DEFAULT '0' COMMENT '购房时间',
  `buy_hid` int(10) NOT NULL DEFAULT '0' COMMENT '购买楼盘',
  `intent_time` int(10) NOT NULL DEFAULT '0' COMMENT '意向购买时间',
  `size` varchar(15) NOT NULL DEFAULT '0' COMMENT '房型面积',
  `budget` varchar(15) NOT NULL DEFAULT '0' COMMENT '购房预算',
  `take_notice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '看房邀请',
  `room_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '房源类型',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `mark_new` int(10) NOT NULL DEFAULT '0' COMMENT '复盘标记',
  `new_order` int(10) NOT NULL DEFAULT '0' COMMENT '最新订单时间',
  `new_log` int(10) NOT NULL DEFAULT '0' COMMENT '最新一条流水时间',
  `concern` int(10) NOT NULL DEFAULT '0' COMMENT '用户关注点',
  `concern_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '其他关注点',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `phone_staffid_ind` (`phone`,`staff_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_area_rel
-- ----------------------------
CREATE TABLE `user_area_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `phone` varchar(11) NOT NULL,
  `area` int(10) NOT NULL COMMENT '区域id',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `area_phone_ind` (`area`,`phone`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户意向区域关系表';

-- ----------------------------
-- Table structure for user_log
-- ----------------------------
CREATE TABLE `user_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `phone` varchar(11) NOT NULL COMMENT '手机号',
  `staff_id` int(10) NOT NULL DEFAULT '0' COMMENT '管家id',
  `staff_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '管家状态',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '工作人员id',
  `visit_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '回访状态',
  `content` varchar(255) NOT NULL COMMENT '流水内容',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `phone_ind` (`phone`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_plot_rel
-- ----------------------------
CREATE TABLE `user_plot_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `phone` varchar(11) NOT NULL COMMENT '用户电话',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `hid_phone_ind` (`hid`,`phone`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户意向楼盘表，订单中报名相关楼盘记录在此，后台小编添加的意向楼盘也记录在此';

-- ----------------------------
-- Table structure for vane
-- ----------------------------
CREATE TABLE `vane` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `type` tinyint(1) NOT NULL COMMENT '类型',
  `data` text NOT NULL COMMENT '统计数据',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `plot_kan_img` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `kan_id` int(10) NOT NULL COMMENT '看房团id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '图片名称',
  `img` varchar(150) NOT NULL DEFAULT '' COMMENT '图片地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `substation` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `area_id` int(10) NOT NULL COMMENT '关联分站的区域id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分站名称',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `area_id` (`area_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `active_record_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `description` varchar(255) NOT NULL COMMENT '操作描述',
  `action` varchar(20) NOT NULL COMMENT '操作动作',
  `model` varchar(255) NOT NULL COMMENT '操作的模型名',
  `mid` int(10) NOT NULL COMMENT '操作数据的id',
  `field` varchar(255) NOT NULL DEFAULT '' COMMENT '操作的字段名',
  `username` varchar(255) NOT NULL COMMENT '操作者用户名',
  `uid` int(10) NOT NULL COMMENT '操作者id',
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'IP',
  `long_ip` int(10) NOT NULL DEFAULT '0' COMMENT '转换成数字的IP',
  `info` text COMMENT '额外信息',
  `created` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `switch_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(150) NOT NULL COMMENT '开关名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '开关描述',
  `value` tinyint(3) NOT NULL DEFAULT '0' COMMENT '值',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='开关配置表';

CREATE TABLE `staff_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `sid` int(10) NOT NULL COMMENT '被点评的顾问id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '点评用户论坛账号id',
  `content` varchar(255) NOT NULL COMMENT '点评内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `created` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plot_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL COMMENT '买房顾问id',
  `hid` int(10) NOT NULL COMMENT '楼盘id',
  `content` text COMMENT '点评内容',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plot_evaluate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `sid` int(10) NOT NULL COMMENT '买房顾问id',
  `hid` int(10) NOT NULL COMMENT '点评的楼盘id',
  `content` text COMMENT '评测内容',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `baike_cate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(15) NOT NULL COMMENT '分类名称',
  `pinyin` varchar(255) NOT NULL DEFAULT '' COMMENT '拼音',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `parent` int(10) NOT NULL DEFAULT '0' COMMENT '父级分类id',
  `belong` tinyint(1) NOT NULL DEFAULT '0' COMMENT '归属',
  `sort` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `baike` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `cid` int(10) NOT NULL COMMENT '分类id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `sub_title` varchar(255) NOT NULL DEFAULT '' COMMENT '副标题',
  `tag` varchar(255) NOT NULL DEFAULT '' COMMENT '标签',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` text COMMENT '内容',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `sort` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo关键词',
  `origin` varchar(255) NOT NULL DEFAULT '' COMMENT '来源',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '外链',
  `scan` int(10) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `author_id` int(10) NOT NULL COMMENT '发布者id',
  `author` varchar(50) NOT NULL DEFAULT '' COMMENT '发布者姓名',
  `oppose` int(8) NOT NULL DEFAULT '0' COMMENT '差评数',
  `praise` int(8) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `yid` int(10) NOT NULL DEFAULT '0' COMMENT '云知识库id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plot_period` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '关联的楼盘id',
  `period` tinyint(1) NOT NULL DEFAULT '1' COMMENT '期数',
  `image` varchar(255) NOT NULL COMMENT '沙盘图',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='楼盘期数表';

CREATE TABLE `plot_building` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '关联楼盘id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '楼栋名称（楼栋号）',
  `unit_total` tinyint(2) NOT NULL DEFAULT '0' COMMENT '单元数',
  `household_total` smallint(3) NOT NULL DEFAULT '0' COMMENT '规划户数',
  `floor_total` tinyint(3) NOT NULL DEFAULT '0' COMMENT '楼层数',
  `sale_total` smallint(3) NOT NULL DEFAULT '0' COMMENT '在售户数',
  `lift_house_match` varchar(5) NOT NULL DEFAULT '' COMMENT '梯户配比',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '关联的期数id',
  `point_x` decimal(60,10) NOT NULL DEFAULT '0.0000000000' COMMENT '标注点x轴值',
  `point_y` decimal(60,10) NOT NULL DEFAULT '0.0000000000' COMMENT '标注点y轴值',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='楼栋表';


CREATE TABLE `plot_red` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL DEFAULT '0' COMMENT '楼盘id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `sub_title` varchar(255) DEFAULT NULL COMMENT '副标题',
  `amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '红包额度',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始领取时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束领取时间',
  `got_num` int(10) NOT NULL DEFAULT '0' COMMENT '红包已领人数',
  `total_num` int(10) NOT NULL DEFAULT '0' COMMENT '默认领取人数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(10) NOT NULL COMMENT '创建时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plot_house_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '关联楼盘id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '户型标题',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '户型图',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '户型描述',
  `bedroom` tinyint(2) NOT NULL DEFAULT '0' COMMENT '几室',
  `livingroom` tinyint(2) NOT NULL DEFAULT '0' COMMENT '几厅',
  `bathroom` tinyint(2) NOT NULL DEFAULT '0' COMMENT '几卫',
  `cookroom` tinyint(2) NOT NULL DEFAULT '0' COMMENT '几厨',
  `inside_size` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '套内面积',
  `size` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '面积',
  `price` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '参考价',
  `sale_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '销售状态',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `is_cover` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否封面',
  `sort` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='户型表';

CREATE TABLE `plot_house_type_building` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `hid` int(10) NOT NULL COMMENT '关联的楼盘id',
  `htid` int(10) NOT NULL COMMENT '关联的户型id',
  `bid` int(10) NOT NULL COMMENT '关联的楼栋id',
  `created` int(10) NOT NULL COMMENT '添加时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='户型楼栋关联表';

CREATE TABLE `baike_tag` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(255) NOT NULL COMMENT '标签名称',
  `sort` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `recom` int(10) NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `created` int(10) NOT NULL COMMENT '创建时间',
  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
