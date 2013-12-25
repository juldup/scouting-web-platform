<ul class='outer'>
  @if (!$user->isConnected())
    {{-- The visitor is not connected --}}
    <script type='text/javascript'>
      // Make sure there is a login and password
      function checkLogin() {
        console.log("check login");
        if (document.login.login_username.value === '') {
          document.login.login_username.focus();
          document.login.login_username.select();
          return false;
        }
        if (document.login.login_password.value === '') {
          document.login.login_password.focus();
          document.login.login_password.select();
          return false;
        }
        return true;
      }
      // Connecte l'utilisateur s'il a entré un pseudo et un mot de passe
      function submitLogin() {
        console.log("submit login");
        if (checkLogin()) document.login.submit();
      }
      // Valide la connexion si 'enter' est pressé
      function checkEnter(e) {
        console.log("check enter");
        if (e.which === 13 || e.keyCode === 13) submitLogin();
      }
    </script>
    <li>
      <span class='menuTitle'><span>Connexion</span></span>
      {{-- Connection form --}}
      <form name="login" method="post" action="{{ URL::route('login') }}" onSubmit="return checkLogin()();">
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
      </form>
    </li>
      {{-- Link to create an account --}}
    <li>
      <span class='menuTitle'><span>Pas encore inscrit ?</span></span>
      <ul class="inner">
        <li>
          <a href="{{ URL::route('create_member') }}">S'inscrire</a>
        </li>
      </ul>
    </li>
  @else
    {{-- The visitor is connected --}}
    <li>
      <span class='menuTitle'><span class='pseudo'>{{ $user->name }}</span></span>
      <ul class='inner'>
        <li><a href="{{ URL::route('logout') }}">Déconnexion</a></li>
        <li><a href="{{ URL::route('edit_member') }}">Modifier</a></li>
        @if ($user->isAnimator())
          <li><a href="{{ URL::route('') }}">Coin animateurs</a></li>
        @endif
      </ul>
    </li>
  @endif
</ul>