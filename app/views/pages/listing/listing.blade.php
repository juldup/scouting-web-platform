@extends('base')

@section('title')
  Listing
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_members.js"></script>
  <script>
    var members = new Array();
    @foreach ($editable_members as $member)
      members[{{ $member->id }}] = @include ('subviews.memberToJavascript', array('member' => $member));
    @endforeach
  </script>
@stop

@section('content')
  
  @if ($can_manage)
    <div class="row">
      <div class="pull-right">
        <p class='management'>
          <a class='button' href='{{ URL::route('manage_listing', array('section_slug' => $user->currentSection->slug)) }}'>
            Modifier le listing
          </a>
        </p>
      </div>
    </div>
  @endif
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      @include('subviews.editMemberForm', array('form_legend' => "Modifier un membre", 'submit_url' => URL::route('listing_submit', array('section_slug' => $user->currentSection->slug)), 'leader_only' => false, 'edit_identity' => true, 'edit_section' => $can_change_section, 'edit_totem' => $can_manage,'edit_leader' => false))
    </div>
  </div>
  
  @foreach ($sections as $sct)
  
    <div class="row">
      <div class="col-lg-12">
        <h1>Listing {{ $sct['section_data']->de_la_section }}</h1>
      </div>
    </div>
  
    @if ($sct['members']->count())
    
      <div class="row">
        <div class="col-lg-12">
          <table class="table table-striped table-hover">
            <thead>
              <th></th>
              <th>Nom</th>
              <th>Prénom</th>
              @if ($sct['show_totem'])
                <th>Totem</th>
              @endif
              @if ($sct['show_subgroup'])
                <th>{{ $sct['section_data']->subgroup_name }}</th>
              @endif
              <th>Téléphone</th>
              <th>E-mail</th>
            </thead>
            <tbody>
              @foreach ($sct['members'] as $member)
              <tr>
                <td>
                  @if ($user->isOwnerOfMember($member))
                    <a class="btn-sm btn-primary" href="javascript:editMember({{ $member->id }})">Modifier</a>
                  @endif
                </td>
                <td>{{ $member->last_name }}</td>
                <td>{{ $member->first_name }}</td>
                  @if ($sct['show_totem'])
                    <td>{{ $member->totem }}</td>
                  @endif
                  @if ($sct['show_subgroup'])
                    <td>{{ $member->subgroup }}</td>
                  @endif
                <td>{{ $member->getPublicPhone() }}</td>
                <td><a class="btn-sm btn-primary" href="">Envoyer un e-mail</a></td>
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
  @endforeach
  
@stop