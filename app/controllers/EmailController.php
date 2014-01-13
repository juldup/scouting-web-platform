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
    return View::make('pages.emails.sendEmail', array(
        'default_subject' => "[" . Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - " . $this->section->name . "] ",
    ));
  }
  
}