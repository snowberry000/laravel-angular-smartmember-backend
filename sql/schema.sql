drop database if exists smartmembers;
create database smartmembers
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

use smartmembers;

CREATE TABLE `affiliate_jvpage` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `embed_content` text NOT NULL,
  `subscribe_button_text` text,
  `redirect_url` text NOT NULL,
  `thankyou_note` text NULL,
  `email_list_id` int(11) NOT NULL,
  `show_thankyou_note` tinyint DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_autoresponder_list` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) NULL,
  `list_id` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_autoresponder_email` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) NULL,
  `sort_order` int(11) DEFAULT 0,
  `email_id` varchar(255) NOT NULL,
  `delay` int(11) DEFAULT 0,
  `unit` tinyint DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_autoresponder` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `name` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);


CREATE TABLE `email_history` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `subscriber_id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `list_type` VARCHAR(50) NULL,
  `auto_id` int(11) NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `unsubfeedback` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `email_id` int(11) NOT NULL,
  `email` varchar(255) NULL,
  `unsub_reason` text NULL,
  `comment` text NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `affleaderboards` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NULL,
  `affiliate_id` int(11) NULL,
  `affiliate_name` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
   PRIMARY KEY (`id`)
);

CREATE TABLE `affcontests` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL,
  `company_id` int(11) NULL,
  `title` text NOT NULL,
  permalink text not null,
  `featured_image` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `refresh_type` varchar(100) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `last_refreshed` datetime NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `affcontest_sites` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `contest_id` bigint NOT NULL,
  `site_id` bigint NOT NULL ,
   `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `emails_queue` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `email_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `list_type` VARCHAR(50) NULL,
  `email_recipient_id` bigint(11) null default null,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_subscriber_pair` (`email_id`,`subscriber_id`,`list_type`)
);

CREATE TABLE `email_recipient` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `email_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_subscribers` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_listledger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `list_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_list_pair` (`list_id`,`subscriber_id`),
  INDEX (  `subscriber_id` )
);

CREATE TABLE `email_lists` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `name` varchar(255) NOT NULL,
  `total_subscribers` int(11) DEFAULT 0,
  `list_type` varchar(255) NOT NULL DEFAULT 'user',
  `segment_query` text NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `opens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `emails` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `mail_name` text NOT NULL,
  `mail_sending_address` text NOT NULL,
  `mail_reply_address` text NOT NULL,
  `mail_signature` text NOT NULL,
  `sendgrid_integration` BIGINT(22) NULL DEFAULT 0,
  `recipient_type` varchar(100) null default null,
  `mail_test_default` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
   PRIMARY KEY (`id`)
);

CREATE TABLE `affteamledger` (
  `id` int(10) unsigned not null AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_team_id` (`team_id`,`affiliate_id`)
);

CREATE TABLE `affteams` (
  `id` int(10) unsigned not null AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `site_id` int(11) NULL,
  `company_id` int(11) NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `user_id` INT(20) NOT NULL,
  `profile_image` text NOT NULL,
  `locked` boolean default false,
  `hash` text NULL,
  permalink varchar(255),
  pending_permalink varchar(255),
  hide_revenue boolean default false,
  hide_sites boolean default false,
  hide_members boolean default false,
  hide_total_lessons boolean default false,
  bio text,
  display_name varchar(255),
  display_image text,
  hide_total_downloads boolean default false,
  subtitle varchar(255),
  `site_count` int(11) default 100,
  is_completed boolean default false,
  progress int(11) default 0,
  deleted_at timestamp NULL DEFAULT NULL,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `company_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `sites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subdomain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  domain varchar(255),
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_id` bigint(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `domain_mask` varchar(255) NULL,
  `total_members` int default 0,
  `total_lessons` int default 0,
  `total_revenue` int default 0,
  `stripe_user_id` varchar(255),
  `stripe_access_token` text,
  `stripe_integrated` boolean default false,
  `type` int(11) NOT NULL,
  `company_id` int(10) not null,
  facebook_app_id varchar(255),
  facebook_secret_key varchar(255),
  `cloneable` boolean NOT NULL default 0,
  `clone_id` int(11) NOT NULL default 0,
  is_completed boolean default false,
  progress int(11) default 0,
  intention int(11) default 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `welcome_content` TEXT NULL
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_subdomain_unique` (`subdomain`)
);


CREATE TABLE `affiliate_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `affiliates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned  NULL,
  `company_id` bigint(20) unsigned NULL,
  `affiliate_request_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_note` text COLLATE utf8_unicode_ci NOT NULL,
  `admin_note` text COLLATE utf8_unicode_ci NULL,
  `past_sales` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `featured_image` text COLLATE utf8_unicode_ci NULL,
  `original` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   UNIQUE KEY `affiliate_request_id` (`affiliate_request_id`)
);

CREATE TABLE `element_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `history_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `browser` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `meta_data_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_value` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
);

CREATE TABLE `site_meta_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NULL,
  `data_type` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);



CREATE TABLE `sites_templates_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `element_type_id` int(11) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
);

CREATE TABLE `table_seeds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seed_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `templates_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `default_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_position` int(11) NOT NULL,
  `element_type_id` int(11) NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(40) NOT NULL,
  `affiliate_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double(8,2) NOT NULL,
  `association_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_hash` text NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `facebook_user_id` VARCHAR(255) COLLATE utf8_unicode_ci NULL,
  `profile_image` text NOT NULL,
  `access_token` text COLLATE utf8_unicode_ci NOT NULL,
  `access_token_expired` datetime NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_token` text COLLATE utf8_unicode_ci NOT NULL,
  `vanity_username` varchar(255) NULL,
  `affiliate_id` INT(11) NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_settings`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `site_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `username` text,
  `password` text,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `replyto_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_signature` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `modules` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  `title` text NOT NULL,
  `note` text NOT NULL,
  `access_level` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lessons` (
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
  `type` varchar(255) NULL,
  `is_draft` boolean default false,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `transcript_content` text NOT NULL,
  `transcript_button_text` varchar(50) default 'Transcript',
  `transcript_content_public` boolean,
  `audio_file` text NOT NULL,
  `access_level_type` int(11) NOT NULL default 1,
  `access_level_id` int(11) NOT NULL default 1,
  `discussion_settings_id` int(11) NOT NULL,
  permalink text not null,
  remote_id text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`)
);

CREATE TABLE `sites_menu_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int(11) NOT NULL,
  `custom_icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `sites_footer_menu_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `access_levels` (
  id bigint(22) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  site_id bigint(22) unsigned NOT NULL,
  name varchar(255) NOT NULL,
  information_url text not null,
  redirect_url text not null,
  product_id varchar(255),
  jvzoo_button TEXT NULL DEFAULT NULL,
  price double(10,2),
  currency varchar(6),
  payment_interval varchar(255) default 'one_time',
  facebook_group_id bigint(22) unsigned,
  stripe_plan_id varchar(255),
  hash varchar(255),
  deleted_at datetime,
  updated_at datetime,
  created_at timestamp default CURRENT_TIMESTAMP,
  `trial_duration` INT NULL,
  `trial_interval` VARCHAR( 10 ) NULL
);

CREATE TABLE `access_grants` (
  id bigint(22) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  access_level_id bigint(22) not null,
  grant_id bigint(22) not null,
  deleted_at datetime,
  updated_at datetime,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE `access_passes` (
  id bigint(22) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  site_id bigint(22) not null,
  access_level_id bigint(22) not null,
  user_id bigint(22) not null,
  subscription_id VARCHAR(255),
  expired_at datetime DEFAULT NULL,
  deleted_at datetime,
  updated_at datetime,
  created_at timestamp default CURRENT_TIMESTAMP,
  INDEX (  `user_id` ),
  INDEX (  `site_id` )
);

CREATE TABLE `integrations` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NULL,
  `site_id` bigint(22) NULL,
  `company_id` bigint(22) NULL,
  `connected_account_id` bigint( 22 ) NULL,
  `type` varchar(255) NOT NULL,
  `auth_type` varchar(255) NOT NULL,
  `username` text,
  `password` text,
  `access_token` text,
  `remote_id` text,
  `expired_at` datetime DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp default CURRENT_TIMESTAMP,
  `updated_at` datetime
);

CREATE TABLE `custom_pages` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `access_level_type` int(11) NOT NULL default 1,
  `access_level_id` int(11) NOT NULL default 1,
  `discussion_settings_id` int(11) NOT NULL,
  permalink text not null,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `download_center` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `creator_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` longtext NOT NULL,
  `download_button_text` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `media_item_id` int(11) NOT NULL,
  `access_level_type` int(11) NOT NULL default 1,
  `access_level_id` int(11) NOT NULL default 1,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  permalink text not null,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `downloads_history` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `download_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) NULL,
  `role_type` int(20) unsigned NOT NULL,
  `total_visits` int unsigned default 0,
  `total_lessons` int unsigned default 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_site_company` (`user_id`,`site_id`,`company_id`),
  KEY `site_id` (`site_id`)
);

CREATE TABLE `role_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(50) NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `site_notices` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` VARCHAR(255) NULL,
  `start_date` datetime ,
  `end_date` datetime ,
  `on` boolean DEFAULT false,
  `type` VARCHAR(255) DEFAULT 'admin',
  `content` TEXT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `site_notices_seen` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_notice_id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `seo_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `link_type` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `media_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `aws_key` text NOT NULL,
  `type` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `posts` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `access_level_type` int(11) NOT NULL default 1,
  `access_level_id` int(11) NOT NULL default 1,
  permalink text not null,
  `transcript_content` text NOT NULL,
  `transcript_button_text` varchar(50) default 'Transcript',
  `transcript_content_public` boolean,
  `audio_file` text NOT NULL,
  `discussion_settings_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `tax_assoc` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  `taxonomy_id` int(11) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `tax_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `special_pages` (
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
  `multiple` int,
  `free_item_url` text NOT NULL,
  `free_item_text` text NOT NULL,
  `continue_refund_text` text NOT NULL,
  `use_free_item_url` boolean,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `user_notes` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `user_id` bigint(11) NOT NULL,
  `complete` BOOLEAN NOT NULL DEFAULT FALSE,
  `note` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `discussion_settings` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `show_comments` BOOLEAN NOT NULL DEFAULT FALSE,
  `newest_comments_first` BOOLEAN NOT NULL DEFAULT FALSE,
  `close_to_new_comments` BOOLEAN NOT NULL DEFAULT FALSE,
  `allow_replies` BOOLEAN NOT NULL DEFAULT FALSE,
  `public_comments` BOOLEAN NOT NULL DEFAULT FALSE,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `comments` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `body` text,
  `parent_id` bigint(11) default 0,
  `user_id` bigint(11) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `target_id` bigint(11) NOT NULL,
  `public` BOOLEAN NOT NULL DEFAULT FALSE,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `support_articles` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  permalink text not null,
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `support_tickets` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_email` varchar(255) NULL,
  `user_name` varchar(255) NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `type` varchar(100) NOT NULL default 'Normal',
  `category` varchar(100) NOT NULL,
  `priority` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL default 'open',
  `read` BOOLEAN default false,
  `parent_id` bigint(20) unsigned NOT NULL,
  `attachment` text NOT NULL,
  `agent_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `support_categories` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `canned_responses` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `site_id` bigint(20) unsigned NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `categories` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `text` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `posts_categories` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `tags` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `text` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `posts_tags` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `sites_ads` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `banner_url` text,
  `banner_image_url` text,
  `custom_ad` text,
  `display` BOOLEAN default true,
  `sort_order` int(11) DEFAULT 0,
  `open_in_new_tab` BOOLEAN default false,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `livecasts` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) unsigned NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `note` text NOT NULL,
  `embed_content` text NOT NULL,
  `featured_image` text NOT NULL,
  `access_level_type` int(11) NOT NULL default 1,
  `access_level_id` int(11) NOT NULL default 1,
  permalink text not null,
  `discussion_settings_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `speed_blogs` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `website_url` text NOT NULL,
  `endpoint_url` text NOT NULL,
  `use_xmlrpc` BOOLEAN NOT NULL DEFAULT TRUE,
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
);

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
);

CREATE TABLE `user_options` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`deleted_at` timestamp NULL DEFAULT NULL,
	`created_at` timestamp,
	`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`user_id` int(11) NOT NULL,
	`meta_key` varchar(250) NOT NULL,
	`meta_value` text NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `site_meta` (`user_id`,`meta_key`)
);

CREATE TABLE `bridge_templates` (
	`id` bigint(11) NOT NULL AUTO_INCREMENT,
	`type_id` int(11) NOT NULL,
	`name` varchar(225) NOT NULL,
	`folder_slug` varchar(100) NOT NULL,
	`icon` text NOT NULL,
	`deleted_at` timestamp NULL DEFAULT NULL,
	`created_at` timestamp,
	`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
);

CREATE TABLE `bridge_bpages` (
	`id` bigint(11) NOT NULL AUTO_INCREMENT,
	`user_id` bigint(11) NOT NULL,
	`name` text NOT NULL,
	`template_id` bigint(11) NOT NULL,
	`content` text NOT NULL,
	`meta` text NOT NULL,
	`type_id` bigint(11) NOT NULL,
	`deleted_at` timestamp NULL DEFAULT NULL,
	`created_at` timestamp,
	`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
);

CREATE TABLE `bridge_types` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) NOT NULL,
  `folder_slug` varchar(100) NOT NULL,
  `icon` text NOT NULL,
  `description` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `bridge_media_items` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `aws_key` text NOT NULL,
  `type` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `bridge_permalinks` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `url_slug` varchar(255) NOT NULL,
  `target_id` bigint(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permalink` (`user_id`,`target_id`),
  UNIQUE KEY `unique_link` (`user_id`,`url_slug`),
  KEY `url_slug` (`url_slug`),
  KEY `target_id` (`target_id`)
);

CREATE TABLE `bridge_seo_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `target_id` (`target_id`),
  KEY `meta_key` (`meta_key`)
);

CREATE TABLE `bridge_user_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_meta` (`user_id`,`meta_key`)
);

CREATE TABLE IF NOT EXISTS `bsite_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(250) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_meta` (`user_id`,`meta_key`)
);

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);

CREATE TABLE IF NOT EXISTS `access_payment_methods` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `access_level_id` bigint(22) NOT NULL,
  `payment_method_id` bigint(22) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL,
  `role_type` int(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
  );

CREATE TABLE IF NOT EXISTS `ticket_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) NOT NULL,
  `note` text NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE permalinks (
  id bigint unsigned not null AUTO_INCREMENT PRIMARY KEY,
  permalink text not null,
  site_id bigint(20) not null,
  type text,
  target_id bigint(20) not null,
  deleted_at DATETIME,
  updated_at DATETIME,
  created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `clicks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `link_id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `company_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `url` text NOT NULL,
  `hash` varchar(35) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `opens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `unsubscribers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subscriber_id` bigint(20) NOT NULL,
  `email_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `unsubscribers_segment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255),
  `company_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `permalink_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `permalink_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NULL,
  `ip` varchar(20) NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `content_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL,
  `meta_key` varchar(255),
  `meta_value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `team_roles` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` bigint(20) unsigned NOT NULL,
   `company_id` bigint(20) NOT NULL,
   `role` int(20) unsigned NOT NULL,
   `deleted_at` timestamp NULL DEFAULT NULL,
   `created_at` timestamp,
   `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `company_id` (`company_id`),
   KEY `user_id` (`user_id`)
);

CREATE TABLE directory_listings(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  site_id bigint unsigned not null,
  title varchar(255) not null,
  pending_title varchar(255) not null,
  description text not null,
  pending_description text not null,
  image text,
  pending_image text,
  pricing text,
  pending_pricing text,
  pending_updates boolean default true,
  is_approved boolean default false,
  permalink text,
  category varchar(255),
  is_free boolean default false,
  expired_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `support_ticket_actions` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` bigint(20) unsigned NOT NULL,
   `ticket_id` bigint(20) NOT NULL,
   `modified_attribute` varchar(255) not null,
   `old_value` varchar(255) not null,
   `new_value` varchar(255) not null,
   `deleted_at` timestamp NULL DEFAULT NULL,
   `created_at` timestamp,
   `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`),
   KEY `ticket_id` (`ticket_id`),
   KEY `user_id` (`user_id`)
);

CREATE TABLE `linked_accounts` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` bigint(20) unsigned NOT NULL,
   `linked_email` varchar(255) NOT NULL,
   `linked_user_id` bigint(20) not null,
   `email_only_link` tinyint(1) NOT NULL,
   `verified` tinyint(1) NOT NULL DEFAULT 0,
   `verification_hash` varchar(255) NOT NULL,
   `deleted_at` timestamp NULL DEFAULT NULL,
   `created_at` timestamp,
   `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`id`)
);

CREATE TABLE `connected_accounts` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar( 255 ) NULL,
  `site_id` bigint(22) NULL,
  `company_id` bigint(22) NULL,
  `type` varchar(255) NOT NULL,
  `access_token` text,
  `remote_id` text,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp default CURRENT_TIMESTAMP,
  `updated_at` datetime
);

CREATE TABLE `integration_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integration_id` bigint(20) unsigned NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `verification_codes` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `expired_at` timestamp NULL DEFAULT NOW(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_recipients` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `email_id` bigint(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `order` bigint(11) NOT NULL DEFAULT 1,
  `subject` text,
  `intro` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`),
  KEY (`recipient`),
  KEY (`type`)
);

CREATE TABLE `email_recipients_queue` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `email_recipient_id` bigint(11) NOT NULL,
  `last_recipient_queued` bigint(11) NULL,
  `total_queued` bigint(11) NULL,
  `total_recipients` bigint(11) NULL,
  `send_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`),
  KEY (`email_recipient_id`),
  KEY (`last_recipient_queued`)
);

CREATE TABLE `wizards` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `site_id` bigint(20) NOT NULL,
  `slug` varchar(20) NOT NULL,
  `completed_nodes` text,
  `options` text,
  `is_completed` boolean default false,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);

CREATE TABLE forum_categories(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  title varchar(255),
  description text, 
  parent_id bigint unsigned default 0,
  site_id bigint unsigned not null,
  access_level_id bigint unsigned not null,
  access_level_type int default 1,
  allow_content boolean default true,
  total_replies int default 0,
  total_topics int default 0,
  icon text,
  permalink text,
  deleted_at timestamp NULL default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE forum_topics(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  title varchar(255),
  description text, 
  category_id bigint unsigned not null,
  total_replies int default 0,
  total_views int default 0,
  total_likes int default 0,
  status varchar(255),
  user_id bigint not null,
  site_id bigint not null,
  allow_content boolean default true,
  permalink text,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE forum_replies(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  content text,
  topic_id bigint unsigned not null,
  category_id bigint unsigned not null,
  user_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);


CREATE TABLE sites_roles(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  type varchar(255) not null,
  site_id bigint unsigned not null,
  user_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE sites_custom_roles(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  name varchar(255),
  site_id bigint unsigned not null,
  user_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE sites_custom_roles_capabilities(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  type varchar(255),
  capability varchar(255) not null,
  site_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE media_files(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  site_id bigint unsigned not null,
  user_id bigint unsigned not null,
  source text,
  type varchar(255) not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE `user_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `site_id` bigint(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`user_id`),
  KEY (`site_id`),
  KEY (`key`)
);

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
  KEY (`site_id`),
  KEY (`permalink`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `smart_link_urls` (
  `id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
  `smart_link_id` bigint(22) NOT NULL,
  `url` text NOT NULL,
  `visits` bigint(22) NULL,
  `weight` bigint(22) NULL,
  `order` bigint(22) NOT NULL DEFAULT 1,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY (`smart_link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;