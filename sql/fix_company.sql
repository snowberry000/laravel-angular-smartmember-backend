drop index user_site_company on roles;

UPDATE companies  SET deleted_at = '2015-09-30' where user_id not in (select r.user_id from roles r inner join user_roles ur on ur.role_id = r.id where r.site_id=1) order by name;
update companies set name = REPLACE(name, '\'sCompany', '\'s Team');
	
source fix_team_name.sql

source remove_company_duplicates.sql