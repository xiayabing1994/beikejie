
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `falisten`
--

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_adress`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_adress` (
  `address_id` int(11) unsigned NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0',
  `region_id` int(11) unsigned NOT NULL DEFAULT '0',
  `detail` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `isdefault` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否默认:0=非默认,1=默认地址',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_category`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_category` (
  `id` int(10) NOT NULL COMMENT 'ID',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `image` varchar(180) NOT NULL COMMENT '图片',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='商品分类';

--
-- 转存表中的数据 `fa_litestore_category`
--

INSERT INTO `__PREFIX__litestore_category` (`id`, `pid`, `name`, `image`, `weigh`, `createtime`, `updatetime`) VALUES
(4, 0, '电子产品', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/509af801726984aaa359b4bf249f5716.png', 4, 1540367287, 1541402940),
(5, 4, '手机', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/4fffea1c27bfb8df655a39114bb05814.jpg', 5, 1540367298, 1541402932),
(6, 0, '水果', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/c83a0019dfa7a768037e98f02b70efd5.jpg', 6, 1540367311, 1541403647),
(7, 6, '进口水果', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/a460ffdbd534b10cdf40487189ccb6b7.jpg', 7, 1540367326, 1541403531),
(8, 4, '笔记本', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/efb53c4c7814c83aa3c21e0fd408b5df.jpg', 8, 1541402921, 1541403316),
(9, 6, '国产水果', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/753cd25e97135e874dca8ab5126ad144.jpg', 9, 1541403546, 1541403622);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_freight`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_freight` (
  `id` int(11) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '运费模版名称',
  `method` enum('10','20') NOT NULL DEFAULT '10' COMMENT '计费方式:10=按件数,20=按重量',
  `weigh` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'createtime',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_freight`
--

INSERT INTO `__PREFIX__litestore_freight` (`id`, `name`, `method`, `weigh`, `createtime`, `updatetime`) VALUES
(22, '手机', '10', 22, 1540288755, 1540288755),
(23, '电脑', '10', 23, 1540363605, 1540363605),
(24, '水果', '20', 24, 1540363644, 1540363644);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_freight_rule`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_freight_rule` (
  `rule_id` int(11) unsigned NOT NULL,
  `litestore_freight_id` int(11) unsigned NOT NULL DEFAULT '0',
  `region` text NOT NULL,
  `first` double unsigned NOT NULL DEFAULT '0',
  `first_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `additional` double unsigned NOT NULL DEFAULT '0',
  `additional_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `create_time` int(11) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_freight_rule`
--

INSERT INTO `__PREFIX__litestore_freight_rule` (`rule_id`, `litestore_freight_id`, `region`, `first`, `first_fee`, `additional`, `additional_fee`, `weigh`, `create_time`) VALUES
(18, 23, '2,20,38,61,76,84,104,124,150,168,180,197,208,221,232,244,250,264,271,278,290,304,319,337,352,362,372,376,389,398,407,422,430,442,449,462,467,481,492,500,508,515,522,530,537,545,553,558,566,574,581,586,597,607,614,619,627,634,640,646,656,675,692,702,711,720,730,748,759,764,775,782,793,802,821,833,842,853,861,871,880,887,896,906,913,920,927,934,948,960,972,980,986,993,1003,1010,1015,1025,1035,1047,1057,1066,1074,1081,1088,1093,1098,1110,1118,1127,1136,1142,1150,1155,1160,1169,1183,1190,1196,1209,1222,1234,1245,1253,1264,1274,1279,1285,1299,1302,1306,1325,1339,1350,1362,1376,1387,1399,1408,1415,1421,1434,1447,1459,1466,1471,1476,1479,1492,1504,1513,1522,1533,1546,1556,1572,1583,1593,1599,1612,1623,1630,1637,1643,1650,1664,1674,1685,1696,1707,1710,1724,1731,1740,1754,1764,1768,1774,1782,1791,1802,1809,1813,1822,1828,1838,1848,1854,1867,1880,1890,1900,1905,1912,1924,1936,1949,1955,1965,1977,1988,1999,2003,2011,2017,2025,2035,2041,2050,2056,2065,2070,2077,2082,2091,2123,2146,2150,2156,2163,2177,2189,2207,2215,2220,2225,2230,2236,2245,2258,2264,2276,2283,2292,2297,2302,2306,2324,2363,2368,2388,2395,2401,2409,2416,2426,2434,2440,2446,2458,2468,2475,2486,2493,2501,2510,2516,2521,2535,2554,2573,2584,2589,2604,2611,2620,2631,2640,2657,2671,2686,2696,2706,2712,2724,2730,2741,2750,2761,2775,2784,2788,2801,2807,2812,2817,2826,2845,2857,2870,2882,2890,2899,2913,2918,2931,2946,2958,2972,2984,2997,3008,3016,3023,3032,3036,3039,3045,3053,3058,3065,3073,3081,3090,3098,3108,3117,3127,3135,3142,3147,3152,3158,3165,3172,3179,3186,3190,3196,3202,3207,3216,3221,3225,3229,3237,3242,3252,3262,3267,3280,3289,3301,3309,3317,3326,3339,3378,3386,3416,3454,3458,3461,3491,3504,3518,3532,3551,3578,3592,3613,3632,3666,3683,3697,3704,3711,3717,3722,3728,3739,3745,3747', 1, '20.00', 1, '10.00', 0, 1540363605),
(49, 24, '2,20,38,61,76,84,104,124,150,168,180,197,208,221,232,244,250,264,271,278,290,304,319,337,352,362,372,376,389,398,407,422,430,442,449,462,467,481,492,500,508,515,522,530,537,545,553,558,566,574,581,586,597,607,614,619,627,634,640,646,656,675,692,702,711,720,730,748,759,764,775,782,793,802,821,833,842,853,861,871,880,887,896,906,913,920,927,934,948,960,972,980,986,993,1003,1010,1015,1025,1035,1047,1057,1066,1074,1081,1088,1093,1098,1110,1118,1127,1136,1142,1150,1155,1160,1169,1183,1190,1196,1209,1222,1234,1245,1253,1264,1274,1279,1285,1299,1302,1306,1325,1339,1350,1362,1376,1387,1399,1408,1415,1421,1434,1447,1459,1466,1471,1476,1479,1492,1504,1513,1522,1533,1546,1556,1572,1583,1593,1599,1612,1623,1630,1637,1643,1650,1664,1674,1685,1696,1707,1710,1724,1731,1740,1754,1764,1768,1774,1782,1791,1802,1809,1813,1822,1828,1838,1848,1854,1867,1880,1890,1900,1905,1912,1924,1936,1949,1955,1965,1977,1988,1999,2003,2011,2017,2025,2035,2041,2050,2056,2065,2070,2077,2082,2091,2123,2146,2150,2156,2163,2177,2189,2207,2215,2220,2225,2230,2236,2245,2258,2264,2276,2283,2292,2297,2302,2306,2324,2363,2368,2388,2395,2401,2409,2416,2426,2434,2440,2446,2458,2468,2475,2486,2493,2501,2510,2516,2521,2535,2554,2573,2584,2589,2604,2611,2620,2631,2640,2657,2671,2686,2696,2706,2712,2724,2730,2741,2750,2761,2775,2784,2788,2801,2807,2812,2817,2826,2845,2857,2870,2882,2890,2899,2913,2918,2931,2946,2958,2972,2984,2997,3008,3016,3023,3032,3036,3039,3045,3053,3058,3065,3073,3081,3090,3098,3108,3117,3127,3135,3142,3147,3152,3158,3165,3172,3179,3186,3190,3196,3202,3207,3216,3221,3225,3229,3237,3242,3252,3262,3267,3280,3289,3301,3309,3317,3326,3339,3378,3386,3416,3454,3458,3461,3491,3504,3518,3532,3551,3578,3592,3613,3632,3666,3683,3697,3704,3711,3717,3722,3728,3739,3745,3747', 1, '10.00', 1, '8.00', 0, 1543387208),
(50, 22, '2,20,38,61,76,84,104,124,150,168,180,197,208,221,232,244,250,264,271,278,290,304,319,337,352,362,372,376,389,398,407,422,430,442,449,462,467,481,492,500,508,515,522,530,537,545,553,558,566,574,581,586,597,607,614,619,627,634,640,646,656,675,692,702,711,720,730,748,759,764,775,782,793,802,821,833,842,853,861,871,880,887,896,906,913,920,927,934,948,960,972,980,986,993,1003,1010,1015,1025,1035,1047,1057,1066,1074,1081,1088,1093,1098,1110,1118,1127,1136,1142,1150,1155,1160,1169,1183,1190,1196,1209,1222,1234,1245,1253,1264,1274,1279,1285,1299,1302,1306,1325,1339,1350,1362,1376,1387,1399,1408,1415,1421,1434,1447,1459,1466,1471,1476,1479,1492,1504,1513,1522,1533,1546,1556,1572,1583,1593,1599,1612,1623,1630,1637,1643,1650,1664,1674,1685,1696,1707,1710,1724,1731,1740,1754,1764,1768,1774,1782,1791,1802,1809,1813,1822,1828,1838,1848,1854,1867,1880,1890,1900,1905,1912,1924,1936,1949,1955,1965,1977,1988,1999,2003,2011,2017,2025,2035,2041,2050,2056,2065,2070,2077,2082,2091,2123,2146,2150,2156,2163,2177,2189,2207,2215,2220,2225,2230,2236,2245,2258,2264,2276,2283,2292,2297,2302,2306,2324,2363,2368,2388,2395,2401,2409,2416,2426,2434,2440,2446,2458,2468,2475,2486,2493,2501,2510,2516,2521,2535,2554,2573,2584,2589,2604,2611,2620,2631,2640,2657,2671,2686,2696,2706,2712,2724,2730,2741,2750,2761,2775,2784,2788,2801,2807,2812,2817,2826,2845,2857,2870,2882,2890,2899,2913,2918,2931,2946,2958,2972,2984,2997,3008,3016,3023,3032,3036,3039,3045,3053,3058,3065,3073,3081,3090,3098,3108,3117,3127,3135,3142,3147,3152,3158,3165,3172,3179,3186,3190,3196,3202,3207,3216,3221,3225,3229,3237,3242,3252,3262,3267,3280,3289,3301,3309,3317,3326,3339,3378,3386,3416,3454,3458,3461,3491,3504,3518,3532,3551,3578,3592,3613,3632,3666,3683,3697,3704,3711,3717,3722,3728,3739,3745,3747', 1, '10.00', 1, '5.00', 0, 1543387223);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_goods`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_goods` (
  `goods_id` int(11) unsigned NOT NULL COMMENT 'ID',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品类别',
  `images` varchar(1800) NOT NULL COMMENT '商品图片',
  `spec_type` enum('10','20') NOT NULL DEFAULT '10' COMMENT '商品规格:10=单规格,20=多规格',
  `deduct_stock_type` enum('10','20') NOT NULL DEFAULT '20' COMMENT '库存计算方式:10=下单减库存,20=付款减库存',
  `content` text NOT NULL COMMENT '描述详情',
  `sales_initial` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '初始销量',
  `sales_actual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际销量',
  `goods_sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '权重',
  `delivery_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '运费模板ID',
  `goods_status` enum('10','20') NOT NULL DEFAULT '10' COMMENT '商品状态:10=上架,20=下架',
  `is_delete` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除:0=未删除,1=已删除',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_goods`
--

INSERT INTO `__PREFIX__litestore_goods` (`goods_id`, `goods_name`, `category_id`, `images`, `spec_type`, `deduct_stock_type`, `content`, `sales_initial`, `sales_actual`, `goods_sort`, `delivery_id`, `goods_status`, `is_delete`, `createtime`, `updatetime`) VALUES
(21, '小米Mix3', 5, 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/ffc4440df18661948b9c2d4dd4ae419b.jpg,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/83bf8f141969a9e3e607a768407fc7e0.jpg,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/c5d85254fc17b1a1b0e2254470881e59.jpg', '20', '20', '<p><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/f5b49f703245ef61bb3faa574f32076d.jpg" data-filename="filename" style="width: 699px;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/7d0fe135394408d4332386117c928003.jpg" data-filename="filename" style="width: 699px;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/6a87fa6af95e39d7dc227f666d7b8ff6.jpg" data-filename="filename" style="width: 699px;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/32d58a08cf92282c8f28078137c970f2.jpg" data-filename="filename" style="width: 699px;"><br></p>', 20, 4, 21, 22, '10', '0', 1541401778, 1543221954),
(22, 'Mate 20 华为 HUAWEI ', 5, 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/b044b7bcd4930202fcd96d6b50453894.jpg,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/4fffea1c27bfb8df655a39114bb05814.jpg,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/e0d6dc822cf7632c66f7718bdd0dc2bc.jpg', '20', '20', '<p style="text-align: center; line-height: 1.6;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/3f0233e227359137bb55152c0750f8a2.png" data-filename="filename" style="width: 603px;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/509af801726984aaa359b4bf249f5716.png" data-filename="filename" style="width: 603px;"><br></p><p><br></p>', 10, 64, 22, 22, '10', '0', 1541402364, 1543242861),
(23, 'MacBook Pro 13寸', 8, 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/eaccd76080ed9e7ece7642694e734885.png,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/85587c2e045b71fb4c977884a19a36cb.jpg,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/efb53c4c7814c83aa3c21e0fd408b5df.jpg', '20', '20', '<p><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/43b7a84d68a15d9058971526068a853a.jpg" data-filename="filename" style="width: 603px;"><br></p>', 0, 12, 23, 23, '10', '0', 1541403061, 1543319289),
(24, '车厘子智利进口', 7, 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/a460ffdbd534b10cdf40487189ccb6b7.jpg,https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/c83a0019dfa7a768037e98f02b70efd5.jpg', '10', '20', '<p><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181105/611619c7dac06511213278a469a1efea.jpg" data-filename="filename" style="width: 603px;"><br></p>', 0, 12, 24, 24, '10', '0', 1541403509, 1543246427);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_goods_spec`
--
ALTER TABLE `__PREFIX__litestore_goods_spec` ADD `spec_image` VARCHAR(580) NOT NULL DEFAULT '' COMMENT '规格的封面';
ALTER TABLE `__PREFIX__litestore_goods_spec` CHANGE `stock_num` `stock_num` INT(11) NOT NULL DEFAULT '0';
CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_goods_spec` (
  `goods_spec_id` int(11) unsigned NOT NULL,
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0',
  `goods_no` varchar(100) NOT NULL DEFAULT '',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `line_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `stock_num` int(11) NOT NULL DEFAULT '0',
  `goods_sales` int(11) unsigned NOT NULL DEFAULT '0',
  `goods_weight` double unsigned NOT NULL DEFAULT '0',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '',
  `spec_image` varchar(580) NOT NULL DEFAULT '' COMMENT '规格封面',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_goods_spec`
--

INSERT INTO `__PREFIX__litestore_goods_spec` (`goods_spec_id`, `goods_id`, `goods_no`, `goods_price`, `line_price`, `stock_num`, `goods_sales`, `goods_weight`, `spec_sku_id`, `create_time`, `update_time`) VALUES
(66, 23, 'mac_0001', '12688.00', '0.00', 989, 10, 1.2, '48', 1541406888, 1543319289),
(67, 23, 'mac_0002', '12688.00', '0.00', 997, 2, 1.2, '49', 1541406888, 1543021905),
(81, 21, 'SN001', '3299.00', '0.00', 997, 2, 0.5, '40_42', 1542271178, 1543221954),
(82, 21, 'SN002', '3999.00', '0.00', 999, 0, 0.5, '40_43', 1542271178, 1542271178),
(83, 21, 'SN011', '3399.00', '0.00', 999, 0, 0.5, '41_42', 1542271178, 1542271178),
(84, 21, 'SN012', '4099.00', '0.00', 999, 0, 0.5, '41_43', 1542271178, 1542271178),
(94, 24, 'CHE001', '258.00', '299.00', 94, 12, 1, '', 1542707236, 1543283382),
(103, 22, 'SNHW001', '4499.00', '0.00', 941, 58, 500, '44_46', 1542784591, 1543242861),
(104, 22, 'SNHW001', '5899.00', '0.00', 997, 2, 500, '44_47', 1542784591, 1542978749),
(105, 22, 'SNHW001', '4699.00', '0.00', 996, 3, 500, '45_46', 1542784591, 1543242861),
(106, 22, 'SNHW001', '6099.00', '0.00', 999, 0, 500, '45_47', 1542784591, 1542784591);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_goods_spec_rel`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_goods_spec_rel` (
  `id` int(11) unsigned NOT NULL,
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0',
  `spec_id` int(11) unsigned NOT NULL DEFAULT '0',
  `spec_value_id` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_goods_spec_rel`
--

INSERT INTO `__PREFIX__litestore_goods_spec_rel` (`id`, `goods_id`, `spec_id`, `spec_value_id`, `create_time`) VALUES
(64, 23, 20, 48, 1541406888),
(65, 23, 20, 49, 1541406888),
(78, 21, 20, 40, 1542271178),
(79, 21, 20, 41, 1542271178),
(80, 21, 21, 42, 1542271178),
(81, 21, 21, 43, 1542271178),
(98, 22, 20, 44, 1542784591),
(99, 22, 20, 45, 1542784591),
(100, 22, 22, 46, 1542784591),
(101, 22, 22, 47, 1542784591);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_news`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_news` (
  `id` smallint(8) unsigned NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(180) NOT NULL DEFAULT '' COMMENT '图片',
  `content` mediumtext COMMENT '内容',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='首页banner';

--
-- 转存表中的数据 `fa_litestore_news`
--

INSERT INTO `__PREFIX__litestore_news` (`id`, `title`, `image`, `content`, `createtime`, `updatetime`, `status`) VALUES
(1, '双十一！来Pink Dream 脱单吧！', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181114/8543faa4986afc586e953137aaf741c3.png', '<section style="margin: 20px 0px 10px; padding: 0px; max-width: 100%; text-align: center; word-wrap: break-word !important;"><section style="margin: 0px; padding: 0px 10px; max-width: 100%; display: inline-block; min-width: 10%; vertical-align: top; word-wrap: break-word !important;"><section class="" powered-by="xiumi.us" style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 0px; padding: 3px 10px 6px; max-width: 100%; display: inline-block; min-width: 10%; vertical-align: top; border-width: 1px; border-radius: 0px; border-style: solid none; border-color: rgb(79, 197, 222); word-wrap: break-word !important;"><section class="" powered-by="xiumi.us" style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 3px 0px 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 0px; padding: 0px; max-width: 100%; font-size: 14px; color: rgb(238, 162, 193); line-height: 2; letter-spacing: 1px; word-wrap: break-word !important;"><p style="margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><span style="margin: 0px; padding: 0px; max-width: 100%; font-size: 16px; word-wrap: break-word !important;">你还是单身吗？</span></p><p style="margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><span style="margin: 0px; padding: 0px; max-width: 100%; font-size: 16px; word-wrap: break-word !important;">快来抓娃娃邂逅你的另一半吧！</span></p><p style="margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><span style="margin: 0px; padding: 0px; max-width: 100%; font-size: 16px; word-wrap: break-word !important;">或许你的他是百发百中的抓娃娃大神，</span></p><p style="margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><span style="margin: 0px; padding: 0px; max-width: 100%; font-size: 16px; word-wrap: break-word !important;">或许你的她是粉粉少女心的小仙女，</span></p><p style="margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><span style="margin: 0px; padding: 0px; max-width: 100%; font-size: 16px; word-wrap: break-word !important;">来Pink Dream活动脱单吧！</span></p></section></section></section><section class="" powered-by="xiumi.us" style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: -5px 0px -20px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 0px; padding: 0px 5px; max-width: 100%; display: inline-block; width: 30px; height: 30px; vertical-align: top; overflow: hidden; word-wrap: break-word !important;"><section class="" powered-by="xiumi.us" style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"><section style="margin: 0px; padding: 0px; max-width: 100%; vertical-align: middle; display: inline-block; width: 20px; word-wrap: break-word !important;"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 168.9 125.4" style="vertical-align: middle; max-width: 100%;" width="100%"><g><path d="M96.7,62.7V9.3c0-5.2,4.3-9.3,9.3-9.3h53.4c5.2,0,9.5,4.1,9.5,9.3v53.4c0,34.6-28.2,62.7-62.8,62.7   c-5.1,0-9.3-4.1-9.3-9.3c0-5.2,4.3-9.3,9.3-9.3c21.1,0,38.8-14.9,43.1-34.7h-43.1C101,72.1,96.7,67.9,96.7,62.7z" fill="rgb(79,197,222)"></path><path d="M0,62.7V9.3C0,4.1,4.3,0,9.3,0h53.4c5.2,0,9.5,4.1,9.5,9.3v53.4c0,34.6-28.2,62.7-62.8,62.7   c-5.1,0-9.3-4.1-9.3-9.3c0-5.2,4.3-9.3,9.3-9.3c21.1,0,38.8-14.9,43.1-34.7H9.3C4.3,72.1,0,67.9,0,62.7z" fill="rgb(79,197,222)"></path></g></svg></section></section></section></section></section></section></section></section></section></section><p style="text-align: center; margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181114/20181128141517.gif" style="width: 373.172px; height: 311.211px;"></p><p style="text-align: center; margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><br></p><p style="text-align: center; margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181114/50286403e58df6c6cb296036f44f6ea4.png" style="width: 537px;"></p><p style="text-align: center; margin-bottom: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; word-wrap: break-word !important;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181114/fe795e4aa817900e223b6152f14eb57b.png" style="width: 533px;"><br style="margin: 0px; padding: 0px; max-width: 100%; word-wrap: break-word !important;"></p></section>', 1542096807, 1543385827, 'normal'),
(2, '轻断食免费送 | 没当上VOGUE女魔头 她却创立了一个婚纱帝国', 'https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181128/88ec778c0b1743586f42b5e848ad5f42.png', '<p><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181128/4647b1cb325061ae2d68c68028a762d0.jpg" style="width: 669px;" data-filename="filename"></p><p>在纽约流行着一段话：</p><p>未婚女人憧憬拥有一件 Vera Wang</p><p>已婚女人时常怀念自己穿过的那件 VeraWang</p><p>再婚女人庆幸自己可以再要一件 Vera Wang</p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181128/297e2a8798261c7c9e5bc82c27377c57.jpg" style="width: 657px;" data-filename="filename"></p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;"><br></p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;">创造了婚纱帝国的王薇薇 VeraWang，简直就是一位传奇女士。或许大家一直向往她的婚纱，但一定不知道这些华服下的，她那精彩的人生。</p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;"><br></p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;"><img src="https://her-family.oss-cn-qingdao.aliyuncs.com/addons_store_uploads/20181128/241e24822db3cf5edab983d7c3fec03f.jpg" style="width: 657px;" data-filename="filename"></p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;"><br></p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;">今年69岁的王薇薇本身是个富家女。她也常常在采访中表示，能获得现在的财富，家庭对她的帮助和影响都很大。</p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;"><br></p><p style="margin-right: 16px; margin-bottom: 0px; margin-left: 16px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; font-variant-numeric: normal; font-variant-east-asian: normal; line-height: 1.75em; word-wrap: break-word !important; overflow-wrap: break-word !important;">她的家庭非常富有，父亲事业上非常成功，精通工业、制造业，还是新加坡 Oceanic Petroleum 的主要股东,学历也超高，是麻省理工毕业的高材生。母亲吴赤芳是联合国的翻译官，小时候就会带着她去巴黎看时装走秀什么的，从小一直学滑冰，养成系的名媛。。。。。。</p>', 1543386743, 1543387060, 'normal');

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_order`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_order` (
  `id` int(11) unsigned NOT NULL COMMENT '订单ID',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编号',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品金额',
  `pay_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总支付金额',
  `pay_status` enum('10','20') NOT NULL DEFAULT '10' COMMENT '支付状态:10=未支付,20=已支付',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `express_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `express_company` varchar(50) NOT NULL DEFAULT '' COMMENT '快递公司',
  `express_no` varchar(50) NOT NULL DEFAULT '' COMMENT '快递单号',
  `freight_status` enum('10','20') NOT NULL DEFAULT '10' COMMENT '发货状态:10=未发货,20=已发货',
  `freight_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `receipt_status` enum('10','20') NOT NULL DEFAULT '10' COMMENT '收货状态:10=未收货,20=已收货',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `order_status` enum('10','20','30') NOT NULL DEFAULT '10' COMMENT '订单状态:10=进行中,20=取消,30=已完成',
  `transaction_id` varchar(30) NOT NULL DEFAULT '' COMMENT '微信支付ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生成时间',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_order_address`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_order_address` (
  `id` int(11) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0',
  `region_id` int(11) unsigned NOT NULL DEFAULT '0',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_order_goods`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_order_goods` (
  `id` int(11) unsigned NOT NULL COMMENT 'ID',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `images` varchar(1800) NOT NULL COMMENT '商品图片',
  `deduct_stock_type` enum('10','20') NOT NULL DEFAULT '20' COMMENT '库存计算方式:10=下单减库存,20=付款减库存',
  `spec_type` enum('10','20') NOT NULL DEFAULT '10' COMMENT '商品规格:10=单规格,20=多规格',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '' COMMENT '规格sku',
  `goods_spec_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格ID',
  `goods_attr` varchar(500) NOT NULL DEFAULT '' COMMENT '商品规格描述',
  `content` text NOT NULL COMMENT '商品描述',
  `goods_no` varchar(100) NOT NULL DEFAULT '' COMMENT '商品编号',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `line_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `goods_weight` double unsigned NOT NULL DEFAULT '0',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总价',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_spec`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_spec` (
  `id` int(11) unsigned NOT NULL,
  `spec_name` varchar(255) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_spec`
--

INSERT INTO `__PREFIX__litestore_spec` (`id`, `spec_name`, `createtime`) VALUES
(20, '颜色', 1541401442),
(21, '版本', 1541401484),
(22, '内存', 1541402270);

-- --------------------------------------------------------

--
-- 表的结构 `fa_litestore_spec_value`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__litestore_spec_value` (
  `id` int(11) unsigned NOT NULL,
  `spec_value` varchar(255) NOT NULL,
  `spec_id` int(11) NOT NULL,
  `createtime` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fa_litestore_spec_value`
--

INSERT INTO `__PREFIX__litestore_spec_value` (`id`, `spec_value`, `spec_id`, `createtime`) VALUES
(40, '黑色', 20, 1541401442),
(41, '翡翠绿', 20, 1541401451),
(42, '6+128', 21, 1541401484),
(43, '8+128', 21, 1541401489),
(44, '亮黑色', 20, 1541402233),
(45, '极光色', 20, 1541402243),
(46, '6GB+64GB', 22, 1541402271),
(47, '8GB+128GB', 22, 1541402279),
(48, '天空灰', 20, 1541403005),
(49, '银色', 20, 1541403011);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fa_litestore_adress`
--
ALTER TABLE `__PREFIX__litestore_adress`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `fa_litestore_category`
--
ALTER TABLE `__PREFIX__litestore_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_litestore_freight`
--
ALTER TABLE `__PREFIX__litestore_freight`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_litestore_freight_rule`
--
ALTER TABLE `__PREFIX__litestore_freight_rule`
  ADD PRIMARY KEY (`rule_id`);

--
-- Indexes for table `fa_litestore_goods`
--
ALTER TABLE `__PREFIX__litestore_goods`
  ADD PRIMARY KEY (`goods_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `fa_litestore_goods_spec`
--
ALTER TABLE `__PREFIX__litestore_goods_spec`
  ADD PRIMARY KEY (`goods_spec_id`);

--
-- Indexes for table `fa_litestore_goods_spec_rel`
--
ALTER TABLE `__PREFIX__litestore_goods_spec_rel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_litestore_news`
--
ALTER TABLE `__PREFIX__litestore_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_litestore_order`
--
ALTER TABLE `__PREFIX__litestore_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`) USING BTREE;

--
-- Indexes for table `fa_litestore_order_address`
--
ALTER TABLE `__PREFIX__litestore_order_address`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_litestore_order_goods`
--
ALTER TABLE `__PREFIX__litestore_order_goods`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_litestore_spec`
--
ALTER TABLE `__PREFIX__litestore_spec`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_litestore_spec_value`
--
ALTER TABLE `__PREFIX__litestore_spec_value`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fa_litestore_adress`
--
ALTER TABLE `__PREFIX__litestore_adress`
  MODIFY `address_id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fa_litestore_category`
--
ALTER TABLE `__PREFIX__litestore_category`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `fa_litestore_freight`
--
ALTER TABLE `__PREFIX__litestore_freight`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `fa_litestore_freight_rule`
--
ALTER TABLE `__PREFIX__litestore_freight_rule`
  MODIFY `rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `fa_litestore_goods`
--
ALTER TABLE `__PREFIX__litestore_goods`
  MODIFY `goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `fa_litestore_goods_spec`
--
ALTER TABLE `__PREFIX__litestore_goods_spec`
  MODIFY `goods_spec_id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=107;
--
-- AUTO_INCREMENT for table `fa_litestore_goods_spec_rel`
--
ALTER TABLE `__PREFIX__litestore_goods_spec_rel`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=102;
--
-- AUTO_INCREMENT for table `fa_litestore_news`
--
ALTER TABLE `__PREFIX__litestore_news`
  MODIFY `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `fa_litestore_order`
--
ALTER TABLE `__PREFIX__litestore_order`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID';
--
-- AUTO_INCREMENT for table `fa_litestore_order_address`
--
ALTER TABLE `__PREFIX__litestore_order_address`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `fa_litestore_order_goods`
--
ALTER TABLE `__PREFIX__litestore_order_goods`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `fa_litestore_spec`
--
ALTER TABLE `__PREFIX__litestore_spec`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `fa_litestore_spec_value`
--
ALTER TABLE `__PREFIX__litestore_spec_value`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=50;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
