<?php

/**
 * Leaders can send e-mails to the parents and scouts of their section.
 * This controller provides the mean to send e-mails to the section, and
 * presents pages to view and manage previously sent e-mails.
 */
class EmailController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows a page containing the e-mails that were previously sent
   * 
   * @param boolean $showArchives  Whether the archived e-mails are being shown
   * @param integer $page  The archive page currently being viewed (starts at 0)
   */
  public function showPage($section_slug = null, $showArchives = false, $page = 0) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_EMAILS)) {
      return App::abort(404);
    }
    // Get e-mail list
    if ($showArchives) {
      // Showing archived e-mails
      $pageSize = 20;
      // Get archived e-mails in the current archive page
      $emails = Email::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('deleted', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->orderBy('id', 'DESC')
              ->skip($page * $pageSize)
              ->take($pageSize)
              ->get();
      // Check whether there is a following page
      $hasArchives = Email::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('deleted', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->orderBy('id', 'DESC')
              ->skip(($page + 1) * $pageSize)
              ->take(1)
              ->count();
    } else {
      // Showing non-archived e-mails
      // Get e-mails that are not archived nor too old
      $emails = Email::where('archived', '=', false)
              ->where('date', '>=', Helper::oneYearAgo())
              ->where('deleted', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->orderBy('id', 'DESC')
              ->get();
      // Check if there are archives
      $hasArchives = Email::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('deleted', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->orderBy('id', 'DESC')
              ->count();
    }
    // Make view
    return View::make('pages.emails.emails', array(
        'emails' => $emails,
        'can_send_emails' => $this->user->can(Privilege::$SEND_EMAILS, $this->section),
        'showing_archives' => $showArchives,
        'has_archives' => $hasArchives,
        'next_page' => $page + 1,
    ));
  }
  
  /**
   * [Route] Shows list of archived e-mails
   */
  public function showArchives($section_slug = null) {
    $page = Input::get('page');
    if (!$page) $page = 0;
    return $this->showPage($section_slug, true, $page);
  }
  
  /**
   * [Route] Outputs an attached document for download
   */
  public function downloadAttachment($attachment_id) {
    // Make sure the user has access to attachments
    if (!$this->user->isMember()) return Helper::forbiddenResponse();
    // Get attachment
    $attachment = EmailAttachment::find($attachment_id);
    if (!$attachment) App::abort(404, "Ce document n'existe plus.");
    // Get e-mail corresponding to attachment, to make sure it has not been deleted
    $email = Email::find($attachment->email_id);
    if (!$email || $email->deleted) App::abort(404, "Cet e-mail a été supprimé. Il n'est plus possible d'accéder à ses pièces jointes.");
    // Output file
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
      // The file has not been found
      return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe plus.");
    }
  }
  
  /**
   * [Route] Shows the private leader page for managing sent e-mails
   */
  public function showManage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_EMAILS)) {
      return App::abort(404);
    }
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Get the list of non-archived e-mails
    $emails = Email::where('archived', '=', false)
            ->where('date', '>=', Helper::oneYearAgo())
            ->where('deleted', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('id', 'DESC')
            ->get();
    // Make view
    return View::make('pages.emails.manageEmails', array(
        'emails' => $emails,
        'can_send_emails' => $this->user->can(Privilege::$SEND_EMAILS, $this->section)
    ));
  }
  
  /**
   * [Route] Shows the page to send an e-mail to the members of a section
   */
  public function sendSectionEmail() {
    // Make sure the user can send e-mails to the members of this section
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Construct list of recipient (sorting them in categories: parents, scouts, leaders)
    $recipients = array();
    if ($this->section->id == 1) {
      // Section is unit, add all members to the list
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
      if (count($parents)) $recipients['Parents'] = $parents;
      if (count($scouts)) $recipients['Scouts'] = $scouts;
      if (count($leaders)) $recipients['Animateurs'] = $leaders;
    } else {
      // Non-unit section
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
  
  /**
   * Returns the list of recipients belonging to the given section, in the form
   * of an array (with the keys being 'parents', 'scouts', 'leaders') of arrays with the keys
   * being the member ids and the elements being arrays containing {'member'=>{the Member object}, 'type'=>{'parent' or 'member'}}
   */
  private function getRecipientsForSection($sectionId) {
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
  
  /**
   * Generates the default e-mail subject for the current section
   */
  private function defaultSubject() {
    return "[" . Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - " . $this->section->name . "] ";
  }
  
  /**
   * [Route] Submits an e-mail and a list of recipients for sending
   */
  public function submitSectionEmail() {
    // Make sure the current user can send e-mails to this section
    if (!$this->user->can(Privilege::$SEND_EMAILS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Gather input
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
  
  /**
   * [Route] Deletes an e-mail that was sent within the last 7 days
   */
  public function deleteEmail($email_id) {
    // Retrieve e-mail
    $email = Email::find($email_id);
    if (!$email) App::abort(404, "Cet e-mail n'existe pas.");
    // Make sure the user can delete this e-mail
    if (!$this->user->can(Privilege::$SEND_EMAILS, $email->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Delete e-mail if it is less than one week old
    if ($email->canBeDeleted()) {
      // Delete e-mail and redirect with status message
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
  
  /**
   * [Route] Archives an e-mail
   */
  public function archiveEmail($section_slug, $email_id) {
    // Get e-mail
    $email = Email::find($email_id);
    if (!$email) {
      App::abort(404, "Cet e-mail n'existe pas.");
    }
    // Make sure the user can archive this e-mail
    if (!$this->user->can(Privilege::$SEND_EMAILS, $email->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Archive it
    try {
      $email->archived = true;
      $email->save();
      $success = true;
      $message = "L'e-mail a été archivé.";
    } catch (Exception $e) {
      $success = false;
      $message = "Une erreur s'est produite. L'e-mail n'a pas été archivé.";
    }
    // Redirect with status message
    return Redirect::route('manage_emails', array(
        "section_slug" => $email->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
