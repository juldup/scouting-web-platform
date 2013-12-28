<?php

class UserController extends BaseController {
  
  public function login($loginError = false, $createData = null) {
    if (!$loginError && !$createData) {
      Session::put('login_referrer', URL::previous());
    }
    
    return View::make('user.login', array(
        "error_login" => $loginError,
        "error_create" => $createData != null,
        "create_data" => $createData,
    ));
  }
  
  public function submitLogin() {
    $username = Input::get('login_username');
    $password = Input::get('login_password');
    $remember = Input::get('login_remember');

    $user = User::getWithUsernameAndPassword($username, $password);

    if ($user) {
      Session::put('user_id', $user->id);
      $referrer = Session::get('login_referrer', URL::route('home'));

      if ($remember) {
        Cookie::queue('username', $username, 365 * 24 * 60);
        Cookie::queue('password', $password, 365 * 24 * 60);
      }

      return Illuminate\Http\RedirectResponse::create($referrer);
    }
    
    // No matching user
    return $this->login(true);
  }

  public function logout() {
    // Unlog user
    Session::flush();
    // Remove cookies
    Cookie::queue('username', null, -1);
    Cookie::queue('password', null, -1);
    // Redirect to previous page
    return \Symfony\Component\HttpFoundation\RedirectResponse::create(URL::previous());
  }
  
  public function editUser() {
    
  }
  
  public function retrievePassword() {
    
  }
  
  public function create() {
//    $username = Input::get('create_username');
//    $email = Input::get('create_email');
//    $password = Input::get('create_password');
//    $remember = Input::get('create_remember');
//    
//    $userWithSameUsername = User::where('username', '=', $username)->first();
//    if ($userWithSameUsername) {
//      $error = "Ce nom d'utilisateur est déjà utilisé.";
//    } else if ($password === "") {
//      $error = "Veuillez introduire un mot de passe."
//    } else if ($email)
    
  }
  
}
