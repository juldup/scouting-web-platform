<ul class="nav navbar-nav navbar-right">
  @if (!$user->isConnected())
    {{-- The visitor is not connected --}}
    <li>
      <a href="{{ URL::route('login') }}">Me connecter</a>
    </li>
  @else
    {{-- The visitor is connected --}}
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{{ $user->username }}} <b class="caret"></b></a>
      <ul class='dropdown-menu'>
        <li><a href="{{ URL::route('logout') }}">Déconnexion</a></li>
        <li><a href="{{ URL::route('edit_user') }}">Modifier</a></li>
      </ul>
    </li>
  @endif
</ul>