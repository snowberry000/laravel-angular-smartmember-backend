drop database if exists smartmembers;
create database smartmembers;
use smartmembers;

source sql/schema.sql;


# Create Dummy users (Password is a hash of hello123)
insert into users (id,first_name,last_name,email,verified,password,access_token,access_token_expired) values
	(1,'John','Smith','admin@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','9f92fb02ecc361000c2ae3ce0982424f',now()+interval 10 year),
	(2,'Bronze','User','bronze@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','a9f92fb02ecc361000c2ae3ce0982424b',now()+interval 10 year),
	(3,'Silver','User','silver@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','b9f92fb02ecc361000c2ae3ce0982424s',now()+interval 10 year),
	(4,'Gold','User','gold@smartmember.com',true,'$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6','c9f92fb02ecc361000c2ae3ce0982424g',now()+interval 10 year);

# Create Dummy site
insert into sites (id,subdomain,name,user_id,total_members,company_id) values (1,'training','Tutorials',1,4,1);
insert into roles (user_id,site_id,company_id) values
	(1,1,1),(2,1,1),(3,1,1),(4,1,1);

insert into companies(name,user_id, hash)
	values("John'sCompany", 1, 'db942a987f09ee53fca8a726ee3735c3'),
		  ("Bronze'sCompany", 2, '9ff4369a7650d82ed23336f171a73419'),
		  ("Silver'sCompany", 3, '701aa56e0f933b38b386148197082789'),
		  ("Gold'sCompany", 4, '5f98debf393b4a6ce1f8e76a3d61a858');

# Access levels
insert into access_levels (id,site_id,name,information_url,redirect_url,product_id,price, hash) values
	(1,1,'Bronze','/','/','12234',49, '17a499a55499f79526fddf9c3c4e4a6e'),
	(2,1,'Silver','/','/','123455',99, '81116641eafba829acc9cfa34653e4e9'),
	(3,1,'Gold','/','/','11212',199, '55b0aebe246b73bcff03719ade06584e');

insert into access_grants(access_level_id,grant_id) values
	(2,1),(3,1),(3,2);
insert into access_passes(site_id,access_level_id,user_id,expired_at) values
	(1,3,1,now()+interval 10 year),(1,1,2,now()+interval 10 year),(1,2,3,now()+interval 10 year),(1,3,4,now()+interval 10 year);

INSERT INTO `role_types` (`id`, `role_name`)
VALUES(1, 'Primary Owner'),(2, 'Owner'),(3, 'Manager'),(4, 'Admin'),(5, 'Agent'),(6, 'Member');

INSERT INTO `user_roles` (`role_id`, `role_type`)
VALUES(1, 1),(2, 3),(3, 4),(4, 6);

INSERT INTO `payment_methods` (`id`,`name`)
VALUES(1, 'jvzoo'),(2, 'paypal'),(3, 'stripe');

-- INSERT INTO `email_setting_types` (`id`, `type_name`, `logo_url`, `description`, `available`, `deleted_at`, `created_at`, `updated_at`)
-- 	VALUES (1, 'SendGrid', 'http://imbmediab.s3.amazonaws.com/wp-content/uploads/2015/06/sendgrid-logo.png', 'Send custom email & transactional messages through SendGrid\'s top-tier reliable email servers through Smart Mail!', 1, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');