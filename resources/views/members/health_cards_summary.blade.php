<?php
use App\Helpers\Helper;
?>

<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fiche santé</title>
  <style>
    body {
      font-family: "Source Sans Pro", "Arial", sans-serif;
      font-size: 80%;
      color: #333333;
    }

    h2 {
      font-size: 0.9rem;
      margin-bottom: 0.1rem;
      color: #1d5ea8;
    }

    h3 {
      font-size: 0.7rem;
      margin-bottom: 0.3rem;
      text-decoration: underline;
    }

    footer {
      margin-top: 4rem;
      font-size: 0.5rem;
    }

    section {
      page-break-inside: avoid;
    }

    .disclaimer {
      font-size: 0.6rem;
      font-style: italic;
      text-align: justify;
    }

    .card {
      width: 100%;
      border: 1px solid #222;
      padding: 0.5rem;
      padding-bottom: 0;
    }

    .row {
      width: 100%;
      margin-bottom: 0.5rem;
      page-break-inside: avoid;
    }

    label, label + span {
      display: inline-block;
      vertical-align: top;
    }

    label {
      text-align: justify;
    }

    span {
      color: #111111;
      font-weight: bold;
      min-height: 1rem;
    }

    .page-break-after {
      page-break-after: always;
    }

    @page {
      size: portrait A4;
    }
  </style>
</head>
<body>

<h1>Résumé des fiches santés {{ $section->de_la_section }}</h1>

@foreach ($healthCards as $healthCard)
  <?php $member = $healthCard->getMember(); ?>
  <h2>{{ $member->first_name }} {{ $member->last_name }}</h2>

  <div class="row">
    <label>Sait nager :</label>
    <span style="width: 20rem;">{{ $healthCard->can_swim ?? 'Inconnu' }}</span>
  </div>
  
  @if (!$healthCard->has_no_constrained_activities)
    <div class="row">
      <label>Ne peut pas participer à toutes les activités :</label>
      <span>{!! Helper::rawToHTML($healthCard->constrained_activities_details) !!}</span>
    </div>
  @endif

  @if ($healthCard->other_important_information)
  <div class="row">
    <label>Autres renseignements :</label>
    <span>{!! Helper::rawToHTML($healthCard->other_important_information) !!}</span>
  </div>
  @endif
  
  @if ($healthCard->medical_data)
    <div class="row">
      <label>Infos médicales :</label>
      <span>{!! Helper::rawToHTML($healthCard->medical_data) !!}</span>
    </div>
  @endif
  
  @if ($healthCard->allergy_details)
    <div class="row">
      <label>Allergies :</label>
      <span>{!! Helper::rawToHTML($healthCard->allergy_details) !!}</span>
    </div>

    @if ($healthCard->allergy_consequences)
      <div class="row">
        <label>Conséquences :</label>
        <span>{!! Helper::rawToHTML($healthCard->allergy_consequences) !!}</span>
      </div>
    @endif
  @endif

  @if ($healthCard->special_diet_details)
    <div class="row">
      <label>Régime alimentaire :</label>
      <span>{!! Helper::rawToHTML($healthCard->special_diet_details) !!}</span>
    </div>
  @endif

  @if ($healthCard->drugs_details)
    <div class="row">
      <label>Médicaments :</label>
      <span>{!! Helper::rawToHTML($healthCard->drugs_details) !!}</span>
      
      <br>
      @if ($healthCard->drugs_autonomy)
        <label>Autonome dans la prise de médicament</label>
      @else
        <label><b>N'EST PAS AUTONOME</b> dans la prise de médicaments</label>
      @endif
    </div>
  @endif

  @if (!$healthCard->has_tetanus_vaccine)
    <div class="row">
      <label><b>N'EST PAS VACCINÉ</b> contre le tétanos</label>
    </div>
  @endif

  @if ($healthCard->comments)
    <div class="row">
      <label>Commentaires :</label>
      <span>{!! Helper::rawToHTML($healthCard->comments) !!}</span>
    </div>
  @endif

  @if ($loop->remaining)
    <hr>
  @endif
  
@endforeach  

</body>
</html>
