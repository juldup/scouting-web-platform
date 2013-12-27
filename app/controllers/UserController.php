<?php

class UserController extends BaseController {
  
  public function login() {
    Session::put('user_id', User::first()->id);
    //return \Symfony\Component\HttpFoundation\RedirectResponse::create(Request::referrer());
    return \Symfony\Component\HttpFoundation\RedirectResponse::create(URL::route('home'));
  }
  
  public function logout() {
    // Unlog user
    Session::flush();
    // Redirect to previous page
    //return \Symfony\Component\HttpFoundation\RedirectResponse::create(Request::referrer());
    return \Symfony\Component\HttpFoundation\RedirectResponse::create(URL::route('home'));
  }
  
  public function editUser() {
    
  }
  
  public function retrievePassword() {
    
  }
  
  public function create() {
    
  }
  
}
