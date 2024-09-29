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
 * This selects random photos every day and presents them
 */
class DailyPhotoController extends BaseController {
  
  /**
   * [Route] Displays the daily photo page
   */
  public function showPage($date = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_DAILY_PHOTOS) || $date > date('Y-m-d')) {
      abort(404);
    }
    // Make sure the current user has access to the photos
    if (!$this->user->isMember() && !$this->user->isFormerLeader() && !Parameter::get(Parameter::$PHOTOS_PUBLIC)) {
      return Helper::forbiddenNotMemberResponse();
    }
    $photos = $this->selectDailyPhotos($date);
    $photoList = array();
    foreach ($photos as $photo) {
      $album = PhotoAlbum::find($photo->album_id);
      $albumSection = Section::find($album->section_id);
      if ($album && $albumSection) {
        $photoList[] = array(
            'photoUrl' => $photo->getPreviewUrl(),
            'albumUrl' => URL::route('photo_album', array('album_id' => $album->id, 'section_slug' => $albumSection->slug)),
            'albumName' => $album->name,
        );
      }
    }
    // Make view
    return View::make('pages.photos.dailyPhoto', array(
        'photos' => $photoList,
        'date' => $date,
        'yesterdayUrl' => URL::route('daily_photos', array('date' => date('Y-m-d', strtotime('-1 day', strtotime($date ? $date : date('Y-m-d')))))),
    ));
  }
  
  /**
   * Returns an array containing the photos selected for the given date
   */
  private function selectDailyPhotos($date) {
    $dailyPhotos = DailyPhoto::getDailyPhotos($date);
    $photos = array();
    foreach ($dailyPhotos as $dailyPhoto) {
      $selectedPhoto = Photo::find($dailyPhoto->photo_id);
      if ($selectedPhoto) {
        $photos[] = $selectedPhoto;
      }
    }
    return $photos;
  }
  
}
