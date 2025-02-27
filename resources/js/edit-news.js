/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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
 * This script is present on the news management page
 */

/**
 * Empties and shows the news form
 */
window.addNews = function() {
  $("#news_form [name='news_id']").val("");
  $("#news_form [name='news_title']").val("");
  window.editorInstance.setData("");
  $("#news_form [name='section']").val(currentSection);
  $("#news_form #delete_link").hide();
  $("#news_form").slideDown();
}

/**
 * Hides the news form
 */
window.dismissNewsForm = function() {
  $("#news_form").slideUp();
}

/**
 * Sets the news form to match an existing news item and shows it
 */
window.editNews = function(newsId) {
  $("#news_form [name='news_id']").val(newsId);
  $("#news_form [name='news_title']").val(news[newsId].title);
  window.editorInstance.setData(news[newsId].body);
  $("#news_form [name='section']").val(news[newsId].section);
  $("#news_form #delete_link").attr('href', news[newsId].delete_url);
  $("#news_form #delete_link").show();
  $("#news_form").slideDown();
}
