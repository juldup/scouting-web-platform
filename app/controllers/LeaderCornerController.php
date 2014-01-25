<?php

class LeaderCornerController extends BaseController {
  
  public function showPage() {
    
    $operations = array(
        "Opérations courantes" => array(
            "Calendrier" => array(
                'url' => URL::route('manage_calendar'),
                'help' => 'calendrier',
            ),
            "Photos" => array(
                'url' => URL::route('edit_photos'),
                'help' => 'photos',
            ),
            "Documents à télécharger" => array(
                'url' => URL::route('manage_documents'),
                'help' => 'documents',
            ),
            "Nouvelles" => array(
                'url' => URL::route('manage_news'),
                'help' => 'nouvelles',
            ),
            "E-mail aux parents" => array(
                'url' => URL::route('send_section_email'),
                'help' => 'emails',
            ),
            "Trésorerie" => array(
                'url' => URL::route('accounts'),
                'help' => 'tresorerie',
            )
        ),
        "Opérations annuelles" => array(
            "Inscriptions" => array(
                'url' => URL::route('manage_registration'),
                'help' => 'inscriptions',
            ),
            "Listing" => array(
                'url' => URL::route('manage_listing'),
                'help' => 'listing',
            ),
            "Les animateurs" => array(
                'url' => URL::route('edit_leaders'),
                'help' => 'animateurs',
            ),
            "Gérer les sections" => array(
                'url' => URL::route('section_data'),
                'help' => 'sections',
            )
        ),
        "Contenu du site" => array(
            "Page d'accueil" => array(
                'url' => URL::route('edit_home_page'),
                'help' => 'pages',
            ),
            "Page d'accueil de la section" => array(
                'url' => URL::route('edit_section_page'),
                'help' => 'pages',
            ),
            "Page d'adresses utiles" => array(
                'url' => URL::route('edit_address_page'),
                'help' => 'pages',
            ),
            "Page d'inscription" => array(
                'url' => URL::route('edit_registration_page'),
                'help' => 'pages',
            ),
            "Page de la charte d'unité" => array(
                'url' => URL::route('edit_unit_policy_page'),
                'help' => 'pages',
            ),
            "Page d'uniforme" => array(
                'url' => URL::route('edit_uniform_page'),
                'help' => 'pages',
            ),
            "Liens utiles" => array(
                'url' => URL::route('edit_links'),
                'help' => 'liens',
            ),
            "Paramètres du site" => array(
                'url' => URL::route('edit_parameters'),
                'help' => 'parametres',
            ),
            "Liste des membres" => array(
                'url' => URL::route('user_list'),
                'help' => 'liste-membres',
            )
        ),
    );
    
    return View::make('pages.leaderCorner.leaderCorner', array(
        'operations' => $operations,
    ));
  }
  
}