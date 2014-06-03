<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * The leader corner provides a list of all the possible management actions
 * for the leaders with a short description.
 */
class LeaderCornerController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Displays the leader corner page
   */
  public function showPage() {
    // Make sure the user is a leader
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // List operations of the leaders
    $operations = array(
        "Opérations courantes" => array(
            "Calendrier" => array(
                'url' => $this->user->can(Privilege::$EDIT_CALENDAR) ? URL::route('manage_calendar') : null,
                'help-anchor' => 'calendrier',
                'help' => 'edit-calendar',
                'condition' => Parameter::$SHOW_CALENDAR,
            ),
            "Photos" => array(
                'url' => $this->user->can(Privilege::$POST_PHOTOS) ? URL::route('edit_photos') : null,
                'help-anchor' => 'photos',
                'help' => 'edit-photos',
                'condition' => Parameter::$SHOW_PHOTOS,
            ),
            "Documents à télécharger" => array(
                'url' => $this->user->can(Privilege::$EDIT_DOCUMENTS) ? URL::route('manage_documents') : null,
                'help-anchor' => 'documents',
                'help' => 'edit-documents',
                'condition' => Parameter::$SHOW_DOCUMENTS,
            ),
            "Nouvelles" => array(
                'url' => $this->user->can(Privilege::$EDIT_NEWS) ? URL::route('manage_news') : null,
                'help-anchor' => 'nouvelles',
                'help' => 'edit-news',
                'condition' => Parameter::$SHOW_NEWS,
            ),
            "E-mail aux parents" => array(
                'url' => $this->user->can(Privilege::$SEND_EMAILS) ? URL::route('send_section_email') : null,
                'help-anchor' => 'emails',
                'help' => 'email-section',
            ),
            "Fiches santé" => array(
                'url' => $this->user->can(Privilege::$VIEW_HEALTH_CARDS) ? URL::route('manage_health_cards') : null,
                'help-anchor' => 'fiches-sante',
                'help' => 'edit-health-cards',
                'condition' => Parameter::$SHOW_HEALTH_CARDS,
            ),
            "Trésorerie" => array(
                'url' => URL::route('accounting'),
                'help-anchor' => 'tresorerie',
                'help' => 'accounting',
            ),
        ),
        "Opérations annuelles" => array(
            "Inscriptions" => array(
                'url' => $this->user->can(Privilege::$EDIT_LISTING_ALL) || $this->user->can(Privilege::$EDIT_LISTING_LIMITED) ||
                         $this->user->can(Privilege::$SECTION_TRANSFER || $this->user->can(Privilege::$MANAGE_ACCOUNTING)) ? URL::route('manage_registration') : null,
                'help-anchor' => 'inscriptions',
                'help' => 'manage-registration',
            ),
            "Listing" => array(
                'url' => $this->user->can(Privilege::$EDIT_LISTING_ALL) || $this->user->can(Privilege::$EDIT_LISTING_LIMITED) ? URL::route('manage_listing') : null,
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
            ),
        ),
        "Contenu du site" => array(
            "Page d'accueil" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_home_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
            ),
            "Page d'accueil de la section" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES) ? URL::route('edit_section_page', array('section_slug' => $this->user->currentSection->slug)) : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_SECTIONS,
            ),
            "Page d'adresses utiles" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_address_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_ADDRESSES,
            ),
            "Page de la fête d'unité" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_annual_feast_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_ANNUAL_FEAST,
            ),
            "Page d'inscription" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_registration_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_REGISTRATION,
            ),
            "Page de la charte d'unité" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_unit_policy_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_UNIT_POLICY,
            ),
            "Page d'uniforme" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES) ? URL::route('edit_uniform_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_UNIFORMS,
            ),
            "Page d'aide" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_help_page') : null,
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_HELP,
            ),
            "Liens utiles" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_links') : null,
                'help-anchor' => 'liens',
                'help' => 'edit-links',
                'condition' => Parameter::$SHOW_LINKS,
            ),
            "Paramètres du site" => array(
                'url' => $this->user->can(Privilege::$EDIT_GLOBAL_PARAMETERS) ? URL::route('edit_parameters') : null,
                'help-anchor' => 'parametres',
                'help' => 'edit-parameters',
            ),
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
            ),
            "Gérer les suggestions" => array(
                'url' => $this->user->can(Privilege::$MANAGE_SUGGESIONS) ? URL::route('edit_suggestions') : null,
                'help-anchor' => 'suggestions',
                'help' => 'suggestions',
                'condition' => Parameter::$SHOW_SUGGESTIONS,
            ),
            "Gérer le livre d'or" => array(
                'url' => $this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES) ? URL::route('edit_guest_book') : null,
                'help-anchor' => 'livre-d-or',
                'help' => 'guest-book',
                'condition' => Parameter::$SHOW_GUEST_BOOK,
            ),
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
    // Make view
    return View::make('pages.leaderCorner.leaderCorner', array(
        'operations' => $operations,
        'help_sections' => $helpSections,
    ));
  }
  
}
