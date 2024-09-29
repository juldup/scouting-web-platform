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
  {{{ $page_title ? $page_title : Parameter::get(Parameter::$UNIT_LONG_NAME) }}}
@stop

@section('head')
  @if ($is_home_page)
    <meta name="description" content="{{{ Parameter::get(Parameter::$WEBSITE_META_DESCRIPTION) }}}" />
    <meta name="keywords" content="{{{ Parameter::get(Parameter::$WEBSITE_META_KEYWORDS) }}}" />
  @endif
  @vite(['resources/css/ckeditor/imageresize.css'])
  @vite(['resources/css/ckeditor/imagestyle.css'])
  @vite(['resources/css/ckeditor/ckeditor-fix.css'])
@stop

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_edit)
    <p>
      <a href='{{ $edit_url }}'>
        Modifier cette page
      </a>
    </p>
  @endif
@stop

@section('content')
  <div class="row page_body">
    <div class="col-md-12">
      @if (Session::has('success_message'))
        <p class='alert alert-success'>{{ Session::get('success_message') }}</p>
      @endif
      <h1>{{{ $page_title }}}</h1>
      <div class="ck-content">
        {!! $page_body !!}
      </div>
      @if ($additional_content)
        @include($additional_content)
      @endif
    </div>
  </div>
@stop
