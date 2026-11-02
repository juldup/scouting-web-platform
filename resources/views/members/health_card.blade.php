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

@foreach ($healthCards as $healthCard)
  <?php $member = $healthCard->getMember(); ?>

<h1>Fiche santé individuelle</h1>
<p>
  Cette fiche a été complétée en ligne sur {{ URL::route('health_card') }} et doit être mise à jour avec précision au début de chaque année scoute, et avant chaque
  camp, par les parents ou par un médecin.
</p>  

<h2>Identité du participant</h2>
<section class="card">
  <div class="row">
    <label>N° registre national :</label>
    <span style="width: 12rem;">{{ $healthCard->national_id }}</span>
  </div>
  
  <div class="row">
    <label>Nom et prénom :</label>
    <span style="width: 25rem;">{{ $member->last_name }} {{ $member->first_name }}</span>

    <label>Né-e le :</label>
    <span style="width: 6rem;">{{ $member->getHumanBirthDate() }}</span>
  </div>

  <div class="row">
    <label>Adresse :</label>
    <span style="width: 39rem;">{{ $member->address }} ; {{ $member->postcode }} {{ $member->city }}</span>
  </div>

  <div class="row">
    <label>Tél. / GSM :</label>
    <span style="width: 10rem;">{{ $member->getPersonalPhone() }}</span>

    <label>Email :</label>
    <span style="width: 24rem;">{{ $member->email_member }}</span>
  </div>
</section>


<h2>Personnes à contacter en cas d'urgence</h2>
<section class="card">
  <div style="float: left; max-width: 49%;">
    <div class="row">
      <label style="width: 8rem;">Nom et prénom :</label>
      <span style="width: 13rem;">{{ $healthCard->contact1_name }}</span>
    </div>
    
    <div class="row">
      <label style="width: 8rem;">Lien de parenté :</label>
      <span style="width: 13rem;">{{ $healthCard->contact1_relationship }}</span>
    </div>
    
    <div class="row">
      <label style="width: 8rem;">Tél. / GSM :</label>
      <span style="width: 13rem;">{{ $healthCard->contact1_phone }}</span>
    </div>
    
    <div class="row">
      <label style="width: 8rem;">Email :</label>
      <span style="width: 13rem;">{{ $healthCard->contact1_email }}</span>
    </div>
    
    <div class="row">
      <label style="width: 8rem;">Remarque(s) :</label>
      <span style="width: 13rem;">{!! Helper::rawToHTML($healthCard->contact1_comment) !!}</span>
    </div>
  </div>

  <div style="float: right; max-width: 49%;">
    <div class="row">
      <label style="width: 8rem;">Nom et prénom :</label>
      <span style="width: 13rem;">{{ $healthCard->contact2_name }}</span>
    </div>

    <div class="row">
      <label style="width: 8rem;">Lien de parenté :</label>
      <span style="width: 13rem;">{{ $healthCard->contact2_relationship }}</span>
    </div>

    <div class="row">
      <label style="width: 8rem;">Tél. / GSM :</label>
      <span style="width: 13rem;">{{ $healthCard->contact2_phone }}</span>
    </div>

    <div class="row">
      <label style="width: 8rem;">Email :</label>
      <span style="width: 13rem;">{{ $healthCard->contact2_email }}</span>
    </div>

    <div class="row">
      <label style="width: 8rem;">Remarque(s) :</label>
      <span style="width: 13rem;">{!! Helper::rawToHTML($healthCard->contact2_comment) !!}</span>
    </div>
  </div>
  
  <div style="clear: both;"></div>
</section>


<h2>Coordonnées du médecin traitant</h2>
<section class="card">
  <div class="row">
    <label>Nom et prénom :</label>
    <span style="width: 24rem;">{{ $healthCard->doctor_name }}</span>

    <label>Tél. / GSM :</label>
    <span style="width: 9rem;">{{ $healthCard->doctor_phone }}</span>
  </div>
  
  <div class="row">
    <label>Adresse :</label>
    <span style="width: 38rem;">{!! Helper::rawToHTML($healthCard->doctor_address) !!}</span>
  </div>
</section>


<h2>Informations confidentielles concernant la santé du participant</h2>
<div style="margin-top: 0.5rem;">
  <div class="row">
    <label>Taille :</label>
    <span style="width: 5rem;">{{ $healthCard->height }}</span>

    <label>Poids :</label>
    <span style="width: 5rem;">{{ $healthCard->weight }}</span>

    <label>Sait-il nager ?</label>
    <span style="width: 20rem;">{{ $healthCard->can_swim }}</span>
  </div>
  
  <section>
    <div class="row">
      <label>Peut-il prendre part aux activités proposées (sport, excursions, jeux, baignade...) ?</label>
      <span>{{ $healthCard->has_no_constrained_activities ? 'Oui' : 'Non' }}</span>
    </div>
  
    @if (!empty($healthCard->constrained_activities_details))
    <div class="row">
      <label>Raisons :</label>
      <span>{!! Helper::rawToHTML($healthCard->constrained_activities_details) !!}</span>
    </div>
    @endif
  </section>
  
  <div class="row">
    <label>
      Souffre-t-il de manière permanente ou régulière de :
      diabète, asthme, épilepsie, mal des transports, rhumatisme,
      énurésie nocturne, affection cardiaque, affection cutanée, somnambulisme, handicap mental, handicap moteur,
      maux de tête / migraines, etc ?
      Indiquez la fréquence, la gravité et les actions à mettre en oeuvre pour les éviter et/ou y réagir.
    </label>
    <br/><span>{!! Helper::rawToHTML($healthCard->medical_data ?? '(Néant)') !!}</span>
  </div>
  
  <div class="row">
    <label>Quelles sont les autres maladies importantes ou les interventions médicales qu'il a dû subir ?</label>
    <br/><span>{!! Helper::rawToHTML($healthCard->medical_history ?? '(Néant)') !!}</span>
  </div>
  
  <div class="row">
    <label>Autres renseignements que vous jugez importants pour le bon déroulement des activités / du camp :</label>
    <br/><span>{!! Helper::rawToHTML($healthCard->other_important_information ?? '(Néant)') !!}</span>
  </div>
  
  <section>
    <h3>Vaccination contre le tétanos</h3>
    <div class="row">
      <label>Est-il en ordre de vaccination contre le tétanos ?</label>
      <span>{{ $healthCard->has_tetanus_vaccine ? 'Oui' : 'Non' }}</span>
      
      <label style="margin-left: 2rem;">Date du dernier rappel :</label>
      <span>{!! Helper::rawToHTML($healthCard->tetanus_vaccine_details) !!}</span>
    </div>
  </section>

  <section>
    <h3>Allergies</h3>
    <div class="row">
      <label>Est-il allergique à certaines substances, aliments ou médicaments ?</label>
      <span>{{ $healthCard->has_allergy ? 'Oui' : 'Non' }}</span>
    </div>
  
    @if (!empty($healthCard->allergy_details))
    <div class="row">
      <label>Lesquels :</label>
      <span>{!! Helper::rawToHTML($healthCard->allergy_details) !!}</span>
    </div>
    @endif
  
    @if (!empty($healthCard->allergy_consequences))
    <div class="row">
      <label>Conséquences :</label>
      <span>{!! Helper::rawToHTML($healthCard->allergy_consequences) !!}</span>
    </div>
    @endif
  </section>

  <section>
    <h3>Régime alimentaire</h3>
    <div class="row">
      <label>A-t-il un régime alimentaire particulier ?</label>
      <span>{{ $healthCard->has_special_diet ? 'Oui' : 'Non' }}</span>
    </div>
  
    @if (!empty($healthCard->special_diet_details))
    <div class="row">
      <label>Lequel :</label>
      <span>{!! Helper::rawToHTML($healthCard->special_diet_details) !!}</span>
    </div>
    @endif
  </section>

  <section>
    <h3>Médicaments</h3>
    <div class="row">
      <label>Doit-il prendre des médicaments quotidiennement ?</label>
      <span>{{ $healthCard->has_drugs ? 'Oui' : 'Non' }}</span>
    </div>
  
    @if (!empty($healthCard->drugs_details))
    <div class="row">
      <label>Lesquels, quand et en quelle quantité :</label>
      <span>{!! Helper::rawToHTML($healthCard->drugs_details) !!}</span>
    </div>
    @endif
  
    <div class="row">
      <label>Est-il autonome dans la prise de ces médicaments ?</label>
      <span>{{ $healthCard->drugs_autonomy ? 'Oui' : 'Non' }}</span>
      
      <div class="disclaimer">
        Notes :
        <ul>
          <li>
            Les médicaments ne peuvent pas être partagés entre les participants.
          </li>
  
          <li>
            <b>Les médicaments fournis par les parents doivent impérativement être accompagnés d’une prescription
              médicale.</b> Elle doit toujours être exigée.
          </li>
  
          <li>
            Les animateurs disposent d’une boite de premiers soins.
            Dans le cas de situations ponctuelles ou dans l’attente de l’arrivée du médecin, ils peuvent administrer les médicaments suivants et ce à bon escient :
            paracétamol, antiseptique, pommade apaisante (sur avis médical ou du pharmacien).
          </li>
        </ul>
      </div>
    </div>
  </section>

  @if (!empty($healthCard->comments))
  <section>
    <h3>Autre détails</h3>
    <div class="row">
      <span>{!! Helper::rawToHTML($healthCard->comments) !!}</span>
    </div>
  </section>
  @endif
</div>

<section>
  <h2></h2>
  <div class="">
    <p style="font-style: italic">
      &laquo;&nbsp;
      Je marque mon accord pour que la prise en charge ou les traitements estimés nécessaires soient entrepris durant le séjour de mon enfant par le responsable de
      centre de vacances ou par le service médical qui y est associé. J'autorise le médecin local à prendre les décisions qu'il juge urgentes et indispensables pour
      assurer l'état de santé de l'enfant, même s'il s'agit d'une intervention chirurgicale à défaut de pouvoir être contacté personnellement.
    <br><br>
      À cette fin, j'autorise les animateurs à prendre la carte d'identité et/ou la carte ISI+ de mon enfant afin de
      permettre son identification en cas de nécessités médicales, telle qu'une consultation chez un médecin ou une
      prescription à la pharmacie.
      &nbsp;&raquo;
    </p>
  </div>
  
  <div class="row" style="margin-top: 2rem;">
    <div style="float: left; width: 15rem;">
      <label>Date :</label>
      <span>{{ Helper::dateToHuman($healthCard->signature_date) }}</span>
    </div>
  
    <div style="float: left; max-width: 49%;">
      <label>Signature électronique :</label>
      <span>{{ $healthCard->signatory_email }}</span>
    </div>
  </div>
</section>
  
<footer>
  <div class="disclaimer">
    <p>
      Les données médicales sont traitées dans le cadre de nos activités légitimes, conformément à l’article 9 §2 du Règlement général sur la protection des données (RGPD) et avec les garanties appropriées.
    </p>

    <p>
      Les informations contenues dans cette fiche sont confidentielles.
      Elles sont accessibles uniquement aux animateurs et animatrices concerné·es et aux professionnel·les de santé consulté·es.
      Les animateurs et animatrices sont tenu·es de respecter la législation relative à la protection de la vie privée ainsi que leur devoir de discrétion, 
      conformément à la loi du 3 juillet 2005 relative aux droits des volontaires.
    </p>
    
    <p>
      Certaines données relatives à une situation de handicap peuvent être transmises à l’ONE en vue de l’octroi de subsides, conformément aux dispositions légales.
      Elles ne sont pas utilisées à d’autres fins.
    </p>

    <p>
      Vous pouvez consulter, corriger ou demander la suppression de ces données à tout moment. Ces données seront
      détruites le {{ $healthCard->getHumanExpirationDate() }} si la fiche n'est pas resignée entretemps et au plus tard un an après le séjour si aucun dossier n'est ouvert.
    </p>

    <p>
      Les informations relatives à vos droits sont disponibles
      dans la charte de protection des données personnelles disponible sur lesscouts.be/vieprivee.
    </p>
  </div>
</footer>
  
@if ($loop->remaining)
<div class="page-break-after">&nbsp;</div>
@endif

@endforeach  

</body>
</html>
