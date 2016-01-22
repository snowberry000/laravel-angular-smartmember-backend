alter table affcontests add content text after last_refreshed;

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