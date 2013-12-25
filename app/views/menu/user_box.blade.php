<ul class="nav navbar-nav navbar-right">
  @if (!$user->isConnected())
    {{-- The visitor is not connected --}}
    <li>
      <a href="{{ URL::route('login') }}">Me connecter</a>
    </li>
      <!--
      {{-- Connection form --}}
      <form name="login" method="post" action="{{ URL::route('login') }}" onSubmit="return checkLogin();">
        <ul class="inner">            
          <li class="data">
            Pseudo/e-mail : 
            <input name="login_username" type="text" id="login_username" size="6" onKeyPress="checkEnter(event);" />
          </li>
          <li class="data">
            Mdp : 
            <input name="login_password" type="password" id="login_password" size="6" onKeyPress="checkEnter(event);" />
          </li>
          <li>
            Mémoriser
            <input type="checkbox" name="login_remember" class="checkbox" onKeyPress="checkEnter(event);" />
          </li>
          <li>
            <a href="javascript:submitLogin()">Connexion</a>
          </li>
          <li>
            <a href="{{ URL::route('retrieve_password') }}">Oublié ?</a>
          </li>
        </ul>
      </form>--}}
    </li>
      {{-- Link to create an account --}}
    <li>
      <span class='menuTitle'><span>Pas encore inscrit ?</span></span>
      <ul class="inner">
        <li>
          <a href="{{ URL::route('create_user') }}">S'inscrire</a>
        </li>
      </ul>
    </li>-->
  @else
    {{-- The visitor is connected --}}
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $user->name }} <b class="caret"></b></a>
      <ul class='dropdown-menu'>
        <li><a href="{{ URL::route('logout') }}">Déconnexion</a></li>
        <li><a href="{{ URL::route('edit_user') }}">Modifier</a></li>
        @if ($user->isLeader())
          <li><a href="{{ URL::route('home') }}">Coin animateurs</a></li>
        @endif
      </ul>
    </li>
  @endif
</ul>