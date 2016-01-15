drop database if exists smartmembers;
create database smartmembers;
use smartmembers;

source sql/schema.sql;

INSERT INTO `access_grants` (`id`, `access_level_id`, `grant_id`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1, 2, 1, NULL, NULL, '2015-08-07 15:37:10'),
(2, 3, 1, NULL, NULL, '2015-08-07 15:37:10'),
(3, 3, 2, NULL, NULL, '2015-08-07 15:37:10');

INSERT INTO `access_levels` (`id`, `site_id`, `name`, `information_url`, `redirect_url`, `product_id`, `price`, `payment_interval`, `stripe_plan_id`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1, 1, 'Bronze', '/', '/', '12234', 49.00, 'one_time', NULL, NULL, NULL, '2015-08-07 15:37:10'),
(2, 1, 'Silver', '/', '/', '123455', 99.00, 'one_time', NULL, NULL, NULL, '2015-08-07 15:37:10'),
(3, 1, 'Gold', '/', '/', '11212', 199.00, 'one_time', NULL, NULL, NULL, '2015-08-07 15:37:10');

INSERT INTO `access_passes` (`id`, `site_id`, `access_level_id`, `user_id`, `expired_at`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1, 1, 3, 1, '2025-08-07 20:37:11', NULL, NULL, '2015-08-07 15:37:11'),
(2, 1, 1, 2, '2025-08-07 20:37:11', NULL, NULL, '2015-08-07 15:37:11'),
(3, 1, 2, 3, '2025-08-07 20:37:11', NULL, NULL, '2015-08-07 15:37:11'),
(4, 1, 3, 4, '2025-08-07 20:37:11', NULL, NULL, '2015-08-07 15:37:11');

INSERT INTO `affiliates` (`id`, `site_id`, `company_id`, `affiliate_request_id`, `user_id`, `user_name`, `user_email`, `user_country`, `user_note`, `admin_note`, `past_sales`, `product_name`, `featured_image`, `original`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 7846325, 165865, 'Chata Hargrow', 'Chataargrow62@outlook.com', 'UNITED STATES', 'Use Social Media and paid search to Promote the offer.', '', 9, '', NULL, '', NULL, '2015-08-07 10:56:51', '2015-08-07 10:56:51'),
(2, 1, 0, 10468419, 225311, 'Ilian Saev', 'smyrfcheto86@gmail.com', 'BULGARIA', 'I will promote your products as  targeting competitor sites', '', 250, '', NULL, '', NULL, '2015-08-07 10:57:59', '2015-08-07 10:57:59');

INSERT INTO `affteamledger` (`id`, `team_id`, `affiliate_id`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1, 1, 1, NULL, '2015-08-07 10:59:47', '2015-08-07 10:59:47'),
(2, 2, 2, NULL, '2015-08-07 10:59:57', '2015-08-07 10:59:57'),
(3, 2, 1, NULL, '2015-08-07 10:59:57', '2015-08-07 10:59:57');

INSERT INTO `affteams` (`id`, `name`, `site_id`, `company_id`, `deleted_at`, `updated_at`, `created_at`) VALUES
(1, 'Team 1', 1, NULL, NULL, '2015-08-07 10:59:47', '2015-08-07 10:59:47'),
(2, 'Team 2', 1, NULL, NULL, '2015-08-07 10:59:57', '2015-08-07 10:59:57');

INSERT INTO `canned_responses` (`id`, `site_id`, `title`, `content`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Predefined response', 'Hey, I am a response', NULL, '2015-08-07 11:42:34', '2015-08-07 11:42:34');

INSERT INTO `custom_pages` (`id`, `site_id`, `title`, `content`, `note`, `embed_content`, `featured_image`, `access_level_type`, `access_level_id`, `discussion_settings_id`, `permalink`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Services Available', '<p>Below is a list of various opportunities available to you as a member.</p><p><span style="line-height:1.5;">The list will grow over time.  We will eventually turn all of the options below into links. These may link to a page that gives instructions on how to use it, and/or access to a tool we can share with you.  There may also be links to additional training and / or the sales page so you can buy it if necessary.</span><br /></p><p><span style="line-height:1.5;">If you see anything you are interested in for any reason, let us know.  We will try to build out the parts we know you''ll need soon.</span><br /></p><p><br /></p><h3><span style="font-weight:bold;">Our Services</span></h3><ol><li><a href="http://theazonzoo.smartmember.com/lesson/product-selection-assistance" style="color:rgb(0,0,255);text-decoration:underline;">Product Selection Assistance</a></li><li><a href="http://theazonzoo.smartmember.com/lesson/china-sourcing-agent" style="text-decoration:underline;color:rgb(0,0,255);">Using the China Sourcing Agent</a></li><li><a href="http://theazonzoo.smartmember.com/lesson/voting-reviews" target="_blank" style="color:rgb(0,0,255);text-decoration:underline;">Voting Reviews Up &amp; Down</a></li></ol><p><br /></p><p><span style="font-weight:bold;color:inherit;font-family:''Helvetica Neue'', Helvetica, Arial, sans-serif;font-size:24px;line-height:1.1;">Software</span><br /></p><ol><li>Merchant Words</li><li>Keyword Inspector</li><li>ScrapeBox</li><li><span style="color:rgb(0,0,255);text-decoration:underline;"><a href="http://theazonzoo.smartmember.com/lesson/amz-tracker" target="_blank">AMZ Tracker</a></span></li><li>FBA Calculator</li><li>Cost Template</li><li>Profit Spotlight</li><li>Best Seller Matrix</li><li>AMA Suite</li><li>FBA Toolkit</li><li>AZON Poer Pack</li><li>Camel Camel Camel</li><li>TaxJar</li><li>Windward Tax</li><li><a href="http://theazonzoo.smartmember.com/lesson/setting-up-feedback-genius" style="text-decoration:underline;color:rgb(0,0,255);">Feedback Genius</a></li><li>AZON VIP</li><li>ASM Ranker</li></ol><p><br /></p><h3><span style="font-weight:bold;">Outside Services</span></h3><ol><li>Ordering Photography</li><li>Ordering Lifestyle Images</li><li>Ordering Digital Images</li><li>Ordering Copy for listings</li><li>Windward Tax</li><li>Accounting</li><li>Lawyer</li></ol><p><br /></p><h3><span style="font-weight:bold;">Marketing</span></h3><ol><li><span style="line-height:1.5;">Submitting products to Coupon Code sites.</span><br /></li><li><span style="line-height:1.5;">Domain Names, Web Hosting, and Web Development</span></li><li><span style="line-height:1.5;">Creating a logo</span></li><li><span style="line-height:1.5;">Ordering titles and content for blog posts</span></li><li><span style="line-height:1.5;">Ordering and distributing press releases</span></li><li>More to come...</li></ol><p><br /></p>', '', '', '', 2, 2, 5, 'services-available', NULL, '2015-08-07 11:44:31', '2015-08-07 11:44:31');

INSERT INTO `discussion_settings` (`id`, `site_id`, `show_comments`, `newest_comments_first`, `close_to_new_comments`, `allow_replies`, `public_comments`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 1, 0, 0, 1, NULL, '2015-08-07 10:44:27', '2015-08-07 10:44:27'),
(2, 0, 1, 1, 0, 0, 1, NULL, '2015-08-07 10:46:08', '2015-08-07 10:46:08'),
(3, 0, 0, 0, 0, 0, 0, NULL, '2015-08-07 10:47:28', '2015-08-07 10:47:28'),
(4, 0, 1, 1, 0, 0, 0, NULL, '2015-08-07 11:43:45', '2015-08-07 11:43:45'),
(5, 0, 1, 0, 0, 0, 0, NULL, '2015-08-07 11:44:31', '2015-08-07 11:44:31'),
(6, 0, 0, 0, 0, 0, 0, NULL, '2015-08-07 11:45:09', '2015-08-07 11:45:09'),
(7, 0, 0, 0, 0, 0, 0, NULL, '2015-08-10 02:52:24', '2015-08-10 02:52:24'),
(8, 0, 0, 0, 0, 0, 0, NULL, '2015-08-10 02:54:04', '2015-08-10 02:54:04'),
(9, 0, 0, 0, 0, 0, 0, NULL, '2015-08-10 02:54:27', '2015-08-10 02:54:27');

INSERT INTO `download_center` (`id`, `site_id`, `creator_id`, `title`, `description`, `download_button_text`, `media_item_id`, `access_level_type`, `access_level_id`, `embed_content`, `featured_image`, `permalink`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Stop Smoking Mind-Power Program (Audio)-Download', 'Mind-power audio program to successfully stop smoking. To be used daily, alone, or in conjunction with the Peter Powers'' Stop Smoking, 30 Day Course.', '', 0, 1, 1, '', '', 'stop-smoking-mind-power-program-audio-download', NULL, '2015-08-07 10:49:51', '2015-08-07 10:49:51');

INSERT INTO `lessons` (`id`, `date`, `site_id`, `author_id`, `module_id`, `sort_order`, `next_lesson`, `prev_lesson`, `presenter`, `title`, `content`, `note`, `type`, `embed_content`, `featured_image`, `transcript_content`, `audio_file`, `access_level_type`, `access_level_id`, `discussion_settings_id`, `permalink`, `remote_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 1, 1, 1, 2, 0, 'Corey Reed', 'Thoughts on Financials & Accounting', '<p>To get started, here are some things you need to know about Financials and Accounting.  In this video, you''ll learn...</p><p>* Get a bank account and credit card</p><p>* Purchase everything with these accounts</p><p>* Accounting is easy</p><p>* There''s no need for QuickBooks or an Accountant initially</p><p>* You''ll need an accountant at the end of the year for taxes</p><p>* To track your progress, total your bank account, subtract your credit card balance, and total your inventory.  Do this every two weeks when paid by Amazon.</p><p>* Nothing else is needed at this point for accounting.</p>', '00:03:31', '', '<iframe width="560" height="315" src="https://www.youtube.com/embed/RpDZcd5JvkE?rel=0&amp;showinfo=0" allowfullscreen=""></iframe>', '', '', '', 1, 1, 1, 'thoughts-financials-accounting', NULL, NULL, '2015-08-07 10:44:27', '2015-08-10 02:49:07'),
(2, 0, 1, 1, 1, 2, 3, 1, 'Jessica Doucette and Darren Little', 'step up to freedom', 'How to market without push back. How to generate income online. How to use the leverage of quantum physics to build your business.<br>', 'Video length: 55 minutes', '', '<iframe width="560" height="315" src="https://www.youtube.com/embed/rcXDJL03xsQ" frameborder="0" allowfullscreen></iframe>', '', '', '', 2, 2, 2, 'step-freedom', NULL, NULL, '2015-08-07 10:46:08', '2015-08-10 02:49:07'),
(3, 0, 1, 1, 2, 3, 0, 2, 'Jessica Doucette', '5 Clever Ways to Market Your Product on Twitter', '<p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p>Do you sell products online?&nbsp; Whether you run an online retail store or if you sell on eBay, internet marketing should be a component of your business plan.&nbsp; When you market your products, you increase your earnings potential.<br><br>In terms of internet marketing, there are many successful approaches.&nbsp; These approaches include article directories, purchasing advertisements, banner exchanges, and search engine optimization.&nbsp; Yes, you should implement each of these steps, but also examine unconventional approaches, such as Twitter.<br><br>Twitter is a social networking website.&nbsp; When you register for a free account, you can search for other users.&nbsp; You can then become a follower.&nbsp; This means that you will receive their messages, also known as updates or Tweets.&nbsp; Many will do the same for you.&nbsp; Search for contacts on the Twitter website based on name, location, or email address.&nbsp; Also, exchange contact information on online communities, like internet forums.<br><br>As previously stated, Twitter is a social networking website.&nbsp; All types of posts are allowed, but many expect social messages online, not advertisements.&nbsp; This does not mean that you cannot use Twitter as an internet marketing tool.&nbsp; It means that you must take the clever approach.&nbsp; As for how you can do so:<br><br>Ask for feedback.&nbsp; With an advertisement, your Tweet may say “Buy eco-friendly products at affordable prices.”&nbsp; Yes, this may work, but you will find some individuals who think “great, another advertisement.”&nbsp; So, instead ask for feedback.&nbsp; Provide a link and ask your followers to review the product in question.&nbsp; Do they think it can help the environment, is the price affordable?<br><br>Offer promotional codes.&nbsp; Even if you use affiliate links to generate income, you should be provided with moneysaving promotional codes.&nbsp; Offer these to your members.&nbsp; For example, your Tweet could include the message “Eco-friendly products available for sale with a moneysaving discount for all my Twitter friends.”&nbsp; Yes, this is still an advertisement, but you are offering an incentive.<br><br>Host contests and Tweet about them.&nbsp; A great way to generate traffic to a website, including a website where products are sold, is to offer something free.&nbsp; Whether it be a free sample or a contest, people love free stuff.&nbsp; Instead of advertising a product you have for sale, highlight a contest on your website.&nbsp; This will get people to your online store.&nbsp; To increase sales, have your contest landing page filled with products.<br><br>Incorporate personal messages into your advertisements.&nbsp; As previously stated, the use of promotional codes is a great way to not only increase your sales and website traffic, but to generate interest.&nbsp; Instead of just staying “Save $25 with a promotional code provided by me,” incorporate a personal message.&nbsp; Mention you are extending the offer to your Twitter friends because you want to help them save money.&nbsp; This extra personal message goes a long way.<br><br>Use @replies.&nbsp; Mentioning the products you sell is okay to do on Twitter, but be limited in your messages.&nbsp; Do not send 10 messages a day highlighting the products you sell.&nbsp; Instead, try one a day.&nbsp; Also, rely on the use of @replies.&nbsp; You can reply to those who send you updates or use Search.Twitter.com.&nbsp; Only reply with a clever advertisements when the situation calls for it.<br><br>The key to using Twitter as an internet marketing tool is to do so cleverly.&nbsp; Internet users tend to sway away from advertisements, so make yours advertisements in disguise.<br><br></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p></p>', '', '', '', '', '', '', 3, 1, 3, '5-clever-ways-market-your-product-twitter', NULL, NULL, '2015-08-07 10:47:28', '2015-08-07 10:48:37'),
(4, 0, 1, 1, 2, 0, 0, 0, NULL, '4th lesson', '', '', '', '', '', '', '', 1, 1, 7, '', NULL, NULL, '2015-08-10 02:52:24', '2015-08-10 02:52:35'),
(5, 0, 1, 1, 2, 0, 0, 0, NULL, '', '', '', '', '', '', '', '', 1, 1, 8, '-2015-08-10T07:54:04+00:00', NULL, NULL, '2015-08-10 02:54:04', '2015-08-10 02:54:04'),
(6, 0, 1, 1, 2, 0, 0, 0, NULL, '', '', '', '', '', '', '', '', 1, 1, 9, '-2015-08-10T07:54:27+00:00', NULL, NULL, '2015-08-10 02:54:27', '2015-08-10 02:54:27');

INSERT INTO `livecasts` (`id`, `site_id`, `author_id`, `company_id`, `title`, `content`, `note`, `embed_content`, `featured_image`, `access_level_type`, `access_level_id`, `permalink`, `discussion_settings_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'Chris Record & Mario Brown Demonstrating Smart Member', '<p></p><p>I just love this membership software. If you want to learn more <a data-bypass="true" target="_blank" href="http://jvz1.com/c/301447/167089!">Click Here!</a><br></p><p></p>', '', '<iframe width="560" height="315" src="https://www.youtube.com/embed/6TCwWkYtupE" frameborder="0" allowfullscreen></iframe>', '', 1, 1, 'chris-record-mario-brown-demonstrating-smart-member', 6, NULL, '2015-08-07 11:45:09', '2015-08-07 11:45:09');

INSERT INTO `modules` (`id`, `site_id`, `company_id`, `date`, `sort_order`, `title`, `note`, `access_level`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 0, 1, 'ProjectSelf Mastery', '', 0, NULL, '2015-08-07 10:47:52', '2015-08-07 10:48:37'),
(2, 1, NULL, 0, 2, '2 - Assessment: What Needs Changing?', '', 0, NULL, '2015-08-07 10:48:04', '2015-08-07 10:48:37');

INSERT INTO `posts` (`id`, `site_id`, `author_id`, `company_id`, `title`, `content`, `note`, `embed_content`, `featured_image`, `access_level_type`, `access_level_id`, `permalink`, `discussion_settings_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 'Where did English come from? - Claire Bowern', 'https://www.youtube.com/watch?v=YEaSxhcns7Y\n\nView full lesson: http://ed.ted.com/lessons/where-did-english-come-from-claire-bowern\n\nWhen we talk about ‘English’, we often think of it as a single language. But what do the dialects spoken in dozens of countries around the world have in common with each other, or with the writings of Chaucer? Claire Bowern traces the language from the present day back to its ancient roots, showing how English has evolved through generations of speakers.\n\nLesson by Claire Bowern, animation by Patrick Smith.', '', '<iframe frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/YEaSxhcns7Y"></iframe>', '', 1, 1, 'where-did-english-come-claire-bowern', 4, NULL, '2015-08-07 11:43:45', '2015-08-07 11:43:45');

INSERT INTO `roles` (`id`, `user_id`, `site_id`, `company_id`, `role_type`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 1, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 2, 1, NULL, 3, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 3, 1, NULL, 3, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 4, 1, NULL, 3, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `role_types` (`id`, `role_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Owner', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Admin', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Member', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `seo_settings` (`id`, `site_id`, `company_id`, `link_type`, `target_id`, `meta_key`, `meta_value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2', 1, 'fb_share_title', 'Thoughts on Financials & Accounting', NULL, '2015-08-07 10:44:27', '2015-08-07 10:44:27'),
(2, 1, NULL, '2', 2, 'fb_share_title', 'step up to freedom', NULL, '2015-08-07 10:46:08', '2015-08-07 10:46:08'),
(3, 1, NULL, '2', 3, 'fb_share_title', '5 Clever Ways to Market Your Product on Twitter', NULL, '2015-08-07 10:47:28', '2015-08-07 10:47:28');

INSERT INTO `sites` (`id`, `subdomain`, `name`, `template_id`, `user_id`, `total_members`, `total_lessons`, `total_leads`, `total_revenue`, `stripe_user_id`, `stripe_access_token`, `stripe_integrated`, `type`, `company_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'training', 'Tutorials', 0, 1, 4, 6, 0, 0, NULL, NULL, 0, 0, 0, NULL, '0000-00-00 00:00:00', '2015-08-10 02:54:27');

INSERT INTO `sites_menu_items` (`id`, `site_id`, `url`, `label`, `icon`, `custom_icon`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'support', 'Support', '', '', NULL, '2015-08-07 11:00:20', '2015-08-07 11:00:32'),
(2, 1, 'support-ticket/create?type=refund', 'Support Ticket', '', '', NULL, '2015-08-07 11:00:34', '2015-08-07 11:00:57'),
(3, 1, 'refund-page', '', '', '', NULL, '2015-08-07 11:00:59', '2015-08-07 11:01:17');

INSERT INTO `site_meta_data` (`id`, `site_id`, `data_type`, `key`, `value`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'course_title', 'Test syllabus', NULL, '2015-08-07 10:48:16', '2015-08-07 10:48:16'),
(2, 1, 0, 'show_copyright', '1', NULL, '2015-08-07 11:01:21', '2015-08-07 11:01:21'),
(3, 1, 0, 'show_powered_by', '1', NULL, '2015-08-07 11:01:21', '2015-08-07 11:01:21'),
(4, 1, 0, 'sales_page_enabled', '0', NULL, '2015-08-07 11:09:45', '2015-08-08 07:31:15'),
(5, 1, 0, 'sales_page_embed', 'I am content', NULL, '2015-08-07 23:56:08', '2015-08-07 23:56:08'),
(6, 1, 0, 'sales_page_content', 'I am content changed', NULL, '2015-08-08 00:06:26', '2015-08-08 07:27:50'),
(7, 1, 0, 'sales_page_outro', 'I am outro changed again', NULL, '2015-08-08 07:07:11', '2015-08-08 07:27:49');

INSERT INTO `site_notices` (`id`, `site_id`, `title`, `content`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 0, 'New: Subscribe to Smart Member''s System Updates!', '<p>Now you can stay up-to-date with any incidences or outages via email &amp; text.</p><p><img src="https://imbmediab.s3.amazonaws.com/1/1bc40354d153339f02afae047628d359/x20jv248c71rjdkmjze65vxvnckjmf.png" style="max-width: 100%;"><br></p>', NULL, '2015-08-07 10:54:23', '2015-08-07 10:54:23'),
(2, 1, 'new', NULL, NULL, '2015-08-07 12:02:30', '2015-08-07 12:02:30'),
(3, 1, 'I am another notice', 'This is my content', NULL, '2015-08-07 12:03:54', '2015-08-07 12:03:54');

INSERT INTO `support_articles` (`id`, `site_id`, `author_id`, `category_id`, `title`, `content`, `embed_content`, `featured_image`, `permalink`, `sort_order`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'How can I add seed files', '<div style="text-align: center;"><img src="https://imbmediab.s3.amazonaws.com/sdw0b3j3y61vv30xy3y6py2jevdrgy.jpg" style="max-width: 100%;"></div><h3 style="text-align: center;"><span style="font-family: Impact; font-weight: bold;">We Show you flowers and it answers your question!</span></h3>', '<iframe width="1280" height="750" src="https://www.youtube.com/embed/D8zZZZzppIM" frameborder="0" allowfullscreen></iframe>', '', 'how-can-i-add-seed-files', 1, NULL, '2015-08-07 11:36:52', '2015-08-07 11:41:49'),
(2, 1, 1, 1, 'Trigger Point Therapy for Self Help', '<div><div><div>The pain you feel may not be coming from where you think. Often the source can be a trigger point, or "muscle knot" several inches away.</div><div><br></div><div>With a little knowledge and practice, you can learn to address your own trigger points, or aid your practitioner in finding them.?</div><div><br></div><div>This interview features two of the leading authorities on Trigger Point Therapy.</div><div><br></div><div>Amber Davis, LMT, is a contributing author of the second edition of <span style="font-style: italic;">The Trigger Point Therapy Workbook</span>, written by her father, the late Clair Davies. She has recently updated a new 3rd edition.</div><div><br></div><div>Sharon Sauer, CMTPT, LMT, is Co-director of Therapy at the MYO Pain Relief Center in Chicago. She was trained and mentored by the late Dr. Janet Travell, co-author of Myofascial Pain and Dysfunction: The Trigger Point Manual.</div><div><br></div><div>Sharon has co-authored <span style="font-style: italic;">Trigger Point Therapy for Low Back Pain</span> to spread the word that a low-risk alternative to more aggressive treatments — like drugs or surgery — exists for chronic low back pain.</div></div></div>', '<center><script type="text/javascript" src="http://thm.evsuite.com/player/QmFja0hvcGUtMDItRGF2aWVzX1NhdWVyX2ZpbmFsLm1wNA==/?container=evp-ARKAG14JQA"></script><div id="evp-ARKAG14JQA" data-role="evp-video" data-evp-id="QmFja0hvcGUtMDItRGF2aWVzX1NhdWVyX2ZpbmFsLm1wNA=="></div></center>', '', 'trigger-point-therapy-self-help', 2, NULL, '2015-08-07 11:39:41', '2015-08-07 11:41:49');

INSERT INTO `support_categories` (`id`, `site_id`, `title`, `sort_order`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'FAQ', 1, NULL, '2015-08-07 11:36:44', '2015-08-07 11:41:49');

INSERT INTO `support_tickets` (`id`, `site_id`, `user_id`, `customer_id`, `subject`, `message`, `type`, `category`, `priority`, `status`, `read`, `parent_id`, `attachment`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 'Help me', 'I need urgent help', 'normal', '', '', 'new', 0, 0, '', NULL, '2015-08-07 11:47:42', '2015-08-07 11:47:42'),
(2, 1, 1, 0, 'I can''t find a use for this product.', 'Your product is really useless', 'refund', '', '', 'new', 0, 0, 'http://api.smartmember.dev/uploads/84e083ccd91195b4352cff3914fb0077.jpg', NULL, '2015-08-07 11:48:10', '2015-08-07 11:48:10');

INSERT INTO `tax_type` (`id`, `type_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Tags', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Categories', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `verified`, `facebook_user_id`, `access_token`, `access_token_expired`, `remember_token`, `reset_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Smith', '', 'admin@smartmember.com', '$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6', 1, NULL, '9f92fb02ecc361000c2ae3ce0982424f', '2025-08-07 20:37:10', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Bronze', 'User', '', 'bronze@smartmember.com', '$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6', 1, NULL, 'a9f92fb02ecc361000c2ae3ce0982424b', '2025-08-07 20:37:10', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Silver', 'User', '', 'silver@smartmember.com', '$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6', 1, NULL, 'b9f92fb02ecc361000c2ae3ce0982424s', '2025-08-07 20:37:10', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Gold', 'User', '', 'gold@smartmember.com', '$2y$10$RH8gyS5aqI3CfDQ.3PWLRe9lUmtHtdT/QJy8l7FE0Bpk/RIeEMdb6', 1, NULL, 'c9f92fb02ecc361000c2ae3ce0982424g', '2025-08-07 20:37:10', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
