@extends('base')

@section('title')
  Gestion de l'année des scouts
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit-year-in-section.js"></script>
  <script>
    var changeYearURL = "{{ URL::route('ajax_update_year_in_section') }}";
    var currentSectionId = {{ $user->currentSection->id }};
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('registration', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la page d'inscription
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-year-in-section'))
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'change_year'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Changer l'année des scouts {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @if (count($active_members))
    <div class="row">
      <div class="col-md-12">
        <table class="table table-striped table-hover wide-table">
          <thead>
            <tr>
              <th class="text-right space-on-right">Nom</th>
              <th class="space-on-right">Date de naissance</th>
              <th colspan="3" class="text-center">Année</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($active_members as $member)
              <tr class="member-row" data-member-id="{{ $member->id }}">
                <th class="space-on-right">
                  {{ $member->first_name }} {{ $member->last_name }}
                </th>
                <td class="space-on-right">
                  {{ Helper::dateToHuman($member->birth_date) }}
                </td>
                <td>
                  <a class='btn-sm btn-default decrease-year-button' href="">
                    -
                  </a>
                </td>
                <td>
                  <span class='member-year'>{{ $member->year_in_section }}</span>
                </td>
                <td>
                  <a class='btn-sm btn-default increase-year-button' href="">
                    +
                  </a>
                </td>
                <td class="space-on-left">
                  @if ($member == $active_members[0])
                    <a class='btn-sm btn-default increase-all-button' href="">
                      Tout augmenter de 1
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <p>Il n'y a aucun membre dans cette section.</p>
  @endif

@stop