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
 * This Eloquent class represents an e-mail that must be sent. The e-mails are
 * sent asynchronously to avoid long request times. A pending e-mail can be a section
 * e-mail (it then has a reference to the Email object), a personal e-mail or a
 * transaction e-mail.
 * E-mails have a priority value, that is increased on each retry. When this value
 * reaches 20, the e-mail is not resent again.
 * 
 * Columns:
 *   - section_email_id      The section e-mail (if this is a section e-mail)
 *   - raw_body              The raw body (can be null if html_body is set or this e-mail is a section e-mail)
 *   - html_body:            The body in html (can be null if raw_body is set or this e-mail is a section e-mail)
 *   - subject:              The subject of the e-mail
 *   - sender_email:         The e-mail address of the sender
 *   - sender_name:          The name of the sender
 *   - recipient:            The e-mail address this e-mail will be sent to
 *   - priority:             E-mails with lower priority values will be sent firt. Also used as retry counter.
 *   - attached_document_id: The document (Document instance) when the e-mail sends a document from the download page
 *   - sent:                 Whether this e-mail has been successfully sent
 *   - last_retry:           Timestamp of the last retry (to avoid successive retries, and to lock the e-mail between processes)
 */
class PendingEmail extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // Preset priorities
  public static $ACCOUNT_EMAIL_PRIORITY = 1;
  public static $PERSONAL_SENDER_PRIORITY = 2;
  public static $PERSONAL_EMAIL_PRIORITY = 5;
  public static $ABSENCE_EMAIL_PRIORITY = 7;
  public static $SECTION_EMAIL_PRIORITY = 10;
  public static $SECTION_SENDER_PRIORITY = 12;
  public static $HEALTH_CARD_REMINDER_PRIORITY = 14;
  public static $MAX_PRIORITY = 20;
  
  /**
   * Tries sending this e-mail
   */
  public function send() {
    // Check that the recipient's e-mail address is not banned
    if (BannedEmail::isBanned($this->recipient) && !Helper::emailIsInListing($this->recipient)) {
      // Recipient is banned and not in the listing, cancel sending
      $this->priority = self::$MAX_PRIORITY;
      $this->save();
      LogEntry::log("Ban", "E-mail non envoyé car adresse bannie", array(
            "Sujet" => $this->subject,
            "Destinataire" => $this->recipient,
            "Expéditeur" => $this->sender_name ? $this->sender_name . " (" . $this->sender_email . ")" : $this->sender_email));
      return;
    }
    // Send e-mail
    try {
      // Create Swift message to encapsulate this e-mail
      $message = ScoutMailer::newMail();
      // Set subject
      $message->Subject = $this->subject;
      // Set sender
      if (Parameter::isVerifiedSender($this->sender_email)) {
        $message->setFrom($this->sender_email, $this->sender_name ? $this->sender_name : null);
      } else {
        $message->setFrom(Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS), $this->sender_name ? $this->sender_name : null);
        $message->addReplyTo($this->sender_email, $this->sender_name ? $this->sender_name : null);
      }
      // Set recipient
      $message->addAddress($this->recipient);
      // Set body
      if ($this->section_email_id) {
        // This is a section e-mail
        $email = Email::find($this->section_email_id);
        // Add attachments
        $attachments = EmailAttachment::where('email_id', '=', $email->id)->get();
        foreach ($attachments as $attachment) {
          $message->addAttachment($attachment->getPath(), $attachment->filename);
        }
        // Generate e-mail content
        $emailContent = Helper::renderEmail('pureHtmlEmail', $this->recipient, array(
            'html_body' => $email->body_html,
        ));
        $message->Body = $emailContent['html'];
        $message->AltBody = $emailContent['txt'];
      } else {
        // This is a regular e-mail, not a section e-mail
        // Add html body
        if ($this->html_body) {
          $message->Body = $this->html_body;
        }
        // Add raw body
        if ($this->raw_body) {
          if ($this->html_body) {
            $message->AltBody = $this->raw_body;
          } else {
            $message->AltBody = $this->raw_body;
          }
        }
      }
      // Add attached document (if any and if it still exists)
      if ($this->attached_document_id) {
        $document = Document::find($this->attached_document_id);
        if ($document) {
          $message->addAttachment($document->getPath(), $document->filename);
        }
      }
      // Send-email
      $result = ScoutMailer::send($message);
    } catch (Exception $ex) {
      Log::error($ex);
      $result = false;
    }
    // Update e-mail depending on result
    if ($result) {
      $this->sent = true;
    } else {
      $this->priority = $this->priority + 1;
      if ($this->priority == PendingEmail::$MAX_PRIORITY) {
        LogEntry::error("E-mails", "Erreur lors de l'envoi d'un e-mail", array(
            "Sujet" => $this->subject,
            "Destinataire" => $this->recipient,
            "Expéditeur" => $this->sender_name ? $this->sender_name . " (" . $this->sender_email . ")" : $this->sender_email));
      }
    }
    $this->save();
  }
  
}
