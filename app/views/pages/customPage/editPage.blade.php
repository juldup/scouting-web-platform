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
      Retour sans enregistrer
    </a>
  </p>
@stop

@section('additional_javascript')
  <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
  <script>
    // Init CKEditor
    CKEDITOR.replace('page_body', {
      language: 'fr',
      extraPlugins: 'divarea,mediaembed',
      height: '400px',
      filebrowserImageUploadUrl: "{{ URL::route('ajax_upload_image') }}",
      on: {
        save: function(event) {
          // Cancel warning before leaving page
          cancelCheckDirty();
        }
      }
    });
    // Warning when exiting the page with unsaved modifications
    function checkDirty(event) {
      if (CKEDITOR.instances['page_body'].checkDirty()) {
        event.returnValue = "Tu n'as pas sauvé les modifications effectuées sur la page.";
      }
    }
    function cancelCheckDirty() {
      window.removeEventListener('beforeunload', checkDirty);
    }
    window.addEventListener('beforeunload', checkDirty);
    // Disable leave page warning on submit
    document.getElementById("edit_page_form").onsubmit = cancelCheckDirty;
  </script>
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
        <div class="col-md-10">
          <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
  
@stop
