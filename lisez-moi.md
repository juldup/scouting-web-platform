# Une plateforme web pour votre unité scoute

Ce projet vous propose une plateforme web clé-sur-porte pour votre unité scoute. Elle permet non seulement
que votre unité scoute ait une présence sur le web, mais vous fournit en plus une série d'outils pour la gestion
de l'unité.

Liste des fonctionnalités présentes:

* Une structure de site contenant les pages publiques suivantes :
  * Page d'accueil
  * Page d'adresses, de contacts et de liens
  * Page charte d'unité
  * Page RGPD
  * Page d'inscription dans l'unité
  * Page d'aide
  * Page "boite à suggestions"
  * Livre d'or
  * Actualités du site
* Des pages spécifiques à chaque section :
  * Présentation de la section
  * Page présentant l'uniforme
  * Actualités de la section
  * Calendrier des activités de la section
  * Présentation des animateur de la section
  * Documents téléchargeables (publics/privés)
  * E-mails envoyés aux parents de la section (privé)
  * Photos des réunions et activités de la section (privé)
  * Listing des membres de la section (privé)
  * Liste des changements récents sur le site
* Possibilité pour les visiteurs de s'inscrire sur le site pour accéder aux données confidentielles s'ils sont membres de l'unité
* Des outils de gestion du site web pour les animateurs :
  * Système de privilèges attribués à chaque animateur pour limiter les erreurs sur le site et les abus
  * Gestion des éphémérides, des photos, des documents à télécharger
  * Modification du contenu des pages publiques
  * Paramétrage du site (pages visibles, nom de l'unité, etc.)
  * Connaitre la liste des utilisateurs du site et de leur dernière visite
  * Gérer les suggestions et le livre d'or
* Des outils de gestion de l'unité :
  * Envoyer des e-mails aux parents d'une section ou de toute l'unité
  * Fiches santé en ligne que les parents ne doivent remplir qu'une fois pour toutes
  * Gestion de la trésorerie par section
  * Gestion du listing et des inscriptions dans l'unité (formulaire d'inscription à remplir par les parents)
  * Gestion des réinscriptions, passages et vérification des paiement de cotisations en 2-3 clics
* Une mise en page compatible avec le web mobile

## Comment l'installer pour mon unité ?

### Démonstration

Jetez un œil à la [démonstration du site](http://scouts-demo.jdupuis.com) pour savoir si cette plateforme vous convient.

### Combien ça coute ?

L'utilisation de cette plateforme est entièrement **gratuite**.

Il vous faudra cependant payer un hébergement pour le site (un serveur mutualisé peut convenir).
Nous vous conseillons d'également souscrire à un système d'envoi d'e-mails professionnel (par exemple Amazon SES) pour
vous assurer de l'envoi correct des e-mails.

### Installation

Une fois que vous avez votre hébergement et votre système d'envoi d'e-mails, voici les instructions d'installation :

1. Copiez toute la hiérarchie du projet sur votre serveur
1. Installez composer à la racine du projet: `curl -sS https://getcomposer.org/installer | php`
1. Assurez-vous que mcrypt est activé: `sudo php5enmod mcrypt && sudo service apache2 restart`
1. Installez les librairies via composer: `php composer.phar install`
1. Faites pointer l'URL de base **/** vers le répertoire **public/**
1. Rendez les dossiers `app/storage` et `public/css` accessibles en écriture par l'utilisateur web, p.ex. avec les commandes suivantes :

        HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
        setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/storage
        setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/storage
        setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX public/css
        setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX public/css
1. Ouvrez l'URL de base dans un navigateur et suivez les étapes de configuration du site

### Besoin d'aide ?

Contactez Julien Dupuis, le créateur du site : <mailto:julien.dupuis+bswp@gmail.com>

