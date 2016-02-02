--
-- Table structure for table `access_grants`
--

CREATE TABLE IF NOT EXISTS `access_grants` (
  `id` bigint(22) unsigned NOT NULL,
  `access_level_id` bigint(22) NOT NULL,
  `grant_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=4168 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `access_grants`
--


--
-- Table structure for table `access_levels`
--

CREATE TABLE IF NOT EXISTS `access_levels` (
  `id` bigint(22) unsigned NOT NULL,
  `site_id` bigint(22) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `information_url` text NOT NULL,
  `redirect_url` text NOT NULL,
  `product_id` varchar(255) DEFAULT NULL,
  `cb_product_id` varchar(25) DEFAULT NULL,
  `wso_product_id` varchar(25) DEFAULT NULL,
  `zaxaa_product_id` varchar(25) DEFAULT NULL,
  `jvzoo_button` text,
  `price` double(10,2) DEFAULT NULL,
  `currency` varchar(6) NOT NULL,
  `payment_interval` varchar(255) DEFAULT 'one_time',
  `stripe_plan_id` varchar(255) DEFAULT NULL,
  `stripe_integration` bigint(22) DEFAULT '0',
  `paypal_integration` bigint(22) DEFAULT '0',
  `hash` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `facebook_group_id` bigint(22) unsigned DEFAULT NULL,
  `expiration_period` int(11) DEFAULT NULL,
  `hide_unowned_content` tinyint(1) DEFAULT '0',
  `trial_amount` int(11) DEFAULT NULL,
  `trial_duration` int(11) DEFAULT NULL,
  `trial_interval` varchar(10) DEFAULT NULL,
  `webinar_url` varchar(255) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3288 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `access_level_shared_keys` (
  `id` bigint(20) NOT NULL,
  `key` varchar(255) NOT NULL,
  `access_level_id` bigint(20) NOT NULL,
  `originate_site_id` bigint(20) NOT NULL,
  `destination_site_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_passes`
--

CREATE TABLE IF NOT EXISTS `access_passes` (
  `id` bigint(22) unsigned NOT NULL,
  `site_id` bigint(22) NOT NULL,
  `access_level_id` bigint(22) NOT NULL,
  `user_id` bigint(22) NOT NULL,
  `expired_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subscription_id` varchar(255) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=72755 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_payment_methods`
--

CREATE TABLE IF NOT EXISTS `access_payment_methods` (
  `id` bigint(22) unsigned NOT NULL,
  `access_level_id` bigint(22) NOT NULL,
  `payment_method_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=1625 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affcontests`
--

CREATE TABLE IF NOT EXISTS `affcontests` (
  `id` bigint(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `site_id` bigint(20) NOT NULL,
  `title` text NOT NULL,
  `featured_image` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `refresh_type` varchar(100) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `last_refreshed` datetime DEFAULT NULL,
  `content` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `permalink` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affcontest_sites`
--

CREATE TABLE IF NOT EXISTS `affcontest_sites` (
  `id` bigint(11) NOT NULL,
  `contest_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affiliates`
--

CREATE TABLE IF NOT EXISTS `affiliates` (
  `id` int(10) unsigned NOT NULL,
  `site_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `affiliate_request_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_country` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `admin_note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `past_sales` int(11) NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `featured_image` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `original` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=456751 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_jvpage`
--

CREATE TABLE IF NOT EXISTS `affiliate_jvpage` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `embed_content` text NOT NULL,
  `subscribe_button_text` text,
  `redirect_url` text NOT NULL,
  `thankyou_note` text,
  `email_list_id` int(11) NOT NULL,
  `show_thankyou_note` tinyint(4) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscribe_button_color` text
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_types`
--

CREATE TABLE IF NOT EXISTS `affiliate_types` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affleaderboards`
--

CREATE TABLE IF NOT EXISTS `affleaderboards` (
  `id` bigint(11) NOT NULL,
  `contest_id` int(11) DEFAULT NULL,
  `affiliate_id` int(11) DEFAULT NULL,
  `affiliate_name` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=104801 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affteamledger`
--

CREATE TABLE IF NOT EXISTS `affteamledger` (
  `id` int(10) unsigned NOT NULL,
  `team_id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `affteams`
--

CREATE TABLE IF NOT EXISTS `affteams` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `app_configurations`
--

CREATE TABLE IF NOT EXISTS `app_configurations` (
  `id` bigint(22) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `site_id` bigint(22) DEFAULT NULL,
  `company_id` bigint(22) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `connected_account_id` bigint(22) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `auth_type` varchar(255) NOT NULL,
  `username` text,
  `password` text,
  `access_token` text,
  `remote_id` text,
  `expired_at` datetime DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3281 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_bpages`
--

CREATE TABLE IF NOT EXISTS `bridge_bpages` (
  `id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `name` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `permalink` varchar(255) NOT NULL,
  `template_id` bigint(11) NOT NULL,
  `content` text NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `featured_image` text NOT NULL,
  `meta` text NOT NULL,
  `type_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=3122 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_media_items`
--

CREATE TABLE IF NOT EXISTS `bridge_media_items` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `aws_key` text NOT NULL,
  `type` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=3058 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_permalinks`
--

CREATE TABLE IF NOT EXISTS `bridge_permalinks` (
  `id` bigint(11) NOT NULL,
  `user_id` int(20) NOT NULL,
  `url_slug` varchar(255) NOT NULL,
  `target_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=1940 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_seo_settings`
--

CREATE TABLE IF NOT EXISTS `bridge_seo_settings` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=5351 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_templates`
--

CREATE TABLE IF NOT EXISTS `bridge_templates` (
  `id` bigint(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `folder_slug` varchar(100) NOT NULL,
  `icon` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_types`
--

CREATE TABLE IF NOT EXISTS `bridge_types` (
  `id` bigint(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `folder_slug` varchar(100) NOT NULL,
  `icon` text NOT NULL,
  `description` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_user_options`
--

CREATE TABLE IF NOT EXISTS `bridge_user_options` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=233 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bsite_options`
--

CREATE TABLE IF NOT EXISTS `bsite_options` (
  `id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `canned_responses`
--

CREATE TABLE IF NOT EXISTS `canned_responses` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `company_id` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `text` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=252 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clicks`
--

CREATE TABLE IF NOT EXISTS `clicks` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `link_id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5694 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(11) NOT NULL,
  `body` text,
  `parent_id` bigint(11) DEFAULT '0',
  `user_id` bigint(11) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `target_id` bigint(11) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=1467 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(20) NOT NULL,
  `profile_image` text NOT NULL,
  `hash` text,
  `subtitle` varchar(255) DEFAULT NULL,
  `hide_total_downloads` tinyint(1) DEFAULT '0',
  `display_image` text,
  `display_name` varchar(255) DEFAULT NULL,
  `bio` text,
  `hide_total_lessons` tinyint(1) DEFAULT '0',
  `hide_members` tinyint(1) DEFAULT '0',
  `hide_sites` tinyint(1) DEFAULT '0',
  `hide_revenue` tinyint(1) DEFAULT '0',
  `pending_permalink` varchar(255) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `locked` tinyint(1) DEFAULT '0',
  `site_count` int(11) DEFAULT '100',
  `is_completed` tinyint(1) DEFAULT '0',
  `completed_nodes` text,
  `progress` int(11) DEFAULT '1',
  `intention` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=11193 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `company_options`
--

CREATE TABLE IF NOT EXISTS `company_options` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=539693 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `connected_accounts`
--

CREATE TABLE IF NOT EXISTS `connected_accounts` (
  `id` bigint(22) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `site_id` bigint(22) DEFAULT NULL,
  `company_id` bigint(22) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `access_token` text,
  `remote_id` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=401 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `content_stats`
--

CREATE TABLE IF NOT EXISTS `content_stats` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=7511 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `custom_attributes`
--

CREATE TABLE IF NOT EXISTS `custom_attributes` (
  `id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `shown` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `custom_pages`
--

CREATE TABLE IF NOT EXISTS `custom_pages` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `transcript_content` text NOT NULL,
  `transcript_button_text` varchar(50) DEFAULT NULL,
  `audio_file` text NOT NULL,
  `access_level_type` int(11) NOT NULL DEFAULT '1',
  `access_level_id` int(11) NOT NULL DEFAULT '1',
  `discussion_settings_id` int(11) NOT NULL,
  `hide_sidebars` tinyint(1) NOT NULL DEFAULT '0',
  `permalink` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  `show_content_publicly` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=11735 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `directory_listings`
--

CREATE TABLE IF NOT EXISTS `directory_listings` (
  `id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` text,
  `pending_description` text,
  `subtitle` text,
  `pending_subtitle` text,
  `pending_title` varchar(255) NOT NULL,
  `image` text,
  `pending_image` text,
  `pricing` text,
  `pending_pricing` text,
  `pending_updates` tinyint(1) DEFAULT '1',
  `is_approved` tinyint(1) DEFAULT '0',
  `total_lessons` int(11) DEFAULT NULL,
  `total_downloads` int(11) DEFAULT NULL,
  `total_revenue` int(11) DEFAULT NULL,
  `permalink` text,
  `is_free` tinyint(1) DEFAULT '0',
  `hide_members` tinyint(1) DEFAULT '0',
  `hide_revenue` tinyint(1) DEFAULT '0',
  `hide_lessons` tinyint(4) NOT NULL DEFAULT '0',
  `hide_downloads` tinyint(4) NOT NULL DEFAULT '0',
  `expired_at` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_settings`
--

CREATE TABLE IF NOT EXISTS `discussion_settings` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `show_comments` tinyint(1) NOT NULL DEFAULT '0',
  `newest_comments_first` tinyint(1) NOT NULL DEFAULT '0',
  `close_to_new_comments` tinyint(1) NOT NULL DEFAULT '0',
  `allow_replies` tinyint(1) NOT NULL DEFAULT '0',
  `public_comments` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=52951 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `discussion_settings`
--

CREATE TABLE IF NOT EXISTS `downloads_history` (
  `id` bigint(11) NOT NULL,
  `download_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=21694 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `download_center`
--

CREATE TABLE IF NOT EXISTS `download_center` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `creator_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` longtext NOT NULL,
  `download_button_text` varchar(255) NOT NULL,
  `download_link` varchar(255) NOT NULL,
  `media_item_id` int(11) NOT NULL,
  `access_level_type` int(11) NOT NULL DEFAULT '1',
  `access_level_id` int(11) NOT NULL DEFAULT '1',
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `permalink` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_published_date` datetime DEFAULT NULL,
  `published_date` datetime NOT NULL,
  `preview_dripfeed` tinyint(4) NOT NULL DEFAULT '0',
  `preview_schedule` tinyint(4) NOT NULL DEFAULT '0',
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=15502 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `drafts`
--

CREATE TABLE IF NOT EXISTS `drafts` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4224 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dripfeed`
--

CREATE TABLE IF NOT EXISTS `dripfeed` (
  `id` bigint(20) NOT NULL,
  `target_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `interval` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=269 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `element_types`
--

CREATE TABLE IF NOT EXISTS `element_types` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `mail_name` text NOT NULL,
  `mail_sending_address` text NOT NULL,
  `mail_reply_address` text NOT NULL,
  `mail_signature` text NOT NULL,
  `sendgrid_integration` bigint(22) DEFAULT '0',
  `recipient_type` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mail_test_default` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1054 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `emails_queue`
--

CREATE TABLE IF NOT EXISTS `emails_queue` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `list_type` varchar(50) DEFAULT NULL,
  `email_recipient_id` bigint(11) DEFAULT NULL,
  `send_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=301654 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=236 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder_email`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder_email` (
  `id` bigint(11) NOT NULL,
  `autoresponder_id` int(11) DEFAULT NULL,
  `email_id` varchar(255) NOT NULL,
  `delay` int(11) DEFAULT '0',
  `unit` tinyint(4) DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort_order` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=4773 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder_list`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder_list` (
  `id` bigint(11) NOT NULL,
  `autoresponder_id` int(11) DEFAULT NULL,
  `list_id` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=178 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_history`
--

CREATE TABLE IF NOT EXISTS `email_history` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `subscriber_id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `list_type` varchar(50) DEFAULT NULL,
  `auto_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=275945 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_jobs`
--

CREATE TABLE IF NOT EXISTS `email_jobs` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `send_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=810 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_listledger`
--

CREATE TABLE IF NOT EXISTS `email_listledger` (
  `id` int(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `list_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=188704 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_lists`
--

CREATE TABLE IF NOT EXISTS `email_lists` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_subscribers` int(11) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `list_type` varchar(255) NOT NULL DEFAULT 'user',
  `segment_query` text
) ENGINE=MyISAM AUTO_INCREMENT=1715 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipient`
--

CREATE TABLE IF NOT EXISTS `email_recipient` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=419 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipients`
--

CREATE TABLE IF NOT EXISTS `email_recipients` (
  `id` bigint(11) NOT NULL,
  `email_id` bigint(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `order` bigint(11) NOT NULL DEFAULT '1',
  `subject` text,
  `intro` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=775 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipients_queue`
--

CREATE TABLE IF NOT EXISTS `email_recipients_queue` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_recipient_id` bigint(11) NOT NULL,
  `last_recipient_queued` bigint(11) DEFAULT NULL,
  `email_job_id` bigint(11) DEFAULT NULL,
  `total_queued` bigint(11) DEFAULT NULL,
  `total_recipients` bigint(11) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `send_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=686 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE IF NOT EXISTS `email_settings` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `site_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `username` text,
  `password` text,
  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sending_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `replyto_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_signature` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `test_email_address` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=48795 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_subscribers`
--

CREATE TABLE IF NOT EXISTS `email_subscribers` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=136510 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) unsigned NOT NULL,
  `site_id` bigint(20) DEFAULT NULL,
  `event_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `count` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=14380 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `events`
--

--
-- Table structure for table `event_metadata`
--

CREATE TABLE IF NOT EXISTS `event_metadata` (
  `id` int(10) unsigned NOT NULL,
  `event_id` bigint(20) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=221331 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_metadata`
--

CREATE TABLE IF NOT EXISTS `forum_categories` (
  `id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `parent_id` bigint(20) unsigned DEFAULT '0',
  `site_id` bigint(20) unsigned NOT NULL,
  `access_level_id` bigint(20) unsigned NOT NULL,
  `access_level_type` int(11) DEFAULT '1',
  `allow_content` tinyint(1) DEFAULT '1',
  `total_replies` int(11) DEFAULT '0',
  `total_topics` int(11) DEFAULT '0',
  `icon` text,
  `permalink` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE IF NOT EXISTS `forum_replies` (
  `id` bigint(20) unsigned NOT NULL,
  `content` text,
  `topic_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=329 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `category_id` bigint(20) unsigned NOT NULL,
  `total_replies` int(11) DEFAULT '0',
  `total_views` int(11) DEFAULT '0',
  `total_likes` int(11) DEFAULT '0',
  `status` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `allow_content` tinyint(1) DEFAULT '1',
  `permalink` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=209 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `group_accesslevel`
--

CREATE TABLE IF NOT EXISTS `group_accesslevel` (
  `id` bigint(20) NOT NULL,
  `facebook_id` bigint(20) NOT NULL,
  `access_level_id` bigint(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `history_logins`
--

CREATE TABLE IF NOT EXISTS `history_logins` (
  `id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `browser` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `imports_queue`
--

CREATE TABLE IF NOT EXISTS `imports_queue` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `access_levels` varchar(255) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `expiry` datetime NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=129853 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `imports_queue`
--

CREATE TABLE IF NOT EXISTS `import_jobs` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `total_count` bigint(20) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1276 DEFAULT CHARSET=utf8;


--
-- Table structure for table `integration_meta`
--

CREATE TABLE IF NOT EXISTS `integration_meta` (
  `id` int(10) unsigned NOT NULL,
  `integration_id` bigint(20) unsigned NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE IF NOT EXISTS `lessons` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `author_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `next_lesson` int(11) NOT NULL,
  `prev_lesson` int(11) NOT NULL,
  `presenter` text,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `transcript_content` text NOT NULL,
  `transcript_button_text` varchar(50) DEFAULT 'Transcript',
  `audio_file` text NOT NULL,
  `access_level_type` int(11) NOT NULL DEFAULT '1',
  `access_level_id` int(11) NOT NULL DEFAULT '1',
  `discussion_settings_id` int(11) NOT NULL,
  `permalink` text NOT NULL,
  `remote_id` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_published_date` datetime DEFAULT NULL,
  `published_date` datetime DEFAULT NULL,
  `preview_dripfeed` tinyint(4) NOT NULL DEFAULT '0',
  `preview_schedule` tinyint(4) NOT NULL DEFAULT '0',
  `transcript_content_public` tinyint(1) DEFAULT '0',
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  `show_content_publicly` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=57535 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lessons`
--

--
-- Table structure for table `linked_accounts`
--

CREATE TABLE IF NOT EXISTS `linked_accounts` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `linked_email` varchar(255) NOT NULL,
  `linked_user_id` bigint(20) NOT NULL,
  `email_only_link` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_hash` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `claimed` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=764 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `url` text NOT NULL,
  `hash` varchar(35) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=398 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `livecasts`
--

CREATE TABLE IF NOT EXISTS `livecasts` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `access_level_type` int(11) NOT NULL DEFAULT '1',
  `access_level_id` int(11) NOT NULL DEFAULT '1',
  `permalink` text NOT NULL,
  `discussion_settings_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_published_date` datetime DEFAULT NULL,
  `published_date` datetime NOT NULL,
  `preview_dripfeed` tinyint(4) NOT NULL DEFAULT '0',
  `preview_schedule` tinyint(4) NOT NULL DEFAULT '0',
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  `show_content_publicly` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=256 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media_files`
--

CREATE TABLE IF NOT EXISTS `media_files` (
  `id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `source` text,
  `type` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=10968 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `media_items`
--

CREATE TABLE IF NOT EXISTS `media_items` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `aws_key` text NOT NULL,
  `type` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=20483 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `member_meta`
--

CREATE TABLE IF NOT EXISTS `member_meta` (
  `id` int(10) unsigned NOT NULL,
  `member_id` bigint(20) NOT NULL,
  `custom_attribute_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `meta_data_types`
--

CREATE TABLE IF NOT EXISTS `meta_data_types` (
  `id` int(10) unsigned NOT NULL,
  `type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type_value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  `title` text NOT NULL,
  `note` text NOT NULL,
  `access_level` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=16425 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `opens`
--

CREATE TABLE IF NOT EXISTS `opens` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=56291 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permalinks`
--

CREATE TABLE IF NOT EXISTS `permalinks` (
  `id` bigint(20) unsigned NOT NULL,
  `permalink` text NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `target_id` bigint(20) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=112615 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permalinks`
--

CREATE TABLE IF NOT EXISTS `permalink_stats` (
  `id` bigint(20) NOT NULL,
  `permalink_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=817299 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permalink_stats`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `access_level_type` int(11) NOT NULL DEFAULT '1',
  `access_level_id` int(11) NOT NULL DEFAULT '1',
  `permalink` text NOT NULL,
  `discussion_settings_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_published_date` datetime DEFAULT NULL,
  `published_date` datetime NOT NULL,
  `preview_schedule` tinyint(4) NOT NULL DEFAULT '0',
  `transcript_content_public` tinyint(1) DEFAULT '0',
  `transcript_content` text NOT NULL,
  `transcript_button_text` varchar(50) DEFAULT 'Transcript',
  `audio_file` text NOT NULL,
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  `show_content_publicly` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=7104 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts_categories`
--

CREATE TABLE IF NOT EXISTS `posts_categories` (
  `id` bigint(11) NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=861 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts_tags`
--

CREATE TABLE IF NOT EXISTS `posts_tags` (
  `id` bigint(11) NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=905 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `remote_logins`
--

CREATE TABLE IF NOT EXISTS `remote_logins` (
  `id` int(11) NOT NULL,
  `date` int(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remote_id` varchar(40) NOT NULL,
  `source` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8217 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `role_type` int(20) unsigned NOT NULL,
  `total_visits` int(10) unsigned DEFAULT '0',
  `total_lessons` int(10) unsigned DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=140735 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `role_types`
--

CREATE TABLE IF NOT EXISTS `role_types` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `seo_settings`
--

CREATE TABLE IF NOT EXISTS `seo_settings` (
  `id` int(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `link_type` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=57191 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `shared_grants` (
  `id` bigint(22) unsigned NOT NULL,
  `access_level_id` bigint(22) NOT NULL,
  `grant_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(10) unsigned NOT NULL,
  `subdomain` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `template_id` bigint(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `domain_mask` varchar(255) DEFAULT NULL,
  `total_members` int(11) DEFAULT '0',
  `total_lessons` int(11) DEFAULT '0',
  `total_revenue` int(11) DEFAULT '0',
  `stripe_user_id` varchar(255) DEFAULT NULL,
  `stripe_access_token` text,
  `stripe_integrated` tinyint(1) DEFAULT '0',
  `type` int(11) NOT NULL,
  `company_id` int(10) NOT NULL,
  `facebook_secret_key` varchar(255) DEFAULT NULL,
  `facebook_app_id` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cloneable` tinyint(1) NOT NULL DEFAULT '0',
  `clone_id` int(11) NOT NULL DEFAULT '0',
  `syllabus_format` varchar(255) NOT NULL DEFAULT 'list',
  `show_syllabus_toggle` tinyint(1) NOT NULL DEFAULT '1',
  `is_completed` tinyint(1) DEFAULT '0',
  `completed_nodes` text,
  `progress` int(11) DEFAULT '0',
  `intention` int(11) DEFAULT '0',
  `welcome_content` text,
  `locked` tinyint(1) DEFAULT '0',
  `hash` varchar(255) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10882 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sites`
--

CREATE TABLE IF NOT EXISTS `sites_ads` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `banner_url` text,
  `banner_image_url` text,
  `open_in_new_tab` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `views` int(11) DEFAULT '0',
  `clicks` int(11) DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  `custom_ad` text,
  `display` tinyint(1) DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=5089 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sites_custom_roles`
--

CREATE TABLE IF NOT EXISTS `sites_custom_roles` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sites_custom_roles_capabilities`
--

CREATE TABLE IF NOT EXISTS `sites_custom_roles_capabilities` (
  `id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `capability` varchar(255) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sites_footer_menu_items`
--

CREATE TABLE IF NOT EXISTS `sites_footer_menu_items` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort_order` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=14157 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sites_menu_items`
--

CREATE TABLE IF NOT EXISTS `sites_menu_items` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `custom_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort_order` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=22750 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sites_roles`
--

CREATE TABLE IF NOT EXISTS `sites_roles` (
  `id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `access_level_id` bigint(20) DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=301096 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sites_roles`
--


--
-- Table structure for table `sites_templates_data`
--

CREATE TABLE IF NOT EXISTS `sites_templates_data` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `element_type_id` int(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `site_meta_data`
--

CREATE TABLE IF NOT EXISTS `site_meta_data` (
  `id` int(10) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `data_type` int(11) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=1996488 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_meta_data`
--

CREATE TABLE IF NOT EXISTS `site_notices` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime DEFAULT NULL,
  `on` tinyint(1) DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT 'admin'
) ENGINE=MyISAM AUTO_INCREMENT=15084 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_notices`
--

CREATE TABLE IF NOT EXISTS `site_notices_seen` (
  `id` bigint(11) NOT NULL,
  `site_notice_id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=1974 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smart_links`
--

CREATE TABLE IF NOT EXISTS `smart_links` (
  `id` bigint(22) unsigned NOT NULL,
  `site_id` bigint(22) NOT NULL,
  `title` text NOT NULL,
  `permalink` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'random',
  `last_url_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=212 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smart_link_urls`
--

CREATE TABLE IF NOT EXISTS `smart_link_urls` (
  `id` bigint(22) unsigned NOT NULL,
  `smart_link_id` bigint(22) NOT NULL,
  `url` text NOT NULL,
  `visits` bigint(22) DEFAULT NULL,
  `weight` bigint(22) DEFAULT NULL,
  `order` bigint(22) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=495 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `special_pages`
--

CREATE TABLE IF NOT EXISTS `special_pages` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `type` text NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `file_url` text,
  `access_level` int(11) NOT NULL,
  `multiple` int(11) DEFAULT NULL,
  `free_item_url` text NOT NULL,
  `free_item_text` text NOT NULL,
  `continue_refund_text` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `use_free_item_url` tinyint(1) DEFAULT NULL,
  `free_item_color` text,
  `continue_refund_color` text
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `speed_blogs`
--

CREATE TABLE IF NOT EXISTS `speed_blogs` (
  `id` bigint(11) NOT NULL,
  `name` text NOT NULL,
  `website_url` text NOT NULL,
  `endpoint_url` text NOT NULL,
  `use_xmlrpc` tinyint(1) NOT NULL DEFAULT '1',
  `username` text NOT NULL,
  `password` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `sbc_hash` varchar(255) DEFAULT NULL,
  `sbc_endpoint` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=3587 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `speed_posts`
--

CREATE TABLE IF NOT EXISTS `speed_posts` (
  `id` bigint(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `wp_post_id` int(11) NOT NULL,
  `post_mode` text NOT NULL,
  `post_title` text NOT NULL,
  `update_count` int(11) NOT NULL,
  `list_items` longtext,
  `type` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=9258 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `support_articles`
--

CREATE TABLE IF NOT EXISTS `support_articles` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `permalink` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `always_show_featured_image` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=6682 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `support_categories`
--

CREATE TABLE IF NOT EXISTS `support_categories` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=287 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `corporate` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `type` varchar(100) NOT NULL DEFAULT 'Normal',
  `category` varchar(100) NOT NULL,
  `priority` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'open',
  `read` tinyint(1) DEFAULT '0',
  `parent_id` bigint(20) unsigned NOT NULL,
  `attachment` text NOT NULL,
  `three_day_sent` tinyint(1) DEFAULT '0',
  `five_day_sent` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_email` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `agent_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rating` varchar(10) DEFAULT NULL,
  `last_replied_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=17131 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_actions`
--

CREATE TABLE IF NOT EXISTS `support_ticket_actions` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `modified_attribute` varchar(255) NOT NULL,
  `old_value` varchar(255) NOT NULL,
  `new_value` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=9237 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `swapspots`
--

CREATE TABLE IF NOT EXISTS `swapspots` (
  `id` bigint(20) NOT NULL,
  `bridge_page_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22706 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_seeds`
--

CREATE TABLE IF NOT EXISTS `table_seeds` (
  `id` int(10) unsigned NOT NULL,
  `seed_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `text` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=327 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tax_assoc`
--

CREATE TABLE IF NOT EXISTS `tax_assoc` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  `taxonomy_id` int(11) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tax_type`
--

CREATE TABLE IF NOT EXISTS `tax_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `team_roles`
--

CREATE TABLE IF NOT EXISTS `team_roles` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `role` int(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=8579 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(10) unsigned NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `template_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `templates_attributes`
--

CREATE TABLE IF NOT EXISTS `templates_attributes` (
  `id` int(10) unsigned NOT NULL,
  `default_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_position` int(11) NOT NULL,
  `element_type_id` int(11) NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_notes`
--

CREATE TABLE IF NOT EXISTS `ticket_notes` (
  `id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `note` text NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=248 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(10) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `source` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(40) NOT NULL,
  `affiliate_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `wso_product_id` varchar(25) DEFAULT NULL,
  `cb_product_id` varchar(25) DEFAULT NULL,
  `zaxaa_product_id` varchar(25) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `price` double(8,2) NOT NULL,
  `association_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=27723 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `unsubfeedback`
--

CREATE TABLE IF NOT EXISTS `unsubfeedback` (
  `id` bigint(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `unsub_reason` text,
  `comment` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `unsubscribers`
--

CREATE TABLE IF NOT EXISTS `unsubscribers` (
  `id` bigint(20) NOT NULL,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=792 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `unsubscribers_segment`
--

CREATE TABLE IF NOT EXISTS `unsubscribers_segment` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `site_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_hash` text NOT NULL,
  `password` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `facebook_user_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_image` text NOT NULL,
  `access_token` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `access_token_expired` datetime NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_token` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vanity_username` varchar(255) DEFAULT NULL,
  `affiliate_id` int(11) DEFAULT NULL,
  `last_logged_in` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `setup_wizard_complete` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=139470 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--
--
-- Table structure for table `user_meta`
--

CREATE TABLE IF NOT EXISTS `user_meta` (
  `id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=6784 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_notes`
--

CREATE TABLE IF NOT EXISTS `user_notes` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `user_id` bigint(11) NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `note` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=22616 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_options`
--

CREATE TABLE IF NOT EXISTS `user_options` (
  `id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text
) ENGINE=MyISAM AUTO_INCREMENT=31059 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `role_type` int(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=124392 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE IF NOT EXISTS `verification_codes` (
  `id` bigint(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `expired_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=4258 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` bigint(11) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `sidebar_id` bigint(11) NOT NULL DEFAULT '1',
  `target_id` bigint(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `sort_order` bigint(11) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=4938 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `widget_meta`
--

CREATE TABLE IF NOT EXISTS `widget_meta` (
  `id` int(10) unsigned NOT NULL,
  `widget_id` bigint(20) NOT NULL,
  `target_id` bigint(11) DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wizards`
--

CREATE TABLE IF NOT EXISTS `wizards` (
  `id` bigint(11) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `slug` varchar(20) NOT NULL,
  `completed_nodes` text,
  `options` text,
  `is_completed` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=2100 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wizards`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_grants`
--
ALTER TABLE `access_grants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_levels`
--
ALTER TABLE `access_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `access_level_shared_keys`
--
ALTER TABLE `access_level_shared_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_passes`
--
ALTER TABLE `access_passes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_access_level_id` (`access_level_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `access_payment_methods`
--
ALTER TABLE `access_payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affcontests`
--
ALTER TABLE `affcontests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `affcontest_sites`
--
ALTER TABLE `affcontest_sites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affiliates`
--
ALTER TABLE `affiliates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affiliate_jvpage`
--
ALTER TABLE `affiliate_jvpage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affiliate_types`
--
ALTER TABLE `affiliate_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affleaderboards`
--
ALTER TABLE `affleaderboards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affteamledger`
--
ALTER TABLE `affteamledger`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `affiliate_team_id` (`team_id`,`affiliate_id`);

--
-- Indexes for table `affteams`
--
ALTER TABLE `affteams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_configurations`
--
ALTER TABLE `app_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `bridge_bpages`
--
ALTER TABLE `bridge_bpages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bridge_media_items`
--
ALTER TABLE `bridge_media_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bridge_permalinks`
--
ALTER TABLE `bridge_permalinks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permalink` (`user_id`,`target_id`),
  ADD UNIQUE KEY `unique_link` (`user_id`,`url_slug`),
  ADD KEY `url_slug` (`url_slug`),
  ADD KEY `target_id` (`target_id`);

--
-- Indexes for table `bridge_seo_settings`
--
ALTER TABLE `bridge_seo_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `meta_key` (`meta_key`);

--
-- Indexes for table `bridge_templates`
--
ALTER TABLE `bridge_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bridge_types`
--
ALTER TABLE `bridge_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bridge_user_options`
--
ALTER TABLE `bridge_user_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_meta` (`user_id`,`meta_key`);

--
-- Indexes for table `bsite_options`
--
ALTER TABLE `bsite_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_meta` (`user_id`,`meta_key`);

--
-- Indexes for table `canned_responses`
--
ALTER TABLE `canned_responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clicks`
--
ALTER TABLE `clicks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_options`
--
ALTER TABLE `company_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `key` (`key`),
  ADD KEY `company_options_id` (`company_id`),
  ADD KEY `company_options_key` (`key`),
  ADD KEY `company_options_deleted` (`deleted_at`);

--
-- Indexes for table `connected_accounts`
--
ALTER TABLE `connected_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `content_stats`
--
ALTER TABLE `content_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_attributes`
--
ALTER TABLE `custom_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `name` (`name`),
  ADD KEY `type` (`type`),
  ADD KEY `archived` (`archived`),
  ADD KEY `shown` (`shown`);

--
-- Indexes for table `custom_pages`
--
ALTER TABLE `custom_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `directory_listings`
--
ALTER TABLE `directory_listings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discussion_settings`
--
ALTER TABLE `discussion_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `downloads_history`
--
ALTER TABLE `downloads_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `download_center`
--
ALTER TABLE `download_center`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drafts`
--
ALTER TABLE `drafts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dripfeed`
--
ALTER TABLE `dripfeed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `element_types`
--
ALTER TABLE `element_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails_queue`
--
ALTER TABLE `emails_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriber_id` (`subscriber_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `list_type` (`list_type`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `email_id` (`email_id`,`subscriber_id`,`job_id`,`list_type`);

--
-- Indexes for table `email_autoresponder`
--
ALTER TABLE `email_autoresponder`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_autoresponder_email`
--
ALTER TABLE `email_autoresponder_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_autoresponder_list`
--
ALTER TABLE `email_autoresponder_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_history`
--
ALTER TABLE `email_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_jobs`
--
ALTER TABLE `email_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `email_id` (`email_id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `email_listledger`
--
ALTER TABLE `email_listledger`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscriber_list_pair` (`list_id`,`subscriber_id`),
  ADD KEY `subscriber_id` (`subscriber_id`);

--
-- Indexes for table `email_lists`
--
ALTER TABLE `email_lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `email_recipient`
--
ALTER TABLE `email_recipient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_recipients`
--
ALTER TABLE `email_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient` (`recipient`),
  ADD KEY `type` (`type`),
  ADD KEY `email_id` (`email_id`);

--
-- Indexes for table `email_recipients_queue`
--
ALTER TABLE `email_recipients_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_recipient_id` (`email_recipient_id`),
  ADD KEY `last_recipient_queued` (`last_recipient_queued`),
  ADD KEY `email_job_id` (`email_job_id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_subscribers`
--
ALTER TABLE `email_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_email` (`email`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `event_name` (`event_name`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `event_metadata`
--
ALTER TABLE `event_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_accesslevel`
--
ALTER TABLE `group_accesslevel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_logins`
--
ALTER TABLE `history_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `imports_queue`
--
ALTER TABLE `imports_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `import_jobs`
--
ALTER TABLE `import_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `integration_meta`
--
ALTER TABLE `integration_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `lessons_site_id` (`site_id`);

--
-- Indexes for table `linked_accounts`
--
ALTER TABLE `linked_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `livecasts`
--
ALTER TABLE `livecasts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media_files`
--
ALTER TABLE `media_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media_items`
--
ALTER TABLE `media_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_meta`
--
ALTER TABLE `member_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `custom_attribute_id` (`custom_attribute_id`);

--
-- Indexes for table `meta_data_types`
--
ALTER TABLE `meta_data_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opens`
--
ALTER TABLE `opens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `permalinks`
--
ALTER TABLE `permalinks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `permalink_stats`
--
ALTER TABLE `permalink_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts_categories`
--
ALTER TABLE `posts_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts_tags`
--
ALTER TABLE `posts_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remote_logins`
--
ALTER TABLE `remote_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `remote_source` (`remote_id`,`source`),
  ADD KEY `remote_id` (`remote_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `index_user_id` (`user_id`);

--
-- Indexes for table `role_types`
--
ALTER TABLE `role_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_settings`
--
ALTER TABLE `seo_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared_grants`
--
ALTER TABLE `shared_grants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sites`
--
ALTER TABLE `sites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_subdomain_unique` (`subdomain`);

--
-- Indexes for table `sites_ads`
--
ALTER TABLE `sites_ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sites_custom_roles`
--
ALTER TABLE `sites_custom_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sites_custom_roles_capabilities`
--
ALTER TABLE `sites_custom_roles_capabilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sites_footer_menu_items`
--
ALTER TABLE `sites_footer_menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sites_menu_items`
--
ALTER TABLE `sites_menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sites_roles`
--
ALTER TABLE `sites_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sites_templates_data`
--
ALTER TABLE `sites_templates_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_meta_data`
--
ALTER TABLE `site_meta_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `site_notices`
--
ALTER TABLE `site_notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_notices_seen`
--
ALTER TABLE `site_notices_seen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smart_links`
--
ALTER TABLE `smart_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `permalink` (`permalink`);

--
-- Indexes for table `smart_link_urls`
--
ALTER TABLE `smart_link_urls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `smart_link_id` (`smart_link_id`);

--
-- Indexes for table `special_pages`
--
ALTER TABLE `special_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `speed_blogs`
--
ALTER TABLE `speed_blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `speed_posts`
--
ALTER TABLE `speed_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`user_id`,`blog_id`,`wp_post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `blog_id` (`blog_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `support_articles`
--
ALTER TABLE `support_articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_categories`
--
ALTER TABLE `support_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_ticket_actions`
--
ALTER TABLE `support_ticket_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `swapspots`
--
ALTER TABLE `swapspots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_seeds`
--
ALTER TABLE `table_seeds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_assoc`
--
ALTER TABLE `tax_assoc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_type`
--
ALTER TABLE `tax_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_roles`
--
ALTER TABLE `team_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `templates_attributes`
--
ALTER TABLE `templates_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_notes`
--
ALTER TABLE `ticket_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_product_id` (`product_id`);

--
-- Indexes for table `unsubfeedback`
--
ALTER TABLE `unsubfeedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unsubscribers`
--
ALTER TABLE `unsubscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unsubscribers_segment`
--
ALTER TABLE `unsubscribers_segment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_email` (`email`);

--
-- Indexes for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `user_notes`
--
ALTER TABLE `user_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_options`
--
ALTER TABLE `user_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_roles_role_id_index` (`role_id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `widgets`
--
ALTER TABLE `widgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `sidebar_id` (`sidebar_id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `widget_meta`
--
ALTER TABLE `widget_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `widget_id` (`widget_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `wizards`
--
ALTER TABLE `wizards`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_grants`
--
ALTER TABLE `access_grants`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4168;
--
-- AUTO_INCREMENT for table `access_levels`
--
ALTER TABLE `access_levels`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3288;
--
-- AUTO_INCREMENT for table `access_level_shared_keys`
--
ALTER TABLE `access_level_shared_keys`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `access_passes`
--
ALTER TABLE `access_passes`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=72755;
--
-- AUTO_INCREMENT for table `access_payment_methods`
--
ALTER TABLE `access_payment_methods`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1625;
--
-- AUTO_INCREMENT for table `affcontests`
--
ALTER TABLE `affcontests`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `affcontest_sites`
--
ALTER TABLE `affcontest_sites`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=69;
--
-- AUTO_INCREMENT for table `affiliates`
--
ALTER TABLE `affiliates`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=456751;
--
-- AUTO_INCREMENT for table `affiliate_jvpage`
--
ALTER TABLE `affiliate_jvpage`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `affiliate_types`
--
ALTER TABLE `affiliate_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `affleaderboards`
--
ALTER TABLE `affleaderboards`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=104801;
--
-- AUTO_INCREMENT for table `affteamledger`
--
ALTER TABLE `affteamledger`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `affteams`
--
ALTER TABLE `affteams`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT for table `app_configurations`
--
ALTER TABLE `app_configurations`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3281;
--
-- AUTO_INCREMENT for table `bridge_bpages`
--
ALTER TABLE `bridge_bpages`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3122;
--
-- AUTO_INCREMENT for table `bridge_media_items`
--
ALTER TABLE `bridge_media_items`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3058;
--
-- AUTO_INCREMENT for table `bridge_permalinks`
--
ALTER TABLE `bridge_permalinks`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1940;
--
-- AUTO_INCREMENT for table `bridge_seo_settings`
--
ALTER TABLE `bridge_seo_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5351;
--
-- AUTO_INCREMENT for table `bridge_templates`
--
ALTER TABLE `bridge_templates`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `bridge_types`
--
ALTER TABLE `bridge_types`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `bridge_user_options`
--
ALTER TABLE `bridge_user_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=233;
--
-- AUTO_INCREMENT for table `bsite_options`
--
ALTER TABLE `bsite_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `canned_responses`
--
ALTER TABLE `canned_responses`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=252;
--
-- AUTO_INCREMENT for table `clicks`
--
ALTER TABLE `clicks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5694;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1467;
--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11193;
--
-- AUTO_INCREMENT for table `company_options`
--
ALTER TABLE `company_options`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=539693;
--
-- AUTO_INCREMENT for table `connected_accounts`
--
ALTER TABLE `connected_accounts`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=401;
--
-- AUTO_INCREMENT for table `content_stats`
--
ALTER TABLE `content_stats`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7511;
--
-- AUTO_INCREMENT for table `custom_attributes`
--
ALTER TABLE `custom_attributes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `custom_pages`
--
ALTER TABLE `custom_pages`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11735;
--
-- AUTO_INCREMENT for table `directory_listings`
--
ALTER TABLE `directory_listings`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=198;
--
-- AUTO_INCREMENT for table `discussion_settings`
--
ALTER TABLE `discussion_settings`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52951;
--
-- AUTO_INCREMENT for table `downloads_history`
--
ALTER TABLE `downloads_history`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21694;
--
-- AUTO_INCREMENT for table `download_center`
--
ALTER TABLE `download_center`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15502;
--
-- AUTO_INCREMENT for table `drafts`
--
ALTER TABLE `drafts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4224;
--
-- AUTO_INCREMENT for table `dripfeed`
--
ALTER TABLE `dripfeed`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=269;
--
-- AUTO_INCREMENT for table `element_types`
--
ALTER TABLE `element_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1054;
--
-- AUTO_INCREMENT for table `emails_queue`
--
ALTER TABLE `emails_queue`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=301654;
--
-- AUTO_INCREMENT for table `email_autoresponder`
--
ALTER TABLE `email_autoresponder`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=236;
--
-- AUTO_INCREMENT for table `email_autoresponder_email`
--
ALTER TABLE `email_autoresponder_email`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4773;
--
-- AUTO_INCREMENT for table `email_autoresponder_list`
--
ALTER TABLE `email_autoresponder_list`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=178;
--
-- AUTO_INCREMENT for table `email_history`
--
ALTER TABLE `email_history`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=275945;
--
-- AUTO_INCREMENT for table `email_jobs`
--
ALTER TABLE `email_jobs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=810;
--
-- AUTO_INCREMENT for table `email_listledger`
--
ALTER TABLE `email_listledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=188704;
--
-- AUTO_INCREMENT for table `email_lists`
--
ALTER TABLE `email_lists`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1715;
--
-- AUTO_INCREMENT for table `email_recipient`
--
ALTER TABLE `email_recipient`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=419;
--
-- AUTO_INCREMENT for table `email_recipients`
--
ALTER TABLE `email_recipients`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=775;
--
-- AUTO_INCREMENT for table `email_recipients_queue`
--
ALTER TABLE `email_recipients_queue`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=686;
--
-- AUTO_INCREMENT for table `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48795;
--
-- AUTO_INCREMENT for table `email_subscribers`
--
ALTER TABLE `email_subscribers`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=136510;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14380;
--
-- AUTO_INCREMENT for table `event_metadata`
--
ALTER TABLE `event_metadata`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=221331;
--
-- AUTO_INCREMENT for table `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=125;
--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=329;
--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=209;
--
-- AUTO_INCREMENT for table `group_accesslevel`
--
ALTER TABLE `group_accesslevel`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `history_logins`
--
ALTER TABLE `history_logins`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `imports_queue`
--
ALTER TABLE `imports_queue`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=129853;
--
-- AUTO_INCREMENT for table `import_jobs`
--
ALTER TABLE `import_jobs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1276;
--
-- AUTO_INCREMENT for table `integration_meta`
--
ALTER TABLE `integration_meta`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57535;
--
-- AUTO_INCREMENT for table `linked_accounts`
--
ALTER TABLE `linked_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=764;
--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=398;
--
-- AUTO_INCREMENT for table `livecasts`
--
ALTER TABLE `livecasts`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=256;
--
-- AUTO_INCREMENT for table `media_files`
--
ALTER TABLE `media_files`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10968;
--
-- AUTO_INCREMENT for table `media_items`
--
ALTER TABLE `media_items`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20483;
--
-- AUTO_INCREMENT for table `member_meta`
--
ALTER TABLE `member_meta`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=157;
--
-- AUTO_INCREMENT for table `meta_data_types`
--
ALTER TABLE `meta_data_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16425;
--
-- AUTO_INCREMENT for table `opens`
--
ALTER TABLE `opens`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=56291;
--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `permalinks`
--
ALTER TABLE `permalinks`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=112615;
--
-- AUTO_INCREMENT for table `permalink_stats`
--
ALTER TABLE `permalink_stats`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=817299;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7104;
--
-- AUTO_INCREMENT for table `posts_categories`
--
ALTER TABLE `posts_categories`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=861;
--
-- AUTO_INCREMENT for table `posts_tags`
--
ALTER TABLE `posts_tags`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=905;
--
-- AUTO_INCREMENT for table `remote_logins`
--
ALTER TABLE `remote_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8217;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=140735;
--
-- AUTO_INCREMENT for table `role_types`
--
ALTER TABLE `role_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `seo_settings`
--
ALTER TABLE `seo_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57191;
--
-- AUTO_INCREMENT for table `shared_grants`
--
ALTER TABLE `shared_grants`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sites`
--
ALTER TABLE `sites`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10882;
--
-- AUTO_INCREMENT for table `sites_ads`
--
ALTER TABLE `sites_ads`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5089;
--
-- AUTO_INCREMENT for table `sites_custom_roles`
--
ALTER TABLE `sites_custom_roles`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sites_custom_roles_capabilities`
--
ALTER TABLE `sites_custom_roles_capabilities`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sites_footer_menu_items`
--
ALTER TABLE `sites_footer_menu_items`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14157;
--
-- AUTO_INCREMENT for table `sites_menu_items`
--
ALTER TABLE `sites_menu_items`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22750;
--
-- AUTO_INCREMENT for table `sites_roles`
--
ALTER TABLE `sites_roles`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=301096;
--
-- AUTO_INCREMENT for table `sites_templates_data`
--
ALTER TABLE `sites_templates_data`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `site_meta_data`
--
ALTER TABLE `site_meta_data`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1996488;
--
-- AUTO_INCREMENT for table `site_notices`
--
ALTER TABLE `site_notices`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15084;
--
-- AUTO_INCREMENT for table `site_notices_seen`
--
ALTER TABLE `site_notices_seen`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1974;
--
-- AUTO_INCREMENT for table `smart_links`
--
ALTER TABLE `smart_links`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=212;
--
-- AUTO_INCREMENT for table `smart_link_urls`
--
ALTER TABLE `smart_link_urls`
  MODIFY `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=495;
--
-- AUTO_INCREMENT for table `special_pages`
--
ALTER TABLE `special_pages`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=89;
--
-- AUTO_INCREMENT for table `speed_blogs`
--
ALTER TABLE `speed_blogs`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3587;
--
-- AUTO_INCREMENT for table `speed_posts`
--
ALTER TABLE `speed_posts`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9258;
--
-- AUTO_INCREMENT for table `support_articles`
--
ALTER TABLE `support_articles`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6682;
--
-- AUTO_INCREMENT for table `support_categories`
--
ALTER TABLE `support_categories`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=287;
--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17131;
--
-- AUTO_INCREMENT for table `support_ticket_actions`
--
ALTER TABLE `support_ticket_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9237;
--
-- AUTO_INCREMENT for table `swapspots`
--
ALTER TABLE `swapspots`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22706;
--
-- AUTO_INCREMENT for table `table_seeds`
--
ALTER TABLE `table_seeds`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=327;
--
-- AUTO_INCREMENT for table `tax_assoc`
--
ALTER TABLE `tax_assoc`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tax_type`
--
ALTER TABLE `tax_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `team_roles`
--
ALTER TABLE `team_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8579;
--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `templates_attributes`
--
ALTER TABLE `templates_attributes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket_notes`
--
ALTER TABLE `ticket_notes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=248;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27723;
--
-- AUTO_INCREMENT for table `unsubfeedback`
--
ALTER TABLE `unsubfeedback`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT for table `unsubscribers`
--
ALTER TABLE `unsubscribers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=792;
--
-- AUTO_INCREMENT for table `unsubscribers_segment`
--
ALTER TABLE `unsubscribers_segment`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=139470;
--
-- AUTO_INCREMENT for table `user_meta`
--
ALTER TABLE `user_meta`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6784;
--
-- AUTO_INCREMENT for table `user_notes`
--
ALTER TABLE `user_notes`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22616;
--
-- AUTO_INCREMENT for table `user_options`
--
ALTER TABLE `user_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31059;
--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=124392;
--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4258;
--
-- AUTO_INCREMENT for table `widgets`
--
ALTER TABLE `widgets`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4938;
--
-- AUTO_INCREMENT for table `widget_meta`
--
ALTER TABLE `widget_meta`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=155;
--
-- AUTO_INCREMENT for table `wizards`
--
ALTER TABLE `wizards`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2100;
