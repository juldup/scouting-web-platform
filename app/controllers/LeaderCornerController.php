<?php

class LeaderCornerController extends BaseController {
  
  public function showPage() {
    
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    
    $operations = array(
        "Opérations courantes" => array(
            "Calendrier" => array(
                'url' => URL::route('manage_calendar'),
                'help' => 'calendrier',
                'condition' => Parameter::$SHOW_CALENDAR,
            ),
            "Photos" => array(
                'url' => URL::route('edit_photos'),
                'help' => 'photos',
                'condition' => Parameter::$SHOW_PHOTOS,
            ),
            "Documents à télécharger" => array(
                'url' => URL::route('manage_documents'),
                'help' => 'documents',
                'condition' => Parameter::$SHOW_DOCUMENTS,
            ),
            "Nouvelles" => array(
                'url' => URL::route('manage_news'),
                'help' => 'nouvelles',
                'condition' => Parameter::$SHOW_NEWS,
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
                'url' => URL::route('edit_section_page', array('section_slug' => $this->user->currentSection->slug)),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_SECTIONS,
            ),
            "Page d'adresses utiles" => array(
                'url' => URL::route('edit_address_page'),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_ADDRESSES,
            ),
            "Page de la fête d'unité" => array(
                'url' => URL::route('edit_annual_feast_page'),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_ANNUAL_FEAST,
            ),
            "Page d'inscription" => array(
                'url' => URL::route('edit_registration_page'),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_REGISTRATION,
            ),
            "Page de la charte d'unité" => array(
                'url' => URL::route('edit_unit_policy_page'),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_UNIT_POLICY,
            ),
            "Page d'uniforme" => array(
                'url' => URL::route('edit_uniform_page'),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_UNIFORMS,
            ),
            "Liens utiles" => array(
                'url' => URL::route('edit_links'),
                'help' => 'liens',
                'condition' => Parameter::$SHOW_LINKS,
            ),
            "Page d'aide" => array(
                'url' => URL::route('edit_help_page'),
                'help' => 'pages',
                'condition' => Parameter::$SHOW_HELP,
            ),
            "Paramètres du site" => array(
                'url' => URL::route('edit_parameters'),
                'help' => 'parametres',
            )
        ),
        "Supervision" => array(
            "Changements récents" => array(
                'url' => "",
                'help' => 'changements-recents',
            ),
            "Liste des membres" => array(
                'url' => URL::route('user_list'),
                'help' => 'liste-membres',
            )
        )
    );
    
    // Remove disabled operations
    foreach ($operations as $category=>$ops) {
      foreach ($ops as $operation=>$operationData) {
        if (array_key_exists('condition', $operationData) && !Parameter::get($operationData['condition'])) {
          unset($ops[$operation]);
          $operations[$category] = $ops;
        }
      }
    }
    
    // Create help section list
    $helpSections = array('general');
    foreach ($operations as $ops) {
      foreach ($ops as $operationData) {
        if (!in_array($operationData['help'], $helpSections)) {
          $helpSections[] = $operationData['help'];
        }
      }
    }
    
    return View::make('pages.leaderCorner.leaderCorner', array(
        'operations' => $operations,
        'help_sections' => $helpSections,
    ));
  }
  
}