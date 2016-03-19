CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint unsigned not null AUTO_INCREMENT primary key,
  `site_id` bigint unsigned NOT NULL,
  `rating` int NOT NULL,
  `comment`varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);


insert into directory_listings (site_id,title,category,description,subtitle,permalink,total_lessons,total_revenue,total_members,is_approved) 
	select id, name, 'other',
	'this is dummy description',
	'this is brief description',
	CONCAT(REPLACE(' ','-'),'-',site_id),
	total_lessons,
	total_revenue,
	total_members,
	1
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
set d.image = smd.value


-- total_downloads insert

Update
  directory_listings as d
  inner join (
    select site_id, count(*) as NumberOfDownloads
    from download_center as dc
    where deleted_at is NULL
    group by site_id
  ) as dl on d.site_id = dl.site_id
set d.total_downloads = dl.NumberOfDownloads

-- pricing insert

Update
  directory_listings as d
  inner join (
    SELECT site_id, min(price) as min_price, max(price) as max_price from access_levels group by site_id
  ) as a on d.site_id = a.site_id
  set d.pricing = case when a.min_price = a.max_price then a.min_price else CONCAT(a.min_price, ' ', a.max_price) end
  
-- where al.deleted_at is NULL and