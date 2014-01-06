@extends('base')

@section('title')
  Gestion des inscriptions
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/manage_registration.js"></script>
  <script>
    var registrations = new Array();
    @foreach ($registrations as $member)
      registrations[{{ $member->id }}] = {
        'first_name': "{{ Helper::sanitizeForJavascript($member->first_name) }}",
        'last_name': "{{ Helper::sanitizeForJavascript($member->last_name) }}",
        'birth_date_day': "{{ Helper::getDateDay($member->birth_date) }}",
        'birth_date_month': "{{ Helper::getDateMonth($member->birth_date) }}",
        'birth_date_year': "{{ Helper::getDateYear($member->birth_date) }}",
        'gender': "{{ $member->gender }}",
        'nationality': "{{ $member->nationality }}",
        'address': "{{ Helper::sanitizeForJavascript($member->address) }}",
        'postcode': "{{ Helper::sanitizeForJavascript($member->postcode) }}",
        'city': "{{ Helper::sanitizeForJavascript($member->city) }}",
        'has_handicap': {{ $member->has_handicap ? "true" : "false" }},
        'handicap_details': "{{ Helper::sanitizeForJavascript($member->handicap_details) }}",
        'comments': "{{ Helper::sanitizeForJavascript($member->comments) }}",
        'is_leader': {{ $member->is_leader ? "true" : "false" }},
        'leader_name': "{{ Helper::sanitizeForJavascript($member->leader_name) }}",
        'leader_in_charge': {{ $member->leader_in_charge ? "true" : "false" }},
        'leader_description': "{{ Helper::sanitizeForJavascript($member->leader_description) }}",
        'leader_role': "{{ Helper::sanitizeForJavascript($member->leader_role) }}",
        'section_id': {{ $member->section_id }},
        'phone1': "{{ Helper::sanitizeForJavascript($member->phone1) }}",
        'phone1_owner': "{{ Helper::sanitizeForJavascript($member->phone1_owner) }}",
        'phone1_private': {{ $member->phone1_private ? "true" : "false" }},
        'phone2': "{{ Helper::sanitizeForJavascript($member->phone2) }}",
        'phone2_owner': "{{ Helper::sanitizeForJavascript($member->phone2_owner) }}",
        'phone2_private': {{ $member->phone2_private ? "true" : "false" }},
        'phone3': "{{ Helper::sanitizeForJavascript($member->phone3) }}",
        'phone3_owner': "{{ Helper::sanitizeForJavascript($member->phone3_owner) }}",
        'phone3_private': {{ $member->phone3_private ? "true" : "false" }},
        'phone_member': "{{ Helper::sanitizeForJavascript($member->phone_member) }}",
        'phone_member_private': {{ $member->phone_member_private ? "true" : "false" }},
        'email1': "{{ $member->email1 }}",
        'email2': "{{ $member->email2 }}",
        'email3': "{{ $member->email3 }}",
        'email_member': "{{ $member->email_member }}",
        'totem': "{{ Helper::sanitizeForJavascript($member->totem) }}",
        'quali': "{{ Helper::sanitizeForJavascript($member->quali) }}",
        'family_in_other_units': {{ $member->family_in_other_units }},
        'family_in_other_units_details' : "{{ Helper::sanitizeForJavascript($member->family_in_other_units_details) }}",
      };
    @endforeach
  </script>
@stop

@section('content')
  
  <div class="row">
    <div class="pull-right">
      <p class='management'>
        <a class='button' href='{{ URL::route('registration', array('section_slug' => $user->currentSection->slug)) }}'>
          Retour à la page
        </a>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Gestion des inscriptions {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div id="member_form" class="table-based-form"
       @if (!Session::has('_old_input')) style="display: none;" @endif
       >
    {{ Form::open(array('files' => true, 'url' => URL::route('manage_registration_submit', array('section_slug' => $user->currentSection->slug)))) }}
      {{ Form::hidden('member_id') }}
      <div class="row">
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
              <th>{{ Form::label('phone1', "Téléphone 1") }} :</th>
              <td>
                {{ Form::text('phone1', '', array('class' => 'medium')) }}
                de {{ Form::text('phone1_owner', '', array('class' => 'medium')) }}
                Caché : {{ Form::checkbox('phone1_private') }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('phone2', "Téléphone 2") }} :</th>
              <td>
                {{ Form::text('phone2', '', array('class' => 'medium')) }}
                de {{ Form::text('phone2_owner', '', array('class' => 'medium')) }}
                Caché : {{ Form::checkbox('phone2_private') }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('phone3', "Téléphone 3") }} :</th>
              <td>
                {{ Form::text('phone3', '', array('class' => 'medium')) }}
                de {{ Form::text('phone3_owner', '', array('class' => 'medium')) }}
                Caché : {{ Form::checkbox('phone3_private') }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('phone_member', "GSM du scout") }} :</th>
              <td>
                {{ Form::text('phone_member') }}
                Caché : {{ Form::checkbox('phone_member_private') }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('email1', "Adresse e-mail 1") }} :</th>
              <td>
                {{ Form::text('email1', '', array('placeholder' => "L'adr. e-mail n'est pas publiée")) }}
                <br />
                {{ Form::text('email2', '', array('placeholder' => "L'adr. e-mail n'est pas publiée")) }}
                <br />
                {{ Form::text('email3', '', array('placeholder' => "L'adr. e-mail n'est pas publiée")) }}
              </td>
            </tr>
            <tr>
              <th>{{ Form::label('email_member', "Adresse e-mail du scout") }} :</th>
              <td>{{ Form::text('email_member', '', array('placeholder' => "L'adr. e-mail n'est pas publiée")) }}</td>
            </tr>
          </table>
        </div>
        <div class="col-lg-6">
          <table>
            <tr>
              <th>{{ Form::label('section', "Section") }} :</th>
              <td>{{ Form::select('section', Section::getSectionsForSelect()) }}</td>
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
              <th>{{ Form::label('is_leader', "Animateur") }} :</th>
              <td>{{ Form::checkbox('is_leader') }}</td>
            </tr>
            <tr class='leader_specific'>
              <th>{{ Form::label('leader_name', "Nom d'animateur") }} :</th>
              <td>{{ Form::text('leader_name', '', array('placeholder' => "Nom utilisé dans sa section")) }}</td>
            </tr>
            <tr class='leader_specific'>
              <th>{{ Form::label('leader_in_charge', "Animateur responsable") }} :</th>
              <td>{{ Form::checkbox('leader_in_charge') }}</td>
            </tr>
            <tr class='leader_specific'>
              <th>{{ Form::label('leader_description', "Description de l'animateur") }} :</th>
              <td>{{ Form::textarea('leader_description', '', array('placeholder' => "Petite description qui apparaitra sur la page des animateurs")) }}</td>
            </tr>
            <tr class='leader_specific'>
              <th>{{ Form::label('leader_role', "Rôle de l'animateur") }} :</th>
              <td>{{ Form::text('leader_role', '', array('placeholder' => "Rôle particulier dans le staff")) }}</td>
            </tr>
            <tr class='leader_specific'>
              <th>{{ Form::label('picture', "Photo") }} :</th>
              <td>
                {{ Form::file('picture') }}
              </td>
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
                                                  "ici qui et dans quelle(s) unité(s).")) }}
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="text-center">
            {{ Form::submit('Enregistrer') }}
            <a href="javascript:dismissMemberForm()">Fermer</a>
          </div>
        </div>
      </div>
    {{ Form::close() }}
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Inscriptions en attentes pour {{ $user->currentSection->la_section }}</h2>
      @if (count($registrations))
      <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th></th>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Animateur</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($registrations as $member)
              <tr>
                <td><a class="btn-sm btn-primary" href="javascript:editRegistration({{ $member->id }})">Inscrire</a></td>
                <td>{{ $member->first_name }}</td>
                <td>{{ $member->last_name }}</td>
                <td>{{ $member->is_leader ? "Oui" : "Non" }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        @if (count($other_sections))
          <p>Il n'y a pas de demande d'inscription pour {{ $user->currentSection->la_section }}.</p>
        @else
          <p>Il n'y a aucune demande d'inscription en attente.</p>
        @endif  
      @endif
      
      @if (count($other_sections))
        <p>Il y a des demandes d'inscription en attente dans d'autres sections :
          <?php $first = true; ?>
          @foreach ($other_sections as $other_section)
            @if ($first) <?php $first = false; ?>
            @else –
            @endif
            <a href='{{ URL::route('manage_registration', array('section_slug' => $other_section->slug)) }}'>
              {{ $other_section->name}}
            </a>
          @endforeach
        </p>
      @endif
      
    </div>
  </div>
  
@stop