<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// VIEWS

View::composer('menu.menu', "MenuComposer");
View::composer('menu.user_box', "UserBoxComposer");
View::composer('menu.tabs', "TabsComposer");



// ROUTES

// Users
Route::get('login/{section_slug?}', array("as" => "login", "uses" => "UserController@login"));
Route::post('login/{section_slug?}', array("as" => "login", "uses" => "UserController@submitLogin"));
Route::get('logout/{section_slug?}', array("as" => "logout", "uses" => "UserController@logout"));
Route::any('modifier-utilisateur/email/{section_slug?}', array("as" => "edit_user_email", "uses" => "UserController@editEmail"));
Route::any('modifier-utilisateur/mot-de-passe/{section_slug?}', array("as" => "edit_user_password", "uses" => "UserController@editPassword"));
Route::any('modifier-utilisateur/section/{section_slug?}', array("as" => "edit_user_section", "uses" => "UserController@editSection"));
Route::get('modifier-utilisateur/{section_slug?}', array("as" => "edit_user", "uses" => "UserController@editUser"));
Route::any('recuperer-mot-de-passe/{section_slug?}', array("as" => "retrieve_password", "uses" => "UserController@retrievePassword"));
Route::any('changer-mot-de-passe/{code}/{section_slug?}', array("as" => "change_password", "uses" => "UserController@changePassword"));
Route::post('nouvel-utilisateur/{section_slug?}', array("as" => "create_user", "uses" => "UserController@create"));
Route::get('verifier-utilisateur/{code}', array("as" => "verify_user", "uses" => "UserController@verify"));
Route::get('annuler-utilisateur/{code}', array("as" => "cancel_user", "uses" => "UserController@cancelVerification"));
Route::get('renvoyer-lien-validation', array("as" => "user_resend_validation_link", "uses" => "UserController@resendValidationLink"));

// Images
Route::get('images/{image_id}', array("as" => "get_page_image", "uses" => "PageImageController@getImage"));
Route::post('images/upload/{page_id}', array("as" => "ajax_upload_image", "uses" => "PageImageController@uploadImage"));
Route::get('images/remove/{image_id}', array("as" => "ajax_remove_image", "uses" => "PageImageController@removeImage"));

// Sections
Route::get('section/{section_slug?}', array("as" => "section", "uses" => "SectionController@showPage"));
Route::get('gestion/section/{section_slug}', array("as" => "manage_section", "uses" => "SectionController@showEdit"));
Route::post('gestion/section/{section_slug}', array("as" => "manage_section", "uses" => "SectionController@savePage"));

// Addresses
Route::get('adresses/{section_slug?}', array("as" => "addresses", "uses" => "AddressesController@showPage"));
Route::get('gestion/adresses/{section_slug?}', array("as" => "manage_addresses", "uses" => "AddressesController@showEdit"));
Route::post('gestion/adresses/{section_slug?}', array("as" => "manage_addresses", "uses" => "AddressesController@savePage"));

// Contacts
Route::get('contacts/{section_slug?}', array("as" => "contacts", "uses" => "ContactController@showPage"));

// Annual feast
Route::get('fete-unite/{section_slug?}', array("as" => "annual_feast", "uses" => "AnnualFeastController@showPage"));

// Registration
Route::get('inscription/{section_slug?}', array("as" => "registration", "uses" => "RegistrationController@showPage"));

// Health card
Route::get('fiche-sante/{section_slug?}', array("as" => "health_card", "uses" => "HealthCardController@showPage"));

// Unit policy
Route::get('charte/{section_slug?}', array("as" => "unit_policy", "uses" => "UnitPolicyController@showPage"));
Route::get('gestion/charte/{section_slug?}', array("as" => "manage_unit_policy", "uses" => "UnitPolicyController@showEdit"));
Route::post('gestion/charte/{section_slug?}', array("as" => "manage_unit_policy", "uses" => "UnitPolicyController@savePage"));

// Uniforms
Route::get('uniforme/{section_slug?}', array("as" => "uniform", "uses" => "UniformController@showPage"));
Route::get('gestion/uniforme/{section_slug}', array("as" => "manage_uniform", "uses" => "UniformController@showEdit"));
Route::post('gestion/uniforme/{section_slug}', array("as" => "manage_uniform", "uses" => "UniformController@savePage"));

// Links
Route::get('liens/{section_slug?}', array("as" => "links", "uses" => "LinkController@showPage"));

// News
Route::get('nouvelles/{section_slug?}', array("as" => "news", "uses" => "NewsController@showPage"));
Route::get('gestion/nouvelles/{section_slug?}', array("as" => "manage_news", "uses" => "NewsController@showEdit"));
Route::post('gestion/nouvelles/submit/{section_slug}', array("as" => "manage_news_submit", "uses" => "NewsController@submitNews"));
Route::get('gestion/nouvelles/delete/{news_id}', array("as" => "manage_news_delete", "uses" => "NewsController@deleteNews"));

// Calendar
Route::get('calendrier/{year}/{month}/{section_slug?}', array("as" => "calendar_month", "uses" => "CalendarController@showPage"));
Route::get('calendrier/{section_slug?}', array("as" => "calendar", "uses" => "CalendarController@showPage"));
Route::get('gestion/calendrier/{year}/{month}/{section_slug?}', array("as" => "manage_calendar_month", "uses" => "CalendarController@showEdit"));
Route::get('gestion/calendrier/{section_slug?}', array("as" => "manage_calendar", "uses" => "CalendarController@showEdit"));
Route::post('gestion/calendrier/submit/{year}/{month}/{section_slug}', array("as" => "manage_calendar_submit", "uses" => "CalendarController@submitItem"));
Route::get('gestion/calendrier/delete/{year}/{month}/{section_slug}/{event_id}', array("as" => "manage_calendar_delete", "uses" => "CalendarController@deleteItem"));

// Documents
Route::get('telecharger/{section_slug?}', array("as" => "documents", "uses" => "DocumentController@showPage"));
Route::get('gestion/telecharger/{section_slug?}', array("as" => "manage_documents", "uses" => "DocumentController@showEdit"));
Route::post('gestion/telecharger/submit/{section_slug}', array("as" => "manage_documents_submit", "uses" => "DocumentController@submitDocument"));
Route::get('gestion/telecharger/delete/{document_id}', array("as" => "manage_documents_delete", "uses" => "DocumentController@deleteDocument"));
Route::get('telechager-document/{document_id}', array("as" => "download_document", "uses" => "DocumentController@downloadDocument"));
Route::post('telecharger/par-email', array("as" => "send_document_by_email", "uses" => "DocumentController@sendByEmail"));

// E-mails
Route::get('e-mails/{section_slug?}', array("as" => "emails", "uses" => "EmailController@showPage"));

// Photos
Route::get('photos/{section_slug?}', array("as" => "photos", "uses" => "PhotoController@showPage"));

// Leaders
Route::get('animateurs/{section_slug?}', array("as" => "leaders", "uses" => "LeaderController@showPage"));
Route::get('animateur/photo/{leader_id}', array("as" => "get_leader_picture", "uses" => "LeaderController@getLeaderPicture"));
Route::get('gestion/animateurs/{section_slug?}', array("as" => "edit_leaders", "uses" => "LeaderController@showEdit"));
Route::get('gestion/animateurs/scout-en-animateur/{member_id}/{section_slug}', array("as" => "edit_leaders_member_to_leader", "uses" => "LeaderController@showMemberToLeader"));
Route::post('gestion/animateurs/scout-en-animateur/{section_slug}', array("as" => "edit_leaders_member_to_leader_post", "uses" => "LeaderController@postMemberToLeader"));
Route::post('gestion/animateurs/submit/{section_slug?}', array("as" => "edit_leaders_submit", "uses" => "LeaderController@submitLeader"));
Route::get('gestion/privileges/{section_slug?}', array("as" => "edit_privileges", "uses" => "LeaderController@showEditPrivileges"));

// Listing
Route::get('listing/{section_slug?}', array("as" => "listing", "uses" => "ListingController@showPage"));

// Suggestions
Route::get('suggestions/{section_slug?}', array("as" => "suggestions", "uses" => "SuggestionController@showPage"));

// Guest book
Route::get('livre-or/{section_slug?}', array("as" => "guest_book", "uses" => "GuestBookController@showPage"));

// Help
Route::get('aide/{section_slug?}', array("as" => "help", "uses" => "HelpController@showPage"));

// Home
Route::get('/{section_slug?}', array("as" => "home", "uses" => "HomeController@showPage"));
Route::get('gestion/accueil/{section_slug?}', array("as" => "manage_home", "uses" => "HomeController@showEdit"));
Route::post('gestion/accueil/{section_slug?}', array("as" => "manage_home", "uses" => "HomeController@savePage"));
