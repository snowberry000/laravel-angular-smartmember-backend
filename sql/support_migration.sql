-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS `migrate_support`;

CREATE PROCEDURE `migrate_support` ()
BEGIN
	DECLARE siteID BIGINT(20) DEFAULT 0;
	DECLARE theID BIGINT(11) DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE user INT DEFAULT 0;
	DECLARE company INT DEFAULT 0;
	DECLARE c_support CURSOR for
		SELECT site_id FROM support_tickets 
		UNION 
			SELECT site_id FROM support_categories 
		UNION 
			SELECT site_id FROM support_articles ORDER BY site_id;


	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN c_support;

	REPEAT
		
		FETCH c_support INTO siteID;
		SET @user := (SELECT user_id from roles where `site_id` = siteID and role_type = 1 limit 1);
		IF @user IS NOT NULL and @user != 0 THEN
			SET @company := (SELECT company_id from roles where `user_id` = @user and company_id != 0 and role_type = 1 limit 1);
			IF @company IS NULL THEN
				SET @company := (SELECT id from companies where user_id = @user);
			END IF;

			UPDATE support_tickets set `company_id` = @company where `site_id` = siteID;
			UPDATE support_categories set `company_id` = @company where `site_id` = siteID;
			UPDATE support_articles set `company_id` = @company where `site_id` = siteID;
		END IF;
		
	UNTIL done END REPEAT;

	CLOSE c_support;
END
$$
DELIMITER ;
CALL migrate_support();
DROP PROCEDURE IF EXISTS `migrate_support`;