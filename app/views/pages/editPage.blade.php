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
  Modifier la page "{{{ $page_title }}}"
@stop

@section('back_links')
  <p>
    <a href='{{ $original_page_url }}'>
      Retour à la page
    </a>
  </p>
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-page.js') }}"></script>
  <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
  <script>
    CKEDITOR.replace('page_body', {
      language: 'fr',
      extraPlugins: 'divarea',
      height: '400px'
    });
    var image_upload_url = "{{ URL::route('ajax_upload_image', array('page_id' => $page_id)) }}";
    var image_remove_url = "{{ URL::route('ajax_remove_image', array('image_id' => 'image_id')) }}";
    var initial_images = [
      @foreach ($images as $image)
        {'image_id': {{ $image->id }}, 'url': '{{ $image->getURL() }}' },
      @endforeach
    ];
  </script>
  <script src="{{ asset('js/libs/upclick.js') }}"></script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-page'))
  
  <div class="row page_body form-horizontal">
    <form name="edit_page" method="post" action="" id="edit_page_form">
      <h1>{{{ $page_title }}}</h1>
      <div class="form-group">
        <div class="col-md-12">
          <textarea cols="80" id="page_body" name="page_body" rows="10">{{ $page_body }}</textarea>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-2">Images</label>
        <div class="col-md-10">
          <input type="button" id="uploader" value="Ajouter" class="btn btn-default" />
          <span class="horiz-divider"></span>
          <span id="image_list"></span>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
  
@stop