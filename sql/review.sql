CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint unsigned not null AUTO_INCREMENT primary key,
  `site_id` bigint unsigned NOT NULL,
  `rating` int NOT NULL,
  `comment`varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

alter table directory_listings add column `sub_category` VARCHAR( 255 ) NULL;

alter table reviews add column `user_id` bigint(22) NOT NULL;

alter table directory_listings add column `total_members` int(11) NULL;
update directory_listings set category = 'Other' , sub_category = 'Other' where category is null;
Update
  directory_listings as d
  inner join (
    select id,total_members
    	from sites c
  ) as smd on smd.id = d.site_id
set d.total_members = smd.total_members;

insert into directory_listings (site_id,title,pending_title,category,sub_category,description,pending_description,subtitle,pending_subtitle,permalink,total_lessons,total_revenue,total_members,is_approved,created_at) 
	select id, name,name, 'Other','Other',
	'this is dummy description',
	'this is dummy description',
	'this is brief description',
	'this is brief description',
	CONCAT(REPLACE(name,' ','-'),'-',id),
	total_lessons,
	total_revenue,
	total_members,
	1,
	CURRENT_TIMESTAMP
	from sites c;


-- image insert
Update
  directory_listings as d
  inner join (
    select `value`,`site_id`
    from site_meta_data
    where `key` = "site_logo"
    group by site_id
  ) as smd on smd.site_id = d.site_id
set d.image = smd.value, d.pending_image = smd.value ;


-- total_downloads insert

Update
  directory_listings as d
  inner join (
    select site_id, count(*) as NumberOfDownloads
    from download_center as dc
    where deleted_at is NULL
    group by site_id
  ) as dl on d.site_id = dl.site_id
set d.total_downloads = dl.NumberOfDownloads;

-- pricing insert

Update
  directory_listings as d
  inner join (
    SELECT site_id, min(price) as min_price, max(price) as max_price from access_levels group by site_id
  ) as a on d.site_id = a.site_id
  set d.pricing = case when a.min_price = a.max_price then a.min_price else CONCAT(a.min_price, ' ', a.max_price) end, d.pending_pricing = case when a.min_price = a.max_price then a.min_price else CONCAT(a.min_price, ' ', a.max_price) end;
  
-- where al.deleted_at is NULL and

update directory_listings set category = 'Development' , sub_category = 'Web Development' where category = 'Other' ORDER BY total_revenue DESC limit 25
update directory_listings set category = 'Development' , sub_category = 'Mobile Apps' where category = 'Other' ORDER BY total_revenue DESC limit 25
update directory_listings set category = 'Business' , sub_category = 'Finance' where category = 'Other' ORDER BY total_revenue DESC limit 25
update directory_listings set category = 'Business' , sub_category = 'Entrepreneurship' where category = 'Other' ORDER BY total_revenue DESC limit 25
