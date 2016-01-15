<?php

return [
	'capabilities' => [
		'view_restricted_content',
		'manage_members',
		'manage_transactions',
		'manage_support_tickets',
		'view_support_stats',
		'view_private_content',
		'manage_content',
		'view_content_stats',
		'manage_access_levels',
		'manage_apps',
		'view_financial_stats',
		'manage_affiliates',
		'view_affiliate_stats',
		'edit_theme_options',
		'manage_email',
		'edit_site_options',
		'manage_roles',
		'delete_site',
		'clone_site'
	],
	'roles' => [
		'member' 	 	=> ['view_restricted_content'],
		'support' 		=> ['view_restricted_content','manage_members','manage_transactions','manage_support_tickets','view_support_stats'],
		'editor'		=> ['view_restricted_content','view_private_content','manage_content','view_content_stats','manage_access_levels','edit_theme_options'],
		'admin' 		=> ['view_restricted_content','manage_members','manage_transactions','manage_support_tickets','view_support_stats','view_private_content','manage_content','view_content_stats','manage_access_levels','manage_apps','view_financial_stats','manage_affiliates','view_affiliate_stats','edit_theme_options','manage_email','edit_site_options','clone_site'],
		'owner' 		=> ['view_restricted_content','manage_members','manage_transactions','manage_support_tickets','view_support_stats','view_private_content','manage_content','view_content_stats','manage_access_levels','manage_apps','view_financial_stats','manage_affiliates','view_affiliate_stats','edit_theme_options','manage_email','edit_site_options','manage_roles','delete_site','clone_site']
	]	
];