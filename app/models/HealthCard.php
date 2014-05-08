<?php

class HealthCard extends Eloquent {
  
  protected $guarded = array('id', 'signatory_id', 'signatory_email',
      'reminder_sent', 'signature_date', 'created_at', 'updated_at');
  
  public function getMember() {
    return Member::find($this->member_id);
  }
  
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
    // Delete too old health cards
    $oneYearAgo = date('Y-m-d', strtotime("-1 year"));
    HealthCard::where('signature_date', '<=', $oneYearAgo)->delete();
    // Create reminder e-mails
    $oneYearMinusOneWeekAgo = date('Y-m-d', strtotime("-1 year") + 8 * 24 * 3600);
    $healthCards = HealthCard::where('reminder_sent', '=', false)
            ->where('signature_date', '<=', $oneYearMinusOneWeekAgo)
            ->get();
    foreach ($healthCards as $healthCard) {
      $member = Member::find($healthCard->member_id);
      if ($member) {
        $body = View::make('emails.healthCardReminder', array(
            'health_card' => $healthCard,
            'member' => $member,
            'website_name' => Parameter::get(Parameter::$UNIT_SHORT_NAME),
        ))->render();
        // Send an e-mail for each parent (or to the member if they are a leader)
        $emailAddresses = $member->getParentsEmailAddresses();
        if ($member->is_leader) {
          $emailAddresses = array($member->email_member);
        }
        foreach ($emailAddresses as $emailAddress) {
          $email = PendingEmail::create(array(
              'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] La fiche santé de " . $member['first_name'] . " " . $member['last_name'] . " va bientôt expirer",
              'raw_body' => $body,
              'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
              'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
              'recipient' => $emailAddress,
              'priority' => PendingEmail::$HEALTH_CARD_REMINDER_PRIORITY
          ));
        }
        // Mark reminder as sent
        $healthCard->reminder_sent = true;
        $healthCard->save();
      }
    }
    // Send e-mails
    ScoutMailer::sendPendingEmails();
  }
  
}