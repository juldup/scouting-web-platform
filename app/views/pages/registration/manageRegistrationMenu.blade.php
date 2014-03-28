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
  </ul>
</div>
