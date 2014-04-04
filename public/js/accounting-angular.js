// Whether dates are displayed dd/mm/yyyy or mm/dd/yyyy
var datesAmericanStyle = false;
var currencyFormatter = "% €";

// Returns the string representation of today
function today() {
	var todayDate = new Date();
	var dd = todayDate.getDate();
	var mm = todayDate.getMonth() + 1;
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

function compareDates(trans1, trans2) {
	// Get date data
	datesplit1 = trans1.date.split('/');
	datesplit2 = trans2.date.split('/');
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
	d1 = datesplit1[datesAmericanStyle ? 1 : 0];
	m1 = datesplit1[datesAmericanStyle ? 0 : 1];
	y1 = datesplit1[2];
	// Get date data of truns2
	d2 = datesplit2[datesAmericanStyle ? 1 : 0];
	m2 = datesplit2[datesAmericanStyle ? 0 : 1];
	y2 = datesplit2[2];
	// Compare dates
	if (y1 != y2)
		return y1 < y2 ? -1 : 1;
	if (m1 != m2)
		return m1 < m2 ? -1 : 1;
	if (d1 != d2)
		return d1 < d2 ? -1 : 1;
	return 0;
}

var angularAccounting = angular.module('accounting', ['ui']);

var newTransactionCounter = 0;

// Angular controller
angularAccounting.controller('AccountingController', function ($scope) {
	
	$scope.formatCurrency = function(value) {
    var sign = value >= 0 ? "" : "-"
    var ints = Math.floor(Math.abs(value));
    var cents = Math.round(Math.abs(value) * 100) % 100;
    var valueString = sign + ints + (cents === 0 ? "" : "," + (cents < 10 ? "0" : "") + cents);
		return currencyFormatter.replace("%", valueString);
	};
  
	// Data
	$scope.categories = categories;
  $scope.previousYear = previousYear;
  $scope.inheritance = {
    cashin: inheritanceCash > 0 ? $scope.formatCurrency(inheritanceCash) : "",
    cashout: inheritanceCash < 0 ? $scope.formatCurrency(-inheritanceCash) : "",
    bankin: inheritanceBank > 0 ? $scope.formatCurrency(inheritanceBank) : "",
    bankout: inheritanceBank < 0 ? $scope.formatCurrency(-inheritanceBank) : "",
  }
  $scope.canEdit = canEdit;
	
  // Sets or resets the category index of each transaction
  $scope.resetCategories = function() {
    for (var i = 0; i < this.categories.length; i++) {
      for (var j = 0; j < this.categories[i].transactions.length; j++) {
        this.categories[i].transactions[j].category = i;
      }
    }
  }
  
  $scope.resetCategories();
	
	// Creates a new transaction at the end of the list
	$scope.addTransaction = function(category) {
		$scope.categories[category].transactions.push({
			date : today(),
			object : '',
			cashin : '',
			cashout : '',
			bankin : '',
			bankout : '',
			comment : '',
      receipt : '',
			category : category,
      id: 'new-' + newTransactionCounter++
		});
    $scope.uploadChanges();
	};
	
  // Adds a category at the end of the list
  $scope.addCategory = function() {
    newCategory = {
      name: "Nouvelle catégorie",
      transactions: []
    };
    $scope.categories.push(newCategory);
    index = $scope.categories.indexOf(newCategory);
    $scope.addTransaction(index);
    $scope.uploadChanges();
  }
	
	// Removes a transaction from the list
	$scope.remove = function() {
    categoryIndex = this.trans.category;
		var transIndex = $scope.categories[categoryIndex].transactions.indexOf(this.trans);
		if (transIndex >= 0) {
			$scope.categories[categoryIndex].transactions.splice(transIndex, 1);
      if ($scope.categories[categoryIndex].transactions.length == 0) {
        $scope.categories.splice(categoryIndex, 1);
        $scope.resetCategories();
      }
    }
    $scope.uploadChanges();
	};
  
  // Changes the id of a transaction
  $scope.replaceTransactionId = function(oldId, newId) {
    // Find transaction
    $scope.categories.forEach(function(category) {
      category.transactions.forEach(function(transaction) {
        if (transaction.id === oldId) transaction.id = newId;
      });
    });
    $scope.$apply();
  }
  
	// Computes the cash or bank total for a category
	$scope.total = function(bankOrCashOrBoth, category) {
		var total = 0;
		angular.forEach($scope.categories[category].transactions, function(trans) {
			if (bankOrCashOrBoth === 'cash' || bankOrCashOrBoth === 'both')
				total += trans.cashin.replace(",", ".") - trans.cashout.replace(",", ".");
			if (bankOrCashOrBoth === 'bank' || bankOrCashOrBoth === 'both')
				total += trans.bankin.replace(",", ".") - trans.bankout.replace(",", ".");
		});
		return total;
	};
	
	// Computes the cash/bank total for all categories
	$scope.bigTotal = function(bankOrCash) {
		var total = 0;
    if (bankOrCash === 'bank') total = inheritanceBank;
    if (bankOrCash === 'cash') total = inheritanceCash;
		for (var i = 0; i < $scope.categories.length; i++) {
			total += $scope.total(bankOrCash, i);
		}
		return total;
	};
	
	// Sorts the list according to date
	$scope.sortList = function(category) {
		$scope.categories[category].transactions.sort(compareDates);
    $scope.uploadChanges();
	};
	
  // Make the transactions movable within the categories and from one category to another
  $scope.sortableOptions = {
    connectWith: 'tbody',
    dropOnEmpty: true,
    items: canEdit ? "tr:not(:first)" : "",
    placeholder: "ui-state-highlight",
    start: function(event, ui) {
      // Save predecessor to reset position in DOM
      prev = ui.item.prev();
      // Save category and position
      var categoryElement = ui.item.closest("[data-category-index]");
      console.log(categoryElement);
      categoryIndex = categoryElement.data('category-index');
      transactionIndex = ui.item.parent().find("[data-transaction-id]").index(ui.item);
      $scope.sortableData = {
        prev: prev,
        categoryIndex: categoryIndex,
        transactionIndex: transactionIndex,
      };
      console.log("from category " + categoryIndex);
    },
    stop: function(event, ui) {
      // Get new category and position
      var categoryElement = ui.item.closest("[data-category-index]");
      var categoryIndex = categoryElement.data("category-index");
      console.log("to category " + categoryIndex);
      var transactionIndex = ui.item.parent().find("[data-transaction-id]").index(ui.item);
      // Move item
      transaction = ui.item.scope().trans;
      if (categoryIndex !== $scope.sortableData.categoryIndex) {
        // Remove old
        $scope.categories[$scope.sortableData.categoryIndex].transactions.splice($scope.sortableData.transactionIndex, 1);
        // Add new
        $scope.categories[categoryIndex].transactions.splice(transactionIndex, 0, transaction);
        // Remove old category if empty
        if ($scope.categories[$scope.sortableData.categoryIndex].transactions.length === 0) {
          $scope.categories.splice($scope.sortableData.categoryIndex, 1);
        }
      } else {
        // Remove old
        $scope.categories[categoryIndex].transactions.splice($scope.sortableData.transactionIndex, 1);
        // Add new
        $scope.categories[categoryIndex].transactions.splice(transactionIndex, 0, transaction);
      }
      $scope.resetCategories();
      // Reset DOM
      // Save scrolling position
      var scroll = $(window).scrollTop();
      // DOM might have been messed up by moving the items, so reset it to an empty state
      var categories = $scope.categories;
      $scope.categories = [];
      $scope.$apply();
      // Then reapply the changes
      $scope.categories = categories;
      $scope.$apply();
      // Then reset the scroll position of the page
      $("html").scrollTop(scroll);
    }
  };
  
  // Watch changes in categories (avoiding initialisation)
  setTimeout(function() {
    $scope.$watch('categories', function() {
      $scope.uploadChanges();
    }, true);
  }, 0);
  
  $scope.uploading = false;
  $scope.uploadId = 0;
  $scope.uploadChanges = function() {
    if (!$scope.canEdit) return;
    $scope.uploadId++;
    $("#pending-commit").show();
    if ($scope.uploading) {
      return;
    }
    var uploadId = $scope.uploadId;
    setTimeout(function() {
      if (uploadId !== $scope.uploadId) {
        // There are more recent changes, don't upload now
      } else {
        // Upload now
        $scope.uploading = true;
        $.ajax({
          type: "POST",
          url: commitAccountingChangesURL,
          data: {'data': JSON.stringify($scope.categories)}
        }).done(function(json) {
          try {
            data = JSON.parse(json);
            if (data.result === "Success") {
              var newTransactions = data.new_transactions;
              for (var oldId in newTransactions) {
                $scope.replaceTransactionId(oldId, newTransactions[oldId]);
              }
              $scope.uploading = false;
              if (uploadId !== $scope.uploadId) {
                $scope.uploadChanges();
              } else {
                $("#pending-commit").hide();
              }
            } else {
              console.error(data.message);
              throw "error";
            }
          } catch (err) {
            alert("Une erreur est survenue lors de l'enregistrement des comptes.");
            // Reload page
            window.location = window.location;
          }
        });
      }
    }, 100); // Upload in 1 second // TODO 1000
  }
  
});

angular.bootstrap(document, ['accounting']);
