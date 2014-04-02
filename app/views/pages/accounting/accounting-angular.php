



<div ng-controller="AccountingController" class="accounting">
  <div class="row">
    <div class="col-sm-6 text-right">
      <label>Total à l'actif :</label>
    </div>
    <div class="col-sm-4">
      <span ng-class="{true:'positive', false:'negative'}[bigTotal('cash') + bigTotal('bank') >= 0]">
        <strong>{{formatCurrency(bigTotal('cash') + bigTotal('bank'))}}</strong>
      </span>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 text-right">
      <label>Total liquide :</label>
    </div>
    <div class="col-sm-4">
      <span ng-class="{true:'positive', false:'negative'}[bigTotal('cash') >= 0]">
        <strong>{{formatCurrency(bigTotal('cash'))}}</strong>
      </span>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 text-right">
      <label>Total compte bancaire :</label>
    </div>
    <div class="col-sm-4">
      <span ng-class="{true:'positive', false:'negative'}[bigTotal('bank') >= 0]">
        <strong>{{formatCurrency(bigTotal('bank'))}}</strong>
      </span>
    </div>
  </div>
  <div class="vertical-divider"></div>
  <div class="row">
    <div class="col-sm-12">
      <table>
        <thead>
          <tr class="table-header">
            <td class="move-handle"></td>
            <th class="cell-date">Date</th>
            <th class="cell-description">Motif</th>
            <th colspan='2'>Liquide</th>
            <th colspan='2'>Compte en banque</th>
            <th class="cell-description">Commentaire</th>
            <th class="cell-receipt">Reçus</th>
            <td></td>
          </tr>
          <tr class='table-header'>
            <td></td>
            <th></th>
            <th></th>
            <th class='cellin cell-amount'>Rentrées</th>
            <th class='cellout'>Dépenses</th>
            <th class='cellin'>Rentrées</th>
            <th class='cellout'>Dépenses</th>
            <th></th>
            <th></th>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat-start="category in categories">
            <td class="move-handle">
            </td>
            <td colspan="8" class="category-name">
              <input type="text" ng-model="category.name" />
            </td>
          </tr>
          <tr ng-repeat="trans in category.transactions" class="transaction-row">
            <td class="move-handle">
              <span class="glyphicon glyphicon-move"></span>
            </td>
            <td>
              <input type='text' ng-model="trans.date" class="input-date">
            </td>
            <td class="input-wrapper-cell">
              <input type="text" ng-model="trans.object" class="input-description">
            </td>
            <td class="cellin">
              <input type="text" ng-model="trans.cashin" class="input-amount">
              <span class="input-amount-symbol">
                {{ trans.cashin != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cellout">
              <input type="text" ng-model="trans.cashout" class="input-amount">
              <span class="input-amount-symbol">
                {{ trans.cashout != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cellin">
              <input type="text" ng-model="trans.bankin" class="input-amount">
              <span class="input-amount-symbol">
                {{ trans.bankin != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cellout">
              <input type="text" ng-model="trans.bankout" class="input-amount">
              <span class="input-amount-symbol">
                {{ trans.bankout != 0 ? "€" : "" }}
              </span>
            </td>
            <td>
              <input type="text" ng-model="trans.comment" class="input-description">
            </td>
            <td>
              <input type="text" ng-model="trans.receipt" class="input-receipt">
            </td>
            <td class="delete-cell">
              <a href='' ng-click='remove()'><span class="glyphicon glyphicon-remove"></span></a>
            </td>
          </tr>
          <tr>
            <td></td>
            <td colspan="2">
              <a href='' ng-click='addTransaction($index)' title='Ajouter une transaction'>
                <span class="glyphicon glyphicon-plus"></span> Ajouter une transaction
              </a>
            </td>
            <td colspan="2" class="category-subtotal">
              <span ng-model='totalBank' ng-class="{true:'positive', false:'negative'}[total('cash', $index) >= 0]">
                {{formatCurrency(total('cash', $index))}}
              </span>
            </td>
            <td colspan="2" class="category-subtotal">
              <span ng-model='totalBank' ng-class="{true:'positive', false:'negative'}[total('bank', $index) >= 0]">
                {{formatCurrency(total('bank', $index))}}
              </span>
            </td>
            <td colspan="2"></td>
          </tr>
          <tr ng-repeat-end>
            <td></td>
            <td colspan="2">
              <a href='' ng-click='sortList($index)' title='Trier par date'>
                <span class="glyphicon glyphicon-sort-by-attributes"></span> Trier par date
              </a>
            </td>
            <td colspan="2"></td>
          </tr>
          <tr class="add-category-row">
            <td></td>
            <td colspan="2">
              <a href='' ng-click='addCategory()' title='Ajouter une catégorie'>
                <span class="glyphicon glyphicon-plus"></span> Ajouter une catégorie
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>