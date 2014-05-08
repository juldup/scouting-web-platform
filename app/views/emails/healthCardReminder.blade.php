(Cet e-mail a été envoyé automatiquement)


Bonjour,

Vous avez créé une fiche santé pour {{ $member->first_name }} {{ $member->last_name }} sur le site {{ $website_name }} il y a près d'un an.
Comme nous ne pouvons pas conserver ces données plus d'un an, cette fiche sera détruite automatiquement dans 7 jours, sauf si vous la signez à nouveau sur {{ URL::route('health_card_edit', array('member_id' => $member->id)) }}.
Si vous ne resignez pas la fiche à temps, vous devrez la remplir à nouveau.
      
Cordialement,
L'équipe d'animation
