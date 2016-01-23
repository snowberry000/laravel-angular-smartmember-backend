alter table affcontests add content text after last_refreshed;

ALTER TABLE  `unsubscribers_segment` CHANGE  `company_id`  `site_id` BIGINT( 20 ) NOT NULL ;

ALTER TABLE  `custom_pages` ADD  `hide_sidebars` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `discussion_settings_id` ;

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