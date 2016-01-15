-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS `fix_team_names`;

CREATE PROCEDURE `fix_team_names` ()
BEGIN
	DECLARE companyID BIGINT(20) DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE firstName varchar(255);
	DECLARE lastName varchar(255);
	DECLARE teamName varchar(255);

	DECLARE c_companies CURSOR for
		select c.id, u.first_name, u.last_name from companies c inner join users u on u.id = c.user_id where name='' or name is null;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN c_companies;

	REPEAT
		
		FETCH c_companies INTO companyID, firstName, lastName;
		IF done <> 1 THEN
			if length(lastName) > 0 then
				SET teamName := concat(firstName, ' ', lastName, '\'s Team');
			ELSE 
				SET teamName := concat(firstName, '\'s ', 'Team');
			END IF;

			update companies set name = teamName where id =  companyID;
		END IF;
		
	UNTIL done END REPEAT;

	CLOSE c_companies;
END
$$
DELIMITER ;
CALL fix_team_names();
DROP PROCEDURE IF EXISTS `fix_team_names`;