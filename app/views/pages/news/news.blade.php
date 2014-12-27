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
  Actualités {{{ ($is_global_news_page) ? "de l'unité" : $user->currentSection->de_la_section }}}
@stop

@section('back_links')
  @if ($showing_archives)
    <p>
      <a href='{{ URL::route('news', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux actualités de cette année
      </a>
    </p>
  @endif
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
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Actualités {{{ ($is_global_news_page) ? "de l'unité" : $user->currentSection->de_la_section }}}</h1>
      @if (count($news) == 0)
        <p>Aucune nouvelle.</p>
      @endif
    </div>
  </div>
  
  @foreach ($news as $newsItem)
    <div class="row">
      <div class="col-md-12">
        <div class="well">
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
            @if ($is_global_news_page)
              <span class="glyphicon glyphicon-certificate" style="color: {{ Section::find($newsItem->section_id)->color }}"></span>
              {{{ Section::find($newsItem->section_id)->name }}} :
            @endif
            {{{ $newsItem->title }}} – {{{ $newsItem->getHumanDate() }}}
          </legend>
          <div>
            {{ $newsItem->body }}
          </div>
        </div>
        @if (count($newsItem->getComments()) || $user->isMember())
          <div class="comments">
            @if (count($newsItem->getComments()))
              <div class="comment-title">Commentaires</div>
            @endif
            @if (count($newsItem->getComments()) > 5)
              <a class="show-hidden-comments">Voir les {{ count($newsItem->getComments()) - 4 }} commentaires précédents</a>
            @endif
            @foreach ($newsItem->getComments() as $index=>$comment)
              <div class="comment @if($index < count($newsItem->getComments()) - 4 && count($newsItem->getComments()) != 5) comment-hidden @endif">
                <span class="comment-meta">{{{ $comment->getHumanDate() }}}, <span class="comment-username">{{{ $comment->getUserName() }}}</span> a écrit &nbsp;:</span>
                <span class="comment-body">{{{ $comment->body }}}</span>
              </div>
            @endforeach
            @if ($user->isMember())
              <a href="" class="add-comment-button" data-referent="{{ $newsItem->id }}" data-referent-type="news">@if (count($newsItem->getComments())) Répondre @else Ajouter un commentaire @endif</a>
            @endif
          </div>
        @endif
      </div>
    </div>
  @endforeach
  
  @if ($has_archives)
    <div class="vertical-divider"></div>
    @if ($showing_archives)
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('news_archives', array('section_slug' => $user->currentSection->slug, 'page' => $next_page)) }}">Voir les nouvelles plus anciennes</a>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('news_archives', array('section_slug' => $user->currentSection->slug)) }}">Voir les nouvelles archivées</a>
        </div>
      </div>
    @endif
  @endif
  
  <div id="comment-prototype" style="display: none;">
    <form class="comment-form" action="{{ URL::route('post-comment', array('referent_id' => "REFERENT_ID", 'referent_type' => "REFERENT_TYPE")) }}" method="POST">
      <textarea name="body" class="form-control"></textarea>
      <input type="submit" value="Poster" class="btn btn-sm btn-primary">
    </form>
  </div>
  <script>
    var currentUserName = "{{{ $user->username }}}";
  </script>
  
@stop
