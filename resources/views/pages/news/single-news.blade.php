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
  {{ $newsItem->title }}
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('global_news') }}'>
      Toutes les actualités
    </a>
  </p>
@stop

@section('forward_links')
  @if ($can_edit)
    <p>
      <a href='{{ $edit_url }}'>
        Modifier les actualités
      </a>
    </p>
  @endif
@stop

@section('head')
  <meta property='og:title' content='{{{ $newsItem->title }}}'>
  <meta property='og:type' content='article'>
  <meta property='og:image' content='{{ URL::route('website_logo') }}'>
  <meta property='og:url' content='{{ URL::route('single_news', array('news_id' => $newsItem->id)) }}'>
  <meta property='og:description' content='{{{ strip_tags($newsItem->body) }}}'>
@stop

@section('body_top')
  @if (Parameter::get(Parameter::$FACEBOOK_APP_ID))
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '{{{ Parameter::get(Parameter::$FACEBOOK_APP_ID) }}}',
          xfbml      : true,
          version    : 'v2.2'
        });
      };
      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/fr_FR/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
  @endif
@stop

@section('additional_javascript')
  <script src="https://apis.google.com/js/platform.js" async defer></script>
@stop

@section('content')
  
  <div class="vertical-divider"></div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="well"><a name="nouvelle-{{ $newsItem->id }}"></a>
        <legend>
          @if (Parameter::get(Parameter::$FACEBOOK_APP_ID))
            <ul class='social-widgets'>
              <li class="google-plus-widget">
                <div class="g-plusone" data-size="medium" data-href="{{ URL::route('single_news', array('news_id' => $newsItem->id)) }}"></div>
              </li>
              <li class="facebook-widget">
                <div
                  class="fb-like"
                  data-share="true"
                  data-layout="button_count"
                  data-show-faces="false"
                  data-kid-directed-site="true"
                  data-href="{{ URL::route('single_news', array('news_id' => $newsItem->id)) }}">
                </div>
              </li>
            </ul>
          @endif
          <span class="glyphicon glyphicon-certificate" style="color: {{ Section::find($newsItem->section_id)->color }}"></span>
          {{{ Section::find($newsItem->section_id)->name }}} :
          {{{ $newsItem->title }}} – {{{ $newsItem->getHumanDate() }}}
        </legend>
        <div>
          {{ $newsItem->body }}
        </div>

      </div>
    </div>
  </div>
  
@stop