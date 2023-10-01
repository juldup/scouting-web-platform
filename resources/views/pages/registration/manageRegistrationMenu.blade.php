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

<div class="registration-tabs">
  <ul class="nav nav-tabs">
    <li class="{{ $selected == 'registration' ? "active" : ($can_manage_registration ? "" : "disabled") }}">
      <a href="{{ $can_manage_registration ? URL::route('manage_registration') : "" }}">
        Nouvelles inscriptions
      </a>
    </li>
    <li class="{{ $selected == 'reregistration' ? "active" : ($can_manage_reregistration ? "" : "disabled") }}">
      <a href="{{ $can_manage_reregistration ? URL::route('manage_reregistration') : "" }}">
        Réinscriptions
      </a>
    </li>
    <li class="{{ $selected == 'change_year' ? "active" : ($can_manage_year_in_section ? "" : "disabled") }}">
      <a href="{{ $can_manage_year_in_section ? URL::route('manage_year_in_section') : "" }}">
        Changer l'année
      </a>
    </li>
    <li class="{{ $selected == 'change_section' ? "active" : ($can_manage_member_section ? "" : "disabled") }}">
      <a href="{{ $can_manage_member_section ? URL::route('manage_member_section') : "" }}">
        Changer de section
      </a>
    </li>
    <li class="{{ $selected == 'subscription_fee' ? "active" : ($can_manage_subscription_fee ? "" : "disabled") }}">
      <a href="{{ $can_manage_subscription_fee ? URL::route('manage_subscription_fee') : "" }}">
        Cotisations
      </a>
    </li>
  </ul>
</div>
