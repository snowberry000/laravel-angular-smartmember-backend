alter table affcontests add content text after last_refreshed;

ALTER TABLE  `unsubscribers_segment` CHANGE  `company_id`  `site_id` BIGINT( 20 ) NOT NULL ;