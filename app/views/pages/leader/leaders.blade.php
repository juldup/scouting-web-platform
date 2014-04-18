@extends('base')

@section('title')
  Animateurs
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('forward_links')
  @if ($is_leader)
    <p>
      <a href='{{ URL::route('edit_leaders', array('section_slug' => $user->currentSection->slug)) }}'>
        Modifier les animateurs
      </a>
    </p>
    <p>
      <a href='{{ URL::route('edit_privileges', array('section_slug' => $user->currentSection->slug)) }}'>
        Modifier les privilèges des animateurs
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Animateurs {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <?php $currentLeaderInCharge = false; ?>
    @foreach ($leaders as $leader)
      @if ($currentLeaderInCharge != $leader->leader_in_charge)
        </div>
        <div class="row">
          <div class="col-md-12">
            <legend>
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
            </legend>
          </div>
        </div>
        <div class="row">
      @endif
      <div class="col-md-6 leader-card">
        <div class="well">
          <div class="row">
            <div class="col-md-6">
              @if ($leader->has_picture)
                <img class="leader_picture" src="{{ $leader->getPictureURL() }}" />
              @else
                <img class="leader_picture" src="" alt=" Pas de photo " />
              @endif
            </div>
            <div class="col-md-6">
              <p class="leader-name">{{{ $leader->leader_name }}}</p>
              <p class="leader-real-name">{{{ $leader->first_name }}} {{{ $leader->last_name }}}</p>
              <p><em>{{ Helper::rawToHTML($leader->leader_description) }}</em></p>
              @if ($leader->leader_role)
                <p><strong>Rôle :</strong> {{ Helper::rawToHTML($leader->leader_role) }}</p>
              @endif
              @if (!$leader->phone_member_private && $leader->phone_member)
                <p><strong>GSM :</strong> {{{ $leader->phone_member }}}</p>
              @endif
              @if ($leader->email_member)
                <p>
                  <strong>E-mail :</strong>
                  <a class="btn-sm btn-primary" href="{{ URL::route('personal_email', array('contact_type' => PersonalEmailController::$CONTACT_TYPE_PERSONAL, 'member_id' => $leader->id)) }}">
                    Envoyer un e-mail
                  </a>
                </p>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  
@stop