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
                'help-anchor' => 'calendrier',
                'help' => 'edit-calendar',
                'condition' => Parameter::$SHOW_CALENDAR,
            ),
            "Photos" => array(
                'url' => URL::route('edit_photos'),
                'help-anchor' => 'photos',
                'help' => 'edit-photos',
                'condition' => Parameter::$SHOW_PHOTOS,
            ),
            "Documents à télécharger" => array(
                'url' => URL::route('manage_documents'),
                'help-anchor' => 'documents',
                'help' => 'edit-documents',
                'condition' => Parameter::$SHOW_DOCUMENTS,
            ),
            "Nouvelles" => array(
                'url' => URL::route('manage_news'),
                'help-anchor' => 'nouvelles',
                'help' => 'edit-news',
                'condition' => Parameter::$SHOW_NEWS,
            ),
            "E-mail aux parents" => array(
                'url' => URL::route('send_section_email'),
                'help-anchor' => 'emails',
                'help' => 'email-section',
            ),
            "Fiches santé" => array(
                'url' => URL::route('manage_health_cards'),
                'help-anchor' => 'fiches-sante',
                'help' => 'edit-health-cards',
            ),
            "Trésorerie" => array(
                'url' => URL::route('accounting'),
                'help-anchor' => 'tresorerie',
                'help' => 'accounting',
            )
        ),
        "Opérations annuelles" => array(
            "Inscriptions" => array(
                'url' => URL::route('manage_registration'),
                'help-anchor' => 'inscriptions',
                'help' => 'manage-registration',
            ),
            "Listing" => array(
                'url' => URL::route('manage_listing'),
                'help-anchor' => 'listing',
                'help' => 'edit-listing',
            ),
            "Les animateurs" => array(
                'url' => URL::route('edit_leaders'),
                'help-anchor' => 'animateurs',
                'help' => 'edit-leaders',
            ),
            "Gérer les sections" => array(
                'url' => URL::route('section_data'),
                'help-anchor' => 'sections',
                'help' => 'manage-sections',
            )
        ),
        "Contenu du site" => array(
            "Page d'accueil" => array(
                'url' => URL::route('edit_home_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
            ),
            "Page d'accueil de la section" => array(
                'url' => URL::route('edit_section_page', array('section_slug' => $this->user->currentSection->slug)),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_SECTIONS,
            ),
            "Page d'adresses utiles" => array(
                'url' => URL::route('edit_address_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_ADDRESSES,
            ),
            "Page de la fête d'unité" => array(
                'url' => URL::route('edit_annual_feast_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_ANNUAL_FEAST,
            ),
            "Page d'inscription" => array(
                'url' => URL::route('edit_registration_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_REGISTRATION,
            ),
            "Page de la charte d'unité" => array(
                'url' => URL::route('edit_unit_policy_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_UNIT_POLICY,
            ),
            "Page d'uniforme" => array(
                'url' => URL::route('edit_uniform_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_UNIFORMS,
            ),
            "Page d'aide" => array(
                'url' => URL::route('edit_help_page'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_HELP,
            ),
            "Liens utiles" => array(
                'url' => URL::route('edit_links'),
                'help-anchor' => 'liens',
                'help' => 'edit-links',
                'condition' => Parameter::$SHOW_LINKS,
            ),
            "Paramètres du site" => array(
                'url' => URL::route('edit_parameters'),
                'help-anchor' => 'parametres',
                'help' => 'edit-parameters',
            )
        ),
        "Supervision" => array(
//            "Changements récents" => array(
//                'url' => URL::route('view_private_recent_changes'),
//                'help-anchor' => 'changements-recents',
//                'help' => 'recent-changes',
//            ),
            "Liste des utilisateurs" => array(
                'url' => URL::route('user_list'),
                'help-anchor' => 'liste-membres',
                'help' => 'user-list',
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
          $helpSections[$operationData['help-anchor']] = $operationData['help'];
        }
      }
    }
    
    return View::make('pages.leaderCorner.leaderCorner', array(
        'operations' => $operations,
        'help_sections' => $helpSections,
    ));
  }
  
}