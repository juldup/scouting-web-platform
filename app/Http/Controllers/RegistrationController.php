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
 * Future members can register as scouts or leaders through a registration form.
 * The leaders can then validate the registration.
 * 
 * This controller also allows the leaders to manage reregistrations, transfers between
 * sections, members' year in the section and check subscription fee payment.
 */
class RegistrationController extends GenericPageController {
  
  protected function currentPageAdaptToSections() {
    return $this->user->isLeader();
  }
  
  protected function getEditRouteName() {
    return "edit_registration_active_page";
  }
  protected function getShowRouteName() {
    return "registration";
  }
  protected function getPageType() {
    return "registration";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Inscription dans l'unité";
  }
  protected function getPageEditTitle() {
    return "Inscription dans l'unité (lorsque les inscriptions sont activées)";
  }
  protected function getAdditionalEditInformationSubview() {
    return 'subviews.registrationEditInformation';
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_REGISTRATION);
  }
  
  /**
   * [Route] Shows the public registration information page
   */
  public function showMain() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_REGISTRATION)) {
      abort(404);
    }
    // Redirect to inactive registration page if deactivated
    if (!Parameter::registrationIsActive()) {
      return redirect()->route('registration_inactive');
    }
    // Get page text and update it with the parametric values
    $page = $this->getPage();
    $pageBody = $page->body_html;
    $pageBody = str_replace("(PRIX UN ENFANT)", Parameter::get(Parameter::$PRICE_1_CHILD), $pageBody);
    $pageBody = str_replace("(PRIX DEUX ENFANTS)", Parameter::get(Parameter::$PRICE_2_CHILDREN), $pageBody);
    $pageBody = str_replace("(PRIX TROIS ENFANTS)", Parameter::get(Parameter::$PRICE_3_CHILDREN), $pageBody);
    $pageBody = str_replace("(PRIX UN ANIMATEUR)", Parameter::get(Parameter::$PRICE_1_LEADER), $pageBody);
    $pageBody = str_replace("(PRIX DEUX ANIMATEURS)", Parameter::get(Parameter::$PRICE_2_LEADERS), $pageBody);
    $pageBody = str_replace("(PRIX TROIS ANIMATEURS)", Parameter::get(Parameter::$PRICE_3_LEADERS), $pageBody);
    $pageBody = str_replace("BEXX-XXXX-XXXX-XXXX", Parameter::get(Parameter::$UNIT_BANK_ACCOUNT), $pageBody);
    $pageBody = str_replace("(ACCES CHARTE)", '<a href="' . URL::route('unit_policy') . '">charte d&apos;unité</a>', $pageBody);
    $pageBody = str_replace("(ACCES RGPD)", '<a href="' . URL::route('gdpr') . '">RGPD</a>', $pageBody);
    $pageBody = str_replace("(ACCES CONTACT)", '<a href="' . URL::route('contacts') . '">contact</a>', $pageBody);
    $pageBody = str_replace("(ACCES FORMULAIRE)", '<a href="' . URL::route('registration_form') . '">formulaire d&apos;inscription</a>', $pageBody);
    // Get the list of members owned by the user for the reregistration form
    $familyMembers = array();
    if ($this->user->isMember()) {
      $familyMembers = $this->user->getAssociatedMembers();
    }
    // Make view
    return View::make('pages.registration.registrationMain', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, 1),
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section) ||
                        $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section) ||
                        $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'page_title' => $this->getPageTitle(),
        'page_body' => $pageBody,
        'family_members' => $familyMembers,
        'reregistration_year' => date('Y') . "-" . (date('Y') + 1),
    ));
  }
  
  /**
   * [Route] Marks a member as reregistered for the next year
   */
  public function reregister($member_id) {
    // Make sure the user owns this member
    if (!$this->user->isOwnerOfMember($member_id)) {
      return Helper::forbiddenResponse();
    }
    // Get the member
    $member = Member::find($member_id);
    if (!$member) abort(404, "Ce member n'existe pas.");
    // Update reregistration status
    try {
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      LogEntry::log("Inscription", "Réinscription d'un membre", array("Membre" => $member->getFullName()));
      return redirect()->route('registration')->with('success_message', "La réinscription de " . $member->getFullName() . " a été enregistrée.");
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Inscription", "Erreur lors de la réinscription d'un membre", array("Erreur" => $ex->getMessage()));
      return redirect()->route('registration')->with('error_message', "Une erreur est survenue. La réinscription de "
              . $member->getFullName() . " n'a pas été enregistrée. Contactez " . Parameter::adaptAnUDenomination("l'animateur d'unité") . ". $ex");
    }
  }
  
  /**
   * [Route] Displays the registration form page
   */
  public function showForm() {
    // Redirect to inactive registration page if deactivated
    if (!Parameter::registrationIsActive()) {
      // Check if a temporary code exists and is valid
      $temporaryCode = Session::get('temporary_code');
      if (!TemporaryRegistrationLink::codeIsValid($temporaryCode)) {
        return redirect()->route('registration_inactive');
      }
    }
    if (Session::get('registration')) {
      // Get default value from last form filled during this session
      $defaultValues = Session::get('registration');
    } else {
      $members = $this->user->getAssociatedMembers();
      if (count($members)) {
        // Get value from another existing member owned by this user
        $defaultValues = $members[0]->attributesToArray();
        $siblings = "";
        foreach ($members as $member) {
          $siblings .= ($siblings ? ", " : "") . $member->getFullName();
        }
        $defaultValues['registration_siblings'] = $siblings;
      } else {
        // Default values if none of the above apply
        $defaultValues = array(
          'last_name' => '',
          'nationality' => 'BE',
          'address' => '',
          'postcode' => '',
          'city' => '',
          'phone1' => '',
          'phone1_private' => '',
          'phone1_owner' => '',
          'phone2' => '',
          'phone2_private' => '',
          'phone2_owner' => '',
          'phone3' => '',
          'phone3_private' => '',
          'phone3_owner' => '',
          'email1' => '',
          'email2' => '',
          'email3' => '',
          'family_in_other_units' => '0',
          'family_in_other_units_details' => '',
          'registration_siblings' => '',
          'registration_former_leader_child' => '',
        );
      }
    }
    // Make view with default values
    return View::make('pages.registration.registrationForm', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'default' => $defaultValues,
        'can_edit'  => $this->user->can(Privilege::$EDIT_PAGES, 1),
    ));
  }
  
  /**
   * [Route] Called when the registration form is submitted
   */
  public function submit(Request $request) {
    // Get whether the policy agreement and GDPR have been accepted
    $policyAgreement = $request->input('policy_agreement') ? true : false;
    $gdprAgreement = $request->input('gdpr_agreement') ? true : false;
    if ((Parameter::get(Parameter::$SHOW_UNIT_POLICY) && !$policyAgreement) ||
        (Parameter::get(Parameter::$SHOW_GDPR) && !$gdprAgreement)) {
      // The policy agreement has not been accepted, set error message
      $success = false;
      $message = "";
      if (Parameter::get(Parameter::$SHOW_UNIT_POLICY) && !$policyAgreement)
        $message .= "Vous devez adhérer à la charte d'unité pour inscrire un enfant.";
      if (Parameter::get(Parameter::$SHOW_GDPR) && !$gdprAgreement)
        $message .= ($message ? " " : "") . "Vous devez accepter le RGPD.";
    } else {
      // The policy agreement and GDPR have been accepted, create a new member instance from input
      $result = Member::createFromInput($request, false);
      if (is_string($result)) {
        // An error has occured
        $success = false;
        $message = $result;
        if (!$message) $message = "Une erreur est survenue. Votre inscription n'a pas été enregistrée. " .
                "Veuillez réessayer ou <a href='" . URL::route('contacts') . 
                "'>contacter " . Parameter::adaptAnUDenomination("l'animateur d'unité") . "</a>.";
      } else {
        // Success
        $success = true;
        $message = "Votre inscription a été enregistrée. " . Parameter::adaptAnUDenomination("L'animateur d'unité") . " la validera prochainement.";
        // Save values in session for re-use for another registration
        Session::put('registration.last_name', $request->input('last_name'));
        Session::put('registration.nationality', $request->input('nationality'));
        Session::put('registration.address', $request->input('address'));
        Session::put('registration.postcode', $request->input('postcode'));
        Session::put('registration.city', $request->input('city'));
        Session::put('registration.phone1', $request->input('phone1'));
        Session::put('registration.phone1_private', $request->input('phone1_private'));
        Session::put('registration.phone1_owner', $request->input('phone1_owner'));
        Session::put('registration.phone2', $request->input('phone2'));
        Session::put('registration.phone2_private', $request->input('phone2_private'));
        Session::put('registration.phone2_owner', $request->input('phone2_owner'));
        Session::put('registration.phone3', $request->input('phone3'));
        Session::put('registration.phone3_private', $request->input('phone3_private'));
        Session::put('registration.phone3_owner', $request->input('phone3_owner'));
        Session::put('registration.email1', $request->input('email1'));
        Session::put('registration.email2', $request->input('email2'));
        Session::put('registration.email3', $request->input('email3'));
        Session::put('registration.family_in_other_units', $request->input('family_in_other_units'));
        Session::put('registration.family_in_other_units_details', $request->input('family_in_other_units_details'));
        Session::put('registration.registration_siblings', $request->input('registration_siblings'));
        Session::put('registration.registration_former_leader_child', $request->input('registration_former_leader_child'));
      }
    }
    // Send confirmation e-mail
    if ($success) {
      $member = $result;
      // E-mail to parents/member
      if ($member->is_leader) {
        $emailAddresses = $member->email_member ? array($member->email_member) : array();
      } else {
        $emailAddresses = $member->getParentsEmailAddresses();
      }
      
      foreach ($emailAddresses as $recipient) {
        $emailContent = Helper::renderEmail('registrationConfirmation', $recipient, array(
            'member' => $member,
            'to_leaders' => false,
            'custom_content' => Parameter::get(Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_FORM_FILLED),
        ));
        $email = PendingEmail::create(array(
            'subject' => "Demande d'inscription de " . $member->getFullName(),
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
            'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
            'recipient' => $recipient,
            'priority' => PendingEmail::$ACCOUNT_EMAIL_PRIORITY,
        ));
        // Don't send e-mail right away
      }
      // E-mail to unit's leader(s) that are allowed to register
      $leader_email_addresses = self::getLeadersWithRegistrationPrivilege($member->section);
      if (Parameter::get(Parameter::$SEND_REGISTRATIONS_TO_UNIT_EMAIL_ADDRESS)) {
        $unit_email_address = Section::find(1)->email;
        if ($unit_email_address) $leader_email_addresses[] = $unit_email_address;
      }
      foreach ($leader_email_addresses as $recipient) {
        $emailContent = Helper::renderEmail('registrationConfirmation', $recipient, array(
            'member' => $member,
            'to_leaders' => true,
        ));
        $email = PendingEmail::create(array(
            'subject' => "Demande d'inscription de " . $member->getFullName() . " dans la section " . $member->getSection()->name,
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
            'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
            'recipient' => $recipient,
            'priority' => PendingEmail::$ACCOUNT_EMAIL_PRIORITY,
        ));
        // Don't send right away, there is no rush
      }
    }
    // Redirect with status message
    if ($success) {
      LogEntry::log("Inscription", "Nouvelle demande d'inscription", array("Nom" => $request->input('first_name') . " " . $request->input('last_name'))); // TODO improve log message
      return redirect(URL::route('registration_form'))
            ->with('success_message', $message);
    } else {
      return redirect(URL::route('registration_form'))
            ->with('error_message', $message)
            ->withInput();
    }
  }
  
  /**
   * Shows the page to edit the form text
   */
  public function editForm() {
    // Make sure the use can access this page
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get existing form texts
    $data = array(
        'introduction' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_INTRODUCTION),
        'fill-in-form' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FILL_IN_FORM),
        'identity' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_IDENTITY),
        'first_name' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FIRST_NAME),
        'last_name' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_LAST_NAME),
        'birth_date' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_BIRTH_DATE),
        'gender' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_GENDER),
        'nationality' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_NATIONALITY),
        'address' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_ADDRESS),
        'address_street' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_ADDRESS_STREET),
        'postcode' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_POSTCODE),
        'city' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_CITY),
        'contact' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_CONTACT),
        'phone' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_PHONE), 
        'phone_member' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_PHONE_MEMBER),
        'email' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_EMAIL),
        'email_member' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_EMAIL_MEMBER),
        'section_header' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_SECTION_HEADER),
        'section' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_SECTION),
        'totem' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_TOTEM),
        'quali' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_QUALI),
        'leader' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_LEADER),
        'remarks' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_REMARKS),
        'handicap' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_HANDICAP),
        'comments' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_COMMENTS),
        'family' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FAMILY),
        'finish' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FINISH),
        'registration_siblings' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_SIBLINGS), 
        'registration_former_leader_child' => Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FORMER_LEADER_CHILD),
    );
    // Make view
    return View::make('pages.registration.editForm', array(
        'data' => $data,
    ));
  }
  
  /**
   * Saves the form help texts and redirects to the form
   */
  public function saveForm(Request $request) {
    // Make sure the use can edit the form help texts
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_INTRODUCTION, $request->input('introduction'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_FILL_IN_FORM, $request->input('fill-in-form'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_IDENTITY, $request->input('identity'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_FIRST_NAME, $request->input('first_name'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_LAST_NAME, $request->input('last_name'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_BIRTH_DATE, $request->input('birth_date'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_GENDER, $request->input('gender'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_NATIONALITY, $request->input('nationality'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_ADDRESS, $request->input('address'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_ADDRESS_STREET, $request->input('address_street'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_POSTCODE, $request->input('postcode'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_CITY, $request->input('city'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_CONTACT, $request->input('contact'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_PHONE, $request->input('phone'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_PHONE_MEMBER, $request->input('phone_member'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_EMAIL, $request->input('email'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_EMAIL_MEMBER, $request->input('email_member'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_SECTION_HEADER, $request->input('section_header'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_SECTION, $request->input('section'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_TOTEM, $request->input('totem'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_QUALI, $request->input('quali'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_LEADER, $request->input('leader'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_REMARKS, $request->input('remarks'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_HANDICAP, $request->input('handicap'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_COMMENTS, $request->input('comments'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_FAMILY, $request->input('family'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_FINISH, $request->input('finish'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_SIBLINGS, $request->input('registration_siblings'));
    Parameter::set(Parameter::$REGISTRATION_FORM_HELP_FORMER_LEADER_CHILD, $request->input('registration_former_leader_child'));
    return redirect()->route('registration_form');
  }
  
  /**
   * Returns an array containing the e-mail addresses of all the unit's leader in charge.
   * In general there, will be only one.
   */
  private static function getLeadersWithRegistrationPrivilege($section) {
    $leaders = Member::where('is_leader', '=', true)
            ->where('validated', '=', true)
            ->get();
    $emailAddresses = array();
    foreach ($leaders as $leader) {
      if (($leader->leader_in_charge && $leader->section_id == 1) || $leader->can(Privilege::$EDIT_LISTING_ALL, $section)) {
        $emailAddresses[] = $leader->email_member;
      }
    }
    return $emailAddresses;
  }
  
  /**
   * [Route] Shows the registration management page, where the leaders
   * can validate or cancel pending registrations
   */
  public function manageRegistration() {
    // Check that the user is allowed to reach this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return redirect()->route('manage_reregistration');
    }
    if (!Parameter::get(Parameter::$ADVANCED_REGISTRATIONS)) { // Normal registrations
      // Gather pending registrations
      $pendingRegistrations = Member::where('validated', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->orderBy('in_waiting_list')
              ->get();
      // List other sections that contain pending registrations
      $otherSection = Section::where('id', '!=', $this->section->id)
              ->whereExists(function($query) {
                $query->select(DB::raw(1))
                        ->from('members')
                        ->where('validated', '=', false)
                        ->whereRaw('members.section_id = sections.id');
              })->get();
      // Render view
      return View::make('pages.registration.manageRegistration', array(
          'registrations' => $pendingRegistrations,
          'other_sections' => $otherSection,
          'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
          'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
          'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
          'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
          'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
      ));
    } else { // Advanced registrations
      // Gather pending registrations
      $sectionCategories = $this->getAdvancedRegistrationSortedByCategory();
      // Render view
      return View::make('pages.registration.manageAdvancedRegistration', array(
          'registrations' => $sectionCategories,
          'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
          'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
          'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
          'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
          'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
      ));
    }
  }
  
  private function getAdvancedRegistrationSortedByCategory() {
    $pendingRegistrations = Member::where('validated', '=', false)
            ->orderBy('section_id', 'ASC')
            ->orderBy('registration_section_category', 'ASC')
            ->orderBy('year_in_section', 'ASC')
            ->get();
    $sectionCategories = [];
    foreach ($pendingRegistrations as $registration) {
      $category = $registration->registration_section_category;
      if ($category && array_key_exists($category, Section::$CATEGORIES)) {
        $category = Section::$CATEGORIES[$category]['name'];
      }
      if (!$category) $category = $registration->getSection()->name;
      $category .= " (année " . $registration->year_in_section . ")";
      if (!array_key_exists($category, $sectionCategories)) {
        $sectionCategories[$category] = [];
      }
      $sectionCategories[$category][] = $registration;
    }
    $referenceCity = Parameter::get(Parameter::$REGISTRATION_PRIORITY_CITY);
    if ($referenceCity) $referenceCity = Helper::slugify($referenceCity);
    foreach ($sectionCategories as $category => $pendingRegistrationArray) {
      usort($pendingRegistrationArray, function($a, $b) use ($referenceCity) {
        // Is leader
        if ($a->is_leader && !$b->is_leader) return -1;
        if ($b->is_leader && !$a->is_leader) return 1;
        // Is registered in priority timespan
        if ($a->registration_priority && !$b->registration_priority) return -1;
        if ($b->registration_priority && !$a->registration_priority) return 1;
        if ($a->registration_priority /*&& $a->year_in_section == 1*/) { // and therefore also $bPrioritary and $b->year_in_section == 1
          // Siblings
          if (trim($a->registration_siblings) && !trim($b->registration_siblings)) return -1;
          if (trim($b->registration_siblings) && !trim($a->registration_siblings)) return 1;
          // City
          $aInCity = false;
          $bInCity = false;
          if ($referenceCity) {
            $aInCity = strpos(Helper::slugify($a->city), $referenceCity) !== false;
            $bInCity = strpos(Helper::slugify($b->city), $referenceCity) !== false;
          }
          // Former leader child
          $aIsFormerLeaderChild = trim($a->registration_former_leader_child);
          $bIsFormerLeaderChild = trim($b->registration_former_leader_child);
          // Compare city and former leader child
          if (($aInCity || $aIsFormerLeaderChild) && !($bInCity || $bIsFormerLeaderChild)) return -1;
          if (($bInCity || $bIsFormerLeaderChild) && !($aInCity || $aIsFormerLeaderChild)) return 1;
        }
        // Date
        return strcmp($a->registration_date, $b->registration_date);
      });
      $femaleCounter = 1;
      $maleCounter = 1;
      foreach ($pendingRegistrationArray as $registration) {
        if ($registration->gender == "M") {
          $registration->gender_order = "" . ($maleCounter++);
        } else {
          $registration->gender_order = "" . ($femaleCounter++);
        }
      }
      $sectionCategories[$category] = $pendingRegistrationArray;
    }
    return $sectionCategories;
  }
  
  /**
   * [Route] Deletes a pending registration
   */
  public function deleteRegistration($member_id) {
    // Get the pending registration to delete
    $member = Member::find($member_id);
    $sectionId = $member ? $member->section_id : null;
    if ($sectionId) {
      // Make sure the leader can cancel registrations
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Remove pending registration
      try {
        $member->delete();
        LogEntry::log("Inscription", "Suppression d'une demande d'inscription", array("Nom" => $member->getFullName()));
        return redirect()->route('manage_registration')
                ->with("success_message", "La demande d'inscription de " . $member->getFullName() . " a été supprimée.");
      } catch (Exception $ex) {
        LogEntry::error("Inscription", "Erreur lors de la suppression d'une demande d'inscription", array("Erreur" => $ex->getMessage()));
        Log::error($ex);
      }
    }
    // An error has occured
    return redirect()->route('manage_registration')
                ->with("error_message", "Une erreur est survenue. La demande d'inscription n'a pas été supprimée. $memberId");
  }
  
  /**
   * [Route] Used to submit a member data and validate their registration
   */
  public function manageSubmit(Request $request) {
    // Get input data
    $sectionId = $request->input('section_id');
    $memberId = $request->input('member_id');
    // Find member
    $member = Member::find($memberId);
    if ($member) {
      // Make sure the user is allowed to change member data
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id) ||
              !$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Update member with input data
      $result = $member->updateFromInput($request, true, true, true, true, true, true);
      // Create result message
      if ($result === true) {
        $success = true;
        $name = $member->getFullName();
        if ($member->is_leader) {
          $message = "$name est à présent inscrit en tant qu'animateur.";
        } else {
          $message = "$name est à présent inscrit.";
        }
        LogEntry::log("Inscription", "Validation d'une demande d'inscription", array("Membre" => $member->getFullName())); // TODO improve log message
        // Send confirmation e-mail (if the automatic e-mail content is not empty)
        if (trim(Parameter::get(Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_VALIDATED))) {
          if ($member->is_leader) {
            $emailAddresses = $member->email_member ? array($member->email_member) : array();
          } else {
            $emailAddresses = $member->getParentsEmailAddresses();
          }
          foreach ($emailAddresses as $recipient) {
            $emailContent = Helper::renderEmail('registrationValidated', $recipient, array(
                'member' => $member,
                'custom_content' => str_replace("((NOM))", $member->getFullName(), Parameter::get(Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_VALIDATED)),
            ));
            $email = PendingEmail::create(array(
                'subject' => "Confirmation de l'inscription de " . $member->getFullName(),
                'raw_body' => $emailContent['txt'],
                'html_body' => $emailContent['html'],
                'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
                'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
                'recipient' => $recipient,
                'priority' => PendingEmail::$ACCOUNT_EMAIL_PRIORITY,
            ));
          }
        }
      } else {
        $success = false;
        $message = $result ? $result : "Une erreur est survenue. Le nouveau membre n'a pas été inscrit.";
      }
    } else {
      // Member not found
      $success = false;
      $message = "Une erreur est survenue. Le nouveau membre n'a pas été inscrit.";
    }
    // Redirect to page with result message
    if ($success)
      return redirect(URL::route('manage_registration', array('section_slug' => $this->section->slug)))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return redirect(URL::previous())->with($success ? 'success_message' : 'error_message', $message)->withInput();
  }
  
  /**
   * [Route] Shows the reregistration management page
   */
  public function manageReregistration() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return redirect()->route('manage_year_in_section');
    }
    // List scouts
    $query = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->orderBy('last_name')
            ->orderBy('first_name');
    if ($this->section->id != 1) {
      $query->where('section_id', '=', $this->section->id);
    }
    $activeMembers = $query->get();
    // Render view
    return View::make('pages.registration.manageReregistration', array(
        'active_members' => $activeMembers,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Ajax call to mark a member as reregistered for the next year
   */
  public function ajaxReregister(Request $request) {
    // Find member
    $memberId = $request->input('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    // Check privileges
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour réinscrire un scout."));
    }
    // Update last reregistration year
    try {
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      LogEntry::log("Inscription", "Réinscription d'un membre", array("Membre" => $member->getFullName()));
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Inscription", "Erreur lors de la réinscription d'un membre", array("Erreur" => $ex->getMessage()));
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  /**
   * [Route] Ajax call to cancel a reregistration
   */
  public function ajaxCancelReregistration(Request $request) {
    // Find member
    $memberId = $request->input('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    // Check privileges
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour annuler la réinscription d'un scout."));
    }
    // Cancel last reregistration year
    try {
      $member->last_reregistration = null;
      $member->save();
      LogEntry::log("Inscription", "Annulation de la réinscription d'un membre", array("Membre" => $member->getFullName()));
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Inscription", "Erreur lors de l'annulation de la réinscription d'un membre", array("Erreur" => $ex->getMessage()));
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  /**
   * [Route] Ajax call to delete a member from the reregistration page
   */
  public function ajaxDeleteMember(Request $request) {
    // Find member
    $memberId = $request->input('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    // Check privilege
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour désinscrire un scout."));
    }
    // Delete member
    try {
      $member->delete();
      LogEntry::log("Inscription", "Suppression d'un membre", array("Membre" => $member->getFullName()));
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Inscription", "Erreur lors de la suppression d'un membre", array("Erreur" => $ex->getMessage()));
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  /**
   * [Route] Ajax call to add/remove a member to/from the waiting list
   */
  public function ajaxToggleWaitingList(Request $request) {
    // Find member
    $memberId = $request->input('member_id');
    $member = Member::find($memberId);
    if (!$member) {
      return json_encode(array("result" => "Failure", "message" => "Cette demande d'inscription n'existe pas."));
    }
    // Make sure the user is allowed to modify the waiting list
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour modifier la liste d'attente."));
    }
    // Update waiting list
    try {
      $inWaitingList = $request->input('in_waiting_list') ? true : false;
      $member->in_waiting_list = $inWaitingList;
      $member->save();
      LogEntry::log("Inscription", "Changement de la liste d'attente", array("Membre" => $member->getFullName(), "Liste d'attente" => ($inWaitingList ? "Oui" : "Non")));
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Inscription", "Erreur lors du changement de la liste d'attente", array("Erreur" => $ex->getMessage()));
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  /**
   * [Route] Shows the year in section management page
   */
  public function manageYearInSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)) {
      return redirect()->route('manage_member_section');
    }
    // List scouts
    $activeMembers = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->orderBy('year_in_section', 'DESC')
            ->orderBy('birth_date')
            ->where('section_id', '=', $this->section->id)
            ->get();
    // Render view
    return View::make('pages.registration.manageYearInSection', array(
        'active_members' => $activeMembers,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Ajax call to change the year of a single member or of all members of a section
   * @return type
   */
  public function ajaxUpdateYearInSection(Request $request) {
    if ($request->has('section_id')) {
      $sectionId = $request->input('section_id');
      // Make sure the user is allowed to access this page
      if (!$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $sectionId)) {
        return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour changer l'année d'un scout."));
      }
      try {
        // Update year for each member of the section
        Member::where('validated', '=', true)
                ->where('is_leader', '=', false)
                ->where('section_id', '=', $sectionId)
                ->increment('year_in_section');
        // Retrieve members
        $members = Member::where('validated', '=', true)
                ->where('is_leader', '=', false)
                ->where('section_id', '=', $sectionId)
                ->get();
        $memberYears = array();
        foreach ($members as $member) {
          $memberYears[$member->id] = $member->year_in_section;
        }
        LogEntry::log("Inscription", "Augmentation de l'année des membres de la section");
        return json_encode(array("result" => "Success", 'years' => $memberYears));
      } catch (Exception $ex) {
        Log::error($ex);
        LogEntry::error("Inscription", "Erreur lors du changement de l'année dans la section", array("Erreur" => $ex->getMessage()));
        return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
      }
    } else {
      // Find member
      $memberId = $request->input('member_id');
      $member = Member::find($memberId);
      if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
      // Get year in section
      $yearInSection = $request->input('year') + 0;
      if ($yearInSection <= 0) {
        return json_encode(array("result" => "Failure", "message" => "L'année doit être un nombre positif."));
      }
      // Make sure the user is allowed to access this page
      if (!$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $member->section_id)) {
        return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour changer l'année d'un scout."));
      }
      // Update year in section
      try {
        $member->year_in_section = $yearInSection;
        $member->save();
        LogEntry::log("Inscription", "Changement de l'année dans la section", array("Membre" => $member->getFullName(), "Année" => $yearInSection)); // TODO improve log message
        return json_encode(array('result' => 'Success'));
      } catch (Exception $ex) {
        Log::error($ex);
        LogEntry::error("Inscription", "Erreur lors du changement de l'année dans la section", array("Erreur" => $ex->getMessage()));
        return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
      }
    }
  }
  
  /**
   * [Route] Shows the section transfer page
   */
  public function manageMemberSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
      return redirect()->route('manage_subscription_fee');
    }
    // List scouts
    $activeMembers = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->orderBy('year_in_section', 'DESC')
            ->orderBy('birth_date')
            ->where('section_id', '=', $this->section->id)
            ->get();
    // Render view
    return View::make('pages.registration.manageMemberSection', array(
        'active_members' => $activeMembers,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Used to submit the transfers from a section to another
   */
  public function submitUpdateSection(Request $request, $section_slug) {
    // Get sections from and to
    $sectionFrom = Section::where('slug', '=', $section_slug)->first();
    $sectionTo = Section::find($request->input('destination'));
    // Get list of members to transfer
    $memberIdsToTransfer = $request->input('members');
    // Make sure these parameters are all set correctly
    if (!$sectionFrom || !$sectionTo || !is_array($memberIdsToTransfer) || !count($memberIdsToTransfer)) {
      return redirect()->route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Une erreur est survenue. Les changements n'ont pas été enregistrés");
    }
    // Make sure the user can operate transfers between sections
    if (!$this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
      return Helper::forbiddenResponse();
    }
    // Transfer each member and reset their year in the section and their subgroup and role
    $errorList = "";
    $success = false;
    $transferedMembers = "";
    foreach ($memberIdsToTransfer as $memberId=>$val) {
      $member = Member::find($memberId);
      if ($member && $member->section_id == $sectionFrom->id) {
        try {
          $member->section_id = $sectionTo->id;
          $member->year_in_section = 1;
          $member->subgroup = null;
          $member->role = null;
          $member->save();
          $success = true;
          $transferedMembers .= ($transferedMembers ? ", " : "") . $member->getFullName();
        } catch (Exception $ex) {
          Log::error($ex);
          $errorList .= ($errorList ? ", " : "") . $member->getFullName();
        }
      } else {
        $errorList .= ($errorList ? ", " : "") . $member->getFullName();
      }
    }
    // Redirect with status message
    if (!$success) {
      LogEntry::error("Inscription", "Erreur lors du transfert de membres entre des sections",
              array("Depuis" => $sectionFrom->name, "Vers" => $sectionTo->name, "Erreurs" => $errorList));
      return redirect()->route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Une erreur s'est produite. Les changements n'ont pas été enregistrés.");
    } elseif ($errorList) {
      LogEntry::error("Inscription", "Erreur lors du transfert de membres entre des sections",
              array("Depuis" => $sectionFrom->name, "Vers" => $sectionTo->name, "Membres transférés" => $transferedMembers, "Erreurs" => $errorList));
      return redirect()->route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Le transfert a été opéré, sauf pour : $errorList");
    } else {
      LogEntry::log("Inscription", "Transfert de membres entre des sections",
              array("Depuis" => $sectionFrom->name, "Vers" => $sectionTo->name, "Membres transférés" => $transferedMembers));
      return redirect()->route('manage_member_section', array('section_slug' => $section_slug))
              ->with('success_message', "Le transfert a été opéré avec succès.");
    }
  }
  
  /**
   * [Route] Shows the subscription fee management page, where the leaders
   * can check members that have paid their fee
   */
  public function manageSubscriptionFee() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$MANAGE_ACCOUNTING, 1)) {
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)
              || $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
              || $this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
        return redirect()->route('manage_registration');
      }
      return Helper::forbiddenResponse();
    }
    // List scouts
    $members = Member::where('validated', '=', true)
            ->where('is_guest', '=', false)
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();
    // Render view
    return View::make('pages.registration.manageSubscriptionFee', array(
        'members' => $members,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Ajax call to toggle a list of fee payment status
   */
  public function updateSubscriptionFee(Request $request) {
    // Make sure the user is allowed to change the fee payment status
    if (!$this->user->can(Privilege::$MANAGE_ACCOUNTING, 1)) {
      return json_encode(array('result' => 'Failure'));
    }
    // Get list of changes
    $changes = $request->all();
    // Apply changes
    $error = false;
    $message = "";
    $members = "";
    foreach ($changes as $memberId => $state) {
      // Get member and payment status
      $memberId = substr($memberId, strlen('member-'));
      $state = $state != "false" && $state;
      $member = Member::find($memberId);
      // Update status
      if ($member) {
        try {
          $member->subscription_paid = $state;
          $member->save();
          $members .= ($members ? ", " : "") . $member->getFullName();
        } catch (Exception $e) {
          Log::error($e);
          $error = true;
          $message .= "$e ";
        }
      } else {
        $error = true;
        $message .= "Member $memberId does not exist. ";
      }
    }
    LogEntry::log("Inscription", "Mise à jour du statut de paiement de cotisation", array("Membres" => $members)); // TODO improve log message
    // Redirect with status message
    return json_encode(array('result' => $error ? "Failure" : "Success", 'message' => $message));
  }
  
  /**
   * [Route] Sets all subscription fee status to on/off
   */
  public function setAllSubscriptionFees($status) {
    // Make sure the user is allowed to change the fee payment status
    if (!$this->user->can(Privilege::$MANAGE_ACCOUNTING, 1)) {
      return json_encode(array('result' => 'Failure'));
    }
    // Get status
    $newValue = $status == "true" ? 1 : 0;
    // Update status for all members
    try {
      Member::where('validated', '=', true)
            ->where('is_guest', '=', false)
            ->update(['subscription_paid' => $newValue]);
      LogEntry::log("Inscription", "Mise à jour du statut de paiement de cotisation de tous les membres : " . ($newValue ? "Payé" : "Non payé"));
    } catch (Exception $e) {
      Log::error($e);
      $error = true;
      $message .= "$e ";
      return redirect()->route('manage_subscription_fee', array('section_slug' => $this->section->slug))
                ->with('error_message', "Une erreur est survenue.");
    }
    // Redirect with status message
    return redirect()->route('manage_subscription_fee', array('section_slug' => $this->section->slug))
              ->with('success_message', "La modification a été effectuée.");
  }
    
  /**
   * [Route] Updates the priority fields of a registration record
   */
  public function submitPriority(Request $request) {
    // Make sure the user is allowed to manage registrations
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    $memberId = $request->input('member_id');
    $member = Member::find($memberId);
    if ($member) {
      $member->is_leader = $request->input('registration_is_leader') ? 1 : 0;
      $member->registration_siblings = $request->input('registration_siblings');
      $member->city = $request->input('registration_city');
      $member->registration_former_leader_child = $request->input('registration_former_leader_child');
      $member->year_in_section = intval($request->input('year_in_section'));
      $member->registration_priority = $request->input('registration_priority') ? 1 : 0;
      $member->registration_status = $request->input('registration_status');
      if ($request->input('section')) {
        $member->section_id = $request->input('section');
        $member->registration_section_category = null;
      } elseif ($request->input('section_category')) {
        $member->registration_section_category = $request->input('section_category');
        $member->section_id = 1;
      }
      // Check date
      if (DateHelper::verifyMysqlDatetime($request->input('registration_date'))) {
        $member->registration_date = $request->input('registration_date');
        $wrongDate = false;
      } else {
        $wrongDate = true;
      }
      $member->save();
      if ($wrongDate) {
        return redirect()->route('manage_registration', array('section_slug' => $this->section->slug))
                ->with('error_message', "Le format de la date d'inscription doit être AAAA-MM-JJ hh:mm:ss (ex. : 2020-08-18 17:07:30). Les autres données ont été enregistrées.");
      } else {
        return redirect()->route('manage_registration', array('section_slug' => $this->section->slug))
                ->with('success_message', "Modifications enregistrées.");
      }
    }
    return redirect()->route('manage_registration', array('section_slug' => $this->section->slug))
              ->with('error_message', "Une erreur est survenue.");
  }
  
  /**
   * [Route] Resets the years in section values in all registrations
   */
  public function recomputeYearsInSection() {
    // Make sure the user is allowed to manage registrations
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    $registrations = Member::where('validated', '=', 0)->get();
    foreach ($registrations as $registration) {
      $registration->year_in_section = $registration->calculateYearInSection();
      $registration->save();
    }
    return redirect()->route('manage_registration', array('section_slug' => $this->section->slug))
              ->with('success_message', "Les années dans les sections ont été recalculées.");
  }
  
  /**
   * [Route] Downloads the registration list in CSV format
   */
  public function downloadRegistrationList() {
    // Make sure the user is allowed to manage registrations
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    $fields = [
        "registration_section_category" => "Type de section",
        "year_in_section" => "Année dans la section",
        "registration_status" => "Status de pré-inscription",
        "registration_priority" => "Inscription prioritaire",
        "last_name" => "Nom",
        "first_name" => "Prénom",
        "birth_date" => "Date de naissance",
        "gender" => "Sexe",
        "gender_order" => "Numéro par sexe",
        "is_leader" => "Animateur",
        "registration_date" => "Date d'inscription",
        "registration_siblings" => "Frères et sœurs",
        "registration_former_leader_child" => "Enfant d'ancien animateur",
        "nationality" => "Nationalité",
        "has_handicap" => "Handicap",
        "handicap_details" => "Détails du handicap",
        "totem" => "Totem",
        "quali" => "Quali",
        "family_in_other_units" => "Famille dans d'autres unités",
        "family_in_other_units_details" => "Détails autres unités",
        "comments" => "Commentaires",
        "address" => "Adresse",
        "postcode" => "Code postal",
        "city" => "Localité",
        "phone1" => "Téléphone parents 1",
        "phone1_owner" => "Propriétaire téléphone 1",
        "phone1_private" => "Téléphone 1 confidentiel",
        "phone2" => "Téléphone parents 2",
        "phone2_owner" => "Propriétaire téléphone 2",
        "phone2_private" => "Téléphone 2 confidentiel",
        "phone3" => "Téléphone parents 3",
        "phone3_owner" => "Propriétaire téléphone 3",
        "phone3_private" => "Téléphone 3 confidentiel",
        "phone_member" => "Téléphone du scout",
        "phone_member_private" => "Téléphone scout confidentiel",
        "email1" => "E-mail parents 1",
        "email2" => "E-mail parents 2",
        "email3" => "E-mail parents 3",
        "email_member" => "E-mail du scout",
        
    ];
    $firstField = "registration_section_category";
    //$registrations = Member::where('validated', '=', 0)->get();
    $sectionCategories = $this->getAdvancedRegistrationSortedByCategory();
    $output = "";
    foreach ($fields as $field => $fieldName) {
      $output .= ($field != $firstField ?  "," : "") . "\"$fieldName\"";
    }
    $output .= "\n";
    foreach ($sectionCategories as $registrations) {
      foreach ($registrations as $registration) {
        foreach ($fields as $field => $fieldName) {
          $output .= ($field != $firstField ?  "," : "") . "\"" . str_replace("\"", "\"\"", preg_replace( "/\r|\n/", " ", $registration->$field)) . "\"";
        }
        $output .= "\n";
      }
    }
    
    return response()->stream(
            function() use ($output) {echo $output;}, 200, [
        'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
        'Content-Disposition'   => 'attachment; filename="Inscriptions.csv"',
    ]);
  }
  
  /**
   * [Route] Displays the registration form page using a special access code
   */
  public function showFormWithTemporaryLink($code) {
    // If registration is active, go directly to registration form
    if (Parameter::registrationIsActive()) {
      return $this->showForm();
    }
    // Get and check temporary link
    $link = TemporaryRegistrationLink::where('code', '=', $code)->first();
    if ($link) {
      $date = $link->expiration;
      if (date('Y-m-d H:i:s') > $date) {
        // Temporary link has expired
        return redirect()->route('registration_inactive')
                ->with('error_message', "Ce lien a expiré.");
      }
      // Temporary link is valid, save it to session and show the registration form
      Session::put('temporary_code', $code);
      return $this->showForm();
    } else {
      // Temporary link does not exist
      return redirect()->route('registration_inactive')
              ->with('error_message', "Ce lien n'existe pas.");
    }
  }
  
  /**
   * [Route] Displays the page that allows to create a temporary registration link
   */
  public function createTemporaryRegistrationLink(Request $request, $code = "") {
    // Make sure the user is allowed to manage registrations
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    // Check if a form is being submitted
    $days = intval($request->input('days'));
    if ($days) {
      // Create temporary link
      $link = TemporaryRegistrationLink::createWithDays($days);
      // Redirect to result page
      return redirect()->route('create_temporary_registration_link')
              ->with('code', $link->code)
              ->with('days', $days)
              ->with('success_message', "Un nouveau lien d'inscription temporaire a été créé.");
    }
    // Render view
    return View::make('pages.registration.createTemporaryRegistrationLink', array(
        'status' => 'form',
    ));
  }
  
  /*
   * [Route] Shows the page with the form to send an e-mail to a selection of registrants
   */
  public function showAdvancedRegistrationEmailPage() {
    // Make sure the user is allowed to manage registrations
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    // Gather pending registrations
    $sectionCategories = $this->getAdvancedRegistrationSortedByCategory();
    $maxOrderF = 0;
    $maxOrderM = 0;
    foreach ($sectionCategories as $category => $registrationList) {
      foreach ($registrationList as $member) {
        if ($member->gender == "F" && $member->gender_order > $maxOrderF) {
          $maxOrderF = $member->gender_order;
        } elseif ($member->gender == "M" && $member->gender_order > $maxOrderM) {
          $maxOrderM = $member->gender_order;
        }
      }
    }
    // Show view
    return View::make('pages.registration.manageAdvancedRegistrationEmail', array(
          'registrations' => $sectionCategories,
          'max_order_f' => $maxOrderF,
          'max_order_m' => $maxOrderM,
          'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
          'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
          'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
          'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
          'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
        ));
  }
}
