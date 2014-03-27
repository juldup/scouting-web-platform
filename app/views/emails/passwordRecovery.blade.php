Bonjour,

Vous avez demandé à récupérer votre mot de passe sur le site {{ $website_name }}.

@if (count($recoveries) == 1)
Cliquez sur le lien suivant pour choisir un nouveau mot de passe : {{ URL::route('change_password', array("code" => reset($recoveries)->code)) }}
@else
Vous possédez plusieurs comptes d'utilisateur. Voici les liens pour changer leurs mots de passe :

@foreach ($recoveries as $username=>$recovery)
{{ $username }} : {{ URL::route('change_password', array("code" => $recovery->code)) }} 
@endforeach
@endif
