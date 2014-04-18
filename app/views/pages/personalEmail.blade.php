@extends('base')

@section('back_links')
<p>
  <a href="{{ Session::get('personal_email_referrer') }}">
    Retour à la page précédente
  </a>
</p>
@stop

@section('content')
  @if (Session::has('success_message'))
    @include('subviews.flashMessages')
  @else
    <div class="row">
      <div class="col-md-12">
        <div class="form-horizontal well">
          {{ Form::open(array('url' => URL::route('personal_email_submit', array('contact_type' => $contact_type, 'member_id' => $member ? $member->id : 0)))) }}
            <legend>
              @if ($contact_type == PersonalEmailController::$CONTACT_TYPE_PARENTS)
                <h2>Envoyer un e-mail aux parents de {{ $member->first_name }} {{ $member->last_name }}</h2>
              @elseif ($contact_type == PersonalEmailController::$CONTACT_TYPE_PERSONAL)
                <h2>Envoyer un e-mail à {{ $member->first_name }} {{ $member->last_name }} ({{ $member->leader_name }})</h2>
              @elseif ($contact_type == PersonalEmailController::$CONTACT_TYPE_WEBMASTER)
                <h2>Envoyer un e-mail au webmaster</h2>
              @endif
            </legend>
            @include('subviews.flashMessages')
            <div class="form-group">
              {{ Form::label('subject', "Sujet", array('class' => 'control-label col-md-3')) }}
              <div class="col-md-7">
                {{ Form::text('subject', null, array('class' => 'form-control')) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('body', "Message", array('class' => 'control-label col-md-3')) }}
              <div class="col-md-7">
                {{ Form::textarea('body', null, array('class' => 'form-control', 'rows' => 10)) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('sender_name', "Votre nom", array('class' => 'control-label col-md-3')) }}
              <div class="col-md-4">
                {{ Form::text('sender_name', null, array('class' => 'form-control')) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('sender_email', "Votre adresse e-mail", array('class' => 'control-label col-md-3')) }}
              <div class="col-md-4">
                {{ Form::text('sender_email', null, array('class' => 'form-control')) }}
              </div>
              <div class="col-md-5">
                <p class="form-side-note">
                  Votre adresse e-mail sera révélée au destinataire.
                </p>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-7 col-md-offset-3">
                {{ Form::submit('Envoyer', array('class' => 'btn btn-primary')) }}
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  @endif
@stop