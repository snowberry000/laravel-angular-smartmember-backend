use smartmembers;

ALTER TABLE smartmembers.discussion_settings add column target_id INT;
ALTER TABLE smartmembers.discussion_settings add column type varchar(100);

DROP TABLE IF EXISTS tmp_migrate_show_comments;

CREATE TABLE tmp_migrate_show_comments
	SELECT c.type, c.site_id, c.meta_key, c.meta_value, c.target_id from smartmember.smartm_extra_meta c where c.meta_key = "show_comments";

CREATE INDEX id_temp1 ON tmp_migrate_show_comments (meta_key);
CREATE INDEX id_temp2 ON tmp_migrate_show_comments (target_id);

DROP TABLE IF EXISTS tmp_newest_comments_first;

CREATE TABLE tmp_newest_comments_first
	SELECT c.type, c.meta_key, c.meta_value, c.target_id from smartmember.smartm_extra_meta c where c.meta_key = "newest_comments_first";

CREATE INDEX id_temp3 ON tmp_newest_comments_first (meta_key);
CREATE INDEX id_temp4 ON tmp_newest_comments_first (target_id);

DROP TABLE IF EXISTS tmp_close_to_new_comments;

CREATE TABLE tmp_close_to_new_comments
	SELECT c.type, c.meta_key, c.meta_value, c.target_id from smartmember.smartm_extra_meta c where c.meta_key = "close_to_new_comments";

CREATE INDEX id_temp5 ON tmp_close_to_new_comments (meta_key);
CREATE INDEX id_temp6 ON tmp_close_to_new_comments (target_id);

DROP TABLE IF EXISTS tmp_allow_replies_to_all_comments;

CREATE TABLE tmp_allow_replies_to_all_comments
	SELECT c.type, c.meta_key, c.meta_value, c.target_id from smartmember.smartm_extra_meta c where c.meta_key = "allow_replies_to_all_comments";

CREATE INDEX id_temp6 ON tmp_allow_replies_to_all_comments (meta_key);
CREATE INDEX id_temp7 ON tmp_allow_replies_to_all_comments (target_id);


DROP TABLE IF EXISTS tmp_publicize_all_comments;

CREATE TABLE tmp_publicize_all_comments
	SELECT c.type, c.meta_key, c.meta_value, c.target_id from smartmember.smartm_extra_meta c where c.meta_key = "publicize_all_comments";

CREATE INDEX id_temp6 ON tmp_publicize_all_comments (meta_key);
CREATE INDEX id_temp7 ON tmp_publicize_all_comments (target_id);

INSERT INTO smartmembers.discussion_settings (
	type, site_id, target_id, show_comments, newest_comments_first, close_to_new_comments, allow_replies, public_comments)
SELECT t1.type, t1.site_id, t1.target_id, 
		CASE t1.meta_value 
			WHEN "true" then 1
			ELSE 0
		END as `show_comments`, 
		CASE t2.meta_value 
			WHEN "true" then 1
			ELSE 0
		END as `newest_comments_first`,
	    CASE t3.meta_value 
			WHEN "true" then 1
			ELSE 0
		END as `close_to_new_comments`, 
		CASE t4.meta_value 
			WHEN "true" then 1
			ELSE 0
		END as `allow_replies_to_all_comments`,
		CASE t5.meta_value  
			WHEN "true" then 1
			ELSE 0
		END as `publicize_all_comments`
FROM tmp_migrate_show_comments as t1 
LEFT JOIN tmp_newest_comments_first as t2 ON t1.target_id = t2.target_id and t1.type = t2.type
LEFT JOIN tmp_close_to_new_comments as t3 ON t1.target_id = t3.target_id and t1.type = t3.type
LEFT JOIN tmp_allow_replies_to_all_comments as t4 ON t1.target_id = t4.target_id and t1.type = t4.type
LEFT JOIN tmp_publicize_all_comments as t5 ON t1.target_id = t5.target_id and t1.type = t5.type;

DROP TABLE tmp_migrate_show_comments;
DROP TABLE tmp_newest_comments_first;
DROP TABLE tmp_close_to_new_comments;
DROP TABLE tmp_allow_replies_to_all_comments;
DROP TABLE tmp_publicize_all_comments;

CREATE INDEX index_target_id ON smartmembers.discussion_settings(target_id);
CREATE INDEX index_type ON smartmembers.discussion_settings(type);

INSERT into smartmembers.access_passes (
	id, site_id, access_level_id, user_id, created_at, expired_at)
SELECT id, site_id, access_level_id, user_id, 
		case date
			when 0 then NOW()
			ELSE from_unixtime(date)
		END as date,
		case expire_date
			when 0 then 0
			ELSE from_unixtime(expire_date)
		END as expire_date
FROM smartmember.smartm_accessledgers;


INSERT into smartmembers.access_levels (
	id, site_id, name, information_url, redirect_url, product_id, price, payment_interval, stripe_plan_id, hash, created_at)
  SELECT id, site_id, name, info_url, redirect_url, jvzoo_product_id, stripe_price, payment_type, stripe_plan_id, hash, from_unixtime(date) 
	FROM smartmember.smartm_accesslevels;

INSERT into smartmembers.affiliates(
	id, site_id, company_id, created_at, affiliate_request_id, user_id, user_name, user_email, user_country, user_note, admin_note, past_sales,
	product_name, featured_image, original)
  SELECT id, site_id, company_id, from_unixtime(date), affiliate_request_id, 
		jvzoo_user_id, jvzoo_user_name, jvzoo_user_email,jvzoo_user_country,jvzoo_user_note, admin_note, past_sales,
		product_name, featured_image, original from smartmember.smartm_affiliates;

INSERT INTO smartmembers.affteams (
	company_id, created_at, id, name, site_id)
SELECT 
	company_id, from_unixtime(date), id, name, site_id
 FROM smartmember.smartm_affteam;
	

INSERT INTO smartmembers.affteamledger (
	affiliate_id, created_at, id, team_id)
SELECT 
	affiliate_id, from_unixtime(date), id, team_id
 FROM smartmember.smartm_affteamledger;

INSERT INTO smartmembers.canned_responses (
	content, created_at, id, site_id, title )
SELECT 
	content, from_unixtime(date), id, site_id, title
 FROM smartmember.smartm_canned_response;

INSERT INTO smartmembers.comments (
	body, created_at, id, parent_id, public, site_id, target_id, type, user_id)
SELECT 
	comment, from_unixtime(date), id, parent_id, public, site_id, target_id, type, user_id
 FROM smartmember.smartm_comments;


INSERT INTO smartmembers.companies (
	created_at, hash, id, name, profile_image, user_id )
SELECT 
	from_unixtime(date), hash, id, name, profile_image, user_id
FROM smartmember.smartm_companies;

INSERT INTO smartmembers.download_center (
	access_level_type, access_level_id, created_at, creator_id, description, embed_content, featured_image, id,
	media_item_id, site_id, title, permalink, download_button_text)
SELECT  case access_level
			when 'logged_in' then '3'
			when 'public' then '1'
			when 'private' then '4'
			when '' then '1'
			ELSE 2
		END AS access_level_type,
		case 
			when access_level regexp "^[0-9]+$" then access_level
			ELSE 0
		END AS access_level_id,
	from_unixtime(smartm_downloads.date), creator_id, description, embed_content, 
	featured_image, smartm_downloads.id, media_item_id, smartm_downloads.site_id, title, pl.url_slug, 'Download' 
FROM smartmember.smartm_downloads 
LEFT JOIN smartmember.smartm_permalinks pl ON smartm_downloads.id = pl.target_id and pl.type = 'download'
LEFT OUTER JOIN smartmember.smartm_extra_meta m ON m.target_id = smartmember.smartm_downloads.id 
		and m.meta_key = 'download_button_text' and m.type='download';


INSERT INTO smartmembers.lessons (
	access_level_type, access_level_id, audio_file, author_id, content, created_at, 
	embed_content, featured_image, id, module_id, next_lesson, note, 
	presenter, prev_lesson, site_id, sort_order, title, transcript_button_text, transcript_content, permalink, discussion_settings_id)
SELECT  case access_level
			when 'logged_in' then '3'
			when 'public' then '1'
			when 'private' then '4'
			when '' then '1'
			ELSE 2
		END AS access_level_type,
		case 
			when access_level regexp "^[0-9]+$" then access_level
			ELSE 0
		END AS access_level_id,
		audio_file, author_id, content, from_unixtime(smartm_lessons.date), embed_content, featured_image, 
		smartm_lessons.id, module_id, next_lesson, note, presenter, prev_lesson, smartm_lessons.site_id,
		sort_order, title, transcript_button_text, transcript_content, pl.url_slug, ds.id as discussion_settings_id
 FROM smartmember.smartm_lessons
LEFT JOIN smartmember.smartm_permalinks pl ON smartm_lessons.id = pl.target_id and pl.type = 'lesson'
LEFT JOIN smartmembers.discussion_settings as ds ON smartm_lessons.id = ds.target_id and ds.type = 'lesson';


INSERT INTO smartmembers.livecasts (
	access_level_type, access_level_id, company_id, content, created_at,
	embed_content, featured_image, id, note, site_id, title, permalink, discussion_settings_id)
SELECT case access_level
			when 'logged_in' then '3'
			when 'public' then '1'
			when 'private' then '4'
			when '' then '1'
			ELSE 2
		END AS access_level_type,
		case 
			when access_level regexp "^[0-9]+$" then access_level
			ELSE 0
		END AS access_level_id,
		smartm_livecasts.company_id, content, from_unixtime(smartm_livecasts.date), embed_content, featured_image, 
		smartm_livecasts.id, note, smartm_livecasts.site_id, title, pl.url_slug, ds.id
 FROM smartmember.smartm_livecasts
LEFT JOIN smartmember.smartm_permalinks pl ON smartm_livecasts.id = pl.target_id and pl.type = 'livecast'
LEFT JOIN smartmembers.discussion_settings as ds ON smartm_livecasts.id = ds.target_id and ds.type = 'livecast';



INSERT INTO smartmembers.users (
	access_token, access_token_expired, created_at, email,
	first_name, id, password, profile_image, username, vanity_username, verified )
  SELECT 
	md5(concat(email, now() + INTERVAL 3 MONTH)), now() + INTERVAL 3 MONTH,
	case date
		when 0 then NOW()
		ELSE from_unixtime(date)
	End as date,
	email, name, id, password, profile_image, username, vanity_username, 1
 FROM smartmember.smartm_users;

INSERT INTO smartmembers.sites (
	company_id, created_at, domain_mask, id, name, subdomain, user_id, total_lessons, total_members,  total_revenue)
SELECT 
	s.company_id, from_unixtime(s.date), domain_mask, s.id, name, 
	url_slug, user_id, 
	case 
		WHEN l.count IS NULL then 0
		ELSE l.count
	END as lessons_count,
	case 
		WHEN r.count IS NULL then 0
		ELSE r.count
	END as members_count,
	CASE 
		WHEN t.sum IS NULL then 0
		ELSE t.sum
	END as total_revenue
FROM smartmember.smartm_sites s
LEFT JOIN (Select id, count(*) as count, site_id from smartmember.smartm_lessons group by site_id) as l ON s.id = l.site_id
LEFT JOIN (Select id, count(*) as count, site_id from smartmember.smartm_roles group by site_id) as r ON s.id = r.site_id
LEFT JOIN (SELECT id, sum(price) as sum, site_id from smartmember.smartm_transactions where type != 'rfnd' group by site_id) as t ON s.id = t.site_id;

INSERT INTO smartmembers.roles (
	company_id, created_at, id, role_type, site_id, user_id )
SELECT 
	company_id, 
	case date
		when 0 then NOW()
		ELSE from_unixtime(date)
	END as date, 
	id, 
	case role
		when 'owner' then 1
		when 'admin' then 2
		when 'member' then 3
		ELSE 4
	END as role,
	site_id, user_id
FROM smartmember.smartm_roles;

INSERT INTO smartmembers.modules (
	access_level, company_id, created_at, id, note, site_id, sort_order, title)
  SELECT 
	access_level, company_id, from_unixtime(date), id, note, site_id, sort_order, title
 FROM smartmember.smartm_modules;

INSERT IGNORE INTO smartmembers.posts (
	access_level_type, access_level_id, author_id, company_id, content, created_at, embed_content, 
	featured_image, id, note, site_id, title, updated_at, permalink, discussion_settings_id )
SELECT 
	case access_level
			when 'logged_in' then '3'
			when 'public' then '1'
			when 'private' then '4'
			when '' then '1'
			ELSE 2
		END AS access_level_type,
		case 
			when access_level regexp "^[0-9]+$" then access_level
			ELSE 0
		END AS access_level_id,
		author_id, smartm_posts.company_id, content, from_unixtime(smartm_posts.date), 
		embed_content, featured_image, smartm_posts.id, note, smartm_posts.site_id, title, from_unixtime(updated),
		pl.url_slug, ds.id
FROM smartmember.smartm_posts
LEFT JOIN smartmember.smartm_permalinks pl ON smartm_posts.id = pl.target_id and pl.type = 'post'
LEFT JOIN smartmembers.discussion_settings as ds ON smartm_posts.id = ds.target_id and ds.type = 'post';


INSERT INTO smartmembers.support_articles (
	author_id, category_id, company_id, content, created_at, embed_content, 
	featured_image, id, site_id, sort_order, title, permalink)
SELECT 
	author_id, category_id, smartm_support_article.company_id, content, from_unixtime(smartm_support_article.date), embed_content, 
	featured_image, smartm_support_article.id, smartm_support_article.site_id, sort_order, title, pl.url_slug
FROM smartmember.smartm_support_article
LEFT JOIN smartmember.smartm_permalinks pl ON smartm_support_article.id = pl.target_id and pl.type = 'suppport-article';


INSERT INTO smartmembers.support_categories (
	company_id, created_at, id, site_id, sort_order, title)
SELECT 
	company_id, from_unixtime(date), id, site_id, sort_order, title
FROM smartmember.smartm_support_category;


INSERT INTO smartmembers.support_tickets (
	attachment, category, company_id, customer_id, created_at, id, message, 
	parent_id, priority, `read`, site_id, status, subject, type, user_id )
SELECT 
	attachment, category, company_id, customer_id, from_unixtime(date), id, 
	message, parent_id, priority, 
	CASE `read`
		when 0 then false
		when 1 then true
	END as read_flag,
	site_id, status, subject, type, user_id
FROM smartmember.smartm_support_ticket;


INSERT INTO smartmembers.emails (
	company_id, content, created_at, id, mail_name, mail_reply_address, mail_sending_address, mail_signature, site_id, subject)
SELECT 
	company_id, content, from_unixtime(date), id, mail_name, mail_reply_address, mail_sending_address, mail_signature, site_id, subject
FROM smartmember.smartm_emails;


INSERT INTO smartmembers.email_subscribers (
	company_id, created_at, email, hash, id, name, site_id)
SELECT 
	company_id, from_unixtime(date), email, hash, id, name, site_id
FROM smartmember.smartm_emailsubscribers;

INSERT INTO smartmembers.email_lists (
	company_id, created_at, id, name, site_id, total_subscribers)
SELECT 
	company_id, from_unixtime(date), id, name, site_id, 
	CASE
		WHEN ledger.total_subscribers IS NULL then 0
		ELSE ledger.total_subscribers
	END as total_subscribers
FROM smartmember.smartm_emaillists
LEFT JOIN (
	SELECT list_id, count(id) as total_subscribers from smartmember.smartm_listledger group by list_id
) AS ledger ON smartm_emaillists.id = ledger.list_id;



INSERT INTO smartmembers.user_notes (
	company_id, complete, created_at, id, lesson_id, note, site_id, user_id )
SELECT 
	company_id, complete, from_unixtime(date), id, lesson_id, note, site_id, user_id
FROM smartmember.smartm_user_note;



INSERT INTO smartmembers.media_items (
	aws_key, company_id, created_at, id, site_id, title, type, url )
SELECT 
	aws_key, company_id, from_unixtime(date), id, site_id, title, type, url
FROM smartmember.smartm_media_items;


INSERT INTO smartmembers.seo_settings (
	company_id, created_at, id, link_type, meta_key, meta_value, site_id, target_id)
SELECT 
	company_id, from_unixtime(date), id, type, meta_key, meta_value, site_id, target_id
FROM smartmember.smartm_seo_settings;


INSERT INTO smartmembers.special_pages (
	access_level, company_id, content, created_at, embed_content, featured_image, 
	id, multiple, note, site_id, title, type, continue_refund_text, free_item_url)
SELECT 
	access_level, sp.company_id, content, from_unixtime(sp.date), embed_content, 
	featured_image, sp.id, multiple, note, sp.site_id, title, sp.type,
	m2.meta_value as continue_refund_text, m.meta_value as free_item_url 
FROM smartmember.smartm_special_pages sp
LEFT OUTER JOIN smartmember.smartm_extra_meta m ON m.site_id = sp.site_id and m.meta_key = 'free_item_url' and m.type='refund-page'
LEFT OUTER JOIN smartmember.smartm_extra_meta m2 ON m2.site_id = sp.site_id and m2.meta_key = 'continue_refund_text' and m2.type='refund-page';

INSERT INTO smartmembers.templates (
	created_at, path, id, template_name)
SELECT 
	from_unixtime(date), folder_slug, id, name
FROM smartmember.smartm_templates;



INSERT INTO smartmembers.downloads_history (
	created_at, id, site_id, download_id, user_id )
SELECT 
	from_unixtime(date), id, site_id, target_id, user_id
FROM smartmember.smartm_file_download;

INSERT INTO smartmembers.site_notices (
	content, created_at, id, site_id, title)
SELECT 
	content, from_unixtime(date), id, site_id, title
FROM smartmember.smartm_sitenotices;


INSERT INTO smartmembers.email_settings (
	company_id, username, password, full_name, sending_address, replyto_address, email_signature)
SELECT c.company_id, 
	c.meta_value as "username", 
	c2.meta_value as "password", 
	c3.meta_value as "full_name", 
	c4.meta_value as "sending_address",
	c5.meta_value as "replyto_address",
	c6.meta_value as "email_signature" 
FROM smartmember.smartm_company_options c
LEFT OUTER JOIN smartmember.smartm_company_options c2 on c.company_id = c2.company_id and c2.meta_key = "sendgrid_password"
LEFT OUTER JOIN smartmember.smartm_company_options c3 on c.company_id = c3.company_id and c3.meta_key = "mail_name"
LEFT OUTER JOIN smartmember.smartm_company_options c4 on c.company_id = c4.company_id and c4.meta_key = "mail_sending_address"
LEFT OUTER JOIN smartmember.smartm_company_options c5 on c.company_id = c5.company_id and c5.meta_key = "mail_reply_address"
LEFT OUTER JOIN smartmember.smartm_company_options c6 on c.company_id = c6.company_id and c6.meta_key = "email_signature"
WHERE c.meta_key = "sendgrid_username";


INSERT INTO smartmembers.email_listledger (
	company_id, created_at, list_id, site_id, subscriber_id)
SELECT 
	company_id, from_unixtime(date), list_id, site_id, subscriber_id
FROM smartmember.smartm_listledger;


INSERT INTO smartmembers.unsubfeedback(comment, company_id, created_at, email, email_id, id, site_id, unsub_reason)
SELECT 
	comment, company_id, from_unixtime(date), email, email_id, id, site_id, unsub_reason
FROM smartmember.smartm_unsubfeedback;


INSERT INTO smartmembers.site_meta_data (
	site_id, company_id, `key`, `value`, created_at)
SELECT 
	site_id, company_id, meta_key, meta_value, from_unixtime(date) 
FROM smartmember.smartm_site_options 
WHERE meta_key ='sales_page_content' OR meta_key = 'sales_page_embed' 
	OR meta_key = 'sales_page_enabled' OR meta_key = 'sales_page_outro'
	OR meta_key = 'site_logo' OR meta_key = 'site_title'
	OR meta_key = 'course_title';

INSERT IGNORE INTO smartmembers.user_options (
	user_id, meta_key, meta_value, created_at )
SELECT
	user_id, meta_key, meta_value, from_unixtime(date)
FROM smartmember.smartm_user_options;

INSERT INTO smartmembers.transactions (
	affiliate_id, association_hash, company_id, data, created_at, email, id, name, 
	payment_method, price, product_id, site_id, source, transaction_id, type,  user_id )
SELECT 
	affiliate_id, association_hash, company_id, data, from_unixtime(date), email, id, name, 
	payment_method, price, product_id, site_id, source, transaction_id, type, user_id
FROM smartmember.smartm_transactions;


INSERT INTO smartmembers.custom_pages (
	access_level_type, access_level_id, content, created_at, embed_content, featured_image, id, note, site_id, title, permalink, discussion_settings_id )
SELECT 
		case access_level
			when 'logged_in' then '3'
			when 'public' then '1'
			when 'private' then '4'
			when '' then '1'
			ELSE 2
		END AS access_level_type,
		case 
			when access_level regexp "^[0-9]+$" then access_level
			ELSE 0
		END AS access_level_id,
		content, from_unixtime(smartm_pages.date), embed_content, featured_image, smartm_pages.id, note, smartm_pages.site_id, title, pl.url_slug, ds.id
 FROM smartmember.smartm_pages	
LEFT JOIN smartmember.smartm_permalinks pl ON smartm_pages.id = pl.target_id and pl.type = 'page'
LEFT JOIN smartmembers.discussion_settings as ds ON smartm_pages.id = ds.target_id and ds.type = 'page';

INSERT INTO smartmembers.sites_ads (
	 site_id, banner_url, banner_image_url, open_in_new_tab)
SELECT c.site_id,
	c.meta_value as "banner_url", 
	c2.meta_value as "banner_image_url", 
	c3.meta_value as "open_in_new_tab"
FROM smartmember.smartm_site_options c
LEFT OUTER JOIN smartmember.smartm_site_options c2 on c.site_id = c2.site_id and c2.meta_key = "course_banner_image"
LEFT OUTER JOIN smartmember.smartm_site_options c3 on c.site_id = c3.site_id and c3.meta_key = "course_banner_open_in_new_tab"
WHERE c.meta_key = "course_banner_url";


INSERT INTO smartmembers.emails_queue (
	company_id, created_at, email_id, id, list_type, site_id, subscriber_id)
SELECT 
	company_id, from_unixtime(date), email_id, id, list_type, site_id, subscriber_id
FROM smartmember.smartm_queueitem;




INSERT INTO smartmembers.email_history (
	auto_id, company_id, created_at, email_id, id, list_type, site_id, subscriber_id )
SELECT 
	auto_id, company_id, from_unixtime(date), email_id, id, list_type, site_id, subscriber_id
FROM smartmember.smartm_emailhistory;



INSERT INTO smartmembers.speed_blogs (
  id, name, website_url, endpoint_url, username, password, user_id, sbc_hash,
  sbc_endpoint, created_at )
SELECT
  id, name, website_url, endpoint_url, username, password, user_id, sbc_hash,
  sbc_endpoint, from_unixtime( date )
FROM smartmember.smartm_speed_blogs;

INSERT INTO smartmembers.speed_posts (
  id, user_id, blog_id, wp_post_id, post_mode, post_title, update_count, list_items,
   type, created_at )
SELECT
  id, user_id, blog_id, wp_post_id, post_mode, post_title, update_count, list_items,
  type, from_unixtime( date )
FROM smartmember.smartm_speed_posts;

INSERT INTO smartmembers.bridge_bpages (
	id, user_id, name, template_id, content, meta, type_id, created_at )
SELECT 
	id, user_id, name, template_id, content, meta, type_id, from_unixtime(date)
FROM smartmember.smartm_bridge_bpages;

INSERT INTO smartmembers.bridge_media_items (
	id, site_id, company_id, title, url, aws_key, type, created_at )
SELECT 
	id, site_id, company_id, title, url, aws_key, type, from_unixtime(date)
FROM smartmember.smartm_bridge_media_items;

INSERT INTO smartmembers.bridge_permalinks (
	id, user_id, url_slug, target_id, created_at )
SELECT 
	id, user_id, url_slug, target_id, from_unixtime(date)
FROM smartmember.smartm_bridge_permalinks;

INSERT INTO smartmembers.bridge_seo_settings (
	id, type, target_id, meta_key, meta_value, created_at)
SELECT
    id, type, target_id, meta_key, meta_value, from_unixtime(date)
FROM smartmember.smartm_bridge_seo_settings;

INSERT INTO smartmembers.bridge_templates (
	id, type_id, name, folder_slug, icon, created_at)
SELECT
	id, type_id, name, folder_slug, icon, from_unixtime(date)
FROM smartmember.smartm_bridge_templates;
	
INSERT INTO smartmembers.bridge_types (
	id, name, folder_slug, icon, description, created_at)
SELECT
	id, name, folder_slug, icon, description, from_unixtime(date)
FROM smartmember.smartm_bridge_types;

INSERT INTO smartmembers.bridge_user_options (
	id, user_id, meta_key, meta_value, created_at)
SELECT
	id, user_id, meta_key, meta_value, from_unixtime(date)
FROM smartmember.smartm_bridge_user_options;


ALTER TABLE smartmembers.discussion_settings DROP COLUMN target_id;
ALTER TABLE smartmembers.discussion_settings DROP COLUMN type;


INSERT INTO `role_types` (`id`, `role_name`)
VALUES(1, 'Owner'),(2, 'Admin'),(3, 'Member');

-- Need them? 

-- INSERT INTO smartmembers.tax_assoc (
-- 	created_at, id, site_id, target_id, tax_type_id, taxonomy_id )
-- SELECT 
-- 	from_unixtime(date), id, site_id, target_id, type, taxonomy_id
-- FROM smartmember.tax_assoc

-- INSERT INTO smartmembers.taxonomies (
--  created_at, deleted_at, id, site_id, tax_type_id, title, updated_at )
--   SELECT 
-- from_unixtime(date), id, site_id, title, type
--  FROM smartmember.taxonomies
-- 
-- INSERT INTO smartmembers.templates (
--  created_at, deleted_at, id, path, template_name, updated_at )
--   SELECT 
-- from_unixtime(date), folder_slug, icon, id, name, type_id
--  FROM smartmember.templates
-- 


