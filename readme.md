#### ***[Instructions en français en bas de ce document]***

&nbsp;

# Belgian Scouting Web Platform

## Copyright and license

The Belgian Scouting Web Platform is licensed under the [GNU General Public License](http://www.gnu.org/licenses/gpl.html).

## Installation

#### Instructions for installation:

1. Copy the whole project on your server
1. Install composer at the root of the file hierarchy: `curl -sS https://getcomposer.org/installer | php`
1. Run composer to load libraries: `curl -sS https://getcomposer.org/installer | php`
1. Make sure the **app/storage/** folder has read/write access for the www user
1. Create a database and configure **/app/config/database.php** (lines 55-64) to access your database
1. Run the command `./artisan migrate:install` to generate the database
1. Make the base url **/** point to the **public/** folder
1. Create the two following cron jobs:
  * A job every minute requesting : `http://yourdomain.com/cron/envoi-automatique-emails`
  * A job every day or twice a day requesting : `http://yourdomain.com/cron/suppression-auto-fiches-sante`
1. In a web browser, load the `http://yourdomain.com/login` page and create a new webmaster user account
1. In the database, table users, find this webmaster user and set the is_webmaster field to 1
1. Create the unit's sections at `http://yourdomain.com/gestion/donnees-section`
1. Configure the website at `http://yourdomain.com/gestion/parametres` (in particular, make sure to configure the SMTP parameters)

## Credits

#### Calendar tent icon

Modified from : http://www.iconarchive.com/show/summer-blue-icons-by-dapino/Tent-icon.html

Licence : [CC Attribution-Noncommercial 3.0](http://creativecommons.org/licenses/by-nc/3.0/)

#### Calendar special icon

Modified from : http://www.iconarchive.com/show/halloween-icons-by-gcds/jack-o-lantern-icon.html

Licence : Freeware

#### Calendar break icon

Modified from : http://www.iconarchive.com/show/oxygen-icons-by-oxygen-icons.org/Actions-process-stop-icon.html

Licence : [GNU Lesser General Public License](http://en.wikipedia.org/wiki/GNU_Lesser_General_Public_License)

#### Calendar leaders icon

Modified from : http://www.iconarchive.com/show/oxygen-icons-by-oxygen-icons.org/Actions-document-encrypt-icon.html

Licence : [GNU Lesser General Public License](http://en.wikipedia.org/wiki/GNU_Lesser_General_Public_License)

#### Calendar weekend icon

Modified from : http://www.iconarchive.com/show/brand-camp-icons-by-thegirltyler/Camp-Fire-Stories-icon.html

Licence : Free for non-commercial use

#### Calendar bar icon

Modified from : http://www.iconarchive.com/show/breakfast-icons-by-snaky-reports/Orange-Juice-icon.html

Licence : Free for non-commercial use

#### Calendar cleaning icon

Modified from : http://www.iconarchive.com/show/witchery-icons-by-cavemanmac/Broom-icon.html + leaders icon

Licence : Free for non-commercial use

#### Calendar normal icon

Modified from : http://www.iconarchive.com/show/farm-fresh-icons-by-fatcow/whistle-icon.html

Licence : [CC Attribution 3.0](http://creativecommons.org/licenses/by/3.0/)

#### Photo rotate icons

Sources:

- http://www.iconarchive.com/show/farm-fresh-icons-by-fatcow/arrow-rotate-anticlockwise-icon.html
- http://www.iconarchive.com/show/farm-fresh-icons-by-fatcow/arrow-rotate-clockwise-icon.html

Licence : [CC Attribution 3.0](http://creativecommons.org/licenses/by/3.0/)

&nbsp;

&nbsp;


# Français : une plateforme web pour votre unité scoute

Ce projet vous propose une plateforme web clé-sur-porte pour votre unité scoute. Elle permet non seulement
que votre unité scoute ait une présence sur le web, mais vous fournit en plus une série d'outil pour la gestion
de l'unité.

Liste des fonctionnalités présentes:

* Une structure de site contenant les pages publiques suivantes :
  * Page d'accueil
  * Page d'adresses
  * Page de contacts
  * Page "fête d'unité"
  * Page charte d'unité
  * Page d'inscription dans l'unité
  * Page de liens utiles
  * Page d'aide
  * Page "boite à suggestions"
  * Livre d'or
* Des pages spécifiques à chaque section :
  * Présentation de la section
  * Page présentant l'uniforme
  * Nouvelles de la section
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

Jetez un œil à la [démonstration du site](http://demo.sv20.be) pour savoir si cette plateforme vous convient.

### Combien ça coute ?

L'utilisation de cette plateforme est entièrement **gratuite**.

Il vous faudra cependant payer un hébergement pour le site (un serveur mutualisé peut convenir).
Nous vous conseillons d'également souscrire à un système d'envoi d'e-mails professionnel (par exemple Amazon SES) pour
vous assurer de l'envoi correct des e-mails.

### Installation

Une fois que vous avez votre hébergement et votre système d'envoi d'e-mails, voici les instructions d'installation :

1. Copiez toute la hiérarchie du projet sur votre serveur
1. Arrangez-vous pour que le répertoire **app/storage/** ait les droit d'accès en lecture et écriture pour l'utilisateur www (`chmod 777 -R app/storage` fera l'affaire)
1. Installez composer à la racine du projet: `curl -sS https://getcomposer.org/installer | php`
1. Installez les librairies via composer: `curl -sS https://getcomposer.org/installer | php`
1. Créez une base de données et configurez **/app/config/database.php** (lignes 55-64) pour accéder à votre base de données
1. Exécutez la commande `./artisan migrate:install` à la racine du projet pour générer la base de données
1. Faites pointer l'URL de base **/** vers le répertoire **public/**
1. Créer les deux tâches cron suivantes :
  * Une tâche toutes les minutes chargeant : `http://yourdomain.com/cron/envoi-automatique-emails`
  * Une tâche tous les jours ou deux fois par jour changeant : `http://yourdomain.com/cron/suppression-auto-fiches-sante`
1. Dans un navigateur web, chargez la page `http://yourdomain.com/login` et créer un compte d'utilisateur pour le webmaster
1. Dans la table users de la base de données, trouvez cet utilisateur webmaster et mettez la valeur du champ is_webmaster à 1 (pour donner tous les droits au webmaster)
1. Créez les sections de l'unité sur la page `http://yourdomain.com/gestion/donnees-section`
1. Configurez le site sur `http://yourdomain.com/gestion/parametres` (en particulier, veillez à configurer correctement les paramètres SMTP pour l'envoi des e-mails)
1. Invitez enfin les animateurs et les parents à s'inscrire

### Besoin d'aide ?

Contacter Julien Dupuis, le créateur du site : <mailto:julien.dupuis+bswp@gmail.com>


