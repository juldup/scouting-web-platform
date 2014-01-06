@extends('base')

@section('title')
  Gestion des animateurs
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_leaders.js"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var leaders = new Array();
    @foreach ($leaders as $leader)
      leaders[{{ $leader->id }}] = {
        'first_name': "{{ Helper::sanitizeForJavascript($leader->first_name) }}",
        'last_name': "{{ Helper::sanitizeForJavascript($leader->last_name) }}",
        'birth_date_day': "{{ Helper::getDateDay($leader->birth_date) }}",
        'birth_date_month': "{{ Helper::getDateMonth($leader->birth_date) }}",
        'birth_date_year': "{{ Helper::getDateYear($leader->birth_date) }}",
        'gender': "{{ $leader->gender }}",
        'nationality': "{{ $leader->nationality }}",
        'address': "{{ Helper::sanitizeForJavascript($leader->address) }}",
        'postcode': "{{ Helper::sanitizeForJavascript($leader->postcode) }}",
        'city': "{{ Helper::sanitizeForJavascript($leader->city) }}",
        'has_handicap': {{ $leader->has_handicap ? "true" : "false" }},
        'handicap_details': "{{ Helper::sanitizeForJavascript($leader->handicap_details) }}",
        'comments': "{{ Helper::sanitizeForJavascript($leader->comments) }}",
        'leader_name': "{{ Helper::sanitizeForJavascript($leader->leader_name) }}",
        'leader_in_charge': {{ $leader->leader_in_charge ? "true" : "false" }},
        'leader_description': "{{ Helper::sanitizeForJavascript($leader->leader_description) }}",
        'leader_role': "{{ Helper::sanitizeForJavascript($leader->leader_role) }}",
        'section_id': {{ $leader->section_id }},
        'phone_member': "{{ Helper::sanitizeForJavascript($leader->phone_member) }}",
        'phone_member_private': {{ $leader->phone_member_private ? "true" : "false" }},
        'email': "{{ $leader->email_member }}",
        'totem': "{{ Helper::sanitizeForJavascript($leader->totem) }}",
        'quali': "{{ Helper::sanitizeForJavascript($leader->quali) }}",
        'family_in_other_units': {{ $leader->family_in_other_units }},
        'family_in_other_units_details' : "{{ Helper::sanitizeForJavascript($leader->family_in_other_units_details) }}",
        'has_picture': {{ $leader->has_picture ? "true" : "false" }},
        'picture_url': "{{ $leader->has_picture ? $leader->getPictureURL() : "" }}"
      };
    @endforeach
    @if ($scout_to_leader && !Session::has('_old_input'))
      editLeader({{ $scout_to_leader }}, true);
    @endif
  </script>
@stop

@section('content')
  
  <div class="row">
    <div class="pull-right">
      <p class='management'>
        <a class='button' href='{{ URL::route('leaders', array('section_slug' => $user->currentSection->slug)) }}'>
          Retour à la page
        </a>
      </p>
      <p class='management'>
        <a class='button' href='{{ URL::route('edit_privileges', array('section_slug' => $user->currentSection->slug)) }}'>
          Modifier les privilèges des animateurs
        </a>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
  
      <h1>Animateurs {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @include('subviews.editMemberForm', array('form_legend' => "Modifier un animateur", 'submit_url' => URL::route('edit_leaders_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => true, 'edit_identity' => true, 'edit_section' => false, 'edit_totem' => true,'edit_leader' => true, 'edit_section' => true))
    
  <div class="row">
    <div class="col-md-12">
      <h2>Liste des animateurs actuels {{ $user->currentSection->de_la_section }}</h2>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th></th>
            <th>Nom d'animateur</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Photo</th>
            <th>Téléphone</th>
            <th>E-mail</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($leaders as $leader)
            @if ($leader->id != $scout_to_leader)
              <tr>
                <td><a class="btn-sm btn-primary" href="javascript:editLeader({{ $leader->id }})">Modifier</a></td>
                <td>{{ $leader->leader_name }} @if ($leader->leader_in_charge) (responsable) @endif</td>
                <td>{{ $leader->first_name }}</td>
                <td>{{ $leader->last_name }}</td>
                <td>
                  @if ($leader->has_picture)
                    <img class="leader_picture_mini" alt="Photo de {{ $leader->leader_name }}" src="{{ $leader->getPictureURL() }}" />
                  @else
                    Pas de photo
                  @endif
                </td>
                <td>{{ $leader->phone_member }}</td>
                <td>{{ $leader->email_member }}</td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <h2>Scout devenant animateur</h2>
      <div id='scout_to_leader' class="form-horizontal">
        {{ Form::open(array('url' => URL::route('edit_leaders_member_to_leader_post',
          array('section_slug' => $user->currentSection->slug)))) }}
          <p class="form-side-note float-left">
            Transformer&nbsp;
          </p>
          <p class="float-left">
            {{ Form::select('member_id', $scouts, '', array('class' => 'form-control large')) }}
          </p>
          <p class="form-side-note">
            &nbsp;en animateur.
          <p>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <h2>Nouvel animateur</h2>
      <p>
        Il est recommandé de laisser un nouvel animateur s'inscrire lui-même via le
        <a href="{{ URL::route('registration_form') }}">formulaire d'inscription</a> pour s'assurer que ses coordonnées soient correctes et complètes. En cas d'urgence,
        il est possible d'<a href="javascript:addLeader({{ $user->currentSection->id }})">encoder un nouvel animateur ici</a>.
      </p>
    </div>
  </div>

@stop