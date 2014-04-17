Bonjour,

Vous avez redemandé le lien d'activation de votre compte d'utilisateur sur le site {{ $website_name }}. Le voici :
{{ URL::route('verify_user', array("code" => $verification_code)) }}


Si vous souhaitez ne plus jamais recevoir d'e-mails depuis le site {{ $website_name }}, veuillez cliquer sur ce lien :
{{ URL::route('ban_email', array("code" => $ban_email_code)) }}
