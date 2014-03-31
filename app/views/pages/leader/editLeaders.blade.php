@extends('base')

@section('title')
  Gestion des animateurs
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit-leaders.js"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var leaders = new Array();
    var ownedLeaders = new Array();
    @foreach ($leaders as $leader)
      leaders[{{ $leader->id }}] = @include('subviews.memberToJavascript', array('member' => $leader));
      @if ($user->isOwnerOfMember($leader->id))
        ownedLeaders.push("{{ $leader->id }}");
      @endif
    @endforeach
    @if ($scout_to_leader && !Session::has('_old_input'))
      editLeader({{ $scout_to_leader }}, true);
    @endif
  </script>
@stop

@section('forward_links')
  <p>
    <a href='{{ URL::route('edit_privileges', array('section_slug' => $user->currentSection->slug)) }}'>
      Modifier les privilèges des animateurs
    </a>
  </p>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('leaders', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la page des animateurs
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Animateurs {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @include('subviews.editMemberForm', array('form_legend' => "Modifier un animateur", 'submit_url' => URL::route('edit_leaders_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => true, 'edit_identity' => $can_edit_all, 'edit_totem' => $can_edit_limited, 'edit_leader' => $can_edit_limited, 'edit_section' => $can_change_section, 'edit_others' => $can_edit_limited, 'edit_contact' => $can_edit_limited))
  @include('subviews.editMemberForm', array('form_legend' => "Modifier mes données personnelles", 'submit_url' => URL::route('edit_leaders_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => true, 'edit_identity' => $can_edit_own_data, 'edit_totem' => $can_edit_own_data,'edit_leader' => $can_edit_own_data, 'edit_section' => $can_change_section, 'form_id' => 'own-data-form', 'edit_others' => $can_edit_own_data, 'edit_contact' => $can_edit_own_data))
  
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
                <td>
                  @if ($user->isOwnerOfMember($leader->id))
                    <a class="btn-sm btn-primary" href="javascript:editOwnData({{ $leader->id }})">
                      @if ($can_edit_own_data)
                        Modifier
                      @else
                        Voir
                      @endif
                    </a>
                  @else
                    <a class="btn-sm btn-primary" href="javascript:editLeader({{ $leader->id }})">
                      @if ($can_edit_limited || $can_edit_all)
                        Modifier
                      @else
                        Voir
                      @endif
                    </a>
                  @endif
                  @if ($can_delete)
                    <a class="btn-sm btn-danger warning-delete"
                       href="{{ URL::route('edit_leaders_delete', array('member_id' => $leader->id, 'section_slug' => $user->currentSection->slug)) }}">
                      Supprimer
                    </a>
                  @endif
                </td>
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
  
  @if ($can_add_leader)
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
  @endif

@stop