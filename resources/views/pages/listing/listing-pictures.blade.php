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
  Listing {{{ $user->currentSection->de_la_section }}}
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('listing', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour au listing
    </a>
  </p>
@stop

@section('forward_links')
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_listing', array('section_slug' => $user->currentSection->slug)) }}'>
        Gérer le listing
      </a>
    </p>
  @endif
  @if (count($sections) == 1)
    @if ($sections[0]['show_subgroup'])
      <p>
        <a href='{{ URL::route('listing_view_subgroups', array('section_slug' => $user->currentSection->slug)) }}'>
          Listing par {{{ strtolower($sections[0]['section_data']->subgroup_name) }}}
        </a>
      </p>
    @endif
  @endif
@stop

@section('content')
  
  @if ($user->currentSection->id == 1)
    <div class="row">
      <div class="col-md-12">
        <h1>
          Photos des membres {{{ $user->currentSection->de_la_section }}} ({{ $total_member_count }} membres)
        </h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-right">
        <p>
          <a class="btn-sm btn-default" href="{{ URL::route('download_member_pictures', array('section_slug' => $user->currentSection->slug)) }}">
            Télécharger les photos de toute l'unité
          </a>
        </p>
      </div>
    </div>
  @endif
  
  @foreach ($sections as $sct)
  
    <div class="row">
      <div class="col-md-12">
        <h2>
          @if (count($sections) > 1)
            <span class="glyphicon glyphicon-certificate" style="color: {{ $sct['section_data']->color }}"></span>
          @endif
          Photos des membres {{{ $sct['section_data']->de_la_section }}}
          @if ($sct['members']->count() > 1) ({{ $sct['members']->count() }} membres) @endif
        </h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-right">
        <p>
          <a class="btn-sm btn-default" href="{{ URL::route('download_member_pictures', array('section_slug' => $sct['section_data']->slug)) }}">
            Télécharger les photos {{{ $sct['section_data']->de_la_section }}}
          </a>
        </p>
      </div>
    </div>
    
    @if ($sct['members']->count())
    
      <div class="row">
        <div class="col-md-12">
          @foreach ($sct['members'] as $member)
            <span class="picture-listing-frame">
              <span class="member-name">{{{ $member->getFullName() }}}</span>
              <br />
              @if ($member->has_picture)
                <img src='{{ $member->getPictureURL() }}' alt='Pas de photo'>
              @else
                <img src='{{ asset('images/no-picture.png') }}' alt='Pas de photo'>
              @endif
            </span>
          @endforeach
        </div>
      </div>
    
    @else
      
      <div class="row">
        <div class="col-md-12">
          <p>Il n'y a aucun membre dans cette section.</p>
        </div>
      </div>
      
    @endif
  @endforeach
  
@stop