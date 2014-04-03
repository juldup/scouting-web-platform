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

var myapp = angular.module('myapp', ['ui']);

// Angular controller
myapp.controller('AccountingController', function ($scope) {
	
	// Example data
	$scope.categories = [ {
		name : "Initial state",
    id: 1,
		transactions : [ {
			date : '01/01/2013',
			object : 'Inheritance from last year',
			cashin : 500,
			cashout : "",
			bankin : 175,
			bankout : "",
			comment : "",
      receipt : "",
      id: 1
		} ]
	}, {
		name : "Bank transactions",
    id: 2,
		transactions : [ {
			date : '01/03/2013',
			object : 'Withdrawal',
			cashin : 20,
			cashout : "",
			bankin : "",
			bankout : 20,
			comment : "Withdrawal is free",
      receipt : "1",
      id: 2
		}, {
			date : '01/01/2013',
			object : 'Deposit',
			cashin : "",
			cashout : 300,
			bankin : 300,
			bankout : 5,
			comment : "Deposit costs 5$",
      receipt : "2",
      id: 3
		} ]
	} ];
	
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
			category : category
		});
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
  }
  
	// Computes the cash or bank total for a category
	$scope.total = function(bankOrCash, category) {
		var total = 0;
		angular.forEach($scope.categories[category].transactions, function(
				trans) {
			if (bankOrCash == 'cash')
				total += trans.cashin - trans.cashout;
			else
				total += trans.bankin - trans.bankout;
		});
		return total;
	};
	
	// Computes the cash/bank total for all categories
	$scope.bigTotal = function(bankOrCash) {
		var total = 0;
		for (var i = 0; i < $scope.categories.length; i++) {
			total += $scope.total(bankOrCash, i);
		}
		return total;
	};
	
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
	};
	
	// Sorts the list according to date
	$scope.sortList = function(category) {
		$scope.categories[category].transactions.sort(compareDates);
	};
	
	$scope.formatCurrency = function(value) {
		return currencyFormatter.replace("%", value);
	};
	
  // Make the transactions movable within the categories and from one category to another
  $scope.sortableOptions = {
    connectWith: 'tbody',
    dropOnEmpty: true,
    items: "tr:not(:first)",
    placeholder: "ui-state-highlight",
    start: function(event, ui) {
      // Save predecessor to reset position in DOM
      prev = ui.item.prev();
      // Save category and position
      var categoryElement = ui.item.closest("[data-category-id]");
      categoryIndex = $("[data-category-id").index(categoryElement);
      transactionIndex = ui.item.parent().find("[data-transaction-id]").index(ui.item); // TODO use something else than data-draggable-id
      $scope.sortableData = {
        prev: prev,
        categoryIndex: categoryIndex,
        transactionIndex: transactionIndex,
      };
    },
    stop: function(event, ui) {
      // Get new category and position
      var categoryElement = ui.item.closest("[data-category-id]");
      var categoryIndex = $("[data-category-id").index(categoryElement);
      var transactionIndex = ui.item.parent().find("[data-transaction-id]").index(ui.item); // TODO use something else than data-draggable-id
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
    
});

angular.bootstrap(document, ['myapp']);
