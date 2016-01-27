drop database if exists smartmembers_2;
create database smartmembers_2;
use smartmembers_2;

source sql/schema.sql;

-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2016 at 07:23 AM
-- Server version: 5.6.25
-- PHP Version: 5.6.11

--
-- Table structure for table `site_notices_seen`
--


INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `email_hash`, `password`, `verified`, `facebook_user_id`, `profile_image`, `access_token`, `access_token_expired`, `remember_token`, `reset_token`, `vanity_username`, `affiliate_id`, `last_logged_in`, `deleted_at`, `created_at`, `updated_at`, `setup_wizard_complete`) VALUES
(1, 'SM Developer', '', NULL, 'developer@sm.com', '2249218ac7473ec6630a5f59c8977015', '$2y$11$rLNivTuepPPRhQaA4NF3BurgnROJRXAbSUEQTMazEU.PUrwV/5J16', 1, NULL, '', 'c7cc98d0167ac722cf3d5ee149ed7a51', '2016-04-27 05:22:53', NULL, '', NULL, NULL, '2016-01-27 00:25:48', NULL, '2016-01-27 00:22:52', '2016-01-27 00:27:20', 1),
(139458, '', '', NULL, 'member1@sm.com', 'b340feeee92ffaafdc335c7094f1644a', '$2y$11$60ZvxLvwluBcuZ6FEbPKZe44He3HW8.VxGrLcahPog22XiQXpy0uS', 1, NULL, '', 'a2440c2d83b2fb9b31e300a8ad080e48', '2016-04-27 05:58:47', NULL, '44311c54eec6ba0323385704bfde2643', NULL, NULL, NULL, NULL, '2016-01-27 00:58:47', '2016-01-27 00:59:17', 0),
(139459, '', '', NULL, 'member2@sm.com', '6965adcc58f38828b9102e44ebb5f67a', '$2y$11$9ruJc9J0KJarY3eZyg1wWOSsBACwToGOzixcyB298p4p42Xqn32Hy', 1, NULL, '', '937f922c1731c830cb265bde7e383107', '2016-04-27 05:58:50', NULL, 'fd7752aa92b27d5b74aa2c9f8788ee9a', NULL, NULL, NULL, NULL, '2016-01-27 00:58:50', '2016-01-27 00:59:17', 0),
(139460, '', '', NULL, 'gold1@sm.com', '01a3cf9807b127e4c96f820782eb65d5', '$2y$11$ASUT7ifW/wGjHG0engP7VOVITc8lJIodDhfTqlrrkM9tYk1C0FevS', 1, NULL, '', '0416392c84e65790d4d1aa610c679286', '2016-04-27 05:58:51', NULL, '7d90575ef3b45e84d000994ac76f414a', NULL, NULL, NULL, NULL, '2016-01-27 00:58:52', '2016-01-27 00:59:17', 0),
(139461, '', '', NULL, 'gold2@sm.com', 'e4f120c065f06ad8ee96ef80f8a98939', '$2y$11$tbWoMRrH9CLAcGmqls5cBuagFmeMPAnF1iXEMKfG57ML9y.0TjTey', 1, NULL, '', '8cc03202d51990823cd451072e8371ac', '2016-04-27 05:58:53', NULL, 'cab16b587ad13044c43b96259a6d6e9a', NULL, NULL, NULL, NULL, '2016-01-27 00:58:53', '2016-01-27 00:59:17', 0),
(139462, '', '', NULL, 'silver1@sm.com', 'a58d0c9b6718502425b88afd69123b7f', '$2y$11$bw9HX3zL65o.mcWGj7L2SO6KviJz6u.qvGFe6iG/xfYZwAOPbdree', 1, NULL, '', '41a08afa5aaa22f24ac4111f99b102b1', '2016-04-27 05:58:54', NULL, 'b4eb19ba5552dc2f64ead2bc7f5184a3', NULL, NULL, NULL, NULL, '2016-01-27 00:58:55', '2016-01-27 00:59:17', 0),
(139463, '', '', NULL, 'silver2@sm.com', '07613341506270e7499f27a16dd1a664', '$2y$11$uByfXumG2g74eNX84RIKl.Eia/XHVFt3tTOewRIOQuU9Dud3DME1q', 1, NULL, '', '9f7bcb93692c49ebf688884e39e048df', '2016-04-27 05:58:56', NULL, '023e301e5b591a4fa1883ea4bef9b25f', NULL, NULL, NULL, NULL, '2016-01-27 00:58:56', '2016-01-27 00:59:17', 0),
(139464, '', '', NULL, 'silver3@sm.com', '2dd6bc99acbf294ba5289a8e09dbe7f6', '$2y$11$bd7HUG5in/FwJUs1Uir9.Ov0e/6axn5xOTcKzKdtx.R9QcN3P46eC', 1, NULL, '', 'b68455b1313342054a280807e8369aff', '2016-04-27 05:58:58', NULL, 'c53f239e16f2a0d5d58acd01a5386837', NULL, NULL, NULL, NULL, '2016-01-27 00:58:58', '2016-01-27 00:59:17', 0),
(139465, '', '', NULL, 'bronze1@sm.com', '03ab13217d8dba20cf47559328eaffca', '$2y$11$Jmnks.qC6eV/pPzrlSqdm.abWx7f8fBFONDIQbaFRXQUAokC9oMYu', 1, NULL, '', '67833c16fb3284c9b4cdff329d64d3da', '2016-04-27 05:58:59', NULL, '28d16a5c417b0b2337fa33744524827e', NULL, NULL, NULL, NULL, '2016-01-27 00:58:59', '2016-01-27 00:59:17', 0),
(139466, '', '', NULL, 'bronze2@sm.com', '0c7082b677e6593df11d68ff1d5b4eae', '$2y$11$EQPHPS9bb74xnRvqWwL8suDcFse9Z/LAOlOgCCqOytnsn2TrGR1j.', 1, NULL, '', '9959ef893cbb9bf8f382fd27dd92a6a4', '2016-04-27 05:59:00', NULL, 'dc97b2e393d975acd3b1438785722253', NULL, NULL, NULL, NULL, '2016-01-27 00:59:01', '2016-01-27 00:59:17', 0),
(139467, '', '', NULL, 'bronze3@sm.com', '2fbbeda8e51e241723e7693bccf4b422', '$2y$11$o2PwW9zXhqPZGY25mTmQT.sTgvUgkq8AoGURpcgdny04OM8qQFRPW', 1, NULL, '', '508c7c8d6c678e9ef8317988616e5831', '2016-04-27 05:59:02', NULL, '5905c8b5a15457c6d0f9a4cde8361db0', NULL, NULL, NULL, NULL, '2016-01-27 00:59:02', '2016-01-27 00:59:17', 0),
(139468, '', '', NULL, 'gold+silver1@sm.com', '7b76cc036b1e9dc959fc60e7e5fbfa2f', '$2y$11$Tan6w7/zKTw7AYp6KXgRfepO0f/bomP5R8oz2.NkQL/s2C5CZ3w8.', 1, NULL, '', '37df4a054c5b8110e51845881cc7a103', '2016-04-27 05:59:03', NULL, '3e77ec96e2c45f47972e5126290e4e66', NULL, NULL, NULL, NULL, '2016-01-27 00:59:04', '2016-01-27 00:59:17', 0),
(139469, '', '', NULL, 'gold+silver2@sm.com', 'e9f9892b2d966b26fbad91ffc7dda8bb', '$2y$11$voltLpKcBvQtBIL3RdpyP.cp.mxPkQ3lO0rd67Sr1OCscoU4vcHba', 1, NULL, '', 'e23aeb5caabc6933f781e7e80ef6a4f7', '2016-04-27 05:59:05', NULL, 'c311a7665f3a9433719e790aa0b79b53', NULL, NULL, NULL, NULL, '2016-01-27 00:59:05', '2016-01-27 00:59:17', 0);

-- --------------------------------------------------------


INSERT INTO `access_grants` (`id`, `access_level_id`, `grant_id`, `deleted_at`, `updated_at`, `created_at`) VALUES
(4165, 2, 3285, NULL, '2016-01-27 05:47:30', '2016-01-27 00:47:30'),
(4166, 3, 3286, NULL, '2016-01-27 05:48:06', '2016-01-27 00:48:06'),
(4167, 3, 3285, NULL, '2016-01-27 05:48:06', '2016-01-27 00:48:06');

-- --------------------------------------------------------


--
-- Dumping data for table `access_levels`
--

INSERT INTO `access_levels` (`id`, `site_id`, `name`, `information_url`, `redirect_url`, `product_id`, `cb_product_id`, `wso_product_id`, `zaxaa_product_id`, `jvzoo_button`, `price`, `currency`, `payment_interval`, `stripe_plan_id`, `stripe_integration`, `paypal_integration`, `hash`, `deleted_at`, `updated_at`, `created_at`, `facebook_group_id`, `expiration_period`, `hide_unowned_content`, `trial_amount`, `trial_duration`, `trial_interval`, `webinar_url`) VALUES
(1753, 6192, 'Smart Member', '', '', '167089', NULL, NULL, NULL, NULL, 497.00, 'USD', 'one_time', NULL, 0, 0, '5d99d2de51be1c522546d17f4cab7d30', NULL, NULL, '2016-01-27 05:16:25', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(1, 1, 'Bronze', '', '', NULL, NULL, NULL, NULL, NULL, 49.99, 'USD', 'one_time', NULL, 0, 0, '18850568987db7786c8fcb4924a849fd', NULL, '2016-01-27 05:46:45', '2016-01-27 00:46:45', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(2, 1, 'Silver', '', '', NULL, NULL, NULL, NULL, NULL, 74.99, 'USD', 'one_time', NULL, 0, 0, '9f233b5a6f692361cecc5f8b3ea61c80', NULL, '2016-01-27 05:47:17', '2016-01-27 00:47:17', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(3, 1, 'Gold', '', '', NULL, NULL, NULL, NULL, NULL, 99.99, 'USD', 'one_time', NULL, 0, 0, '38b9a35acf7a2978c96dee55ffcdd189', NULL, '2016-01-27 05:48:05', '2016-01-27 00:48:05', NULL, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `access_level_shared_keys`
--



INSERT INTO `discussion_settings` (`id`, `site_id`, `show_comments`, `newest_comments_first`, `close_to_new_comments`, `allow_replies`, `public_comments`, `deleted_at`, `created_at`, `updated_at`) VALUES
(52942, 0, 1, 0, 0, 1, 1, NULL, '2016-01-27 00:30:11', '2016-01-27 00:30:11'),
(52943, 0, 1, 0, 1, 0, 0, NULL, '2016-01-27 00:31:00', '2016-01-27 00:31:16'),
(52944, 0, 1, 1, 0, 1, 0, NULL, '2016-01-27 00:33:18', '2016-01-27 00:33:18'),
(52945, 0, 1, 1, 0, 1, 0, NULL, '2016-01-27 00:34:10', '2016-01-27 00:34:10'),
(52946, 0, 1, 1, 0, 1, 1, NULL, '2016-01-27 00:35:29', '2016-01-27 00:41:46'),
(52947, 0, 1, 1, 1, 1, 1, NULL, '2016-01-27 00:37:07', '2016-01-27 00:37:07'),
(52948, 0, 0, 0, 0, 0, 0, NULL, '2016-01-27 00:49:24', '2016-01-27 00:50:13'),
(52949, 0, 1, 1, 1, 1, 1, NULL, '2016-01-27 00:50:00', '2016-01-27 00:50:00'),
(52950, 0, 0, 0, 0, 1, 1, NULL, '2016-01-27 00:51:08', '2016-01-27 00:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `downloads_history`
--



INSERT INTO `events` (`id`, `site_id`, `event_name`, `user_id`, `email`, `count`, `deleted_at`, `created_at`, `updated_at`) VALUES
(14370, 6192, 'sent-welcome-email', 139457, 'developer@sm.com', 1, NULL, '2016-01-27 00:22:55', '2016-01-27 00:22:55'),
(14371, 6192, 'registered', 139457, NULL, 1, NULL, '2016-01-27 00:22:55', '2016-01-27 00:22:55'),
(14372, 0, 'logged-in', 1, NULL, 1, NULL, '2016-01-27 00:25:48', '2016-01-27 00:25:48'),
(14373, 0, 'created-site', 1, '', 1, NULL, '2016-01-27 00:27:20', '2016-01-27 00:27:20'),
(14374, 1, 'created-lesson', 1, '', 9, NULL, '2016-01-27 00:30:11', '2016-01-27 00:51:08'),
(14375, 1, 'updated-lesson', 1, '', 6, NULL, '2016-01-27 00:31:15', '2016-01-27 01:12:01'),
(14376, 1, 'sent-welcome-email', 139458, 'member1@sm.com', 1, NULL, '2016-01-27 00:58:50', '2016-01-27 00:58:50'),
(14377, 1, 'sent-welcome-email', 139459, 'member2@sm.com', 1, NULL, '2016-01-27 00:58:51', '2016-01-27 00:58:51'),
(14378, 1, 'sent-welcome-email', 139460, 'gold1@sm.com', 1, NULL, '2016-01-27 00:58:53', '2016-01-27 00:58:53'),
(14379, 1, 'sent-welcome-email', 139461, 'gold2@sm.com', 1, NULL, '2016-01-27 00:58:54', '2016-01-27 00:58:54');

-- --------------------------------------------------------



INSERT INTO `event_metadata` (`id`, `event_id`, `key`, `value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(220921, 14371, 'referring-url', '', NULL, '2016-01-27 00:22:55', '2016-01-27 00:22:55'),
(220920, 14371, 'request-url', 'http://sm.smartmember.dev/sign/up/', NULL, '2016-01-27 00:22:55', '2016-01-27 00:22:55'),
(220922, 14372, 'login-url', 'http://my.smartmember.dev/sign/in/', NULL, '2016-01-27 00:25:48', '2016-01-27 00:25:48'),
(220923, 14372, 'referring-url', 'http://my.smartmember.dev/admin/sites', NULL, '2016-01-27 00:25:48', '2016-01-27 00:25:48'),
(220925, 14373, 'subdomain', 'developer', NULL, '2016-01-27 00:27:20', '2016-01-27 00:27:20'),
(221330, 14375, 'lesson-id', '1', NULL, '2016-01-27 01:12:01', '2016-01-27 01:12:01'),
(221263, 14374, 'lesson-title', 'Lesson Bronze', NULL, '2016-01-27 00:51:08', '2016-01-27 00:51:08'),
(221264, 14374, 'lesson-id', '57534', NULL, '2016-01-27 00:51:08', '2016-01-27 00:51:08'),
(221329, 14375, 'lesson-title', 'Lesson Public 1', NULL, '2016-01-27 01:12:01', '2016-01-27 01:12:01');

-- --------------------------------------------------------

--
-- Table structure for table `forum_categories`
--



INSERT INTO `imports_queue` (`id`, `email`, `access_levels`, `site_id`, `expiry`, `job_id`, `created_at`, `deleted_at`, `updated_at`) VALUES
(129841, 'member1@sm.com', '', 1, '0000-00-00 00:00:00', 1271, '2016-01-27 05:53:12', '2016-01-27 05:58:50', '2016-01-27 05:58:50'),
(129842, 'member2@sm.com', '', 1, '0000-00-00 00:00:00', 1271, '2016-01-27 05:53:12', '2016-01-27 05:58:51', '2016-01-27 05:58:51'),
(129843, 'gold1@sm.com', '', 1, '0000-00-00 00:00:00', 1272, '2016-01-27 05:53:39', '2016-01-27 05:58:53', '2016-01-27 05:58:53'),
(129844, 'gold2@sm.com', '', 1, '0000-00-00 00:00:00', 1272, '2016-01-27 05:53:39', '2016-01-27 05:58:54', '2016-01-27 05:58:54'),
(129845, 'silver1@sm.com', '2', 1, '0000-00-00 00:00:00', 1273, '2016-01-27 05:54:15', '2016-01-27 05:58:56', '2016-01-27 05:58:56'),
(129846, 'silver2@sm.com', '2', 1, '0000-00-00 00:00:00', 1273, '2016-01-27 05:54:15', '2016-01-27 05:58:58', '2016-01-27 05:58:58'),
(129847, 'silver3@sm.com', '2', 1, '0000-00-00 00:00:00', 1273, '2016-01-27 05:54:15', '2016-01-27 05:58:59', '2016-01-27 05:58:59'),
(129848, 'bronze1@sm.com', '1', 1, '0000-00-00 00:00:00', 1274, '2016-01-27 05:54:41', '2016-01-27 05:59:00', '2016-01-27 05:59:00'),
(129849, 'bronze2@sm.com', '1', 1, '0000-00-00 00:00:00', 1274, '2016-01-27 05:54:41', '2016-01-27 05:59:02', '2016-01-27 05:59:02'),
(129850, 'bronze3@sm.com', '1', 1, '0000-00-00 00:00:00', 1274, '2016-01-27 05:54:41', '2016-01-27 05:59:03', '2016-01-27 05:59:03'),
(129851, 'gold+silver1@sm.com', '2,3', 1, '0000-00-00 00:00:00', 1275, '2016-01-27 05:55:42', '2016-01-27 05:59:05', '2016-01-27 05:59:05'),
(129852, 'gold+silver2@sm.com', '2,3', 1, '0000-00-00 00:00:00', 1275, '2016-01-27 05:55:42', '2016-01-27 05:59:06', '2016-01-27 05:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `import_jobs`
--


--
-- Dumping data for table `import_jobs`
--

INSERT INTO `import_jobs` (`id`, `site_id`, `total_count`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1271, 1, 2, '2016-01-27 00:53:12', NULL, '2016-01-27 05:53:12'),
(1272, 1, 2, '2016-01-27 00:53:39', NULL, '2016-01-27 05:53:39'),
(1273, 1, 3, '2016-01-27 00:54:15', NULL, '2016-01-27 05:54:15'),
(1274, 1, 3, '2016-01-27 00:54:41', NULL, '2016-01-27 05:54:41'),
(1275, 1, 2, '2016-01-27 00:55:42', NULL, '2016-01-27 05:55:42');

-- --------------------------------------------------------


INSERT INTO `lessons` (`id`, `site_id`, `author_id`, `module_id`, `sort_order`, `next_lesson`, `prev_lesson`, `presenter`, `title`, `content`, `note`, `type`, `embed_content`, `featured_image`, `transcript_content`, `transcript_button_text`, `audio_file`, `access_level_type`, `access_level_id`, `discussion_settings_id`, `permalink`, `remote_id`, `deleted_at`, `created_at`, `updated_at`, `end_published_date`, `published_date`, `preview_dripfeed`, `preview_schedule`, `transcript_content_public`, `always_show_featured_image`, `show_content_publicly`) VALUES
(1, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Public 1', '<p>I am public lesson 1 content with display comment, enable replies and auto-approve comments</p>', '', NULL, '', '', '', 'Transcript', '', 1, 0, 52942, 'lesson-public-1', NULL, NULL, '2016-01-27 00:30:11', '2016-01-27 01:12:01', NULL, '2016-01-27 05:28:00', 0, 0, 0, 0, 1),
(2, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Public 2', '<p><span>I am public lesson 2 content with display comment and close to new replies</span></p>', '', NULL, '', '', '', 'Transcript', '', 1, 0, 52943, 'lesson-public-2', NULL, NULL, '2016-01-27 00:31:00', '2016-01-27 00:31:38', NULL, '2016-01-27 05:30:00', 0, 0, 0, 0, 1),
(3, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Member 1', '<p><span>I am member lesson 1 content with display comment, enable replies and newest comments on top</span></p>', '', NULL, '', '', '', 'Transcript', '', 3, 0, 52944, 'lesson-member-1', NULL, NULL, '2016-01-27 00:33:18', '2016-01-27 00:33:18', NULL, '2016-01-27 05:32:00', 0, 0, 0, 0, 0),
(4, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Member 2', '<p><span>I am member lesson 2 content with enable replies and newest comments on top</span></p>', '', NULL, '', '', '', 'Transcript', '', 3, 0, 52945, 'lesson-member-2', NULL, NULL, '2016-01-27 00:34:10', '2016-01-27 00:34:10', NULL, '2016-01-27 05:32:00', 0, 0, 0, 0, 0),
(5, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Private 1', '<p><span>I am private lesson 1 content with display comment, enable replies, auto-approve comments and newest comments on top</span></p>', '', NULL, '', '', '', 'Transcript', '', 4, 0, 52946, 'lesson-private-1', NULL, NULL, '2016-01-27 00:35:29', '2016-01-27 00:41:46', NULL, '2016-01-27 05:34:00', 0, 0, 0, 0, 0),
(6, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Private 2', '<p><span>I am private lesson 2 content with display comment, enable replies, auto-approve comments, close to new comments and newest comments on top</span></p>', '', NULL, '', '', '', 'Transcript', '', 4, 0, 52947, 'lesson-private-2', NULL, NULL, '2016-01-27 00:37:07', '2016-01-27 00:37:07', NULL, '2016-01-27 05:34:00', 0, 0, 0, 0, 0),
(7, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Gold', '<p><span>I am gold lesson content with no discussion settings</span></p>', '', NULL, '', '', '', 'Transcript', '', 2, 3287, 52948, 'lesson-gold', NULL, NULL, '2016-01-27 00:49:24', '2016-01-27 00:50:13', NULL, '2016-01-27 05:34:00', 0, 0, 0, 0, 0),
(8, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Silver', '<p><span>I am silver lesson content with all discussion settings</span></p>', '', NULL, '', '', '', 'Transcript', '', 2, 3286, 52949, 'lesson-silver', NULL, NULL, '2016-01-27 00:50:00', '2016-01-27 00:52:28', NULL, '2016-01-27 05:34:00', 0, 0, 0, 0, 0),
(9, 1, 1, 0, 1, 0, 0, NULL, 'Lesson Bronze', '<p><span>I am bronze lesson content with enable new replies and auto-approve comments</span></p>', '', NULL, '', '', '', 'Transcript', '', 2, 3285, 52950, 'lesson-bronze', NULL, NULL, '2016-01-27 00:51:08', '2016-01-27 00:51:08', NULL, '2016-01-27 05:34:00', 0, 0, 0, 0, 0);

-- --------------------------------------------------------


INSERT INTO `permalinks` (`id`, `permalink`, `site_id`, `target_id`, `type`, `deleted_at`, `updated_at`, `created_at`) VALUES
(112606, 'lesson-public-1', 1, 1, 'lessons', NULL, '2016-01-27 05:30:11', '2016-01-27 00:30:11'),
(112607, 'lesson-public-2', 1, 2, 'lessons', NULL, '2016-01-27 05:31:00', '2016-01-27 00:31:00'),
(112608, 'lesson-member-1', 1, 3, 'lessons', NULL, '2016-01-27 05:33:18', '2016-01-27 00:33:18'),
(112609, 'lesson-member-2', 1, 4, 'lessons', NULL, '2016-01-27 05:34:10', '2016-01-27 00:34:10'),
(112610, 'lesson-private-1', 1, 5, 'lessons', NULL, '2016-01-27 05:35:29', '2016-01-27 00:35:29'),
(112611, 'lesson-private-2', 1, 6, 'lessons', NULL, '2016-01-27 05:37:07', '2016-01-27 00:37:07'),
(112612, 'lesson-gold', 1, 7, 'lessons', NULL, '2016-01-27 05:49:24', '2016-01-27 00:49:24'),
(112613, 'lesson-silver', 1, 8, 'lessons', NULL, '2016-01-27 05:50:00', '2016-01-27 00:50:00'),
(112614, 'lesson-bronze', 1, 9, 'lessons', NULL, '2016-01-27 05:51:08', '2016-01-27 00:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `permalink_stats`
--



INSERT INTO `permalink_stats` (`id`, `permalink_id`, `site_id`, `user_id`, `ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(817298, 112606, 1, 1, '127.0.0.1', '2016-01-27 01:11:48', '2016-01-27 06:11:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--


--
-- Dumping data for table `seo_settings`
--

INSERT INTO `seo_settings` (`id`, `site_id`, `company_id`, `link_type`, `target_id`, `meta_key`, `meta_value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(57182, 1, NULL, '2', 1, 'fb_share_title', 'Lesson Public 1', NULL, '2016-01-27 00:30:11', '2016-01-27 00:30:11'),
(57183, 1, NULL, '2', 2, 'fb_share_title', 'Lesson Public 2', NULL, '2016-01-27 00:31:00', '2016-01-27 00:31:00'),
(57184, 1, NULL, '2', 3, 'fb_share_title', 'Lesson Member 1', NULL, '2016-01-27 00:33:18', '2016-01-27 00:33:18'),
(57185, 1, NULL, '2', 4, 'fb_share_title', 'Lesson Member 2', NULL, '2016-01-27 00:34:10', '2016-01-27 00:34:10'),
(57186, 1, NULL, '2', 5, 'fb_share_title', 'Lesson Private 1', NULL, '2016-01-27 00:35:29', '2016-01-27 00:35:29'),
(57187, 1, NULL, '2', 6, 'fb_share_title', 'Lesson Private 2', NULL, '2016-01-27 00:37:07', '2016-01-27 00:37:07'),
(57188, 1, NULL, '2', 7, 'fb_share_title', 'Lesson Gold', NULL, '2016-01-27 00:49:24', '2016-01-27 00:49:24'),
(57189, 1, NULL, '2', 8, 'fb_share_title', 'Lesson Silver', NULL, '2016-01-27 00:50:00', '2016-01-27 00:50:00'),
(57190, 1, NULL, '2', 9, 'fb_share_title', 'Lesson Bronze', NULL, '2016-01-27 00:51:08', '2016-01-27 00:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `shared_grants`
--



INSERT INTO `sites` (`id`, `subdomain`, `domain`, `name`, `template_id`, `user_id`, `domain_mask`, `total_members`, `total_lessons`, `total_revenue`, `stripe_user_id`, `stripe_access_token`, `stripe_integrated`, `type`, `company_id`, `facebook_secret_key`, `facebook_app_id`, `deleted_at`, `created_at`, `updated_at`, `cloneable`, `clone_id`, `syllabus_format`, `show_syllabus_toggle`, `is_completed`, `completed_nodes`, `progress`, `intention`, `welcome_content`, `locked`, `hash`) VALUES
(6192, 'sm', NULL, 'Smart Member', 0, 1, NULL, 1, 0, 0, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, '2016-01-27 05:19:47', '2016-01-27 00:22:53', 0, 0, 'list', 1, 0, NULL, 0, 0, NULL, 0, '7f4e69fd1c8078158efd6244387c85f8'),
(1, 'developer', '', 'Developer', 0, 1, NULL, 13, 9, 0, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, '2016-01-27 00:27:20', '2016-01-27 01:17:29', 0, 0, 'list', 1, 0, NULL, 0, 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sites_ads`
--



INSERT INTO `sites_roles` (`id`, `type`, `site_id`, `user_id`, `access_level_id`, `expired_at`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1, 'member', 6192, 1, 1753, NULL, NULL, '2016-01-27 00:22:53', '2016-01-27 00:22:53'),
(301079, 'owner', 1, 1, NULL, NULL, NULL, '2016-01-27 00:27:20', '2016-01-27 00:27:20'),
(301080, 'member', 1, 139458, NULL, NULL, NULL, '2016-01-27 00:58:47', '2016-01-27 00:58:47'),
(301081, 'member', 1, 139459, NULL, NULL, NULL, '2016-01-27 00:58:50', '2016-01-27 00:58:50'),
(301082, 'member', 1, 139460, NULL, NULL, NULL, '2016-01-27 00:58:52', '2016-01-27 00:58:52'),
(301083, 'member', 1, 139461, NULL, NULL, NULL, '2016-01-27 00:58:53', '2016-01-27 00:58:53'),
(301084, 'member', 1, 139462, 2, '0000-00-00 00:00:00', NULL, '2016-01-27 00:58:55', '2016-01-27 00:58:55'),
(301085, 'member', 1, 139463, 2, '0000-00-00 00:00:00', NULL, '2016-01-27 00:58:56', '2016-01-27 00:58:56'),
(301086, 'member', 1, 139464, 2, '0000-00-00 00:00:00', NULL, '2016-01-27 00:58:58', '2016-01-27 00:58:58'),
(301087, 'member', 1, 139465, 1, '0000-00-00 00:00:00', NULL, '2016-01-27 00:58:59', '2016-01-27 00:58:59'),
(301088, 'member', 1, 139466, 1, '0000-00-00 00:00:00', NULL, '2016-01-27 00:59:01', '2016-01-27 00:59:01'),
(301089, 'member', 1, 139467, 1, '0000-00-00 00:00:00', NULL, '2016-01-27 00:59:02', '2016-01-27 00:59:02'),
(301090, 'member', 1, 139468, 2, '0000-00-00 00:00:00', NULL, '2016-01-27 00:59:04', '2016-01-27 00:59:04'),
(301091, 'member', 1, 139468, 3, '0000-00-00 00:00:00', NULL, '2016-01-27 00:59:04', '2016-01-27 00:59:04'),
(301092, 'member', 1, 139469, 2, '0000-00-00 00:00:00', NULL, '2016-01-27 00:59:05', '2016-01-27 00:59:05'),
(301093, 'member', 1, 139469, 3, '0000-00-00 00:00:00', NULL, '2016-01-27 00:59:05', '2016-01-27 00:59:05'),
(301094, 'member', 1, 139460, 3, NULL, NULL, '2016-01-27 01:00:00', '2016-01-27 01:00:00'),
(301095, 'member', 1, 139461, 3, NULL, NULL, '2016-01-27 01:00:13', '2016-01-27 01:00:13');

-- --------------------------------------------------------


INSERT INTO `site_meta_data` (`id`, `site_id`, `company_id`, `data_type`, `key`, `value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1996484, 1, NULL, 0, 'default_syllabus_closed', '0', NULL, '2016-01-27 01:17:29', '2016-01-27 01:17:29'),
(1996485, 1, NULL, 0, 'thankyou_use_custom', '0', NULL, '2016-01-27 01:17:29', '2016-01-27 01:17:29'),
(1996486, 1, NULL, 0, 'welcome_email_subject', 'Welcome to %site_name%', NULL, '2016-01-27 01:17:29', '2016-01-27 01:17:29'),
(1996487, 1, NULL, 0, 'welcome_email_content', '<h2 style="color:#2ab27b;line-height:30px;margin-bottom:12px;margin:0 0 12px">You''re in!</h2><p style="font-size:18px;line-height:24px;margin:0 0 16px;">You''re now a member at <strong>%site_name%</strong> - welcome!</p><p style="font-size:20px;line-height:26px;margin:0 0 16px"><strong>Ready to login?</strong> Below you''ll find your login details and a link to get started.</p><hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">%login_details% <br>', NULL, '2016-01-27 01:17:29', '2016-01-27 01:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `site_notices`
--



INSERT INTO `site_notices` (`id`, `site_id`, `title`, `content`, `deleted_at`, `created_at`, `updated_at`, `end_date`, `on`, `start_date`, `type`) VALUES
(15078, 1, 'lesson alert', 'Lesson Public 1', NULL, '2016-01-27 00:30:11', '2016-01-27 00:30:11', '2016-01-28 05:30:11', 0, '2016-01-27 05:30:11', 'lesson'),
(15079, 1, 'lesson alert', 'Lesson Member 1', NULL, '2016-01-27 00:33:18', '2016-01-27 00:33:18', '2016-01-28 05:33:18', 0, '2016-01-27 05:33:18', 'lesson'),
(15080, 1, 'lesson alert', 'Lesson Member 2', NULL, '2016-01-27 00:34:10', '2016-01-27 00:34:10', '2016-01-28 05:34:10', 0, '2016-01-27 05:34:10', 'lesson'),
(15081, 1, 'lesson alert', 'Lesson Gold', NULL, '2016-01-27 00:49:24', '2016-01-27 00:49:24', '2016-01-28 05:49:24', 0, '2016-01-27 05:49:24', 'lesson'),
(15082, 1, 'lesson alert', 'Lesson Silver', NULL, '2016-01-27 00:50:00', '2016-01-27 00:50:00', '2016-01-28 05:50:00', 0, '2016-01-27 05:50:00', 'lesson'),
(15083, 1, 'lesson alert', 'Lesson Bronze', NULL, '2016-01-27 00:51:08', '2016-01-27 00:51:08', '2016-01-28 05:51:08', 0, '2016-01-27 05:51:08', 'lesson');

-- --------------------------------------------------------




INSERT INTO `wizards` (`id`, `company_id`, `site_id`, `slug`, `completed_nodes`, `options`, `is_completed`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2099, 0, 1, 'account_wizard', 'create_new_site', NULL, 0, NULL, '2016-01-27 00:27:20', '2016-01-27 00:27:20');

