<?php

class AccountingController extends BaseController {
  
  public function showPage($year = null) {
    if (!$this->user->isLeader()) return Helper::forbiddenResponse();
    $canEdit = $this->user->can(Privilege::$MANAGE_ACCOUNTING, $this->section);
    $thisYear = $this->getCurrentYear();
    if (!$year) {
      $year = $thisYear;
    }
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
        $categories[$accountingItem->category_name][] = $accountingItem;
      } else {
        $inheritTransaction = $accountingItem;
      }
    }
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
  
  public function commitChanges($year) {
    if (!$this->user->isLeader() || !$this->user->can(Privilege::$MANAGE_ACCOUNTING, $this->section)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges pour modifier les comptes de cette section."));
    }
    try {
      if (!$year) throw new Exception("Year parameter is missing");
      if (!Input::has('data')) throw new Exception("There is no transaction data");
      $categories = json_decode(Input::get('data'));
      $error = false;
      $position = 1;
      $transactions = AccountingItem::where('section_id', '=', $this->section->id)
              ->where('year', '=', $year)
              ->get();
      $oldTransactions = array();
      foreach ($transactions as $transaction) {
        $oldTransactions[$transaction->id] = $transaction;
      }
      $newTransactions = array();
      // Update and create transactions
      foreach ($categories as $category) {
        $categoryName = $category->name;
        foreach ($category->transactions as $transaction) {
          $accountingItem = AccountingItem::find($transaction->id);
          $date = $this->humanDateToSql($transaction->date);
          if ($accountingItem) {
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
    if ($error) {
      return json_encode(array("result" => "Failure", "message" => $error));
    } else {
      return json_encode(array("result" => "Success", "new_transactions" => $newTransactions));
    }
  }
  
  private function updateInheritance($year) {
    $previousYear = $this->getPreviousYear($year);
    $transactions = AccountingItem::where('section_id', '=', $this->section->id)
            ->where('year', '=', $previousYear)
            ->get();
    $inheritanceCash = 0;
    $inheritanceBank = 0;
    foreach ($transactions as $transaction) {
      $inheritanceCash += $transaction->cashin_cents - $transaction->cashout_cents;
      $inheritanceBank += $transaction->bankin_cents - $transaction->bankout_cents;
    }
    $inheritanceTransaction = AccountingItem::where('section_id', '=', $this->section->id)
            ->where('year', '=', $year)
            ->where('object', '=', AccountingItem::$INHERIT)
            ->where('category_name', '=', AccountingItem::$INHERIT)
            ->first();
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
    $inheritanceTransaction->cashin_cents = max(0, $inheritanceCash);
    $inheritanceTransaction->cashout_cents = max(0, -$inheritanceCash);
    $inheritanceTransaction->bankin_cents = max(0, $inheritanceBank);
    $inheritanceTransaction->bankout_cents = max(0, -$inheritanceBank);
    $inheritanceTransaction->save();
  }
  
  private function getCurrentYear() {
    $month = date('m');
    $startYear = date('Y');
    if ($month <= 8) $startYear--;
    return $startYear . "-" . ($startYear + 1);
  }
  
  private function getPreviousYear($currentYear) {
    $startYear = substr($currentYear, 0, 4) - 1;
    return $startYear . "-" . ($startYear + 1);
  }
  
  private function getNextYear($currentYear) {
    $startYear = substr($currentYear, 0, 4) + 1;
    return $startYear . "-" . ($startYear + 1);
  }
  
  private function cashAmountToCents($cash) {
    $cash = str_replace(",", ".", $cash);
    return $cash * 100;
  }
  
  private function humanDateToSql($humanDate) {
    try {
      $split = explode("/", $humanDate);
      return $split[2] . "-" . $split[1] . "-" . $split[0];
    } catch (Exception $e) {
      return false;
    }
  }
  
}
