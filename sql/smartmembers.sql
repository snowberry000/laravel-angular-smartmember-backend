-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 05, 2016 at 10:16 PM
-- Server version: 5.5.41-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `smartmembers`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_grants`
--

CREATE TABLE IF NOT EXISTS `access_grants` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `access_level_id` bigint(22) NOT NULL,
  `grant_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `access_levels`
--

CREATE TABLE IF NOT EXISTS `access_levels` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
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
  `webinar_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5163 ;

--
-- Dumping data for table `access_levels`
--

INSERT INTO `access_levels` (`id`, `site_id`, `name`, `information_url`, `redirect_url`, `product_id`, `cb_product_id`, `wso_product_id`, `zaxaa_product_id`, `jvzoo_button`, `price`, `currency`, `payment_interval`, `stripe_plan_id`, `stripe_integration`, `paypal_integration`, `hash`, `deleted_at`, `updated_at`, `created_at`, `facebook_group_id`, `expiration_period`, `hide_unowned_content`, `trial_amount`, `trial_duration`, `trial_interval`, `webinar_url`) VALUES
(1753, 6192, 'Smart Member', '', '', '167089', NULL, NULL, NULL, NULL, 497.00, 'USD', 'one_time', NULL, 0, 0, '5d99d2de51be1c522546d17f4cab7d30', NULL, '2015-10-28 18:32:26', '2015-10-27 05:30:38', NULL, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `access_level_shared_keys`
--

CREATE TABLE IF NOT EXISTS `access_level_shared_keys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `access_level_id` bigint(20) NOT NULL,
  `originate_site_id` bigint(20) NOT NULL,
  `destination_site_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `access_passes`
--

CREATE TABLE IF NOT EXISTS `access_passes` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(22) NOT NULL,
  `access_level_id` bigint(22) NOT NULL,
  `user_id` bigint(22) NOT NULL,
  `expired_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subscription_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_access_level_id` (`access_level_id`),
  KEY `user_id` (`user_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `access_payment_methods`
--

CREATE TABLE IF NOT EXISTS `access_payment_methods` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `access_level_id` bigint(22) NOT NULL,
  `payment_method_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affcontests`
--

CREATE TABLE IF NOT EXISTS `affcontests` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `permalink` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affcontest_sites`
--

CREATE TABLE IF NOT EXISTS `affcontest_sites` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `contest_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affiliates`
--

CREATE TABLE IF NOT EXISTS `affiliates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_jvpage`
--

CREATE TABLE IF NOT EXISTS `affiliate_jvpage` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `subscribe_button_color` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_types`
--

CREATE TABLE IF NOT EXISTS `affiliate_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affleaderboards`
--

CREATE TABLE IF NOT EXISTS `affleaderboards` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) DEFAULT NULL,
  `affiliate_id` int(11) DEFAULT NULL,
  `affiliate_name` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affteamledger`
--

CREATE TABLE IF NOT EXISTS `affteamledger` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_team_id` (`team_id`,`affiliate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `affteams`
--

CREATE TABLE IF NOT EXISTS `affteams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `app_configurations`
--

CREATE TABLE IF NOT EXISTS `app_configurations` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_bpages`
--

CREATE TABLE IF NOT EXISTS `bridge_bpages` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_media_items`
--

CREATE TABLE IF NOT EXISTS `bridge_media_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `aws_key` text NOT NULL,
  `type` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_permalinks`
--

CREATE TABLE IF NOT EXISTS `bridge_permalinks` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `url_slug` varchar(255) NOT NULL,
  `target_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permalink` (`user_id`,`target_id`),
  UNIQUE KEY `unique_link` (`user_id`,`url_slug`),
  KEY `url_slug` (`url_slug`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_seo_settings`
--

CREATE TABLE IF NOT EXISTS `bridge_seo_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `target_id` (`target_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bridge_templates`
--

CREATE TABLE IF NOT EXISTS `bridge_templates` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `folder_slug` varchar(100) NOT NULL,
  `icon` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `bridge_templates`
--

INSERT INTO `bridge_templates` (`id`, `type_id`, `name`, `folder_slug`, `icon`, `deleted_at`, `created_at`, `updated_at`) VALUES
(4, 2, 'Page 1', 'one', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 3, 'Page 1', 'one', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 1, 'Page 1', 'one', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 2, 'Webinar Replay', 'two', '', NULL, '2015-11-03 17:24:05', '0000-00-00 00:00:00'),
(8, 3, 'Clean Slate', 'two', '', NULL, '2016-02-14 21:07:16', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bridge_types`
--

CREATE TABLE IF NOT EXISTS `bridge_types` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) NOT NULL,
  `folder_slug` varchar(100) NOT NULL,
  `icon` text NOT NULL,
  `description` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `bridge_types`
--

INSERT INTO `bridge_types` (`id`, `name`, `folder_slug`, `icon`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Leadcapture', 'leadcapture', '', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Video', 'video', '', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Ecommerce', 'ecommerce', '', '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bridge_user_options`
--

CREATE TABLE IF NOT EXISTS `bridge_user_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_meta` (`user_id`,`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bsite_options`
--

CREATE TABLE IF NOT EXISTS `bsite_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_meta` (`user_id`,`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `canned_responses`
--

CREATE TABLE IF NOT EXISTS `canned_responses` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `company_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `permalink` varchar(255) NOT NULL DEFAULT '',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `permalink` (`permalink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `clicks`
--

CREATE TABLE IF NOT EXISTS `clicks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `link_id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `segment_id` bigint(20) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `segment_id` (`segment_id`),
  KEY `identifier` (`identifier`),
  KEY `link_id` (`link_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `body` text,
  `parent_id` bigint(11) DEFAULT '0',
  `user_id` bigint(11) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `target_id` bigint(11) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `intention` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `company_options`
--

CREATE TABLE IF NOT EXISTS `company_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `key` (`key`),
  KEY `company_options_id` (`company_id`),
  KEY `company_options_key` (`key`),
  KEY `company_options_deleted` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `connected_accounts`
--

CREATE TABLE IF NOT EXISTS `connected_accounts` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `site_id` bigint(22) DEFAULT NULL,
  `company_id` bigint(22) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `access_token` text,
  `remote_id` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `content_stats`
--

CREATE TABLE IF NOT EXISTS `content_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `custom_attributes`
--

CREATE TABLE IF NOT EXISTS `custom_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `shown` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`),
  KEY `type` (`type`),
  KEY `archived` (`archived`),
  KEY `shown` (`shown`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `custom_pages`
--

CREATE TABLE IF NOT EXISTS `custom_pages` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `show_content_publicly` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `directory_listings`
--

CREATE TABLE IF NOT EXISTS `directory_listings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `is_visible` tinyint(1) DEFAULT '1',
  `hide_members` tinyint(1) DEFAULT '0',
  `hide_revenue` tinyint(1) DEFAULT '0',
  `hide_lessons` tinyint(4) NOT NULL DEFAULT '0',
  `hide_downloads` tinyint(4) NOT NULL DEFAULT '0',
  `expired_at` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sub_category` varchar(255) DEFAULT NULL,
  `total_members` int(11) DEFAULT NULL,
  `min_price` double(10,2) DEFAULT NULL,
  `max_price` double(10,2) DEFAULT NULL,
  `min_price_interval` varchar(15) DEFAULT NULL,
  `max_price_interval` varchar(15) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '0',
  `owner` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_settings`
--

CREATE TABLE IF NOT EXISTS `discussion_settings` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `show_comments` tinyint(1) NOT NULL DEFAULT '0',
  `newest_comments_first` tinyint(1) NOT NULL DEFAULT '0',
  `close_to_new_comments` tinyint(1) NOT NULL DEFAULT '0',
  `allow_replies` tinyint(1) NOT NULL DEFAULT '0',
  `public_comments` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `downloads_history`
--

CREATE TABLE IF NOT EXISTS `downloads_history` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `download_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `download_center`
--

CREATE TABLE IF NOT EXISTS `download_center` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `drafts`
--

CREATE TABLE IF NOT EXISTS `drafts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dripfeed`
--

CREATE TABLE IF NOT EXISTS `dripfeed` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `target_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `interval` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `element_types`
--

CREATE TABLE IF NOT EXISTS `element_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `mail_test_default` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails_queue`
--

CREATE TABLE IF NOT EXISTS `emails_queue` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `job_id` (`job_id`),
  KEY `list_type` (`list_type`),
  KEY `company_id` (`company_id`),
  KEY `email_id` (`email_id`,`subscriber_id`,`job_id`,`list_type`),
  KEY `email_recipient_id` (`email_recipient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email_when` int(11) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder_access_level`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder_access_level` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) DEFAULT NULL,
  `access_level_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder_email`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder_email` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) DEFAULT NULL,
  `email_id` varchar(255) NOT NULL,
  `delay` int(11) DEFAULT '0',
  `unit` tinyint(4) DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort_order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder_list`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder_list` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) DEFAULT NULL,
  `list_id` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponder_site`
--

CREATE TABLE IF NOT EXISTS `email_autoresponder_site` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_history`
--

CREATE TABLE IF NOT EXISTS `email_history` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `subscriber_id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `list_type` varchar(50) DEFAULT NULL,
  `auto_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_jobs`
--

CREATE TABLE IF NOT EXISTS `email_jobs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `send_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `email_id` (`email_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_listledger`
--

CREATE TABLE IF NOT EXISTS `email_listledger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `list_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_list_pair` (`list_id`,`subscriber_id`),
  KEY `subscriber_id` (`subscriber_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_lists`
--

CREATE TABLE IF NOT EXISTS `email_lists` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_subscribers` int(11) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `list_type` varchar(255) NOT NULL DEFAULT 'user',
  `segment_query` text,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipient`
--

CREATE TABLE IF NOT EXISTS `email_recipient` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipients`
--

CREATE TABLE IF NOT EXISTS `email_recipients` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `email_id` bigint(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `order` bigint(11) NOT NULL DEFAULT '1',
  `subject` text,
  `intro` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `recipient` (`recipient`),
  KEY `type` (`type`),
  KEY `email_id` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipients_queue`
--

CREATE TABLE IF NOT EXISTS `email_recipients_queue` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_recipient_id` bigint(11) NOT NULL,
  `last_recipient_queued` bigint(11) DEFAULT NULL,
  `email_job_id` bigint(11) DEFAULT NULL,
  `total_queued` bigint(11) DEFAULT NULL,
  `total_recipients` bigint(11) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `sending_user_id` bigint(20) DEFAULT NULL,
  `send_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `email_recipient_id` (`email_recipient_id`),
  KEY `last_recipient_queued` (`last_recipient_queued`),
  KEY `email_job_id` (`email_job_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE IF NOT EXISTS `email_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_subscribers`
--

CREATE TABLE IF NOT EXISTS `email_subscribers` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `index_email` (`email`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) DEFAULT NULL,
  `event_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `count` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `event_name` (`event_name`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_metadata`
--

CREATE TABLE IF NOT EXISTS `event_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_categories`
--

CREATE TABLE IF NOT EXISTS `forum_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE IF NOT EXISTS `forum_replies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text,
  `topic_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_accesslevel`
--

CREATE TABLE IF NOT EXISTS `group_accesslevel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `facebook_id` bigint(20) NOT NULL,
  `access_level_id` bigint(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `history_logins`
--

CREATE TABLE IF NOT EXISTS `history_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `browser` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `imports_queue`
--

CREATE TABLE IF NOT EXISTS `imports_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `access_levels` varchar(255) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `expiry` datetime NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `email_ac` tinyint(1) DEFAULT NULL,
  `email_welcome` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `import_jobs`
--

CREATE TABLE IF NOT EXISTS `import_jobs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `total_count` bigint(20) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `integration_meta`
--

CREATE TABLE IF NOT EXISTS `integration_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integration_id` bigint(20) unsigned NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE IF NOT EXISTS `lessons` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `show_content_publicly` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `lessons_site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `linked_accounts`
--

CREATE TABLE IF NOT EXISTS `linked_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `linked_email` varchar(255) NOT NULL,
  `linked_user_id` bigint(20) NOT NULL,
  `email_only_link` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_hash` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `claimed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `url` text NOT NULL,
  `hash` varchar(35) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `email_id` (`email_id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `livecasts`
--

CREATE TABLE IF NOT EXISTS `livecasts` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `show_content_publicly` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `media_files`
--

CREATE TABLE IF NOT EXISTS `media_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `source` text,
  `type` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `media_items`
--

CREATE TABLE IF NOT EXISTS `media_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `aws_key` text NOT NULL,
  `type` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_meta`
--

CREATE TABLE IF NOT EXISTS `member_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL,
  `custom_attribute_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `custom_attribute_id` (`custom_attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `meta_data_types`
--

CREATE TABLE IF NOT EXISTS `meta_data_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type_value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  `title` text NOT NULL,
  `note` text NOT NULL,
  `access_level` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `opens`
--

CREATE TABLE IF NOT EXISTS `opens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `segment_id` bigint(20) DEFAULT NULL,
  `ip` varchar(20) NOT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `identifier` (`identifier`),
  KEY `segment_id` (`segment_id`),
  KEY `job_id` (`job_id`),
  KEY `email_id` (`email_id`),
  KEY `subscriber_id` (`subscriber_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'jvzoo', NULL, '2015-09-10 15:52:09', '0000-00-00 00:00:00'),
(2, 'paypal', NULL, '2015-09-10 15:52:09', '0000-00-00 00:00:00'),
(3, 'stripe', NULL, '2015-09-10 15:52:09', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `permalinks`
--

CREATE TABLE IF NOT EXISTS `permalinks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permalink` text NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `target_id` bigint(20) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `target_id` (`target_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=214358 ;

--
-- Dumping data for table `permalinks`
--

INSERT INTO `permalinks` (`id`, `permalink`, `site_id`, `target_id`, `type`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1437, 'access-passes', 2056, 18389, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1442, 'access-levels', 2056, 18388, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1451, 'member-management', 2056, 18387, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1893, 'how-to-login-to-your-smart-member-account', 1, 1946, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1894, 'how-to-contact-our-customer-support', 1, 1947, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1895, 'how-to-report-any-bugs-you-find', 1, 1948, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1896, 'creating-a-membership-site-in-60-seconds', 1, 1949, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1897, 'how-to-join-the-smart-member-referral-program', 1, 1950, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1898, 'how-to-create-membership-sites', 1, 1951, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1899, 'managing-sites', 1, 1952, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1900, 'admin-overview', 1, 1953, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1901, 'member-overview', 1, 1954, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1902, 'adding-modules', 1, 1955, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1903, 'adding-lessons', 1, 1956, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1904, 'organizing-your-syllabus', 1, 1957, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1905, 'editing-lessons', 1, 1958, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1906, 'embedding-videos', 1, 1959, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1907, 'importing-videos', 1, 1960, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1908, 'creating-a-vimeo-account', 1, 1961, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1909, 'uploading-videos', 1, 1962, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1910, 'editing-video-settings', 1, 1963, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1911, 'overview-of-membership-levels', 1, 1964, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1912, 'public-levels', 1, 1965, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1913, 'opt-in-levels', 1, 1966, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1914, 'paid-levels', 1, 1967, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1915, 'jvzoo-integration', 1, 1968, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1916, 'overview-of-jvzoo', 1, 1969, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1917, 'creating-a-product', 1, 1970, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1918, 'pricing-structures', 1, 1971, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1919, 'adding-your-jvzipn-url', 1, 1972, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1920, 'jvzoo-product-ids', 1, 1973, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1921, 'installing-the-jv-chrome-extension', 1, 1974, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1922, 'managing-affiliate-requests', 1, 1975, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1923, 'managing-your-jv-partners', 1, 1976, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1924, 'adding-new-members', 1, 1977, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1925, 'free-memberships', 1, 1978, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1926, 'paid-memberships', 1, 1979, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1927, 'viewing-members', 1, 1980, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1928, 'free-access-passes', 1, 1981, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1929, 'adding-new-admins', 1, 1982, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1930, 'role-management', 1, 1983, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1931, 'affiliate-dashboard', 1, 1984, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1932, 'adding-new-affiliates', 1, 1985, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1933, 'adding-sales-contests', 1, 1986, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1934, 'managing-contest-leaderboards', 1, 1987, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1935, 'managing-affiliate-teams', 1, 1988, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1936, 'knowledgebase-creator', 1, 1989, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1937, 'adding-new-categories', 1, 1990, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1938, 'adding-new-faqs', 1, 1991, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1939, 'managing-customer-tickets', 1, 1992, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1940, 'creating-new-pages', 1, 1993, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1941, 'managing-your-pages', 1, 1994, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1942, 'seo-settings', 1, 1995, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1943, 'facebook-settings', 1, 1996, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1944, 'dashboard-overview', 1, 1997, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1945, 'member-transactions', 1, 1998, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1946, 'payment-gateways', 1, 1999, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1947, 'wallboard-stats', 1, 2000, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1948, 'jvzoo', 1, 2001, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1949, 'paypal', 1, 2002, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1950, 'stripe', 1, 2003, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1951, '1shopping-cart', 1, 2004, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1952, 'infusionsoft', 1, 2005, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1953, '2checkout', 1, 2006, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1954, 'powerpay', 1, 2007, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1955, 'warrior-', 1, 2008, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1956, 'authorizenet', 1, 2009, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1957, 'ontraport', 1, 2010, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1958, 'zaxaa', 1, 2011, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1959, 'deal-guardian', 1, 2012, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1960, 'clickbank', 1, 2013, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1961, 'nanacast', 1, 2014, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1962, 'sendgrid', 1, 2015, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1963, 'aweber', 1, 2016, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1964, 'get-response', 1, 2017, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1965, 'sendlane', 1, 2018, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1966, 'mailchimp', 1, 2019, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1967, 'constant-contact', 1, 2020, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1968, 'mandrill', 1, 2021, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1969, 'icontact', 1, 2022, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1970, 'how-to-suggest-integrations', 1, 2023, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1971, 'white-label-overview', 1, 2024, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1972, 'client-login-access', 1, 2025, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1973, 'reselling-tips-and-techniques', 1, 2026, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1974, 'overview-of-bridge-pages', 1, 2027, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1975, 'creating-dynamic-variables', 1, 2028, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1976, 'bridge-marketing-101', 1, 2029, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1977, 'advanced-bridge-marketing', 1, 2030, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1978, 'facebook-url-tags-and-bridge-pages', 1, 2031, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1979, 'personalized-messages-with-bridge-pages', 1, 2032, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1980, 'bonus-pages-with-bridge-pages', 1, 2033, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1981, 'case-study-dark-post-profits-10', 1, 2034, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1982, 'case-study-dark-post-profits-20', 1, 2035, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1983, 'case-study-smart-member-training', 1, 2036, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1984, 'case-study-get-money-method', 1, 2037, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1985, 'case-study-nowdriven-training', 1, 2038, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1986, 'overview-of-livecasts', 1, 2039, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1987, 'setting-up-your-registration-page', 1, 2040, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1988, 'setting-up-your-livecast-page', 1, 2041, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1989, 'managing-your-autoresponders', 1, 2042, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1990, 'running-a-livecast', 1, 2043, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1991, 'archiving-livecasts', 1, 2044, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1992, 'how-to-create-a-sales-page', 1, 2045, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1993, 'how-to-view-your-sales-page', 1, 2046, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1994, 'how-to-create-a-jv-page', 1, 2047, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1995, 'adding-your-jv-page-to-your-menu-bar', 1, 2048, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1996, 'adding-your-sales-page-to-your-menu-bar', 1, 2049, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1997, 'how-to-use-3rd-party-sales-pages', 1, 2050, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1998, 'adding-a-new-downloadable-item', 1, 2051, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(1999, 'creating-member-levels-for-your-downloads', 1, 2052, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2000, 'designing-your-downloads-center', 1, 2053, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2001, 'seo-optimizing-your-downloads', 1, 2054, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2002, 'overview-of-the-smart-member-affiliate-program', 1, 2055, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2003, 'applying-to-become-an-affiliate', 1, 2056, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2004, 'affiliate-terms-of-service', 1, 2057, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2005, 'how-often-you-get-paid', 1, 2058, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2006, 'best-practices-for-affiliates', 1, 2059, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2007, 'smart-member-banner-ads', 1, 2060, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2008, 'smart-member-email-swipe', 1, 2061, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2009, 'best-converting-customer-markets', 1, 2062, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2010, 'smart-member-facebook-ads', 1, 2063, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2011, 'zapable-mobile-app-builder', 1, 2064, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2012, 'email-domination-email-marketing-system', 1, 2065, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2013, 'insights-hero-finding-targeted-interests', 1, 2066, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2014, 'speed-blogging-easy-content-embedding', 1, 2067, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2015, 'seo-best-practices', 1, 2068, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2016, 'seo-lesson-titles', 1, 2069, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2017, 'seo-descriptions', 1, 2070, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2018, 'seo-transcriptions', 1, 2071, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2019, 'how-to-brainstorm-effective-names-for-lead-magnets', 1, 2072, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2020, 'how-to-create-high-converting-landing-pages-for-facebook-ads', 1, 2073, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2021, 'how-to-create-a-high-converting-lead-magnet-for-your-offers', 1, 2074, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2022, 'how-to-generate-leads-effectively-using-fb-ads', 1, 2075, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2023, 'how-to-sell-high-ticket-items-using-livecast-events-and-recorded-events', 1, 2076, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2024, 'how-to-fill-webinars-using-facebook-ads', 1, 2077, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2025, 'example-facebook-ads-for-lead-generation', 1, 2078, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2026, 'how-to-create-high-converting-facebook-ads', 1, 2079, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2027, 'nicholas-kusmich-introduction', 1, 2080, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2028, 'lead-magnet-cheat-sheet-walkthrough', 1, 2081, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2029, 'how-to-use-fb-ads-for-lead-generation-and-high-ticket-sales-with-nicholas-kusmich', 1, 2082, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2030, 'adding-appointment-forms-to-your-apps', 1, 2083, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2031, 'how-to-use-custom-thumbnails-on-youtube-videos', 1, 2084, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2032, 'new-lesson', 1, 2085, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2033, 'how-to-get-facebook-video-views-for-a-penny-or-less-each', 1, 2086, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2034, 'how-to-use-facebook-video-retargeting-to-increase-roi-conversions', 1, 2087, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2035, 'the-art-of-delivering-value', 1, 2088, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(2036, 'stay-tuned-for-50-customer-support-modules', 1, 2089, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(3563, 'stripe-integration', 2056, 18386, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(3567, 'integrations', 2056, 18385, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(3572, 'account-settings', 2056, 18384, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(3576, 'creating-site', 2056, 18383, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(3583, 'welcome-smart-member', 2056, 18382, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6700, '5-tips-to-close-more-sales-in-your-business', 1, 7249, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6701, '21-tips-to-generate-more-leads-online', 1, 7250, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6702, 'create-capture-convert-conference-in-las-vegas-july-24th-25th-26th-2015', 1, 7251, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6703, 'how-to-create-high-converting-landing-pages-for-facebook-ads', 1, 7252, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6704, 'how-to-brainstorm-effective-names-for-lead-magnets', 1, 7253, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6705, 'how-to-create-a-high-converting-lead-magnet-for-your-offers', 1, 7254, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(6706, 'example-facebook-ads-for-lead-generation', 1, 7255, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(12799, 'n-a', 1, 14260, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(13349, '', 1, 14863, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16050, 'Version-1.2-Is-Being-Rolled-Out', 1, 17586, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16051, 'V1.2-Tutorial-Videos-Being-Created', 1, 17587, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16052, 'Smart-Member-Will-Re-Open-to-the-Public-on-Sep-25th.', 1, 17588, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16837, 'jvzoo-payment-gateway-integration', 2056, 18390, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16838, 'facebook-group-integration', 2056, 18391, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16839, 'lessons', 2056, 18392, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16900, 'modules', 2056, 18453, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16901, 'pages', 2056, 18454, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16902, 'downloads', 2056, 18455, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16903, 'content-importing-vimeo', 2056, 18456, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16904, 'syllabus-creator', 2056, 18457, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16905, 'blog-posts', 2056, 18458, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16933, 'Content-Importing-(with-Vimeo)', 2056, 18486, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16934, 'Syllabus-Creator', 2056, 18487, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16935, 'Blog-Posts', 2056, 18488, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16936, 'Downloads', 2056, 18489, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16937, 'Pages', 2056, 18490, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16938, 'Modules', 2056, 18491, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16939, 'Lessons', 2056, 18492, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16940, 'Facebook-Group-Integration', 2056, 18493, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16941, 'JVZoo-Payment-Gateway-Integration', 2056, 18494, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16942, 'Access-Passes', 2056, 18495, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16943, 'Access-Levels', 2056, 18496, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16944, 'Managing-your-members', 2056, 18497, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16945, 'Stripe-Integration', 2056, 18498, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16946, 'Integrations', 2056, 18499, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16947, 'Account-Settings', 2056, 18500, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16948, 'Creating-a-site', 2056, 18501, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(16949, 'welcome-to-smart-member', 2056, 18502, 'lessons', NULL, '2016-01-28 20:20:03', '2015-09-30 10:57:55'),
(17000, 'granting-admin-access', 2056, 18552, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17035, 'domain-mapping', 2056, 18586, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17046, 'importing-members', 2056, 18594, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17052, 'special-pages', 2056, 18601, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17100, 'marketing-tools', 2056, 18649, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17373, '3c-event-recordings-e-brian-rose', 1, 18922, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17374, '3c-event-recordings-brandon-burgess', 1, 18923, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17375, '3c-event-recordings-tanner-larsson-and-los-silva-2015-09-14t203513-0000', 1, 18924, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17380, '3c-event-recordings-mario-brown-2015-09-14t210025-0000-2015-09-14t212434-0000', 1, 18929, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17381, '3c-event-recordings-nicholas-kusmich-2015-09-14t215454-0000', 1, 18930, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17383, '3c-event-recordings-devin-zander', 1, 18932, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17385, '3c-event-recordings-chris-record-second-friday-session', 1, 18934, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17386, '3c-event-recordings-intro', 1, 18935, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17441, '3c-event-recordings-chris-record-saturday-session', 1, 18989, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17442, '3c-event-recordings-chris-record-second-saturday-session-2015-09-14t231016-0000', 1, 18990, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17443, '3c-event-recordings-chris-record-sunday-session-2015-09-14t230955-0000-2015-09-15t184124-0000', 1, 18991, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17444, '3c-event-recordings-jimmy-kim-2015-09-15t180335-0000-2015-09-15t180756-0000', 1, 18992, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(17580, '3c-event-recordings-chris-record-first-friday-session', 1, 19130, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(18358, 'understanding-roles-permissions-inside-smart-member', 2056, 19955, 'lessons', NULL, NULL, '2015-09-30 10:57:55'),
(19492, 'privacy-policy', 1, 63, 'custom_pages', NULL, NULL, '2015-09-30 10:58:01'),
(19493, 'simplified-privacy-policy', 1, 64, 'custom_pages', NULL, NULL, '2015-09-30 10:58:01'),
(19559, 'dashboard-stuff', 1, 137, 'custom_pages', NULL, NULL, '2015-09-30 10:58:01'),
(19579, 'bridge-pages', 1, 159, 'custom_pages', NULL, NULL, '2015-09-30 10:58:01'),
(19580, '3c-conference-las-vegas', 1, 160, 'custom_pages', NULL, NULL, '2015-09-30 10:58:01'),
(20121, 'test-page', 1, 756, 'custom_pages', NULL, NULL, '2015-09-30 10:58:01'),
(20401, 'the-fitness-simplified-guide-pdf', 1, 161, 'download_center', NULL, NULL, '2015-09-30 10:58:06'),
(20402, '7-million-dollar-lessons-from-events-pdf', 1, 162, 'download_center', NULL, NULL, '2015-09-30 10:58:06'),
(20403, 'pdf-5-tips-on-re-launching-your-successful-t-shirt-campaigns', 1, 163, 'download_center', NULL, NULL, '2015-09-30 10:58:06'),
(21625, 'why-people-become-entrepreneurs', 1, 45, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(21626, 'entrepreneurship-infographics', 1, 49, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22353, 'howdy-and-welcome', 2056, 958, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22354, 'smart-member-version-120-released', 2056, 959, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22358, 'smart-member-version-121-released', 2056, 963, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22374, 'smart-member-version-122-released', 2056, 979, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22401, 'tutorial-videos-have-arrived', 2056, 1006, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22403, 'smart-member-version-123-released', 2056, 1008, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22445, 'smart-member-version-124-released', 2056, 1052, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22472, 'smart-member-version-125-released', 2056, 1079, 'posts', NULL, NULL, '2015-09-30 10:58:11'),
(22499, 'smart-member-orientation', 1, 5, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22515, 'product-launch-workshops', 1, 22, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22535, 'week-1', 1, 52, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22536, 'week-2', 1, 53, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22537, 'bonus-week', 1, 54, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22540, 'week3', 1, 57, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22544, 'smart-member-replay', 1, 62, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22550, 'week-4---product-launch-workshop-rescheduled', 1, 70, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22559, 'product-launch-workshop', 1, 86, 'livecasts', NULL, NULL, '2015-09-30 10:58:16'),
(22615, 'domain-mapping', 1, 25, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22668, 'Access-Levels-2015-08-26T11:38:46+00:00', 1, 79, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22671, 'Refund-Policy-&-Procedure-2015-08-26T11:38:46+00:00', 1, 83, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22672, 'Associating-Your-Facebook-Account-with-Smart-Member-2015-08-26T11:38:46+00:00', 1, 84, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22673, 'Lessons-2015-08-26T11:38:46+00:00', 1, 85, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22676, 'Modules-and-Lessons-Do-Not-Show-Up-2015-08-26T11:38:46+00:00', 1, 88, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22677, 'Access-to-Bridge-Page-Bonus-2015-08-26T11:38:46+00:00', 1, 90, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22678, 'Creating-Multiple-Admins-2015-08-26T11:38:46+00:00', 1, 91, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22679, 'Trouble-Creating-Site-2015-08-26T11:38:46+00:00', 1, 92, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22680, 'Audio-Hosting-2015-08-26T11:38:46+00:00', 1, 94, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22681, 'How-to-Change-Logo-2015-08-26T11:38:46+00:00', 1, 95, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22682, 'What-is-the-"Wallboard"?-2015-08-26T11:38:46+00:00', 1, 96, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22684, 'Vimeo-2015-08-26T11:38:46+00:00', 1, 98, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(22697, 'How-to-Submit-a-Support-Ticket-2015-08-26T11:38:46+00:00', 1, 111, 'support_articles', NULL, NULL, '2015-09-30 10:58:19'),
(23834, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-01 19:49:08', '2015-10-01 19:49:08'),
(23839, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-01 19:51:35', '2015-10-01 19:51:35'),
(23859, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-01 21:34:22', '2015-10-01 21:34:22'),
(24177, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-02 20:41:40', '2015-10-02 20:41:40'),
(24180, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-02 21:00:14', '2015-10-02 21:00:14'),
(24186, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-02 21:09:36', '2015-10-02 21:09:36'),
(26517, 'smart-member-version-127-released', 2056, 1224, 'posts', NULL, '2015-11-10 06:37:56', '2015-10-09 14:59:11'),
(26518, '127', 2056, 1224, 'posts', '2015-11-10 06:37:56', '2015-11-10 06:37:56', '2015-10-09 15:04:50'),
(26552, '127', 2056, 1224, 'posts', '2015-11-10 06:37:56', '2015-11-10 06:37:56', '2015-10-09 15:34:22'),
(26553, 'smart-member-version-127-released', 2056, 1224, 'posts', '2015-11-10 06:37:56', '2015-11-10 06:37:56', '2015-10-09 15:35:45'),
(26555, 'smart-member-version-127-released', 2056, 1224, 'posts', '2015-11-10 06:37:56', '2015-11-10 06:37:56', '2015-10-09 15:58:22'),
(26564, 'smart-member-version-127-released', 2056, 1224, 'posts', '2015-11-10 06:37:56', '2015-11-10 06:37:56', '2015-10-09 16:06:43'),
(26566, 'smart-member-version-126-released', 2056, 1123, 'posts', NULL, '2015-10-09 16:08:30', '2015-10-09 16:08:30'),
(29883, 'smart-member-version-128-released', 2056, 1278, 'posts', NULL, '2015-10-16 15:55:58', '2015-10-16 15:55:58'),
(29888, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 16:09:07'),
(29892, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 16:15:26'),
(29893, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 16:23:56'),
(30010, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 18:45:08'),
(30016, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 18:54:37'),
(30029, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 19:07:16'),
(30033, 'smart-member-version-128-released', 2056, 1278, 'posts', '2015-11-10 06:38:14', '2015-11-10 06:38:14', '2015-10-16 19:10:41'),
(32766, 'Email-Overview', 2056, 25000, 'lessons', NULL, '2015-10-20 17:33:03', '2015-10-20 17:33:03'),
(32849, 'Subscribers', 2056, 25031, 'lessons', NULL, '2015-10-20 19:44:03', '2015-10-20 19:44:03'),
(32850, 'Email-Creation', 2056, 25032, 'lessons', NULL, '2015-10-20 19:44:03', '2015-10-20 19:44:03'),
(32851, 'Email-Lists', 2056, 25033, 'lessons', NULL, '2015-10-20 19:44:03', '2015-10-20 19:44:03'),
(32883, 'Autoresponders', 2056, 25050, 'lessons', NULL, '2015-10-20 20:26:18', '2015-10-20 20:26:18'),
(32888, 'Settings', 2056, 25052, 'lessons', NULL, '2015-10-20 20:31:13', '2015-10-20 20:31:13'),
(32891, 'Email-Stats', 2056, 25054, 'lessons', NULL, '2015-10-20 20:37:41', '2015-10-20 20:37:41'),
(32900, 'Optin-Form', 2056, 25057, 'lessons', NULL, '2015-10-20 20:56:54', '2015-10-20 20:56:54'),
(37237, 'smart-member-version-129-released', 2056, 1590, 'posts', NULL, '2015-10-27 03:47:02', '2015-10-27 03:47:02'),
(45707, 'How-to-create-sites', 2056, 292, 'support_articles', NULL, '2015-11-10 08:22:03', '2015-11-10 08:22:03'),
(45708, 'How-to-map-a-domain-to-a-site', 2056, 293, 'support_articles', NULL, '2015-11-10 08:22:16', '2015-11-10 08:22:16'),
(47778, 'test-bridge-page', 2056, 1985, 'bridge_bpages', NULL, '2015-11-13 21:47:14', '2015-11-13 21:47:14'),
(67502, '182016-smart-member-20-live-important-update-re-mobile-users', 2056, 2133, 'custom_pages', NULL, '2016-01-09 01:07:29', '2016-01-09 01:07:29'),
(69067, 'how-new-browse-features-smart-member-20-works', 2056, 45765, 'lessons', NULL, '2016-01-11 02:47:13', '2016-01-11 02:47:13'),
(69068, 'how-new-design-section-smart-member-20-works', 2056, 45766, 'lessons', NULL, '2016-01-11 02:49:19', '2016-01-11 02:49:19'),
(69069, 'how-customize-and-edit-content-smart-member-20', 2056, 45767, 'lessons', NULL, '2016-01-11 02:49:34', '2016-01-11 02:49:34'),
(69070, 'how-set-permissions-members-area-smart-member-20', 2056, 45768, 'lessons', NULL, '2016-01-11 02:49:46', '2016-01-11 02:49:46'),
(69071, 'how-sidebars-work-smart-member-20', 2056, 45769, 'lessons', NULL, '2016-01-11 02:49:54', '2016-01-11 02:49:54'),
(69072, 'overview-affiliate-and-site-settings', 2056, 45770, 'lessons', NULL, '2016-01-11 02:50:04', '2016-01-11 02:50:04'),
(69073, 'how-apps-smart-member-20-work', 2056, 45771, 'lessons', NULL, '2016-01-11 02:50:24', '2016-01-11 02:50:24'),
(69074, 'how-email-functions-smart-member-20-works', 2056, 45772, 'lessons', NULL, '2016-01-11 02:50:42', '2016-01-11 02:50:42'),
(69075, 'site-creation-overview-smart-member-20', 2056, 45773, 'lessons', NULL, '2016-01-11 02:50:52', '2016-01-11 02:50:52'),
(69076, 'how-bridge-pages-works-smart-member-20', 2056, 45774, 'lessons', NULL, '2016-01-11 02:51:11', '2016-01-11 02:51:11'),
(69077, 'how-facebook-marketing-works-bridge-pages', 2056, 45775, 'lessons', NULL, '2016-01-11 02:51:30', '2016-01-11 02:51:30'),
(69085, 'using-canva-and-importing-your-image-bridge-pages', 2056, 45777, 'lessons', NULL, '2016-01-11 02:52:03', '2016-01-11 02:52:03'),
(69086, 'linking-your-facebook-ad-bridge-pages', 2056, 45778, 'lessons', NULL, '2016-01-11 02:52:17', '2016-01-11 02:52:17'),
(70303, 'Intgegrating-aweber-with-paypal-and-jvzoo', 1, 10, 'forum_topics', NULL, '2016-01-12 10:53:51', '2016-01-12 10:53:51'),
(70455, 'integration-with-Infusionsoft', 1, 11, 'forum_topics', NULL, '2016-01-12 16:51:17', '2016-01-12 16:51:17'),
(70456, 'hhb8sm', 1, 2674, 'bridge_bpages', NULL, '2016-01-12 16:52:22', '2016-01-12 16:52:22'),
(71784, 'Integation-with-Zapable', 1, 13, 'forum_topics', NULL, '2016-01-13 05:20:23', '2016-01-13 05:20:23'),
(72228, 'Autoresponders-integration', 1, 14, 'forum_topics', NULL, '2016-01-13 09:12:36', '2016-01-13 09:12:36'),
(73438, 'Integrate-with-ClickFunnels/Stripe', 1, 17, 'forum_topics', NULL, '2016-01-13 20:10:27', '2016-01-13 20:10:27'),
(77012, 'Integration-With-Webinar-Jam-API', 1, 24, 'forum_topics', NULL, '2016-01-15 05:50:36', '2016-01-15 05:50:36'),
(77013, 'Integration-With-Amazon-S3-and-DropBox', 1, 25, 'forum_topics', NULL, '2016-01-15 05:52:15', '2016-01-15 05:52:15'),
(77792, 'Integration-with-Automatic-Webinars-(Replay-of-Webinars)', 1, 26, 'forum_topics', NULL, '2016-01-15 16:47:48', '2016-01-15 16:47:48'),
(78710, 'test', 2056, 48281, 'lessons', '2016-01-16 22:42:08', '2016-01-16 22:42:08', '2016-01-15 23:38:34'),
(78823, 'how-the-jv-link-approval-tool-works', 2056, 48312, 'lessons', NULL, '2016-01-16 00:39:45', '2016-01-16 00:39:45'),
(78944, 'demo-of-dynamic-bridge-pages-in-smart-member', 2056, 48338, 'lessons', NULL, '2016-01-16 02:22:26', '2016-01-16 02:22:26'),
(78945, 'url-tags-in-and-demo-of-facebook-ad-creation', 2056, 48339, 'lessons', NULL, '2016-01-16 02:24:34', '2016-01-16 02:24:34'),
(78946, 'creating-and-customizing-a-bridge-page-in-smart-member', 2056, 48340, 'lessons', NULL, '2016-01-16 02:25:47', '2016-01-16 02:25:47'),
(78947, 'setting-up-a-video-from-vimeo-for-bridge-pages', 2056, 48341, 'lessons', NULL, '2016-01-16 02:27:12', '2016-01-16 02:27:12'),
(78948, 'demo-of-a-facebook-post-incorporating-bridge-pages', 2056, 48342, 'lessons', NULL, '2016-01-16 02:28:56', '2016-01-16 02:28:56'),
(78949, 'second-demo-of-a-facebook-post-incorporating-bridge-pages', 2056, 48343, 'lessons', NULL, '2016-01-16 02:30:29', '2016-01-16 02:30:29'),
(78950, 'how-to-create-a-community-with-smart-member-and-use-the-forum', 2056, 48344, 'lessons', NULL, '2016-01-16 02:38:11', '2016-01-16 02:38:11'),
(78951, 'how-to-break-down-niches-and-market-products-for-them', 2056, 48345, 'lessons', NULL, '2016-01-16 02:40:11', '2016-01-16 02:40:11'),
(78952, 'reverse-engineering-other-online-businesses-to-create-your-smart-member-site', 2056, 48346, 'lessons', NULL, '2016-01-16 02:42:11', '2016-01-16 02:42:11'),
(79473, 'Integration-with-Taxamo', 1, 36, 'forum_topics', NULL, '2016-01-16 09:35:11', '2016-01-16 09:35:11'),
(80492, 'how-to-use-jvzoo-to-manage-affiliate-offers-and-set-goals', 2056, 48660, 'lessons', NULL, '2016-01-16 22:00:20', '2016-01-16 22:00:20'),
(80495, 'using-the-jvzoo-marketplace-to-find-products-to-sell', 2056, 48661, 'lessons', NULL, '2016-01-16 22:01:32', '2016-01-16 22:01:32'),
(80497, 'how-to-create-front-end-offers-and-bonuses-with-jvzoo', 2056, 48662, 'lessons', NULL, '2016-01-16 22:04:45', '2016-01-16 22:04:45'),
(80500, 'using-your-smart-member-site-as-a-bonus-and-jv-zoo-integration', 2056, 48663, 'lessons', NULL, '2016-01-16 22:09:41', '2016-01-16 22:09:41'),
(80501, 'linking-a-smart-member-site-to-jvzoo', 2056, 48664, 'lessons', NULL, '2016-01-16 22:12:04', '2016-01-16 22:12:04'),
(80502, 'generating-content-and-customizing-your-smart-member-site', 2056, 48665, 'lessons', NULL, '2016-01-16 22:13:18', '2016-01-16 22:13:18'),
(80503, 'promoting-affiliates-with-your-smart-member-site-and-configuring-bonus-downloads', 2056, 48666, 'lessons', NULL, '2016-01-16 22:14:34', '2016-01-16 22:14:34'),
(80504, 'planning-and-creating-your-first-smart-member-lesson', 2056, 48667, 'lessons', NULL, '2016-01-16 22:15:32', '2016-01-16 22:15:32'),
(80505, 'creating-thumbnails-and-titles-for-your-lessons', 2056, 48668, 'lessons', NULL, '2016-01-16 22:17:01', '2016-01-16 22:17:01'),
(80506, 'how-do-you-use-bridge-pages-with-smart-member', 2056, 48669, 'lessons', NULL, '2016-01-16 22:18:10', '2016-01-16 22:18:10'),
(80507, 'do-bonuses-really-matter-much-for-smart-member-sites', 2056, 48670, 'lessons', NULL, '2016-01-16 22:19:44', '2016-01-16 22:19:44'),
(80519, 'where-are-the-forums-on-smart-member', 2056, 48672, 'lessons', NULL, '2016-01-16 22:21:07', '2016-01-16 22:21:07'),
(80520, 'Should-I-Use-a-Smart-Member-Forum-or-a-Facebook-Group', 2056, 48673, 'lessons', NULL, '2016-01-16 22:40:27', '2016-01-16 22:22:43'),
(80521, 'if-you-have-multiple-products-do-you-put-them-all-in-one-site', 2056, 48674, 'lessons', NULL, '2016-01-16 22:24:08', '2016-01-16 22:24:08'),
(80523, 'why-should-you-not-use-older-jvzoo-products', 2056, 48676, 'lessons', NULL, '2016-01-16 22:27:59', '2016-01-16 22:27:59'),
(80524, 'what-are-the-main-differences-between-smart-member-10-and-20', 2056, 48677, 'lessons', NULL, '2016-01-16 22:29:44', '2016-01-16 22:29:44'),
(80525, 'how-speed-blogging-works-in-smart-member-20', 2056, 48678, 'lessons', NULL, '2016-01-16 22:32:43', '2016-01-16 22:32:43'),
(80537, 'how-does-customer-support-in-smart-member-work', 2056, 48680, 'lessons', NULL, '2016-01-16 22:34:02', '2016-01-16 22:34:02'),
(87644, 'I-am-test', 1, 8300, 'download_center', NULL, '2016-01-20 06:32:05', '2016-01-20 06:32:05'),
(87645, 'I-am-test-1', 1, 8301, 'download_center', '2016-01-20 06:33:30', '2016-01-20 06:33:30', '2016-01-20 06:32:14'),
(87648, 'I-am-test-1', 1, 8302, 'download_center', NULL, '2016-01-20 06:34:23', '2016-01-20 06:34:23'),
(87666, 'I-am-test-2', 1, 8307, 'download_center', NULL, '2016-01-20 06:37:43', '2016-01-20 06:37:43'),
(87667, 'I-am-test-3', 1, 8308, 'download_center', NULL, '2016-01-20 06:37:50', '2016-01-20 06:37:50'),
(87671, 'I-am-test-4', 1, 8309, 'download_center', NULL, '2016-01-20 06:41:35', '2016-01-20 06:41:35'),
(90242, 'Mobile-Opt-In-Integration', 1, 121, 'forum_topics', NULL, '2016-01-21 10:33:33', '2016-01-21 10:33:33'),
(97386, 'Integration-with-Easy-Video-Suite', 1, 140, 'forum_topics', NULL, '2016-01-22 22:58:23', '2016-01-22 22:58:23'),
(99296, 'Video-Player-for-Amazon-S3-files-etc', 1, 151, 'forum_topics', NULL, '2016-01-23 18:59:30', '2016-01-23 18:59:30'),
(102437, 'Integration-with-Shopify', 1, 175, 'forum_topics', NULL, '2016-01-25 02:24:53', '2016-01-25 02:24:53'),
(103434, 'Social-Connect-integrate-with-Smart-Member', 1, 177, 'forum_topics', NULL, '2016-01-25 14:26:03', '2016-01-25 14:26:03'),
(110785, 'Notification-when-new-user-become-member', 2056, 199, 'forum_topics', NULL, '2016-01-26 19:17:18', '2016-01-26 19:17:18'),
(110917, 'Tutorials-area-Not-urgent-but-getting-bugged-', 2056, 201, 'forum_topics', NULL, '2016-01-26 19:51:47', '2016-01-26 19:51:47'),
(111195, 'Gallery-Style-layout', 2056, 202, 'forum_topics', NULL, '2016-01-26 21:36:41', '2016-01-26 21:36:41'),
(111269, 'Dark-Post-Profit-selection-based-on-country', 2056, 203, 'forum_topics', NULL, '2016-01-26 21:55:27', '2016-01-26 21:55:27'),
(111375, 'Increase-email-opens-enable-new-members-to-select-their-correct-inbox', 2056, 204, 'forum_topics', NULL, '2016-01-26 22:38:28', '2016-01-26 22:38:28'),
(111471, 'Signup-by-Text', 2056, 205, 'forum_topics', NULL, '2016-01-26 23:10:59', '2016-01-26 23:10:59'),
(112170, 'How-to-Get-Started', 2056, 206, 'forum_topics', NULL, '2016-01-27 01:14:57', '2016-01-27 01:14:57'),
(112248, 'Bridge-Page-Opt-in', 2056, 207, 'forum_topics', NULL, '2016-01-27 02:16:51', '2016-01-27 02:16:51'),
(112262, 'BRIDGE-PAGE-REMOVING-ACCESS-LEVELS', 2056, 208, 'forum_topics', NULL, '2016-01-27 02:23:21', '2016-01-27 02:23:21'),
(112607, 'Changing-the-color-of-Privacy-Policy-text-in-footer', 2056, 209, 'forum_topics', NULL, '2016-01-27 05:15:44', '2016-01-27 05:15:44'),
(112805, 'Allow-addition-of-HTML-page-to-SM-site', 2056, 212, 'forum_topics', NULL, '2016-01-27 06:38:05', '2016-01-27 06:38:05'),
(112845, 'Custom-code-in-header-and-footer', 2056, 213, 'forum_topics', NULL, '2016-01-27 06:56:18', '2016-01-27 06:56:18'),
(113548, 'Managing-Email-Lists', 2056, 214, 'forum_topics', NULL, '2016-01-27 15:12:52', '2016-01-27 15:12:52'),
(113711, 'Same-Gap-of-many-others-How-to-start', 2056, 215, 'forum_topics', NULL, '2016-01-27 17:04:34', '2016-01-27 17:04:34'),
(114026, 'default-view-of-site', 2056, 217, 'forum_topics', NULL, '2016-01-27 18:59:20', '2016-01-27 18:59:20'),
(114170, 'Dynamically-change-site-name-on-Sign-Up-Form', 2056, 218, 'forum_topics', NULL, '2016-01-27 20:17:49', '2016-01-27 20:17:49'),
(114227, 'Drip-feed-is-set-to-wrong-sign-up-date', 2056, 219, 'forum_topics', NULL, '2016-01-27 20:46:07', '2016-01-27 20:46:07'),
(114733, 'Step-by-Step-Tutorials', 2056, 221, 'forum_topics', NULL, '2016-01-27 23:43:49', '2016-01-27 23:43:49'),
(115849, 'Integration-With-2checkout', 2056, 222, 'forum_topics', NULL, '2016-01-28 03:25:30', '2016-01-28 03:25:30'),
(116040, 'Show-ONLY-the-lessons-people-have-access-to', 2056, 223, 'forum_topics', NULL, '2016-01-28 04:09:30', '2016-01-28 04:09:30'),
(116043, 'Import-members-with-password-so-no-email-is-sent-out', 2056, 224, 'forum_topics', NULL, '2016-01-28 04:11:43', '2016-01-28 04:11:43'),
(116044, 'Option-to-add-in-copyright-text-below-bottom-navigation', 2056, 225, 'forum_topics', NULL, '2016-01-28 04:13:02', '2016-01-28 04:13:02'),
(116045, 'Logged-in-users-can-see-all-tickets-they-have-submitted', 2056, 226, 'forum_topics', NULL, '2016-01-28 04:14:22', '2016-01-28 04:14:22'),
(116048, 'Banners-shown-to-people-withwithout-certain-access-levels-on-certain-pages', 2056, 227, 'forum_topics', NULL, '2016-01-28 04:15:37', '2016-01-28 04:15:37'),
(116086, 'Site-Notices-Show-Only-to-FREE-or-Only-to-Paid-Members', 2056, 229, 'forum_topics', NULL, '2016-01-28 04:46:45', '2016-01-28 04:46:45'),
(116170, 'MEMBERS-ONLY-Image-show-different-image-for-upgrade-content', 2056, 230, 'forum_topics', NULL, '2016-01-28 05:46:18', '2016-01-28 05:46:18'),
(116208, 'Edit-Lesson-Access-Levels-in-BULK', 2056, 231, 'forum_topics', NULL, '2016-01-28 06:14:15', '2016-01-28 06:14:15'),
(116248, 'For-Articles-a-Custom-Picture-insert', 2056, 232, 'forum_topics', NULL, '2016-01-28 06:29:35', '2016-01-28 06:29:35'),
(116292, 'Javascript-into-Text-Widget', 2056, 233, 'forum_topics', NULL, '2016-01-28 07:04:06', '2016-01-28 07:04:06'),
(116770, 'SendGrid-From-Name-isnt-showing-on-emails', 2056, 234, 'forum_topics', NULL, '2016-01-28 13:41:39', '2016-01-28 13:41:39'),
(116778, 'One-or-the-other', 2056, 235, 'forum_topics', NULL, '2016-01-28 14:02:48', '2016-01-28 14:02:48'),
(116829, 'Sound-Not-Working-in-Tutorials-in-SM-Help', 2056, 236, 'forum_topics', NULL, '2016-01-28 14:29:16', '2016-01-28 14:29:16'),
(117166, 'Search-Feature-for-Forums', 2056, 237, 'forum_topics', NULL, '2016-01-28 20:20:47', '2016-01-28 20:20:47'),
(117313, 'Integrate-with-InstaBuilder', 2056, 242, 'forum_topics', NULL, '2016-01-28 22:05:20', '2016-01-28 22:05:20'),
(117454, 'Integrate-with-Trackerly', 2056, 243, 'forum_topics', NULL, '2016-01-28 23:19:43', '2016-01-28 23:19:43'),
(118025, 'Bridgepages-and-Mobiloptin', 2056, 244, 'forum_topics', NULL, '2016-01-29 06:18:14', '2016-01-29 06:18:14'),
(118306, 'Rename-Audio-Button-text', 2056, 245, 'forum_topics', NULL, '2016-01-29 14:01:04', '2016-01-29 14:01:04'),
(118450, 'Products', 2056, 246, 'forum_topics', NULL, '2016-01-29 16:10:59', '2016-01-29 16:10:59'),
(119476, 'How-membership-level-handled', 2056, 247, 'forum_topics', NULL, '2016-01-30 05:57:37', '2016-01-30 05:57:37'),
(119712, 'Sites-Dont-Open', 2056, 249, 'forum_topics', NULL, '2016-01-30 13:36:55', '2016-01-30 13:36:55'),
(119809, 'Apostrophe-Rendering-Problem', 2056, 251, 'forum_topics', NULL, '2016-01-30 15:05:01', '2016-01-30 15:05:01'),
(120842, 'Bridge-PAGE-Backgrounds', 2056, 264, 'forum_topics', NULL, '2016-01-31 06:28:37', '2016-01-31 06:28:37'),
(120843, 'Something-other-than-Youtube-Bridge-page-iframes', 2056, 265, 'forum_topics', NULL, '2016-01-31 06:32:42', '2016-01-31 06:32:42'),
(121241, 'Email-Templates-andor-Clone-Email', 2056, 270, 'forum_topics', NULL, '2016-01-31 15:14:28', '2016-01-31 15:14:28'),
(121243, 'Notifying-Members-about-Comment-and-Forum-Replies', 2056, 271, 'forum_topics', NULL, '2016-01-31 15:16:04', '2016-01-31 15:16:04'),
(121547, '3rd-Party-IFrames-and-pop-ups', 2056, 272, 'forum_topics', NULL, '2016-01-31 17:58:05', '2016-01-31 17:58:05'),
(121555, 'Automated-Funnel-Settings', 2056, 273, 'forum_topics', NULL, '2016-01-31 18:03:49', '2016-01-31 18:03:49'),
(121644, 'Bitcoin-Payment-Gateway-APP', 2056, 274, 'forum_topics', NULL, '2016-01-31 19:19:13', '2016-01-31 19:19:13'),
(121847, 'Domain-mapping-1', 2056, 277, 'forum_topics', NULL, '2016-01-31 21:59:36', '2016-01-31 21:59:36'),
(121859, 'Configure-JVZoo-Grabber-App', 2056, 278, 'forum_topics', NULL, '2016-01-31 22:31:09', '2016-01-31 22:31:09'),
(128266, 'Getting-Started-Registration', 2056, 8243, 'support_articles', NULL, '2016-02-04 20:20:21', '2016-02-04 20:20:21'),
(128521, 'Creating-Your-First-Site', 2056, 8268, 'support_articles', NULL, '2016-02-04 23:10:02', '2016-02-04 23:10:02'),
(128522, 'Customization-Branding', 2056, 8269, 'support_articles', NULL, '2016-02-04 23:14:24', '2016-02-04 23:14:24'),
(129591, 'How-do-I-create-a-lesson-on-my-site', 2056, 8380, 'support_articles', NULL, '2016-02-06 00:08:00', '2016-02-06 00:08:00'),
(129592, 'How-do-I-create-a-module-for-my-site', 2056, 8381, 'support_articles', NULL, '2016-02-06 00:12:43', '2016-02-06 00:12:43'),
(129593, 'How-do-I-change-the-order-of-my-lessons-and-modules', 2056, 8382, 'support_articles', NULL, '2016-02-06 00:13:58', '2016-02-06 00:13:58'),
(129595, 'How-do-I-create-blog-posts-for-my-site', 2056, 8383, 'support_articles', NULL, '2016-02-06 00:16:27', '2016-02-06 00:16:27'),
(129596, 'How-do-I-create-Helpdesk-categories-for-my-site', 2056, 8384, 'support_articles', NULL, '2016-02-06 00:18:30', '2016-02-06 00:18:30'),
(129597, 'How-do-I-create-Helpdesk-articles-for-my-site', 2056, 8385, 'support_articles', NULL, '2016-02-06 00:20:33', '2016-02-06 00:20:33'),
(132746, 'My-course-includes-files-members-can-download-How-do-I-share-these-files', 2056, 8655, 'support_articles', NULL, '2016-02-08 17:17:18', '2016-02-08 17:17:18'),
(132760, 'How-do-I-create-a-Livecast-on-my-site', 2056, 8658, 'support_articles', NULL, '2016-02-08 17:31:24', '2016-02-08 17:31:24'),
(132763, 'How-can-I-create-a-stand-alone-page-for-my-site', 2056, 8659, 'support_articles', NULL, '2016-02-08 17:37:23', '2016-02-08 17:37:23'),
(132787, 'How-do-I-see-the-number-of-times-my-files-have-been-downloaded', 2056, 8664, 'support_articles', NULL, '2016-02-08 17:54:56', '2016-02-08 17:54:56'),
(132788, 'How-do-I-know-how-many-people-have-viewed-my-lesson', 2056, 8665, 'support_articles', NULL, '2016-02-08 17:57:14', '2016-02-08 17:57:14'),
(132789, 'How-do-I-notify-my-members-about-something-important-like-a-sale-or-special-event', 2056, 8666, 'support_articles', NULL, '2016-02-08 18:00:11', '2016-02-08 18:00:11'),
(132790, 'How-do-I-get-back-to-the-main-page-of-my-site', 2056, 8667, 'support_articles', NULL, '2016-02-08 18:04:34', '2016-02-08 18:04:34'),
(132793, 'How-can-I-see-a-list-of-people-who-are-registered-on-my-site', 2056, 8668, 'support_articles', NULL, '2016-02-08 18:07:35', '2016-02-08 18:07:35'),
(132799, 'How-do-I-create-access-levels-for-my-course-content-and-downloads', 2056, 8669, 'support_articles', NULL, '2016-02-08 18:13:17', '2016-02-08 18:13:17'),
(132800, 'How-can-I-grant-access-to-the-protected-content-on-my-site', 2056, 8670, 'support_articles', NULL, '2016-02-08 18:18:07', '2016-02-08 18:18:07'),
(132801, 'Where-can-I-review-the-transactions-for-my-site', 2056, 8671, 'support_articles', NULL, '2016-02-08 18:20:42', '2016-02-08 18:20:42'),
(132802, 'How-can-I-see-the-notes-members-have-made-about-my-lesson', 2056, 8672, 'support_articles', NULL, '2016-02-08 18:24:33', '2016-02-08 18:24:33'),
(132814, 'How-can-I-add-custom-textHTML-or-banners-to-my-Syllabus-page', 2056, 8675, 'support_articles', NULL, '2016-02-08 18:28:34', '2016-02-08 18:28:34'),
(132838, 'How-can-I-upload-a-banner-to-use-on-my-page', 2056, 8680, 'support_articles', NULL, '2016-02-08 18:32:49', '2016-02-08 18:32:49'),
(132840, 'How-can-I-add-an-affiliate-to-my-page', 2056, 8681, 'support_articles', NULL, '2016-02-08 18:35:21', '2016-02-08 18:35:21'),
(132847, 'How-can-I-keep-track-of-groups-of-affiliates-promoting-my-products', 2056, 8682, 'support_articles', NULL, '2016-02-08 18:42:33', '2016-02-08 18:42:33'),
(132851, 'How-can-I-create-a-contest-for-my-affiliates', 2056, 8683, 'support_articles', NULL, '2016-02-08 18:52:00', '2016-02-08 18:52:00'),
(132863, 'How-can-I-update-my-sites-basic-settings', 2056, 8686, 'support_articles', NULL, '2016-02-08 18:54:16', '2016-02-08 18:54:16'),
(132864, 'How-can-I-make-changes-to-my-sites-navigation-menus', 2056, 8687, 'support_articles', NULL, '2016-02-08 18:55:56', '2016-02-08 18:55:56'),
(132865, 'How-can-I-view-a-list-of-the-basic-pages-that-are-included-with-my-website', 2056, 8688, 'support_articles', NULL, '2016-02-08 18:57:59', '2016-02-08 18:57:59'),
(132867, 'How-do-I-add-my-site-to-Smart-Members-public-site-directory', 2056, 8689, 'support_articles', NULL, '2016-02-08 18:59:54', '2016-02-08 18:59:54'),
(132869, 'How-can-add-a-third-party-app-or-tool-to-my-site', 2056, 8690, 'support_articles', NULL, '2016-02-08 19:03:02', '2016-02-08 19:03:02'),
(132881, 'How-can-I-see-which-third-party-apps-I-have-installed-on-my-site', 2056, 8693, 'support_articles', NULL, '2016-02-08 19:04:37', '2016-02-08 19:04:37'),
(132882, 'How-can-I-viewremove-connections-my-site-has-with-other-third-party-sites', 2056, 8694, 'support_articles', NULL, '2016-02-08 19:08:23', '2016-02-08 19:08:23'),
(132883, 'How-do-I-import-videos-from-Vimeo', 2056, 8695, 'support_articles', NULL, '2016-02-08 19:17:26', '2016-02-08 19:17:26'),
(132924, 'jvzoo-affiliate-grabber-set-up-tutorial', 2056, 65815, 'lessons', NULL, '2016-02-08 19:40:32', '2016-02-08 19:40:32'),
(137914, 'How-do-I-integrate-Sendgrid-into-my-site', 2056, 9080, 'support_articles', NULL, '2016-02-11 22:47:15', '2016-02-11 22:47:15'),
(141743, 'Frequently-Asked-Questions', 1, 9430, 'support_articles', NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40'),
(141784, 'Getting-Started', 1, 9471, 'support_articles', NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40'),
(141789, 'Account-Management', 1, 9476, 'support_articles', NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40'),
(141793, 'Access-Issues', 1, 9480, 'support_articles', NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40'),
(141909, 'Site-Management', 2056, 9596, 'support_articles', NULL, '2016-02-14 22:08:42', '2016-02-14 22:08:42'),
(141911, 'support-category', 2056, 9598, 'support_articles', NULL, '2016-02-14 22:08:42', '2016-02-14 22:08:42'),
(142029, 'Smart-Member-How-Tos', 2056, 9698, 'support_articles', NULL, '2016-02-14 22:24:19', '2016-02-14 22:24:19'),
(160143, 'Overview-of-the-Smart-Member-FAQ-Feature-training-section', 2056, 77297, 'lessons', NULL, '2016-02-23 21:44:18', '2016-02-23 21:44:18'),
(160147, 'how-dynamic-smart-member-sites-can-be-and-site-walkthrough', 2056, 77298, 'lessons', NULL, '2016-02-23 21:51:45', '2016-02-23 21:51:45'),
(160148, 'what-other-pages-can-you-build-in-your-smart-member-site', 2056, 77299, 'lessons', NULL, '2016-02-23 21:52:46', '2016-02-23 21:52:46'),
(160154, 'how-to-build-a-forum-in-your-smart-member-site-and-set-access-levels', 2056, 77305, 'lessons', NULL, '2016-02-23 22:08:23', '2016-02-23 22:08:23'),
(160156, 'how-to-build-pages-in-your-smart-member-site', 2056, 77307, 'lessons', NULL, '2016-02-23 22:09:35', '2016-02-23 22:09:35'),
(160158, 'how-to-embed-videos-in-smart-member-pages', 2056, 77309, 'lessons', NULL, '2016-02-23 22:10:43', '2016-02-23 22:10:43'),
(160160, 'how-to-place-audio-files-in-smart-member-pages', 2056, 77311, 'lessons', NULL, '2016-02-23 22:11:39', '2016-02-23 22:11:39'),
(160162, 'how-to-use-transcriptions-with-smart-member-pages-for-seo', 2056, 77313, 'lessons', NULL, '2016-02-23 22:12:45', '2016-02-23 22:12:45'),
(160164, 'differences-between-transcriptions-and-blog-posts-on-smart-member', 2056, 77315, 'lessons', NULL, '2016-02-23 22:13:51', '2016-02-23 22:13:51'),
(160167, 'how-commenting-works-in-smart-member-pages', 2056, 77317, 'lessons', NULL, '2016-02-23 22:15:03', '2016-02-23 22:15:03'),
(160168, 'how-social-sharing-on-smart-member-works', 2056, 77318, 'lessons', NULL, '2016-02-23 22:16:20', '2016-02-23 22:16:20'),
(160173, 'do-you-have-the-option-to-embed-videos-within-main-page-content', 2056, 77323, 'lessons', NULL, '2016-02-23 22:21:10', '2016-02-23 22:21:10'),
(160177, 'how-to-brand-videos-using-vimeo-with-smart-member-and-speed-blogging', 2056, 77325, 'lessons', NULL, '2016-02-23 22:22:41', '2016-02-23 22:22:41'),
(160179, 'how-do-you-load-up-a-video-that-is-not-on-youtube-or-vimeo', 2056, 77327, 'lessons', NULL, '2016-02-23 22:23:34', '2016-02-23 22:23:34');
INSERT INTO `permalinks` (`id`, `permalink`, `site_id`, `target_id`, `type`, `deleted_at`, `updated_at`, `created_at`) VALUES
(160181, 'can-we-add-features-such-as-polls-and-quizzes', 2056, 77329, 'lessons', NULL, '2016-02-23 22:24:27', '2016-02-23 22:24:27'),
(160184, 'how-do-you-set-a-page-as-your-home-page', 2056, 77332, 'lessons', NULL, '2016-02-23 22:25:31', '2016-02-23 22:25:31'),
(160186, 'will-smart-member-integrate-with-shopify', 2056, 77334, 'lessons', NULL, '2016-02-23 22:26:32', '2016-02-23 22:26:32'),
(160188, 'will-there-be-a-google-adsense-integration', 2056, 77336, 'lessons', NULL, '2016-02-23 22:27:26', '2016-02-23 22:27:26'),
(160191, 'how-does-youzign-work-in-smart-member', 2056, 77339, 'lessons', NULL, '2016-02-23 22:28:58', '2016-02-23 22:28:58'),
(160193, 'what-is-so-important-about-the-smart-member-directory', 2056, 77341, 'lessons', NULL, '2016-02-23 22:30:31', '2016-02-23 22:30:31'),
(160195, 'what-is-stripe-how-does-it-work-with-smart-member', 2056, 77343, 'lessons', NULL, '2016-02-23 22:31:27', '2016-02-23 22:31:27'),
(160199, 'Are-People-Making-Lessons-Mostly-With-Vimeo-Youtube-or-Camtasia', 2056, 77347, 'lessons', NULL, '2016-02-23 22:33:05', '2016-02-23 22:33:05'),
(160201, 'what-are-the-requirements-to-be-listed-in-the-directory', 2056, 77349, 'lessons', NULL, '2016-02-23 22:33:57', '2016-02-23 22:33:57'),
(160205, 'do-customers-see-the-smart-member-admin-bar-at-the-top', 2056, 77351, 'lessons', NULL, '2016-02-23 22:35:23', '2016-02-23 22:35:23'),
(160207, 'hiding-the-sign-up-button-and-other-settings', 2056, 77353, 'lessons', NULL, '2016-02-23 22:36:29', '2016-02-23 22:36:29'),
(160209, 'can-you-hide-the-smart-member-logo', 2056, 77355, 'lessons', NULL, '2016-02-23 22:37:20', '2016-02-23 22:37:20'),
(160210, 'how-do-you-create-a-new-site', 2056, 77356, 'lessons', NULL, '2016-02-23 22:38:07', '2016-02-23 22:38:07'),
(160213, 'does-google-calendar-integrate-with-smart-member', 2056, 77358, 'lessons', NULL, '2016-02-23 22:39:07', '2016-02-23 22:39:07'),
(160239, 'clarification-on-the-affiliate-section-of-smart-member', 2056, 77365, 'lessons', NULL, '2016-02-23 22:40:03', '2016-02-23 22:40:03'),
(160240, 'launch-jacking-and-smart-member', 2056, 77366, 'lessons', NULL, '2016-02-23 22:40:50', '2016-02-23 22:40:50'),
(160242, 'do-we-have-to-rewrite-the-content-with-speedblogging', 2056, 77368, 'lessons', NULL, '2016-02-23 22:41:41', '2016-02-23 22:41:41'),
(160244, 'will-smart-member-be-mobile-optimized', 2056, 77370, 'lessons', NULL, '2016-02-23 22:42:35', '2016-02-23 22:42:35'),
(172615, 'fb-groups-tutorial', 2056, 18688, 'custom_pages', NULL, '2016-03-03 00:50:47', '2016-03-03 00:50:47'),
(214356, 'test', 2056, 98139, 'lessons', NULL, '2016-04-04 15:00:01', '2016-04-04 15:00:01'),
(214357, 'testtest', 2056, 98140, 'lessons', NULL, '2016-04-04 21:22:15', '2016-04-04 21:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `permalink_stats`
--

CREATE TABLE IF NOT EXISTS `permalink_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `permalink_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` longtext,
  `note` text,
  `embed_content` text,
  `featured_image` text,
  `access_level_type` int(11) NOT NULL DEFAULT '1',
  `access_level_id` int(11) NOT NULL DEFAULT '1',
  `permalink` text NOT NULL,
  `discussion_settings_id` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_published_date` datetime DEFAULT NULL,
  `published_date` datetime NOT NULL,
  `preview_schedule` tinyint(4) DEFAULT '0',
  `transcript_content_public` tinyint(1) DEFAULT '0',
  `transcript_content` text,
  `transcript_button_text` varchar(50) DEFAULT 'Transcript',
  `audio_file` text,
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  `show_content_publicly` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts_categories`
--

CREATE TABLE IF NOT EXISTS `posts_categories` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts_tags`
--

CREATE TABLE IF NOT EXISTS `posts_tags` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `remote_logins`
--

CREATE TABLE IF NOT EXISTS `remote_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remote_id` varchar(40) NOT NULL,
  `source` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_source` (`remote_id`,`source`),
  KEY `remote_id` (`remote_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` bigint(22) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `role_type` int(20) unsigned NOT NULL,
  `total_visits` int(10) unsigned DEFAULT '0',
  `total_lessons` int(10) unsigned DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `index_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=140735 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `user_id`, `site_id`, `company_id`, `role_type`, `total_visits`, `total_lessons`, `deleted_at`, `created_at`, `updated_at`) VALUES
(75, 10, 1, 0, 1, 71, 14, NULL, '2015-08-26 11:38:39', '2016-01-25 17:55:02'),
(7769, 10, 2056, 0, 1, 387, 196, NULL, '2015-06-22 20:17:14', '2016-04-05 22:16:24');

-- --------------------------------------------------------

--
-- Table structure for table `role_types`
--

CREATE TABLE IF NOT EXISTS `role_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `role_types`
--

INSERT INTO `role_types` (`id`, `role_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Primary Owner', NULL, '2015-09-24 06:03:45', '0000-00-00 00:00:00'),
(2, 'Owner', NULL, '2015-09-24 06:03:45', '0000-00-00 00:00:00'),
(3, 'Manager', NULL, '2015-09-24 06:03:45', '0000-00-00 00:00:00'),
(4, 'Admin', NULL, '2015-09-24 06:03:45', '0000-00-00 00:00:00'),
(5, 'Agent', NULL, '2015-09-24 06:03:45', '0000-00-00 00:00:00'),
(6, 'Member', NULL, '2015-09-24 06:03:45', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `seo_settings`
--

CREATE TABLE IF NOT EXISTS `seo_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `link_type` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shared_grants`
--

CREATE TABLE IF NOT EXISTS `shared_grants` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `access_level_id` bigint(22) NOT NULL,
  `grant_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `hash` varchar(255) DEFAULT NULL,
  `blog_format` varchar(10) DEFAULT 'thumbnail',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_subdomain_unique` (`subdomain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14086 ;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`id`, `subdomain`, `domain`, `name`, `template_id`, `user_id`, `domain_mask`, `total_members`, `total_lessons`, `total_revenue`, `stripe_user_id`, `stripe_access_token`, `stripe_integrated`, `type`, `company_id`, `facebook_secret_key`, `facebook_app_id`, `deleted_at`, `created_at`, `updated_at`, `cloneable`, `clone_id`, `syllabus_format`, `show_syllabus_toggle`, `is_completed`, `completed_nodes`, `progress`, `intention`, `welcome_content`, `locked`, `hash`, `blog_format`) VALUES
(1, 'training', '', 'Training', 0, 1, NULL, 2597, 169, 683423, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2015-06-12 16:36:05', '2016-03-21 19:13:41', 0, 0, 'list', 1, 0, 'product,blog,menu,sendgrid,lesson,paypal', 6, 0, NULL, 0, '4e9811887f470a30cbd749b0fe646466', 'thumbnail'),
(2056, 'help', '', 'Help', 0, 10, NULL, 3007, 77, 0, NULL, NULL, 0, 0, 10372, NULL, NULL, NULL, '2015-06-22 20:17:14', '2016-04-04 21:22:15', 0, 0, 'list', 1, 0, NULL, 0, 0, '<p><iframe src="https://player.vimeo.com/video/153566749" width="500" height="375" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe><br></p>', 0, '438dea4540707e048724a77722b1b3d7', 'thumbnail');

-- --------------------------------------------------------

--
-- Table structure for table `sites_ads`
--

CREATE TABLE IF NOT EXISTS `sites_ads` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `display` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sites_custom_roles`
--

CREATE TABLE IF NOT EXISTS `sites_custom_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sites_custom_roles_capabilities`
--

CREATE TABLE IF NOT EXISTS `sites_custom_roles_capabilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `capability` varchar(255) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sites_footer_menu_items`
--

CREATE TABLE IF NOT EXISTS `sites_footer_menu_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26999 ;

--
-- Dumping data for table `sites_footer_menu_items`
--

INSERT INTO `sites_footer_menu_items` (`id`, `site_id`, `url`, `label`, `deleted_at`, `created_at`, `updated_at`, `sort_order`) VALUES
(44, 1, '/support', 'Contact Support', NULL, '2015-08-26 11:38:42', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sites_menu_items`
--

CREATE TABLE IF NOT EXISTS `sites_menu_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `custom_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `open_in_new_tab` tinyint(1) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37047 ;

--
-- Dumping data for table `sites_menu_items`
--

INSERT INTO `sites_menu_items` (`id`, `site_id`, `url`, `label`, `icon`, `custom_icon`, `open_in_new_tab`, `deleted_at`, `created_at`, `updated_at`, `sort_order`) VALUES
(1291, 1, 'http://help.smartmember.site/', 'Tutorial Videos', 'fa-graduation-cap', 'fa fa-play-circle', NULL, '2015-12-23 07:43:50', '2015-08-26 11:38:41', '2015-12-23 07:43:50', 0),
(1292, 1, 'livecast/smart-member-orientation', 'New Members', 'fa-user', '', NULL, '2015-10-27 00:09:20', '2015-08-26 11:38:41', '2015-10-27 00:09:20', 1),
(1293, 1, 'livecast/product-launch-workshop', 'P. Launch Workshops', 'fa-flask', '', NULL, '2015-10-27 00:09:15', '2015-08-26 11:38:41', '2015-10-27 00:09:15', 2),
(1294, 1, '/support', 'Support', 'fa-support', '', NULL, NULL, '2015-08-26 11:38:41', '2016-01-24 00:32:45', 0),
(1295, 1, 'http://www.smartmember.com/blog', 'Blog', 'fa-home', '', NULL, NULL, '2015-08-26 11:38:41', '2016-01-24 00:32:49', 1),
(1351, 2056, 'lessons', 'Tutorials', 'student icon', '', NULL, NULL, '2015-08-26 11:38:41', '2016-03-14 16:51:00', 0),
(6643, 1, '', '', '', '', NULL, '2015-09-07 02:20:35', '2015-09-07 02:20:27', '2015-09-07 02:20:35', 0),
(6692, 2056, 'blog', 'The SM Blog', 'rss icon', '', NULL, NULL, '2015-09-09 19:19:40', '2016-03-14 16:51:00', 1),
(6716, 2056, 'http://docs.smartmember.com', 'Docs', 'book icon', '', NULL, NULL, '2015-09-10 18:02:02', '2016-03-14 16:51:06', 2),
(10017, 2056, 'support-ticket', 'Submit a Ticket', 'ticket icon', '', NULL, NULL, '2016-01-10 20:13:30', '2016-03-14 16:51:06', 3),
(10202, 2056, '/support-tickets', 'All my tickets', 'ticket icon', '', NULL, NULL, '2016-01-11 02:30:09', '2016-03-14 16:51:58', 4),
(18977, 1, 'http://my.smartmember.com', 'SMART MEMBER 2.0 LOGIN HERE!', 'checkmark icon', '', NULL, NULL, '2016-01-24 00:32:32', '2016-01-24 00:33:15', 2);

-- --------------------------------------------------------

--
-- Table structure for table `sites_roles`
--

CREATE TABLE IF NOT EXISTS `sites_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `access_level_id` bigint(20) DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=370016 ;

--
-- Dumping data for table `sites_roles`
--

INSERT INTO `sites_roles` (`id`, `type`, `site_id`, `user_id`, `access_level_id`, `expired_at`, `deleted_at`, `updated_at`, `created_at`) VALUES
(2, 'owner', 1, 10, NULL, NULL, NULL, '0000-00-00 00:00:00', '2015-10-16 01:07:58'),
(1565, 'owner', 2056, 10, NULL, NULL, NULL, '0000-00-00 00:00:00', '2015-10-16 01:08:22'),
(94806, 'member', 1, 10, 652, NULL, NULL, '0000-00-00 00:00:00', '2015-07-09 02:27:32'),
(97391, 'member', 1, 10, 160, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', '2015-07-25 07:08:50'),
(173877, 'member', 2056, 10, NULL, NULL, NULL, '2016-01-11 16:01:21', '2016-01-11 16:01:21'),
(178909, 'member', 2056, 10, 2682, NULL, NULL, '2016-01-13 16:12:52', '2016-01-13 16:12:52');

-- --------------------------------------------------------

--
-- Table structure for table `sites_templates_data`
--

CREATE TABLE IF NOT EXISTS `sites_templates_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `element_type_id` int(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `site_meta_data`
--

CREATE TABLE IF NOT EXISTS `site_meta_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `data_type` int(11) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7446031 ;

--
-- Dumping data for table `site_meta_data`
--

INSERT INTO `site_meta_data` (`id`, `site_id`, `company_id`, `data_type`, `key`, `value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1245, 1, 0, 0, 'course_title', 'Welcome to SmartMember Training!', NULL, '2015-06-15 03:21:20', '0000-00-00 00:00:00'),
(1246, 1, 0, 0, 'site_logo', 'https://s3.amazonaws.com/imbmediab/uploads/befaedb6ae6101887507209a1bfa5b5f/SMTraining.png', NULL, '2015-06-15 10:04:10', '2016-01-19 23:53:21'),
(1247, 1, 0, 0, 'sales_page_enabled', '1', NULL, '2015-06-18 01:27:33', '0000-00-00 00:00:00'),
(1248, 1, 0, 0, 'sales_page_embed', '', NULL, '2015-06-18 01:27:33', '0000-00-00 00:00:00'),
(1249, 1, 0, 0, 'sales_page_content', '', NULL, '2015-06-18 01:27:33', '2015-09-07 19:42:49'),
(1250, 1, 0, 0, 'sales_page_outro', '<p><br /></p>', NULL, '2015-06-18 01:27:33', '0000-00-00 00:00:00'),
(1301, 2056, 0, 0, 'course_title', 'Smart Member Video Tutorials', NULL, '2015-06-22 20:17:14', '2015-09-08 21:25:43'),
(3150, 1, 0, 0, 'site_title', 'Smart Member', NULL, '2015-06-26 14:19:22', '0000-00-00 00:00:00'),
(8614, 2056, NULL, 0, 'show_copyright', '1', NULL, '2015-09-15 21:45:57', '2016-03-18 03:32:44'),
(8615, 2056, NULL, 0, 'show_powered_by', '1', NULL, '2015-09-15 21:45:57', '2015-09-15 21:45:57'),
(8482, 1, NULL, 0, 'default_module_sort_order', '1', NULL, '2015-09-14 21:39:36', '2015-09-14 21:39:36'),
(8484, 1, NULL, 0, 'isOpen', '0', NULL, '2015-09-14 21:55:21', '2015-09-22 07:05:24'),
(8485, 1, NULL, 0, 'module_text', '=>', NULL, '2015-09-14 21:55:21', '2015-09-14 21:55:21'),
(8486, 1, NULL, 0, 'primary_theme_color', '#42d692', NULL, '2015-09-14 21:56:32', '2015-09-14 21:56:32'),
(8487, 1, NULL, 0, 'secondary_theme_color', '#4986e7', NULL, '2015-09-14 21:56:32', '2015-09-14 21:56:32'),
(7986, 1, NULL, 0, 'theme', 'default', NULL, '2015-09-03 02:45:40', '2015-10-05 04:23:44'),
(8161, 2056, NULL, 0, 'isOpen', '0', NULL, '2015-09-08 21:26:14', '2016-01-03 23:06:41'),
(8162, 2056, NULL, 0, 'site_logo', 'https://s3.amazonaws.com/imbmediab/uploads/8f62e728327d788a65164a75b8f9865d/smart%20member%20help.png', NULL, '2015-09-08 21:26:14', '2016-01-03 23:06:41'),
(8163, 2056, NULL, 0, 'homepage_url', 'lessons', NULL, '2015-09-08 21:26:14', '2015-11-10 05:58:58'),
(8164, 2056, NULL, 0, 'theme', 'default', NULL, '2015-09-08 21:27:51', '2015-11-23 16:03:30'),
(8204, 2056, NULL, 0, 'default_module_sort_order', '10', NULL, '2015-09-09 15:38:10', '2015-10-20 17:34:13'),
(13829, 2056, NULL, 0, 'front_page_featured_image', 'https://s3.amazonaws.com/imbmediab/uploads/606fd460b0fcb874352d7d28d0c77fa7/sm_help.jpg', NULL, '2015-11-10 08:13:31', '2015-11-10 08:16:54'),
(13830, 2056, NULL, 0, 'default_category_sort_order', '1', NULL, '2015-11-10 08:22:47', '2015-11-10 08:22:47'),
(13147, 1, NULL, 0, 'homepage_url', 'info', NULL, '2015-11-10 05:19:09', '0000-00-00 00:00:00'),
(13498, 1, NULL, 0, 'show_syllabus', '1', NULL, '2015-11-10 05:19:46', '0000-00-00 00:00:00'),
(17600, 2056, NULL, 0, 'site_background_color', '', NULL, '2015-12-07 22:09:13', '2016-01-10 20:11:25'),
(17601, 2056, NULL, 0, 'navigation_background_color', '', NULL, '2015-12-07 22:09:13', '2016-01-10 20:11:25'),
(17602, 2056, NULL, 0, 'navigation_text_color', '#444444', NULL, '2015-12-07 22:09:13', '2016-01-03 23:08:06'),
(17603, 2056, NULL, 0, 'section_background_color', '', NULL, '2015-12-07 22:09:13', '2016-01-10 20:11:25'),
(17604, 2056, NULL, 0, 'headline_text_color', '#cc0000', NULL, '2015-12-07 22:09:13', '2016-01-03 22:56:00'),
(17605, 2056, NULL, 0, 'module_label_text_color', '#FFFFFF', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17606, 2056, NULL, 0, 'module_label_background_color', '#cc0000', NULL, '2015-12-07 22:09:13', '2016-01-03 22:56:00'),
(17607, 2056, NULL, 0, 'main_button_text_color', '#FFFFFF', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17608, 2056, NULL, 0, 'main_button_background_color', '#cc0000', NULL, '2015-12-07 22:09:13', '2016-01-03 22:56:00'),
(17609, 2056, NULL, 0, 'site_top_background_color', '', NULL, '2015-12-07 22:09:13', '2016-01-03 22:54:43'),
(17610, 2056, NULL, 0, 'site_middle_background_color', '#f6f6f6', NULL, '2015-12-07 22:09:13', '2016-01-03 23:08:06'),
(17611, 2056, NULL, 0, 'site_bottom_background_color', '#f6f6f6', NULL, '2015-12-07 22:09:13', '2016-01-10 20:09:54'),
(17612, 2056, NULL, 0, 'logo_position', 'left', NULL, '2015-12-07 22:09:13', '2016-01-10 20:15:29'),
(17613, 2056, NULL, 0, 'show_nav_icons', '1', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17614, 2056, NULL, 0, 'icon_position', 'top', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17615, 2056, NULL, 0, 'navigation_style', '', NULL, '2015-12-07 22:09:13', '2016-01-10 20:12:06'),
(17616, 2056, NULL, 0, 'navigation_location', '', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17617, 2056, NULL, 0, 'logo_size', 'big', NULL, '2015-12-07 22:09:13', '2015-12-07 22:11:14'),
(17618, 2056, NULL, 0, 'logo_border', '', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17619, 2056, NULL, 0, 'sidebar_position', 'right', NULL, '2015-12-07 22:09:13', '2016-01-10 20:13:14'),
(17620, 2056, NULL, 0, 'module_label_style', 'ribbon', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17621, 2056, NULL, 0, 'module_label_position', 'left', NULL, '2015-12-07 22:09:13', '2015-12-07 22:09:13'),
(17622, 2056, NULL, 0, 'icon_size', 'fa-3x', NULL, '2015-12-07 22:09:13', '2016-01-10 20:09:32'),
(17643, 2056, NULL, 0, 'navigation_icon_color', '#cc0000', NULL, '2015-12-07 22:11:15', '2016-01-03 22:56:30'),
(17644, 2056, NULL, 0, 'site_background_image', 'https://s3.amazonaws.com/imbmediab/uploads/06a4b26dcfabce3546b14e0d90b09edf/7_Wallpapers_for_L20.png', NULL, '2015-12-07 22:17:07', '2016-01-10 20:09:32'),
(17645, 2056, NULL, 0, 'page_background_style', 'fluid', NULL, '2015-12-07 22:30:48', '2016-01-10 20:09:32'),
(26656, 2056, NULL, 0, 'footer_text_color', '#1b1c1d', NULL, '2016-01-03 22:53:44', '2016-01-03 22:53:44'),
(26657, 2056, NULL, 0, 'cap_icon', 'fa fa-graduation-cap', NULL, '2016-01-03 23:08:06', '2016-01-03 23:08:06'),
(460771, 1, NULL, 0, 'turn_optin_to_member', '0', NULL, '2016-01-12 16:52:22', '2016-01-12 16:52:22'),
(986908, 2056, NULL, 0, 'default_syllabus_closed', '0', NULL, '2016-01-17 09:32:07', '2016-03-18 03:32:44'),
(986909, 2056, NULL, 0, 'access_deny_image', 'https://s3.amazonaws.com/imbmediab/uploads/68e2e16fe9a8b1d95de4fc3e40288179/smart%20member%20support%20center.png', NULL, '2016-01-17 09:32:07', '2016-01-17 09:32:07'),
(986910, 2056, NULL, 0, 'fb_share_image', 'https://s3.amazonaws.com/imbmediab/uploads/6ed8f0a10820dc5342173b9c0b562fdf/smart%20member%20support%20center.png', NULL, '2016-01-17 09:32:07', '2016-01-17 09:32:07'),
(986911, 2056, NULL, 0, 'fb_share_title', 'Smart Member Support Center and Help Forum', NULL, '2016-01-17 09:32:07', '2016-01-17 09:32:07'),
(986912, 2056, NULL, 0, 'fb_share_description', 'Have questions about Smart Member?  Ask them here!  Browse the Smart Member forum and post topics for feature requests, bug reports, and more!', NULL, '2016-01-17 09:32:07', '2016-01-17 09:32:07'),
(1334907, 1, NULL, 0, 'default_syllabus_closed', '0', NULL, '2016-01-19 23:53:21', '2016-01-19 23:53:21'),
(1334908, 1, NULL, 0, 'welcome_email_subject', 'Welcome to %site_name%', NULL, '2016-01-19 23:53:21', '2016-01-19 23:53:21'),
(1334909, 1, NULL, 0, 'welcome_email_content', '<h2 style="color:#2ab27b;line-height:30px;margin-bottom:12px;margin:0 0 12px">You''re in!</h2><p style="font-size:18px;line-height:24px;margin:0 0 16px;">You''re now a member at <strong>%site_name%</strong> - welcome!</p><p style="font-size:20px;line-height:26px;margin:0 0 16px"><strong>Ready to login?</strong> Below you''ll find your login details and a link to get started.</p><hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">%login_details%', NULL, '2016-01-19 23:53:21', '2016-01-19 23:53:21'),
(6930250, 2056, NULL, 0, 'facebook_conversion_pixel', '346575762160624', NULL, '2016-03-18 03:32:44', '2016-03-18 03:32:44'),
(6930249, 2056, NULL, 0, 'welcome_email_content', '<h2 style="color:#2ab27b;line-height:30px;margin-bottom:12px;margin:0 0 12px">You''re in!</h2><p style="font-size:18px;line-height:24px;margin:0 0 16px;">You''re now a member at <strong>%site_name%</strong> - welcome!</p><p style="font-size:20px;line-height:26px;margin:0 0 16px"><strong>Ready to login?</strong> Below you''ll find your login details and a link to get started.</p><hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">%login_details% <br>', NULL, '2016-03-18 03:32:44', '2016-03-18 03:32:44'),
(6930248, 2056, NULL, 0, 'welcome_email_subject', 'Welcome to %site_name%', NULL, '2016-03-18 03:32:44', '2016-03-18 03:32:44'),
(6930246, 2056, NULL, 0, 'nav_items_dropdown', '0', NULL, '2016-03-18 03:32:44', '2016-03-18 03:32:44'),
(6930247, 2056, NULL, 0, 'thankyou_use_custom', '0', NULL, '2016-03-18 03:32:44', '2016-03-18 03:32:44');

-- --------------------------------------------------------

--
-- Table structure for table `site_notices`
--

CREATE TABLE IF NOT EXISTS `site_notices` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime DEFAULT NULL,
  `on` tinyint(1) DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `site_notices_seen`
--

CREATE TABLE IF NOT EXISTS `site_notices_seen` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_notice_id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `smart_links`
--

CREATE TABLE IF NOT EXISTS `smart_links` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(22) NOT NULL,
  `title` text NOT NULL,
  `permalink` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'random',
  `last_url_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `permalink` (`permalink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `smart_link_urls`
--

CREATE TABLE IF NOT EXISTS `smart_link_urls` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `smart_link_id` bigint(22) NOT NULL,
  `url` text NOT NULL,
  `visits` bigint(22) DEFAULT NULL,
  `weight` bigint(22) DEFAULT NULL,
  `order` bigint(22) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `smart_link_id` (`smart_link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `special_pages`
--

CREATE TABLE IF NOT EXISTS `special_pages` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `continue_refund_color` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `speed_blogs`
--

CREATE TABLE IF NOT EXISTS `speed_blogs` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `speed_posts`
--

CREATE TABLE IF NOT EXISTS `speed_posts` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id` (`user_id`,`blog_id`,`wp_post_id`),
  KEY `user_id` (`user_id`),
  KEY `blog_id` (`blog_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `support_articles`
--

CREATE TABLE IF NOT EXISTS `support_articles` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `permalink` text NOT NULL,
  `display` varchar(255) DEFAULT 'default',
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `parent_id` bigint(22) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `always_show_featured_image` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16153 ;

--
-- Dumping data for table `support_articles`
--

INSERT INTO `support_articles` (`id`, `site_id`, `company_id`, `author_id`, `category_id`, `title`, `content`, `embed_content`, `featured_image`, `permalink`, `display`, `status`, `parent_id`, `sort_order`, `deleted_at`, `created_at`, `updated_at`, `always_show_featured_image`) VALUES
(25, 1, 1, 1, 23, 'Domain Mapping', '<p>Instead of using the default http://yoursite.smartmember.com URLs for your sites, you can "map" a custom domain to each one! This will allow you to brand your membership sites how you want - like http://yoursite.com for example.</p><h2>When will it be ready?</h2><p>This feature is VERY high on our priority list and has been requested by many, so it will be rolling out <span style="font-weight:bold;">very</span> soon.</p>', '', '', 'domain-mapping', 'default', 'published', 9430, 3, NULL, '2015-06-18 16:09:23', '0000-00-00 00:00:00', 0),
(79, 1, 1, 10, 66, 'Access Levels', '<p>One of the very first things you''ll want to do when setting up a site, is to give it''s content a couple <span style="font-weight:bold;">Access Levels</span>.</p><p>An <span style="font-weight:bold;">Access Level</span> is a type of restriction on private / protected content that a user must have "permission" to be able to view / download it. An example of an <span style="font-weight:bold;">Access Level</span> might be called "Premium", where lessons, downloads, and livecasts can be set to be viewable only by users who are allowed to access the "Premium" <span style="font-weight:bold;">Access Level</span>.</p><h2>Creating an Access Level</h2><p>To create an <span style="font-weight:bold;">Access Level</span>, first choose which site you''d like to administrate by going to the My Sites page then clicking the "Dashboard" button on the site you''re interested in.</p><p><img src="https://imbmediab.s3.amazonaws.com/1/3563bdabd679ff4a6d93fa2743beb455/txye0726qje9uckh8dkzxg7k5cpd1q.png" alt="txye0726qje9uckh8dkzxg7k5cpd1q.png" /></p><p>From there, head over to the <span style="font-weight:bold;">Access Level</span> page as seen below. To create a new one, simply click the "Create New Access Level" button.</p><p><img src="https://imbmediab.s3.amazonaws.com/1/e8d246596d1ce05ee8fb727a21fda8c1/jf6cfyhfjylfsieubnqj2cxiabk4x2.png" alt="jf6cfyhfjylfsieubnqj2cxiabk4x2.png" /><br /></p><p>From there, you''ll be presented with a form to fill out to customize the details of your new <span style="font-weight:bold;">Access Level</span>. At the top you''ll see an area for General Settings, and below it areas for specific Payment Gateway settings, like JVZoo and Stripe options. </p><h2>Access Level Options</h2><p>The General options let you customize the name of the <span style="font-weight:bold;">Access Level</span> - such as "Premium" or "Pro", "Silver" or "Gold". You can also specify an Information Url, which lets you plug in a link to your sales page / information page when customers try to get access to restricted content.</p><p>Lastly, you can specify the Redirect Url - also known as the "Thank You" url - which is where your customers will be redirected to after purchasing this <span style="font-weight:bold;">Access Level</span>.</p><p>When you''re done, click "Save Changes". You can now select your new <span style="font-weight:bold;">Access Level</span> when administrating other ares of your site and content creation to lock it down!</p>', '', '', 'Access-Levels-2015-08-26T11:38:46+00:00', 'default', 'published', 9471, 8, NULL, '2015-07-03 13:17:33', '0000-00-00 00:00:00', 0),
(83, 1, 1, 10, 23, 'Refund Policy & Procedure', '<div class="de elHeadlineWrapper de-editable" style="color:rgb(47,47,47);font-family:''Open Sans'', sans-serif;font-size:14px;line-height:20px;margin-top:0px;"><div class="ne elHeadline hsSize3 lh3 elMargin0 elBGStyle0 hsTextShadow0" style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:32px;line-height:normal;color:rgb(44,62,80);"><span style="font-weight:700;">Our 30-Day Money Back Guarantee</span></div></div><div class="de elTextBlockWrapper elMargin0 de-editable" style="margin-top:20px;color:rgb(47,47,47);font-family:''Open Sans'', sans-serif;font-size:14px;line-height:20px;"><div class="elTextblock" style="color:rgb(44,62,80);"><p style="text-align:justify;"><span style="font-weight:700;">We are more interested in having you as a life-long customer with us, rather than just making a sale. </span></p><p style="text-align:justify;">So when you purchase Smart Member™, you will have a chance to watch our team steadily improve the product right before your eyes, constantly releasing new features, fixing bugs, handling support requests, and most importantly, Listening To Your Feedback!</p><p style="text-align:justify;">If there is anything you can think of for us to improve this product to help you have more success as an entrepreneur, please contact us and let us know! Give us a chance to fix any bugs you might come across, or add any feature requests you might have. We live for that stuff!</p><p style="text-align:justify;">And if for some reason, even after our bug fixes and added features, if you prefer to de-activate your lifetime access, you can take advantage of our risk-free 30-day money back guarantee by submitting a refund request <a href="/support-ticket/create?refund">here</a>.</p></div></div>', '', '', 'Refund-Policy-&-Procedure-2015-08-26T11:38:46+00:00', 'default', 'published', 9430, 5, NULL, '2015-07-03 23:30:32', '0000-00-00 00:00:00', 0),
(84, 1, 1, 10, 71, 'Associating Your Facebook Account with Smart Member', '<p>When first being asked to create an account or login, you can fill out the name / email / password fields manually, or click the "Connect with Facebook" button. <span style="line-height:1.5;">Sometimes, you''ll already have an account created but you''ll want to connect Facebook to it (or set a password for your account that''s only ever connected through Facebook. </span></p><p><span style="line-height:1.5;">We''ll be rolling out the ability to edit connected accounts &amp; emails / passwords within the Smart Member administration area very soon. Until then, feel free to send a ticket to <a href="/support-ticket/create" target="_blank">support</a> if you need to re-associate your account(s).</span></p>', '', '', 'Associating-Your-Facebook-Account-with-Smart-Member-2015-08-26T11:38:46+00:00', 'default', 'published', 9476, 14, NULL, '2015-07-03 23:39:23', '0000-00-00 00:00:00', 0),
(85, 1, 1, 10, 66, 'Lessons', '<p>Lessons represent the main type of "content" your membership site will host. If you''d like to have protected articles, content, posts, embeds, etc. then you can create them as Lessons, then restrict them by choosing an Access Level.</p><p>Lessons are one type of protected content - other types are Livecasts and Downloads to name a few. </p><p>When creating a Lesson, you may enter it''s text / html content, embed a video, add some notes, attach a transcript, attach an audio download, control how comments are displayed, change how social sharing will be shown, and more.</p><p>Lessons are grouped together by Modules, and are displayed publicly as a Syllabus</p>', '', '', 'Lessons-2015-08-26T11:38:46+00:00', 'default', 'published', 9471, 9, NULL, '2015-07-03 23:44:58', '0000-00-00 00:00:00', 0),
(88, 1, 1, 3984, 23, 'Modules and Lessons Do Not Show Up', '<p><span><span style="font-size:14.6666666666667px;font-family:Arial;color:rgb(34,34,34);vertical-align:baseline;white-space:pre-wrap;background-color:rgb(250,250,250);"><span><span style="font-size:18px;vertical-align:baseline;background-color:rgb(255,255,255);">"Some of the modules and lessons do not show up in the Syllabus Creator even if they are created and show up in the Lessons and Modules tab."</span></span></span></span></p><p><span><span style="font-size:14.6666666666667px;font-family:Arial;color:rgb(34,34,34);vertical-align:baseline;white-space:pre-wrap;background-color:rgb(250,250,250);"><span><span style="font-size:14.6666666666667px;vertical-align:baseline;background-color:rgb(255,255,255);"><br /></span></span><span style="font-size:18px;">This sounds like this may be an </span><span style="font-weight:bold;font-size:18px;">accessibility issue</span><span style="font-size:18px;">. For instance, under Course Content, in the Syllabus Creator, try setting everything to public and test to see if the lessons show up. If so, then you will want to configure access levels and what members have access too.</span></span></span></p>', '', '', 'Modules-and-Lessons-Do-Not-Show-Up-2015-08-26T11:38:46+00:00', 'default', 'published', 9430, 4, NULL, '2015-07-06 11:57:45', '0000-00-00 00:00:00', 0),
(90, 1, 1, 3984, 75, 'Access to Bridge Page Bonus', '<p><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:18px;line-height:21px;">If you purchased the <span style="text-decoration:underline;">lifetime membership</span> to SmartMember then y</span><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;line-height:21px;font-size:18px;">ou will be getting </span><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;line-height:21px;font-weight:bold;font-size:18px;">"LIFETIME ACCESS"</span><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;line-height:21px;font-size:18px;"> to our hosted version of BridgePages. </span></p><p><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:18px;line-height:21px;">This product launched<span style="font-weight:bold;"> </span>as a monthly membership, so you will have lifetime access which means you will have unlimited pages, unlimited traffic, with no monthly fees.</span><br /></p><p><br style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:21px;background-color:rgb(250,250,250);" /><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:21px;background-color:rgb(250,250,250);"><span style="font-size:18px;">There will also be an <span style="font-weight:bold;">Orientation Webinar</span> for all members, including a walkthrough of BridgePages and how to get access to start using it available.</span><span style="font-size:18px;"> </span></span><br style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:21px;background-color:rgb(250,250,250);" /><br style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:21px;background-color:rgb(250,250,250);" /><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:18px;line-height:21px;background-color:rgb(250,250,250);text-decoration:underline;">If you did not receive the email, you will be able to find BridgePages through this link:</span></p><p><a href="http://my.bridgepages.net/" target="_blank" style="color:rgb(42,128,185);font-family:Lato, appleLogo, sans-serif;font-size:15px;line-height:22px;background-color:rgb(255,255,255);"><span style="font-size:36px;"><span style="font-size:24px;">h</span><span style="font-size:24px;">ttp://my.bridgepages.net</span></span></a><span style="color:rgb(34,34,34);font-family:Helvetica, Arial, sans-serif;font-size:18px;line-height:21px;background-color:rgb(250,250,250);text-decoration:underline;"><br /></span></p>', '', '', 'Access-to-Bridge-Page-Bonus-2015-08-26T11:38:46+00:00', 'default', 'published', 9480, 13, NULL, '2015-07-06 12:02:24', '0000-00-00 00:00:00', 0),
(91, 1, 1, 3984, 23, 'Creating Multiple Admins', '<p><span style="font-size:18px;">If you are working to create a site with a business partner or multiple people, you are able to grant them access to your site as an admin.</span></p><p><span style="font-size:18px;"><br /></span></p><p><span style="font-size:18px;">The person, or people, you are trying to add must go into the sub site you are building and log in, once they have logged in through that, you will see an access ledger. By clicking on <span style="font-weight:bold;">Members</span> and then <span style="font-weight:bold;">Member Manager</span>, next to the persons name should be an admin button.</span></p><p><img src="http://content.screencast.com/users/BrianCain/folders/Jing/media/834135e8-02cf-4284-b2f9-0c590691031d/2015-07-06_1302.png" style="width:849px;" alt="2015-07-06_1302.png" /><span style="font-size:18px;"><br /></span></p><p><span style="font-size:18px;"><br /></span></p><p><span style="font-size:18px;">Once that person is added as an admin they will have <span style="text-decoration:underline;">full access</span>, but will not be able to add others as an admin, only the main admin will be able to do this.</span></p>', '', '', 'Creating-Multiple-Admins-2015-08-26T11:38:46+00:00', 'default', 'published', 9430, 2, NULL, '2015-07-06 12:36:19', '0000-00-00 00:00:00', 0),
(92, 1, 1, 3984, 75, 'Trouble Creating Site', '<span style="font-size:18px;">If you purchased Smart Member, but did not use a </span><span style="text-decoration:underline;font-size:18px;">JVZoo link</span><span style="font-size:18px;">, you might have an issue with creating and managing sites. If this is the case for you, reach out under the "<span style="font-weight:bold;">Contact Our Support</span>" tab and we will be able to manually grant you access.</span>', '', '', 'Trouble-Creating-Site-2015-08-26T11:38:46+00:00', 'default', 'published', 9480, 12, NULL, '2015-07-06 15:29:56', '0000-00-00 00:00:00', 0),
(94, 1, 1, 3984, 66, 'Audio Hosting', '<span></span><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.8;background-color:rgb(250,250,250);">If you are looking to upload a transcription, go to "</span><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.8;font-weight:bold;">Create a Lesson</span><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.8;background-color:rgb(250,250,250);">" or go to an already built lesson and you will see the option to "<span style="font-weight:bold;">upload an audio</span><span style="font-style:italic;">"</span>. </span></p><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.8;background-color:rgb(250,250,250);"><br /></span></p><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><img src="https://imbmediab.s3.amazonaws.com/1/967b38dfaedd202d13147c0242c9d7c3/mz3fzol443iickv5zwlsl09qcpo70w.png" alt="mz3fzol443iickv5zwlsl09qcpo70w.png" /></p><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><br /><img src="https://imbmediab.s3.amazonaws.com/1/ebb1b657271fc26a44a9c169072eb2d9/i2tkejtqk1coljq9kz4r2la07pgfp6.png" alt="i2tkejtqk1coljq9kz4r2la07pgfp6.png" /></p><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><br /><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.5;background-color:rgb(250,250,250);"><br /></span></p><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.5;background-color:rgb(250,250,250);"><br /></span></p><p dir="ltr" style="line-height:1.7999999999999998;margin-top:0pt;margin-bottom:0pt;"><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.5;background-color:rgb(250,250,250);">If you are looking to embed into Smart Member, then </span><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.5;text-decoration:underline;">Soundcloud</span><span style="color:rgb(34,34,34);font-family:Arial;font-size:18px;white-space:pre-wrap;line-height:1.5;background-color:rgb(250,250,250);"> might be a good option and the embed will need to be in the &lt;Iframe&gt; coding.</span></p>', '', '', 'Audio-Hosting-2015-08-26T11:38:46+00:00', 'default', 'published', 9471, 10, NULL, '2015-07-06 15:47:17', '0000-00-00 00:00:00', 0),
(95, 1, 1, 3984, 66, 'How to Change Logo', '<p><span style="font-size:18px;font-weight:bold;">Step 1: </span><span style="font-size:18px;">Click "<span style="font-weight:bold;">Appearance</span>" in the main navigation bar.</span></p><p><img src="https://imbmediab.s3.amazonaws.com/1/b6d1a9c512a35a434e11a3dc193d7651/08v2d2vt2jg1x9s2llq55u6fkp6yjc.png" alt="08v2d2vt2jg1x9s2llq55u6fkp6yjc.png" /></p><p><br /></p><p><span style="font-size:18px;"><span style="font-weight:bold;">Step 2: </span>Click "<span style="font-weight:bold;">Site Logo</span>"</span></p><p><img src="https://imbmediab.s3.amazonaws.com/1/f5c72d1685525f978568900da03a6ed1/hlzunl1oqv421lwow7scav7numnepe.png" alt="hlzunl1oqv421lwow7scav7numnepe.png" /><span style="font-size:18px;"><br /></span></p><p><span style="font-size:18px;"><br /></span><span style="font-size:18px;"><span style="font-weight:bold;">Step 3: </span>Click "<span style="font-weight:bold;">Choose Image</span>" and upload specific image you would like as your logo.</span></p><p><img src="https://imbmediab.s3.amazonaws.com/1/f3e63d95161d128e5f9cea0355ecf44f/1ds2esidqer2no4x93selfqzj3jz9m.png" alt="1ds2esidqer2no4x93selfqzj3jz9m.png" /></p><p><span style="font-size:18px;line-height:1.5;"><br /></span></p><p><span style="font-size:18px;line-height:1.5;"><span style="font-weight:bold;">Step 4: </span>Make sure to click "<span style="font-weight:bold;">Save Changes</span>" when you finish uploading your new logo.</span></p><p><img src="https://imbmediab.s3.amazonaws.com/1/e8242eaca7095e77757a176eb733d9b9/1gr2i9v13wm1mxiapcb9xg7a1iq7wa.png" alt="1gr2i9v13wm1mxiapcb9xg7a1iq7wa.png" /><span style="font-size:18px;line-height:1.5;"> </span><br /></p>', '', '', 'How-to-Change-Logo-2015-08-26T11:38:46+00:00', 'default', 'published', 9471, 11, NULL, '2015-07-06 16:25:57', '0000-00-00 00:00:00', 0),
(96, 1, 1, 3984, 23, 'What is the "Wallboard"?', '<p><span style="font-size:18px;">Under "<span style="font-weight:bold;">Finance</span>" in the main navigation bar, you will see a tab named "<span style="font-weight:bold;">Wallboard</span>". </span></p><p><img src="https://imbmediab.s3.amazonaws.com/1/1521847c6c04f271d8f9a7a956fa74b9/h4h6w1rssqy8fb10o0zgcix3f7y2hv.png" alt="h4h6w1rssqy8fb10o0zgcix3f7y2hv.png" /></p><p><br /></p><p><span style="font-size:18px;">The Wallboard is your </span><span style="font-size:18px;font-weight:bold;">Statistic Overview</span><span style="font-size:18px;">, in there you will be able to see </span><span style="font-size:18px;line-height:27px;">reoccurring</span><span style="font-size:18px;"> revenue, total sales, total money made, daily sales, money made for that day and money made the day before, as well as refunds, and total money refunded.</span><br /></p>', '', '', 'What-is-the-"Wallboard"?-2015-08-26T11:38:46+00:00', 'default', 'published', 9430, 6, NULL, '2015-07-06 16:46:32', '0000-00-00 00:00:00', 0),
(98, 1, 1, 3984, 66, 'Vimeo', '<p><span style="font-size:18px;">When uploading your own videos into a new lesson or course, we recommend using <span style="font-weight:bold;">Vimeo</span>.</span></p><p><span style="font-size:18px;">Vimeo is the most time efficient method of uploading videos with a <span style="text-decoration:underline;">1 click upload option</span>.</span></p><p><img src="https://imbmediab.s3.amazonaws.com/1/f1c14db34fdca43ea3fe6e477cfbf7d4/tq18aqo1xd5nunz7cpdrooaymoc1ll.png" alt="tq18aqo1xd5nunz7cpdrooaymoc1ll.png" /></p><p><span style="font-size:18px;"><br /></span></p><p><span style="font-size:18px;">﻿</span><span style="font-size:24px;text-decoration:underline;">Creating a Vimeo Account</span></p><p><span style="font-size:18px;">The only thing you need to do to link your videos with Smart Member is to log into your Vimeo account, or create a new account.</span><br /></p><p><img src="https://imbmediab.s3.amazonaws.com/1/cf79b18d4da7d72a9053db231d94b594/sswetnd1ysthtpk88bce22mffk7kkn.png" alt="sswetnd1ysthtpk88bce22mffk7kkn.png" /></p><p><span style="font-size:18px;"><br /></span></p><p><span style="font-size:18px;">If you do not wish to use Vimeo, you are also able to embed any video using the &lt;iframe&gt; html, however script is not allowed.</span></p>', '', '', 'Vimeo-2015-08-26T11:38:46+00:00', 'default', 'published', 9471, 7, NULL, '2015-07-07 09:33:32', '0000-00-00 00:00:00', 0),
(111, 1, 1, 1, 23, 'How to Submit a Support Ticket', '<div style="text-align:center;"><span style="font-size:18px;font-weight:bold;line-height:1.5;background-color:#FFFF00;"><a href="http://training.smartmember.com/support-ticket" target="_blank">CLICK HERE TO SUBMIT A NEW SUPPORT TICKET</a></span></div>', '', '', 'How-to-Submit-a-Support-Ticket-2015-08-26T11:38:46+00:00', 'default', 'published', 9430, 1, NULL, '2015-07-19 04:50:18', '2015-12-16 22:15:49', 0),
(9430, 1, 0, 3840, 0, 'Frequently Asked Questions', '', '', '', 'Frequently-Asked-Questions', 'article-index', 'published', 0, 0, NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40', 0),
(9471, 1, 0, 3840, 0, 'Getting Started', '', '', '', 'Getting-Started', 'article-index', 'published', 0, 0, NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40', 0),
(9476, 1, 0, 3840, 0, 'Account Management', '', '', '', 'Account-Management', 'article-index', 'published', 0, 0, NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40', 0),
(9480, 1, 0, 3840, 0, 'Access Issues', '', '', '', 'Access-Issues', 'article-index', 'published', 0, 0, NULL, '2016-02-14 22:08:40', '2016-02-14 22:08:40', 0);

-- --------------------------------------------------------

--
-- Table structure for table `support_categories`
--

CREATE TABLE IF NOT EXISTS `support_categories` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `migrated` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=365 ;

--
-- Dumping data for table `support_categories`
--

INSERT INTO `support_categories` (`id`, `site_id`, `company_id`, `title`, `sort_order`, `deleted_at`, `created_at`, `updated_at`, `migrated`) VALUES
(23, 1, 1, 'Frequently Asked Questions', 0, NULL, '2015-06-18 16:09:13', '2016-02-14 22:08:40', 1),
(66, 1, 1, 'Getting Started', 1, NULL, '2015-07-03 13:17:24', '2016-02-14 22:08:40', 1),
(71, 1, 1, 'Account Management', 3, NULL, '2015-07-03 23:39:10', '2016-02-14 22:08:40', 1),
(75, 1, 1, 'Access Issues', 2, NULL, '2015-07-06 15:29:29', '2016-02-14 22:08:40', 1),
(212, 2056, 10372, 'Site Management', 1, NULL, '2015-11-10 08:20:27', '2016-02-14 22:08:42', 1),
(214, 2056, 10372, '', 0, NULL, '2015-11-13 21:16:38', '2016-02-14 22:08:42', 1),
(327, 2056, 10372, 'Smart Member How-Tos', 0, NULL, '2016-02-04 19:58:20', '2016-02-14 22:24:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `escalated_site_id` bigint(20) DEFAULT NULL,
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
  `last_replied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_actions`
--

CREATE TABLE IF NOT EXISTS `support_ticket_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `modified_attribute` varchar(255) NOT NULL,
  `old_value` varchar(255) NOT NULL,
  `new_value` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `swapspots`
--

CREATE TABLE IF NOT EXISTS `swapspots` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bridge_page_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bridge_page_id` (`bridge_page_id`),
  KEY `site_id` (`site_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `table_seeds`
--

CREATE TABLE IF NOT EXISTS `table_seeds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seed_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `text` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tax_assoc`
--

CREATE TABLE IF NOT EXISTS `tax_assoc` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  `taxonomy_id` int(11) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tax_type`
--

CREATE TABLE IF NOT EXISTS `tax_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `team_roles`
--

CREATE TABLE IF NOT EXISTS `team_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `role` int(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `template_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `templates_attributes`
--

CREATE TABLE IF NOT EXISTS `templates_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `default_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `default_position` int(11) NOT NULL,
  `element_type_id` int(11) NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_notes`
--

CREATE TABLE IF NOT EXISTS `ticket_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `note` text NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `index_type` (`type`),
  KEY `index_product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `unsubfeedback`
--

CREATE TABLE IF NOT EXISTS `unsubfeedback` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `unsub_reason` text,
  `comment` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `unsubscribers`
--

CREATE TABLE IF NOT EXISTS `unsubscribers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `unsubscribers_segment`
--

CREATE TABLE IF NOT EXISTS `unsubscribers_segment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `site_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `setup_wizard_complete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=165547 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `email_hash`, `password`, `verified`, `facebook_user_id`, `profile_image`, `access_token`, `access_token_expired`, `remember_token`, `reset_token`, `vanity_username`, `affiliate_id`, `last_logged_in`, `deleted_at`, `created_at`, `updated_at`, `setup_wizard_complete`) VALUES
(10, 'John', 'Razmus', 'john@razmus.net', 'john@razmus.net', '771747715bc5577b6dc0231103b84374', '$2y$11$lnNIzEchUYuMva.AmYiSFOAyfChlTT7v.zxPTrqLEiCl710vLcqSS', 1, NULL, 'https://s3.amazonaws.com/imbmediab/uploads/08cc15e00a35860568a487aa54cd027d/john-razmus.jpg', '983a2fc63daba528303f82e1013039f3', '2015-12-04 18:12:54', NULL, '01454d804465289d5dcf3884583df341', 'john2', NULL, '2016-03-29 21:28:39', NULL, '2015-06-12 23:36:19', '2016-04-05 22:12:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_meta`
--

CREATE TABLE IF NOT EXISTS `user_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `site_id` (`site_id`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6815 ;

--
-- Dumping data for table `user_meta`
--

INSERT INTO `user_meta` (`id`, `user_id`, `site_id`, `key`, `value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(8, 10, 6194, 'aid', '123', NULL, '2016-01-05 20:13:58', '2016-01-05 20:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_notes`
--

CREATE TABLE IF NOT EXISTS `user_notes` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `user_id` bigint(11) NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `note` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_options`
--

CREATE TABLE IF NOT EXISTS `user_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32095 ;

--
-- Dumping data for table `user_options`
--

INSERT INTO `user_options` (`id`, `deleted_at`, `created_at`, `updated_at`, `user_id`, `meta_key`, `meta_value`) VALUES
(77, NULL, '2015-07-07 14:17:24', '0000-00-00 00:00:00', 10, 'show_notice', '1'),
(283, NULL, '2015-07-20 15:50:33', '0000-00-00 00:00:00', 10, 'login_count', '26'),
(284, NULL, '2015-07-20 15:50:33', '0000-00-00 00:00:00', 10, 'last_login', '1440028800'),
(1790, NULL, '2015-07-21 10:12:41', '0000-00-00 00:00:00', 10, 'show_notice_lesson', '0'),
(14602, NULL, '2015-09-18 16:36:29', '2016-01-08 06:10:15', 10, 'current_company_id', '10372'),
(29957, '2016-01-07 08:38:21', '2016-01-07 08:38:19', '2016-01-07 08:38:21', 10, 'vimeo_integration', '1197'),
(31765, '2016-03-08 17:07:55', '2016-03-08 17:07:32', '2016-03-08 17:07:55', 10, 'stripe_integration', '4318');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL,
  `role_type` int(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_roles_role_id_index` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=124392 ;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_id`, `role_type`, `deleted_at`, `updated_at`, `created_at`) VALUES
(58, 75, 1, NULL, NULL, '2015-09-16 13:59:00'),
(4241, 7769, 1, NULL, NULL, '2015-09-16 13:59:00'),
(23900, 75, 1, NULL, NULL, '2015-09-16 20:21:02'),
(28083, 7769, 1, NULL, NULL, '2015-09-16 20:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE IF NOT EXISTS `verification_codes` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `expired_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(11) NOT NULL,
  `sidebar_id` bigint(11) NOT NULL DEFAULT '1',
  `target_id` bigint(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `sort_order` bigint(11) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `target_id` (`target_id`),
  KEY `sidebar_id` (`sidebar_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `widget_locations`
--

CREATE TABLE IF NOT EXISTS `widget_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `widget_id` bigint(20) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `widget_id` (`widget_id`),
  KEY `type` (`type`),
  KEY `target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `widget_meta`
--

CREATE TABLE IF NOT EXISTS `widget_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `widget_id` bigint(20) NOT NULL,
  `target_id` bigint(11) DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `widget_id` (`widget_id`),
  KEY `target_id` (`target_id`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wizards`
--

CREATE TABLE IF NOT EXISTS `wizards` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `slug` varchar(20) NOT NULL,
  `completed_nodes` text,
  `options` text,
  `is_completed` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
