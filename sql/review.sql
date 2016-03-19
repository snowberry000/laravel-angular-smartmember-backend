CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint unsigned not null AUTO_INCREMENT primary key,
  `site_id` bigint unsigned NOT NULL,
  `rating` int NOT NULL,
  `comment`varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

alter table directory_listings add column `sub_category` VARCHAR( 255 ) NULL;
update directory_listings set category = 'Other' , sub_category = 'Other' where category is null;

alter table reviews add column `user_id` bigint(22) NOT NULL;