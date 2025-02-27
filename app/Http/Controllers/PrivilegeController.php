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
 * There are a set of privileges (i.e. allowed actions) on the site, and each
 * leader can be assigned a subset of them individually.
 * 
 * This controller provides a tool to assign the privileges to the leaders.
 */
class PrivilegeController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows the leader privilege management page
   */
  public function showEdit() {
    // Make sure the user is a leader and therefore has access to this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Check if the user can edit the other leader's privileges or can only view this page in readonly mode
    $canEditPrivileges = $this->user->can(Privilege::$EDIT_LEADER_PRIVILEGES, $this->section);
    // Get the leaders of the currently selected section
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    // List of privileges
    $privilegeList = Privilege::getPrivilegeArrayByCategory($this->section->id != 1);
    // List of assigned privileges
    $privilegeRawData = Privilege::all();
    // Construct privilege table
    $privilegeTable = array();
    foreach ($privilegeList as $category) {
      foreach ($category as $privilegeData) {
        $privilege = $privilegeData['privilege'];
        $sOrU = $privilegeData['scope'];
        $privilegeTable[$privilege['id']] = array();
        foreach ($leaders as $leader) {
          $privilegeTable[$privilege['id']][$leader->id] = array(
              'S' => array(
                  'state' => false,
                  'can_change' => $canEditPrivileges && $this->user->can($privilege, $this->section) && !$this->user->isOwnerOfMember($leader->id),
              ),
              'U' => array(
                  'state' => false,
                  'can_change' => $canEditPrivileges && $this->user->can($privilege, 1) && !$this->user->isOwnerOfMember($leader->id),
              ),
          );
        }
      }
    }
    // Set state to 'true' for all assigned privileges
    foreach ($privilegeRawData as $privilegeData) {
      if (isset($privilegeTable[$privilegeData->operation][$privilegeData->member_id])) {
        $privilegeTable[$privilegeData->operation][$privilegeData->member_id][$privilegeData->scope]['state'] = true;
      }
    }
    // Make view
    return View::make('pages.leader.editPrivileges', array(
        'leaders' => $leaders,
        'privilege_list' => $privilegeList,
        'privilege_table' => $privilegeTable,
    ));
  }
  
  /**
   * [Route] Ajax call to update a list of privilege assignment
   */
  public function updatePrivileges(Request $request) {
    // Get the list of changes from the input
    $privileges = $request->all();
    $error = false;
    // Update each of them in the database
    $logChanges = array();
    $logCount = 1;
    foreach ($privileges as $privilegeData=>$state) {
      try {
        // Get input data
        $privilegeData = explode(":", $privilegeData);
        $operation = str_replace("_", " ", $privilegeData[0]);
        $scope = $privilegeData[1];
        $leaderId = $privilegeData[2];
        $leader = Member::find($leaderId);
        // A leader cannot grant privileges to themselves
        if ($this->user->isOwnerOfMember($leaderId)) {
          $error = true;
          continue;
        }
        // A leader can only grant privileges that they have
        if ($this->user->can(Privilege::$EDIT_LEADER_PRIVILEGES, $leader->section_id)
                && $this->user->can($operation, $leader->section_id)) {
          $state = $state == "true" ? true : false;
          // Update privilege state in database
          Privilege::set($operation, $scope, $leaderId, $state);
          $logChanges["Changement " . $logCount++] = $leader->getFullName() . ($state ? " peut : " : " ne peut plus : ") . $operation;
        } else {
          $error = true;
        }
      } catch (Exception $e) {
        Log::error($e);
        $error = true;
      }
    }
    // Log
    LogEntry::log("Privilèges", "Changement des privilèges", $logChanges); // TODO improve log message
    // Return response
    if ($error) return json_encode(array("result" => "Failure"));
    else return json_encode(array("result" => "Success"));
  }
  
}
