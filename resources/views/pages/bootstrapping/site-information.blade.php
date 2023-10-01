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
<h2>Informations sur la gestion du site</h2>
<h3>Rédiger les pages</h3>
<p>Le site est composé de pages remplies automatiquement (listing, e-mails, contacts, fiches santé, etc.) et de pages à contenu libre.</p>
<p>
  Voici la liste des pages à contenu libre qui nécessitent une rédaction&nbsp;:
</p>
<ul>
  <li><a target="_blank" href="{{ URL::route('home') }}">La page d'accueil du site</a></li>
  <li><a target="_blank" href="{{ URL::route('contacts') }}">La page d'adresses utiles</a></li>
  <li><a target="_blank" href="{{ URL::route('annual_feast') }}">La page de la fête d'unité</a></li>
  <li><a target="_blank" href="{{ URL::route('registration') }}">La page d'inscription</a> (déjà préremplie)</li>
  <li><a target="_blank" href="{{ URL::route('unit_policy') }}">La charte d'unité</a></li>
  <li><a target="_blank" href="{{ URL::route('leader_policy') }}">La charte des animateurs</a></li>
  <li><a target="_blank" href="{{ URL::route('gdpr') }}">Le RGPD</a></li>
  <li><a target="_blank" href="{{ URL::route('help') }}">La page d'aide</a> (déjà préremplie)</li>
  <li>
    Pour chaque section&nbsp;:
    <ul>
      <li><a target="_blank" href="{{ URL::route('section') }}">La page d'accueil de la section</a></li>
      <li><a target="_blank" href="{{ URL::route('uniform') }}">La page de l'uniforme de la section</a></li>
    </ul>
  </li>
</ul>
<p>
  Quand le webmaster ou un animateur pouvant modifier ces pages est connecté au site, il
  verra un bouton "Modifier cette page" lui permettant de passer en mode rédaction de la page
</p>
<h3>Navigation entre les sections</h3>
<p>
  Certaines pages du site changent en fonction de la section qui est active. Elles sont reconnaissables avec la ligne
  de couleur visible sous les menus.
  Pour changer la section active, utilisez le menu de droite.
</p>
<h3>Inscrire les membres</h3>
<p>
  Tout a été conçu pour que les membres puissent s'inscrire eux-mêmes sur le site
  (ou se faire inscrire par leurs parents). Ceci permet d'assurer une exactitude des
  données personnelles.
</p>
<p>
  L'inscription d'un membre se fait de la manière suivante&nbsp;:
</p>
<ol>
  <li>
    Le membre ou son parent remplit le <a target="_blank" href="{{ URL::route('registration_form') }}">formulaire d'inscription</a>
    accessible depuis la <a target='_blank' href='{{ URL::route('registration') }}'>page d'inscription</a>.
  </li>
  <li>
    L'animateur d'unité (ou un de ses équipier à qui il a donné ce droit) doit ensuite valider l'inscription via
    l'<a target="_blank" href="{{ URL::route('manage_registration') }}">outil de gestion des inscriptions</a>.
  </li>
</ol>
<h3>Lien entre compte d'utilisateur et membre</h3>
<p>
  Les comptes d'utilisateur sur le site et les membres de l'unité sont deux choses distinctes, mais qui ont un lien.
</p>
<p>
  Les membres ont accès à certaines informations que les visiteurs quelconques ne peuvent pas voir, comme le listing,
  les photos ou encore l'accès à la page des fiches santé.
</p>
<p>
  Pour avoir accès à ces informations, le membre doit&nbsp;:
</p>
<ol>
  <li>Créer un compte d'utilisateur dont l'adresse e-mail correspond à une de celles renseignées lors de l'inscription</li>
  <li>Valider son compte en cliquant sur le lien dans un e-mail qu'il recevra alors</li>
  <li>Se connecter sur le site avec ce compte d'utilisateur</li>
</ol>
<p>En particulier, les animateurs pourront ainsi avoir accès à la gestion des informations contenue sur le site.</p>
<h3>Donner les droits d'accès aux animateurs</h3>
<p>
  En plus de pouvoir accéder aux données privées du site, les animateurs peuvent faire un nombre d'opérations sur le site.
  Il est possible d'attribuer à chaque animateur individuellement des privilèges lui permettant d'accomplir certaines opérations.
  Voici la marche à suivre&nbsp;:
</p>
<ol>
  <li>L'animateur doit s'inscrire en tant que membre comme un membre normal et l'animateur d'unité doit valider son inscription.</li>
  <li>
    L'animateur d'unité (ou tout animateur ayant ces privilèges) peut visiter la
    <a target="_blank" href="{{ URL::route('edit_privileges') }}">page des privilèges</a> pour attribuer des privilèges à cet animateur.
  </li>
</ol>
<p>
  En particulier, avant toute chose, il est nécessaire d'inscrire l'animateur d'unité ou un autre responsable et de lui conférer tous les droits.
  Ceci doit être fait par le webmaster, puisqu'il est au départ le seul ayant des droit d'administration sur le site.
</p>
