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
 * ScoutMailer provides functions to send e-mails through an SMTP server
 */
class ScoutMailer {
  
  // The mailer (stored for reuse)
  protected static $mailer;
  
  /**
   * Sends the given e-mail. Returns whether the e-mail was sent.
   */
  public static function send(Swift_Message $message) {
    // Check that the recipient's e-mail address is not banned
    $to = $message->getTo();
    foreach (array_keys($to) as $email) {
      if (BannedEmail::isBanned($email)) return false;
    }
    // Try sending e-mail
    $mailer = self::getMailer();
    return $mailer->send($message);
  }
  
  /**
   * Returns (and generates if needed) the mailer
   */
  protected static function getMailer() {
    if (!self::$mailer) {
      $transport = Swift_SmtpTransport::newInstance(
              Parameter::get(Parameter::$SMTP_HOST),
              Parameter::get(Parameter::$SMTP_PORT),
              Parameter::get(Parameter::$SMTP_SECURITY))
              ->setUsername(Parameter::get(Parameter::$SMTP_USERNAME))
              ->setPassword(Parameter::get(Parameter::$SMTP_PASSWORD));
      self::$mailer = Swift_Mailer::newInstance($transport);
    }
    return self::$mailer;
  }
  
  /**
   * If there are unsent e-mails in the database, try to sent them (with a limit on the number of e-mails to send)
   */
  public static function sendPendingEmails($limit = 10) {
    // Current time
    $time = time();    
    // Delete all e-mails sent more than one hour ago
    $oneHourAgo = $time - 3600;
    DB::table('pending_emails')
            ->where('sent', '=', 1)
            ->where('last_retry', '<', $oneHourAgo)
            ->delete();
    // Select e-mails (with no too recent retry)
    $twoMinutesAgo = $time - 120;
    $emails = PendingEmail::where('sent', '=', 0)
            ->where('last_retry', '<', $twoMinutesAgo)
            ->where('priority', '<=', PendingEmail::$MAX_PRIORITY)
            ->orderBy('priority')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    // Try sending e-mails (if not locked by another process)
    foreach ($emails as $email) {
      // Lock with last_retry to avoid collision
      $count = DB::table('pending_emails')
              ->where('id', '=', $email->id)
              ->where('last_retry', '<', $twoMinutesAgo)
              ->update(array('last_retry' => $time));
      if ($count) {
        $email->send();
      }
    }
  }
  
}
