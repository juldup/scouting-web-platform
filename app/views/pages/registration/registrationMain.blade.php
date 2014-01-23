@extends('base')

@section('title')
  Inscriptions
@stop

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_edit)
    <p>
      <a href='{{ URL::route('edit_registration_page') }}'>
        Modifier cette page
      </a>
    </p>
  @endif
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_registration') }}'>
        Gérer les inscriptions
      </a>
    </p>
  @endif
@stop

@section('content')
  <div class="row page_body">
    <div class="col-md-12">
      <h1>{{ $page_title }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  @if (count($family_members))
    <form class="well form-horizontal">
      <legend>Réinscription des membres de votre famille pour l'année {{ $reregistration_year }}</legend>
      @foreach ($family_members as $member)
        <div class="form-group">
          <div class="col-md-12">
            @if ($member->last_reregistration == $reregistration_year)
              {{ $member->first_name }} {{ $member->last_name }} est réinscrit{{ $member->gender == 'F' ? 'e' : '' }}.
            @else
              <a class="btn btn-default" href="{{ URL::route('reregistration', array('member_id' => $member->id)) }}">
                Réinscrire {{ $member->first_name }} {{ $member->last_name }}
              </a>
            @endif
          </div>
        </div>
      @endforeach
    </form>
  @endif
  <div class="row page_body">
    <div class="col-md-12">
      {{ $page_body }}
    </div>
  </div>
@stop
