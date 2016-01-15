-- Copy to new site id. 

DELIMITER $$
DROP PROCEDURE IF EXISTS `set_siteid_index`;

CREATE PROCEDURE `set_siteid_index` ()
BEGIN
	DECLARE siteID BIGINT(20) DEFAULT 0;
	DECLARE theID BIGINT(11) DEFAULT 0;
	DECLARE tableName varchar(2550);
	DECLARE query TEXT;
	DECLARE done INT DEFAULT 0;
	DECLARE user INT DEFAULT 0;
	DECLARE company INT DEFAULT 0;
	DECLARE c_tables CURSOR for
		SELECT table_name from information_schema.columns where column_name = 'site_id' and table_schema='smartmembers' and table_name NOT IN ('sites', 'lessons', 'roles');


	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN c_tables;

	REPEAT
		
		FETCH c_tables INTO tableName;
		IF done <> 1 THEN
			SET @query = concat('CREATE INDEX index_site ON ', tableName, ' (site_id)');
			SELECT @query;
			PREPARE stmt1 FROM @query;
			EXECUTE stmt1;
			DEALLOCATE PREPARE stmt1;

	 	END IF;
		
	UNTIL done END REPEAT;

	CLOSE c_tables;
END
$$
DELIMITER ;

CALL show_index;

DROP PROCEDURE IF EXISTS `set_siteid_index`;

