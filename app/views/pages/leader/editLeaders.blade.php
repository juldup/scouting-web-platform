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
    @if ($scout_to_leader && !Session::has('error_message'))
      editLeader({{ $scout_to_leader }});
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
    <div class="col-lg-12">
  
      <h1>Animateurs {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div id="leader_form"
       @if (!Session::has('error_message')) style="display: none;" @endif
       >
    {{ Form::open(array('files' => true, 'url' => URL::route('edit_leaders_submit', array('section_slug' => $user->currentSection->slug)))) }}
      {{ Form::hidden('member_id', 0) }}
      <div class="row">
        <div class="col-lg-6">
          <table>
            <tr>
              <th>{{ Form::label('leader_name', "Nom d'animateur") }} :</th>
              <td>{{ Form::text('leader_name', '', array('placeholder' => "Nom utilisé dans sa section")) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('leader_in_charge', "Animateur responsable") }} :</th>
              <td>{{ Form::checkbox('leader_in_charge') }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('leader_description', "Description de l'animateur") }} :</th>
              <td>{{ Form::textarea('leader_description', '', array('placeholder' => "Petite description qui apparaitra sur la page des animateurs")) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('leader_role', "Rôle de l'animateur") }} :</th>
              <td>{{ Form::text('leader_role', '', array('placeholder' => "Rôle particulier dans le staff")) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('section', "Section") }} :</th>
              <td>{{ Form::select('section', Section::getSectionsForSelect()) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('phone_member', "GSM") }} :</th>
              <td>
                {{ Form::text('phone_member') }}
                Caché : {{ Form::checkbox('phone_member_private') }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('email_member', "Adresse e-mail") }} :</th>
              <td>{{ Form::text('email_member', '', array('placeholder' => "L'adr. e-mail n'est pas publiée")) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('totem', "Totem") }} :</th>
              <td>{{ Form::text('totem') }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('quali', "Quali") }} :</th>
              <td>{{ Form::text('quali') }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('picture', "Photo") }} :</th>
              <td>
                <img class="leader_picture_mini" id="current_leader_picture" src="" />
                {{ Form::file('picture') }}
              </td>
            </tr>
          </table>
        </div>
        <div class="col-lg-6">
          <table>
            <tr>
              <th>{{ Form::label('first_name', "Prénom") }} :</th>
              <td>{{ Form::text('first_name', '', array('size' => 25)) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('last_name', "Nom") }} :</th>
              <td>{{ Form::text('last_name', '', array('size' => 25)) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('birth_date', "Date de naissance") }} :</th>
              <td>
                {{ Form::text('birth_date_day', '', array('class' => 'small', 'placeholder' => 'Jour')) }} /
                {{ Form::text('birth_date_month', '', array('class' => 'small', 'placeholder' => 'Mois')) }} /
                {{ Form::text('birth_date_year', '', array('class' => 'small', 'placeholder' => 'Année')) }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('gender', "Sexe") }} :</th>
              <td>{{ Form::select('gender', array('M' => 'Garçon', 'F' => 'Fille')) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('nationality', "Nationalité") }} :</th>
              <td>{{ Form::text('nationality', 'BE', array('class' => 'small')) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('address', "Rue et numéro") }} :</th>
              <td>{{ Form::text('address') }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('postcode', "Code postal") }} :</th>
              <td>{{ Form::text('postcode') }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('city', "Localité") }} :</th>
              <td>{{ Form::text('city') }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('has_handicap', "Handicap") }} :</th>
              <td>
                {{ Form::checkbox('has_handicap') }}
                <br />
                {{ Form::text('handicap_details', '', array('placeholder' => "Détails du handicap")) }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('comments', "Commentaires (privés)") }} :</th>
              <td>{{ Form::textarea('comments', '', array('placeholder' => 'Toute information utile à partager aux animateurs')) }}</td>
            </tr>
            <tr>
              <th>{{ Form::label('family_in_other_units', "Famille autres unités") }} :</th>
              <td>
                {{ Form::select('family_in_other_units', Member::getFamilyOtherUnitsForSelect()) }}
                {{ Form::textarea('family_in_other_units_details', '',
                          array('placeholder' => "S'il y a des membres de la même famille dans une autre unité, " .
                                                  "cela peut entrainer une réduction de la cotisation. Indiquer " .
                                                  "ici qui et dans quelle(s) unité(s).")) }}</td>
            </tr>
          </table>
        </div>
        <div class="text-center">
          {{ Form::submit('Enregistrer') }}
          <a href="javascript:dismissLeaderForm()">Fermer</a>
        </div>
      </div>
    {{ Form::close() }}
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Liste des animateurs actuels {{ $user->currentSection->de_la_section }}</h2>
      <table>
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
                <td><a href="javascript:editLeader({{ $leader->id }})">Modifier</a></td>
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
                <td>{{ $leader->phone1 }}</td>
                <td>{{ $leader->email_member }}</td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Scout devenant animateur</h2>
      <div id='scout_to_leader'>
        {{ Form::open(array('url' => URL::route('edit_leaders_member_to_leader_post',
          array('section_slug' => $user->currentSection->slug)))) }}
        Transformer
        {{ Form::select('member_id', $scouts) }}
        en animateur.
        {{ Form::close() }}
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Nouvel animateur</h2>
      <p>
        Il est recommandé de laisser un nouvel animateur s'inscrire lui-même via le
        <a href="{{ URL::route('registration') }}">formulaire d'inscription</a> pour s'assurer que ses coordonnées soient correctes et complètes. En cas d'urgence,
        il est possible d'<a href="javascript:addLeader({{ $user->currentSection->id }})">encoder un nouvel animateur ici</a>.
      </p>
    </div>
  </div>

@stop