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
    // Retrieve data from form
    $username = Input::get('create_username');
    $email = Input::get('create_email');
    $password = Input::get('create_password');
    $remember = Input::get('create_remember');
    
    // Validate data
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
                "create_password.min" => "Votre mot de passe doit compter au moins 6 caractères.",
            )
    );
    if ($validator->fails()) {
      return Redirect::route('login')->withInput()->withErrors($validator)->with('action', 'create');
    }
    
    // Validation passed, create user
    $user = User::createWith($username, $email, $password);
    // TODO send verification e-mail
    
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
    
    // Find user corresponding to verification code
    $user = User::where('verification_code', '=', $code)->first();
    
    if ($user) {
      // User exists, mark it as verified
      $user->verified = true;
      $user->save();
      $status = "verified";
    } else {
      // Code is invalid
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
  
  public function editUser($action = null) {
    
    if (!$this->user->isConnected()) {
      Redirect::route('login');
    }
    
    if (Request::isMethod('post')) {
      $oldPassword = Input::get('old_password');
      $email = Input::get('email');
      $password = Input::get('password');
      $defaultSection = Input::get('default_section');
      
      // Check that the old password is valid
      $oldPasswordValid = User::testPassword($oldPassword, $this->user->password);
      
      if ($oldPasswordValid || $action == 'section') {
        if ($action == 'email') {
          // Validation for e-mail update
          $validator = Validator::make(
                  array("email" => $email),
                  array("email" => "required|email"),
                  array(
                      "email.required" => "Veuillez entrer votre adresse e-mail.",
                      "email.email" => "L'adresse e-mail n'est pas valide.",
                  )
          );
          if ($validator->fails()) {
            return Redirect::route('edit_user_email')->withInput()->withErrors($validator);
          } else {
            $this->user->changeEmail($email);
            // TODO send verification e-mail
            return Redirect::route('edit_user')->with('success_message', 'Votre adresse e-mail a été modifiée avec succès.');
          }
        } elseif ($action == 'password') {
          // Validation for password update
          $validator = Validator::make(
                  array("password" => $password),
                  array("password" => "required|min:6"),
                  array(
                      "password.required" => "Veuillez entrer un nouveau mot de passe.",
                      "password.min" => "Votre mot de passe doit compter au moins 6 caractères.",
                  )
          );
          if ($validator->fails()) {
            return Redirect::route('edit_user_password')->withInput()->withErrors($validator);
          } else {
            $this->user->changePassword($password);
            return Redirect::route('edit_user')->with('success_message', 'Votre mot de passe a été modifiée avec succès.');
          }
        } elseif ($action == 'section') {
          // Validation for default section update
          $validator = Validator::make(
                  array("default_section" => $defaultSection),
                  array("default_section" => "required|integer")
          );
          if ($validator->fails()) {
            return Redirect::route('edit_user_section')->withInput()->withErrors($validator);
          } else {
            $this->user->changeDefaultSection($defaultSection);
            return Redirect::route('edit_user')->with('success_message', 'Votre section par défaut a été modifiée avec succès.');
          }
          
        }
      } else {
        return Redirect::to(URL::current()) ->withInput()->withErrors(array('old_password' => 'Le mot de passe actuel est erronné'));
      }
    }
    
    // Get section list for default section selection
    $sectionArray = array();
    if ($action == 'section') {
      $sections = Section::orderBy('position')->get();
      foreach ($sections as $section) {
        $sectionArray[$section->id] = $section->name;
      }
    }
    
    return View::make('user.edit', array(
        'action' => $action,
        'sections' => $sectionArray,
    ));
    
  }
  
  public function editEmail() {
    return $this->editUser('email');
  }
  
  public function editPassword() {
    return $this->editUser('password');
  }
  
  public function editSection() {
    return $this->editUser('section');
  }
  
  public function retrievePassword() {
    
  }
  
}
