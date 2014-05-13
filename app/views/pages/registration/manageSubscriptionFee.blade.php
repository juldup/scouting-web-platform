@extends('base')

@section('title')
  Gestion des cotisations
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-subscription-fee.js') }}"></script>
  <script>
    commitSubscriptionFeeChangesURL = "{{ URL::route('ajax_update_subscription_fee') }}";
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
  
  @include('subviews.contextualHelp', array('help' => 'edit-subscription-fee'))
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'subscription_fee'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Paiement des cotisations</h1>
    </div>
  </div>
  
  @if (count($members))
    <div class="row">
      <div class="col-md-12">
        @include('subviews.flashMessages')
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
      </div>
    </div>
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-xs-4 col-sm-5 text-right">
          <strong>
            Cotisation non payée
          </strong>
        </div>
        <div class="col-xs-4 col-sm-2"></div>
        <div class="col-xs-4 col-sm-5" />
          <strong>
            Cotisation payée
          </strong>
        </div>
      </div>
      @foreach ($members as $member)
        <div class="form-group {{ $member->subscription_paid ? "paid-member" : "unpaid-member" }}" data-member-id="{{ $member->id }}">
          <div class="col-xs-4 col-sm-5 text-right">
            <div class="fee-unpaid">
              {{{ $member->last_name }}} {{{ $member->first_name }}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-2 text-center">
            <a class="btn-sm btn-primary toggle-subscription-paid-button fee-unpaid">
              <span class="glyphicon glyphicon-arrow-right"></span>
            </a>
            <a class="btn-sm btn-default toggle-subscription-paid-button fee-paid">
              <span class="glyphicon glyphicon-arrow-left"></span>
            </a>
          </div>
          <div class="col-xs-4 col-sm-5 text-left">
            <div class="fee-paid">
              {{{ $member->last_name }}} {{{ $member->first_name }}}
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="row">
      <div class="col-md-12">
        <p>Il n'y a aucun membre dans l'unité.</p>
      </div>
    </div>
  @endif
  
  <div id="pending-commit" style="display: none;"><span class="glyphicon glyphicon-refresh"></span></div>
  
@stop
