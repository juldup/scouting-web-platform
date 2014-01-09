@extends('base')

@section('title')
  Gestion du listing
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_members.js"></script>
  <script>
    var members = new Array();
    @foreach ($members as $member)
      members[{{ $member->id }}] = @include ('subviews.memberToJavascript', array('member' => $member));
    @endforeach
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('listing', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour au listing
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Gestion du listing {{ $user->currentSection->de_la_section }}</h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.editMemberForm', array('form_legend' => "Modifier un membre", 'submit_url' => URL::route('listing_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => false, 'edit_identity' => true, 'edit_section' => $can_change_section, 'edit_totem' => true, 'edit_leader' => false))
    </div>
  </div>
  
  @if ($members->count())
    <div class="row">
      <div class="col-lg-12">
        <table class="table table-striped table-hover">
          <thead>
            <th></th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date de naissance</th>
            <th>Année</th>
          </thead>
          <tbody>
            @foreach ($members as $member)
              <tr>
                <td>
                  <a class="btn-sm btn-primary" href="javascript:editMember({{ $member->id }})">Modifier</a>
                  <a class="btn-sm btn-danger warning-delete" href="{{ URL::route('manage_listing_delete', array('member_id' => $member->id)) }}">
                    Supprimer
                  </a>
                </td>
                <td>{{ $member->last_name }}</td>
                <td>{{ $member->first_name }}</td>
                <td>{{ $member->getHumanBirthDate() }}</td>
                <td>{{ $member->year_in_section }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    
  @else
    
    <div class="row">
      <div class="col-lg-12">
        <p>Il n'y a aucun membre dans cette section.</p>
      </div>
    </div>
    
  @endif
  
@stop