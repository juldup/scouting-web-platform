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
 * This Eloquent class represents a transaction of the accounting page
 * 
 * Columns:
 *   - year:          The 'YYYY-YYYY' scouting year (e.g 2014-2015)
 *   - section_id:    The section this transaction belongs to
 *   - category_name: The category containing the transaction
 *   - date:          The date of the transaction
 *   - object:        The title of the transaction
 *   - cashin_cents:  The amount of cash received (in cents)
 *   - cashout_cents: The amount of cash paid (in cents)
 *   - bankin_cents:  The amount of money received on the bank account (in cents)
 *   - bankout_cents: The amount of money paid from the bank account in cents)
 *   - comment:       An optional comment giving additional information about the transaction
 *   - receipt:       An optional field where the users can record the numbers/ids of
 *                    the receipts associated with this transaction
 *   - position:      The position of this transaction within its category
 */
class AccountingItem extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * When a transaction has this as object and category_name, it is a special auto-computed
   * transaction that represents the cash and bank amounts inherited from the previous year
   */
  public static $INHERIT = "SPECIAL_TRANSACTION_INHERIT";
  
  /**
   * Returns the cash in amount formatted
   */
  public function cashinFormatted() {
    return Helper::formatCashAmount($this->cashin_cents / 100.0);
  }
  
  /**
   * Returns the cash out amount formatted
   */
  public function cashoutFormatted() {
    return Helper::formatCashAmount($this->cashout_cents / 100.0);
  }
  
  /**
   * Returns the bank in amount formatted
   */
  public function bankinFormatted() {
    return Helper::formatCashAmount($this->bankin_cents / 100.0);
  }
  
  /**
   * Returns the bank out amount formatted
   */
  public function bankoutFormatted() {
    return Helper::formatCashAmount($this->bankout_cents / 100.0);
  }
  
}
