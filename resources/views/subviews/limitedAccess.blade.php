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

<div class="row">
  <div class="col-lg-12 alert alert-warning">
    <p><strong>Cette page est réservée aux membres de l'unité</strong></p>
    @if (!$user->isConnected()) {{-- User is not connected --}}
      <p>Pour pouvoir y accéder, vous devez <a href="{{ URL::route('login') }}">vous connecter</a>.</p>
    @elseif (!$user->verified) {{-- User account is not verified --}}
      <p>
        Votre compte d'utilisateur n'a pas été activé.
        Pour l'activer, cliquez sur le lien d'activation dans l'e-mail reçu
        lors de la création de votre compte d'utilisateur.
      </p>
      <p>
        Je voudrais <a href="{{ URL::route('user_resend_validation_link') }}"
           >recevoir à nouveau lien de validation par e-mail</a>
        ({{{ $user->email }}}).
      </p>
    @else {{-- User is not member --}}
      <p>Votre adresse e-mail ({{{ $user->email }}}) ne fait pas partie de notre listing.</p>
      <p>
        Si vous êtes membre de l'unité, pour accéder à cette page, vous pouvez :
        <ul>
          <li><a href="{{ URL::route('edit_user_email') }}">Changer l'adresse e-mail de votre compte d'utilisateur</a> et utiliser une adresse e-mail que nous connaissons.</li>
          <li><a href="{{ URL::route('login') . "#nouvel-utilisateur" }}">Créer un nouveau compte d'utilisateur</a> avec une adresse e-mail que nous connaissons.</li>
        </ul>
      </p>
    @endif
  </div>
</div>