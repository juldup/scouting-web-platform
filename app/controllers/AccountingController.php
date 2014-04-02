<?php

class AccountingController extends BaseController {
  
  public function showPage() {
    // TODO access control
    return View::make('pages.accounting.accounting');
  }
  
  public function commitChanges() {
    
  }
  
}