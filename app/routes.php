<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

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
Route::get('cron/envoi-automatique-emails', array("as" => "cron_send_emails_automatically", "uses" => function() {
  LogEntry::$isCronJobUser = true;
  ScoutMailer::sendPendingEmails();
  // Update cron job status
  Parameter::set(Parameter::$CRON_EMAIL_LAST_EXECUTION, time());
}));
Route::get('cron/suppression-auto-fiches-sante', array("as" => "cron_auto_delete_health_cards", "uses" => function() {
  LogEntry::$isCronJobUser = true;
  HealthCard::autoReminderAndDelete();
  // Update cron job last execution time
  Parameter::set(Parameter::$CRON_HEALTH_CARDS_LAST_EXECUTION, time());
}));
Route::get('cron/augmenter-annee-auto', array("as" => "cron_auto_increment_year_in_section", "uses" => function() {
  LogEntry::$isCronJobUser = true;
  Member::updateYearInSectionAuto();
  // Update cron job last execution time
  Parameter::set(Parameter::$CRON_INCREMENT_YEAR_IN_SECTION_LAST_EXECUTION, time());
}));
Route::get('cron/suppression-auto-comptes-non-verifies', array("as" => "cron_auto_clean_up_unverified_accounts", "uses" => function() {
  LogEntry::$isCronJobUser = true;
  User::cleanUpUnverifiedAccounts();
  // Update cron job last execution time
  Parameter::set(Parameter::$CRON_CLEAN_UP_UNUSED_ACCOUNTS, time());
}));
Route::get('cron/mise-a-jour-elasticsearch', array("as" => "cron_update_elasticsearch", "uses" => function() {
  LogEntry::$isCronJobUser = true;
  ElasticsearchHelper::fillElasticsearchDatabase();
  // Update cron job last execution time
  Parameter::set(Parameter::$CRON_UPDATE_ELASTICSEARCH, time());
}));

// General
Route::get('logo-image', array("as" => "website_logo", "uses" => "HomePageController@websiteLogo"));
Route::get('icon-image', array("as" => "website_icon", "uses" => "HomePageController@websiteIcon"));
Route::get('session/keepalive', array("as" => "session_keepalive", "uses" => function() {}));
Route::get('css-unite.css', array("as" => "additional_css", "uses" => function() {
  if (Session::get('testing-css')) return Response::make(Parameter::get(Parameter::$ADDITIONAL_CSS_BUFFER))->header('Content-Type', 'text/css');
  return Response::make(Parameter::get(Parameter::$ADDITIONAL_CSS))->header('Content-Type', 'text/css');
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
Route::get('nouvel-utilisation/confirmation/{section_slug?}', array("as" => "user_created", "uses" => "UserController@userCreated"));
Route::get('verifier-utilisateur/{code}', array("as" => "verify_user", "uses" => "UserController@verify"));
Route::get('annuler-utilisateur/{code}', array("as" => "cancel_user", "uses" => "UserController@cancelVerification"));
Route::get('renvoyer-lien-validation', array("as" => "user_resend_validation_link", "uses" => "UserController@resendValidationLink"));

// Ban e-mail address
Route::get('desinscrire-addresse-email/{ban_code}', array("as" => "ban_email", "uses" => "BanEmailAddressController@banEmailAddress"));
Route::get('desinscrire-addresse-email/confirmer/{ban_code}', array("as" => "confirm_ban_email", "uses" => "BanEmailAddressController@confirmBanEmailAddress"));
Route::get('desinscrire-addresse-email/annuler/{ban_code}', array("as" => "confirm_unban_email", "uses" => "BanEmailAddressController@cancelBanEmailAddress"));

// Custom pages
Route::get('gestion/pages/{section_slug?}', array("as" => "edit_pages", "uses" => "CustomPageController@showPageList"));
Route::get('gestion/page/{page_slug}/{section_slug?}', array("as" => "edit_custom_page", "uses" => "CustomPageController@showEdit"));
Route::get('page/{page_slug}/{section_slug?}', array("as" => "custom_page", "uses" => "CustomPageController@showPage"));
Route::post('gestion/page/{page_slug}/{section_slug?}', array("as" => "edit_custom_page_submit", "uses" => "CustomPageController@savePage"));
Route::get('gestion/pages/supprimer-page/{page_slug}', array("as" => "delete_custom_page", "uses" => "CustomPageController@deletePage"));
Route::post('gestion/pages/nouvelle-page', array("as" => "add_custom_page", "uses" => "CustomPageController@addCustomPage"));
Route::post('gestion/pages/nouvel-ordre', array("as" => "ajax_change_custom_page_order", "uses" => "CustomPageController@saveCustomPageOrder"));

// Images
Route::get('images/{image_id}', array("as" => "get_page_image", "uses" => "PageImageController@getImage"));
Route::post('ajax/images/upload', array("as" => "ajax_upload_image", "uses" => "PageImageController@uploadImage"));

// Section pages
Route::get('unite', array("as" => "section_unit", "uses" => "SectionPageController@showUnitPage"));
Route::get('section/{section_slug?}', array("as" => "section", "uses" => "SectionPageController@showPage"));
Route::get('gestion/page-section/{section_slug}', array("as" => "edit_section_page", "uses" => "SectionPageController@showEdit"));
Route::post('gestion/page-section/{section_slug}', array("as" => "edit_section_page_submit", "uses" => "SectionPageController@savePage"));

// Addresses
Route::get('gestion/adresses/{section_slug?}', array("as" => "edit_address_page", "uses" => "ContactController@showEdit"));
Route::post('gestion/adresses/{section_slug?}', array("as" => "edit_address_page_submit", "uses" => "ContactController@savePage"));

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
Route::get('inscription/reinscription/{member_id}', array("as" => "reregistration", "uses" => "RegistrationController@reregister"));
Route::get('inscription-fermee/{section_slug?}', array("as" => "registration_inactive", "uses" => "RegistrationInactiveController@showMain"));
Route::get('gestion/inscription/page-principale/{section_slug?}', array("as" => "edit_registration_active_page", "uses" => "RegistrationController@showEdit"));
Route::post('gestion/inscription/page-principale/{section_slug?}', array("as" => "edit_registration_active_page_submit", "uses" => "RegistrationController@savePage"));
Route::get('gestion/inscription/page-inscriptions-desactivees/{section_slug?}', array("as" => "edit_registration_inactive_page", "uses" => "RegistrationInactiveController@showEdit"));
Route::post('gestion/inscription/page-inscriptions-desactivees/{section_slug?}', array("as" => "edit_registration_inactive_page_submit", "uses" => "RegistrationInactiveController@savePage"));
Route::get('gestion/inscription/nouvelles-inscriptions/{section_slug?}', array("as" => "manage_registration", "uses" => "RegistrationController@manageRegistration"));
Route::post('gestion/inscription/nouvelles-inscriptions/submit/{section_slug?}', array("as" => "manage_registration_submit", "uses" => "RegistrationController@manageSubmit"));
Route::get('gestion/inscription/supprimer-inscription/{member_id}', array("as" => "edit_delete_registration", "uses" => "RegistrationController@deleteRegistration"));
Route::get('gestion/inscription/reinscription/{section_slug?}', array("as" => "manage_reregistration", "uses" => "RegistrationController@manageReregistration"));
Route::get('ajax/gestion/inscription/reinscription', array("as" => "ajax_reregister", "uses" => "RegistrationController@ajaxReregister"));
Route::get('ajax/gestion/inscription/annulation-reinscription', array("as" => "ajax_cancel_reregistration", "uses" => "RegistrationController@ajaxCancelReregistration"));
Route::get('ajax/gestion/inscription/desinscription', array("as" => "ajax_delete_member", "uses" => "RegistrationController@ajaxDeleteMember"));
Route::get('gestion/inscription/annee-des-scouts/{section_slug?}', array("as" => "manage_year_in_section", "uses" => "RegistrationController@manageYearInSection"));
Route::get('ajax/gestion/inscription/annee-des-scouts/changer', array("as" => "ajax_update_year_in_section", "uses" => "RegistrationController@ajaxUpdateYearInSection"));
Route::get('gestion/inscription/changer-de-section/{section_slug?}', array("as" => "manage_member_section", "uses" => "RegistrationController@manageMemberSection"));
Route::post('gestion/inscription/changer-de-section/submit/{section_slug}', array("as" => "manage_member_section_submit", "uses" => "RegistrationController@submitUpdateSection"));
Route::get('gestion/inscription/cotisation/{section_slug?}', array("as" => "manage_subscription_fee", "uses" => "RegistrationController@manageSubscriptionFee"));
Route::post('ajax/gestion/inscription/cotisation', array("as" => "ajax_update_subscription_fee", "uses" => "RegistrationController@updateSubscriptionFee"));

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
Route::get('gestion/liens/{section_slug?}', array("as" => "edit_links", "uses" => "LinkController@showEdit"));
Route::post('gestion/liens/{section_slug?}', array("as" => "edit_links_submit", "uses" => "LinkController@submitLink"));
Route::get('gestion/liens/delete/{link_id}/{section_slug?}', array("as" => "edit_links_delete", "uses" => "LinkController@deleteLink"));

// News
Route::get('actualites-de-lunite/{section_slug?}', array("as" => "global_news", "uses" => "NewsController@showGlobalNewsPage"));
Route::get('actualites/archives/{section_slug?}', array("as" => "news_archives", "uses" => "NewsController@showArchives"));
Route::get('actualites/{section_slug?}', array("as" => "news", "uses" => "NewsController@showPage"));
Route::get('gestion/actualites/{section_slug?}', array("as" => "manage_news", "uses" => "NewsController@showEdit"));
Route::post('gestion/actualites/submit/{section_slug}', array("as" => "manage_news_submit", "uses" => "NewsController@submitNews"));
Route::get('gestion/actualites/delete/{news_id}', array("as" => "manage_news_delete", "uses" => "NewsController@deleteNews"));
Route::get('nouvelle/{news_id}', array("as" => "single_news", "uses" => "NewsController@showSingleNews"));

// Calendar
Route::get('calendrier/{year}/{month}/{section_slug?}', array("as" => "calendar_month", "uses" => "CalendarController@showPage"));
Route::get('calendrier/{section_slug?}', array("as" => "calendar", "uses" => "CalendarController@showPage"));
Route::get('gestion/calendrier/{year}/{month}/{section_slug?}', array("as" => "manage_calendar_month", "uses" => "CalendarController@showEdit"));
Route::get('gestion/calendrier/{section_slug?}', array("as" => "manage_calendar", "uses" => "CalendarController@showEdit"));
Route::post('gestion/calendrier/submit/{year}/{month}/{section_slug}', array("as" => "manage_calendar_submit", "uses" => "CalendarController@submitItem"));
Route::get('gestion/calendrier/delete/{year}/{month}/{section_slug}/{event_id}', array("as" => "manage_calendar_delete", "uses" => "CalendarController@deleteItem"));
Route::post('calendrier/telecharger', array("as" => "download_calendar", "uses" => "CalendarController@downloadCalendar"));

// Attendance
Route::get('gestion/presences/{section_slug?}/{year?}', array("as" => "edit_attendance", "uses" => "AttendanceController@editAttendance"));
Route::post('gestion/presences/upload/{section_slug}/{year}', array("as" => "upload_attendance", "uses" => "AttendanceController@upload"));

// Payment
Route::get('gestion/paiement/{section_slug?}/{year?}', array("as" => "edit_payment", "uses" => "PaymentController@editPayment"));
Route::post('ajax/gestion/paiement/{section_slug}/{year}', array("as" => "upload_payment", "uses" => "PaymentController@upload"));
Route::post('ajax/gestion/paiement/nouvelle-activite/{section_slug}/{year}', array("as" => "add_payment_event", "uses" => "PaymentController@addNewEvent"));
Route::post('ajax/gestion/paiement/supprimer-activite/{section_slug}/{year}', array("as" => "delete_payment_event", "uses" => "PaymentController@deleteEvent"));

// Documents
Route::get('telecharger/archives/{section_slug?}', array("as" => "document_archives", "uses" => "DocumentController@showArchives"));
Route::get('telecharger/{section_slug?}', array("as" => "documents", "uses" => "DocumentController@showPage"));
Route::get('gestion/telecharger/{section_slug?}', array("as" => "manage_documents", "uses" => "DocumentController@showEdit"));
Route::post('gestion/telecharger/submit/{section_slug}', array("as" => "manage_documents_submit", "uses" => "DocumentController@submitDocument"));
Route::get('gestion/telecharger/delete/{document_id}', array("as" => "manage_documents_delete", "uses" => "DocumentController@deleteDocument"));
Route::get('gestion/telecharger/archiver/{section_slug}/{document_id}', array("as" => "manage_documents_archive", "uses" => "DocumentController@archiveDocument"));
Route::get('telechager-document/{document_id}', array("as" => "download_document", "uses" => "DocumentController@downloadDocument"));
Route::post('telecharger/par-email', array("as" => "send_document_by_email", "uses" => "DocumentController@sendByEmail"));

// E-mails
Route::get('e-mails/archives/{section_slug?}', array("as" => "email_archives", "uses" => "EmailController@showArchives"));
Route::get('e-mails/{section_slug?}', array("as" => "emails", "uses" => "EmailController@showPage"));
Route::get('e-mails/piece-jointe/{attachment_id}', array("as" => "download_attachment", "uses" => "EmailController@downloadAttachment"));
Route::get('gestion/e-mails/{section_slug?}', array("as" => "manage_emails", "uses" => "EmailController@showManage"));
Route::get('gestion/envoi-e-mail/{section_slug?}', array("as" => "send_section_email", "uses" => "EmailController@sendSectionEmail"));
Route::get('gestion/envoi-e-mail-cotisation-impayee/{section_slug?}', array("as" => "send_unpaid_subscription_fee_email", "uses" => "EmailController@sendUnpaidSubscriptionFeeEmail"));
Route::post('gestion/envoi-e-mail/submit/{section_slug}', array("as" => "send_section_email_submit", "uses" => "EmailController@submitSectionEmail"));
Route::get('gestion/e-mails/supprimer/{email_id}', array("as" => "manage_emails_delete", "uses" => "EmailController@deleteEmail"));
Route::get('gestion/e-mails/archiver/{section_slug}/{email_id}', array("as" => "manage_emails_archive", "uses" => "EmailController@archiveEmail"));
Route::get('gestion/envoi-e-mail-animateurs/{section_slug?}', array("as" => "send_leader_email", "uses" => "EmailController@sendLeaderEmail"));

// Daily photo
Route::get('photos-du-jour/{date?}', array("as" => "daily_photos", "uses" => "DailyPhotoController@showPage"));

// Photos
Route::get('photos/archives/{section_slug?}', array("as" => "photo_archives", "uses" => "PhotoController@showArchives"));
Route::get('photos/{section_slug?}', array("as" => "photos", "uses" => "PhotoController@showPage"));
Route::get('photos-{album_id}/{section_slug?}', array("as" => "photo_album", "uses" => "PhotoController@showAlbum"));
Route::get('photo/{format}/{photo_id}/{filename?}', array("as" => "get_photo", "uses" => "PhotoController@getPhoto"));
Route::get('photos/telecharger-album/{album_id}/{first_photo}/{last_photo}', array("as" => "download_photo_album", "uses" => "PhotoController@downloadAlbum"));
Route::get('gestion/photos/{section_slug?}', array("as" => "edit_photos", "uses" => "PhotoController@showEdit"));
Route::get('gestion/photos/{section_slug?}', array("as" => "edit_photos", "uses" => "PhotoController@showEdit"));
Route::get('gestion/photos/supprimer-album/{album_id}/{section_slug?}', array("as" => "delete_photo_album", "uses" => "PhotoController@deletePhotoAlbum"));
Route::get('gestion/photos/archiver-album/{album_id}/{section_slug?}', array("as" => "archive_photo_album", "uses" => "PhotoController@archivePhotoAlbum"));
Route::get('gestion/photos/album/{album_id}/{section_slug?}', array("as" => "edit_photo_album", "uses" => "PhotoController@showEditAlbum"));
Route::post('ajax/gestion/photos/changer-ordre-albums', array("as" => "ajax_change_album_order", "uses" => "PhotoController@changeAlbumOrder"));
Route::post('ajax/gestion/photos/changer-ordre-photos', array("as" => "ajax_change_photo_order", "uses" => "PhotoController@changePhotoOrder"));
Route::get('gestion/photos/nouvel-album/{section_slug}', array("as" => "create_photo_album", "uses" => "PhotoController@createPhotoAlbum"));
Route::get('ajax/gestion/photos/supprimer-photo', array("as" => "ajax_delete_photo", "uses" => "PhotoController@deletePhoto"));
Route::post('ajax/gestion/photos/ajouter-photo', array("as" => "ajax_add_photo", "uses" => "PhotoController@addPhoto"));
Route::post('ajax/gestion/photos/changer-nom-album', array("as" => "ajax_change_album_name", "uses" => "PhotoController@changeAlbumName"));
Route::post('ajax/gestion/photos/changer-description-photo', array("as" => "ajax_change_photo_caption", "uses" => "PhotoController@changePhotoCaption"));
Route::get('ajax/gestion/photos/tourner', array("as" => "ajax_rotate_photo", "uses" => "PhotoController@rotatePhoto"));

// Leaders
Route::get('animateurs/archives/{year}/{section_slug?}', array("as" => "archived_leaders", "uses" => "LeaderController@showArchivedLeaders"));
Route::get('animateurs/{section_slug?}', array("as" => "leaders", "uses" => "LeaderController@showPage"));
Route::get('animateurs/photo/{leader_id}', array("as" => "get_leader_picture", "uses" => "LeaderController@getLeaderPicture"));
Route::get('archive-animateurs/photo/{archived_leader_id}', array("as" => "get_archived_leader_picture", "uses" => "LeaderController@getArchivedLeaderPicture"));
Route::get('gestion/animateurs/{section_slug?}', array("as" => "edit_leaders", "uses" => "LeaderController@showEdit"));
Route::get('gestion/animateurs/scout-en-animateur/{member_id}/{section_slug}', array("as" => "edit_leaders_member_to_leader", "uses" => "LeaderController@showMemberToLeader"));
Route::post('gestion/animateurs/scout-en-animateur/{section_slug}', array("as" => "edit_leaders_member_to_leader_post", "uses" => "LeaderController@postMemberToLeader"));
Route::post('gestion/animateurs/submit/{section_slug?}', array("as" => "edit_leaders_submit", "uses" => "LeaderController@submitLeader"));
Route::get('gestion/animateurs/supprimer/{member_id}/{section_slug}', array("as" => "edit_leaders_delete", "uses" => "LeaderController@deleteLeader"));
Route::get('gestion/privileges/{section_slug?}', array("as" => "edit_privileges", "uses" => "PrivilegeController@showEdit"));
Route::post('ajax/gestion/privileges/change', array("as" => "ajax_change_privileges", "uses" => "PrivilegeController@updatePrivileges"));
Route::get('gestion/anciens-animateurs/{section_slug}/{archive?}', array("as" => "edit_archived_leaders", "uses" => "ArchivedLeaderController@showPage"));
Route::post('gestion/anciens-animateurs/submit/{archive}/{section_slug?}', array("as" => "edit_archived_leaders_submit", "uses" => "ArchivedLeaderController@submitLeader"));
Route::get('gestion/anciens-animateurs/supprimer/{member_id}/{section_slug}/{archive}', array("as" => "edit_archived_leaders_delete", "uses" => "ArchivedLeaderController@deleteLeader"));

// Listing
Route::get('listing/{section_slug?}', array("as" => "listing", "uses" => "ListingController@showPage"));
Route::get('gestion/listing/{section_slug?}', array("as" => "manage_listing", "uses" => "ListingController@showEdit"));
Route::post('gestion/listing/submit/{section_slug?}', array("as" => "manage_listing_submit", "uses" => "ListingController@manageSubmit"));
Route::post('listing/submit/{section_slug?}', array("as" => "listing_submit", "uses" => "ListingController@submit"));
Route::get('gestion/listing/delete/{member_id}/{section_slug?}', array("as" => "manage_listing_delete", "uses" => "ListingController@deleteMember"));
Route::get('listing/telecharger/{section_slug}/{format?}', array("as" => "download_listing", "uses" => "ListingController@downloadListing"));
Route::get('gestion/animateurs/listing/telecharger/{section_slug}/{format}', array("as" => "download_listing_leaders", "uses" => "ListingController@downloadLeaderListing"));
Route::get('gestion/listing/telecharger/{format}/{section_slug}', array("as" => "download_full_listing", "uses" => "ListingController@downloadFullListing"));
Route::get('gestion/listing/enveloppes/{format}/{section_slug}', array("as" => "download_envelops", "uses" => "ListingController@downloadEnvelops"));
Route::get('gestion/listing/telechargement/{section_slug?}', array("as" => "download_listing_options", "uses" => "ListingController@showDownloadListingPage"));
Route::post('gestion/listing/telechargement-options', array("as" => "download_listing_with_options", "uses" => "ListingController@downloadListingWithOptions"));
Route::any('gestion/listing-desk/{section_slug?}', array("as" => "desk_listing", "uses" => "ListingController@showDeskPage"));

// Suggestions
Route::get('suggestions/{section_slug?}', array("as" => "suggestions", "uses" => "SuggestionController@showPage"));
Route::get('gestion/suggestions/{section_slug?}', array("as" => "edit_suggestions", "uses" => "SuggestionController@showEdit"));
Route::post('suggestions/submit', array("as" => "suggestions_submit", "uses" => "SuggestionController@submit"));
Route::get('gestion/suggestion/supprimer/{suggestion_id}', array("as" => "edit_suggestions_delete", "uses" => "SuggestionController@deleteSuggestion"));
Route::post('gestion/suggestion/soumettre-reponse/{suggestion_id}', array("as" => "edit_suggestions_submit_response", "uses" => "SuggestionController@submitResponse"));

// Guest book
Route::get('livre-or/{section_slug?}', array("as" => "guest_book", "uses" => "GuestBookController@showPage"));
Route::post('livre-or/soumettre', array("as" => "guest_book_submit", "uses" => "GuestBookController@submit"));
Route::get('gestion/livre-or/{section_slug?}', array("as" => "edit_guest_book", "uses" => "GuestBookController@showEdit"));
Route::get('gestion/livre-or/supprimer/{entry_id}', array("as" => "edit_guest_book_delete", "uses" => "GuestBookController@delete"));

// Help
Route::get('aide/{section_slug?}', array("as" => "help", "uses" => "HelpPageController@showPage"));
Route::get('gestion/aide/{section_slug?}', array("as" => "edit_help_page", "uses" => "HelpPageController@showEdit"));
Route::post('gestion/aide/{section_slug?}', array("as" => "edit_help_page_submit", "uses" => "HelpPageController@savePage"));

// Leaders' corner
Route::get('gestion/coin-des-animateurs/aide/{section_slug?}', array("as" => "leader_help", "uses" => "LeaderHelpController@showPage"));
Route::get('gestion/coin-des-animateurs/{section_slug?}', array("as" => "leader_corner", "uses" => "LeaderCornerController@showPage"));

// Users
Route::get('gestion/utilisateurs/{section_slug?}', array("as" => "user_list", "uses" => "UserController@showUserList"));
Route::get('gestion/utilisateurs/supprimer/{user_id}', array("as" => "delete_user", "uses" => "UserController@deleteUser"));

// Accounting
Route::get('gestion/tresorerie/{section_slug?}', array("as" => "accounting", "uses" => "AccountingController@showPageCurrentYear"));
Route::get('gestion/tresorerie/annee/{year}/{section_slug?}', array("as" => "accounting_by_year", "uses" => "AccountingController@showPage"));
Route::post('ajax/gestion/tresorerie/commit-changes/{lockId}/{section_slug?}', array("as" => "ajax-accounting-commit-changes", "uses" => "AccountingController@commitChanges"));
Route::get('ajax/gestion/tresorerie/update-lock/{lockId}', array("as" => "ajax-accounting-extend-lock", "uses" => "AccountingController@ajaxUpdateLock"));

// Section data
Route::get('gestion/donnees-section/{section_slug?}', array("as" => "section_data", "uses" => "SectionDataController@showPage"));
Route::post('gestion/donnees-section/submit/{section_slug?}', array("as" => "edit_section_submit", "uses" => "SectionDataController@submitSectionData"));
Route::get('gestion/donnees-section/supprimer/{section_id}', array("as" => "edit_section_delete", "uses" => "SectionDataController@deleteSection"));
Route::post('ajax/gestion/donnees-section/changer-ordre-sections', array("as" => "ajax_change_section_order", "uses" => "SectionDataController@changeSectionOrder"));

// Parameters
Route::get('gestion/parametres/css/{section_slug?}', array("as" => "edit_css", "uses" => "ParameterController@showEditCSS"));
Route::get('gestion/parametres/css-quitter-mode-test', array("as" => "edit_css_stop_testing", "uses" => "ParameterController@exitCSSTestMode"));
Route::post('gestion/parametres/css/submit', array("as" => "edit_css_submit", "uses" => "ParameterController@submitCSS"));
Route::get('gestion/parametres/{section_slug?}', array("as" => "edit_parameters", "uses" => "ParameterController@showEdit"));
Route::post('gestion/parametres/submit', array("as" => "edit_parameters_submit", "uses" => "ParameterController@submitParameters"));

// View recent changes
Route::get('changements-recents/{section_slug?}', array("as" => "view_recent_changes", "uses" => "RecentChangesController@showPage"));
Route::get('gestion/changements-recents/{section_slug?}', array("as" => "view_private_recent_changes", "uses" => "RecentChangesController@showPrivateChanges"));

// Monitoring
Route::get('gestion/supervision', array("as" => "monitoring", "uses" => "MonitoringController@showPage"));

// Personal e-mails
Route::get('email-personnel/{contact_type}/{member_id}/{section_slug?}', array("as" => "personal_email", "uses" => "PersonalEmailController@sendEmail"));
Route::post('email-personnel/soumettre/{contact_type}/{member_id}', array("as" => "personal_email_submit", "uses" => "PersonalEmailController@submit"));

// Logs
Route::get('gestion/logs', array("as" => "logs", "uses" => "LogController@showPage"));
Route::get('ajax/gestion/logs/logs-suivants/{lastKnownLogId}/{count}', array("as" => "ajax_load_more_logs", "uses" => "LogController@loadMoreLogs"));

// Comments
Route::post('post-comment/{referent_type}/{referent_id}', array("as" => "post-comment", "uses" => "CommentController@postComment"));

// Search
Route::get('rechercher/{section_slug?}', array("as" => "search", "uses" => "SearchController@showSearchPage"));
Route::post('rechercher/{section_slug?}', array("as" => "search", "uses" => "SearchController@showSearchPage"));

// Website bootstrapping
Route::get('/initialisation-du-site', array("as" => "bootstrapping", "uses" => "WebsiteBootstrappingController@showPage"));
Route::any('/initialisation-du-site/etape-{step}', array("as" => "bootstrapping_step", "uses" => "WebsiteBootstrappingController@showStep"));

// Home
Route::get('/{section_slug?}', array("as" => "home", "uses" => "HomePageController@showPage"));
Route::get('gestion/accueil/{section_slug?}', array("as" => "edit_home_page", "uses" => "HomePageController@showEdit"));
Route::post('gestion/accueil/{section_slug?}', array("as" => "edit_home_page_submit", "uses" => "HomePageController@savePage"));
