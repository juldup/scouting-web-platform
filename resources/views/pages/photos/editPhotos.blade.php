@extends('base')
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

use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;

?>

@section('title')
  Gestion des photos
@stop

@section('additional_javascript')
  @vite(['resources/js/edit-photos.js'])
  @vite(['resources/js/reorder-list.js'])
  @vite(['resources/js/editable-text.js'])
  <script>
    var saveAlbumOrderURL = "{{ URL::route('ajax_change_album_order') }}";
    @if ($selected_album_id)
      $().ready(function() {
        $(".editable-text[data-editable-id={{ $selected_album_id }}]").changeEditableTextToEditMode();
      });
    @endif
  </script>
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('photos') }}">Retour aux photos</a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-photos'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Gestion des photos {{{ $user->currentSection->de_la_section }}}</h1>
      @include ('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @if (count($albums))
        <h2>Albums</h2>
        <table class="table table-striped table-hover">
          <tbody class="draggable-tbody">
            @foreach($albums as $album)
              <tr class="draggable-row" data-draggable-id="{{ $album->id }}">
                <td class="photo-album-name-column">
                  <span class="editable-text"
                        data-editable-submit-url="{{ URL::route('ajax_change_album_name') }}"
                        data-editable-id="{{ $album->id }}">
                    <span class="editable-text-value">
                      {{{ $album->name }}}
                    </span>
                  </span>
                </td>
                <td class="photo-album-count-column">
                  @if ($album->photo_count)
                    {{ $album->photo_count }} {{{ $album->photo_count > 1 ? "photos" : "photo" }}}
                  @else
                    L'album est vide
                  @endif
                </td>
                <td class="photo-album-leaders-only-column">
                  @if ($album->leaders_only)
                  <a class="btn-sm btn-primary" href="{{ URL::route('toggle_photo_album_privacy', array('album_id' => $album->id, 'status' => 0)) }}">
                      <i class="glyphicon glyphicon-lock"></i>&nbsp;&nbsp;Pour les animateurs
                    </a>
                  @else
                  <a class="btn-sm btn-primary" href="{{ URL::route('toggle_photo_album_privacy', array('album_id' => $album->id, 'status' => 1)) }}">
                      <i class="glyphicon glyphicon-eye-open"></i>&nbsp;&nbsp;Pour tous
                    </a>
                  @endif
                </td>
                <td class="photo-album-actions-column">
                  <a class="btn-sm btn-default" href="{{ URL::route('edit_photo_album', array('album_id' => $album->id)) }}">
                    Modifier l'album
                  </a>
                  @if ($album->photo_count == 0)
                    &nbsp;<a class="btn-sm btn-default" href="{{ URL::route('delete_photo_album', array('album_id' => $album->id)) }}">
                      Supprimer
                    </a>
                  @else
                    &nbsp;<a class="btn-sm btn-default archive-photo-album-button" href="{{ URL::route('archive_photo_album', array('album_id' => $album->id)) }}">
                      Archiver
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
          <tbody>
            <tr>
              <td colspan="3"></td>
              <td>
                <a class="btn-sm btn-default" href="{{ URL::route('create_photo_album', array('section_slug' => $user->currentSection->slug)) }}">
                  Créer un nouvel album
                </a>
              </td>
            </tr>
          </tbody>
        </table>
        <p>Note : tu peux glisser-déplacer un album pour changer sa position.</p>
      @else
        <div class="row">
          <div class="col-md-12">
            <p>Il n'y a pas encore d'albums pour cette section.</p>
            <p>
              <a class="btn-sm btn-default" href="{{ URL::route('create_photo_album', array('section_slug' => $user->currentSection->slug)) }}">
                Créer un nouvel album
              </a>
            </p>
          </div>
        </div>
      @endif
    </div>
  </div>
    
@stop