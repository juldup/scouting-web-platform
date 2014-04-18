@extends('base')

@section('title')
  Fiche santé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('back_links')
  <p>
    <a href="{{ URL::route('health_card') }}">
      Retour à la page de fiches santé
    </a>
  </p>
@stop

@section('content')

  <div class="row">
    <div class="col-md-12">
      <h1>Gestion des fiche santé {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @if (count($members))
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <table class="table table-striped table-hover ">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Dernière signature</th>
              <th>Destruction automatique dans...</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($members as $member)
            <tr>
              <td>
                {{{ $member['member']->first_name }}} {{{ $member['member']->last_name }}}
              </td>
              <td>
                @if (array_key_exists('health_card', $member))
                  {{ Helper::dateToHuman($member['health_card']->signature_date) }}
                @else
                  -
                @endif
              </td>
              <td>
                @if (array_key_exists('health_card', $member))
                  @if ($member['health_card']->daysBeforeDeletion() > 100)
                    {{ $member['health_card']->daysBeforeDeletion() }} jours</span>
                  @elseif ($member['health_card']->daysBeforeDeletion() > 1)
                    <span class="danger">{{ $member['health_card']->daysBeforeDeletion() }} jours</span>
                  @else
                    <span class="danger">Quelques heures</span>
                  @endif
                @else
                  -
                @endif
              </td>
              <td>
                @if (array_key_exists('health_card', $member))
                  <a class="btn-sm btn-primary" href="{{ URL::route("health_card_download", $member['member']->id) }}">
                    Télécharger
                  </a>
                @else
                  <p class='label label-danger'>Fiche inexistante</p>
                @endif
              </td>
            @endforeach
            @if ($download_all)
              <tr>
                <td colspan="3"></td>
                <td>
                  <p>
                    <a class="btn-sm btn-primary" href="{{ URL::route("manage_health_cards_download_all", array('section_slug' => $user->currentSection->slug)) }}">
                      Télécharger tout
                    </a>
                  </p>
                  <p>
                    <a class="btn-sm btn-primary" href="{{ URL::route("manage_health_cards_download_summary", array('section_slug' => $user->currentSection->slug)) }}">
                      Télécharger le résumé
                    </a>
                  </p>
                </td>
              </tr>
            @endif
          </tobdy>
        </table>
      </div>
    </div>
  @else
    {{-- No members --}}
    <div class="row">
      <div class="col-md-12">
        <p>Il n'y a pas de membres dans cette section</p>
      </div>
    </div>
  @endif
  
  
@stop