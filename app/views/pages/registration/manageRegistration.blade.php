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

@section('back_links')
  <p>
    <a href='{{ URL::route('registration', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la page d'inscription
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Gestion des inscriptions {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <?php echo $__env->make('subviews.editMemberForm', array(
      'form_legend' => "Inscription d'un membre",
      'submit_url' => URL::route('manage_registration_submit', array('section_slug' => $user->currentSection->slug)),
      'edit_identity' => true,
      'edit_section' => true,
      'edit_totem' => true,
      'edit_leader' => true), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  
  <div class="row">
    <div class="col-md-12">
      <h2>Nouvelles inscriptions en attente pour {{ $user->currentSection->la_section }}</h2>
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
  
  <div class="row">
    <div class="col-md-12">
      <h2>Réinscription des membres actifs {{ $user->currentSection->de_la_section }}</h2>
      <table class="table table-striped table-hover reregistration-table">
        <tbody>
          @foreach ($active_members as $member)
            <?php $unreregistered = $member->isReregistered() ? " style='display: none;' " : "" ?>
            <?php $reregistered = $member->isReregistered() ? "" : " style='display: none;' " ?>
            <tr class="member-row" data_member_id="{{ $member-> id }}">
              <th>
                <span class="member-name">
                  {{ $member->first_name }} {{ $member->last_name }}
                </span>
                <span class="reregistered" {{ $reregistered }}>
                  est réinscrit
                </span>
              </th>
              <td>
                <a class='btn-sm btn-primary unreregistered reregister-member-button' href="" {{ $unreregistered }}>
                  Réinscrire
                </a>
              </td>
              <td>
                <a class='btn-sm btn-warning unreregistered delete-member-button' href="" {{ $unreregistered }}>
                  Désinscrire
                </a>
              </td>
              <td>
                <a class='btn-sm btn-default cancel-reregistration-button reregistered' href="" {{ $reregistered }}>
                  Annuler la réinscription
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@stop