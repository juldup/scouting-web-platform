@extends('base')

@section('title')
  Contacts
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  <div class='row'>
    <h2>Contacter les animateurs d'unité</h2>
  </div>
  
  @foreach ($unitLeaders as $leader)
    <div class='row'>
      <div class="col-lg-1">
        <span class='important'>{{ $leader->leader_name }}</span>
      </div>
      <div class="col-lg-2">
        @if ($leader->leader_in_charge)
          @if ($leader->gender == "F") animatrice d'unité @else animateur d'unité @endif
        @else
          @if ($leader->gender == "F") assistante d'unité @else assistant d'unité @endif
        @endif
      </div>
      <div class="col-lg-2">
        {{ $leader->first_name }} {{ $leader->last_name }}
      </div>
      <div class="col-lg-1">
        @if ($leader->phone1) {{ $leader->phone1 }} @endif
      </div>
      <div class="col-lg-2">
        <a class='button' href='envoiEmail.php?dest={{ $leader->id }}'>contacter par e-mail</a>
      </div>
    </div>
  @endforeach
  
  <div class='row'>
    <h2 id='responsables'>Contacter les responsables des sections</h2>
  </div>
  
  @foreach ($sectionLeaders as $leader)
    <div class='row'>
      <div class="col-lg-1">
        {{ $leader->getSection()->name }}
      </div>
      <div class="col-lg-2">
        <span class='important'>{{ $leader->leader_name }}</span>
      </div>
      <div class="col-lg-2">
        {{ $leader->first_name }} {{ $leader->last_name }}
      </div>
      <div class="col-lg-1">
        @if ($leader->phone1) {{ $leader->phone1 }} @endif
      </div>
      <div class="col-lg-2">
        <a class='button' href='mailto:{{ $leader->getSection()->email }}'>contacter par e-mail</a>
      </div>
    </div>
  @endforeach
  
  <div class='row'>
    <h2 id='contactAdmin'>Contacter le webmaster (Julien Dupuis)</h2>
    <p>
      <span class='important'>Webmaster</span>
      : Julien Dupuis, 0496/628.600
      <a class='button' href='envoiEmail.php?dest=webmaster'>contacter par e-mail</a>
    </p>
  </div>
@stop