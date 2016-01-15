-- Copy to new site id. 

DELIMITER $$
DROP PROCEDURE IF EXISTS `clone_site`;

CREATE PROCEDURE `clone_site` (srcSite INT(11), destSite INT(11))
BEGIN
	DECLARE siteID BIGINT(20) DEFAULT 0;
	DECLARE theID BIGINT(11) DEFAULT 0;
	DECLARE tableName varchar(2550);
	DECLARE query TEXT;
	DECLARE done INT DEFAULT 0;
	DECLARE user INT DEFAULT 0;
	DECLARE company INT DEFAULT 0;
	DECLARE c_tables CURSOR for
		SELECT table_name from information_schema.columns where column_name = 'site_id' and table_schema='smartmembers' and table_name != 'sites';


	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN c_tables;

	REPEAT
		
		FETCH c_tables INTO tableName;
		IF done <> 1 THEN
			SET @query = concat('CREATE TABLE clone as SELECT * from ', tableName, ' where site_id = ', srcSite);
			SELECT @query;
			PREPARE stmt1 FROM @query;
			EXECUTE stmt1;
			DEALLOCATE PREPARE stmt1;

			UPDATE clone set site_id = destSite;
			ALTER table clone drop column id;	

			SET @query = concat('INSERT into ', tableName, ' SELECT 0, clone.* from clone');
			SELECT @query;			
			PREPARE stmt1 FROM @query;
			EXECUTE stmt1;
			DEALLOCATE PREPARE stmt1;

			DROP table clone;

	 	END IF;
		
	UNTIL done END REPEAT;

	CLOSE c_tables;
END
$$
DELIMITER ;

-- INSERT INTO sites (
-- 	subdomain,domain,name,template_id,user_id,domain_mask,
-- 	total_members,total_lessons,total_revenue,stripe_user_id,
-- 	stripe_access_token,stripe_integrated,type,company_id,
-- 	facebook_secret_key,facebook_app_id,deleted_at,created_at,updated_at
-- )
-- SELECT 
-- 	'coparenting',domain,name,template_id,user_id,domain_mask,
-- 	total_members,total_lessons,total_revenue,stripe_user_id,
-- 	stripe_access_token,stripe_integrated,type,company_id,
-- 	facebook_secret_key,facebook_app_id,deleted_at,created_at,updated_at
-- FROM sites where id=5236;

CALL clone_site(5236, 6269);

create table clone select * from modules where site_id=6269;
create table temp select a.id as old_id, b.id as new_id from modules a inner join clone b on a.created_at = b.created_at where a.site_id=5236;
update lessons l join temp on l.module_id = temp.old_id set l.module_id = temp.new_id where l.site_id=6269;
drop table temp;
drop table clone;

DROP PROCEDURE IF EXISTS `migrate_support`;

