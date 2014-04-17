Bonjour,

Vous avez changé l'adresse e-mail associée à votre compte d'utilisateur sur le site {{ $website_name }}.

Vous devez réactiver votre compte en cliquant sur le lien :
{{ URL::route('verify_user', array("code" => $verification_code)) }}


Si vous souhaitez ne plus jamais recevoir d'e-mails depuis le site {{ $website_name }}, veuillez cliquer sur ce lien :
{{ URL::route('ban_email', array("code" => $ban_email_code)) }}
