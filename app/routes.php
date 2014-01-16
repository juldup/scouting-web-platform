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

// Cron tasks
Route::get('/envoi-automatique-emails', array("as" => "send_emails_automatically", "uses" => function() {
  ScoutMailer::sendPendingEmails();
}));

// Users
Route::get('login/{section_slug?}', array("as" => "login", "uses" => "UserController@login"));
Route::post('login/{section_slug?}', array("as" => "login_submit", "uses" => "UserController@submitLogin"));
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
Route::get('section/{section_slug?}', array("as" => "section", "uses" => "SectionPageController@showPage"));
Route::get('gestion/section/{section_slug}', array("as" => "edit_section_page", "uses" => "SectionPageController@showEdit"));
Route::post('gestion/section/{section_slug}', array("as" => "edit_section_page_submit", "uses" => "SectionPageController@savePage"));

// Addresses
Route::get('adresses/{section_slug?}', array("as" => "addresses", "uses" => "AddressPageController@showPage"));
Route::get('gestion/adresses/{section_slug?}', array("as" => "edit_address_page", "uses" => "AddressPageController@showEdit"));
Route::post('gestion/adresses/{section_slug?}', array("as" => "edit_address_page_submit", "uses" => "AddressPageController@savePage"));

// Contacts
Route::get('contacts/{section_slug?}', array("as" => "contacts", "uses" => "ContactController@showPage"));

// Annual feast
Route::get('fete-unite/{section_slug?}', array("as" => "annual_feast", "uses" => "AnnualFeastController@showPage"));
Route::get('gestion/fete-unite/{section_slug?}', array("as" => "edit_annual_feast_page", "uses" => "AnnualFeastController@showEdit"));
Route::post('gestion/fete-unite/{section_slug?}', array("as" => "edit_annual_feast_page_submit", "uses" => "AnnualFeastController@savePage"));

// Registration
Route::get('inscription/formulaire/{section_slug?}', array("as" => "registration_form", "uses" => "RegistrationController@showForm"));
Route::post('inscription/formulaire/submit/{section_slug?}', array("as" => "registration_form_submit", "uses" => "RegistrationController@submit"));
Route::get('inscription/{section_slug?}', array("as" => "registration", "uses" => "RegistrationController@showMain"));
Route::get('gestion/inscription/page-principale/{section_slug?}', array("as" => "edit_registration_page", "uses" => "RegistrationController@showEdit"));
Route::post('gestion/inscription/page-principale/{section_slug?}', array("as" => "edit_registration_page_submit", "uses" => "RegistrationController@savePage"));
Route::get('gestion/inscription/valider-supprimer/{section_slug?}', array("as" => "manage_registration", "uses" => "RegistrationController@manage"));
Route::post('gestion/inscription/valider-supprimer/submit/{section_slug?}', array("as" => "manage_registration_submit", "uses" => "RegistrationController@manageSubmit"));

// Health card
Route::get('fiche-sante/completer/{member_id}/{section_slug?}', array("as" => "health_card_edit", "uses" => "HealthCardController@showEdit"));
Route::post('fiche-sante/submit/{section_slug?}', array("as" => "health_card_submit", "uses" => "HealthCardController@submit"));
Route::get('fiche-sante/telecharger/{member_id}/{section_slug?}', array("as" => "health_card_download", "uses" => "HealthCardController@download"));
Route::get('fiche-sante/telecharger-tout/{section_slug?}', array("as" => "health_card_download_all", "uses" => "HealthCardController@downloadAll"));
Route::get('fiche-sante/{section_slug?}', array("as" => "health_card", "uses" => "HealthCardController@showPage"));
Route::get('gestion/fiche-sante/{section_slug?}', array("as" => "manage_health_cards", "uses" => "HealthCardController@showManage"));
Route::get('gestion/fiche-sante/telecharger-tout/{section_slug}', array("as" => "manage_health_cards_download_all", "uses" => "HealthCardController@downloadSectionCards"));
Route::get('gestion/fiche-sante/telecharger-resume/{section_slug}', array("as" => "manage_health_cards_download_summary", "uses" => "HealthCardController@downloadSectionSummary"));

// Unit policy
Route::get('charte/{section_slug?}', array("as" => "unit_policy", "uses" => "UnitPolicyPageController@showPage"));
Route::get('gestion/charte/{section_slug?}', array("as" => "edit_unit_policy_page", "uses" => "UnitPolicyPageController@showEdit"));
Route::post('gestion/charte/{section_slug?}', array("as" => "edit_unit_policy_page_submit", "uses" => "UnitPolicyPageController@savePage"));

// Uniforms
Route::get('uniforme/{section_slug?}', array("as" => "uniform", "uses" => "UniformPageController@showPage"));
Route::get('gestion/uniforme/{section_slug}', array("as" => "edit_uniform_page", "uses" => "UniformPageController@showEdit"));
Route::post('gestion/uniforme/{section_slug}', array("as" => "edit_uniform_page_submit", "uses" => "UniformPageController@savePage"));

// Links
Route::get('liens/{section_slug?}', array("as" => "links", "uses" => "LinkController@showPage"));
Route::get('gestion/liens/{section_slug?}', array("as" => "edit_links", "uses" => "LinkController@showEdit"));
Route::post('gestion/liens/{section_slug?}', array("as" => "edit_links_submit", "uses" => "LinkController@submitLink"));
Route::get('gestion/liens/delete/{link_id}/{section_slug?}', array("as" => "edit_links_delete", "uses" => "LinkController@deleteLink"));

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
Route::get('gestion/e-mails/{section_slug?}', array("as" => "manage_emails", "uses" => "EmailController@showManage"));
Route::get('gestion/envoi-e-mail/{section_slug?}', array("as" => "send_section_email", "uses" => "EmailController@sendSectionEmail"));
Route::post('gestion/envoi-e-mail/submit/{section_slug}', array("as" => "send_section_email_submit", "uses" => "EmailController@submitSectionEmail"));
Route::get('gestion/e-mails/supprimer/{email_id}', array("as" => "manage_emails_delete", "uses" => "EmailController@deleteEmail"));

// Photos
Route::get('photos/{section_slug?}', array("as" => "photos", "uses" => "PhotoController@showPage"));
Route::get('photos-{album_id}/{section_slug?}', array("as" => "photo_album", "uses" => "PhotoController@showPage"));
Route::get('photo/{format}/{photo_id}/{filename?}', array("as" => "get_photo", "uses" => "PhotoController@getPhoto"));
Route::get('photos/telecharger-album/{album_id}', array("as" => "download_photo_album", "uses" => "PhotoController@downloadAlbum"));
Route::get('gestion/photos/{section_slug?}', array("as" => "edit_photos", "uses" => "PhotoController@showEdit"));
Route::get('gestion/photos/{section_slug?}', array("as" => "edit_photos", "uses" => "PhotoController@showEdit"));
Route::get('gestion/photos/supprimer-album/{album_id}/{section_slug?}', array("as" => "delete_photo_album", "uses" => "PhotoController@deletePhotoAlbum"));
Route::get('gestion/photos/album/{album_id}/{section_slug?}', array("as" => "edit_photo_album", "uses" => "PhotoController@showEditAlbum"));
Route::post('ajax/gestion/photos/changer-ordre-albums', array("as" => "ajax_change_album_order", "uses" => "PhotoController@changeAlbumOrder"));
Route::post('ajax/gestion/photos/changer-ordre-photos', array("as" => "ajax_change_photo_order", "uses" => "PhotoController@changePhotoOrder"));
Route::get('gestion/photos/nouvel-album/{section_slug}', array("as" => "create_photo_album", "uses" => "PhotoController@createPhotoAlbum"));
Route::get('ajax/gestion/photos/supprimer-photo', array("as" => "ajax_delete_photo", "uses" => "PhotoController@deletePhoto"));
Route::post('ajax/gestion/photos/ajouter-photo', array("as" => "ajax_add_photo", "uses" => "PhotoController@addPhoto"));
Route::post('ajax/gestion/photos/changer-nom-album', array("as" => "ajax_change_album_name", "uses" => "PhotoController@changeAlbumName"));
Route::post('ajax/gestion/photos/changer-description-photo', array("as" => "ajax_change_photo_caption", "uses" => "PhotoController@changePhotoCaption"));

// Leaders
Route::get('animateurs/{section_slug?}', array("as" => "leaders", "uses" => "LeaderController@showPage"));
Route::get('animateur/photo/{leader_id}', array("as" => "get_leader_picture", "uses" => "LeaderController@getLeaderPicture"));
Route::get('gestion/animateurs/{section_slug?}', array("as" => "edit_leaders", "uses" => "LeaderController@showEdit"));
Route::get('gestion/animateurs/scout-en-animateur/{member_id}/{section_slug}', array("as" => "edit_leaders_member_to_leader", "uses" => "LeaderController@showMemberToLeader"));
Route::post('gestion/animateurs/scout-en-animateur/{section_slug}', array("as" => "edit_leaders_member_to_leader_post", "uses" => "LeaderController@postMemberToLeader"));
Route::post('gestion/animateurs/submit/{section_slug?}', array("as" => "edit_leaders_submit", "uses" => "LeaderController@submitLeader"));
Route::get('gestion/privileges/{section_slug?}', array("as" => "edit_privileges", "uses" => "PrivilegeController@showEdit"));

// Listing
Route::get('listing/{section_slug?}', array("as" => "listing", "uses" => "ListingController@showPage"));
Route::get('gestion/listing/{section_slug?}', array("as" => "manage_listing", "uses" => "ListingController@showEdit"));
Route::post('gestion/listing/submit/{section_slug?}', array("as" => "manage_listing_submit", "uses" => "ListingController@manageSubmit"));
Route::post('listing/submit/{section_slug?}', array("as" => "listing_submit", "uses" => "ListingController@submit"));
Route::get('gestion/listing/delete/{member_id}/{section_slug?}', array("as" => "manage_listing_delete", "uses" => "ListingController@deleteMember"));

// Suggestions
Route::get('suggestions/{section_slug?}', array("as" => "suggestions", "uses" => "SuggestionController@showPage"));

// Guest book
Route::get('livre-or/{section_slug?}', array("as" => "guest_book", "uses" => "GuestBookController@showPage"));

// Help
Route::get('aide/{section_slug?}', array("as" => "help", "uses" => "HelpController@showPage"));

// Home
Route::get('/{section_slug?}', array("as" => "home", "uses" => "HomePageController@showPage"));
Route::get('gestion/accueil/{section_slug?}', array("as" => "edit_home_page", "uses" => "HomePageController@showEdit"));
Route::post('gestion/accueil/{section_slug?}', array("as" => "edit_home_page_submit", "uses" => "HomePageController@savePage"));

