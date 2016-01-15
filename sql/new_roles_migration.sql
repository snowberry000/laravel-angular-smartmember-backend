-- migrate everything to use new roles system and not use teams anymore -- note we add an index and then drop it at the end so that we aren't adding duplicates
INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'owner', s.id, r.user_id, r.created_at FROM `companies` c
    INNER JOIN `sites` s ON s.company_id = c.id
    INNER JOIN `team_roles` r ON r.company_id = c.id
  WHERE r.deleted_at is null AND s.id is not null AND s.deleted_at is NULL AND c.deleted_at is NULL AND r.role = 1 )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'owner', r.site_id, r.user_id, r.created_at FROM `roles` r
    INNER JOIN `user_roles` ur ON r.id = ur.role_id
    WHERE r.deleted_at is null AND r.site_id is not null AND r.site_id != 0 AND ur.deleted_at is NULL AND ur.role_type = 1 )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'admin', s.id, r.user_id, r.created_at FROM `companies` c
    INNER JOIN `sites` s ON s.company_id = c.id
    INNER JOIN `team_roles` r ON r.company_id = c.id
  WHERE r.deleted_at is null AND s.id is not null AND s.deleted_at is NULL AND c.deleted_at is NULL AND r.role IN (2,3,4) )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'admin', r.site_id, r.user_id, r.created_at FROM `roles` r
    INNER JOIN `user_roles` ur ON r.id = ur.role_id
  WHERE r.deleted_at is null AND r.site_id is not null AND r.site_id != 0 AND ur.deleted_at is NULL AND ur.role_type IN (2,3,4) )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'support', s.id, r.user_id, r.created_at FROM `companies` c
    INNER JOIN `sites` s ON s.company_id = c.id
    INNER JOIN `team_roles` r ON r.company_id = c.id
  WHERE r.deleted_at is null AND s.id is not null AND s.deleted_at is NULL AND c.deleted_at is NULL AND r.role = 5 )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'support', r.site_id, r.user_id, r.created_at FROM `roles` r
    INNER JOIN `user_roles` ur ON r.id = ur.role_id
  WHERE r.deleted_at is null AND r.site_id is not null AND r.site_id != 0 AND ur.deleted_at is NULL AND ur.role_type = 5 )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

INSERT INTO `sites_roles` ( `type`, `site_id`, `user_id`, `created_at` )
  ( SELECT 'member', r.site_id, r.user_id, r.created_at FROM `roles` r
    INNER JOIN `user_roles` ur ON r.id = ur.role_id
  WHERE r.deleted_at is null AND r.site_id is not null AND r.site_id != 0 AND ur.deleted_at is NULL AND ur.role_type = 6 )
ON DUPLICATE KEY UPDATE `sites_roles`.`user_id` = `sites_roles`.`user_id`;

ALTER TABLE  `sites_roles` DROP INDEX  `user_role` ;

INSERT INTO `sites_roles` (`type`,`site_id`,`user_id`,`access_level_id`,`expired_at`,`created_at`)
    SELECT 'member', `site_id`,`user_id`,`access_level_id`,`expired_at`,`created_at` FROM `access_passes` WHERE `deleted_at` IS NULL;