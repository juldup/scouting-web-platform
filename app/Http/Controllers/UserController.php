<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\CalendarPDF;
use App\Helpers\DateHelper;
use App\Helpers\ElasticsearchHelper;
use App\Helpers\EnvelopsPDF;
use App\Helpers\Form;
use App\Helpers\HealthCardPDF;
use App\Helpers\Helper;
use App\Helpers\ListingComparison;
use App\Helpers\ListingPDF;
use App\Helpers\Resizer;
use App\Helpers\ScoutMailer;
use App\Models\Absence;
use App\Models\AccountingItem;
use App\Models\AccountingLock;
use App\Models\ArchivedLeader;
use App\Models\Attendance;
use App\Models\BannedEmail;
use App\Models\CalendarItem;
use App\Models\Comment;
use App\Models\DailyPhoto;
use App\Models\Document;
use App\Models\Email;
use App\Models\EmailAttachment;
use App\Models\GuestBookEntry;
use App\Models\HealthCard;
use App\Models\Link;
use App\Models\LogEntry;
use App\Models\Member;
use App\Models\MemberHistory;
use App\Models\News;
use App\Models\Page;
use App\Models\PageImage;
use App\Models\Parameter;
use App\Models\PasswordRecovery;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\PendingEmail;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Privilege;
use App\Models\Section;
use App\Models\Suggestion;
use App\Models\TemporaryRegistrationLink;
use App\Models\User;

/**
 * This controller manages the user account creation, modification and validation
 * as well as the login/logout.
 */
class UserController extends BaseController {
  
  /**
   * [Route] Shows the login page that contains a form to log in
   * and a form to create a new user account
   */
  public function login() {
    // The action is a session flash variable that can be 'login' (in case of a login error),
    // 'create' (in case of a creation error) or undefined
    $action = Session::get('action', null);
    // Save referrer to session if need be, to redirect back to the previous page
    if (!$action && URL::previous() != URL::current()) {
      Session::put('login_referrer', URL::previous());
    }
    // Make view
    return View::make('user.login', array(
        "error_login" => $action == 'login',
        "error_create" => $action == "create"
    ));
  }
  
  /**
   * [Route] Logs the user in if the login data is valid
   */
  public function submitLogin(Request $request) {
    // Get input
    $username = $request->input('login_username');
    $password = $request->input('login_password');
    $remember = $request->input('login_remember');
    // Find user that corresponds to this login data
    $user = User::getWithUsernameAndPassword($username, $password);
    if ($user) {
      // Log user in
      Session::put('user_id', $user->id);
      // Save cookies to automatically log the user in on the next visits
      if ($remember) {
        $cookiePassword = User::getCookiePassword($password, $user->password);
        Cookie::queue(User::getCookieUsernameName(), $username, 365 * 24 * 60);
        Cookie::queue(User::getCookiePasswordName(), $cookiePassword, 365 * 24 * 60);
      }
      // Redirect to previous page
      $referrer = Session::get('login_referrer', URL::route('home'));
      Session::forget('login_referrer');
      return redirect($referrer);
    }
    // No matching user
    return redirect()->route('login')
            ->withInput()
            ->with('action', 'login');
  }
  
  /**
   * [Route] Logs the user out, forgetting the login cookies
   */
  public function logout() {
    // Unlog user
    Session::flush();
    // Remove cookies
    Cookie::queue(User::getCookieUsernameName(), null, -1);
    Cookie::queue(User::getCookiePasswordName(), null, -1);
    // Redirect to previous page
    return back();
  }
  
  /**
   * [Route] Creates a new user with the input data
   */
  public function create(Request $request) {
    // Retrieve data from form
    $username = $request->input('create_username');
    $email = trim(strtolower($request->input('create_email')));
    $password = $request->input('create_password');
    $remember = $request->input('create_remember');
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
      return redirect(URL::route('login') . '#nouvel-utilisateur')->withInput()->withErrors($validator)->with('action', 'create');
    }
    // Validation passed, create user
    $user = User::createWith($username, $email, $password);
    // Send verification e-mail
    $emailContent = Helper::renderEmail('createUser', $email, array(
        'username' => $username,
        'verification_code' => $user->verification_code,
    ));
    $pendingEmail = PendingEmail::create(array(
        'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] Validez votre compte d'utilisateur",
        'raw_body' => $emailContent['txt'],
        'html_body' => $emailContent['html'],
        'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
        'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
        'recipient' => $email,
        'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
    ));
    $pendingEmail->send();
    // Log user in
    Session::put('user_id', $user->id);
    // Save cookies
    if ($remember) {
      $cookiePassword = User::getCookiePassword($password, $user->password);
      Cookie::queue(User::getCookieUsernameName(), $username, 365 * 24 * 60);
      Cookie::queue(User::getCookiePasswordName(), $cookiePassword, 365 * 24 * 60);
    }
    // Redirect to previous page
    LogEntry::log("Utilisateur", "Nouvel utilisateur", array("Nom d'utilisateur" => $username, "E-mail" => $email));
    return redirect()->route('user_created');
  }
  
  /**
   * [Route] Shows a page confirming that the user account has been created
   */
  public function userCreated() {
    $referrer = Session::get('login_referrer', null);
    Session::forget('login_referrer');
    return View::make('user.userCreated', array(
        'referrer' => $referrer,
    ));
  }
  
  /**
   * [Route] Validates a user account with a verification code
   */
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
    // Make view
    return View::make('user.verify', array('status' => $status));
  }
  
  /**
   * [Route] Invalidates a user account
   */
  public function cancelVerification($code) {
    // Find user corresponding to verification code
    $user = User::where('verification_code', '=', $code)->first();
    if ($user) {
      // The user exists
      if ($user->verified) {
        // The user was already verified and cannot be invalidated
        $status = "already verified";
      } else {
        // Remove the user from the system
        $user->delete();
        $status = "canceled";
        LogEntry::log("Utilisateur", "Invalidation d'un compte d'utilisateur", array("Nom d'utilisateur" => $user->username, "E-mail" => $user->email));
      }
    } else {
      // The verification code is invalid
      $status = "unknown";
    }
    // Make view
    return View::make('user.verify', array('status' => $status));
  }
  
  /**
   * [Route] Shows the edit user data page
   * [Route] Updates new user data
   * 
   * @param string $action  The data that is being edited (null, 'section', 'password' or 'email')
   * @return type
   */
  public function editUser(Request $request, $action = null) {
    // Make sure the current user is logged in
    if (!$this->user->isConnected()) {
      return redirect()->route('login');
    }
    if ($request->isMethod('post')) {
      // Post: data is being modified
      // Get input data (some of these will be defined, depending on the action)
      $oldPassword = $request->input('old_password');
      $email = trim(strtolower($request->input('email')));
      $password = $request->input('password');
      $defaultSection = $request->input('default_section');
      // Check that the old password is valid (for 'section' action, there is no password)
      $oldPasswordValid = User::testPassword($oldPassword, $this->user->password);
      if ($oldPasswordValid || $action == 'section') {
        if ($action == 'email') {
          // Updating e-mail
          // Input data validation
          $validator = Validator::make(
                  array("email" => $email),
                  array("email" => "required|email"),
                  array(
                      "email.required" => "Veuillez entrer votre adresse e-mail.",
                      "email.email" => "L'adresse e-mail n'est pas valide.",
                  )
          );
          if ($validator->fails()) {
            return redirect(URL::route('edit_user_email') . "#modification")
                    ->withInput()
                    ->withErrors($validator);
          }
          // Data is valid, update user e-mail
          $this->user->changeEmail($email);
          // Send validation link by e-mail
          $emailContent = Helper::renderEmail('changeUserEmailAddress', $this->user->email, array(
              'verification_code' => $this->user->verification_code,
          ));
          $pendingEmail = PendingEmail::create(array(
              'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] Activer votre compte d'utilisateur",
              'html_body' => $emailContent['html'],
              'raw_body' => $emailContent['txt'],
              'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
              'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
              'recipient' => $this->user->email,
              'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
          ));
          $pendingEmail->send();
          // Redirect with success message
          LogEntry::log("Utilisateur", "Changement d'adresse e-mail", array("Utilisateur" => $this->user->username, "E-mail" => $email)); // TODO improve log message
          return redirect()->route('edit_user')
                  ->with('success_message', 'Votre adresse e-mail a été modifiée avec succès. Un lien de validation vous a été envoyé par e-mail.');
        } elseif ($action == 'password') {
          // Updating password
          // Input data validation
          $validator = Validator::make(
                  array("password" => $password),
                  array("password" => "required|min:6"),
                  array(
                      "password.required" => "Veuillez entrer un nouveau mot de passe.",
                      "password.min" => "Votre mot de passe doit compter au moins 6 caractères.",
                  )
          );
          if ($validator->fails()) {
            return redirect(URL::route('edit_user_password') . "#modification")
                    ->withInput()
                    ->withErrors($validator);
          }
          // Input data is valid, update password
          $this->user->changePassword($password);
          // Redirect with success message
          LogEntry::log("Utilisateur", "Changement de mot de passe", array("Utilisateur" => $this->user->username));
          return redirect()->route('edit_user')
                  ->with('success_message', 'Votre mot de passe a été modifié avec succès.');
        } elseif ($action == 'section') {
          // Updating default section
          // Input data validation
          $validator = Validator::make(
                  array("default_section" => $defaultSection),
                  array("default_section" => "required|integer")
          );
          if ($validator->fails()) {
            return redirect(URL::route('edit_user_section') . "#modification")
                    ->withInput()
                    ->withErrors($validator);
          }
          // Update user default section
          $this->user->changeDefaultSection($defaultSection);
          // Redirect with success message
          LogEntry::log("Utilisateur", "Changement de section par défaut", array("Utilisateur" => $this->user->username, "Section" => Section::find($defaultSection)->name)); // TODO improve log message
          return redirect()->route('edit_user')
                  ->with('success_message', 'Votre section par défaut a été modifiée avec succès.');
        }
      } else {
        // Old password required, but is erroneous, redirect with error message
        return redirect(URL::current() . "#modification")
                ->withInput()
                ->withErrors(array('old_password' => 'Le mot de passe actuel est erronné'));
      }
    }
    // No post data, simply showing page
    // Get section list for default section selection
    $sections = array();
    if ($action == 'section') {
      $sections = Section::getSectionsForSelect();
    }
    // Make view
    return View::make('user.edit', array(
        'action' => $action,
        'sections' => $sections,
    ));
  }
  
  /**
   * [Route] (GET) Shows the edit e-mail page 
   *         (POST) updates the e-mail
   */
  public function editEmail(Request $request) {
    return $this->editUser($request, 'email');
  }
  
  /**
   * [Route] (GET) Shows the edit password page 
   *         (POST) updates the password
   */
  public function editPassword(Request $request) {
    return $this->editUser($request, 'password');
  }
  
  /**
   * [Route] (GET) Shows the edit default section page 
   *         (POST) updates the default section
   */
  public function editSection(Request $request) {
    return $this->editUser($request, 'section');
  }
  
  /**
   * [Route] Sends the validation link by e-mail
   */
  public function resendValidationLink() {
    // Make sure the user is logged in
    if (!$this->user->isConnected()) {
      return Helper::forbiddenNotMemberResponse();
    }
    // Send validation link by e-mail
    $emailContent = Helper::renderEmail('resendValidationLink', $this->user->email, array(
        'username' => $this->user->username,
        'verification_code' => $this->user->verification_code,
    ));
    $pendingEmail = PendingEmail::create(array(
        'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] Activer votre compte d'utilisateur",
        'raw_body' => $emailContent['txt'],
        'html_body' => $emailContent['html'],
        'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
        'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
        'recipient' => $this->user->email,
        'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
    ));
    $pendingEmail->send();
    // Redirect with success message
    return redirect(URL::previous())
            ->with('success_message', 'Un e-mail avec le lien de validation vous a été envoyé.');
  }
  
  /**
   * [Route] (GET) Shows a page where the user can change their password if forgotten
   *         (POST) Sends an e-mail with a link to change their password
   */
  public function retrievePassword(Request $request) {
    // Post method
    if ($request->isMethod('post')) {
      // Get e-mail address
      $email = strtolower($request->input('email'));
      // Find user(s) with this e-mail address
      $users = User::where('email', '=', $email)->get();
      if (count($users)) {
        // There is at least one user with this e-mail
        // Generate password recovery instances for these users
        $passwordRecoveries = array();
        foreach ($users as $user) {
          $passwordRecoveries[$user->username] = PasswordRecovery::createForUser($user);
        }
        // Send e-mail
        $emailContent = Helper::renderEmail('passwordRecovery', $email, array(
            'recoveries' => $passwordRecoveries,
        ));
        $pendingEmail = PendingEmail::create(array(
            'subject' => 'Récupérer votre mot de passe',
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
            'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
            'recipient' => $email,
            'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
        ));
        $pendingEmail->send();
        // Redirect with success message
        LogEntry::log("Utilisateur", "Envoi d'un e-mail pour récupérer son mot de passe", array("Adresse e-mail" => $email));
        return redirect(URL::current())
                ->with('success_message', "Un e-mail a été envoyé à $email.");
      } else {
        // No user with this e-mail address, redirect with error message
        LogEntry::log("Utilisateur", "Adresse inconnue pour la récupération de mot de passe", array("Adresse e-mail" => $email));
        return redirect(URL::current())->with('error_message', "Aucun utilisateur n'est enregistré avec l'adresse $email.");
      }
    }
    // Get method
    // Make view
    return View::make('user.retrieve_password');
  }
  
  /**
   * [Route] (GET) Shows a page to update the user's password with a validation code
   *         (POST) Updates the user's password
   */
  public function changePassword(Request $request, $code) {
    if ($code != 'done') {
      // Find the password recovery entry corresponding to the code
      $passwordRecovery = PasswordRecovery::where('code', '=', $code)
              ->where('timestamp', '>=', time() - 24*3600) // A password recovery code is valid for 24 hours
              ->first();
      if (!$passwordRecovery) {
        // Password recovery entry not found
        $status = 'unknown';
      } else {
        // Password recovery entry found
        if ($request->isMethod('post')) {
          // Updating the password
          // Get password and make sure it is correct
          $password = $request->input('password');
          $validator = Validator::make(
                  array("password" => $password),
                  array("password" => "required|min:6"),
                  array(
                      "password.required" => "Veuillez entrer un nouveau mot de passe.",
                      "password.min" => "Votre mot de passe doit compter au moins 6 caractères.",
                  )
          );
          if ($validator->fails()) {
            // Validation failed, redirect with error message
            return redirect()->route('change_password', array('code' => $code))
                    ->withErrors($validator);
          } else {
            // Update password
            $user = $passwordRecovery->getUser();
            $user->changePassword($password);
            // Remove password recovery entry
            $passwordRecovery->delete();
            // Redirect with 'done' status
            LogEntry::log("Utilisateur", "Changement de mot de passe via e-mail", array("Utilisateur" => $user->username, "E-mail" => $user->email));
            return redirect()->route('change_password', array('code' => 'done'));
          }
        } else {
          // GET method, simply show a page with the form to update the password
          $status = 'normal';
        }
      }
    } else {
      // Code is 'done', the password has been updated
      $status = 'done';
    }
    // Make view
    return View::make('user.change_password', array(
        "status" => $status,
    ));
  }
  
  /**
   * [Route] Displays a page with the website user list
   */
  public function showUserList() {
    // Make sure the current user can access this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get users by order of last visit
    $users = User::orderBy('last_visit', 'desc')->get();
    // Make view
    return View::make('user.userList', array(
        'users' => $users,
        'can_delete' => ($this->user->can(Privilege::$DELETE_USERS)),
    ));
  }
  
  /**
   * [Route] Deletes a user
   */
  public function deleteUser($user_id) {
    // Make sure the current user can delete users
    if (!$this->user->can(Privilege::$DELETE_USERS)) {
      return Helper::forbiddenResponse();
    }
    // Find the user to delete
    $user = User::find($user_id);
    if ($user) {
      // Delete user
      try {
        $user->delete();
        // Redirect with success message
        LogEntry::log("Utilisateur", "Suppression d'un utilisateur", array("Utilisateur" => $user->username, "E-mail" => $user->email));
        return redirect()->route('user_list')
                ->with("success_message", "L'utilisateur " . $user->username . " a été supprimé du site.");
      } catch (Exception $e) {
        Log::error($e);
        LogEntry::error("Utilisateur", "Erreur lors de la suppression d'un utilisateur", array("Erreur" => $e->getMessage()));
      }
    }
    // User not found or another error, redirect with error message
    return redirect()->route('user_list')
            ->with('error_message', "Une erreur est survenue. L'utilisateur n'a pas été supprimé");
  }
  
}
