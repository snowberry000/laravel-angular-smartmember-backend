<?php

return [
    'default_page_size' => 25,
    'email_from' => 'noreply@smember.site',
    'email_lock' => env('EMAIL_LOCK', false),
    'member_access_level' => env("MEMBER_ACCESS_LEVEL_ID"),
    'admin_notices_site_id' => env("ADMIN_NOTICES_SITE_ID"),
	'default_site_to_clone' => env("DEFAULT_SITE_TO_CLONE")
];