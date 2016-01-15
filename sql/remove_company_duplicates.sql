-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS `remove_duplicates`;

CREATE PROCEDURE `remove_duplicates` ()
BEGIN
	DECLARE companyID BIGINT(20) DEFAULT 0;
	DECLARE userID BIGINT(20) DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE teamName varchar(255);
	DECLARE companyIDs TEXT;

	DECLARE c_companies CURSOR for
		select id, name, user_id from companies group by name, user_id having count(*) > 1;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN c_companies;

	REPEAT
		FETCH c_companies INTO companyID, teamName, userID;
		IF done <> 1 THEN
			SET companyIDs := (select group_concat(id) from companies where name = teamName and id != companyID and deleted_at is NULL and user_id=userID);
			update affcontests set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update affiliate_jvpage set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update affiliates set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update affteams set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update canned_responses set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update clicks set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update company_options set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_autoresponder  set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_history set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_listledger set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_lists set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_recipient set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_settings set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update email_subscribers set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update emails set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update emails_queue set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update links set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update livecasts set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update emails set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update media_items set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update modules set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update permalinks set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update posts set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update roles set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update seo_settings set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update site_meta_data set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update sites set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update special_pages set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update support_articles set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update support_categories set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update support_tickets set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update transactions set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update unsubfeedback set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			update user_notes set company_id = companyID where FIND_IN_SET(company_id, companyIDs);
			UPDATE companies SET deleted_at = '2015-09-30' where FIND_IN_SET(id, companyIDs);
	 	END IF;
		
	UNTIL done END REPEAT;

	CLOSE c_companies;
END
$$
DELIMITER ;
CALL remove_duplicates();
DROP PROCEDURE IF EXISTS `remove_duplicates`;