@extends('base')

@section('title')
  Gestion des photos
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/editPhotos.js"></script>
  <script src="{{ URL::to('/') }}/js/reorderList.js"></script>
  <script src="{{ URL::to('/') }}/js/editableText.js"></script>
  <script>
    var saveAlbumOrderURL = "{{ URL::route('ajax_change_album_order') }}";
  </script>
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('photos') }}">Retour aux photos</a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Gestion des photos {{ $user->currentSection->de_la_section }}</h1>
      @include ('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @if (count($albums))
        <h2>Albums</h2>
        <table class="table table-striped table-hover draggable-table">
          <tbody>
            @foreach($albums as $album)
              <tr class="draggable-row" data-draggable-id="{{ $album->id }}">
                <td>
                  <span class="editable-text"
                        data-editable-submit-url="{{ URL::route('ajax_change_album_name') }}"
                        data-editable-id="{{ $album->id }}">
                    <span class="editable-text-value">
                      {{ $album->name }}
                    </span>
                  </span>
                </td>
                <td>
                  @if ($album->photo_count)
                    {{ $album->photo_count }} {{ $album->photo_count > 1 ? "photos" : "photo" }}
                  @else
                    Aucune photo
                  @endif
                </td>
                <td>
                  <a class="btn-sm btn-default" href="{{ URL::route('edit_photo_album', array('album_id' => $album->id)) }}">
                    Modifier l'album
                  </a>
                  @if ($album->photo_count == 0)
                    &nbsp;<a class="btn-sm btn-default" href="{{ URL::route('delete_photo_album', array('album_id' => $album->id)) }}">
                      Supprimer
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
            <tr>
              <td colspan="2"></td>
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
          </div>
        </div>
      @endif
    </div>
  </div>
    
@stop