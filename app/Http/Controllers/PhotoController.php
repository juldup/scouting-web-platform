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
use Illuminate\Support\Facades\Config;

/**
 * Photos can be posted on the website by the leaders. Photos are arrange by section in
 * photo albums that can be archived. Photos are private and only visible to members.
 * 
 * This controller provides the page to view the photos, and the pages to manage the albums
 * and the photos.
 */
class PhotoController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Displays the photo pages to view photo albums
   * 
   * @param boolean $showArchives  True if archived photos are being shown
   * @param integer $page  The archive page index (starts at 0) if archives are being shown
   */
  public function showPage($section_slug = null, $showArchives = false, $page = 0) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_PHOTOS)) {
      abort(404);
    }
    // Make sure the current user has access to the photos
    if (!$this->user->isMember() && !$this->user->isFormerLeader() && !Parameter::get(Parameter::$PHOTOS_PUBLIC)) {
      return Helper::forbiddenNotMemberResponse();
    }
    // Get the current album (if any)
    $albumId = Route::input('album_id');
    $pageSize = 10;
    $currentAlbum = null;
    if ($albumId) {
      $currentAlbum = PhotoAlbum::where('id', '=', $albumId)
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0)
              ->first();
      if ($currentAlbum->leaders_only && !$this->user->isLeader()) {
        return Helper::forbiddenResponse();
      }
      if (!$currentAlbum) {
        return redirect()->route('photos', array('section_slug' => $this->section->slug));
      }
      if ($currentAlbum->archived || $currentAlbum->date < Helper::oneYearAgo()) {
        // Showing an archived album
        $showArchives = true;
        // Determine current page, by counting albums before it in the list
        $index = count(PhotoAlbum::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0)
              ->where(function($query) use ($currentAlbum) {
                  // Counting albums later than current
                  $query->where('date', '>', $currentAlbum->date);
                  // Or with same date, but with a smaller id
                  $query->orWhere(function($query) use ($currentAlbum) {
                      $query->where('date', '=', $currentAlbum->date);
                      $query->where('id', '<', $currentAlbum->id);
                  });
              })
              ->orderBy('date', 'desc')
              ->get());
        // Deduce the page from the index
        $page = (int)($index / $pageSize);
      }
    }
    // Get the list of albums
    if ($showArchives) {
      // Showing archives
      $albums = PhotoAlbum::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0);
      if (!$this->user->isLeader()) {
        $albums = $albums->where('leaders_only', '=', false);
      }
      $albums = $albums
              ->orderBy('date', 'desc')
              ->orderBy('id')
              ->skip($page * $pageSize)
              ->take($pageSize)
              ->get();
      // Determine whether there are further archive pages
      $hasArchives = count(PhotoAlbum::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0)
              ->skip(($page + 1) * $pageSize)
              ->take(1)
              ->get());
    } else {
      // Showing album of this year
      $albums = PhotoAlbum::where('archived', '=', false)
              ->where('date', '>=', Helper::oneYearAgo())
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0);
      if (!$this->user->isLeader()) {
        $albums = $albums->where('leaders_only', '=', false);
      }
      $albums = $albums
              ->orderBy('position')
              ->get();
      // Determine whether there are archived albums
      $hasArchives = PhotoAlbum::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('date', '<', Helper::oneYearAgo());
              })
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0)
              ->take(1)
              ->count();
    }
    // Get list of photos
    $photos = null;
    if (count($albums)) {
      // If no album is selected, select the first one of the list
      if (!$currentAlbum) $currentAlbum = $albums[0];
      $photos = Photo::where('album_id', '=', $currentAlbum->id)
              ->orderBy('position')
              ->get();
    }
    // Make view
    return View::make('pages.photos.photos', array(
        'albums' => $albums,
        'current_album' => $currentAlbum,
        'photos' => $photos,
        'can_manage' => $this->user->can(Privilege::$POST_PHOTOS, $this->section),
        'showing_archives' => $showArchives,
        'has_archives' => $hasArchives,
        'next_page' => $page + 1,
        'downloadPartSize' => Config::get('app.photoAlbumDownloadPartSize'),
    ));
  }
  
  /**
   * [Route] Shows the photo page with an album selected
   */
  public function showAlbum() {
    return $this->showPage();
  }
  
  /**
   * [Route] Shows the archived photos (with page number as route parameter)
   */
  public function showArchives(Request $request, $section_slug = null) {
    $page = $request->input('page');
    if (!$page) $page = 0;
    return $this->showPage($section_slug, true, $page);
  }
  
  /**
   * [Route] Outputs a photo for display or download
   * 
   * @param string $format  The format of the photo (original, thumbnail, preview: see Photo class)
   * @param integer $photo_id  The id of the photo to download
   */
  public function getPhoto($format, $photo_id) {
    // Make sure the user has access to photos
    if (!$this->user->isMember() && !$this->user->isFormerLeader() && !Parameter::get(Parameter::$PHOTOS_PUBLIC)) {
      return Helper::forbiddenResponse();
    }
    // Get photo object
    $photo = Photo::find($photo_id);
    if (!$photo) abort(404, "La photo n'existe plus.");
    // Make sure the photo is not for leader only and the current user is not a leader
    $album = PhotoAlbum::where('id', '=', $photo->album_id)->first();
    if (!$album || (!$this->user->isLeader() && $album->leaders_only)) {
      return Helper::forbiddenResponse();
    }
    // Get photo
    $path = $photo->getPhotoPath($format);
    if (file_exists($path)) {
      // Output photo
      return response(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    } else {
      // Photo not found
      abort(404, "La photo n'existe plus.");
    }
  }
  
  /**
   * [Route] Downloads a full photo album in a zip archive
   */
  public function downloadAlbum($album_id, $first_photo, $last_photo) {
    // Check that the user is allowed to download photos
    if (!$this->user->isMember() && !$this->user->isFormerLeader() && !Parameter::get(Parameter::$PHOTOS_PUBLIC)) {
      return Helper::forbiddenResponse();
    }
    // Gather photos
    $photos = Photo::where('album_id', '=', $album_id)
              ->orderBy('position')
              ->get();
    if (!count($photos)) {
      abort(404, "Cet album est vide.");
    }
    $album = PhotoAlbum::find($album_id);
    $albumName = Helper::removeSpecialCharacters($album->name);
    $outputFileName = "$albumName (photos $first_photo-$last_photo).zip";
    // Make sure the photo is not for leader only and the current user is not a leader
    if (!$album || (!$this->user->isLeader() && $album->leaders_only)) {
      return Helper::forbiddenResponse();
    }
    // Create zip file in temporary folder
    $filename = tempnam(storage_path("app/site_data/tmp/"), "photos.zip");
    $zip = new ZipArchive();
    $zip->open($filename);
    // Add each photo in the zip file
    $totalSize = 0;
    foreach ($photos as $photo) {
      // Only add up to last photo
      $last_photo--;
      if ($last_photo < 0) {
        break;
      }
      // Only start at first photo
      $first_photo--;
      if ($first_photo > 0) {
        continue;
      }
      // Add photo
      $photoFilename = $photo->getPhotoPath(Photo::$FORMAT_ORIGINAL);
      if (file_exists($photoFilename)) {
        $totalSize += filesize($photoFilename);
        $zip->addFile($photoFilename, $photo->filename);
        if ($totalSize >= 314572800) {
          // Bigger than 300 MB, stop adding files
          break;
        }
      }
    }
    $zip->close();
    if (file_exists($filename)) {
      LogEntry::log("Photos", "Téléchargement d'un album", array("Album" => PhotoAlbum::find($album_id)->name));
      // Output file
      Helper::outputBigFile($filename, $outputFileName);
      // Delete file
      unlink($filename);
      // Output has already be made, end script
      exit();
    } else {
      // An error has occured
      LogEntry::error("Photos", "Erreur lors du téléchargement d'un album", "Le fichier n'a pas pu être créé");
      throw abort(500, "Une erreur est survenue.");
    }
  }
  
  /**
   * [Route] Shows the photo management page (the page where albums can be created and managed)
   */
  public function showEdit() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_PHOTOS)) {
      abort(404);
    }
    // Make sure the user has access to this page
    if (!$this->user->can(Privilege::$POST_PHOTOS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Get the list of albums
    $albums = PhotoAlbum::where('archived', '=', false)
            ->where('date', '>=', Helper::oneYearAgo())
            ->where('section_id', '=', $this->section->id)
            ->orderBy('position')
            ->get();
    // Get selected album if any (i.e. the album that was just created)
    $selectedAlbumId = Session::get('album_id', null);
    // Make view
    return View::make('pages.photos.editPhotos', array(
        'albums' => $albums,
        'selected_album_id' => $selectedAlbumId,
    ));
  }
  
  /**
   * [Route] Creates a new photo album
   */
  public function createPhotoAlbum() {
    // Make sure the user can create albums
    if (!$this->user->can(Privilege::$POST_PHOTOS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Create album
    $album = null;
    try {
      $album = PhotoAlbum::create(array(
          'section_id' => $this->section->id,
          'name' => "Album du " . Helper::dateToHuman(date('Y-m-d')),
          'date' => date('Y-m-d'),
      ));
      $album->position = $album->id;
      $album->save();
      LogEntry::log("Photos", "Création d'un nouvel album", array("Album" => $album->name));
    } catch (Exception $ex) {
      Log::error($ex);
      // The album could not be created
      if ($album) $album.delete();
      LogEntry::error("Photos", "Erreur lors de la création d'un album", array("Erreur" => $ex->getMessage()));
      return redirect()->route('edit_photos')
              ->with('error_message', "Une erreur est survenue. L'album n'a pas pu être créé.");
    }
    // Redirect to photo management page with the newly created album selected
    return redirect()->route('edit_photos')
            ->with('album_id', $album->id);
  }
  
  /**
   * [Route] Ajax call to change the of the albums
   */
  public function changeAlbumOrder() {
    // Error message, ready to be sent
    $errorResponse = json_encode(array("result" => "Failure"));
    // Get list of albums in order
    $albumIdsInOrder = $request->input('album_order');
    $albumIdsInOrderArray = explode(" ", $albumIdsInOrder);
    // Retrieve albums
    $albums = PhotoAlbum::where('archived', '=', false)
            ->where(function($query) use ($albumIdsInOrderArray) {
              foreach ($albumIdsInOrderArray as $albumId) {
                $query->orWhere('id', '=', $albumId);
              }
            })->get();
    // Check that the number of albums corresponds
    if (count($albumIdsInOrderArray) != count($albums)) {
      return $errorResponse;
    }
    // Check that all albums belong to the same section
    $sectionId = 0;
    foreach ($albums as $album) {
      if (!$sectionId) $sectionId = $album->section_id;
      if ($sectionId != $album->section_id) {
        return $errorResponse;
      }
    }
    if (!$sectionId) return $errorResponse;
    // Check that the user has the right to modify this section's albums
    if (!$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Get the list of positions
    $positions = array();
    foreach ($albums as $album) {
      $positions[] = $album->position;
    }
    sort($positions);
    // Assign new positions
    foreach ($albums as $album) {
      // Get new order of this album
      $index = array_search($album->id, $albumIdsInOrderArray);
      if ($index === false) return $errorResponse;
      // Assign position
      $album->position = $positions[$index];
    }
    // Save all albums
    foreach ($albums as $album) {
      try {
        $album->save();
      } catch (Exception $ex) {
        Log::error($ex);
        return $errorResponse;
      }
    }
    // Log
    LogEntry::log("Photos", "Réordonnancement des albums"); // TODO improve log message
    // Return success response
    return json_encode(array('result' => "Success"));
  }
  
  /**
   * [Route] Toggle album privacy (leaders only / all members)
   */
  public function toggleAlbumPrivacy($album_id, $status) {
    // Get the album
    $album = PhotoAlbum::find($album_id);
    if (!$album) abort(404, "Cet album n'existe pas.");
    $sectionId = $album->section_id;
    // Make sure the user can delete this album
    if (!$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    // Toggle privacy
    $album->leaders_only = ($status ? true : false);
    $album->save();
    // Return to album page with success message
    return redirect()->route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('success_message',
                      ($status ? "L'album est maintenant visible uniquement par les animateurs." :
        "L'album est maintenant visible par tous les membres de l'unité."));
  }
  
  /**
   * [Route] Deletes an empty album
   */
  public function deletePhotoAlbum($album_id) {
    // Get the album
    $album = PhotoAlbum::find($album_id);
    if (!$album) abort(404, "Cet album n'existe pas.");
    $sectionId = $album->section_id;
    // Make sure the user can delete this album
    if (!$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    // Make sure the album is empty
    if ($album->photo_count != 0) {
      return redirect()->route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('error_message', "Cet album n'est pas vide et ne peut pas être supprimé.");
    }
    // Delete the album
    try {
      $album->delete();
      LogEntry::log("Photos", "Suppression d'un album", array("Album" => $album->name));
    } catch (Exception $ex) {
      // Album could not be deleted, redirect with error message
      Log::error($ex);
      LogEntry::error("Photos", "Erreur lors de la suppression d'un album", array("Erreur" => $ex->getMessage()));
      return redirect()->route('edit_photos', array('section_slug' => Section::find($sectionId)->slug))
              ->with('error_message', "Une erreur est survenue. L'album n'as pas été supprimé.");
    }
    // Redirect with success message
    return redirect()->route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('success_message', "L'album a été supprimé.");
  }
  
  /**
   * [Route] Archives a photo album
   */
  public function archivePhotoAlbum($album_id) {
    // Get photo album
    $album = PhotoAlbum::find($album_id);
    if (!$album) abort(404, "Cet album n'existe pas.");
    $sectionId = $album->section_id;
    // Make sure the user can archive this photo album
    if (!$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    // Archive the album
    try {
      $album->archived = true;
      $album->save();
      LogEntry::log("Photos", "Archivage d'un album", array("Album" => $album->name));
    } catch (Exception $ex) {
      // An error has occured
      Log::error($ex);
      LogEntry::error("Photos", "Erreur lors de l'archivage d'un album", array("Erreur" => $ex->getMessage()));
      return redirect()->route('edit_photos', array('section_slug' => Section::find($sectionId)->slug))
              ->with('error_message', "Une erreur est survenue. L'album n'as pas été archivé.");
    }
    // Redirect with success message
    return redirect()->route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('success_message', "L'album a été archivé.");
  }
  
  /**
   * [Route] Ajax call to rename a photo album
   */
  public function changeAlbumName(Request $request) {
    // Error message ready to be sent
    $errorResponse = json_encode(array("result" => "Failure"));
    // Get album and new name
    $albumId = $request->input('id');
    $newName = $request->input('value');
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    // Make sure that the user can change the name of this album and that the input data is correct
    if (!$sectionId || !$newName || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Update the album name
    try {
      $album->name = $newName;
      $album->save();
      LogEntry::log("Photos", "Renommage d'un album", array("Album" => $album->name)); // TODO improve log message
    } catch (Exception $ex) {
      // Error
      Log::error($ex);
      LogEntry::error("Photos", "Erreur lors du renommage d'un album", array("Erreur" => $ex->getMessage()));
      return $errorResponse;
    }
    // Success
    return json_encode(array('result' => "Success"));
  }
  
  /**
   * [Route] Shows the page to edit the photos of a given album
   */
  public function showEditAlbum($album_id) {
    // Get album
    $album = PhotoAlbum::find($album_id);
    if (!$album) abort(404, "Cet album n'existe pas.");
    // Make sure the user can edit this album
    if (!$this->user->can(Privilege::$POST_PHOTOS, $album->section_id)) {
      return Helper::forbiddenResponse();
    }
    // If the current section does not correspond to the album's section (i.e. a new tab has been selected), redirect to edit photos page
    if ($album->section_id != $this->section->id) {
      return redirect()->route('edit_photos');
    }
    // Get album's photo list
    $photos = Photo::where('album_id', '=', $album->id)
              ->orderBy('position')
              ->get();
    // Make view
    return View::make('pages.photos.editAlbum', array(
        'album' => $album,
        'photos' => $photos,
    ));
  }
  
  /**
   * [Route] Ajax call to reorder the photos of an album
   */
  public function changePhotoOrder(Request $request) {
    // Error response ready to be sent
    $errorResponse = json_encode(array("result" => "Failure"));
    // Get new order from input
    $photoIdsInOrder = $request->input('photo_order');
    $photoIdsInOrderArray = explode(" ", $photoIdsInOrder);
    // Retrieve photos
    $photos = Photo::where(function($query) use ($photoIdsInOrderArray) {
              foreach ($photoIdsInOrderArray as $photoId) {
                $query->orWhere('id', '=', $photoId);
              }
            })->get();
    // Check that the number of photos corresponds
    if (count($photoIdsInOrderArray) != count($photos)) {
      return $errorResponse;
    }
    // Check that all albums belong to the same album
    $albumId = 0;
    foreach ($photos as $photo) {
      if (!$albumId) $albumId = $photo->album_id;
      if ($albumId != $photo->album_id) {
        return $errorResponse;
      }
    }
    if (!$albumId) return $errorResponse;
    // Make sure that the user has the right to modify this album
    $album = PhotoAlbum::find($albumId);
    if (!$album || !$this->user->can(Privilege::$POST_PHOTOS, $album->section_id)) {
      return $errorResponse;
    }
    // Get list of positions
    $positions = array();
    foreach ($photos as $photo) {
      $positions[] = $photo->position;
    }
    sort($positions);
    // Assign new positions
    foreach ($photos as $photo) {
      // Get new order of this album
      $index = array_search($photo->id, $photoIdsInOrderArray);
      if ($index === false) return $errorResponse;
      // Assign position
      $photo->position = $positions[$index];
    }
    // Save all photos
    foreach ($photos as $photo) {
      try {
        $photo->save();
      } catch (Exception $ex) {
        Log::error($ex);
        return $errorResponse;
      }
    }
    // Everything went well
    LogEntry::log("Photos", "Réordonnancement des photos", array("Album" => $album->name));
    return json_encode(array('result' => "Success"));
  }
  
  /**
   * [Route] Ajax call to delete a photo
   */
  public function deletePhoto(Request $request) {
    // Get photo
    $photoId = $request->input('photo_id');
    $photo = Photo::find($photoId);
    $album = $photo ? PhotoAlbum::find($photo->album_id) : null;
    $sectionId = $album ? $album->section_id : null;
    // Make sure the user can delete this photo
    if ($sectionId && $this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      // Delete photo
      try {
        $photo->delete();
        // Update album photo count
        try {
          $album->updatePhotoCount();
        } catch (Exception $ex) {
          Log::error($ex);
          // Never mind
        }
        // Remove actual files
        try {
          unlink($photo->getPhotoPath(Photo::$FORMAT_ORIGINAL));
        } catch (Exception $e) {
          Log::error($e);
          // The photo was not removed from the filesystem, do nothing special
        }
        try {
          unlink($photo->getPhotoPath(Photo::$FORMAT_PREVIEW));
        } catch (Exception $e) {
          Log::error($e);
          // The photo was not removed from the filesystem, do nothing special
        }
        try {
          unlink($photo->getPhotoPath(Photo::$FORMAT_THUMBNAIL));
        } catch (Exception $e) {
          Log::error($e);
          // The photo was not removed from the filesystem, do nothing special
        }
        // Log
        LogEntry::log("Photos", "Suppression d'une photo", array("Album" => $album->name, "Photo" => $photo->filename));
        // Return success response
        return json_encode(array('result' => "Success"));
      } catch (Exception $ex) {
        // Do nothing
        Log::error($ex);
      }
    }
    // If reaching here, the photo has not been deleted
    LogEntry::error("Photos", "Erreur lors de la suppression d'une photo", array("Album" => $album ? $album->name : "?", "Photo" => $photo ? $photo->filename : "?"));
    return json_encode(array('result' => "Failure"));
  }
  
  /**
   * [Route] Ajax call to add a photo to an album
   */
  public function addPhoto(Request $request) {
    // Get input data
    $file = $request->file('file');
    $uploadId = $request->input('id', 0);
    $albumId = $request->input('album_id');
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    // Prepare error response
    $errorResponse = json_encode(array("result" => "Failure", "id" => $uploadId));
    // Check if user is allowed to upload a photo
    if (!$sectionId || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Check that the image exists
    if ($file == null || !$file->getSize()) {
      return $errorResponse;
    }
    try {
      // Create photo
      $photo = Photo::create(array(
          'album_id' => $albumId,
          'filename' => $file->getClientOriginalName(),
      ));
      // Set position to last
      $photo->position = $photo->id;
      $photo->save();
      // Move file
      $file->move($photo->getPhotoPathFolder(Photo::$FORMAT_ORIGINAL), $photo->getPhotoPathFilename());
      // Create thumbnail and preview pictures
      $photo->createThumbnailPicture();
      $photo->createPreviewPicture();
      LogEntry::log("Photos", "Ajout d'une photo", array("Album" => $album->name, "Photo" => $photo->filename));
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Photos", "Erreur lors de l'ajout d'une photo", array("Erreur" => $ex->getMessage()));
      // Revert if possible
      try {
        if ($photo != null) $photo->delete();
      } catch (Exception $e) {
        Log::error($e);
      }
      return $errorResponse;
    }
    // Update album photo count
    try {
      $album->updatePhotoCount();
    } catch (Exception $ex) {
      Log::error($ex);
      // Never mind
    }
    // Return success response
    return json_encode(array(
        "result" => "Success",
        "id" => $uploadId,
        "photo_id" => $photo->id,
        "photo_thumbnail_url" => $photo->getThumbnailURL(),
    ));
  }
  
  /**
   * [Route] Ajax call to update a photo's caption
   */
  public function changePhotoCaption(Request $request) {
    // Prepare error response
    $errorResponse = json_encode(array("result" => "Failure"));
    // Get input data
    $photoId = $request->input('id');
    $newCaption = $request->input('value', "");
    $photo = Photo::find($photoId);
    $albumId = $photo ? $photo->album_id : null;
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    // Make sure the user can edit this photo
    if (!$sectionId || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Update the caption
    try {
      $photo->caption = $newCaption;
      $photo->save();
      LogEntry::log("Photos", "Changement de la description d'une photo", array("Album" => $album->name, "Photo" => $photo->filename, "Description" => $newCaption)); // TODO improve log message
    } catch (Exception $ex) {
      // Error
      Log::error($ex);
      LogEntry::error("Photos", "Erreur lors du changement de la description d'une photo", array("Erreur" => $ex->getMessage()));
      return $errorResponse;
    }
    // Success
    return json_encode(array('result' => "Success"));
  }
  
  /**
   * [Route] Ajax call to rotate a photo
   */
  public function rotatePhoto(Request $request) {
    // Prepare error response
    $errorResponse = json_encode(array("result" => "Failure"));
    // Get input data
    $photoId = $request->input('photo_id');
    $clockwise = $request->input('clockwise') == "true" ? true : false;
    $photo = Photo::find($photoId);
    $albumId = $photo ? $photo->album_id : null;
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    // Make sure the user can update this photo
    if (!$sectionId || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Rotate the photo
    try {
      $photo->rotate($clockwise);
    } catch (Exception $e) {
      Log::error($e);
      // Error
      return $errorResponse;
    }
    // Success
    return json_encode(array("result" => "Success"));
  }
  
}
