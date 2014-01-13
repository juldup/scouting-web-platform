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
    $subject = Input::get('subject');
    if ($subject == "" || $subject == $this->defaultSubject())
      return Redirect::route('send_section_email')->withInput()->with('error_message', "Tu dois choisir un sujet à l'e-mail");
    return Redirect::route('send_section_email')->withInput();
  }
  
}