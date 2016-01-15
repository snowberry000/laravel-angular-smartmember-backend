create table permalinks(
	id bigint unsigned not null auto_increment primary key,
	permalink text not null,
	site_id bigint(20) not null,
	target_id bigint(20) not null,
	type varchar(255),
	deleted_at datetime,
	updated_at datetime,
	created_at timestamp default current_timestamp
);


insert into permalinks(permalink,site_id,target_id,type) (select permalink,site_id,id,'lessons' from lessons);
insert into permalinks(permalink,site_id,target_id,type) (select permalink,site_id,id,'custom_pages' from custom_pages);
insert into permalinks(permalink,site_id,target_id,type) (select permalink,site_id,id,'download_center' from download_center);
insert into permalinks(permalink,site_id,target_id,type) (select permalink,site_id,id,'posts' from posts);
insert into permalinks(permalink,site_id,target_id,type) (select permalink,site_id,id,'livecasts' from livecasts);
insert into permalinks(permalink,site_id,target_id,type) (select permalink,site_id,id,'support_articles' from support_articles);

-- Support Articles