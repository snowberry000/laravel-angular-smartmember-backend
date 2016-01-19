alter table affcontests add content text after last_refreshed;

ALTER TABLE  `imports_queue` ADD  `name` VARCHAR( 50 ) NULL AFTER  `id` ;