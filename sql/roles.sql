drop table sites_roles, sites_custom_roles, sites_custom_roles_capabilities;

CREATE TABLE sites_roles(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  type varchar(255) not null,
  site_id bigint unsigned not null,
  user_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE sites_custom_roles(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  name varchar(255),
  site_id bigint unsigned not null,
  user_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

CREATE TABLE sites_custom_roles_capabilities(
  id bigint unsigned not null AUTO_INCREMENT primary key,
  type varchar(255),
  capability varchar(255) not null,
  site_id bigint unsigned not null,
  deleted_at timestamp null default null,
  updated_at timestamp,
  created_at timestamp default CURRENT_TIMESTAMP
);

insert into sites_roles (type,site_id,user_id) values
    ('vip',6325,1),
    ('custom_role',6325,1);


insert into sites_custom_roles (name,site_id,user_id) 
    values ('custom_role',6325,1);

insert into sites_custom_roles_capabilities (type,capability,site_id) values 
  ('custom_role','view_restricted_content',6325),
  ('custom_role','manage_email',6325);


