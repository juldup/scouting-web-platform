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
use App\Models\MemberHistory;

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
  @vite(['resources/js/ckeditor/ckeditor.js'])
  <script type='module'>
    ClassicEditor.create(document.querySelector('#page_body'), {
      simpleUpload: {
        uploadUrl: '{{ URL::route('ajax_upload_image') }}?_token=' + $('meta[name="csrf-token"]').attr('content')
      }
    })
    .then(editor => {
      console.log( editor );
    })
    .catch(error => {
      console.error( error );
    });
  </script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-page'))
  
  <div class="row page_body form-horizontal">
    <form name="edit_page" method="post" action="" id="edit_page_form">
      @csrf
      <h1>{{{ $page_title }}}</h1>
      @if ($additional_information_subview)
        @include($additional_information_subview)
      @endif
      <div class="form-group">
        <div class="col-md-10">
          <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-12">
          <textarea cols="80" id="page_body" name="page_body" rows="10">{!! $page_body !!}</textarea>
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
