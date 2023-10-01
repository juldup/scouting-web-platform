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
 * Comments can be added on some objects of the website.
 * 
 * This class provides a function to add a new comment.
 */
class CommentController extends BaseController {
  
  /**
   * [Route] Call to create a new comment
   */
  public function postComment($referentType, $referentId) {
    if (!$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    $body = trim(Input::get('body'));
    if (!$body) return Response::json(array('result' => 'Error'), 400);
    Comment::create(array(
        "body" => Input::get('body'),
        "user_id" => $this->user->id,
        "referent_id" => $referentId,
        "referent_type" => $referentType,
    ));
    return json_encode(array("result" => "Success"));
  }
  
}
