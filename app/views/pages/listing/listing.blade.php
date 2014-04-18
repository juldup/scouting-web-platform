@extends('base')

@section('title')
  Listing
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit-members.js"></script>
  <script>
    var members = new Array();
    @foreach ($editable_members as $member)
      members[{{ $member->id }}] = @include ('subviews.memberToJavascript', array('member' => $member));
    @endforeach
  </script>
@stop

@section('forward_links')
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_listing', array('section_slug' => $user->currentSection->slug)) }}'>
        Gérer le listing
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @include('subviews.editMemberForm', array('form_legend' => "Modifier un membre", 'submit_url' => URL::route('listing_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => false, 'edit_identity' => true, 'edit_section' => $can_change_section, 'edit_totem' => $can_manage,'edit_leader' => false))
    </div>
  </div>

  @if ($user->currentSection->id == 1)
    <div class="row">
      <div class="col-md-12">
        <h1>
          Listing {{{ $user->currentSection->de_la_section }}}
        </h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-right">
        <p>
          <a class="btn-sm btn-default" href="{{ URL::route('download_listing', array('section_slug' => $user->currentSection->slug)) }}">
            Télécharger le listing de toute l'unité
          </a>
        </p>
      </div>
    </div>
  @endif
  
  @foreach ($sections as $sct)
  
    <div class="row">
      <div class="col-md-12">
        <h2>
          Listing {{{ $sct['section_data']->de_la_section }}}
        </h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 text-right">
        <p>
          <a class="btn-sm btn-default" href="{{ URL::route('download_listing', array('section_slug' => $sct['section_data']->slug)) }}">
            Télécharger le listing {{{ $sct['section_data']->de_la_section }}}
          </a>
        </p>
      </div>
    </div>
  
    @if ($sct['members']->count())
    
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped table-hover">
            <thead>
              <th></th>
              <th>Nom</th>
              <th>Prénom</th>
              @if ($sct['show_totem'])
                <th>Totem</th>
              @endif
              @if ($sct['show_subgroup'])
                <th>{{{ $sct['section_data']->subgroup_name }}}</th>
              @endif
              <th>Téléphone</th>
              <th>E-mail</th>
            </thead>
            <tbody>
              @foreach ($sct['members'] as $member)
                <tr>
                  <td>
                    <a class="btn-sm btn-default" href="javascript:showMemberDetails({{ $member->id }})">Détails</a>
                    @if ($user->isOwnerOfMember($member))
                      <a class="btn-sm btn-primary" href="javascript:editMember({{ $member->id }})">Modifier</a>
                    @endif
                  </td>
                  <td>{{{ $member->last_name }}}</td>
                  <td>{{{ $member->first_name }}}</td>
                    @if ($sct['show_totem'])
                      <td>{{{ $member->totem }}}</td>
                    @endif
                    @if ($sct['show_subgroup'])
                      <td>{{{ $member->subgroup }}}</td>
                    @endif
                  <td>{{{ $member->getPublicPhone() }}}</td>
                  <td>
                    <a class="btn-sm btn-default" href="{{ URL::route('personal_email', array("contact_type" => PersonalEmailController::$CONTACT_TYPE_PARENTS, "member_id" => $member->id)) }}">
                      Envoyer un e-mail
                    </a>
                  </td>
                </tr>
                <tr id="details_{{ $member->id }}" class="details_member" style="display: none;">
                  <td colspan="{{ 5 + ($sct['show_totem'] ? 1 : 0) + ($sct['show_subgroup'] ? 1 : 0) }}">
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Adresse :
                      </div>
                      <div class="col-md-9">
                        {{{ $member->address}}} <br /> {{{ $member->postcode }}} {{{ $member->city }}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Téléphone :
                      </div>
                      <div class="col-md-9">
                        {{ $member->getAllPublicPhones("<span class='horiz-divider'></span>", $user->isLeader()) }}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Sexe :
                      </div>
                      <div class="col-md-9">
                        {{{ $member->gender == 'M' ? "Garçon" : "Fille" }}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 member-detail-label">
                        Date de naissance :
                      </div>
                      <div class="col-md-9">
                        {{{ $member->getHumanBirthDate() }}}
                      </div>
                    </div>
                    @if ($member->quali)
                      <div class="row">
                        <div class="col-md-3 member-detail-label">
                          Totem et quali :
                        </div>
                        <div class="col-md-9">
                          {{{ $member->totem }}} {{{ $member->quali }}}
                        </div>
                      </div>
                    @endif
                    @if ($user->isLeader())
                      <div class="row">
                        <div class="col-md-3 member-detail-label">
                          Adresse e-mail :
                        </div>
                        <div class="col-md-9">
                          {{ $member->getAllEmailAddresses("<span class='horiz-divider'></span>") }}
                        </div>
                      </div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    
    @else
      
      <div class="row">
        <div class="col-md-12">
          <p>Il n'y a aucun membre dans cette section.</p>
        </div>
      </div>
      
    @endif
  @endforeach
  
@stop