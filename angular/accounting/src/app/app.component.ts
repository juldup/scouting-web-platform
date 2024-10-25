import { NgClass, NgFor, NgIf } from '@angular/common';
import { Component, NO_ERRORS_SCHEMA } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import * as $ from 'jquery';
import { FormsModule } from '@angular/forms';

/* HELPER FUNCTIONS */

// Whether dates are displayed dd/mm/yyyy or mm/dd/yyyy
const datesAmericanStyle = false;
// How the money amounts are displayed
const currencyFormatter = "% €";


/**
 * (Helper function) Returns the string representation of today
 */
function today() {
  var todayDate = new Date();
  var dd: number | string = todayDate.getDate();
  var mm: number | string = todayDate.getMonth() + 1;
  var yyyy = todayDate.getFullYear();
  if (dd < 10)
    dd = '0' + dd;
  if (mm < 10)
    mm = '0' + mm;
  if (datesAmericanStyle)
    return mm + '/' + dd + '/' + yyyy;
  else
    return dd + '/' + mm + '/' + yyyy;
}

/**
 * (Helper function) Compares two transactions by dates. Dates are
 * expressed in the format 'dd/mm/yyyy' or 'mm/dd/yyyy' depending
 * on the datesAmericanStyle parameter.
 * This function can be used to sort an array of transactions.
 * Returns -1 if trans1 < trans2, 0 if trans1 = trans2 and 1 if
 * trans1 > trans2.
 */
function compareDates(trans1: any, trans2: any) {
  // Get date data
  var datesplit1 = trans1.date.split('/');
  var datesplit2 = trans2.date.split('/');
  // Check date validity
  if (datesplit1.length != 3) {
    if (datesplit2.length == 3) {
      // Date 1 is invalid and date 2 is valid
      return 1;
    } else {
      // Both dates are invalid
      if (trans1.date < trans2.date)
        return -1;
      if (trans1.date == trans2.date)
        return 0;
      return 1;
    }
  } else if (datesplit2.length != 3) {
    // Date 1 is valid and date 2 is invalid
    return -1;
  }
  // Get date data of trans1
  var d1 = datesplit1[datesAmericanStyle ? 1 : 0];
  var m1 = datesplit1[datesAmericanStyle ? 0 : 1];
  var y1 = datesplit1[2];
  // Get date data of truns2
  var d2 = datesplit2[datesAmericanStyle ? 1 : 0];
  var m2 = datesplit2[datesAmericanStyle ? 0 : 1];
  var y2 = datesplit2[2];
  // Compare dates
  if (y1 != y2)
    return y1 < y2 ? -1 : 1;
  if (m1 != m2)
    return m1 < m2 ? -1 : 1;
  if (d1 != d2)
    return d1 < d2 ? -1 : 1;
  return 0;
}

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NgIf, NgFor, NgClass, FormsModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.less',
  schemas: [NO_ERRORS_SCHEMA],
})
export class AppComponent {
  title = 'accounting';
  
  // Counter of newly created transactions to create unique ids
  newTransactionCounter = 0;
    
  /**
   * Formats the given value using the currencyFormatter parameter
   */
  formatCurrency(value: any) {
    var sign = value >= 0 ? "" : "-"
    var ints = Math.floor(Math.abs(value));
    var cents = Math.round(Math.abs(value) * 100) % 100;
    var valueString = sign + ints + (cents === 0 ? "" : "," + (cents < 10 ? "0" : "") + cents);
    return currencyFormatter.replace("%", valueString);
  };
  
  // Urls for ajax requests
  extendLockURL = (window as any).extendLockURL;
  commitAccountingChangesURL = (window as any).commitAccountingChangesURL;
  
  /* Data */
  
  // List of categories, initial values must be set in the categories variable in the page in this form:
  // categories = [
  //   {name: "%NAME%",
  //    transactions: [
  //      {date: "DD/MM/YYYY or MM/DD/YYYY",
  //       object: "%OBJECT%",
  //       cashin: "X,XX or X.XX",
  //       cashout: "X,XX or X.XX",
  //       bankin: "X,XX or X.XX",
  //       bankout: "X,XX or X.XX",
  //       comment: "%COMMENT%",
  //       receipt: "%RECEIPT%",
  //       id: X
  //      },...
  //    ]
  //   },...
  // ]
  categories = (window as any).categories;
  
  // The previous year (used in the inheritance transaction), must be set in the page
  previousYear = (window as any).previousYear;
  
  // Inheritance values (inheritanceCash and inheritanceBank must be set in the script in the format -X.XX)
  inheritance = {
    cashin: (window as any).inheritanceCash > 0 ? this.formatCurrency((window as any).inheritanceCash) : "",
    cashout: (window as any).inheritanceCash < 0 ? this.formatCurrency(-(window as any).inheritanceCash) : "",
    bankin: (window as any).inheritanceBank > 0 ? this.formatCurrency((window as any).inheritanceBank) : "",
    bankout: (window as any).inheritanceBank < 0 ? this.formatCurrency(-(window as any).inheritanceBank) : "",
  };
  
  // Whether the user can edit the values (must be set in the page)
  canEdit = (window as any).canEdit;
  
  // Sortable data
  sortableData: any = null;
  
  /**
   * Sets or resets the category index of each transaction
   */
  resetCategories() {
    for (var i = 0; i < this.categories.length; i++) {
      for (var j = 0; j < this.categories[i].transactions.length; j++) {
        this.categories[i].transactions[j].category = i;
      }
    }
  };
  
  extendLock() {
    console.log("Extending lock");
    // Get csrf token
    let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
      alert("Une erreur est survenue. La page va être rechargée.");
      window.location = window.location;
    }
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': token,
      }
    });
    $.ajax({
      type: "GET",
      url: this.extendLockURL,
    }).done((json) => {
      console.log(json);
      try {
        var data = JSON.parse(json);
        if (data.result === "Success") {
          // Lock extension was successful, re-extend in 10 seconds
          setTimeout(this.extendLock, 10000);
        } else {
          // An error has occured
          console.error(data.message);
          throw "error";
        }
      } catch (err) {
        console.log(err);
        // Lock has been lost for this page
        alert("Il est impossible de modifier les comptes sur plusieurs pages simultanément. Pour les modifier à nouveau ici, rafraichis cette page.");
        // Quit editing mode
        this.canEdit = false;
      }
    });
  };
  
  constructor() {  
    // Set categories initially
    this.resetCategories();
    // Watch changes in categories (avoiding initialization)
    /*setTimeout(() => {
      this.$watch('categories', function() {
        this.uploadChanges();
      }, true);
    }, 0);*/ // TODO ?
    // Extend lock periodically
    if (this.canEdit) {
      setTimeout(this.extendLock, 10000);
    }
  }
  
  /**
   * Creates a new empty transaction at the end of the list in a
   * given category
   */
  addTransaction(category: any) {
    this.categories[category].transactions.push({
      date : today(),
      object : '',
      cashin : '',
      cashout : '',
      bankin : '',
      bankout : '',
      comment : '',
      receipt : '',
      category : category,
      id: 'new-' + this.newTransactionCounter++
    });
    this.uploadChanges();
  };
  
  /**
   * Adds a category at the end of the list
   */
  addCategory() {
    var newCategory = {
      name: "Nouvelle catégorie",
      transactions: []
    };
    this.categories.push(newCategory);
    var index = this.categories.indexOf(newCategory);
    this.addTransaction(index);
    this.uploadChanges();
  };
  
  /**
   * Removes a transaction (this) from the list
   */
  remove(trans: any) {
    var categoryIndex = trans.category;
    var transIndex = this.categories[categoryIndex].transactions.indexOf(trans);
    if (transIndex >= 0) {
      this.categories[categoryIndex].transactions.splice(transIndex, 1);
      if (this.categories[categoryIndex].transactions.length == 0) {
        this.categories.splice(categoryIndex, 1);
        this.resetCategories();
      }
    }
    // Save the change
    this.uploadChanges();
  };
  
  /**
   * Changes the id of a transaction (used to replace the temporary id with the
   * actual id of a new transaction)
   */
  replaceTransactionId(oldId: any, newId: any) {
    // Find transaction
    this.categories.forEach((category: any) => {
      category.transactions.forEach((transaction: any) => {
        if (transaction.id === oldId) transaction.id = newId;
      });
    });
  };
  
  /**
   * Computes the cash or bank total for a category
   */
  total(bankOrCashOrBoth: any, category: any) {
    var total = 0;
    this.categories[category].transactions.forEach((trans: any) => {
      if (bankOrCashOrBoth === 'cash' || bankOrCashOrBoth === 'both')
        total += trans.cashin.replace(",", ".") - trans.cashout.replace(",", ".");
      if (bankOrCashOrBoth === 'bank' || bankOrCashOrBoth === 'both')
        total += trans.bankin.replace(",", ".") - trans.bankout.replace(",", ".");
    });
    return total;
  };
  
  /**
   * Computes the cash/bank total for all categories
   */
  bigTotal(bankOrCash: any) {
    var total = 0;
    if (bankOrCash === 'bank') total = (window as any).inheritanceBank;
    if (bankOrCash === 'cash') total = (window as any).inheritanceCash;
    for (var i = 0; i < this.categories.length; i++) {
      total += this.total(bankOrCash, i);
    }
    return total;
  };
  
  /**
   * Sorts the transactions of a category according to date
   */
  sortList(category: any) {
    this.categories[category].transactions.sort(compareDates);
    this.uploadChanges();
  };
  
  //Make the transactions movable within the categories and from one category to another
  sortableOptions = {
    connectWith: 'tbody',
    dropOnEmpty: true,
    items: this.canEdit ? "tr:not(:first)" : "",
    placeholder: "ui-state-highlight",
    start: (event: any, ui: any) => {
      // Save predecessor to reset position in DOM
      var prev = ui.item.prev();
      // Save category and position
      var categoryElement = ui.item.closest("[data-category-index]");
      var categoryIndex = categoryElement.data('category-index');
      var transactionIndex = ui.item.parent().find("[data-transaction-id]").index(ui.item);
      this.sortableData = {
        prev: prev,
        categoryIndex: categoryIndex,
        transactionIndex: transactionIndex,
      };
    },
    stop: (event: any, ui: any) => {
      // Get new category and position
      var categoryElement = ui.item.closest("[data-category-index]");
      var categoryIndex = categoryElement.data("category-index");
      var transactionIndex = ui.item.parent().find("[data-transaction-id]").index(ui.item);
      // Move item
      var transaction = ui.item.scope().trans;
      if (categoryIndex !== this.sortableData.categoryIndex) {
        // Remove old
        this.categories[this.sortableData.categoryIndex].transactions.splice(this.sortableData.transactionIndex, 1);
        // Add new
        this.categories[categoryIndex].transactions.splice(transactionIndex, 0, transaction);
        // Remove old category if empty
        if (this.categories[this.sortableData.categoryIndex].transactions.length === 0) {
          this.categories.splice(this.sortableData.categoryIndex, 1);
        }
      } else {
        // Remove old
        this.categories[categoryIndex].transactions.splice(this.sortableData.transactionIndex, 1);
        // Add new
        this.categories[categoryIndex].transactions.splice(transactionIndex, 0, transaction);
      }
      this.resetCategories();
      // Reset DOM
      // Save scrolling position
      var scroll = window.scrollY || document.documentElement.scrollTop;
      // DOM might have been messed up by moving the items, so reset it to an empty state
      var categories = this.categories;
      this.categories = [];
      // Then reapply the changes
      this.categories = categories;
      // Then reset the scroll position of the page
      document.documentElement.scrollTop = scroll; // For most modern browsers
      document.body.scrollTop = scroll; // For older browsers (mostly for older versions of Safari)
    }
  };
  
  /* SYNCHRONIZATION */
  
  
  
  // Uploading status
  uploading = false;
  
  // Change counter (to avoid uploading when more recent changes have been made)
  uploadId = 0;
  
  // Whether the uploading icon must be shown
  showUploading = false;
  
  /**
   * Uploads the current state to the server
   */
  uploadChanges() {
    console.log("uploadChanges()");
    // If editing is not allowed, don't upload
    if (!this.canEdit) return;
    // Increment upload counter
    this.uploadId++;
    // Show synchronization icon
    this.showUploading = true;
    // Don't upload now if an upload is already running
    if (this.uploading) {
      console.log("Already uploading");
      return;
    }
    // Get current upload
    var uploadId = this.uploadId;
    // Set timeout in a short time, to avoid sending data all the time if
    // others changes are made subsequently
    setTimeout(() => {
      if (uploadId !== this.uploadId) {
        console.log("There are more recent changes, don't upload now");
        // There are more recent changes, don't upload now
      } else {
        // Get csrf token
        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
          alert("Une erreur est survenue. La page va être rechargée.");
          window.location = window.location;
        }
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': token,
          }
        });
        // Upload now
        this.uploading = true;
        console.log("Post request to", this.commitAccountingChangesURL, this.categories);
        $.ajax({
          type: "POST",
          url: this.commitAccountingChangesURL,
          data: {'data': JSON.stringify(this.categories)}
        }).done((json) => {
          console.log(json);
          try {
            var data = JSON.parse(json);
            var errorMessage = null;
            if (data.result === "Success") {
              // Upload was successful
              // Replace new transactions' temporary ids with actual ids
              var newTransactions = data.new_transactions;
              for (var oldId in newTransactions) {
                this.replaceTransactionId(oldId, newTransactions[oldId]);
              }
              // Stop uploading
              this.uploading = false;
              if (uploadId !== this.uploadId) {
                // Other changes are waiting for upload, upload them
                this.uploadChanges();
              } else {
                // No more pending upload, hide the synchronization icon
                this.showUploading = false;
              }
            } else {
              // An error has occured
              console.error(data.message);
              errorMessage = data.message;
              throw "error";
            }
          } catch (err) {
            console.log(err);
            // On error, reload the page so the user can see what has actually been saved
            alert(errorMessage ? errorMessage : "Une erreur est survenue lors de l'enregistrement des comptes.");
            // Reload page
            window.location = window.location;
          }
        });
      }
    }, 1000); // Upload in 1 second
  };
  
}