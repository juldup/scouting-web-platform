@extends('base')

@section('title')
  Listing
@stop

@section('head')
  <meta name="robots" content="noindex">
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
  
  @foreach ($sections as $sct)
  
    <div class="row">
      <div class="col-lg-12">
        <h1>Listing {{ $sct['section_data']->de_la_section }}</h1>
        @include('subviews.flashMessages')
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-12">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
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
              <td>{{ $member->last_name }}</td>
              <td>{{ $member->first_name }}</td>
                @if ($sct['show_totem'])
                  <td>{{ $member->totem }}</td>
                @endif
                @if ($sct['show_subgroup'])
                  <td>{{ $member->subgroup }}</td>
                @endif
              <td>{{ $member->getPublicPhone() }}</td>
              <td><a href="">Envoyer un e-mail</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endforeach
  
@stop