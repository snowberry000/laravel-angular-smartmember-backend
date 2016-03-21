alter table affcontests add content text after last_refreshed;

ALTER TABLE  `imports_queue` ADD  `name` VARCHAR( 255 ) NULL AFTER  `id` ;

ALTER TABLE  `unsubscribers_segment` CHANGE  `company_id`  `site_id` BIGINT( 20 ) NOT NULL ;

ALTER TABLE  `custom_pages` ADD  `hide_sidebars` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `discussion_settings_id` ;


ALTER TABLE  `email_autoresponder` ADD  `email_when` INT NULL AFTER  `name` ;

CREATE TABLE `email_autoresponder_access_level` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) DEFAULT NULL,
  `access_level_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `email_autoresponder_site` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NULL,
  `event_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NULL,
  `count` bigint(20) NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`site_id`),
  KEY (`event_name`),
  KEY (`user_id`),
  KEY (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `event_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`event_id`),
  KEY (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `custom_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NULL,
  `archived` boolean not null default false,
  `shown` boolean not null default true,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`user_id`),
  KEY (`name`),
  KEY (`type`),
  KEY (`archived`),
  KEY (`shown`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `member_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL,
  `custom_attribute_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`member_id`),
  KEY (`custom_attribute_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE  `email_recipients_queue` ADD  `sending_user_id` BIGINT NULL AFTER  `info` ;

ALTER TABLE  `sites_menu_items` ADD  `open_in_new_tab` BOOLEAN NULL AFTER  `custom_icon` ;

ALTER TABLE  `opens` ADD  `segment_id` BIGINT NULL AFTER  `job_id` ;

ALTER TABLE  `clicks` ADD  `identifier` VARCHAR( 255 ) NULL AFTER  `ip` ;

ALTER TABLE  `opens` ADD  `identifier` VARCHAR( 255 ) NULL AFTER  `ip` ;

ALTER TABLE  `clicks` ADD  `segment_id` BIGINT NULL AFTER  `ip` ;

ALTER TABLE  `support_tickets` ADD  `escalated_site_id` BIGINT NULL AFTER  `site_id` ;

TRUNCATE `categories`;
TRUNCATE `tags`;
TRUNCATE `posts_categories`;
TRUNCATE `posts_tags`;

ALTER TABLE  `categories` ADD  `permalink` VARCHAR( 255 ) NOT NULL DEFAULT  '' AFTER  `text` ,
ADD INDEX (  `permalink` ) ;
ALTER TABLE  `categories` CHANGE  `text`  `title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE  `support_articles` ADD  `display` VARCHAR( 255 ) NULL DEFAULT 'default' AFTER  `permalink` ,
ADD  `parent_id` BIGINT( 22 ) NOT NULL DEFAULT  '0' AFTER  `display`;

UPDATE  `support_articles` SET display =  'default' WHERE display IS NULL AND deleted_at IS NULL;

ALTER TABLE  `support_categories` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS `widget_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `widget_id` bigint(20) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY (`widget_id`),
  KEY (`type`),
  KEY (`target`)
) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=latin1;

INSERT INTO `widget_locations` (`widget_id`,`type`)
    SELECT `id`, 'everywhere' FROM `widgets` WHERE `deleted_at` IS NULL;

ALTER TABLE  `support_articles` ADD  `status` VARCHAR( 255 ) NOT NULL DEFAULT  'published' AFTER  `display` ,
ADD INDEX (  `status` ) ;

ALTER TABLE  `support_articles` CHANGE  `status`  `status` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'draft';

ALTER TABLE  `events` ADD  `company_id` BIGINT NULL AFTER  `site_id` ;
ALTER TABLE  `imports_queue` ADD  `email_welcome` BOOLEAN NULL AFTER  `job_id` ;
ALTER TABLE  `imports_queue` ADD  `email_ac` BOOLEAN NULL AFTER  `job_id` ;

ALTER TABLE  `app_configurations` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE  `connected_accounts` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE  `transactions` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE  `sites` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE  `permalinks` CHANGE  `target_id`  `target_id` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `lessons` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE  `modules` ADD  `migrated` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE  `permalinks` ADD  `parent_id` varchar(255) NULL DEFAULT null;