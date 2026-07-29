-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2026 at 02:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_website_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','editor','staff') DEFAULT 'editor',
  `profile_photo` varchar(500) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `role`, `profile_photo`, `last_login`, `is_active`, `created_at`, `updated_at`) VALUES
(18, 'macarthy junior', '2500715710@mubs.ac.ug', '$2y$10$l/T5h7nIWDmpvcEmvJjEVOIRWQmCs5WLYBZU2N7ekPP7r76G7CM4W', 'super_admin', '', '2026-07-17 12:56:15', 1, '2026-06-26 13:00:56', '2026-07-17 10:56:15'),
(19, '', '', '$2y$10$k1YMe85GHAAp4W.wZJidsuWTUwxZWFgplYvKKyiXlM622Zs9xF9ee', 'editor', '', NULL, 1, '2026-06-26 16:05:23', '2026-06-26 16:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `admissions_documents`
--

CREATE TABLE `admissions_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `filename` varchar(500) NOT NULL,
  `file_size` varchar(20) DEFAULT NULL,
  `level` enum('S1','S5','ALL') DEFAULT 'ALL',
  `downloads` int(10) UNSIGNED DEFAULT 0,
  `sort_order` smallint(6) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `enquiry_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admissions_documents`
--

INSERT INTO `admissions_documents` (`id`, `title`, `description`, `filename`, `file_size`, `level`, `downloads`, `sort_order`, `is_active`, `created_at`, `enquiry_id`) VALUES
(1, 'results', 'uneb', 'uploads/documents/academic_doc_6a5a07355ae7e.jpg', '74 KB', 'S5', 0, 0, 1, '2026-07-17 10:43:01', 7);

-- --------------------------------------------------------

--
-- Table structure for table `admissions_enquiries`
--

CREATE TABLE `admissions_enquiries` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_name` varchar(200) NOT NULL,
  `parent_phone` varchar(30) NOT NULL,
  `parent_email` varchar(200) DEFAULT NULL,
  `student_name` varchar(200) NOT NULL,
  `entry_level` enum('S1','S5') NOT NULL,
  `current_school` varchar(200) DEFAULT NULL,
  `ple_aggregate` tinyint(3) UNSIGNED DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','contacted','enrolled','declined') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admissions_enquiries`
--

INSERT INTO `admissions_enquiries` (`id`, `parent_name`, `parent_phone`, `parent_email`, `student_name`, `entry_level`, `current_school`, `ple_aggregate`, `message`, `status`, `created_at`) VALUES
(3, 'rwoms', '0761448099', 'westboyb88y373@gmail.com', 'macarthy nuuum', '', 'kiswa', 8, 'yeats', 'enrolled', '2026-06-10 21:00:00'),
(4, 'breyed', '0761448099', 'westbouuboy373@gmail.com', 'stydebt', '', 'uuuu', 99, 'hi there', 'enrolled', '2026-06-03 21:00:00'),
(5, 'emmanuel', '0761448099', 'westboyboyo73@gmail.com', 'macarthy juniorlll', 'S1', 'kiswaswa', 7, 'jjj', 'enrolled', '2026-07-14 19:08:24'),
(7, 'edrine paul', '07614480940', 'westboyboy393@gmail.com', 'Westboy Boy', 'S5', 'mbuya pp', 4, 'he is a good boy', 'enrolled', '2026-07-17 10:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `admissions_requirements`
--

CREATE TABLE `admissions_requirements` (
  `id` int(10) UNSIGNED NOT NULL,
  `level` enum('S1','S5','OTHER') NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `sort_order` smallint(6) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(80) DEFAULT NULL,
  `record_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(300) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `ip_address`, `is_read`, `replied_at`, `created_at`) VALUES
(1, 'dang james', 'westboyboy373@gmail.com', '+256761448094', 'lost school id', 'found in the library', '::1', 0, NULL, '2026-07-17 15:24:05'),
(2, 'dang james', 'westboyboy373@gmail.com', '+256761448094', 'lost school id', 'found in the library', '::1', 0, NULL, '2026-07-17 15:25:33'),
(3, 'dang james', 'westboyboy373@gmail.com', '+256761448094', 'lost school id', 'found in the library', '::1', 0, NULL, '2026-07-17 15:25:36'),
(4, 'dang james', 'westboyboy373@gmail.com', '+256761448094', 'lost school id', 'found in the library', '::1', 0, NULL, '2026-07-17 15:25:46'),
(5, 'george frog', 'westboyboy303@gmail.com', '+256781448094', 'lost school library book', 'let me know where to put it', '::1', 0, NULL, '2026-07-17 15:26:55'),
(6, 'george frog', 'westboyboy303@gmail.com', '+256781448094', 'lost school library book', 'let me know where to put it', '::1', 0, NULL, '2026-07-17 15:28:11'),
(7, 'george frog', 'westboyboy303@gmail.com', '+256781448094', 'lost school library book', 'let me know where to put it', '::1', 0, NULL, '2026-07-17 15:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `head_of_dept` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(300) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(10) UNSIGNED NOT NULL,
  `question` varchar(400) NOT NULL,
  `answer` text NOT NULL,
  `category` enum('admissions','fees','academics','boarding','general') DEFAULT 'general',
  `sort_order` smallint(6) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_albums`
--

CREATE TABLE `gallery_albums` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_albums`
--

INSERT INTO `gallery_albums` (`id`, `name`, `description`, `cover_image`, `sort_order`, `is_published`, `created_at`) VALUES
(1, 'nnnnnnnnn', 'nnnnnnnn', NULL, 0, 1, '2026-07-14 16:48:15'),
(2, '2026', 'test', NULL, 0, 1, '2026-07-14 17:01:14'),
(3, '2026', 'test', NULL, 0, 1, '2026-07-14 17:01:22'),
(4, '2028', 'test', NULL, 0, 1, '2026-07-14 17:03:08'),
(5, '2028', 'test', NULL, 0, 1, '2026-07-14 17:03:13'),
(6, '2028', 'test', NULL, 0, 1, '2026-07-14 17:13:52'),
(7, '2028', 'test', NULL, 0, 1, '2026-07-14 17:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_photos`
--

CREATE TABLE `gallery_photos` (
  `id` int(10) UNSIGNED NOT NULL,
  `album_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(500) NOT NULL,
  `caption` varchar(300) DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT 0,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_photos`
--

INSERT INTO `gallery_photos` (`id`, `album_id`, `filename`, `caption`, `sort_order`, `uploaded_by`, `created_at`) VALUES
(1, 1, 'uploads/gallery/gallery_6a56684f4341e4.44506266.jpg', 'nnnnn', 1, 18, '2026-07-14 16:48:15'),
(2, 2, 'uploads/gallery/gallery_6a566b5ad892c1.92658247.jpg', 'test', 1, 18, '2026-07-14 17:01:14'),
(3, 3, 'uploads/gallery/gallery_6a566b62075953.82577797.jpg', 'test', 1, 18, '2026-07-14 17:01:22'),
(4, 4, 'uploads/gallery/gallery_6a566bccd4a6f8.99807865.jpg', 'test', 0, 18, '2026-07-14 17:03:08'),
(5, 5, 'uploads/gallery/gallery_6a566bd1961f86.16856808.jpg', 'test', 0, 18, '2026-07-14 17:03:13'),
(6, 6, 'uploads/gallery/gallery_6a566e5037d6c1.77764511.jpg', 'test', 0, 18, '2026-07-14 17:13:52'),
(7, 7, 'uploads/gallery/gallery_6a566e5695f895.21422203.jpg', 'test', 0, 18, '2026-07-14 17:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(400) NOT NULL,
  `slug` varchar(450) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `body` longtext NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `author_id` int(10) UNSIGNED DEFAULT NULL,
  `views` int(10) UNSIGNED DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `excerpt`, `body`, `featured_image`, `author_id`, `views`, `is_published`, `is_featured`, `published_at`, `created_at`, `updated_at`) VALUES
(3, NULL, 'gggg', 'gggg', 'nnnnn', 'nnnnnn', 'uploads/news/news_6a53975bb5e447.63281498.png', 18, 0, 1, 0, '2026-07-12 15:32:11', '2026-07-12 12:32:11', '2026-07-12 12:32:11'),
(5, NULL, 'sports day coming soon', 'sports-day-coming-soon', 'sports day', 'sports day coming next week', '', 18, 0, 1, 0, '2026-07-12 15:50:54', '2026-07-12 12:50:54', '2026-07-12 12:50:54'),
(6, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a539bf8ebe440.94069741.jpg', 18, 0, 1, 0, '2026-07-12 15:51:52', '2026-07-12 12:51:52', '2026-07-12 12:51:52'),
(7, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a539fd3c89920.28809720.jpg', 18, 0, 1, 0, '2026-07-12 16:08:19', '2026-07-12 13:08:19', '2026-07-12 13:08:19'),
(8, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a00c853039.65399566.jpg', 18, 0, 1, 0, '2026-07-12 16:09:16', '2026-07-12 13:09:16', '2026-07-12 13:09:16'),
(9, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a0d4bfe966.71439522.jpg', 18, 0, 1, 0, '2026-07-12 16:12:36', '2026-07-12 13:12:36', '2026-07-12 13:12:36'),
(10, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a0e5718376.28541272.jpg', 18, 0, 1, 0, '2026-07-12 16:12:53', '2026-07-12 13:12:53', '2026-07-12 13:12:53'),
(11, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a17a103ab1.78647562.jpg', 18, 0, 1, 0, '2026-07-12 16:15:22', '2026-07-12 13:15:22', '2026-07-12 13:15:22'),
(12, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a198317f08.94010230.jpg', 18, 0, 1, 0, '2026-07-12 16:15:52', '2026-07-12 13:15:52', '2026-07-12 13:15:52'),
(13, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a1f5b30f63.99713654.jpg', 18, 0, 1, 0, '2026-07-12 16:17:25', '2026-07-12 13:17:25', '2026-07-12 13:17:25'),
(14, NULL, 'sports dayd', 'sports-dayd', 'spotddd', 'nnnn', 'uploads/news/news_6a53a21f592392.73757900.jpg', 18, 0, 1, 0, '2026-07-12 16:18:07', '2026-07-12 13:18:07', '2026-07-12 13:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `is_confirmed` tinyint(1) DEFAULT 0,
  `confirm_token` varchar(64) DEFAULT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `name`, `is_confirmed`, `confirm_token`, `subscribed_at`, `unsubscribed_at`) VALUES
(1, 'westboyboy373@gmail.com', 'mather', 1, 'd4eae10e4b10ecd2367fd4d6d4eb7367', '2026-07-17 13:42:16', NULL),
(2, 'westboyboy3993@gmail.com', 'westbou', 1, '0471d6d84d71c4cd0bf012062dfe0a4d', '2026-07-17 14:22:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `news_categories`
--

CREATE TABLE `news_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `color` varchar(7) DEFAULT '#1565C0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

CREATE TABLE `page_content` (
  `id` int(10) UNSIGNED NOT NULL,
  `page` varchar(60) NOT NULL,
  `section` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_info`
--

CREATE TABLE `school_info` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `role` varchar(200) NOT NULL,
  `subjects` varchar(300) DEFAULT NULL,
  `qualification` varchar(300) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(500) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `is_management` tinyint(1) DEFAULT 0,
  `sort_order` smallint(6) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `level` set('O_LEVEL','A_LEVEL') NOT NULL,
  `is_compulsory` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_name` varchar(200) NOT NULL,
  `author_role` varchar(100) DEFAULT NULL,
  `photo` varchar(500) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `sort_order` smallint(6) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admissions_documents`
--
ALTER TABLE `admissions_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_document_parent_enquiry` (`enquiry_id`);

--
-- Indexes for table `admissions_enquiries`
--
ALTER TABLE `admissions_enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`,`created_at`);

--
-- Indexes for table `admissions_requirements`
--
ALTER TABLE `admissions_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin` (`admin_id`,`created_at`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_unread` (`is_read`,`created_at`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_events_creator` (`created_by`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cat` (`category`,`sort_order`);

--
-- Indexes for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_photos_uploader` (`uploaded_by`),
  ADD KEY `idx_album` (`album_id`,`sort_order`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_news_category` (`category_id`),
  ADD KEY `fk_news_author` (`author_id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_confirmed` (`is_confirmed`);

--
-- Indexes for table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_page_section` (`page`,`section`),
  ADD KEY `fk_page_content_editor` (`updated_by`);

--
-- Indexes for table `school_info`
--
ALTER TABLE `school_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `fk_school_info_updated_by` (`updated_by`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_staff_department` (`department_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subjects_department` (`department_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `admissions_documents`
--
ALTER TABLE `admissions_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admissions_enquiries`
--
ALTER TABLE `admissions_enquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admissions_requirements`
--
ALTER TABLE `admissions_requirements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school_info`
--
ALTER TABLE `school_info`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions_documents`
--
ALTER TABLE `admissions_documents`
  ADD CONSTRAINT `fk_document_parent_enquiry` FOREIGN KEY (`enquiry_id`) REFERENCES `admissions_enquiries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_audit_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_creator` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  ADD CONSTRAINT `fk_photos_album` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_photos_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_news_category` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `page_content`
--
ALTER TABLE `page_content`
  ADD CONSTRAINT `fk_page_content_editor` FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `school_info`
--
ALTER TABLE `school_info`
  ADD CONSTRAINT `fk_school_info_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_subjects_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
