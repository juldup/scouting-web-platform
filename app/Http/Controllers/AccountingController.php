<?php
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

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\CalendarPDF;
use App\Helpers\DateHelper;
use App\Helpers\ElasticsearchHelper;
use App\Helpers\EnvelopsPDF;
use App\Helpers\Form;
use App\Helpers\HealthCardPDF;
use App\Helpers\Helper;
use App\Helpers\ListingComparison;
use App\Helpers\ListingPDF;
use App\Helpers\Resizer;
use App\Helpers\ScoutMailer;
use App\Models\Absence;
use App\Models\AccountingItem;
use App\Models\AccountingLock;
use App\Models\ArchivedLeader;
use App\Models\Attendance;
use App\Models\BannedEmail;
use App\Models\CalendarItem;
use App\Models\Comment;
use App\Models\DailyPhoto;
use App\Models\Document;
use App\Models\Email;
use App\Models\EmailAttachment;
use App\Models\GuestBookEntry;
use App\Models\HealthCard;
use App\Models\Link;
use App\Models\LogEntry;
use App\Models\Member;
use App\Models\MemberHistory;
use App\Models\News;
use App\Models\Page;
use App\Models\PageImage;
use App\Models\Parameter;
use App\Models\PasswordRecovery;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\PendingEmail;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Privilege;
use App\Models\Section;
use App\Models\Suggestion;
use App\Models\TemporaryRegistrationLink;
use App\Models\User;

/**
 * Accounting is a tool for the leaders to manage the section or unit finances.
 * 
 * There is a unique page for accounting, and the changes are recorded using ajax.
 * This page's access is restricted to leaders.
 */
class AccountingController extends BaseController {
  
  protected $isSectionPage = true;
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Generates the page when no year is specified
   */
  public function showPageCurrentYear() {
    return $this->showPage(null);
  }
  
  /**
   * [Route] Generates the page
   * 
   * @param string $year  Used to view the accounting data of another year (default is the current scout year)
   */
  public function showPage($year = null) {
    // Access is restricted to leaders
    if (!$this->user->isLeader()) return Helper::forbiddenResponse();
    // Check if the current leader can edit this accounting data
    $canEdit = $this->user->can(Privilege::$MANAGE_ACCOUNTING, $this->section);
    // Select year
    $thisYear = $this->getCurrentYear();
    if (!$year) {
      $year = $thisYear;
    }
    // Compute inheritance from the previous year
    $this->updateInheritance($year);
    // Get categories
    $categories = array();
    $accountingItems = AccountingItem::where('section_id', '=', $this->section->id)
            ->where('year', '=', $year)
            ->groupBy('category_name')
            ->orderBy('id')
            ->get();
    foreach ($accountingItems as $accountingItem) {
      if ($accountingItem->category_name != AccountingItem::$INHERIT) {
        $categories[$accountingItem->category_name] = array();
      }
    }
    // Get transactions
    $accountingItems = AccountingItem::where('section_id', '=', $this->section->id)
            ->where('year', '=', $year)
            ->orderBy('position')
            ->get();
    foreach ($accountingItems as $accountingItem) {
      if ($accountingItem->category_name != AccountingItem::$INHERIT || $accountingItem->object != AccountingItem::$INHERIT) {
        // Add to the list
        $categories[$accountingItem->category_name][] = $accountingItem;
      } else {
        // Inheritance transaction, don't add it to the list
        $inheritTransaction = $accountingItem;
      }
    }
    // Delete expired locks
    AccountingLock::where('timestamp', '<', time() - 30)->delete();
    // Check if this accounting page is locked by another user
    $lockedByUser = false;
    $accountingLock = null;
    if ($canEdit) {
      // Get current lock on this page
      $lock = AccountingLock::where('invalidated', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->where('year', '=', $year)
              ->first();
      if ($lock) {
        // There is a lock
        if ($lock->user_id == $this->user->id) {
          // Locked by another window of the same user, invalidate previous lock and create a new one
          $lock->invalidated = true;
          $lock->save();
          $accountingLock = AccountingLock::create(array(
              'section_id' => $this->section->id,
              'user_id' => $this->user->id,
              'timestamp' => time(),
              'year' => $year
          ));
        } else {
          // Locked by another user, disable editing
          $canEdit = false;
          $user = User::find($lock->user_id);
          $lockedByUser = $user->username;
        }
      } else {
        // Not locked, create new lock
        $accountingLock = AccountingLock::create(array(
            'section_id' => $this->section->id,
            'user_id' => $this->user->id,
            'timestamp' => time(),
            'year' => $year
        ));
      }
    }
    // Make view
    return View::make('pages.accounting.accounting', array(
        'categories' => $categories,
        'year' => $year,
        'previous_year' => $this->getPreviousYear($year),
        'next_year' => $this->getNextYear($year),
        'this_year' => $thisYear,
        'inherit_cash' => ($inheritTransaction->cashin_cents - $inheritTransaction->cashout_cents) / 100.0,
        'inherit_bank' => ($inheritTransaction->bankin_cents - $inheritTransaction->bankout_cents) / 100.0,
        'can_edit' => $canEdit,
        'locked_by_user' => $lockedByUser,
        'lock_id' => $accountingLock ? $accountingLock->id : "none",
    ));
  }
  
  /**
   * [Route] Ajax call to update the lock of an accounting instance
   */
  public function ajaxUpdateLock($lock_id) {
    // Get lock
    $lock = AccountingLock::where('id', '=', $lock_id)
            ->where('timestamp', '>', time() - 30)
            ->where('invalidated', '=', false)
            ->where('user_id', '=', $this->user->id)
            ->first();
    if ($lock) {
      // Lock found, refresh its timestamp
      $lock->timestamp = time();
      $lock->save();
      return json_encode(array("result" => "Success"));
    } else {
      // Lock does not exist, return error message
      return json_encode(array("result" => "Failure", "message" => "Cette page n'est plus réservée. $lock_id"));
    }
  }
  
  /**
   * [Route] Ajax call to update the changes to the accounting data
   * 
   * @param string $lock_id  Id of the accounting lock
   */
  public function commitChanges(Request $request, $lock_id) {
    if (!$this->user->isLeader() || !$this->user->can(Privilege::$MANAGE_ACCOUNTING, $this->section)) {
      // Access denied, return error result
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges pour modifier les comptes de cette section."));
    }
    // Check lock
    $lock = AccountingLock::where('id', '=', $lock_id)
            ->where('timestamp', '>', time() - 30)
            ->where('invalidated', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->where('user_id', '=', $this->user->id)
            ->first();
    if (!$lock) {
      return json_encode(array("result" => "Failure", "message" => "La connexion à ces comptes a été interrompue ou une autre page a été ouverte. Veuillez réessayer."));
    }
    $year = $lock->year;
    // Lock is valid
    try {
      // Basic request checks
      if (!$year) throw new Exception("Year parameter is missing");
      if (!$request->has('data')) throw new Exception("There is no transaction data");
      $error = false;
      $changesMade = "";
      // Get data and unescape it if necessary
      $data = $request->input('data');
      if (strpos($data, "[{\\\"") === 0) $data = str_replace("\\\"", "\"", $data);
      // Get the new list of transactions
      $categories = json_decode($data);
      // Position to create the order of the transactions
      $position = 1;
      // Get current transactions
      $transactions = AccountingItem::where('section_id', '=', $this->section->id)
              ->where('year', '=', $year)
              ->get();
      // List of deleted transactions (undeleted transactions will be removed from this list)
      $oldTransactions = array();
      foreach ($transactions as $transaction) {
        if ($transaction->category_name != AccountingItem::$INHERIT) {
          $oldTransactions[$transaction->id] = $transaction;
        }
      }
      // Container for the new transactions (to pass the new ids to the page)
      $newTransactions = array();
      // Update and create transactions
      foreach ($categories as $category) {
        $categoryName = $category->name;
        $orderModified = false;
        foreach ($category->transactions as $transaction) {
          $accountingItem = AccountingItem::find($transaction->id);
          $date = $this->humanDateToSql($transaction->date);
          if ($accountingItem) {
            if (!$date) $date = $accountingItem->date;
            $cashinCents = $this->cashAmountToCents($transaction->cashin);
            $cashoutCents = $this->cashAmountToCents($transaction->cashout);
            $bankinCents = $this->cashAmountToCents($transaction->bankin);
            $bankoutCents = $this->cashAmountToCents($transaction->bankout);
            if ($categoryName != $accountingItem->category_name || $date != $accountingItem->date || $transaction->object != $accountingItem->object ||
                    round($cashinCents) != round($accountingItem->cashin_cents) || round($cashoutCents) != round($accountingItem->cashout_cents) ||
                    round($bankinCents) != round($accountingItem->bankin_cents) || round($bankoutCents) != round($accountingItem->bankout_cents) ||
                    $transaction->comment != $accountingItem->comment || $transaction->receipt != $accountingItem->receipt) {
              $changesMade .= "- Modification " . $accountingItem->diffRepresentation($categoryName, $date,
                      $transaction->object, $cashinCents, $cashoutCents, $bankinCents, $bankoutCents,
                      $transaction->comment, $transaction->receipt) . "<br>" ;
            }
            if ($accountingItem->position != $position) $orderModified = true;
            // Update transaction
            unset($oldTransactions[$transaction->id]);
            $accountingItem->category_name = $categoryName;
            $accountingItem->date = $date;
            $accountingItem->object = $transaction->object;
            $accountingItem->cashin_cents = $cashinCents;
            $accountingItem->cashout_cents = $cashoutCents;
            $accountingItem->bankin_cents = $bankinCents;
            $accountingItem->bankout_cents = $bankoutCents;
            $accountingItem->comment = $transaction->comment;
            $accountingItem->receipt = $transaction->receipt;
            $accountingItem->position = $position++;
            try {
              $accountingItem->save();
            } catch (Exception $e) {
              Log::error($e);
              $error = $e->getMessage();
            }
          } else {
            // Create new transaction
            try {
              $accountingItem = AccountingItem::create(array(
                  'object' => $transaction->object,
                  'year' => $year,
                  'section_id' => $this->section->id,
                  'category_name' => $categoryName,
                  'date' => $date ? $date : date('Y-m-d'),
                  'cashin_cents' => $this->cashAmountToCents($transaction->cashin),
                  'cashout_cents' => $this->cashAmountToCents($transaction->cashout),
                  'bankin_cents' => $this->cashAmountToCents($transaction->bankin),
                  'bankout_cents' => $this->cashAmountToCents($transaction->bankout),
                  'comment' => $transaction->comment,
                  'receipt' => $transaction->receipt,
                  'position' => $position++,
              ));
              // Record new transaction id for sending back to the page
              $newTransactions[$transaction->id] = $accountingItem->id;
              $changesMade .= "- Ajout <ins>" . $accountingItem->tupleRepresentation() . "</ins><br>";
            } catch (Exception $e) {
              Log::error($e);
              $error = $e->getMessage();
            }
          }
        }
        if ($orderModified) $changesMade = "- L'ordre des transactions de la catégorie <strong>$categoryName</strong> a été modifié<br>";
      }
      // Delete unexisting transactions
      foreach ($oldTransactions as $transaction) {
        $changesMade .= "- Suppression <del>" . $transaction->tupleRepresentation() . "</del><br>";
        $transaction->delete();
      }
    } catch (Exception $e) {
      Log::error($e);
      $error = $e->getMessage();
    }
    // Return response
    if ($error) {
      LogEntry::error("Comptes", "Erreur lors de l'enregistrement des comptes", array('Erreur' => $error));
      return json_encode(array("result" => "Failure", "message" => "Une erreur est survenue lors de l'enregistrement des comptes."));
    } else {
      if ($changesMade) {
        LogEntry::log("Comptes", "Comptes modifiés", ["Changements" => $changesMade], true);
      }
      return json_encode(array("result" => "Success", "new_transactions" => $newTransactions));
    }
  }
  
  /**
   * Computes the total of transaction of the year before and update
   * the current year's inheritance transaction
   * 
   * @param string $year  The current scout year
   */
  private function updateInheritance($year) {
    // Get previous year
    $previousYear = $this->getPreviousYear($year);
    // Get previous year's transactions
    $transactions = AccountingItem::where('section_id', '=', $this->section->id)
            ->where('year', '=', $previousYear)
            ->get();
    // Compute the sum (cash and bank)
    $inheritanceCash = 0;
    $inheritanceBank = 0;
    foreach ($transactions as $transaction) {
      $inheritanceCash += $transaction->cashin_cents - $transaction->cashout_cents;
      $inheritanceBank += $transaction->bankin_cents - $transaction->bankout_cents;
    }
    // Get inheritance transaction
    $inheritanceTransaction = AccountingItem::where('section_id', '=', $this->section->id)
            ->where('year', '=', $year)
            ->where('object', '=', AccountingItem::$INHERIT)
            ->where('category_name', '=', AccountingItem::$INHERIT)
            ->first();
    // Create inheritance transaction if it does not exist
    if (!$inheritanceTransaction) {
      $inheritanceTransaction = AccountingItem::create(array(
          'object' => AccountingItem::$INHERIT,
          'year' => $year,
          'section_id' => $this->section->id,
          'category_name' => AccountingItem::$INHERIT,
          'date' => "1900-01-01",
          'comment' => '',
          'receipt' => '',
          'position' => 0,
      ));
    }
    // Update inheritance transaction
    $inheritanceTransaction->cashin_cents = max(0, $inheritanceCash);
    $inheritanceTransaction->cashout_cents = max(0, -$inheritanceCash);
    $inheritanceTransaction->bankin_cents = max(0, $inheritanceBank);
    $inheritanceTransaction->bankout_cents = max(0, -$inheritanceBank);
    $inheritanceTransaction->save();
  }
  
  /**
   * Returns the current scout year in the form 'YYYY-YYYY' (scout year starts in August)
   */
  private function getCurrentYear() {
    $month = date('m');
    $startYear = date('Y');
    if ($month < 8) $startYear--;
    return $startYear . "-" . ($startYear + 1);
  }
  
  /**
   * Returns the scout year before a given scout year (both in the form 'YYYY-YYYY')
   */
  private function getPreviousYear($currentYear) {
    $startYear = substr($currentYear, 0, 4) - 1;
    return $startYear . "-" . ($startYear + 1);
  }
  
  /**
   * Returns the scout year after the given scout year (both in the form 'YYYY-YYYY')
   */
  private function getNextYear($currentYear) {
    $startYear = substr($currentYear, 0, 4) + 1;
    return $startYear . "-" . ($startYear + 1);
  }
  
  /**
   * Converts an amount in euros to the same amount in eurocents.
   * Input must be a real positive number. Decimal delimiter can be '.' or ','.
   */
  private function cashAmountToCents($cash) {
    $cash = str_replace(",", ".", $cash);
    return intval($cash) * 100;
  }
  
  /**
   * Converts a 'D/M/YYYY' date to an sql date
   */
  private function humanDateToSql($humanDate) {
    try {
      $split = explode("/", $humanDate);
      return $split[2] . "-" . ($split[1] <= 9 ? "0" : "") . (0 + $split[1]) . "-" . ($split[0] <= 9 ? "0" : "") . (0 + $split[0]);
    } catch (Exception $e) {
      Log::error($e);
      return false;
    }
  }
  
}
