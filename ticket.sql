-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th8 15, 2022 lúc 02:57 PM
-- Phiên bản máy phục vụ: 5.7.33
-- Phiên bản PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ticket`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `calendars`
--

CREATE TABLE `calendars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `DAY` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `calendars`
--

INSERT INTO `calendars` (`id`, `DAY`, `from`, `to`, `created_at`, `updated_at`) VALUES
(4, 'Tuesday', '08:30:00', '23:59:00', NULL, NULL),
(5, 'Thursday', '08:30:00', '22:30:00', NULL, NULL),
(6, 'Monday', '08:30:00', '17:30:00', NULL, NULL),
(7, 'Wednesday', '06:30:00', '18:00:00', NULL, NULL),
(8, 'Friday', '08:30:00', '17:30:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_like` int(11) UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `sender_id`, `ticket_id`, `content`, `count_like`, `created_at`, `updated_at`) VALUES
(7, 949, 30, 'test', 0, '2022-08-15 09:21:17', '2022-08-15 09:21:17'),
(8, 949, 30, 'test3', 0, '2022-08-15 09:21:22', '2022-08-15 09:21:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comment_user`
--

CREATE TABLE `comment_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comment_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `configs`
--

CREATE TABLE `configs` (
  `cfg_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cfg_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `configs`
--

INSERT INTO `configs` (`cfg_key`, `cfg_value`) VALUES
('timeclose', '4');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `members_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `members_id`, `leader_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(7, 'Nhóm test', '[\"945\",\"949\"]', 949, NULL, '2022-08-15 02:36:36', '2022-08-15 03:29:19'),
(9, 'Nhóm test 1', '[\"7\",\"9\",\"21\"]', 7, '2022-08-15 07:16:59', '2022-08-15 07:05:28', '2022-08-15 07:16:59'),
(10, 'Nhóm test 2', '[\"3\",\"7\",\"15\"]', 15, NULL, '2022-08-15 07:18:38', '2022-08-15 08:14:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `histories`
--

CREATE TABLE `histories` (
  `id` bigint(20) NOT NULL,
  `ticket_id` int(20) NOT NULL,
  `creator_id` int(20) NOT NULL,
  `desc_change` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `histories`
--

INSERT INTO `histories` (`id`, `ticket_id`, `creator_id`, `desc_change`, `created_at`) VALUES
(83, 26, 951, 'Tạo yêu cầu', '2022-08-15 04:36:40'),
(84, 27, 949, 'Tạo yêu cầu', '2022-08-15 04:38:03'),
(85, 28, 949, 'Tạo yêu cầu', '2022-08-15 04:41:00'),
(86, 29, 951, 'Tạo yêu cầu', '2022-08-15 04:44:05'),
(87, 29, 951, 'Đã cập nhật lại yêu cầu', '2022-08-15 04:44:38'),
(88, 29, 951, 'Trạng thái yêu cầu thay đổi từ đang xử lý <i class=\'fas fa-arrow-right\'></i> đóng yêu cầu', '2022-08-15 04:47:27'),
(89, 29, 951, 'Trạng thái yêu cầu thay đổi từ đóng yêu cầu <i class=\'fas fa-arrow-right\'></i> đang xử lý', '2022-08-15 04:51:25'),
(90, 29, 949, 'Trạng thái yêu cầu thay đổi từ đang chờ xử lý<i class=\'fas fa-arrow-right\'></i> đang xử lý', '2022-08-15 04:52:50'),
(91, 29, 949, 'Trạng thái yêu cầu thay đổi từ đang xử lý <i class=\'fas fa-arrow-right\'></i> đã xử lý', '2022-08-15 04:56:44'),
(92, 29, 951, 'Đã cập nhật lại yêu cầu', '2022-08-15 05:00:31'),
(93, 29, 951, 'Trạng thái yêu cầu thay đổi từ đã xử lý <i class=\'fas fa-arrow-right\'></i> đóng yêu cầu', '2022-08-15 05:00:41'),
(94, 29, 951, 'Trạng thái yêu cầu thay đổi từ đóng yêu cầu <i class=\'fas fa-arrow-right\'></i> đang xử lý', '2022-08-15 05:02:04'),
(95, 29, 949, 'Trạng thái yêu cầu thay đổi từ đang xử lý <i class=\'fas fa-arrow-right\'></i> đã xử lý', '2022-08-15 05:02:38'),
(96, 29, 951, 'Trạng thái yêu cầu thay đổi từ đã xử lý <i class=\'fas fa-arrow-right\'></i> đang xử lý', '2022-08-15 05:03:22'),
(97, 29, 951, 'Trạng thái yêu cầu thay đổi từ đang xử lý <i class=\'fas fa-arrow-right\'></i> đóng yêu cầu', '2022-08-15 07:50:15'),
(98, 30, 951, 'Tạo yêu cầu', '2022-08-15 08:04:35'),
(99, 31, 951, 'Tạo yêu cầu', '2022-08-15 08:05:14'),
(100, 32, 951, 'Tạo yêu cầu', '2022-08-15 08:05:33'),
(101, 32, 951, 'Đã cập nhật lại yêu cầu', '2022-08-15 08:05:47'),
(102, 33, 951, 'Tạo yêu cầu', '2022-08-15 08:14:31'),
(103, 30, 949, 'Trạng thái yêu cầu thay đổi từ đang chờ xử lý<i class=\'fas fa-arrow-right\'></i> đang xử lý', '2022-08-15 09:46:30'),
(104, 30, 949, 'Trạng thái yêu cầu thay đổi từ đang xử lý <i class=\'fas fa-arrow-right\'></i> đã xử lý', '2022-08-15 09:47:29'),
(105, 30, 951, 'Trạng thái yêu cầu thay đổi từ đã xử lý <i class=\'fas fa-arrow-right\'></i> đang xử lý', '2022-08-15 09:49:24'),
(106, 30, 949, 'Trạng thái yêu cầu thay đổi từ đang xử lý <i class=\'fas fa-arrow-right\'></i> đã xử lý', '2022-08-15 09:51:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2022_07_19_034119_create_calendars_table', 2),
(7, '2022_07_19_081146_create_holidays_table', 3),
(8, '2022_07_19_100919_create_configs_table', 4),
(11, '2022_07_20_020703_create_groups_table', 5),
(14, '2022_07_20_082905_create_tickets_table', 6),
(15, '2022_07_25_025342_create_comments_table', 7),
(16, '2022_07_25_155006_create_comment_user_table', 8),
(17, '2022_07_29_113435_alter_column_user', 9),
(18, '2022_08_01_152112_create_jobs_table', 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `store_tokens`
--

CREATE TABLE `store_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `store_tokens`
--

INSERT INTO `store_tokens` (`id`, `user_id`, `access_token`, `domain`, `refresh_token`, `application_token`) VALUES
(15, 952, '0cbff962005c8e3d0053bf39000003b7403807d44cdd0b95996dec28461a9e13d5af84', 'admin.sconnect.edu.vn', 'fc3d2163005c8e3d0053bf39000003b74038075148af40de3806510e453ec668c81d10', '6fe81107f9b508394eb49c0848f1ef0c'),
(16, 945, 'db10fa62005c8e3d0053bf39000003b140380717bf53654ca36ae881b10ead924b71d2', 'oauth.bitrix.info', 'cb8f2163005c8e3d0053bf39000003b140380721ae6c9fae815227a37bd98d954bda2d', '6ba73a4db2058d1778bf620b2f0bb3c8'),
(17, 949, '7319fa62005c8e3d0053bf39000003b540380704926d3be849696ec1b8ec1308f431c8', 'admin.sconnect.edu.vn', '63982163005c8e3d0053bf39000003b54038075deb3ad5b99745fea62ed3b8a4c6f402', '24cf0f79f9d35942d6c4dba2f4b4a0c9'),
(18, 951, '720afa62005c8e3d0053bf39000003b74038075f488dd357e7b246bc62e96afd478d37', 'admin.sconnect.edu.vn', '62892163005c8e3d0053bf39000003b74038074a2c007cfe9595f663e298e9bfb4e9b5', '89dbd53930d9599e3f07746c1a4ce79c');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `cc` bigint(20) DEFAULT NULL,
  `name_creator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_creator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assignees_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deadline` timestamp NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` tinyint(4) NOT NULL COMMENT '1:Gấp, 2: Quan trọng, 3: Bình thường',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: Đang chở xử lý, 2: Đang xử lý,\r\n3: Đã xử lý xong,\r\n4: Đóng tác vụ',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tickets`
--

INSERT INTO `tickets` (`id`, `title`, `creator_id`, `cc`, `name_creator`, `email_creator`, `group_id`, `assignees_id`, `deadline`, `content`, `file`, `level`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(30, 'Thiết kế thông mình', 951, 947, 'Vũ Alpha', 'nachinh2406@gmail.com', 7, '[\"949\"]', '2022-08-15 05:00:00', '<p>Thiết kế thông mình</p>', NULL, 1, 3, NULL, '2022-08-15 08:04:35', '2022-08-15 09:51:54'),
(31, 'Sửa mạng tầng 11', 951, 3, 'Vũ Alpha', 'nachinh2406@gmail.com', 7, NULL, '2022-08-16 05:00:00', '<p>Sửa mạng tầng 11</p>', NULL, 2, 1, NULL, '2022-08-15 08:05:14', '2022-08-15 08:05:14'),
(32, 'Sửa mạng tầng 12', 951, 7, 'Vũ Alpha', 'nachinh2406@gmail.com', 7, '[\"949\"]', '2022-08-17 05:00:00', '<p>Sửa mạng tầng 12</p>', NULL, 3, 1, NULL, '2022-08-15 08:05:33', '2022-08-15 08:05:47'),
(33, 'Trồng cây xanh', 951, 9, 'Vũ Alpha', 'nachinh2406@gmail.com', 10, NULL, '2022-08-15 05:00:00', '<p>Trồng cây xanh</p>', NULL, 3, 1, NULL, '2022-08-15 08:14:31', '2022-08-15 08:14:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_bitrix_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `user_bitrix_id`, `name`, `email`, `avatar`, `email_verified_at`, `password`, `api_token`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, NULL, 'Tran Thanh  Ha', 'tranha@sconnect.edu.vn', NULL, NULL, NULL, NULL, NULL, '2022-08-15 07:18:38', '2022-08-15 07:18:38'),
(7, NULL, 'Ngô  Nguyệt', 'nguyetngo@sconnect.edu.vn', NULL, NULL, NULL, NULL, NULL, '2022-08-15 07:05:28', '2022-08-15 07:05:28'),
(9, NULL, 'Vũ Thị Thu  Hiền', 'hienvtt@s-connect.net', NULL, NULL, NULL, NULL, NULL, '2022-08-15 07:05:28', '2022-08-15 07:05:28'),
(15, NULL, 'Từ Thị  Thủy', 'thuytt@s-connect.net', NULL, NULL, NULL, NULL, NULL, '2022-08-15 07:18:38', '2022-08-15 07:18:38'),
(21, NULL, 'Nguyễn  Ngọc', 'ngocntb@s-connect.net', NULL, NULL, NULL, NULL, NULL, '2022-08-15 07:05:28', '2022-08-15 07:05:28'),
(945, 945, 'Nguyễn Thanh Giang', 'giangnt1@s-connect.net', 'https://cdn.bitrix24.vn/b17298131/main/08a/08ac52e455ae6bd9ac64eefd7d28c980/images.png', NULL, NULL, '9J8yxTUDueAXDx0uYPxAsuJsU2mLvMG9iiqBOjIHkHDsJ3Uzmnp0nmVYahsb', NULL, '2022-08-15 02:36:01', '2022-08-15 04:54:41'),
(949, 949, ' Ariana Grande', 'toiyeucuocsong2000@gmail.com', 'https://cdn.bitrix24.vn/b17298131/main/037/037e5dc54871a0d577738d3be739065d/maxresdefault.png', NULL, NULL, 'sBbnUoTl3E3xYxeiJ0axH32wJ9dhWR47pUtAhoOTCa6cUJsWyDZG7MkDtsLp', NULL, '2022-08-15 02:36:16', '2022-08-15 09:01:28'),
(951, 951, 'Vũ Alpha', 'nachinh2406@gmail.com', 'https://cdn.bitrix24.vn/b17298131/main/b20/b200d92a766ecf95c5a81ac2daeaad41/cau-rong-da-nang.png', NULL, NULL, 'QQM540d0Ygt4GTU1jbdFN75Zc2yeN0JlxPppb9Mmsw20roIrkfsrNOrJEwjT', NULL, '2022-08-15 02:49:32', '2022-08-15 07:57:26');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `calendars`
--
ALTER TABLE `calendars`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `comment_user`
--
ALTER TABLE `comment_user`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `configs`
--
ALTER TABLE `configs`
  ADD UNIQUE KEY `configs_cfg_key_unique` (`cfg_key`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `histories`
--
ALTER TABLE `histories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `store_tokens`
--
ALTER TABLE `store_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tickets_creator_id_foreign` (`creator_id`),
  ADD KEY `tickets_group_id_foreign` (`group_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_api_token_unique` (`api_token`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `calendars`
--
ALTER TABLE `calendars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `comment_user`
--
ALTER TABLE `comment_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `histories`
--
ALTER TABLE `histories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT cho bảng `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `store_tokens`
--
ALTER TABLE `store_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=952;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
