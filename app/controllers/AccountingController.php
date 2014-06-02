<?php

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
    ));
  }
  
  /**
   * [Route] Ajax call to update the changes to the accounting data
   * 
   * @param string $year  The scout year being modified
   */
  public function commitChanges($year) {
    if (!$this->user->isLeader() || !$this->user->can(Privilege::$MANAGE_ACCOUNTING, $this->section)) {
      // Access denied, return error result
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges pour modifier les comptes de cette section."));
    }
    try {
      // Basic request checks
      if (!$year) throw new Exception("Year parameter is missing");
      if (!Input::has('data')) throw new Exception("There is no transaction data");
      $error = false;
      // Get the new list of transactions
      $categories = json_decode(Input::get('data'));
      // Position to create the order of the transactions
      $position = 1;
      // Get current transactions
      $transactions = AccountingItem::where('section_id', '=', $this->section->id)
              ->where('year', '=', $year)
              ->get();
      // List of deleted transactions (undeleted transactions will be removed from this list)
      $oldTransactions = array();
      foreach ($transactions as $transaction) {
        $oldTransactions[$transaction->id] = $transaction;
      }
      // Container for the new transactions (to pass the new ids to the page)
      $newTransactions = array();
      // Update and create transactions
      foreach ($categories as $category) {
        $categoryName = $category->name;
        foreach ($category->transactions as $transaction) {
          $accountingItem = AccountingItem::find($transaction->id);
          $date = $this->humanDateToSql($transaction->date);
          if ($accountingItem) {
            // Update transaction
            unset($oldTransactions[$transaction->id]);
            $accountingItem->category_name = $categoryName;
            if ($date) {
              $accountingItem->date = $date;
            }
            $accountingItem->object = $transaction->object;
            $accountingItem->cashin_cents = $this->cashAmountToCents($transaction->cashin);
            $accountingItem->cashout_cents = $this->cashAmountToCents($transaction->cashout);
            $accountingItem->bankin_cents = $this->cashAmountToCents($transaction->bankin);
            $accountingItem->bankout_cents = $this->cashAmountToCents($transaction->bankout);
            $accountingItem->comment = $transaction->comment;
            $accountingItem->receipt = $transaction->receipt;
            $accountingItem->position = $position++;
            try {
              $accountingItem->save();
            } catch (Exception $e) {
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
            } catch (Exception $e) {
              $error = $e->getMessage();
            }
            // Record new transaction id for sending back to the page
            $newTransactions[$transaction->id] = $accountingItem->id;
          }
        }
      }
      // Delete unexisting transactions
      foreach ($oldTransactions as $transaction) {
        $transaction->delete();
      }
    } catch (Exception $e) {
      $error = $e->getMessage();
    }
    // Return response
    if ($error) {
      return json_encode(array("result" => "Failure", "message" => $error));
    } else {
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
          'date' => "0000-00-00",
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
    return $cash * 100;
  }
  
  /**
   * Converts a 'D/M/YYYY' date to an sql date
   */
  private function humanDateToSql($humanDate) {
    try {
      $split = explode("/", $humanDate);
      return $split[2] . "-" . $split[1] . "-" . $split[0];
    } catch (Exception $e) {
      return false;
    }
  }
  
}
