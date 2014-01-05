@extends('base')

@section('title')
  Contacts
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')

  <div class="well">
    <legend>Contacter les animateurs d'unité</legend>

    @foreach ($unitLeaders as $leader)
      <div class='row'>
        <div class="col-md-3">
          <p>
            <strong>{{ $leader->leader_name }}</strong>
            @if ($leader->leader_in_charge)
              @if ($leader->gender == "F") (animatrice d'unité) @else (animateur d'unité) @endif
            @else
              @if ($leader->gender == "F") (assistante d'unité) @else (assistant d'unité) @endif
            @endif
          </p>
        </div>
        <div class="col-md-3">
          <p>{{ $leader->first_name }} {{ $leader->last_name }}</p>
        </div>
        <div class="col-md-2">
          <p>
            @if ($leader->phone_member && !$leader->phone_member_private) {{ $leader->phone_member }} @endif
          </p>
        </div>
        <div class="col-md-4">
          <a class='btn-sm btn-primary' href='envoiEmail.php?dest={{ $leader->id }}'>Contacter {{ $leader->leader_name }} par e-mail</a>
        </div>
      </div>
    @endforeach
  </div>
  
  <div class='well'>
    <legend>Contacter les responsables des sections</legend>
    @foreach ($sectionLeaders as $leader)
      <div class='row'>
        <div class="col-md-3">
          <p><strong>{{ $leader->getSection()->name }}</strong></p>
        </div>
        <div class="col-md-3">
          <p>{{ $leader->first_name }} {{ $leader->last_name }} ({{ $leader->leader_name }})</p>
        </div>
        <div class="col-md-2">
          <p>
            @if ($leader->phone_member && !$leader->phone_member_private) {{ $leader->phone_member }} @endif
          </p>
        </div>
        <div class="col-md-3">
          <a class='btn-sm btn-primary' href='mailto:{{ $leader->getSection()->email }}'>Contacter {{ $leader->leader_name }} par e-mail</a>
        </div>
      </div>
    @endforeach
  </div>
  
  <div class='well'>
    <legend>Contacter le webmaster (Julien Dupuis)</legend>
    <div class='row'>
      <div class="col-md-3">
        Webmaster
      </div>
      <div class="col-md-3">
        <span class='important'>{{ $webmaster['name'] }}</span>
      </div>
      <div class="col-md-2">
        {{ $webmaster['phone'] }}
      </div>
      <div class="col-md-4">
        <a class='btn-sm btn-primary' href='envoiEmail.php?dest=webmaster'>Contacter {{ $webmaster['name'] }} par e-mail</a>
      </div>
    </div>
  </div>
  
@stop