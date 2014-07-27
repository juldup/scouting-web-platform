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
            @if ($is_global_news_page)
              <span class="glyphicon glyphicon-certificate" style="color: {{ Section::find($newsItem->section_id)->color }}"></span>
              {{{ Section::find($newsItem->section_id)->name }}} :
            @endif
            {{{ $newsItem->title }}} – {{{ $newsItem->getHumanDate() }}}
          </legend>
          <div>
            {{ Helper::rawToHTML($newsItem->body) }}
          </div>
        </div>
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
  
@stop