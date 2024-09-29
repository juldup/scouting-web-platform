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
 * This controller provides routes to upload content-edited page images
 * and to output these image for the visitors.
 */
class PageImageController extends BaseController {
  
  /**
   * [Route] Outputs the given image
   */
  public function getImage($image_id) {
    $image = PageImage::find($image_id);
    if ($image) {
      $path = $image->getPath();
      return response(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
  /**
   * [Route] Adds an image to the image library of a page
   */
  public function uploadImage(Request $request) {
    if (!$this->user->isLeader()) return;
    try {
      // Get file input data
      $file = $request->file('upload');
      // Make sure the file has been uploaded
      if (!$file->getSize()) {
        return response()->json([
            'uploaded'=> 0,
            'error' => ['message' => "Le fichier est trop gros et n'a pas pu être ajouté."]
          ]);
      }
      // Create the image object in the database
      $image = PageImage::create(array(
          'original_name' => $file->getClientOriginalName(),
      ));
      // Save the image in the filesystem
      $file->move($image->getPathFolder(), $image->getPathFilename());
      // Log
      LogEntry::log("Page", "Ajout d'une image à la librairie d'images", array("Image" => $image->original_name));
      // Return the response
      return response()->json([
          'fileName' => $image->getPathFilename(),
          'uploaded'=> 1,
          'url' => $image->getURL()
        ]);
    } catch (Exception $e) {
      Log::error($e);
      return response()->json([
          'uploaded'=> 0,
          'error' => ['message' => "Une erreur est survenue."]
        ]);
    }
  }
  
  
  
  /**
   * [Route] Returns the image corresponding to the type
   */
  public function getStaticImage(Request $request, $filename) {
    $type = Helper::removeSpecialCharacters($filename); // TODO better security
    $path = "../resources/images/" . $filename;
    return response(file_get_contents($path), 200, array(
        "Content-Type" => "image",
        "Content-Length" => filesize($path),
    ));
  }
  
}
