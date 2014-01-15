<?php

class EmailController extends BaseController {
  
  public function showPage() {
    $emails = Email::where('archive', '=', '')
            ->where('section_id', '=', $this->section->id)
            ->orderBy('id', 'DESC')
            ->get();
    return View::make('pages.emails.emails', array(
        'emails' => $emails,
        'can_send_emails' => $this->user->can(Privilege::$SEND_EMAILS, $this->section)
    ));
  }
  
  public function showManage() {
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    $emails = Email::where('archive', '=', '')
            ->where('section_id', '=', $this->section->id)
            ->orderBy('id', 'DESC')
            ->get();
    return View::make('pages.emails.manageEmails', array(
        'emails' => $emails,
        'can_send_emails' => $this->user->can(Privilege::$SEND_EMAILS, $this->section)
    ));
  }
  
  public function sendSectionEmail() {
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    $parents = array();
    $scouts = array();
    $leaders = array();
    $members = Member::where('validated', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->get();
    foreach ($members as $member) {
      if ($member->is_leader) {
        $leaders[] = array('member' => $member, 'type' => 'member');
      } else {
        if ($member->email1 || $member->email2 || $member->email3) {
          $parents[$member->id] = array('member' => $member, 'type' => 'parent');
        }
        if ($member->email_member) {
          $scouts[$member->id] = array('member' => $member, 'type' => 'parent');
        }
      }
    }
    $recipients = array();
    if (count($parents)) $recipients['Parents'] = $parents;
    if (count($scouts)) $recipients['Scouts'] = $scouts;
    if (count($leaders)) $recipients['Animateurs'] = $leaders;
    return View::make('pages.emails.sendEmail', array(
        'default_subject' => $this->defaultSubject(),
        'recipients' => $recipients,
    ));
  }
  
  private function defaultSubject() {
    return "[" . Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - " . $this->section->name . "] ";
  }
  
  public function submitSectionEmail() {
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    $subject = Input::get('subject');
    $body = Input::get('body');
    $senderName = Input::get('sender_name');
    $senderAddress = Input::get('sender_address');
    $files = Input::file('attachments');
    $attachments = array();
    // Create attachments
    foreach ($files as $file) {
      if ($file != null) {
        try {
          $attachments[] = EmailAttachment::newFromFile($file);
        } catch (Exception $ex) {
          return Redirect::route('send_section_email')
                  ->withInput()
                  ->with('error_message', "Une erreur s'est produite lors de l'enregistrement des pièces jointes. L'e-mail n'a pas été envoyé.");
        }
      }
    }
    // Gather recipients
    $recipientArray = array();
    $allInput = Input::all();
    foreach ($allInput as $key=>$value) {
      if (strpos($key, "parent_") === 0) {
        $memberId = substr($key, strlen("parent_"));
        $member = Member::find($memberId);
        if ($member) {
          if ($member->email1 && !in_array($member->email1, $recipientArray)) $recipientArray[] = $member->email1;
          if ($member->email2 && !in_array($member->email2, $recipientArray)) $recipientArray[] = $member->email2;
          if ($member->email3 && !in_array($member->email3, $recipientArray)) $recipientArray[] = $member->email3;
        }
      }
      if (strpos($key, "member_") === 0) {
        $memberId = substr($key, strlen("member_"));
        $member = Member::find($memberId);
        if ($member) {
          if ($member->email_member && !in_array($member->email_member, $recipientArray))
                  $recipientArray[] = $member->email_member;
        }
      }
    }
    // Create e-mail
    try {
      Email::create(array(
          'section_id' => $this->section->id,
          'date' => date('Y-m-d'),
          'time' => date('H:i:s'),
          'subject' => $subject,
          'body_html' => $body,
          'recipient_list' => implode(", ", $recipientArray),
          'sender_name' => $senderName,
          'sender_email' => $senderAddress,
      ));
    } catch (Exception $ex) {
      return Redirect::route('send_section_email')
              ->withInput()
              ->with('error_message', "Une erreur s'est produite. L'e-mail n'a pas été envoyé. $ex");
    }
    // Create pending e-mails
    foreach ($recipientArray as $recipient) {
      $message = Swift_Message::newInstance();
      $message->setSubject($subject);
      $message->setBody($body, 'text/html', 'utf-8');
      $message->setFrom($senderAddress, $senderName ? $senderName : null);
      $message->setTo($recipient);
      $serializedMessage = serialize($message);
      $pendingEmail = PendingEmail::create(array(
          'email_object' => $serializedMessage,
          'priority' => PendingEmail::$SECTION_EMAIL_PRIORITY,
          'sent' => false,
      ));
    }
    // Create confirmation email
    $message = Swift_Message::newInstance();
    $message->setSubject($subject);
    $message->setBody("<p><strong><em>[Cet e-mail a bien été envoyé aux destinataires sélectionnés]</em></strong></p>" . $body, 'text/html', 'utf-8');
    $message->setFrom(Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS), "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $message->setTo($senderAddress, $senderName ? $senderName : null);
    $serializedMessage = serialize($message);
    $pendingEmail = PendingEmail::create(array(
        'email_object' => $serializedMessage,
        'priority' => PendingEmail::$SECTION_SENDER_PRIORITY,
        'sent' => false,
    ));
    return Redirect::route('manage_emails')
            ->with('success_message', "L'e-mail a été enregistré avec succès et est en cours d'envoi.");
  }
  
  public function deleteEmail($email_id) {
    if (!$this->user->can(Privilege::$SEND_EMAILS)) {
      return Helper::forbiddenResponse();
    }
    // Retrieve e-mail
    $email = Email::find($email_id);
    if (!$email) App::abort(404, "Cet e-mail n'existe pas.");
    // Delete e-mail if it is less than one week old
    if ($email->canBeDeleted()) {
      // Delete e-mail
      try {
        $email->deleteWithAttachments();
        return Redirect::route('manage_emails')
                ->with('success_message', "L'e-mail a été supprimé.");
      } catch (Exception $ex) {
        return Redirect::route('manage_emails')
                ->with('error_message', "Une erreur est survenue. L'e-mail n'a pas pu être supprimé.");
      }
    } else {
      // The e-mail is too old and cannot be deleted
      return Redirect::route('manage_emails')
              ->with('error_message', "Cet e-mail est trop vieux. Il ne peut plus être supprimé mais peut être archivé.");
    }
  }
  
}
