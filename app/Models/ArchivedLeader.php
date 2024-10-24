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

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use App\Helpers\Helper;

/**
 * This Eloquent class represents the data of a former leader. Each leader of
 * a previous year has one instance of this class for each year they were a leader.
 * 
 * Leaders are being archived on the first modification made after July 1st.
 * 
 * Columns:
 *   - member_id:            The active member if the leader is still registered, null if the leader has left the unit
 *   - year:                 The year this member was a leader
 *   - first_name:           The first name of the leader
 *   - last_name:            The last name of the leader
 *   - gender:               The gender M/F of the leader
 *   - totem:                The totem of the leader (if any)
 *   - quali:                The quali of the leader (if any)
 *   - section_id:           The section this leader was in
 *   - phone_member:         The phone number of the leader
 *   - phone_member_private: Whether the phone number is private
 *   - email_member:         The e-mail address of the leader
 *   - leader_in_charge:     Whether they were leader in charge of the section
 *   - leader_name:          Their leader name (how they were called in the section)
 *   - leader_description:   A short description of the leader
 *   - leader_role:          The role the leader was playing in the section
 *   - has_picture:          Whether there is a picture for this leader
 *   - picture_filename:     The file name of the picture (if any)
 */
class ArchivedLeader extends Model {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Creates archived leader entries for all leaders of the previous year if they don't exist yet
   */
  public static function archiveLeadersIfNeeded() {
    $lastYear = DateHelper::getLastYearForArchiving();
    $count = ArchivedLeader::where('year', '=', $lastYear)
            ->take(1)
            ->count();
    if (!$count) {
      self::archiveLeaders($lastYear);
      LogEntry::log("Animateurs", "Archivage des animateurs", array("Année" => $lastYear));
    }
  }
  
  /**
   * Creates an archived leader entry for each active leaders, with the given $lastYear as year
   */
  private static function archiveLeaders($lastYear) {
    $leaders = Member::where('validated', '=', true)
            ->where('is_leader', '=', true)
            ->get();
    foreach ($leaders as $leader) {
      ArchivedLeader::create(array(
          'member_id' => $leader->id,
          'year' => $lastYear,
          'first_name' => $leader->first_name,
          'last_name' => $leader->last_name,
          'gender' => $leader->gender,
          'totem' => $leader->totem,
          'quali' => $leader->quali,
          'section_id' => $leader->section_id,
          'phone_member' => $leader->phone_member,
          'phone_member_private' => $leader->phone_member_private,
          'email_member' => $leader->email_member,
          'leader_in_charge' => $leader->leader_in_charge,
          'leader_name' => $leader->leader_name,
          'leader_description' => $leader->leader_description,
          'leader_role' => $leader->leader_role,
          'has_picture' => $leader->has_picture,
          'picture_filename' => $leader->getPicturePathFilename(),
      ));
    }
  }
  
  /**
   * Returns the URL of the picture of this leader
   */
  public function getPictureURL() {
    return URL::route('get_archived_leader_picture', array('archived_leader_id' => $this->id));
  }
  
  /**
   * Returns the path of the picture in the file system
   */
  public function getPicturePath($pictureFilename = null) {
    if (!$pictureFilename) $pictureFilename = $this->picture_filename;
    return storage_path(Member::$PICTURE_FOLDER_PATH) . $pictureFilename;
  }
  
  /**
   * Checks whether the input data is valid. If it is valid, returns
   * the an array containg the data. If it is invalid, returns a string
   * containing an error message.
   */
  public static function checkInputData(Request $request, $canEditIdentity = true, $canEditContact = true, $canEditSection = true, $canEditTotem = true, $canEditLeader = true) {
    // Get data from input
    $firstName = $request->input('first_name');
    $lastName = $request->input('last_name');
    $gender = $request->input('gender');
    $leaderName = $request->input('leader_name');
    $leaderInCharge = $request->input('leader_in_charge') ? true : false;
    $leaderDescription = $request->input('leader_description');
    $leaderRole = $request->input('leader_role');
    $sectionId = $request->input('section');
    $phoneMemberUnformatted = $request->input('phone_member');
    $phoneMemberPrivate = $request->input('phone_member_private');
    $emailMember = strtolower($request->input('email_member'));
    $totem = $request->input('totem');
    $quali = $request->input('quali');
    // Error message is initially empty
    $errorMessage = "";
    // Check all fields for errors
    // First name
    if (!$firstName)
      $errorMessage .= "Il manque le prénom. ";
    elseif (!Helper::hasCorrectCapitals($firstName, true))
      $errorMessage .= "L'usage des majuscules dans le prénom n'est pas correct. ";
    // Last name
    if (!$lastName)
      $errorMessage .= "Il manque le nom de famille. ";
    elseif (!Helper::hasCorrectCapitals($lastName, false))
      $errorMessage .= "L'usage des majuscules dans le nom de famille n'est pas correct. ";
    // Gender
    if ($gender != 'M' && $gender != 'F')
      $errorMessage .= "Le sexe n'est pas une entrée valide. ";
    // Phone number
    $phoneMember = Helper::formatPhoneNumber($phoneMemberUnformatted);
    if ($phoneMemberUnformatted && !$phoneMember)
      $errorMessage .= "Le numéro de GSM du scout \"$phoneMemberUnformatted\" n'est pas correct. ";
    // E-mail address
    if ($emailMember && !filter_var($emailMember, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail du scout \"$emailMember\" n'est pas valide. ";
    // Totem
    if ($totem && !Helper::hasCorrectCapitals($totem))
      $errorMessage .= "L'usage des majuscules dans le totem n'est pas correct (il doit commencer par une majuscule). ";
    // Leader name
    if (!$leaderName)
      $errorMessage .= "Il manque le nom d'animateur. ";
    elseif (!Helper::hasCorrectCapitals ($leaderName, true))
      $errorMessage .= "L'usage des majuscule dans le nom d'animateur n'est pas correct. ";
    // Return error message or array containing the data
    if ($errorMessage) {
      return $errorMessage;
    } else {
      return array(
          'first_name' => $firstName,
          'last_name' => $lastName,
          'gender' => $gender,
          'leader_name' => $leaderName,
          'leader_in_charge' => $leaderInCharge,
          'leader_description' => $leaderDescription,
          'leader_role' => $leaderRole,
          'section_id' => $sectionId,
          'phone_member' => $phoneMember,
          'phone_member_private' => $phoneMemberPrivate,
          'email_member' => $emailMember,
          'totem' => $totem,
          'quali' => $quali,
      );
    }
  }
  
  /**
   * Saves the uploaded picture to the file system and updates the member
   * to mark it as having a leader picture. Returns this member instance, or
   * a string in case of error.
   */
  public function uploadPictureFromInput(Request $request) {
    // Get picture file
    $pictureFile = $request->file('picture');
    if ($pictureFile) {
      if (!$pictureFile->getSize()) {
        // An upload error has occured
        return false;
      } else {
        try {
          // Resize image
          $image = new Resizer($pictureFile->getRealPath());
          $image->resizeImage(256, 256, "crop");
          // Save image
          $pictureFilename =  "archive-" . $this->id . ".picture";
          $image->saveImage($this->getPicturePath($pictureFilename));
          // Update member
          $this->has_picture = true;
          $this->picture_filename = $pictureFilename;
          $this->save();
          return $this;
        } catch (Exception $e) {
          Log::error($e);
          // An error has occured while saving the picture
          return false;
        }
      }
    }
    // There is no picture file
    return true;
  }
  
}
