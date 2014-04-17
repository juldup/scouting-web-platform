<?php

class BanEmailAddressController extends BaseController {
  
  public function banEmailAddress($ban_code) {
    $banned = BannedEmail::where('ban_code', '=', $ban_code)->first();
    if (!$banned) {
      return App::abort(404);
    }
    if ($banned->banned) {
      return $this->confirmBanEmailAddress($ban_code);
    }
    return View::make('pages.banEmailAddress.banEmailAddress', array(
        'email' => $banned->email,
        'ban_code' => $ban_code,
    ));
  }
  
  public function confirmBanEmailAddress($ban_code) {
    $banned = BannedEmail::where('ban_code', '=', $ban_code)->first();
    if (!$banned) {
      return App::abort(404);
    }
    $banned->banned = true;
    $banned->save();
    return View::make('pages.banEmailAddress.confirmBan', array(
        'email' => $banned->email,
    ));
  }
  
}