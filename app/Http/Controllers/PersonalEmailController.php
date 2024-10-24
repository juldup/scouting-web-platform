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
 * This tools presents a page for visitors and members to contact other members by e-mail
 * without the recipient's e-mail address being revealed.
 * Only members can contact non-leader members. Leaders can be contacted by any visitor.
 */
class PersonalEmailController extends BaseController {
  
  // The kinds of contactable persons
  public static $CONTACT_TYPE_PARENTS = "parents";
  public static $CONTACT_TYPE_PERSONAL = "personnel";
  public static $CONTACT_TYPE_ARCHIVED_LEADER = "archive-animateur";
  public static $CONTACT_TYPE_WEBMASTER = "webmaster";
  public static $CONTACT_TYPE_SECTION = "section";
  
  /**
   * [Route] Shows a page with a form to send an e-mail to a specific person
   * 
   * @param string $contact_type  The kind of contact (see above)
   * @param string $member_id  The id of the member/section to contact (or anything for the webmaster)
   */
  public function sendEmail($contact_type, $member_id) {
    if (URL::previous() != URL::current()) {
      // Record referrer url
      Session::put('personal_email_referrer', URL::previous());
    }
    $member = null;
    $section = null;
    if ($contact_type == self::$CONTACT_TYPE_WEBMASTER) {
      // Nothing to do
    } elseif ($contact_type == self::$CONTACT_TYPE_SECTION) {
      $section = Section::find($member_id);
      if (!$section) abort(404, "Impossible d'envoyer un message : cette section n'existe pas.");
      if (!$section->email) abort(404, "Impossible d'envoyer un message à " . $section->la_section . " car son adresse e-mail est inconnue.");
    } else {
      // Get recipient member
      if ($contact_type == self::$CONTACT_TYPE_ARCHIVED_LEADER) {
        $member = ArchivedLeader::find($member_id);
      } else {
        $member = Member::find($member_id);
      }
      if (!$member) abort(404, "Impossible d'envoyer un message personnel : ce membre n'existe pas ou plus.");
      // Not members cannot contact non-leader members
      if (!$member->is_leader && !$this->user->isMember()) {
        return Helper::forbiddenResponse();
      }
      // Nobody can contact non-leader members personnally
      if (!$member->is_leader && $contact_type == self::$CONTACT_TYPE_PERSONAL) {
        return Helper::forbiddenResponse();
      }
      // Nobody can contact a leader's parents
      if ($member->is_leader && $contact_type == self::$CONTACT_TYPE_PARENTS) {
        return Helper::forbiddenResponse();
      }
      // Leaders cannot be contacted by non-members if the personal contact option is disabled
      if ($member->is_leader && !Parameter::get(Parameter::$ALLOW_PERSONAL_CONTACT)) {
        return Helper::forbiddenResponse();
      }
      // Check that there is a parent's e-mail address to write to
      if (($contact_type == self::$CONTACT_TYPE_PARENTS && !$member->hasParentsEmailAddress())) {
        abort(404, "Impossible de contacter les parents de " . $member->getFullName() . ". Leur adresse e-mail est inconnue.");
      }
      // Check that there is a personnal e-mail address to write to
      if ($contact_type == self::$CONTACT_TYPE_PERSONAL && !$member->email_member) {
        abort(404, "Impossible de contacter " . $member->getFullName() . ". Son adresse e-mail est inconnue.");
      }
    }
    // Make view
    return View::make('pages.contacts.personalEmail', array(
        'member' => $member,
        'section' => $section,
        'contact_type' => $contact_type,
    ));
  }
  
  /**
   * [Route] Sends an e-mail to a given person
   * 
   * @param string $contact_type  The kind of contact (see the list at the top of this class)
   * @param string $member_id  The id of the member to contact (or anything for the webmaster)
   */
  public function submit(Request $request, $contact_type, $member_id) {
    // Get input data
    $subject = $request->input('subject');
    $body = $request->input('body');
    $senderName = $request->input('sender_name');
    $senderEmail = $request->input('sender_email');
    // Check that all fields are non-empty
    $errorMessage = "";
    if (!$subject) $errorMessage .= "Vous devez entrer un sujet. ";
    if (!$body) $errorMessage .= "Vous devez entrer un message. ";
    if (!$senderName) $errorMessage .= "Vous devez indiquer votre nom. ";
    if (!$senderEmail) $errorMessage .= "Vous devez indiquer votre adresse e-mail. ";
    else if (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL))
            $errorMessage .= "L'adresse $senderEmail n'est pas correcte. ";
    if ($errorMessage) {
      // One of the fields is incorrect, redirect with error message
      return redirect()->route('personal_email', array('contact_type' => $contact_type, 'member_id' => $member_id))
              ->withInput()
              ->with('error_message', $errorMessage);
    } else {
      // Send e-mail to each recipient
      foreach ($this->getEmailAddressesFor($contact_type, $member_id) as $recipient) {
        $emailContent = Helper::renderEmail('personalEmail', $recipient, array(
            'message_body' => $body,
            'header_text' => "Voici un message de la part de $senderName ($senderEmail) envoyé depuis le site de l'unité " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
        ));
        $email = PendingEmail::create(array(
            'subject' => $subject,
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_email' => $senderEmail,
            'sender_name' => $senderName,
            'recipient' => $recipient,
            'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
        ));
        $email->send();
      }
      // Send confirmation e-mail to the sender
      $emailContent = Helper::renderEmail('personalEmail', $senderEmail, array(
          'message_body' => $body,
          'header_text' => $this->getConfirmationHeader($contact_type, $member_id),
      ));
      $confirmationEmail = PendingEmail::create(array(
            'subject' => $subject,
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
            'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
            'recipient' => $senderEmail,
            'priority' => PendingEmail::$PERSONAL_SENDER_PRIORITY,
        ));
      $confirmationEmail->send();
      // Log
      LogEntry::log("E-mail personnel", "Envoi d'un e-mail personnel", array("De" => $senderEmail, "Type" => $contact_type, "Destinataire" => $member_id)); // TODO improve log message
      // Redirect with success message
      return redirect()->route('personal_email', array('contact_type' => $contact_type, 'member_id' => $member_id))
              ->with('success_message', "Votre e-mail a bien été envoyé.");
    }
    
  }
  
  /**
   * Returns the header of the confirmation message
   * 
   * @param string $contact_type  The kind of contact (see the list at the top of this class)
   * @param string $member_id  The id of the member to contact (or anything for the webmaster)
   */
  private function getConfirmationHeader($contact_type, $member_id) {
    $middlePart = "";
    if ($contact_type == self::$CONTACT_TYPE_WEBMASTER) {
      $middlePart = " au webmaster";
    } elseif ($contact_type == self::$CONTACT_TYPE_SECTION) {
      $section = Section::find($member_id);
      $middlePart = " aux animateurs " . $section->de_la_section;
    } else {
      // Get recipient member
      if ($contact_type == self::$CONTACT_TYPE_ARCHIVED_LEADER) {
        $member = ArchivedLeader::find($member_id);
      } else {
        $member = Member::find($member_id);
      }
      // Check that there is a personnal e-mail address to write to
      if ($contact_type == self::$CONTACT_TYPE_PERSONAL || $contact_type == self::$CONTACT_TYPE_ARCHIVED_LEADER) {
        $middlePart = " à " . $member->first_name . " " . $member->last_name;
      } elseif ($contact_type == self::$CONTACT_TYPE_PARENTS) {
        $middlePart = " aux parents de " . $member->getFullName();
      }
    }
    return "Voici une copie du message que vous avez envoyé$middlePart depuis le site de l'unité " . Parameter::get(Parameter::$UNIT_SHORT_NAME);
  }
  
  /**
   * Returns the list of e-mail address for the given recipient
   * 
   * @param string $contact_type  The kind of contact (see the list at the top of this class)
   * @param string $member_id  The id of the member to contact (or anything for the webmaster)
   */
  private function getEmailAddressesFor($contact_type, $member_id) {
    if ($contact_type == self::$CONTACT_TYPE_WEBMASTER) {
      return array(Parameter::get(Parameter::$WEBMASTER_EMAIL));
    } elseif ($contact_type == self::$CONTACT_TYPE_SECTION) {
      $section = Section::find($member_id);
      if (!$section) abort(404, "Impossible d'envoyer un message : cette section n'existe pas.");
      if (!$section->email) abort(404, "Impossible d'envoyer un message à " . $section->la_section . " car son adresse e-mail est inconnue.");
      return array($section->email);
    } else {
      // Get recipient member
      if ($contact_type == self::$CONTACT_TYPE_ARCHIVED_LEADER) {
        $member = ArchivedLeader::find($member_id);
      } else {
        $member = Member::find($member_id);
      }
      if (!$member) abort(404, "Impossible d'envoyer un message personnel : ce membre n'existe plus.");
      // Not members cannot contact non-leader members
      if ($contact_type != self::$CONTACT_TYPE_ARCHIVED_LEADER && !$member->is_leader && !$this->user->isMember()) {
        abort(403);
      }
      // Nobody can contact non-leader members personnally
      if ($contact_type == self::$CONTACT_TYPE_PERSONAL && !$member->is_leader) {
        abort(403);
      }
      // Nobody can contact a leader's parents
      if ($contact_type == self::$CONTACT_TYPE_PARENTS && $member->is_leader) {
        abort(403);
      }
      // Check that there is a parent's e-mail address to write to
      if ($contact_type == self::$CONTACT_TYPE_PARENTS && !$member->hasParentsEmailAddress()) {
        abort(404, "Impossible de contacter les parents de " . $member->getFullName() . ". Leur adresse e-mail est inconnue.");
      }
      // Check that there is a personnal e-mail address to write to
      if ($contact_type == self::$CONTACT_TYPE_PERSONAL && !$member->email_member) {
        abort(404, "Impossible de contacter " . $member->getFullName() . ". Son adresse e-mail est inconnue.");
      }
      if ($contact_type == self::$CONTACT_TYPE_ARCHIVED_LEADER && !$member->email_member) {
        abort(404, "Impossible de contacter " . $member->getFullName() . ". Son adresse e-mail est inconnue.");
      }
      if ($contact_type == self::$CONTACT_TYPE_PARENTS) {
        return $member->getParentsEmailAddresses();
      } elseif ($contact_type == self::$CONTACT_TYPE_PERSONAL || $contact_type == self::$CONTACT_TYPE_ARCHIVED_LEADER) {
        return array($member->email_member);
      }
    }
  }
  
}
