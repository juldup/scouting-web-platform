<div class="registration-tabs">
  <ul class="nav nav-tabs">
    <li class="{{ $selected == 'registration' ? "active" : "" }}">
      <a href="{{ URL::route('manage_registration') }}">
        Nouvelles inscriptions
      </a>
    </li>
    <li class="{{ $selected == 'reregistration' ? "active" : "" }}">
      <a href="{{ URL::route('manage_reregistration') }}">
        Réinscriptions
      </a>
    </li>
    <li class="{{ $selected == 'change_year' ? "active" : "" }}">
      <a href="{{ URL::route('manage_year_in_section') }}">
        Changer l'année
      </a>
    </li>
    <li class="{{ $selected == 'change_section' ? "active" : "" }}">
      <a href="{{ URL::route('manage_member_section') }}">
        Changer de section
      </a>
    </li>
  </ul>
</div>
