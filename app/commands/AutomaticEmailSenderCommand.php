<?php

class AutomaticEmailSenderCommand extends \Illuminate\Console\Command {
  
  protected $name = "scouts:send-emails";
  protected $description = "Tries sending a few pending e-mails";
  
  public function fire() {
    ScoutMailer::sendPendingEmails(10);
  }
  
}