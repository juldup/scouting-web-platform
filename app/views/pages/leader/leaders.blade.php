@extends('base')

@section('title')
  Animateurs
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  @if ($is_leader)
    <div class="row">
      <p class='pull-right management'>
        <a class='button' href='{{ URL::route('edit_leaders', array('section_slug' => $user->currentSection->slug)) }}'>
          Modifier les animateurs
        </a>
      </p>
      <p class='pull-right management'>
        <a class='button' href='{{ URL::route('edit_privileges', array('section_slug' => $user->currentSection->slug)) }}'>
          Modifier les privilèges des animateurs
        </a>
      </p>
    </div>
  @endif
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Animateurs {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <?php $currentLeaderInCharge = false; ?>
  @foreach ($leaders as $leader)
    @if ($currentLeaderInCharge != $leader->leader_in_charge)
      <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-8">
          <h3>
            @if ($leader->leader_in_charge)
              <?php $currentLeaderInCharge = true; ?>
              @if ($user->currentSection->id == 1)
                @if ($count_in_charge > 1 && $men_in_charge) Animateurs d'unité
                @elseif ($count_in_charge > 1 && !$men_in_charge) Animatrices d'unité
                @elseif ($men_in_charge) Animateur d'unité
                @else Animatrice d'unité
                @endif
              @else
                @if ($count_in_charge > 1 && $men_in_charge) Animateurs responsables
                @elseif ($count_in_charge > 1 && !$men_in_charge) Animatrices responsables
                @elseif ($men_in_charge) Animateur responsable
                @else Animatrice responsable
                @endif
              @endif
            @else
              <?php $currentLeaderInCharge = false; ?>
              @if ($user->currentSection->id == 1)
                @if ($count_others > 1 && $men_in_others) Équipiers d'unité
                @elseif ($count_others > 1 && !$men_in_others) Équipières d'unité
                @elseif ($men_in_others) Équipier d'unité
                @else Équipière d'unité
                @endif
              @else
                @if ($count_others > 1 && $men_in_others) Équipiers
                @elseif ($count_others > 1 && !$men_in_others) Équipières
                @elseif ($men_in_others) Équipier
                @else Équipière
                @endif
              @endif
            @endif
          </h3>
        </div>
      </div>
    @endif
    <div class="row">
      <div class="col-lg-4">
        @if ($leader->has_photo)
          <img src="{{ $leader->getPhotoURL }}" />
        @endif
      </div>
      <div class="col-lg-8">
        <p>{{ $leader->leader_name }}</p>
        <p>{{ Helper::rawToHTML($leader->leader_description) }}</p>
        @if ($leader->leader_role)
          <p>Rôle : {{ Helper::rawToHTML($leader->leader_role) }}</p>
        @endif
        <p>Nom : {{ $leader->firstname }} {{ $leader->lastname }}</p>
        @if (!$leader->phone1_private)
          <p>Tél. : {{ $leader->phone1 }}</p>
        @endif
        <p>E-mail : <a href="">Envoyer un e-mail</a></p>
      </div>
    </div>
  @endforeach
  
@stop