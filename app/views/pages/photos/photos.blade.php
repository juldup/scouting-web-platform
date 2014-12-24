@extends('base')
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
?>

@section('title')
  Photos {{{ $user->currentSection->de_la_section }}}
@stop

@section('additional_javascript')
  <script src="{{ asset('js/photos.js') }}"></script>
@stop

@section('back_links')
  @if ($showing_archives)
    <p>
      <a href='{{ URL::route('photos', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux photos de cette année
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if ($can_manage)
    <p>
      <a href="{{ URL::route('edit_photos') }}">Gérer les photos</a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Photos {{{ $user->currentSection->de_la_section }}}</h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @if (count($albums))
        <h2>Albums @if ($showing_archives) archivés @endif</h2>
        <table class="table table-striped table-hover">
          <tbody>
            @foreach($albums as $album)
              <tr class="photo-album-row">
                <td>
                  @if ($showing_archives)
                    <strong>{{{ Helper::dateToHuman($album->date) }}} :</strong>
                  @endif
                  @if ($album == $current_album)
                    <strong>{{{ $album->name }}}</strong>
                  @else
                  <a class="photo-album-link" href='{{ URL::route('photo_album', array('album_id' => $album->id, 'section_slug' => $user->currentSection->slug)) }}'>{{ $album->name }}</a>
                  @endif
                </td>
                <td class="photo-album-count">{{ $album->photo_count }} {{{ $album->photo_count > 1 ? "photos" : "photo" }}}</td>
                <td class="download-photos-column">
                  <div>
                    @if ($album->photo_count <= $downloadPartSize)
                      <div class='download-photos-button-wrapper'>
                        <a class="btn-sm btn-default" href="{{ URL::route('download_photo_album', array('album_id' => $album->id, 'first_photo' => 1, 'last_photo' => $album->photo_count)) }}">
                          <span class="glyphicon glyphicon-download-alt"></span> Télécharger l'album
                        </a>
                      </div>
                    @else
                      @for ($i = 0; $i <= ($album->photo_count - 1) / $downloadPartSize; $i++)
                        <div class='download-photos-button-wrapper'>
                          <a class="btn-sm btn-default"
                             href="{{ URL::route('download_photo_album', array('album_id' => $album->id, 'first_photo' => $i * $downloadPartSize + 1,
                                                 'last_photo' => min(($i + 1) * $downloadPartSize, $album->photo_count))) }}">
                            <span class="glyphicon glyphicon-download-alt"></span> Photos {{ $i * $downloadPartSize + 1 }}–{{ min(($i + 1) * $downloadPartSize, $album->photo_count) }}
                          </a>
                        </div>
                      @endfor
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div class="row">
          <div class="col-md-12">
            @if ($showing_archives)
              <p>Il n'y a pas d'albums archivés pour cette section.</p>
            @else
              <p>Il n'y a pas encore d'albums pour cette section.</p>
            @endif
          </div>
        </div>
      @endif
      @if ($has_archives)
        <div class="vertical-divider"></div>
        @if ($showing_archives)
          <div class="row">
            <div class="col-md-12">
              <a class="btn-sm btn-default" href="{{ URL::route('photo_archives', array('section_slug' => $user->currentSection->slug, 'page' => $next_page)) }}">Voir les albums plus anciens</a>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-md-12">
              <a class="btn-sm btn-default" href="{{ URL::route('photo_archives', array('section_slug' => $user->currentSection->slug)) }}">Voir les albums archivés</a>
            </div>
          </div>
        @endif
      @endif
    </div>
  </div>
  
  
  @if (count($photos))  
    <div class="row">
      <div class="col-md-12">
        <h2>{{{ $current_album->name }}}</h2>
        <div id="photo-carousel" class="carousel slide">
          @if (count($photos) > 1)
            <div id="carousel-controls">
              <a id="carousel-start" href="javascript:startCarousel();" title="Diaporama">
                <span class="glyphicon glyphicon-play"></span>
              </a>
              <a id="carousel-stop" href="javascript:stopCarousel();" style="display: none;">
                <span class="glyphicon glyphicon-pause"></span>
              </a>
            </div>
          @endif
          <div class="carousel-inner">
            <?php $photoCounter = 1; ?>
            @foreach ($photos as $photo)
              <div class="item @if ($photo == $photos[0]) active @endif">
                <div class="image-wrapper-outer">
                  <div class="image-wrapper-inner">
                    <a href="{{ $photo->getOriginalURL(); }}">
                      <img data-src="{{ $photo->getPreviewURL(); }}" />
                    </a>
                  </div>
                </div>
                @if ($photo->caption)
                  <div class="carousel-caption">{{{ $photo->caption }}}</div>
                @endif
                <div class="carousel-counter">{{ $photoCounter++ }} / {{ count($photos) }}</div>
              </div>
            @endforeach
          </div>
          @if (count($photos) > 1)
            <a class="carousel-control left" href="#photo-carousel" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="carousel-control right" href="#photo-carousel" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          @endif
        </div>
      </div>
    </div>
    
    <div class="row">
      <?php $photoIndex = 0; ?>
      @foreach ($photos as $photo)
        <div class="col-xs-4 col-sm-3 col-md-2 photo-thumbnail-wrapper">
          <div class="photo-thumbnail">
            <a href="javascript:showPhoto({{ $photoIndex++ }})">
              <img src="{{ $photo->getThumbnailURL(); }}" />
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @endif
  
@stop