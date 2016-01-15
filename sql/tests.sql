-- insert into users (first_name,last_name,email,verified,password,access_token,access_token_expired) values
-- 	('John','Smith','admin@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','9f92fb02ecc361000c2ae3ce0982424f',now()+interval 10 year);

-- insert into companies(name,user_id, hash)
-- 	values("John's Team", 11371, 'db942a987f09ee53fca8a726ee3735c3');

-- insert into sites (subdomain,name, user_id, total_members,company_id) values ('ui_tests','Tests',11371, 1, 10241);


-- insert into roles (user_id,site_id,company_id) values
-- 	(11371, 6235, 10241);

-- INSERT INTO `user_roles` (`role_id`, `role_type`)
-- VALUES(66340, 1);


DELIMITER $$
DROP PROCEDURE IF EXISTS `setup_tests`;

CREATE PROCEDURE `setup_tests` ()
BEGIN
	DECLARE userID BIGINT(20) DEFAULT 0;
	DECLARE siteID BIGINT(20) DEFAULT 0;
	DECLARE companyID BIGINT(20) DEFAULT 0;
	DECLARE roleID BIGINT(20) DEFAULT 0;
	DECLARE done INT DEFAULT 0;

	INSERT INTO users (first_name,last_name,email,verified,password,access_token,access_token_expired) VALUES
		('John','Smith','admin@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','9f92fb02ecc361000c2ae3ce0982424f',now()+interval 10 year);

	SET userID = LAST_INSERT_ID();

	INSERT INTO companies(name,user_id, hash)
		VALUES("John's Team", userID, 'db942a987f09ee53fca8a726ee3735c3');

	SET companyID = LAST_INSERT_ID();

	INSERT INTO sites (subdomain,name, user_id, total_members,company_id) values ('ui_tests','Tests',userID, 1, companyID);

	SET siteID = LAST_INSERT_ID();

	insert into roles (user_id, site_id, company_id) values
		(userID, siteID, companyID);

	SET roleID = LAST_INSERT_ID();

	INSERT INTO `user_roles` (`role_id`, `role_type`)
		VALUES(roleID, 1);


	INSERT INTO users (first_name,last_name,email,verified,password,access_token,access_token_expired) VALUES
		('J','Smith','noadmin@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','9f92fb02ecc361000c2ae3ce0982424f',now()+interval 10 year);

	SET userID = LAST_INSERT_ID();


	insert into roles (user_id, site_id, company_id) values
		(userID, siteID, companyID);

	SET roleID = LAST_INSERT_ID();

	INSERT INTO `user_roles` (`role_id`, `role_type`)
		VALUES(roleID, 6);

END
$$
DELIMITER ;
CALL setup_tests();
DROP PROCEDURE IF EXISTS `setup_tests`;