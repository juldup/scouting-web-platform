<?php

class AccountingItem extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  public static $INHERIT = "SPECIAL_TRANSACTION_INHERIT";
  
  public function cashinFormatted() {
    return Helper::formatCashAmount($this->cashin_cents / 100.0);
  }
  
  public function cashoutFormatted() {
    return Helper::formatCashAmount($this->cashout_cents / 100.0);
  }
  
  public function bankinFormatted() {
    return Helper::formatCashAmount($this->bankin_cents / 100.0);
  }
  
  public function bankoutFormatted() {
    return Helper::formatCashAmount($this->bankout_cents / 100.0);
  }
  
}
