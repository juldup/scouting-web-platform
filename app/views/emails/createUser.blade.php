Bonjour,

Vous venez de créer un compte d'utilisateur sur le site {{ $website_name }}.

Pour avoir accès aux données confidentielles du site, vous devez activer votre compte en cliquant sur ce lien :
{{ URL::route('verify_user', array("code" => $verification_code)) }}


Si vous n'avez pas créé vous-même ce compte d'utilisateur, veuillez ignorer ce message.

Si vous souhaitez ne plus jamais recevoir d'e-mails depuis le site {{ $website_name }}, veuillez cliquer sur ce lien :
{{ URL::route('ban_email', array("code" => $ban_email_code)) }}
