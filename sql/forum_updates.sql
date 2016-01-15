drop table forum_categories; drop table forum_topics; drop table forum_replies;

CREATE TABLE forum_categories(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  title varchar(255),
  description text, 
  parent_id bigint unsigned default 0,
  site_id bigint unsigned not null,
  access_level_id bigint unsigned not null,
  access_level_type int default 1,
  allow_content boolean default true,
  total_replies int default 0,
  total_topics int default 0,
  icon text,
  permalink text,
  deleted_at timestamp NULL default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE forum_topics(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  title varchar(255),
  description text, 
  category_id bigint unsigned not null,
  total_replies int default 0,
  total_views int default 0,
  total_likes int default 0,
  status varchar(255),
  user_id bigint not null,
  site_id bigint not null,
  allow_content boolean default true,
  permalink text,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE forum_replies(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  content text,
  topic_id bigint unsigned not null,
  category_id bigint unsigned not null,
  user_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

insert into forum_categories(id,title,description,permalink,icon,total_topics, total_replies,parent_id,site_id,access_level_type,access_level_id) values 
	(1,'Facebook Advertising','This is the description text','facebook-advertisement-1','facebook',0,0,0,6325,1,0),
	(2,'YouTube Advertising','This is the description text','youtube-advertisement-1','youtube',0,0,0,6325,3,0),
	(3,'Shopify Selling','This is the description text','shopify-selling-1','cart',0,0,0,6325,3,0),
  (4,'Google Adwords','This is the description text','google-adwords-1','google',0,0,0,6325,4,0);

insert into permalinks(permalink,site_id,target_id,type) values
  ('facebook-advertisement-1',6325,1,'forum_categories'),
  ('youtube-advertisement-1',6325,1,'forum_categories'),
  ('shopify-selling-1',6325,1,'forum_categories'),
  ('google-adwords-1',6325,1,'forum_categories');



