<?php

class RecentChangesController extends BaseController {
  
  public function showPage() {
    
    $startDate = date('Y-m-d', time() - 3600 * 24 * 60);
    
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
    $emails = Email::where('created_at', '>=', $startDate)
            ->where('archived', '=', false)
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
    
    function compareChanges($a, $b) {
      return strcmp($b['datetime'], $a['datetime']);
    }
    
    usort($recentChanges, "compareChanges");
    
    return View::make('pages.recentChanges.recentChanges', array(
        'recent_changes' => $recentChanges,
    ));
  }
  
}