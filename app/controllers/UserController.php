<?php

class UserController extends BaseController {
  
  public function login() {
    
    $action = Session::get('action', null);
    
    // Save referrer to session if need be
    if (!$action && URL::previous() != URL::current()) {
      Session::put('login_referrer', URL::previous());
    }
    
    return View::make('user.login', array(
        "error_login" => $action == 'login',
        "error_create" => $action == "create"
    ));
  }
  
  public function submitLogin() {
    $username = Input::get('login_username');
    $password = Input::get('login_password');
    $remember = Input::get('login_remember');

    $user = User::getWithUsernameAndPassword($username, $password);

    if ($user) {
      // Log user in
      Session::put('user_id', $user->id);
      // Save cookies
      if ($remember) {
        $cookiePassword = User::getCookiePassword($password, $user->password);
        Cookie::queue('username', $username, 365 * 24 * 60);
        Cookie::queue('password', $cookiePassword, 365 * 24 * 60);
      }
      // Redirect to previous page
      $referrer = Session::get('login_referrer', URL::route('home'));
      Session::forget('login_referrer');
      return Redirect::to($referrer);
    }
    
    // No matching user
    return Redirect::route('login')->withInput()->with('action', 'login');
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
  
  public function create() {
    $username = Input::get('create_username');
    $email = Input::get('create_email');
    $password = Input::get('create_password');
    $remember = Input::get('create_remember');
    
    $validator = Validator::make(
            array(
                "create_username" => $username,
                "create_email" => $email,
                "create_password" => $password,
            ),
            array(
                "create_username" => "required|unique:users,username",
                "create_email" => "required|email",
                "create_password" => "required|min:6",
            ),
            array(
                "create_username.required" => "Veuillez entrer un nom d'utilisateur.",
                "create_username.unique" => "Ce nom d'utilisateur est déjà utilisé. Choisissez-en un autre.",
                "create_email.required" => "Veuillez entrer votre adresse e-mail.",
                "create_email.email" => "Votre adresse e-mail n'est pas valide.",
                "create_password.required" => "Veuillez entrer un mot de passe.",
                "create_password.min" => "Votre mot de passe doit faire au moins 6 caractères de long.",
            )
    );
    
    if ($validator->fails()) {
      return Redirect::route('login')->withInput()->withErrors($validator)->with('action', 'create');
    }
    
    // Create user
    $user = User::createWith($username, $email, $password);
    
    // Log user in
    Session::put('user_id', $user->id);
    
    // Save cookies
    if ($remember) {
      $cookiePassword = User::getCookiePassword($password, $user->password);
      Cookie::queue('username', $username, 365 * 24 * 60);
      Cookie::queue('password', $cookiePassword, 365 * 24 * 60);
    }
    
    // Redirect to previous page
    $referrer = Session::get('login_referrer', URL::route('home'));
    Session::forget('login_referrer');
    return Redirect::to($referrer);
    
  }
  
  public function verify($code) {
    
    $user = User::where('verification_code', '=', $code)->first();
    if ($user) {
      $user->verified = true;
      $user->save();
      $status = "verified";
    } else {
      $status = "unknown";
    }
    
    return View::make('user.verify', array('status' => $status));
    
  }
  
  public function cancelVerification($code) {
    $user = User::where('verification_code', '=', $code)->first();
    if ($user) {
      if ($user->verified) {
        $status = "already verified";
      } else {
        $user->delete();
        $status = "canceled";
      }
    } else {
      $status = "unknown";
    }
    
    return View::make('user.verify', array('status' => $status));
  }
  
  public function editUser() {
    
  }
  
  public function retrievePassword() {
    
  }
  
}
