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
  public static $SECTION_EMAIL_PRIORITY = 10;
  public static $SECTION_SENDER_PRIORITY = 12;
  public static $HEALTH_CARD_REMINDER_PRIORITY = 14;
  public static $MAX_PRIORITY = 20;
  
  /**
   * Tries sending this e-mail
   */
  public function send() {
    // Create Swift message to encapsulate this e-mail
    $message = Swift_Message::newInstance();
    // Set subject
    $message->setSubject($this->subject);
    // Set sender
    if (Parameter::isVerifiedSender($this->sender_email)) {
      $message->setFrom($this->sender_email, $this->sender_name ? $this->sender_name : null);
    } else {
      $message->setFrom(Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS), $this->sender_name ? $this->sender_name : null);
      $message->setReplyTo($this->sender_email, $this->sender_name ? $this->sender_name : null);
    }
    // Set recipient
    $message->setTo($this->recipient);
    // Set body
    if ($this->section_email_id) {
      // This is a section e-mail
      $email = Email::find($this->section_email_id);
      // Add attachments
      $attachments = EmailAttachment::where('email_id', '=', $email->id)->get();
      foreach ($attachments as $attachment) {
        $message->attach(Swift_Attachment::newInstance(file_get_contents($attachment->getPath()), $attachment->filename));
      }
      // Generate e-mail content
      $emailContent = Helper::renderEmail('pureHtmlEmail', $this->recipient, array(
          'html_body' => $email->body_html,
      ));
      $message->setBody($emailContent['html'], 'text/html', 'utf-8');
      $message->addPart($emailContent['txt'], 'text/plain', 'utf-8');
    } else {
      // This is a regular e-mail, not a section e-mail
      // Add html body
      if ($this->html_body) {
        $message->setBody($this->html_body, 'text/html', 'utf-8');
      }
      // Add raw body
      if ($this->raw_body) {
        if ($this->html_body) {
          $message->addPart($this->raw_body, 'text/plain', 'utf-8');
        } else {
          $message->setBody($this->raw_body, 'text/plain', 'utf-8');
        }
      }
    }
    // Add attached document (if any and if it still exists)
    if ($this->attached_document_id) {
      $document = Document::find($this->attached_document_id);
      if ($document) {
        $message->attach(Swift_Attachment::newInstance(file_get_contents($document->getPath()), $document->filename));
      }
    }
    // Send-email
    try {
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
    }
    $this->save();
  }
  
}
