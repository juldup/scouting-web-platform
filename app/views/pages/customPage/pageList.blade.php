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
  Pages du site
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-pages.js') }}"></script>
  <script src="{{ asset('js/reorder-list.js') }}"></script>
  <script>
    var savePageOrderURL = "{{ URL::route('ajax_change_custom_page_order') }}";
  </script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'custom-page-list'))
  
  <div class="row">
    <div class="col-sm-12">
      <h1>Pages du site</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  <div class='row'>
    <div class='col-md-5'>
      <h2>Pages standards</h2>
      <table class=''>
        <tbody>
          <tr>
            <th>Page d'accueil</th>
            <td>
              <span class='horiz-divider'></span>
              <a href='{{ URL::route('home') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_home_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Charte d'unité</th>
            <td>
              <span class='horiz-divider'></span>
                <a href='{{ URL::route('unit_policy') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_unit_policy_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Adresses</th>
            <td>
              <span class='horiz-divider'></span>
              <a href='{{ URL::route('contacts') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_address_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Inscription (activée)</th>
            <td>
              <span class='horiz-divider'></span>
              @if (Parameter::get(Parameter::$REGISTRATION_ACTIVE))
                <a href='{{ URL::route('registration') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              @else
                <button class='btn btn-default disabled'><span class='glyphicon glyphicon-eye-close'></span></button>
              @endif
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_registration_active_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Inscription (désactivée)</th>
            <td>
              <span class='horiz-divider'></span>
              @if (!Parameter::get(Parameter::$REGISTRATION_ACTIVE))
                <a href='{{ URL::route('registration_inactive') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              @else
                <button class='btn btn-default disabled'><span class='glyphicon glyphicon-eye-close'></span></button>
              @endif
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_registration_inactive_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Formulaire d'inscription</th>
            <td>
              <span class='horiz-divider'></span>
                <a href='{{ URL::route('registration_form') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_registration_form') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Fête d'unité</th>
            <td>
              <span class='horiz-divider'></span>
              <a href='{{ URL::route('annual_feast') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_annual_feast_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
          <tr>
            <th>Aide</th>
            <td>
              <span class='horiz-divider'></span>
              <a href='{{ URL::route('help') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
              &nbsp;
              @if ($user->can(Privilege::$EDIT_PAGES, 1))
                <a href='{{ URL::route('edit_help_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class='col-md-7'>
      <h2>Pages de section</h2>
        <table>
          <thead>
            <th></th>
            <th><span class='horiz-divider'></span><span class='horiz-divider'></span>Page d'accueil</th>
            <th><span class='horiz-divider'></span><span class='horiz-divider'></span>Uniforme</th>
          </thead>
          <tbody>
            @foreach (Section::all() as $section)
              <tr>
                <th>
                  <span style='color:{{{ $section->color}}};'>{{{ $section->name }}}</span>
                </th>
                <td>
                  <span class='horiz-divider'></span><span class='horiz-divider'></span>
                  <a href='{{ URL::route('section', array('section_slug' => $section->slug)) }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
                  &nbsp;
                  @if ($user->can(Privilege::$EDIT_PAGES, $section))
                    <a href='{{ URL::route('edit_section_page', array('section_slug' => $section->slug)) }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
                  @endif
                </td>
                <td>
                  <span class='horiz-divider'></span><span class='horiz-divider'></span>
                  <a href='{{ URL::route('help') }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
                  &nbsp;
                  @if ($user->can(Privilege::$EDIT_PAGES, $section))
                    <a href='{{ URL::route('edit_help_page') }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
    </div>
  </div>
  <div class='row'>
    <div class='col-sm-12'>
      <h2>Pages libres</h2>
      <table>
        <tbody class="@if ($user->can(Privilege::$EDIT_PAGES, 1)) draggable-tbody photos-uploaded @endif">
          @foreach (Page::where('type', '=', 'custom')->orderBy('position')->get() as $page)
            <tr class="draggable-row" id="page-{{ $page->id }}" data-draggable-id="{{ $page->id }}">
              <th>{{{ $page->title }}} {{ $page->leaders_only ? "(privé)" : "" }}<span class='horiz-divider'></span></th>
              <td>
                <a href='{{ URL::route('custom_page', array('page_slug' => $page->slug)) }}' class='btn btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
                &nbsp;
                @if ($user->can(Privilege::$EDIT_PAGES, 1))
                  <a href='{{ URL::route('edit_custom_page', array('page_slug' => $page->slug)) }}' class='btn btn-primary'><span class='glyphicon glyphicon-edit'></span></a>
                  &nbsp;
                  <a href='{{ URL::route('delete_custom_page', array('page_slug' => $page->slug)) }}' class='btn btn-danger delete-page-button'>Supprimer</a>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
        @if ($user->can(Privilege::$EDIT_PAGES, 1))
          <tbody>
            <tr>
              <th>Ajouter une page<span class='horiz-divider'></span></th>
              <td>
                {{ Form::open(array('route' => 'add_custom_page')) }}
                {{ Form::text('page_title', null, array('class' => 'form-control large', 'placeholder' => 'Titre de la page')) }}
                <span class='horiz-divider'></span>
                Page privée : {{ Form::checkbox('leaders_only', 1, 0) }}
                <span class='horiz-divider'></span>
                {{ Form::submit('Ajouter', array('class' => 'btn btn-primary')) }}
                {{ Form::close() }}
              </td>
            </tr>
          </tbody>
        @endif
      </table>
    </div>
  </div>
@stop
