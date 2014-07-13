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

/**
 * This Eloquent class represents the health card of a member, filled in by their parents.
 * Health cards are kept in the system for 365 days when they are created or modified.
 * 
 * Columns:
 *   - member_id:       Member this health card refers to
 *   - signatory_id:    User account that was used to sign this health card
 *   - signatory_email: The e-mail of the user account used to sign this health card
 *   - reminder_sent:   Whether an expiration reminder e-mail has been sent for this health card
 *   - signature_date:  Date at which the health card was last signed by the parents
 *   The following fields are the health data filled in by the parents
 *   - contact1_name
 *   - contact1_address
 *   - contact1_phone
 *   - contact1_relationship
 *   - contact2_name
 *   - contact2_address
 *   - contact2_phone
 *   - contact2_relationship
 *   - doctor_name
 *   - doctor_address
 *   - doctor_phone
 *   - has_no_constrained_activities
 *   - constrained_activities_details
 *   - medical_data
 *   - medical_history
 *   - has_tetanus_vaccine
 *   - tetanus_vaccine_details
 *   - has_allergy
 *   - allergy_details
 *   - allergy_consequences
 *   - has_special_diet
 *   - special_diet_details
 *   - other_important_information
 *   - has_drugs
 *   - drugs_details
 *   - drugs_autonomy
 *   - comments
 */
class HealthCard extends Eloquent {
  
  protected $guarded = array('id', 'signatory_id', 'signatory_email',
      'reminder_sent', 'signature_date', 'created_at', 'updated_at');
  
  /**
   * Returns the member this health card refers to
   */
  public function getMember() {
    return Member::find($this->member_id);
  }
  
  /**
   * Returns the number of days remaining before the automatic deletion of this health card
   */
  public function daysBeforeDeletion() {
    $seconds_diff = strtotime($this->signature_date) + 365*24*3600 - time();
    return ceil($seconds_diff / (3600 * 24));
  }
  
  /**
   * This function should be called with a daily cron task.
   * Sends a reminder by e-mail one week before the health card expiration
   * and deletes expired health cards.
   */
  public static function autoReminderAndDelete() {
    $oneYearAgo = date('Y-m-d', strtotime("-1 year"));
    // Log deleted health cards
    $deletedHealthCards = HealthCard::where('signature_date', '<=', $oneYearAgo)->get();
    $deletedNames = "";
    foreach ($deletedHealthCards as $healthCard) {
      $deletedNames .= ($deletedNames ? ", " : "") . $healthCard->getMember()->getFullName();
    }
    if ($deletedNames) {
      LogEntry::log("Fiche santé", "Suppression automatique de fiches santé", array("Membres" => $deletedNames));
    }
    // Delete too old health cards
    HealthCard::where('signature_date', '<=', $oneYearAgo)->delete();
    // Create reminder e-mails
    $oneYearMinusOneWeekAgo = date('Y-m-d', strtotime("-1 year") + 8 * 24 * 3600);
    $healthCards = HealthCard::where('reminder_sent', '=', false)
            ->where('signature_date', '<=', $oneYearMinusOneWeekAgo)
            ->get();
    foreach ($healthCards as $healthCard) {
      $member = Member::find($healthCard->member_id);
      if ($member) {
        // Send an e-mail for each parent (or to the member if they are a leader)
        $emailAddresses = $member->getParentsEmailAddresses();
        if ($member->is_leader) {
          $emailAddresses = array($member->email_member);
        }
        $recipients = "";
        foreach ($emailAddresses as $emailAddress) {
          $recipients .= ($recipients ? ", " : "") . $emailAddress;
          $emailContent = Helper::renderEmail('healthCardReminder', $emailAddress, array(
              'health_card' => $healthCard,
              'member' => $member,
          ));
          PendingEmail::create(array(
              'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] La fiche santé de " . $member['first_name'] . " " . $member['last_name'] . " va bientôt expirer",
              'raw_body' => $emailContent['txt'],
              'html_body' => $emailContent['html'],
              'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
              'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
              'recipient' => $emailAddress,
              'priority' => PendingEmail::$HEALTH_CARD_REMINDER_PRIORITY
          ));
        }
        // Mark reminder as sent
        $healthCard->reminder_sent = true;
        $healthCard->save();
        LogEntry::log("Fiche santé", "Envoi d'une e-mail de rappel", array("Membre" => $member->getFullName(), "Destinataires" => $recipients));
      }
    }
    // Send e-mails
    ScoutMailer::sendPendingEmails();
  }
  
}
