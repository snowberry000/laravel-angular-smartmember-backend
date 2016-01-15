-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS `fix_sites`;

CREATE PROCEDURE `fix_sites` ()
BEGIN
	DECLARE siteID BIGINT(20) DEFAULT 0;
	DECLARE userID BIGINT(20) DEFAULT 0;
	DECLARE done INT DEFAULT 0;

	DECLARE c_companies CURSOR for
		select id, user_id from sites where company_id = 0 or company_id is null;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN c_companies;

	REPEAT
		FETCH c_companies INTO siteID, userID;
		IF done <> 1 THEN
			update sites set company_id = (select id from companies where user_id = userID limit 1) where id=siteID;			
	 	END IF;
		
	UNTIL done END REPEAT;

	CLOSE c_companies;
END
$$
DELIMITER ;
CALL fix_sites();
DROP PROCEDURE IF EXISTS `remove_duplicates`;