<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
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

/**
 * Provides a page that list changes that were recently made to the website content
 */
class RecentChangesController extends BaseController {
  
  /**
   * [Route] Displays the recent changes page
   */
  public function showPage() {
    // 60 days ago
    $startDate = date('Y-m-d', time() - 3600 * 24 * 60);
    // Container for the recent changes
    $recentChanges = array();
    // List recent news
    $news = News::where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();
    foreach ($news as $newsInstance) {
      $recentChanges[] = array(
          'datetime' => $newsInstance->created_at->toDateTimeString(),
          'date' => $newsInstance->created_at->format('Y-m-d'),
          'item' => $newsInstance->title,
          'section' => $newsInstance->getSection(),
          'url' => URL::route('news', array('section_slug' => $newsInstance->getSection()->slug)),
          'type' => 'Nouvelle',
      );
    }
    // List recent documents
    $documents = Document::where('created_at', '>=', $startDate)
            ->where('archived', '=', false)
            ->orderBy('created_at', 'desc')
            ->get();
    foreach ($documents as $doc) {
      $recentChanges[] = array(
          'datetime' => $doc->created_at->toDateTimeString(),
          'date' => $doc->created_at->format('Y-m-d'),
          'item' => $doc->title,
          'section' => $doc->getSection(),
          'url' => URL::route('documents', array('section_slug' => $doc->getSection()->slug)),
          'type' => 'Document',
      );
    }
    // List recent e-mails
    $emails = Email::where('created_at', '>=', $startDate);
    if (!$this->user->isLeader()) $emails->where('target', '!=', 'leaders');
    $emails = $emails->where('archived', '=', false)
            ->where('deleted', '=', false)
            ->orderBy('created_at', 'desc')
            ->get();
    foreach ($emails as $email) {
      $recentChanges[] = array(
          'datetime' => $email->created_at->toDateTimeString(),
          'date' => $email->created_at->format('Y-m-d'),
          'item' => $email->subject,
          'section' => $email->getSection(),
          'url' => URL::route('emails', array('section_slug' => $email->getSection()->slug)),
          'type' => 'E-mail',
      );
    }
    // List recent photos
    $albums = PhotoAlbum::where('archived', '=', false)
            ->where('photo_count', '!=', 0)
            ->where('updated_at', '>=', $startDate)
            ->orderBy('updated_at')
            ->get();
    foreach ($albums as $album) {
      $recentChanges[] = array(
          'datetime' => $album->updated_at->toDateTimeString(),
          'date' => $album->updated_at->format('Y-m-d'),
          'item' => $album->name,
          'section' => $album->getSection(),
          'url' => URL::route('photo_album', array('section_slug' => $album->getSection()->slug, 'album_id' => $album->id)),
          'type' => 'Photos',
      );
    }
    // Function to sort changes by date
    function compareChanges($a, $b) {
      return strcmp($b['datetime'], $a['datetime']);
    }
    // Sort changes by date
    usort($recentChanges, "compareChanges");
    // Make view
    return View::make('pages.recentChanges.recentChanges', array(
        'recent_changes' => $recentChanges,
    ));
  }
  
}
