<?php

class EmailController extends BaseController {
  
  public function showPage() {
    $emails = Email::where('archived', '=', false)
            ->where('deleted', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('id', 'DESC')
            ->get();
    return View::make('pages.emails.emails', array(
        'emails' => $emails,
        'can_send_emails' => $this->user->can(Privilege::$SEND_EMAILS, $this->section)
    ));
  }
  
  public function downloadAttachment($attachment_id) {
    if (!$this->user->isMember()) return Helper::forbiddenResponse();
    $attachment = EmailAttachment::find($attachment_id);
    if (!$attachment) App::abort(404, "Ce document n'existe plus.");
    $email = Email::find($attachment->email_id);
    if (!$email || $email->deleted) App::abort(404, "Cet e-mail a été supprimé. Il n'est plus possible d'accéder à ses pièces jointes.");
    
    $path = $attachment->getPath();
    $filename = str_replace("\"", "", $attachment->filename);
    if (file_exists($path)) {
      return Response::make(file_get_contents($path), 200, array(
          'Content-Type' => 'application/octet-stream',
          'Content-length' => filesize($path),
          'Content-Transfer-Encoding' => 'Binary',
          'Content-disposition' => "attachment; filename=\"$filename\"",
      ));
    } else {
      return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe plus.");
    }
  }
  
  public function showManage() {
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    $emails = Email::where('archived', '=', false)
            ->where('deleted', '=', false)
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
    $recipients = array();
    if ($this->section->id == 1) {
      $parents = array();
      $scouts = array();
      $leaders = array();
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
      foreach ($sections as $section) {
        $recipientList = $this->getRecipientsForSection($section->id);
        if (array_key_exists('parents', $recipientList)) $parents['Parents ' . $section->de_la_section] = $recipientList['parents'];
        if (array_key_exists('scouts', $recipientList)) $scouts['Scouts ' . $section->de_la_section] = $recipientList['scouts'];
        if (array_key_exists('leaders', $recipientList)) $leaders['Animateurs ' . $section->de_la_section] = $recipientList['leaders'];
      }
      $recipientList = $this->getRecipientsForSection(1);
      if (array_key_exists('leaders', $recipientList)) $leaders["Équipe d'unité"] = $recipientList['leaders'];
//      foreach ($parents as $category=>$list) {
//        $recipients[$category] = $list;
//      }
//      foreach ($scouts as $category=>$list) {
//        $recipients[$category] = $list;
//      }
//      foreach ($leaders as $category=>$list) {
//        $recipients[$category] = $list;
//      }
      if (count($parents)) $recipients['Parents'] = $parents;
      if (count($scouts)) $recipients['Scouts'] = $scouts;
      if (count($leaders)) $recipients['Animateurs'] = $leaders;
    } else {
      $recipientList = $this->getRecipientsForSection($this->section->id);
      if (array_key_exists('parents', $recipientList)) $recipients['Parents'] = $recipientList['parents'];
      if (array_key_exists('scouts', $recipientList)) $recipients['Scouts'] = $recipientList['scouts'];
      if (array_key_exists('leaders', $recipientList)) $recipients['Animateurs'] = $recipientList['leaders'];
      $recipients = array($recipients);
    }
    return View::make('pages.emails.sendEmail', array(
        'default_subject' => $this->defaultSubject(),
        'recipients' => $recipients,
    ));
  }
  
  protected function getRecipientsForSection($sectionId) {
    $parents = array();
    $scouts = array();
    $leaders = array();
    $members = Member::where('validated', '=', true)
            ->where('section_id', '=', $sectionId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    foreach ($members as $member) {
      if ($member->is_leader) {
        $leaders[] = array('member' => $member, 'type' => 'member');
      } else {
        if ($member->email1 || $member->email2 || $member->email3) {
          $parents[$member->id] = array('member' => $member, 'type' => 'parent');
        }
        if ($member->email_member) {
          $scouts[$member->id] = array('member' => $member, 'type' => 'member');
        }
      }
    }
    $recipients = array();
    if (count($parents)) $recipients['parents'] = $parents;
    if (count($scouts)) $recipients['scouts'] = $scouts;
    if (count($leaders)) $recipients['leaders'] = $leaders;
    return $recipients;
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
    $extraRecipients = Input::get('extra_recipients');
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
    // Add extra recipients
    $extraRecipientArray = explode(",", $extraRecipients);
    foreach ($extraRecipientArray as $extra) {
      $address = trim($extra);
      if (filter_var($address, FILTER_VALIDATE_EMAIL) && !in_array($address, $recipientArray)) {
        $recipientArray[] = $address;
      }
    }
    // Add sender as a recipient
    if (!in_array($senderAddress, $recipientArray)) $recipientArray[] = $senderAddress;
    // Create e-mail
    try {
      $email = Email::create(array(
          'section_id' => $this->section->id,
          'date' => date('Y-m-d'),
          'time' => date('H:i:s'),
          'subject' => $subject,
          'body_html' => $body,
          'recipient_list' => implode(", ", $recipientArray),
          'sender_name' => $senderName,
          'sender_email' => $senderAddress,
      ));
      foreach ($attachments as $attachment) {
        $attachment->email_id = $email->id;
        $attachment->save();
      }
    } catch (Exception $ex) {
      return Redirect::route('send_section_email')
              ->withInput()
              ->with('error_message', "Une erreur s'est produite. L'e-mail n'a pas été envoyé. $ex");
    }
    // Create pending e-mails
    foreach ($recipientArray as $recipient) {
      PendingEmail::create(array(
          'subject' => $subject,
          'section_email_id' => $email->id,
          'sender_email' => $senderAddress,
          'sender_name' => $senderName,
          'recipient' => $recipient,
          'priority' => PendingEmail::$SECTION_EMAIL_PRIORITY,
          'sent' => false,
      ));
    }
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
        $email->deleted = true;
        $email->save();
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
