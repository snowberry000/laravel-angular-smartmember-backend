<?php

/*
    A list of resources
*/
$resources = array(
    'site',
    'user',
    'affiliate',
    'affiliateTeam',
    'affiliateJVPage',
    'bridgePage',
    'transaction',
    'transactionType',
    'elementType',
    'template',
    'templateAttribute',
    'permalinkType',
    'siteMetaData',
    'siteTemplateData',
    'email',
    'emailList',
    'emailSubscriber',
    'emailQueue',
    'emailAutoResponder',
    'emailJob',
    'importQueue',
    'importJob',
    'accessLevelShareKey',
    'appConfiguration',
    'lesson',
    'module',
    'siteMenuItem',
    'siteFooterMenuItem',
    'customPage',
    'accessLevel',
    'download',
    'role',
    'siteNotice',
    'siteNoticeSeen',
    'post',
    'specialPage',
    'userNote',
    'comment',
    'supportArticle',
    'supportTicket',
    'supportCategory',
    'cannedResponse',
    'tag',
    'category',
    'siteAds',
    'livecast',
    'affiliateContest',
    'userRole',
    'userOptions',
    'companyOption',
    'adminNote',
    'draft',
    'company',
    'teamRole',
    'directory',
	'connectedAccount',
    'linkedAccount',
    'wizard',
	'widget',
	'smartLink',
    'media',
	'event',
	'eventMetaData',
	'customAttribute',
	'memberMeta'
);

//TODO: Don't use - in the URL. Use Camel cased syntax e.g. facebookLogin

Route::get("/auth/verify/{access_token}", "Auth\\AuthController@getVerify");
Route::post("/auth/facebook-login", "Auth\\AuthController@postFacebookLogin");

Route::controller('auth',"Auth\\AuthController");
Route::get('/directory/siteListing',"Api\\DirectoryController@siteListing");
Route::get('/directory/approve/{siteId}',"Api\\DirectoryController@approve");
Route::get('/directoryByPermalink/{permalink}',"Api\\DirectoryController@byPermalink");
Route::get('/directory/category',"Api\\DirectoryController@categories");


Route::get('ticketCount', "Api\\SiteController@getTicketcount");
Route::get('latestOfEverything', "Api\\SiteController@getLatestOfAllContent");
Route::post('updateSiteHash', "Api\\SiteController@updateSiteHash");

Route::get('/site/details',"Api\\SiteController@details");
Route::get('/checkHomepageBP/{domain}',"Api\\BridgePageController@checkHomepageBridgePage");
Route::get('/initialLoadingData',"Api\\BridgePageController@initialLoadingData");
Route::post('/site/addMember',"Api\\SiteController@addMember");
Route::get('/site/members',"Api\\SiteController@members");
Route::get('/module/home',"Api\\ModuleController@home");
Route::get('/module/syllabus',"Api\\ModuleController@syllabus");
Route::post('/module/syllabusSave',"Api\\ModuleController@syllabusSave");
Route::get('/site/summary', "Api\\SiteController@getSummary");

//APPEARANCE
Route::post('/siteMetaData/save',"Api\\SiteMetaDataController@save");
Route::post('/siteMetaData/wizard',"Api\\SiteMetaDataController@wizard");
Route::get('/siteMetaData/getOptions',"Api\\SiteMetaDataController@getOptions");
Route::get('/siteMetaData/getTrackingCode',"Api\\SiteMetaDataController@getTrackingCode");
Route::post('/siteMetaData/saveSingleOption',"Api\\SiteMetaDataController@saveSingleOption");
Route::post('/siteFooterMenuItem/save',"Api\\SiteFooterMenuItemController@save");

//Custom page
Route::get('/customPage/getlist',"Api\\CustomPageController@getlist");
Route::get('/customPage/single/{id}',"Api\\CustomPageController@single");

//BridgePage
Route::get('/bridgePage/getlist',"Api\\BridgePageController@getlist");
Route::get('/bridgePage/single/{id}',"Api\\BridgePageController@single");
Route::get('/bridgeTemplate/getlist', "Api\\BridgeTemplateController@getList");


Route::get('/lesson/getDraftedLesson',"Api\\LessonController@getDraftedLesson");
Route::get('/lesson/single/{id}',"Api\\LessonController@single");
Route::post('/lesson/addAll',"Api\\LessonController@addAllVideos");
Route::put('/lesson/bulkUpdate',"Api\\LessonController@bulkUpdate");
Route::post('/lesson/bulkUpdateAccess',"Api\\LessonController@bulkUpdateAccess");
Route::post('/lesson/bulkDelete',"Api\\LessonController@bulkDelete");

Route::get('/appConfiguration/single/{id}',"Api\\AppConfigurationController@single");
Route::post('/appConfiguration/addAll',"Api\\AppConfigurationController@addAllVideos");

Route::post('/appConfiguration/uninstall',"Api\\AppConfigurationController@uninstallApp");

Route::get('/appConfiguration/getSendgridIntegrations', "Api\\AppConfigurationController@getSendgridIntegrations");
Route::get('/appConfiguration/getPaymentIntegrations', "Api\\AppConfigurationController@getPaymentIntegrations");
Route::get('/appConfiguration/getIntegratedEmailList', "Api\\AppConfigurationController@getEmailListFromMailIntegration");
Route::get('/appConfiguration/wordpress', "AppConfiguration\WordpressController@show");
Route::get('/appConfiguration/wordpress/{id}/postTypes/{post_type}', "AppConfiguration\WordpressController@listPosts");
Route::get('/appConfiguration/wordpress/{id}/postTypes', "AppConfiguration\WordpressController@listPostTypes");
Route::post('/appConfiguration/wordpress', "AppConfiguration\WordpressController@store");


Route::get('/download/getlist',"Api\\DownloadController@getlist");
Route::post('/download/putDownloads',"Api\\DownloadController@putDownloads");
Route::get('/download/single/{id}',"Api\\DownloadController@single");

//Roles
Route::get('/role/getlist',"Api\\RoleController@getlist");
// Route::get('/user/getCSV',"Api\\UserController@getCSV");
Route::get('/siteRole/getCSV',"Api\\Site\\RoleController@getCSV");
Route::get('/role/agents',"Api\\RoleController@getAgents");

// The roles custom routes can be merged with Role::controller
Route::post('/siteRole/import',"Api\\Site\\RoleController@postImport");

Route::post('/siteRole/removeUserFromSite',"Api\\Site\\RoleController@removeUserFromSite");
Route::post('/siteRole/removeUserFromCurrentSite',"Api\\Site\\RoleController@removeUserFromCurrentSite");

Route::get('/emailSubscriber/getCSV',"Api\\EmailSubscriberController@getCSV");
Route::get('/emailSubscriber/getUnsubscribeInfo',"Api\\EmailSubscriberController@getUnsubscribeInfo");
Route::post('/emailSubscriber/unsubscribeList', "Api\\EmailSubscriberController@unsubscribeList");
Route::post('/emailList/users',"Api\\EmailListController@users");
Route::get('/emailList/sendMailLists',"Api\\EmailListController@sendMailLists");
Route::get('/accessLevel/sendMailAccessLevels',"Api\\AccessLevelController@sendMailAccessLevels");
Route::get('/user/isSuperAdmin',"Api\\UserController@isSuperAdmin");
Route::post('/user/saveFacebookGroupOption',"Api\\UserController@saveFacebookGroupOption");
Route::post('/emailSubscriber/unsubscribe',"Api\\EmailSubscriberController@unsubscribe");
Route::get('/emailSubscriber/clearWhiteSpaceFromEmails',"Api\\EmailSubscriberController@clearWhiteSpaceFromEmails");
Route::post('/email/sendTest',"Api\\EmailController@sendTestEmail");
Route::get('/listOfEmails',"Api\\EmailController@ListOfEmails");
Route::get('/email/getSegments',"Api\\EmailController@getSegments");
Route::post('/email/calculateSubscribers',"Api\\EmailController@calculateSubscribers");
Route::post('/emailJob/sendNow',"Api\\EmailJobController@sendNow");
Route::post('/emailJob/deleteJob',"Api\\EmailJobController@deleteJob");
Route::post('/accessLevel/refreshHash',"Api\\AccessLevelController@refreshHash");
//Route::get('/cronprocessqueue', "Api\\ImportQueueController@cronProcessQueue");
Route::get('/siteNotice/getlist',"Api\\SiteNoticeController@getlist");
Route::get('/siteNotice/getnotifications',"Api\\SiteNoticeController@getnotifications");
Route::get('/siteNotice/getPrimaryAdminNotices',"Api\\SiteNoticeController@getPrimaryAdminNotices");

Route::get('/post/single/{id}',"Api\\PostController@single");

Route::get('/userNote/single/{id}',"Api\\UserNoteController@single");

Route::model('pass' , "App\\Models\\AccessLevel\\Pass");
Route::resource('pass' , "Api\\AccessLedgerController");

Route::post('/supportCategory/creator',"Api\\SupportCategoryController@creator");

Route::get('/post/getMostUsed/{site_id}',"Api\\PostController@getMostUsed");
Route::post('/jvzoo/{hash}',"Api\\AffiliateController@processJVZooData");

Route::get('/permalink/{permalink}',"Api\\PermalinkController@getByPermalink");

Route::get('/get/download/{id}',"Api\\DownloadController@getDownload");
Route::get('/lessonByPermalink/{id}',"Api\\LessonController@getByPermalink");
Route::get('/lessonByTitle/{id}',"Api\\LessonController@getLessonByName");
Route::get('/affiliateContestByPermalink/{id}',"Api\\AffiliateContestController@getByPermalink");
// Route::get('/affiliateLeaderboardByContest/{id}',"Api\\AffiliateLeaderboardController@getByContest");
Route::get('/sm-url/{domain}',"Api\\SiteController@SMUrl");

Route::get('/getBlogPosts',"Api\\PostController@getBlogPosts");
Route::get('/pageByPermalink/{id}',"Api\\CustomPageController@getByPermalink");
Route::get('/bridgePageByPermalink/{id}',"Api\\BridgePageController@getByPermalink");
Route::get('/postByPermalink/{id}',"Api\\PostController@getByPermalink");
Route::get('/blogPostByPermalink/{id}','Api\\PostController@getByPermalinkForBlog');
Route::get('/downloadByPermalink/{id}',"Api\\DownloadController@getByPermalink");
Route::get('/livecastByPermalink/{id}',"Api\\LivecastController@getByPermalink");
Route::get('/articleByPermalink/{id}',"Api\\SupportArticleController@getByPermalink");
Route::get('/migrateToArticles',"Api\\SupportCategoryController@migrateToArticles");
Route::get('/importJob/active', "Api\\ImportJobController@countActiveJob");
//Route::get('/regenerateToken',"Auth\\AuthController@regenerateAccessToken");
Route::get('/sharedKey/associatedKey', "Api\\AccessLevelShareKeyController@getAssociatedKey");
Route::get('/generateShareKey', "Api\\AccessLevelShareKeyController@generateShareKey");
Route::get('/accessLevel/getGrantedShareAccessLevel', "Api\\AccessLevelShareKeyController@getGrantedShareAccessLevels");
Route::post('/user/changePassword',"Api\\UserController@changePassword");
Route::post('/user/setCompany',"Api\\UserController@setCompany");
Route::post('/user/resendVerification',"Api\\UserController@resendVerificationCode");
Route::get('/user/transactionAccount/{id}',"Api\\UserController@transactionAccount");
Route::get('/user/transactionAccess/{id}',"Api\\UserController@transactionAccess");
Route::post('/user/saveTransactionAccount',"Api\\UserController@saveTransactionAccount");
Route::post('/user/associateHash',"Api\\UserController@associateHash");
Route::post('/user/associateTransactionAccount',"Api\\UserController@associateTransactionAccount");
Route::post('/user/registerTransactionAccount',"Api\\UserController@registerTransactionAccount");
Route::post('/user/sendVerificationCode',"Api\\UserController@sendVerificationCode");
Route::get('/user/sites',"Api\\UserController@getSites");
Route::get('/user/members',"Api\\UserController@getMembers");
Route::get('/affiliateLeaderboard/{id}', "Api\\AffiliateLeaderboardController@show");



Route::get('/supportTicket/userTickets', "Api\\SupportTicketController@userTickets");
Route::get('/supportTicket/filterSearch', "Api\\SupportTicketController@filterSearch");
Route::put('/supportTicket/bulk', "Api\\SupportTicketController@bulkUpdate");
Route::get('/supportTicket/sites', "Api\\SupportTicketController@sites");


Route::get('/supportTicket/rate', "Api\\SupportTicketController@rate"); 

Route::get('/company/getUsersCompanies', "Api\\CompanyController@getUsersCompanies");
Route::get('/company/getUsersSitesAndTeams', "Api\\CompanyController@getUsersSitesAndTeams");
Route::get('/company/getUserCompaniesAndSites', "Api\\CompanyController@getUserCompaniesAndSites");
Route::get('/company/getCurrentCompany', "Api\\CompanyController@getCurrentCompany");
Route::get('/company/getCurrentCompanyHash', "Api\\CompanyController@getCurrentCompanyHash");
Route::get('/companyByPermalink/{permalink}', "Api\\CompanyController@byPermalink");

Route::controller('emailSetting', "AppConfiguration\\SendGridController");

Route::post('/emailSubscriber/subscribe', "Api\\EmailSubscriberController@postSubscribe");
//Route::get('/emailSubscriber/getCSV', 'Api\EmailSubscriberController@getCSV');


Route::match(['get','post'],'/optin', "Api\\EmailSubscriberController@formSubscribe");
Route::match(['get','post'],'/optintomember', "Api\\EmailSubscriberController@turnOptInToMember");
Route::post('/saveAlFb/{id}', "AppConfiguration\\FacebookController@saveGroupAccessLevels");
Route::get('/integration/getByGroupId/{group_id}', "AppConfiguration\\FacebookController@getByGroupId");
Route::get('/fixUrlForSite/{id}', "Api\\LessonController@fixUrlForSite");
Route::post('/trackClicks/{id}', "Api\\SiteAdsController@postTrackClicks");
Route::post('/trackViews/{id}', "Api\\SiteAdsController@postTrackViews");
Route::post('/trackViews', "Api\\SiteAdsController@postTrackViews");
Route::post('putAdvertisementsOrder', "Api\\SiteAdsController@putAds");
Route::get('/trackClick', "Api\\ClickController@trackClick");
Route::get('/trackOpen', "Api\\OpenController@trackOpen");
Route::post('/processEmailQueue', "Api\\EmailQueueController@processEmailQueue");
Route::post('/processEmailRecipientsQueue', "Api\\EmailQueueController@processEmailRecipientsQueue");

// Data Dashboard
Route::get('/transaction/summary',"Api\\TransactionController@summary");
Route::get('/role/summary',"Api\\RoleController@summary");
Route::get('/affiliate/summary',"Api\\AffiliateController@summary");
Route::post('/teamRole/import',"Api\\TeamRoleController@postImport");

// Team Roles
Route::post('/teamRole/updateRole',"Api\\TeamRoleController@updateRole");
Route::post('/teamRole/addToTeam',"Api\\TeamRoleController@addToTeam");
Route::get('/teamRole/migrateTeamRole',"Api\\TeamRoleController@migrateTeamRole");
Route::post('/teamRole/verifyPassword',"Api\\TeamRoleController@verifyPassword");

Route::post('/linkedAccount/togglePrimary',"Api\\LinkedAccountController@togglePrimary");
Route::post('/linkedAccount/link',"Api\\LinkedAccountController@link");
Route::post('/linkedAccount/claim',"Api\\LinkedAccountController@claim");
Route::post('/linkedAccount/merge',"Api\\LinkedAccountController@merge");

Route::post('/accessLevel/lock',"Api\\AccessLevelController@lock");

Route::post('/widget/updateOrder', "Api\\WidgetController@updateOrder");
Route::get('/widget/locationOptions', "Api\\WidgetController@locationOptions");

Route::get('/supportAgents', "Api\\Site\RoleController@getSupportAgent");
Route::get('/getSMMembers', "Api\\Site\RoleController@getSMMembers");

foreach ($resources as $res){
    $resource = ucwords(str_replace("-", " ", $res));
    $resource = implode('',explode(" ", $res));  
    $resource = ucfirst($resource);
    Route::model($res,"App\\Models\\" . $resource);
    Route::resource($res,"Api\\" . $resource . "Controller");
}

Route::get('forumCategory/permalink','Api\Forum\CategoryController@getByPermalink');
Route::model('forumCategory','App\Models\Forum\Category');
Route::resource('forumCategory','Api\Forum\CategoryController');

Route::get('forumTopic/permalink','Api\Forum\TopicController@getByPermalink');
Route::model('forumTopic','App\Models\Forum\Topic');
Route::resource('forumTopic','Api\Forum\TopicController');

Route::model('forumReply','App\Models\Forum\Reply');
Route::resource('forumReply','Api\Forum\ReplyController');
Route::get('siteRole/passes','Api\Site\RoleController@passes');

Route::model('siteRole','App\Models\Site\Role');
Route::resource('siteRole','Api\Site\RoleController');

Route::model('siteCustomRole','App\Models\Site\CustomRole');
Route::resource('siteCustomRole','Api\Site\CustomRoleController');

Route::get('/sendPurchaseEmail', "AppConfiguration\SendGridController@sendPurchaseEmail");
Route::post('memberMeta/save', "Api\\MemberMetaController@save");
Route::post('customAttribute/set', "Api\\CustomAttributeController@set");
