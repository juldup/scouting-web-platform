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
 * This tool and page allows the visitors to search for a string throughout the website
 */
class SearchController extends BaseController {
  
  /**
   * [Route] Applies a search and shows the search page with results
   */
  public function showSearchPage() {
    // Search results
    $searchString = Input::get('search_string');
    if ($searchString) {
      $results = ElasticsearchHelper::find($searchString, $this->user->isMember());
    } else {
      $results = ['hits' => ['total' => 0, 'hits' => []]];
    }
    // Make view
    return View::make('pages.search.search-results', array(
        'search_string' => $searchString,
        'results' => $results['hits'],
        'userIsMember' => $this->user->isMember(),
    ));
  }
  
}
