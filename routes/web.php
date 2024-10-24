<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

use App\Models\Parameter;
use App\Models\LogEntry;
use App\Helpers\ScoutMailer;
use App\Models\HealthCard;
use App\Models\Member;
use App\Models\User;
use App\Helpers\ElasticsearchHelper;

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

View::composer('menu.menu', "App\ViewComposers\MenuComposer");
View::composer('menu.user_box', "App\ViewComposers\UserBoxComposer");
View::composer('menu.tabs', "App\ViewComposers\TabsComposer");



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
Route::get('logo-image', array("as" => "website_logo", "uses" => "App\Http\Controllers\HomePageController@websiteLogo"));
Route::get('icon-image', array("as" => "website_icon", "uses" => "App\Http\Controllers\HomePageController@websiteIcon"));
Route::get('session/keepalive', array("as" => "session_keepalive", "uses" => function() {}));
Route::get('css-unite.css', array("as" => "additional_css", "uses" => function() {
  if (Session::get('testing-css')) return response(Parameter::get(Parameter::$ADDITIONAL_CSS_BUFFER))->header('Content-Type', 'text/css');
  return response(Parameter::get(Parameter::$ADDITIONAL_CSS))->header('Content-Type', 'text/css');
}));
Route::get('image-statique/{filename}', array("as" => "static_image", "uses" => "App\Http\Controllers\PageImageController@getStaticImage"));


// Users
Route::get('login/{section_slug?}', array("as" => "login", "uses" => "App\Http\Controllers\UserController@login"));
Route::post('login/{section_slug?}', array("as" => "login_submit", "uses" => "App\Http\Controllers\UserController@submitLogin"));
Route::get('logout/{section_slug?}', array("as" => "logout", "uses" => "App\Http\Controllers\UserController@logout"));
Route::any('modifier-utilisateur/email/{section_slug?}', array("as" => "edit_user_email", "uses" => "App\Http\Controllers\UserController@editEmail"));
Route::any('modifier-utilisateur/mot-de-passe/{section_slug?}', array("as" => "edit_user_password", "uses" => "App\Http\Controllers\UserController@editPassword"));
Route::any('modifier-utilisateur/section/{section_slug?}', array("as" => "edit_user_section", "uses" => "App\Http\Controllers\UserController@editSection"));
Route::get('modifier-utilisateur/{section_slug?}', array("as" => "edit_user", "uses" => "App\Http\Controllers\UserController@editUser"));
Route::any('recuperer-mot-de-passe/{section_slug?}', array("as" => "retrieve_password", "uses" => "App\Http\Controllers\UserController@retrievePassword"));
Route::any('changer-mot-de-passe/{code}/{section_slug?}', array("as" => "change_password", "uses" => "App\Http\Controllers\UserController@changePassword"));
Route::post('nouvel-utilisateur/{section_slug?}', array("as" => "create_user", "uses" => "App\Http\Controllers\UserController@create"));
Route::get('nouvel-utilisation/confirmation/{section_slug?}', array("as" => "user_created", "uses" => "App\Http\Controllers\UserController@userCreated"));
Route::get('verifier-utilisateur/{code}', array("as" => "verify_user", "uses" => "App\Http\Controllers\UserController@verify"));
Route::get('annuler-utilisateur/{code}', array("as" => "cancel_user", "uses" => "App\Http\Controllers\UserController@cancelVerification"));
Route::get('renvoyer-lien-validation', array("as" => "user_resend_validation_link", "uses" => "App\Http\Controllers\UserController@resendValidationLink"));

// Ban e-mail address
Route::get('desinscrire-addresse-email/{ban_code}', array("as" => "ban_email", "uses" => "App\Http\Controllers\BanEmailAddressController@banEmailAddress"));
Route::get('desinscrire-addresse-email/confirmer/{ban_code}', array("as" => "confirm_ban_email", "uses" => "App\Http\Controllers\BanEmailAddressController@confirmBanEmailAddress"));
Route::get('desinscrire-addresse-email/annuler/{ban_code}', array("as" => "confirm_unban_email", "uses" => "App\Http\Controllers\BanEmailAddressController@cancelBanEmailAddress"));

// Custom pages
Route::get('gestion/pages/{section_slug?}', array("as" => "edit_pages", "uses" => "App\Http\Controllers\CustomPageController@showPageList"));
Route::get('gestion/page/{page_slug}/{section_slug?}', array("as" => "edit_custom_page", "uses" => "App\Http\Controllers\CustomPageController@showEdit"));
Route::get('page/{page_slug}/{section_slug?}', array("as" => "custom_page", "uses" => "App\Http\Controllers\CustomPageController@showPage"));
Route::post('gestion/page/{page_slug}/{section_slug?}', array("as" => "edit_custom_page_submit", "uses" => "App\Http\Controllers\CustomPageController@savePage"));
Route::get('gestion/pages/supprimer-page/{page_slug}', array("as" => "delete_custom_page", "uses" => "App\Http\Controllers\CustomPageController@deletePage"));
Route::post('gestion/pages/nouvelle-page', array("as" => "add_custom_page", "uses" => "App\Http\Controllers\CustomPageController@addCustomPage"));
Route::post('gestion/pages/nouvel-ordre', array("as" => "ajax_change_custom_page_order", "uses" => "App\Http\Controllers\CustomPageController@saveCustomPageOrder"));

// Images
Route::get('images/{image_id}', array("as" => "get_page_image", "uses" => "App\Http\Controllers\PageImageController@getImage"));
Route::post('ajax/images/upload', array("as" => "ajax_upload_image", "uses" => "App\Http\Controllers\PageImageController@uploadImage"));

// Section pages
Route::get('unite', array("as" => "section_unit", "uses" => "App\Http\Controllers\SectionPageController@showUnitPage"));
Route::get('section/{section_slug?}', array("as" => "section", "uses" => "App\Http\Controllers\SectionPageController@showPage"));
Route::get('gestion/page-section/{section_slug}', array("as" => "edit_section_page", "uses" => "App\Http\Controllers\SectionPageController@showEdit"));
Route::post('gestion/page-section/{section_slug}', array("as" => "edit_section_page_submit", "uses" => "App\Http\Controllers\SectionPageController@savePage"));

// Addresses
Route::get('gestion/adresses/{section_slug?}', array("as" => "edit_address_page", "uses" => "App\Http\Controllers\ContactController@showEdit"));
Route::post('gestion/adresses/{section_slug?}', array("as" => "edit_address_page_submit", "uses" => "App\Http\Controllers\ContactController@savePage"));

// Contacts
Route::get('contacts/{section_slug?}', array("as" => "contacts", "uses" => "App\Http\Controllers\ContactController@showPage"));

// Annual feast
Route::get('fete-unite/{section_slug?}', array("as" => "annual_feast", "uses" => "App\Http\Controllers\AnnualFeastController@showPage"));
Route::get('gestion/fete-unite/{section_slug?}', array("as" => "edit_annual_feast_page", "uses" => "App\Http\Controllers\AnnualFeastController@showEdit"));
Route::post('gestion/fete-unite/{section_slug?}', array("as" => "edit_annual_feast_page_submit", "uses" => "App\Http\Controllers\AnnualFeastController@savePage"));

// Registration
Route::get('inscription/formulaire/{section_slug?}', array("as" => "registration_form", "uses" => "App\Http\Controllers\RegistrationController@showForm"));
Route::post('inscription/formulaire/submit/{section_slug?}', array("as" => "registration_form_submit", "uses" => "App\Http\Controllers\RegistrationController@submit"));
Route::get('inscription/{section_slug?}', array("as" => "registration", "uses" => "App\Http\Controllers\RegistrationController@showMain"));
Route::get('inscription/reinscription/{member_id}', array("as" => "reregistration", "uses" => "App\Http\Controllers\RegistrationController@reregister"));
Route::get('inscription-fermee/{section_slug?}', array("as" => "registration_inactive", "uses" => "App\Http\Controllers\RegistrationInactiveController@showMain"));
Route::get('gestion/inscription/page-principale/{section_slug?}', array("as" => "edit_registration_active_page", "uses" => "App\Http\Controllers\RegistrationController@showEdit"));
Route::post('gestion/inscription/page-principale/{section_slug?}', array("as" => "edit_registration_active_page_submit", "uses" => "App\Http\Controllers\RegistrationController@savePage"));
Route::get('gestion/inscription/page-inscriptions-desactivees/{section_slug?}', array("as" => "edit_registration_inactive_page", "uses" => "App\Http\Controllers\RegistrationInactiveController@showEdit"));
Route::post('gestion/inscription/page-inscriptions-desactivees/{section_slug?}', array("as" => "edit_registration_inactive_page_submit", "uses" => "App\Http\Controllers\RegistrationInactiveController@savePage"));
Route::get('gestion/inscription/formulaire/{section_slug?}', array("as" => "edit_registration_form", "uses" => "App\Http\Controllers\RegistrationController@editForm"));
Route::post('gestion/inscription/formulaire/{section_slug?}', array("as" => "edit_registration_form_submit", "uses" => "App\Http\Controllers\RegistrationController@saveForm"));
Route::get('gestion/inscription/nouvelles-inscriptions/{section_slug?}', array("as" => "manage_registration", "uses" => "App\Http\Controllers\RegistrationController@manageRegistration"));
Route::post('gestion/inscription/nouvelles-inscriptions/submit/{section_slug?}', array("as" => "manage_registration_submit", "uses" => "App\Http\Controllers\RegistrationController@manageSubmit"));
Route::get('gestion/inscription/supprimer-inscription/{member_id}', array("as" => "edit_delete_registration", "uses" => "App\Http\Controllers\RegistrationController@deleteRegistration"));
Route::get('ajax/gestion/inscription/liste-attente', array("as" => "ajax_toggle_waiting_list", "uses" => "App\Http\Controllers\RegistrationController@ajaxToggleWaitingList"));
Route::get('gestion/inscription/reinscription/{section_slug?}', array("as" => "manage_reregistration", "uses" => "App\Http\Controllers\RegistrationController@manageReregistration"));
Route::get('ajax/gestion/inscription/reinscription', array("as" => "ajax_reregister", "uses" => "App\Http\Controllers\RegistrationController@ajaxReregister"));
Route::get('ajax/gestion/inscription/annulation-reinscription', array("as" => "ajax_cancel_reregistration", "uses" => "App\Http\Controllers\RegistrationController@ajaxCancelReregistration"));
Route::get('ajax/gestion/inscription/desinscription', array("as" => "ajax_delete_member", "uses" => "App\Http\Controllers\RegistrationController@ajaxDeleteMember"));
Route::get('gestion/inscription/annee-des-scouts/{section_slug?}', array("as" => "manage_year_in_section", "uses" => "App\Http\Controllers\RegistrationController@manageYearInSection"));
Route::get('ajax/gestion/inscription/annee-des-scouts/changer', array("as" => "ajax_update_year_in_section", "uses" => "App\Http\Controllers\RegistrationController@ajaxUpdateYearInSection"));
Route::get('gestion/inscription/changer-de-section/{section_slug?}', array("as" => "manage_member_section", "uses" => "App\Http\Controllers\RegistrationController@manageMemberSection"));
Route::post('gestion/inscription/changer-de-section/submit/{section_slug}', array("as" => "manage_member_section_submit", "uses" => "App\Http\Controllers\RegistrationController@submitUpdateSection"));
Route::get('gestion/inscription/cotisation/{section_slug?}', array("as" => "manage_subscription_fee", "uses" => "App\Http\Controllers\RegistrationController@manageSubscriptionFee"));
Route::post('ajax/gestion/inscription/cotisation', array("as" => "ajax_update_subscription_fee", "uses" => "App\Http\Controllers\RegistrationController@updateSubscriptionFee"));
Route::get('gestion/inscription/reinitialisation-cotisations/{status}', array("as" => "set_all_suscription_fees", "uses" => "App\Http\Controllers\RegistrationController@setAllSubscriptionFees"));
Route::post('gestion/inscription/priorite', array("as" => "submit_registration_priority", "uses" => "App\Http\Controllers\RegistrationController@submitPriority"));
Route::get('gestion/inscription/recalculer-annees', array("as" => "recompute_years_in_section", "uses" => "App\Http\Controllers\RegistrationController@recomputeYearsInSection"));
Route::get('gestion/inscription/telecharger-liste', array("as" => "download_registration_list", "uses" => "App\Http\Controllers\RegistrationController@downloadRegistrationList"));
Route::get('inscription/formulaire-lien-unique/{code}', array("as" => "temporary_registration_link", "uses" => "App\Http\Controllers\RegistrationController@showFormWithTemporaryLink"));
Route::get('gestion/inscription/creer-lien-temporaire/{section_slug?}', array("as" => "create_temporary_registration_link", "uses" => "App\Http\Controllers\RegistrationController@createTemporaryRegistrationLink"));
Route::post('gestion/inscription/creer-lien-temporaire/{section_slug?}', array("as" => "create_temporary_registration_link_post", "uses" => "App\Http\Controllers\RegistrationController@createTemporaryRegistrationLink"));
Route::get('gestion/inscription/e-mail/{section_slug?}', array("as" => "advanced_registration_email", "uses" => "App\Http\Controllers\RegistrationController@showAdvancedRegistrationEmailPage"));

// Absences
Route::get('absences/{section_slug?}', array("as" => "absences", "uses" => "App\Http\Controllers\AbsenceController@showPage"));
Route::post('absences/submit/{section_slug?}', array("as" => "submit_absence", "uses" => "App\Http\Controllers\AbsenceController@submit"));
Route::get('gestion/absences/{section_slug?}', array("as" => "manage_absences", "uses" => "App\Http\Controllers\AbsenceController@showManage"));
Route::get('gestion/absences/inscription-emails/{member_id}/{section_slug?}', array("as" => "register_to_absence_emails", "uses" => "App\Http\Controllers\AbsenceController@registerToAbsenceEmails"));
Route::get('gestion/absences/desinscription-emails/{member_id}/{section_slug?}', array("as" => "unregister_from_absence_emails", "uses" => "App\Http\Controllers\AbsenceController@unregisterFromAbsenceEmails"));

// Health card
Route::get('fiche-sante/completer/{member_id}/{section_slug?}', array("as" => "health_card_edit", "uses" => "App\Http\Controllers\HealthCardController@showEdit"));
Route::post('fiche-sante/submit/{section_slug?}', array("as" => "health_card_submit", "uses" => "App\Http\Controllers\HealthCardController@submit"));
Route::get('fiche-sante/telecharger/{member_id}/{section_slug?}', array("as" => "health_card_download", "uses" => "App\Http\Controllers\HealthCardController@download"));
Route::get('fiche-sante/telecharger-tout/{section_slug?}', array("as" => "health_card_download_all", "uses" => "App\Http\Controllers\HealthCardController@downloadAll"));
Route::get('fiche-sante/{section_slug?}', array("as" => "health_card", "uses" => "App\Http\Controllers\HealthCardController@showPage"));
Route::get('gestion/fiche-sante/{section_slug?}', array("as" => "manage_health_cards", "uses" => "App\Http\Controllers\HealthCardController@showManage"));
Route::get('gestion/fiche-sante/telecharger-tout/{section_slug}', array("as" => "manage_health_cards_download_all", "uses" => "App\Http\Controllers\HealthCardController@downloadSectionCards"));
Route::get('gestion/fiche-sante/telecharger-resume/{section_slug}', array("as" => "manage_health_cards_download_summary", "uses" => "App\Http\Controllers\HealthCardController@downloadSectionSummary"));

// Unit policy
Route::get('charte/{section_slug?}', array("as" => "unit_policy", "uses" => "App\Http\Controllers\UnitPolicyPageController@showPage"));
Route::get('gestion/charte/{section_slug?}', array("as" => "edit_unit_policy_page", "uses" => "App\Http\Controllers\UnitPolicyPageController@showEdit"));
Route::post('gestion/charte/{section_slug?}', array("as" => "edit_unit_policy_page_submit", "uses" => "App\Http\Controllers\UnitPolicyPageController@savePage"));

// Leader policy
Route::get('charte-animateurs/{section_slug?}', array("as" => "leader_policy", "uses" => "App\Http\Controllers\LeaderPolicyPageController@showPage"));
Route::get('gestion/charte-animateurs/{section_slug?}', array("as" => "edit_leader_policy_page", "uses" => "App\Http\Controllers\LeaderPolicyPageController@showEdit"));
Route::post('gestion/charte-animateurs/{section_slug?}', array("as" => "edit_leader_policy_page_submit", "uses" => "App\Http\Controllers\LeaderPolicyPageController@savePage"));
Route::post('signature-charte-animateurs', array("as" => "submit_leader_policy_signature", "uses" => "App\Http\Controllers\LeaderPolicyPageController@submitSignature"));

// GDPR
Route::get('rgpd/{section_slug?}', array("as" => "gdpr", "uses" => "App\Http\Controllers\GDPRPageController@showPage"));
Route::get('gestion/rgpd/{section_slug?}', array("as" => "edit_gdpr_page", "uses" => "App\Http\Controllers\GDPRPageController@showEdit"));
Route::post('gestion/rgpd/{section_slug?}', array("as" => "edit_gdpr_page_submit", "uses" => "App\Http\Controllers\GDPRPageController@savePage"));

// Uniforms
Route::get('uniforme/{section_slug?}', array("as" => "uniform", "uses" => "App\Http\Controllers\UniformPageController@showPage"));
Route::get('gestion/uniforme/{section_slug}', array("as" => "edit_uniform_page", "uses" => "App\Http\Controllers\UniformPageController@showEdit"));
Route::post('gestion/uniforme/{section_slug}', array("as" => "edit_uniform_page_submit", "uses" => "App\Http\Controllers\UniformPageController@savePage"));

// Links
Route::get('gestion/liens/{section_slug?}', array("as" => "edit_links", "uses" => "App\Http\Controllers\LinkController@showEdit"));
Route::post('gestion/liens/{section_slug?}', array("as" => "edit_links_submit", "uses" => "App\Http\Controllers\LinkController@submitLink"));
Route::get('gestion/liens/delete/{link_id}/{section_slug?}', array("as" => "edit_links_delete", "uses" => "App\Http\Controllers\LinkController@deleteLink"));

// News
Route::get('actualites-de-lunite/{section_slug?}', array("as" => "global_news", "uses" => "App\Http\Controllers\NewsController@showGlobalNewsPage"));
Route::get('actualites/archives/{section_slug?}', array("as" => "news_archives", "uses" => "App\Http\Controllers\NewsController@showArchives"));
Route::get('actualites/{section_slug?}', array("as" => "news", "uses" => "App\Http\Controllers\NewsController@showPage"));
Route::get('gestion/actualites/{section_slug?}', array("as" => "manage_news", "uses" => "App\Http\Controllers\NewsController@showEdit"));
Route::post('gestion/actualites/submit/{section_slug}', array("as" => "manage_news_submit", "uses" => "App\Http\Controllers\NewsController@submitNews"));
Route::get('gestion/actualites/delete/{news_id}', array("as" => "manage_news_delete", "uses" => "App\Http\Controllers\NewsController@deleteNews"));
Route::get('nouvelle/{news_id}', array("as" => "single_news", "uses" => "App\Http\Controllers\NewsController@showSingleNews"));

// Calendar
Route::get('calendrier/{year}/{month}/{section_slug?}', array("as" => "calendar_month", "uses" => "App\Http\Controllers\CalendarController@showPage"));
Route::get('calendrier/{section_slug?}', array("as" => "calendar", "uses" => "App\Http\Controllers\CalendarController@showPage"));
Route::get('gestion/calendrier/{year}/{month}/{section_slug?}', array("as" => "manage_calendar_month", "uses" => "App\Http\Controllers\CalendarController@showEdit"));
Route::get('gestion/calendrier/{section_slug?}', array("as" => "manage_calendar", "uses" => "App\Http\Controllers\CalendarController@showEdit"));
Route::post('gestion/calendrier/submit/{year}/{month}/{section_slug}', array("as" => "manage_calendar_submit", "uses" => "App\Http\Controllers\CalendarController@submitItem"));
Route::get('gestion/calendrier/delete/{year}/{month}/{section_slug}/{event_id}', array("as" => "manage_calendar_delete", "uses" => "App\Http\Controllers\CalendarController@deleteItem"));
Route::post('calendrier/telecharger', array("as" => "download_calendar", "uses" => "App\Http\Controllers\CalendarController@downloadCalendar"));
Route::get('calendrier-liste/{section_slug?}', array("as" => "calendar_as_list", "uses" => "App\Http\Controllers\CalendarController@showCalendarAsList"));
Route::get('calendrier-icalendar/{section_id}', array("as" => "export_calendar", "uses" => "App\Http\Controllers\CalendarController@exportCalendar"));
Route::get('icone-calendrier/{type}', array("as" => "calendar_icon", "uses" => "App\Http\Controllers\CalendarController@getCalendarIcon"));

// Attendance
Route::get('gestion/presences/{section_slug?}/{year?}', array("as" => "edit_attendance", "uses" => "App\Http\Controllers\AttendanceController@editAttendance"));
Route::post('gestion/presences/upload/{section_slug}/{year}', array("as" => "upload_attendance", "uses" => "App\Http\Controllers\AttendanceController@upload"));

// Payment
Route::get('gestion/paiement/{section_slug?}/{year?}', array("as" => "edit_payment", "uses" => "App\Http\Controllers\PaymentController@editPayment"));
Route::post('ajax/gestion/paiement/{section_slug}/{year}', array("as" => "upload_payment", "uses" => "App\Http\Controllers\PaymentController@upload"));
Route::post('ajax/gestion/paiement/nouvelle-activite/{section_slug}/{year}', array("as" => "add_payment_event", "uses" => "App\Http\Controllers\PaymentController@addNewEvent"));
Route::post('ajax/gestion/paiement/supprimer-activite/{section_slug}/{year}', array("as" => "delete_payment_event", "uses" => "App\Http\Controllers\PaymentController@deleteEvent"));

// Documents
Route::get('telecharger/archives/{section_slug?}', array("as" => "document_archives", "uses" => "App\Http\Controllers\DocumentController@showArchives"));
Route::get('telecharger/{section_slug?}', array("as" => "documents", "uses" => "App\Http\Controllers\DocumentController@showPage"));
Route::get('gestion/telecharger/{section_slug?}', array("as" => "manage_documents", "uses" => "App\Http\Controllers\DocumentController@showEdit"));
Route::post('gestion/telecharger/submit/{section_slug}', array("as" => "manage_documents_submit", "uses" => "App\Http\Controllers\DocumentController@submitDocument"));
Route::get('gestion/telecharger/delete/{document_id}', array("as" => "manage_documents_delete", "uses" => "App\Http\Controllers\DocumentController@deleteDocument"));
Route::get('gestion/telecharger/archiver/{section_slug}/{document_id}', array("as" => "manage_documents_archive", "uses" => "App\Http\Controllers\DocumentController@archiveDocument"));
Route::get('telechager-document/{document_id}', array("as" => "download_document", "uses" => "App\Http\Controllers\DocumentController@downloadDocument"));
Route::post('telecharger/par-email', array("as" => "send_document_by_email", "uses" => "App\Http\Controllers\DocumentController@sendByEmail"));

// E-mails
Route::get('e-mails/archives/{section_slug?}', array("as" => "email_archives", "uses" => "App\Http\Controllers\EmailController@showArchives"));
Route::get('e-mails/{section_slug?}', array("as" => "emails", "uses" => "App\Http\Controllers\EmailController@showPage"));
Route::get('e-mails/piece-jointe/{attachment_id}', array("as" => "download_attachment", "uses" => "App\Http\Controllers\EmailController@downloadAttachment"));
Route::get('gestion/e-mails/{section_slug?}', array("as" => "manage_emails", "uses" => "App\Http\Controllers\EmailController@showManage"));
Route::get('gestion/envoi-e-mail/{section_slug?}', array("as" => "send_section_email", "uses" => "App\Http\Controllers\EmailController@sendSectionEmail"));
Route::get('gestion/envoi-e-mail-cotisation-impayee/{section_slug?}', array("as" => "send_unpaid_subscription_fee_email", "uses" => "App\Http\Controllers\EmailController@sendUnpaidSubscriptionFeeEmail"));
Route::post('gestion/envoi-e-mail/submit/{section_slug}', array("as" => "send_section_email_submit", "uses" => "App\Http\Controllers\EmailController@submitSectionEmail"));
Route::get('gestion/e-mails/supprimer/{email_id}', array("as" => "manage_emails_delete", "uses" => "App\Http\Controllers\EmailController@deleteEmail"));
Route::get('gestion/e-mails/archiver/{section_slug}/{email_id}', array("as" => "manage_emails_archive", "uses" => "App\Http\Controllers\EmailController@archiveEmail"));
Route::get('gestion/envoi-e-mail-animateurs/{section_slug?}', array("as" => "send_leader_email", "uses" => "App\Http\Controllers\EmailController@sendLeaderEmail"));
Route::post('gestion/envoi-e-mail-liste-destinataire/{section_slug?}', array("as" => "send_email_to_recipient_list", "uses" => "App\Http\Controllers\EmailController@sendEmailToRecipientList"));

// Daily photo
Route::get('photos-du-jour/{date?}', array("as" => "daily_photos", "uses" => "App\Http\Controllers\DailyPhotoController@showPage"));

// Photos
Route::get('photos/archives/{section_slug?}', array("as" => "photo_archives", "uses" => "App\Http\Controllers\PhotoController@showArchives"));
Route::get('photos/{section_slug?}', array("as" => "photos", "uses" => "App\Http\Controllers\PhotoController@showPage"));
Route::get('photos-{album_id}/{section_slug?}', array("as" => "photo_album", "uses" => "App\Http\Controllers\PhotoController@showAlbum"));
Route::get('photo/{format}/{photo_id}/{filename?}', array("as" => "get_photo", "uses" => "App\Http\Controllers\PhotoController@getPhoto"));
Route::get('photos/telecharger-album/{album_id}/{first_photo}/{last_photo}', array("as" => "download_photo_album", "uses" => "App\Http\Controllers\PhotoController@downloadAlbum"));
Route::get('gestion/photos/{section_slug?}', array("as" => "edit_photos", "uses" => "App\Http\Controllers\PhotoController@showEdit"));
Route::get('gestion/photos/{section_slug?}', array("as" => "edit_photos", "uses" => "App\Http\Controllers\PhotoController@showEdit"));
Route::get('gestion/photos/changer-acces-prive/{album_id}/{status}/{section_slug?}', array("as" => "toggle_photo_album_privacy", "uses" => "App\Http\Controllers\PhotoController@toggleAlbumPrivacy"));
Route::get('gestion/photos/supprimer-album/{album_id}/{section_slug?}', array("as" => "delete_photo_album", "uses" => "App\Http\Controllers\PhotoController@deletePhotoAlbum"));
Route::get('gestion/photos/archiver-album/{album_id}/{section_slug?}', array("as" => "archive_photo_album", "uses" => "App\Http\Controllers\PhotoController@archivePhotoAlbum"));
Route::get('gestion/photos/album/{album_id}/{section_slug?}', array("as" => "edit_photo_album", "uses" => "App\Http\Controllers\PhotoController@showEditAlbum"));
Route::post('ajax/gestion/photos/changer-ordre-albums', array("as" => "ajax_change_album_order", "uses" => "App\Http\Controllers\PhotoController@changeAlbumOrder"));
Route::post('ajax/gestion/photos/changer-ordre-photos', array("as" => "ajax_change_photo_order", "uses" => "App\Http\Controllers\PhotoController@changePhotoOrder"));
Route::get('gestion/photos/nouvel-album/{section_slug}', array("as" => "create_photo_album", "uses" => "App\Http\Controllers\PhotoController@createPhotoAlbum"));
Route::get('ajax/gestion/photos/supprimer-photo', array("as" => "ajax_delete_photo", "uses" => "App\Http\Controllers\PhotoController@deletePhoto"));
Route::post('ajax/gestion/photos/ajouter-photo', array("as" => "ajax_add_photo", "uses" => "App\Http\Controllers\PhotoController@addPhoto"));
Route::post('ajax/gestion/photos/changer-nom-album', array("as" => "ajax_change_album_name", "uses" => "App\Http\Controllers\PhotoController@changeAlbumName"));
Route::post('ajax/gestion/photos/changer-description-photo', array("as" => "ajax_change_photo_caption", "uses" => "App\Http\Controllers\PhotoController@changePhotoCaption"));
Route::get('ajax/gestion/photos/tourner', array("as" => "ajax_rotate_photo", "uses" => "App\Http\Controllers\PhotoController@rotatePhoto"));

// Leaders
Route::get('animateurs/archives/{year}/{section_slug?}', array("as" => "archived_leaders", "uses" => "App\Http\Controllers\LeaderController@showArchivedLeaders"));
Route::get('animateurs/{section_slug?}', array("as" => "leaders", "uses" => "App\Http\Controllers\LeaderController@showPage"));
Route::get('archive-animateurs/photo/{archived_leader_id}', array("as" => "get_archived_leader_picture", "uses" => "App\Http\Controllers\LeaderController@getArchivedLeaderPicture"));
Route::get('gestion/animateurs/{section_slug?}', array("as" => "edit_leaders", "uses" => "App\Http\Controllers\LeaderController@showEdit"));
Route::get('gestion/animateurs/scout-en-animateur/{member_id}/{section_slug}', array("as" => "edit_leaders_member_to_leader", "uses" => "App\Http\Controllers\LeaderController@showMemberToLeader"));
Route::post('gestion/animateurs/scout-en-animateur/{section_slug}', array("as" => "edit_leaders_member_to_leader_post", "uses" => "App\Http\Controllers\LeaderController@postMemberToLeader"));
Route::post('gestion/animateurs/submit/{section_slug?}', array("as" => "edit_leaders_submit", "uses" => "App\Http\Controllers\LeaderController@submitLeader"));
Route::get('gestion/animateurs/supprimer/{member_id}/{section_slug}', array("as" => "edit_leaders_delete", "uses" => "App\Http\Controllers\LeaderController@deleteLeader"));
Route::get('gestion/privileges/{section_slug?}', array("as" => "edit_privileges", "uses" => "App\Http\Controllers\PrivilegeController@showEdit"));
Route::post('ajax/gestion/privileges/change', array("as" => "ajax_change_privileges", "uses" => "App\Http\Controllers\PrivilegeController@updatePrivileges"));
Route::get('gestion/anciens-animateurs/{section_slug}/{archive?}', array("as" => "edit_archived_leaders", "uses" => "App\Http\Controllers\ArchivedLeaderController@showPage"));
Route::post('gestion/anciens-animateurs/submit/{archive}/{section_slug?}', array("as" => "edit_archived_leaders_submit", "uses" => "App\Http\Controllers\ArchivedLeaderController@submitLeader"));
Route::get('gestion/anciens-animateurs/supprimer/{member_id}/{section_slug}/{archive}', array("as" => "edit_archived_leaders_delete", "uses" => "App\Http\Controllers\ArchivedLeaderController@deleteLeader"));

// Listing
Route::get('listing/{section_slug?}', array("as" => "listing", "uses" => "App\Http\Controllers\ListingController@showPage"));
Route::get('gestion/listing/{section_slug?}', array("as" => "manage_listing", "uses" => "App\Http\Controllers\ListingController@showEdit"));
Route::post('gestion/listing/submit/{section_slug?}', array("as" => "manage_listing_submit", "uses" => "App\Http\Controllers\ListingController@manageSubmit"));
Route::post('listing/submit/{section_slug?}', array("as" => "listing_submit", "uses" => "App\Http\Controllers\ListingController@submit"));
Route::get('gestion/listing/delete/{member_id}/{section_slug?}', array("as" => "manage_listing_delete", "uses" => "App\Http\Controllers\ListingController@deleteMember"));
Route::get('listing/telecharger/{section_slug}/{format?}', array("as" => "download_listing", "uses" => "App\Http\Controllers\ListingController@downloadListing"));
Route::get('gestion/animateurs/listing/telecharger/{section_slug}/{format}', array("as" => "download_listing_leaders", "uses" => "App\Http\Controllers\ListingController@downloadLeaderListing"));
Route::get('gestion/listing/telecharger/{format}/{section_slug}', array("as" => "download_full_listing", "uses" => "App\Http\Controllers\ListingController@downloadFullListing"));
Route::get('gestion/listing/enveloppes/{format}/{section_slug}', array("as" => "download_envelops", "uses" => "App\Http\Controllers\ListingController@downloadEnvelops"));
Route::get('gestion/listing/telechargement/{section_slug?}', array("as" => "download_listing_options", "uses" => "App\Http\Controllers\ListingController@showDownloadListingPage"));
Route::post('gestion/listing/telechargement-options', array("as" => "download_listing_with_options", "uses" => "App\Http\Controllers\ListingController@downloadListingWithOptions"));
Route::any('gestion/listing-desk/{section_slug?}', array("as" => "desk_listing", "uses" => "App\Http\Controllers\ListingController@showDeskPage"));
Route::get('listing/sous-groupes/{section_slug?}', array("as" => "listing_view_subgroups", "uses" => "App\Http\Controllers\ListingController@showSubgroupPage"));
Route::get('listing/photos-membres/{section_slug?}', array("as" => "listing_view_pictures", "uses" => "App\Http\Controllers\ListingController@showMemberPicturePage"));
Route::get('listing/photo/{leader_id}', array("as" => "get_member_picture", "uses" => "App\Http\Controllers\ListingController@getMemberPicture"));
Route::get('listing/telecharger-photos-membres/{section_slug}/{format?}', array("as" => "download_member_pictures", "uses" => "App\Http\Controllers\ListingController@downloadMemberPictures"));
Route::post('ajax/listing/change-subgroup-or-role', array("as" => "ajax_change_subgroup_or_role", "uses" => "App\Http\Controllers\ListingController@ajaxChangeSubgroupOrRole"));

// Suggestions
Route::get('suggestions/{section_slug?}', array("as" => "suggestions", "uses" => "App\Http\Controllers\SuggestionController@showPage"));
Route::get('gestion/suggestions/{section_slug?}', array("as" => "edit_suggestions", "uses" => "App\Http\Controllers\SuggestionController@showEdit"));
Route::post('suggestions/submit', array("as" => "suggestions_submit", "uses" => "App\Http\Controllers\SuggestionController@submit"));
Route::get('gestion/suggestion/supprimer/{suggestion_id}', array("as" => "edit_suggestions_delete", "uses" => "App\Http\Controllers\SuggestionController@deleteSuggestion"));
Route::post('gestion/suggestion/soumettre-reponse/{suggestion_id}', array("as" => "edit_suggestions_submit_response", "uses" => "App\Http\Controllers\SuggestionController@submitResponse"));

// Guest book
Route::get('livre-or/{section_slug?}', array("as" => "guest_book", "uses" => "App\Http\Controllers\GuestBookController@showPage"));
Route::post('livre-or/soumettre', array("as" => "guest_book_submit", "uses" => "App\Http\Controllers\GuestBookController@submit"));
Route::get('gestion/livre-or/{section_slug?}', array("as" => "edit_guest_book", "uses" => "App\Http\Controllers\GuestBookController@showEdit"));
Route::get('gestion/livre-or/supprimer/{entry_id}', array("as" => "edit_guest_book_delete", "uses" => "App\Http\Controllers\GuestBookController@delete"));

// Help
Route::get('aide/{section_slug?}', array("as" => "help", "uses" => "App\Http\Controllers\HelpPageController@showPage"));
Route::get('gestion/aide/{section_slug?}', array("as" => "edit_help_page", "uses" => "App\Http\Controllers\HelpPageController@showEdit"));
Route::post('gestion/aide/{section_slug?}', array("as" => "edit_help_page_submit", "uses" => "App\Http\Controllers\HelpPageController@savePage"));

// Leaders' corner
Route::get('gestion/coin-des-animateurs/aide/{section_slug?}', array("as" => "leader_help", "uses" => "App\Http\Controllers\LeaderHelpController@showPage"));
Route::get('gestion/coin-des-animateurs/{section_slug?}', array("as" => "leader_corner", "uses" => "App\Http\Controllers\LeaderCornerController@showPage"));

// Users
Route::get('gestion/utilisateurs/{section_slug?}', array("as" => "user_list", "uses" => "App\Http\Controllers\UserController@showUserList"));
Route::get('gestion/utilisateurs/supprimer/{user_id}', array("as" => "delete_user", "uses" => "App\Http\Controllers\UserController@deleteUser"));

// Accounting
Route::get('gestion/tresorerie/{section_slug?}', array("as" => "accounting", "uses" => "App\Http\Controllers\AccountingController@showPageCurrentYear"));
Route::get('gestion/tresorerie/annee/{year}/{section_slug?}', array("as" => "accounting_by_year", "uses" => "App\Http\Controllers\AccountingController@showPage"));
Route::post('ajax/gestion/tresorerie/commit-changes/{lock_id}/{section_slug?}', array("as" => "ajax-accounting-commit-changes", "uses" => "App\Http\Controllers\AccountingController@commitChanges"));
Route::get('ajax/gestion/tresorerie/update-lock/{lock_id}', array("as" => "ajax-accounting-extend-lock", "uses" => "App\Http\Controllers\AccountingController@ajaxUpdateLock"));

// Section data
Route::get('gestion/donnees-section/{section_slug?}', array("as" => "section_data", "uses" => "App\Http\Controllers\SectionDataController@showPage"));
Route::post('gestion/donnees-section/submit/{section_slug?}', array("as" => "edit_section_submit", "uses" => "App\Http\Controllers\SectionDataController@submitSectionData"));
Route::get('gestion/donnees-section/supprimer/{section_id}', array("as" => "edit_section_delete", "uses" => "App\Http\Controllers\SectionDataController@deleteSection"));
Route::post('ajax/gestion/donnees-section/changer-ordre-sections', array("as" => "ajax_change_section_order", "uses" => "App\Http\Controllers\SectionDataController@changeSectionOrder"));

// Parameters
Route::get('gestion/parametres/css/{section_slug?}', array("as" => "edit_css", "uses" => "App\Http\Controllers\ParameterController@showEditCSS"));
Route::get('gestion/parametres/css-quitter-mode-test', array("as" => "edit_css_stop_testing", "uses" => "App\Http\Controllers\ParameterController@exitCSSTestMode"));
Route::post('gestion/parametres/css/submit', array("as" => "edit_css_submit", "uses" => "App\Http\Controllers\ParameterController@submitCSS"));
Route::get('gestion/parametres/{section_slug?}', array("as" => "edit_parameters", "uses" => "App\Http\Controllers\ParameterController@showEdit"));
Route::post('gestion/parametres/submit', array("as" => "edit_parameters_submit", "uses" => "App\Http\Controllers\ParameterController@submitParameters"));

// View recent changes
Route::get('changements-recents/{section_slug?}', array("as" => "view_recent_changes", "uses" => "App\Http\Controllers\RecentChangesController@showPage"));
Route::get('gestion/changements-recents/{section_slug?}', array("as" => "view_private_recent_changes", "uses" => "App\Http\Controllers\RecentChangesController@showPrivateChanges"));

// Monitoring
Route::get('gestion/supervision', array("as" => "monitoring", "uses" => "App\Http\Controllers\MonitoringController@showPage"));

// Personal e-mails
Route::get('email-personnel/{contact_type}/{member_id}/{section_slug?}', array("as" => "personal_email", "uses" => "App\Http\Controllers\PersonalEmailController@sendEmail"));
Route::post('email-personnel/soumettre/{contact_type}/{member_id}', array("as" => "personal_email_submit", "uses" => "App\Http\Controllers\PersonalEmailController@submit"));

// Logs
Route::get('gestion/logs', array("as" => "logs", "uses" => "App\Http\Controllers\LogController@showPage"));
Route::get('ajax/gestion/logs/logs-suivants/{lastKnownLogId}/{count}', array("as" => "ajax_load_more_logs", "uses" => "App\Http\Controllers\LogController@loadMoreLogs"));

// Comments
Route::post('post-comment/{referent_type}/{referent_id}', array("as" => "post-comment", "uses" => "App\Http\Controllers\CommentController@postComment"));

// Search
Route::get('rechercher/{section_slug?}', array("as" => "search", "uses" => "App\Http\Controllers\SearchController@showSearchPage"));
Route::post('rechercher/{section_slug?}', array("as" => "search_post", "uses" => "App\Http\Controllers\SearchController@showSearchPage"));

// Website bootstrapping
Route::get('/initialisation-du-site', array("as" => "bootstrapping", "uses" => "App\Http\Controllers\WebsiteBootstrappingController@showPage"));
Route::any('/initialisation-du-site/etape-{step}', array("as" => "bootstrapping_step", "uses" => "App\Http\Controllers\WebsiteBootstrappingController@showStep"));

// Home
Route::get('/{section_slug?}', array("as" => "home", "uses" => "App\Http\Controllers\HomePageController@showPage"));
Route::get('gestion/accueil/{section_slug?}', array("as" => "edit_home_page", "uses" => "App\Http\Controllers\HomePageController@showEdit"));
Route::post('gestion/accueil/{section_slug?}', array("as" => "edit_home_page_submit", "uses" => "App\Http\Controllers\HomePageController@savePage"));