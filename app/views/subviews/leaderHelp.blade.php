<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/
?>

@section('back_to_top')
  <a href="#" class="back-to-top"></a>
@stop

<div class="well help-content">

@if ($help == 'edit-calendar')
  <legend>Calendrier @yield('back_to_top')</legend>
  <p>
    Cet outil permet de remplir le calendrier de la section avec tous les événements de l'année, afin d'offrir aux parents et aux scouts une information facile d'accès.
  </p>
  <p>
    Il est très important de tenir les informations de ce calendrier à jour.
  </p>
@endif

@if ($help == 'edit-photos')
  <legend>Gérer les photos @yield('back_to_top')</legend>
  <p>
    Grâce à cet outil, tu peux partager les photos de tes réunions et autres activités avec les membres de l'unité.
  </p>
@endif

@if ($help == 'edit-documents')
  <legend>Gérer les documents à télécharger @yield('back_to_top')</legend>
  <p>
    Cette page te permet de partager des documents (convocation, carnet de camp, etc.) avec les parents et les scouts.
  </p>
  <p>
    Essaie de mettre ici une copie de tous les documents que tu partages avec les parents où les scouts. Ainsi, ils pourront toujours
    les retrouver facilement s'ils les égarent.
  </p>
@endif

@if ($help == 'edit-news')
  <legend>Gérer les nouvelles @yield('back_to_top') </legend>
  <p>
    Cette page te permet de publier toutes les nouvelles concernant ta section ou l'unité que tu souhaites partager.
    N'hésite pas faire vivre le site en postant régulièrement des nouvelles&nbsp;: une réunion qui s'est bien déroulée, une annonce d'un événement à venir, etc.
  </p>
@endif

@if ($help == 'email-section')
  <legend>Envoi d'un e-mail aux parents @yield('back_to_top')</legend>
  <p>
    Cet outil te permet d'envoyer facilement des e-mails à tous les parents de ta section.
    Il offre certains avantages par rapport à envoyer les e-mails depuis une autre boite e-mail :
    <ul>
      <li>Les adresses e-mail des destinaires ne sont jamais partagées, tu les gardes ainsi confidentiel et évites tout abu de la part des parents.</li>
      <li>Les e-mails envoyés depuis le site peuvent être consultés sur le site. Les parents peuvent donc facilement consulter les e-mails égarés.</li>
    </ul>
  </p>
  <p>
    L'outil te permet de sélectionner seulement destinaires parmi les parents, scouts et animateurs de la section.
  </p>
@endif

@if ($help == 'edit-health-cards')
  <legend>Fiches santé @yield('back_to_top')</legend>
  <p>
    Depuis cette page, tu peux télécharger les fiches santé de ta section et rapidement voir quelles sont les fiches manquantes.
  </p>
@endif

@if ($help == 'accounting')
  <legend>Trésorerie @yield('back_to_top')</legend>
  <p>
    Cet outil te permet de consulter et modifier les comptes financiers de ta section ou de l'unité.
  <p>
@endif

@if ($help == 'manage-registration')
  <legend>Gérer les inscriptions, désinscriptions et passages @yield('back_to_top')</legend>
  <p>
    Cette page permet&nbsp;:
    <ul>
      <li>d'inscrire des nouveaux scouts ou animateurs ayant rempli le formulaire d'inscription</li>
      <li>de gérer les réinscriptions</li>
      <li>de gérer l'année des scouts dans la section</li>
      <li>de gérer les passages d'une section à une autre</li>
  </p>
@endif

@if ($help == 'edit-listing')
  <legend>Modifier le listing @yield('back_to_top')</legend>
  <p>
    Le listing du site web est le listing officiel de l'unité.
    Il est disponible en ligne à tous les membres (sauf les données confidentielles).
    <strong>Il est donc primordial qu'il soit bien entretenu.</strong>
  </p>
  <p>
    Cette page permet de modifier le listing et de l'exporter.
  </p>
  <p>
    Il est également possible de sortir les adresses imprimables sur des enveloppes.
  </p>
@endif

@if ($help == 'edit-leaders')
  <legend>Les animateurs @yield('back_to_top')</legend>
  <p>
    Le listing des animateurs sert à deux choses&nbsp;:
    <ul>
      <li>Mettre des informations publiques sur la page dédiée aux animateurs.</li>
      <li>Il sert de listing officiel pour la fédé.</li>
    </ul>
    Il est donc important que les informations qui s'y trouvent soient correctes et complètes.
  </p>
  <p>
    Tu peux inscrire et modifier les animateurs via cet outil.
    Il est également possible d'attribuer à chaque animateurs des privilèges différents sur site, de manière
    à éviter les erreurs et/ou les abus, via la <a href="{{ URL::route('edit_privileges') }}">page de gestion des privilèges</a>.
  </p>
@endif

@if ($help == 'manage-sections')
  <legend>Modification des sections @yield('back_to_top')</legend>
  <p>
    Cette page permet de modifier les données des sections, ainsi que de créer de nouvelles sections ou d'en supprimer.
  </p>
@endif

@if ($help == 'edit-pages')
  <legend>Modifier les pages du site @yield('back_to_top')</legend>
  <p>
    Toute une série de pages du site peuvent être modifiées&nbsp;:
    <ul>
      <li>La page d'accueil du site</li>
      <li>La page de présentation de chaque section</li>
      <li>La page d'adresses utiles (adresse du local, des scouteries, etc.)</li>
      <li>La page spécifique de la fête d'unité, à mettre à jour chaque année</li>
      <li>La page d'inscription</li>
      <li>La charte d'unité</li>
      <li>La page de présentation de l'uniforme de chaque section</li>
      <li>La page d'aide</li>
    </ul>
  </p>
@endif

@if ($help == 'edit-links')
  <legend>Les liens @yield('back_to_top')</legend>
  <p>Cet outil permet de modifier la liste de liens vers d'autres sites de la page "Liens utiles".</p>
@endif


@if ($help == 'edit-parameters')
  <legend>Paramètres du site @yield('back_to_top')</legend>
  <p>Sur cette page, tu peux paramétriser le site&nbsp;:
    <ul>
      <li>Modifier le prix des cotisations</li>
      <li>Désactiver les inscriptions pour l'année suivante</li>
      <li>Rendre accessible ou non chaque page du site</li>
      <li>Déterminer les catégories de documents à télécharger</li>
      <li>Modifier les informations de l'unité et du site</li>
      <li>Modifier les paramètres pour l'envoi d'e-mails</li>
    </ul>
  </p>
@endif

@if ($help == 'recent-changes')
  <legend>Changements récents @yield('back_to_top')</legend>
  <p>
    Cette page t'offre un aperçu des changements récemment effectués sur le site.
  </p>
@endif

@if ($help == 'user-list')
  <legend>Liste des membres @yield('back_to_top')</legend>
  <p>
    Cette page affiche tous les membres inscrits sur le site, avec leur statut et leur dernière date de visite.
  </p>
  <p>
    Il est possible de supprimer un membre, mais pas d'en modifier les paramètres.
  </p>
@endif

@if ($help == 'suggestions')
  <legend>Suggestions @yield('back_to_top')</legend>
  <p>
    Les visiteurs du site peuvent laisser des suggestions, concernant le site tout comme les activités de l'unité.
  </p>
  <p>
    Cet outil permet de répondre aux suggestions ou de les supprimer.
  </p>
  <p>
    Toutes les suggestions et leurs réponses sont publiques.
  </p>
@endif

@if ($help == 'guest-book')
  <legend>Livre d'or @yield('back_to_top')</legend>
  <p>
    Les visiteurs du site peuvent laisser des messages publics dans le livre d'or.
  </p>
  <p>
    Cet outil permet de supprimer un message inadéquat.
  </p>
@endif

@if ($help == 'monitoring')
  <legend>Tâches cron @yield('back_to_top')</legend>
  <p>
    Cette page liste les tâches cron du site, avec leur statut.
  </p>
  <p>
    Si une tâche n'a pas été exécutée depuis trop longtemps, un message d'erreur est affiché.
  </p>
@endif

@if ($help == 'logs')
  <legend>Logs @yield('back_to_top')</legend>
  <p>
    Cette page montre toutes les opérations qui ont été effectuées sur le site.
  </p>
@endif

</div>