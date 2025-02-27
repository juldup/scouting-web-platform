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

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\CalendarPDF;
use App\Helpers\DateHelper;
use App\Helpers\ElasticsearchHelper;
use App\Helpers\EnvelopsPDF;
use App\Helpers\Form;
use App\Helpers\HealthCardPDF;
use App\Helpers\Helper;
use App\Helpers\ListingComparison;
use App\Helpers\ListingPDF;
use App\Helpers\Resizer;
use App\Helpers\ScoutMailer;
use App\Models\Absence;
use App\Models\AccountingItem;
use App\Models\AccountingLock;
use App\Models\ArchivedLeader;
use App\Models\Attendance;
use App\Models\BannedEmail;
use App\Models\CalendarItem;
use App\Models\Comment;
use App\Models\DailyPhoto;
use App\Models\Document;
use App\Models\Email;
use App\Models\EmailAttachment;
use App\Models\GuestBookEntry;
use App\Models\HealthCard;
use App\Models\Link;
use App\Models\LogEntry;
use App\Models\Member;
use App\Models\MemberHistory;
use App\Models\News;
use App\Models\Page;
use App\Models\PageImage;
use App\Models\Parameter;
use App\Models\PasswordRecovery;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\PendingEmail;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Privilege;
use App\Models\Section;
use App\Models\Suggestion;
use App\Models\TemporaryRegistrationLink;
use App\Models\User;

/**
 * Parameters are global parameters for the website. This controller provides tools
 * for the leaders to change these parameters.
 */
class ParameterController extends BaseController {
  
  /**
   * [Route] Shows the parameter management page
   */
  function showEdit() {
    // Check that the user can edit the parameters
    if (!$this->user->can(Privilege::$EDIT_GLOBAL_PARAMETERS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Put the registration fee values in an array
    $prices = array(
        '1 child' => Parameter::get(Parameter::$PRICE_1_CHILD),
        '1 leader' => Parameter::get(Parameter::$PRICE_1_LEADER),
        '2 children' => Parameter::get(Parameter::$PRICE_2_CHILDREN),
        '2 leaders' => Parameter::get(Parameter::$PRICE_2_LEADERS),
        '3 children' => Parameter::get(Parameter::$PRICE_3_CHILDREN),
        '3 leaders' => Parameter::get(Parameter::$PRICE_3_LEADERS),
    );
    // AnU denomination list
    $anuDenominationList = [
        "animateur d'unité" => "Animateur d'unité",
        "anu" => "AnU",
        "responsable d'unité" => "Responsable d'unité",
        "animateur responsable d'unité" => "Animateur responsable d'unité",
    ];
    $asuDenominationList = [
        "équipier d'unité" => "Équipier d'unité",
        "asu" => "AsU",
        "animateur d'unité" => "Animateur d'unité",
        "assistant d'unité" => "Assistant d'unité",
    ];
    // Make view
    return View::make('pages.parameters.editParameters', array(
        'pages' => $this->getPageList(),
        'registration_active' => Parameter::get(Parameter::$REGISTRATION_ACTIVE),
        'reregistration_active' => Parameter::get(Parameter::$REREGISTRATION_ACTIVE),
        'registration_automatic' => Parameter::get(Parameter::$REGISTRATION_AUTOMATIC),
        'advanced_registrations' => Parameter::get(Parameter::$ADVANCED_REGISTRATIONS),
        'grouped_section_menu' => Parameter::get(Parameter::$GROUPED_SECTION_MENU),
        'prices' => $prices,
        'document_categories' => explode(";", Parameter::get(Parameter::$DOCUMENT_CATEGORIES)),
        'safe_emails' => explode(";", Parameter::get(Parameter::$VERIFIED_EMAIL_SENDERS)),
        'logo_two_lines' => Parameter::get(Parameter::$LOGO_TWO_LINES),
        'allow_personal_contact' => Parameter::get(Parameter::$ALLOW_PERSONAL_CONTACT),
        'consider_scouts_as_members' => Parameter::get(Parameter::$CONSIDER_SCOUTS_AS_MEMBERS),
        'anuDenominationList' => $anuDenominationList,
        'asuDenominationList' => $asuDenominationList,
    ));
  }
  
  /**
   * [Route] Updates the parameters. The whole set of parameters is updated with this action.
   */
  function submitParameters(Request $request) {
    // Check that the user can edit the parameters
    if (!$this->user->can(Privilege::$EDIT_GLOBAL_PARAMETERS, 1)) {
      return Helper::forbiddenResponse();
    }
    $changesMade = "";
    $error = false;
    $errorMessage = "";
    $parameterNewValues = [
        // Prices
        ["name" => "Prix un membre - enfant", "key" => Parameter::$PRICE_1_CHILD, "value" => Helper::formatCashAmount($request->input('price_1_child'))],
        ["name" => "Prix un membre - animateur", "key" => Parameter::$PRICE_1_LEADER, "value" => Helper::formatCashAmount($request->input('price_1_leader'))],
        ["name" => "Prix deux membres - enfant", "key" => Parameter::$PRICE_2_CHILDREN, "value" => Helper::formatCashAmount($request->input('price_2_children'))],
        ["name" => "Prix deux membres - animateur", "key" => Parameter::$PRICE_2_LEADERS, "value" => Helper::formatCashAmount($request->input('price_2_leaders'))],
        ["name" => "Prix trois membres ou plus - enfant", "key" => Parameter::$PRICE_3_CHILDREN, "value" => Helper::formatCashAmount($request->input('price_3_children'))],
        ["name" => "Prix trois membres ou plus - animateur", "key" => Parameter::$PRICE_3_LEADERS, "value" => Helper::formatCashAmount($request->input('price_3_leaders'))],
        // Registration
        ["name" => "Inscriptions", "key" => Parameter::$REGISTRATION_ACTIVE, "value" => ($request->input('registration_active') ? "true" : "false"),
            "valueNames" => ["true" => "actives", "false" => "désactivées"]],
        ["name" => "Réinscriptions", "key" => Parameter::$REREGISTRATION_ACTIVE, "value" => ($request->input('reregistration_active') ? "true" : "false"),
            "valueNames" => ["true" => "actives", "false" => "désactivées"]],
        ["name" => "Inscriptions activation automatique", "key" => Parameter::$REGISTRATION_AUTOMATIC, "value" => ($request->input('registration_automatic') ? "true" : "false"),
            "valueNames" => ["true" => "oui", "false" => "non"]],
        ["name" => "Inscriptions avancées", "key" => Parameter::$ADVANCED_REGISTRATIONS, "value" => ($request->input('advanced_registrations') ? "true" : "false"),
            "valueNames" => ["true" => "actives", "false" => "désactivées"]],
        ["name" => "Localité prioritaire", "key" => Parameter::$REGISTRATION_PRIORITY_CITY,
            "value" => $request->input('registration_priority_city') ? $request->input('registration_priority_city') : Parameter::get(Parameter::$REGISTRATION_PRIORITY_CITY)],
        ["name" => "Sections génériques pour les inscriptions", "key" => Parameter::$REGISTRATION_GENERIC_SECTIONS, "value" => ($request->input('registration_generic_sections') ? "true" : "false"),
            "valueNames" => ["true" => "oui", "false" => "non"]],
        // Section menu
        ["name" => "Menu de section", "key" => Parameter::$GROUPED_SECTION_MENU, "value" => ($request->input('grouped_section_menu') ? "true" : "false"),
            "valueNames" => ["true" => "groupé", "false" => "séparé"]],
    ];
    // Automatic registration start and end dates
    if ($request->input('registration_automatic')) {
      $registrationStartDate = $request->input('registration_start_date');
      if (DateHelper::checkMMDDHHMMFormat($registrationStartDate)) {
        $parameterNewValues = array_merge($parameterNewValues, [
            ["name" => "Date début inscriptions", "key" => Parameter::$REGISTRATION_START_DATE, "value" => $registrationStartDate],
        ]);
      } else {
        $error = true;
        $errorMessage .= "<br>Le format de la date de début des inscriptions n'est pas valide : il doit être de la forme \"MM-JJ hh:mm\" (p. ex. \"01-15 20:00\").";
      }
      $registrationEndDate = $request->input('registration_end_date');
      if (DateHelper::checkMMDDHHMMFormat($registrationEndDate)) {
        $parameterNewValues = array_merge($parameterNewValues, [
            ["name" => "Date fin inscriptions", "key" => Parameter::$REGISTRATION_END_DATE, "value" => $registrationEndDate],
        ]);
      } else {
        $error = true;
        $errorMessage .= "<br>Le format de la date de fin des inscriptions n'est pas valide : il doit être de la forme \"MM-JJ hh:mm\" (p. ex. \"01-15 20:00\").";
      }
    }
    // Pages
    foreach ($this->getPageList() as $page => $pageData) {
      $parameterNewValues[] = [
          "name" => $pageData['description'],
          "key" => $pageData['parameter_name'],
          "value" => $request->input($page) ? "true" : "false",
          "valueNames" => ["true" => "oui", "false" => "non"],
      ];
    }
    // Document categories
    $documentCategoryArray = $request->input('document_categories');
    $documentCategories = "";
    foreach ($documentCategoryArray as $categoryName) {
      if ($categoryName) {
        if ($documentCategories) $documentCategories .= ";";
        $documentCategories .= str_replace(";", ",", $categoryName);
      }
    }
    $parameterNewValues = array_merge($parameterNewValues, [
        ["name" => "Catégories de documents", "key" => Parameter::$DOCUMENT_CATEGORIES, "value" => $documentCategories],
        // Unit parameters
        ["name" => "Nom de l'unité", "key" => Parameter::$UNIT_LONG_NAME, "value" => $request->input('unit_long_name')],
        ["name" => "Sigle de l'unité", "key" => Parameter::$UNIT_SHORT_NAME, "value" => $request->input('unit_short_name')],
        ["name" => "N° de compte", "key" => Parameter::$UNIT_BANK_ACCOUNT, "value" => $request->input('unit_bank_account')],
        ["name" => "Logo", "key" => Parameter::$LOGO_TWO_LINES, "value" => $request->input('logo_two_lines') ? 1 : 0,
            "valueNames" => ["1" => "sur deux lignes", "0" => "sur une ligne"]],
        ["name" => "Appellation AnU", "key" => Parameter::$ANU_DENOMINATION, "value" => $request->input('anu_denomination')],
        ["name" => "Appellation AsU", "key" => Parameter::$ASU_DENOMINATION, "value" => $request->input('asu_denomination')],
        ["name" => "Contact personnel", "key" => Parameter::$ALLOW_PERSONAL_CONTACT, "value" => $request->input('allow_personal_contact') ? 1 : 0,
            "valueNames" => ["1" => "oui", "0" => "non"]],
        ["name" => "Accès aux pages confidentielles pour les scouts", "key" => Parameter::$CONSIDER_SCOUTS_AS_MEMBERS, "value" => $request->input('consider_scouts_as_members') ? 1 : 0,
            "valueNames" => ["1" => "oui", "0" => "non"]],
        ["name" => "Historique des membres", "key" => Parameter::$SHOW_MEMBER_HISTORY, "value" => $request->input('show_member_history') ? 1 : 0,
            "valueNames" => ["1" => "oui", "0" => "non"]],
        // Search engine
        ["name" => "Description du site", "key" => Parameter::$WEBSITE_META_DESCRIPTION, "value" => $request->input('website_meta_description')],
        ["name" => "Mots-clés de recherche", "key" => Parameter::$WEBSITE_META_KEYWORDS, "value" => $request->input('website_meta_keywords')],
        // Automatic e-mail content
        ["name" => "Contenu e-mail automatique demande d'inscription", "key" => Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_FORM_FILLED, "value" => $request->input('registration_form_filled')],
        ["name" => "Contenu e-mail automatique inscription validée", "key" => Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_VALIDATED, "value" => $request->input('registration_validated')],
        // Facebook app id
        ["name" => "Facebook App ID", "key" => Parameter::$FACEBOOK_APP_ID, "value" => $request->input('facebook_app_id')],
        // Save the advanced site parameters
        ["name" => "Contenu additionnel &lt;head&gt;", "key" => Parameter::$ADDITIONAL_HEAD_HTML, "value" => $request->input('additional_head_html')],
        ["name" => "Photos", "key" => Parameter::$PHOTOS_PUBLIC, "value" => $request->input('photos_public') ? "true" : "false",
            "valueNames" => ["true" => "publiques", "false" => "privées"]],
        // E-mail parameters
        ["name" => "Adresse e-mail du webmaster", "key" => Parameter::$WEBMASTER_EMAIL, "value" => $request->input('webmaster_email')],
        ["name" => "Adresse e-mail du site", "key" => Parameter::$DEFAULT_EMAIL_FROM_ADDRESS, "value" => $request->input('default_email_from_address')],
        ["name" => "Hôte SMTP", "key" => Parameter::$SMTP_HOST, "value" => $request->input('smtp_host')],
        ["name" => "Port SMTP", "key" => Parameter::$SMTP_PORT, "value" => $request->input('smtp_port')],
        ["name" => "Login SMTP", "key" => Parameter::$SMTP_USERNAME, "value" => $request->input('smtp_username')],
        ["name" => "Mot de passe SMTP", "key" => Parameter::$SMTP_PASSWORD, "value" => $request->input('smtp_password')],
        ["name" => "Sécurité SMTP", "key" => Parameter::$SMTP_SECURITY, "value" => $request->input('smtp_security')],
        ["name" => "Envoyer les demandes d'inscription à l'adresse d'unité", "key" => Parameter::$SEND_REGISTRATIONS_TO_UNIT_EMAIL_ADDRESS, "value" => $request->input('send_registrations_to_unit_email_address') ? "true" : "false"],
    ]);
    // Verified e-mail sender list
    $verifiedSendersArray = $request->input('email_safe_list');
    $verifiedSenders = "";
    foreach ($verifiedSendersArray as $verifiedSender) {
      if ($verifiedSender && strpos($verifiedSender, ";") === false) {
        if ($verifiedSenders) $verifiedSenders .= ";";
        $verifiedSenders .= strtolower($verifiedSender);
      }
    }
    $parameterNewValues = array_merge($parameterNewValues, [
        ["name" => "Adresse e-mail vérifiées", "key" => Parameter::$VERIFIED_EMAIL_SENDERS, "value" => $verifiedSenders],
    ]);
    
    // Update parameter values in database
    foreach ($parameterNewValues as $parameterData) {
      try {
        $oldValue = Parameter::get($parameterData['key'], false);
        if ($oldValue != $parameterData['value']) {
          Parameter::set($parameterData['key'], $parameterData['value']);
          if (array_key_exists("valueNames", $parameterData)) {
            $changesMade .= "- " . $parameterData['name'] . "&nbsp;: <del>" . 
                    ($oldValue ? str_replace("<", "&lt;", $parameterData['valueNames'][$oldValue]) : "(inexistant)") .
                    "</del> <ins>" . str_replace("<", "&lt;", $parameterData['valueNames'][$parameterData['value']]) . "</ins><br>";
          } else {
            $changesMade .= "- " . $parameterData['name'] . "&nbsp;: <del>" . str_replace("<", "&lt;", $oldValue) .
                    "</del> <ins>" . str_replace("<", "&lt;", $parameterData['value']) . "</ins><br>";
          }
        }
      } catch (Exception $e) {
        Log::error($e);
        $error = true;
      }
    }
    // Save the logo
    $logoFile = $request->file('logo');
    try {
      if ($logoFile) {
        $filename = $logoFile->getClientOriginalName();
        $logoFile->move(storage_path() . "/" . Parameter::$LOGO_IMAGE_FOLDER, $filename);
        Parameter::set(Parameter::$LOGO_IMAGE, $filename);
        $changesMade .= "- Remplacement du logo de l'unité<br />";
      }
    } catch (Exception $e) {
      Log::error($e);
      $error = true;
    }
    // Save the icon
    $iconFile = $request->file('icon');
    try {
      if ($iconFile) {
        $filename = $iconFile->getClientOriginalName();
        $iconFile->move(storage_path() . "/" . Parameter::$ICON_IMAGE_FOLDER, $filename);
        Parameter::set(Parameter::$ICON_IMAGE, $filename);
        $changesMade .= "- Remplacement de l'icône du site<br />";
      }
    } catch (Exception $e) {
      Log::error($e);
      $error = true;
    }
    // Save unit e-mail address and google calendar link
    try {
      $unitSection = Section::find(1);
      $unitEmail = $request->input('unit_email_address');
      if ($unitSection->email != $unitEmail) {
        $changesMade .= "- Adresse e-mail de l'unité&nbsp;: <del>" . $unitSection->email . "</del> <ins>$unitEmail</ins><br />";
        $unitSection->email = $unitEmail;
        $unitSection->save();
      }
      $googleCalendarLink = $request->input('unit_google_calendar_link');
      if ($unitSection->google_calendar_link != $googleCalendarLink) {
        $changesMade .= "- Lien Google Agenda&nbsp;: <del>" . $unitSection->google_calendar_link . "</del> <ins>$googleCalendarLink</ins><br />";
        $unitSection->google_calendar_link = $googleCalendarLink;
        $unitSection->save();
      }
    } catch (Exception $e) {
      Log::error($e);
      $error = true;
    }
    // Return to parameter page
    if (!$error) {
      if ($changesMade) {
        LogEntry::log("Paramètres", "Modification des paramètres du site", ["Changements" => $changesMade], true);
      }
      return redirect()->route('edit_parameters')
              ->with('success_message', 'Les paramètres ont été enregistrés avec succès.');
    } else {
      LogEntry::error("Paramètres", "Erreur lors de la modification des paramètres du site", ["Changements" => $changesMade]);
      return redirect()->route('edit_parameters')
              ->with('error_message', 'Une erreur est survenue. Tous les paramètres n\'ont peut-être pas été enregistrés. ' . $errorMessage);
    }
  }
  
  /**
   * Returns the list of pages that can be toggled on/off with paramaters
   */
  private function getPageList() {
    $pages = array(
        'page_sections' => array(
            'description' => 'Afficher les pages des sections',
            'parameter_name' => Parameter::$SHOW_SECTIONS
        ),
        'page_contacts' => array(
            'description' => 'Afficher la page de contacts',
            'parameter_name' => Parameter::$SHOW_CONTACTS
        ),
        'page_addresses' => array(
            'description' => "Afficher l'adresse dans la page de contacts",
            'parameter_name' => Parameter::$SHOW_ADDRESSES
        ),
        'page_links' => array(
            'description' => "Afficher les liens dans la page de contacts",
            'parameter_name' => Parameter::$SHOW_LINKS
        ),
        'page_annual_feast' => array(
            'description' => "Afficher la page de la fête d'unité",
            'parameter_name' => Parameter::$SHOW_ANNUAL_FEAST
        ),
        'page_registration' => array(
            'description' => "Afficher la page \"inscription\"",
            'parameter_name' => Parameter::$SHOW_REGISTRATION
        ),
        'page_absences' => array(
            'description' => "Afficher la page \"absences\"",
            'parameter_name' => Parameter::$SHOW_ABSENCES
        ),
        'page_health_cards' => array(
            'description' => "Afficher la page \"fiches santé\"",
            'parameter_name' => Parameter::$SHOW_HEALTH_CARDS
        ),
        'page_unit_policy' => array(
            'description' => "Afficher la page \"charte d'unité\"",
            'parameter_name' => Parameter::$SHOW_UNIT_POLICY
        ),
        'page_leader_policy' => array(
            'description' => "Afficher la page \"charte des animateurs\"",
            'parameter_name' => Parameter::$SHOW_LEADER_POLICY
        ),
        'page_gdpr' => array(
            'description' => "Afficher la page \"RGPD\"",
            'parameter_name' => Parameter::$SHOW_GDPR
        ),
        'page_uniforms' => array(
            'description' => "Afficher la page \"uniformes\"",
            'parameter_name' => Parameter::$SHOW_UNIFORMS
        ),
        'page_suggestions' => array(
            'description' => "Afficher la page \"suggestions\"",
            'parameter_name' => Parameter::$SHOW_SUGGESTIONS
        ),
        'page_guest_book' => array(
            'description' => "Afficher le livre d'or",
            'parameter_name' => Parameter::$SHOW_GUEST_BOOK
        ),
        'page_help' => array(
            'description' => "Afficher la page \"aide\"",
            'parameter_name' => Parameter::$SHOW_HELP
        ),
        // Section
        'page_news' => array(
            'description' => "Afficher la page \"actualités\"",
            'parameter_name' => Parameter::$SHOW_NEWS
        ),
        'page_calendar' => array(
            'description' => 'Afficher la page "calendrier"',
            'parameter_name' => Parameter::$SHOW_CALENDAR
        ),
        'download_calendar' => array(
            'description' => 'Calendrier téléchargeable en pdf',
            'parameter_name' => Parameter::$CALENDAR_DOWNLOADABLE
        ),
        'legend_in_calendar' => array(
            'description' => "Légende dans le calendrier d'unité",
            'parameter_name' => Parameter::$LEGEND_IN_CALENDAR
        ),
        'page_documents' => array(
            'description' => 'Afficher la page "télécharger"',
            'parameter_name' => Parameter::$SHOW_DOCUMENTS
        ),
        'page_emails' => array(
            'description' => 'Afficher la page "e-mails"',
            'parameter_name' => Parameter::$SHOW_EMAILS
        ),
        'page_photos' => array(
            'description' => "Afficher la page \"photos\"",
            'parameter_name' => Parameter::$SHOW_PHOTOS
        ),
        'page_leaders' => array(
            'description' => "Afficher la page \"animateurs\"",
            'parameter_name' => Parameter::$SHOW_LEADERS
        ),
        'page_daily_photos' => array(
            'description' => "Afficher les photos du jour",
            'parameter_name' => Parameter::$SHOW_DAILY_PHOTOS
        ),
        'page_listing' => array(
            'description' => "Afficher la page \"listing\"",
            'parameter_name' => Parameter::$SHOW_LISTING
        ),
        'page_search' => array(
            'description' => "Afficher l'outil de recherche",
            'parameter_name' => Parameter::$SHOW_SEARCH
        ),
    );
    foreach ($pages as $page=>$pageData) {
      $pageData['active'] = Parameter::get($pageData['parameter_name']);
      $pages[$page] = $pageData;
    }
    return $pages;
  }
  
  /**
   * [Route] Shows the edit CSS page
   */
  function showEditCSS() {
    // Check that the user can edit the parameters
    if (!$this->user->can(Privilege::$EDIT_STYLE, 1)) {
      return Helper::forbiddenResponse();
    }
    // Make view
    $additionalCSS = Parameter::get(Parameter::$ADDITIONAL_CSS);
    $additionalCSSBuffer = Parameter::get(Parameter::$ADDITIONAL_CSS_BUFFER);
    if (!$additionalCSSBuffer) $additionalCSSBuffer = "/* Il n'y a pas de CSS. */";
    return View::make('pages.parameters.editCSS', array(
        'additional_CSS' => $additionalCSSBuffer,
    ));
  }
  
  /**
   * [Route] Updates the CSS. Depending on the action, applies it to the public site or enter test mode.
   */
  function submitCSS(Request $request) {
    // Check that the user can edit the parameters
    if (!$this->user->can(Privilege::$EDIT_STYLE, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get input
    $newCSS = $request->input('newCSS');
    $action = $request->input('action');
    if (!$newCSS) $newCSS = "";
    $cssFile = $request->file('cssFile');
    $error = false;
    try {
      if ($cssFile) {
        $newCSS = file_get_contents($cssFile->getRealPath());
      }
    } catch (Exception $e) {
      Log::error($e);
      $error = true;
    }
    // Save CSS
    Parameter::set(Parameter::$ADDITIONAL_CSS_BUFFER, $newCSS);
    if ($action == 'apply') {
      // Apply to public website
      Parameter::set(Parameter::$ADDITIONAL_CSS, $newCSS);
      // Quit testing mode
      session()->remove('testing-css');
    }
    if ($action == 'test') {
      // Enter testing mode
      session(['testing-css' => true]);
    }
    // Redirect
    if ($error) return redirect()->route('edit_css')->with('error_message', "Une erreur est survenue lors de l'upload du fichier.");
    elseif ($action == 'apply') return redirect()->route('edit_css')->with('success_message', "Le nouveau style a été enregistré et appliqué au site public.");
    else return redirect()->route('edit_css')->with('success_message', "Le nouveau style a été enregistré mais pas appliqué au site public.");
  }
  
  public function exitCSSTestMode() {
    session()->remove('testing-css');
    return redirect()->route('edit_css');
  }
  
}
