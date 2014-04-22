@extends('base')

@section('title')
  Gestion de l'album "{{{ $album->name }}}"
@stop

@section('additional_javascript')
  <script src="{{ asset('js/reorder-list.js') }}"></script>
  <script src="{{ asset('js/edit-album.js') }}"></script>
  <script src="{{ asset('js/editable-text.js') }}"></script>
  <script>
    var savePhotoOrderURL = "{{ URL::route('ajax_change_photo_order') }}";
    var deletePhotoURL = "{{ URL::route('ajax_delete_photo') }}";
    var uploadPhotoURL = "{{ URL::route('ajax_add_photo') }}";
    var currentAlbumId = "{{ $album->id }}";
    var rotatePhotoURL = "{{ URL::route('ajax_rotate_photo') }}"
  </script>
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('edit_photos') }}">Retour à la liste d'albums</a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-album'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Album "{{{ $album->name }}}"</h1>
      @include ('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover draggable-table" id="photo-table">
        <tbody>
          @foreach($photos as $photo)
            <tr class="photo-row draggable-row" id="photo-{{ $photo->id }}" data-draggable-id="{{ $photo->id }}" data-photo-id="{{ $photo->id }}">
              <td class="photo-thumnail-column">
                <div class="photo-thumbnail">
                  <img src='{{ $photo->getThumbnailURL() }}' />
                </div>
              </td>
              <td class="photo-data-column">
                <div>
                  <strong>Tourner: </strong>
                  <img class="rotate-icon rotate-anticlockwise-button" src="{{ asset('images/photos/rotate-anticlockwise.png') }}" />
                  <img class="rotate-icon rotate-clockwise-button" src="{{ asset('images/photos/rotate-clockwise.png') }}" />
                </div>
                <div>
                  <span class="editable-text"
                        data-editable-input-type="textarea"
                        data-editable-allow-empty="1"
                        data-editable-submit-url="{{ URL::route('ajax_change_photo_caption') }}"
                        data-editable-id="{{ $photo->id }}">
                    <strong>Description :</strong>
                    <span class="editable-text-value">
                      {{{ $photo->caption }}}
                    </span>
                  </span>
                </div>
              </td>
              <td class="photo-actions-column">
                <a class="btn-sm btn-default" onclick="deletePhoto(this)">
                  Supprimer
                </a>
              </td>
            </tr>
          @endforeach
          <tr style="display: none;" id="upload-row-prototype" class="photo-row">
            <td class="photo-thumnail-column">
              <div class="photo-thumbnail">
                <img src="{{ asset('images/loading.gif') }}" />
              </div>
            </td>
            <td></td>
            <td></td>
          </tr>
          <tr style="display: none;" id="photo-row-prototype" class="photo-row">
            <td class="photo-thumnail-column">
              <div class="photo-thumbnail">
                <img src="" />
              </div>
            </td>
            <td class="photo-data-column">
              <div>
                <strong>Tourner: </strong>
                <img class="rotate-icon rotate-anticlockwise-button" src="{{ asset('images/photos/rotate-anticlockwise.png') }}" />
                <img class="rotate-icon rotate-clockwise-button" src="{{ asset('images/photos/rotate-clockwise.png') }}" />
              </div>
              <div>
                <span class="editable-text"
                      data-editable-input-type="textarea"
                      data-editable-submit-url="{{ URL::route('ajax_change_photo_caption') }}">
                  <strong>Description :</strong>
                  <span class="editable-text-value"></span>
                </span>
              </div>
            </td>
            <td class="photo-actions-column">
              <a class="btn-sm btn-default" onclick="deletePhoto(this)">
                Supprimer
              </a>
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <div id='photo-drop-area'>
                <p>
                  <strong>Glisse ici</strong> des photos depuis ton ordinateur pour les ajouter (jpeg/png)
                </p>
                <p>
                  ou <strong>clique ici</strong> pour les sélectionner.
                </p>
              </div>
              <input type='file' style='display: none;' multiple id='file-input' onChange='picturesManuallySelected()' />
            </td>
          </tr>
        </tbody>
      </table>
      <p>Note : tu peux glisser-déplacer une photo pour changer sa position.</p>
    </div>
  </div>
    
@stop