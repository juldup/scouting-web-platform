@extends('base')

@section('title')
  Gestion des réinscriptions
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit-reregistration.js"></script>
  <script>
    var reregisterMemberURL = "{{ URL::route('ajax_reregister') }}";
    var unreregisterMemberURL = "{{ URL::route('ajax_cancel_reregistration') }}";
    var deleteMemberURL = "{{ URL::route('ajax_delete_member') }}";
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
  
  @include('subviews.contextualHelp', array('help' => 'edit-reregistrations'))
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'reregistration'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Réinscription des membres actifs {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover wide-table">
        <tbody>
          @foreach ($active_members as $member)
            <?php $unreregistered = $member->isReregistered() ? " style='display: none;' " : "" ?>
            <?php $reregistered = $member->isReregistered() ? "" : " style='display: none;' " ?>
            <tr class="member-row" data-member-id="{{ $member->id }}">
              <th class="space-on-right">
                <span class="member-name">
                  {{{ $member->first_name }}} {{{ $member->last_name }}}
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