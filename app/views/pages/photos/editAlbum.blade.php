@extends('base')

@section('title')
  Gestion des photos
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/reorderList.js"></script>
  <script src="{{ URL::to('/') }}/js/editAlbum.js"></script>
  <script>
    var savePhotoOrderURL = "{{ URL::route('ajax_change_photo_order') }}";
  </script>
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('edit_photos') }}">Retour à la liste d'albums</a>
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
      <h2>Album : {{ $album->name }}</h2>
      <table class="table table-striped table-hover draggable-table" id="photo-table">
        <tbody>
          @foreach($photos as $photo)
            <tr class="photo-row draggable-row" data-photo-id="{{ $photo->id }}" data-draggable-id="{{ $photo->id }}">
              <td>
                <div class="photo-thumbnail">
                  <img src='{{ $photo->getThumbnailURL() }}' />
                </div>
              </td>
              <td>
<!--                <p>
                  Nom du fichier : {{ $photo->filename }} <span class="glyphicon glyphicon-edit"></span>
                </p>-->
                <p>
                  Description : {{ $photo->caption }} <span class="glyphicon glyphicon-edit"></span>
              </td>
              <td>
                &nbsp;<a class="btn-sm btn-default" href="{{ URL::route('delete_photo_album', array('album_id' => $album->id)) }}">
                  Supprimer
                </a>
              </td>
            </tr>
          @endforeach
          <tr>
            <td colspan="2">
              <div id='photo-drop-area'>
                Glisse une ou plusieurs photos ici pour les ajouter.
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <p>Note : tu peux glisser-déplacer une photo pour changer sa position.</p>
    </div>
  </div>
    
@stop