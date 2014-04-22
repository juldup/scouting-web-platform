<div ng-controller="AccountingController" class="accounting">
  <div class="row">
    <div class="col-sm-6 col-xs-8 text-right">
      <label>Total à l'actif :</label>
    </div>
    <div class="col-xs-4">
      <span ng-class="{true:'positive', false:'negative'}[bigTotal('cash') + bigTotal('bank') >= 0]">
        <strong>{{formatCurrency(bigTotal('cash') + bigTotal('bank'))}}</strong>
      </span>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 col-xs-8 text-right">
      <label>Total liquide :</label>
    </div>
    <div class="col-xs-4">
      <span ng-class="{true:'positive', false:'negative'}[bigTotal('cash') >= 0]">
        <strong>{{formatCurrency(bigTotal('cash'))}}</strong>
      </span>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 col-xs-8 text-right">
      <label>Total compte bancaire :</label>
    </div>
    <div class="col-xs-4">
      <span ng-class="{true:'positive', false:'negative'}[bigTotal('bank') >= 0]">
        <strong>{{ formatCurrency(bigTotal('bank')) }}</strong>
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
            <th class='cellout cell-amount'>Dépenses</th>
            <th class='cellin cell-amount'>Rentrées</th>
            <th class='cellout cell-amount'>Dépenses</th>
            <th></th>
            <th></th>
            <td></td>
          </tr>
          <tr class="transaction-row" ng-show="inheritance.cashin || inheritance.cashout || inheritance.bankin || inheritance.bankout">
            <th></th>
            <td colspan="2" class="cell-inheritance">Héritage de l'année {{ previousYear }}</th>
            <td class='cellin cell-amount cell-inheritance'>{{ inheritance.cashin }}</th>
            <td class='cellout cell-amount cell-inheritance'>{{ inheritance.cashout }}</th>
            <td class='cellin cell-amount cell-inheritance'>{{ inheritance.bankin }}</th>
            <td class='cellout cell-amount cell-inheritance'>{{ inheritance.bankout }}</th>
            <td colspan="2" class="cell-inheritance">L'héritage est mis à jour automatiquement</td>
            <th></th>
          </tr>
        </thead>
        <tbody ng-repeat-start="category in categories" data-category-index="{{ $index }}" ui-sortable="sortableOptions">
          <tr>
            <td class="move-handle">
            </td>
            <td colspan="8" class="category-name">
              <input type="text" ng-model="category.name" ng-disabled="!canEdit">
            </td>
          </tr>
          <tr ng-repeat="trans in category.transactions" class="transaction-row" data-transaction-id="{{ trans.id }}">
            <td class="move-handle">
              <span class="glyphicon glyphicon-move" ng-show="canEdit"></span>
            </td>
            <td class="cell-date">
              <input type='text' ng-model="trans.date" class="input-date" ng-disabled="!canEdit">
            </td>
            <td class="input-wrapper-cell cell-description">
              <input type="text" ng-model="trans.object" class="input-description" ng-disabled="!canEdit">
            </td>
            <td class="cellin cell-amount">
              <input type="text" ng-model="trans.cashin" class="input-amount" ng-disabled="!canEdit">
              <span class="input-amount-symbol">
                {{ trans.cashin != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cellout cell-amount">
              <input type="text" ng-model="trans.cashout" class="input-amount" ng-disabled="!canEdit">
              <span class="input-amount-symbol">
                {{ trans.cashout != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cellin cell-amount">
              <input type="text" ng-model="trans.bankin" class="input-amount" ng-disabled="!canEdit">
              <span class="input-amount-symbol">
                {{ trans.bankin != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cellout cell-amount">
              <input type="text" ng-model="trans.bankout" class="input-amount" ng-disabled="!canEdit">
              <span class="input-amount-symbol">
                {{ trans.bankout != 0 ? "€" : "" }}
              </span>
            </td>
            <td class="cell-description">
              <input type="text" ng-model="trans.comment" class="input-description" ng-disabled="!canEdit">
            </td>
            <td class="cell-receipt">
              <input type="text" ng-model="trans.receipt" class="input-receipt" ng-disabled="!canEdit">
            </td>
            <td class="delete-cell">
              <a href='' ng-click='remove()' ng-show="canEdit"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
          </tr>
        </tbody>
        <tbody ng-repeat-end>
          <tr>
            <td></td>
            <td colspan="2">
              <a href='' ng-click='addTransaction($index)' title='Ajouter une transaction' ng-show="canEdit">
                <span class="glyphicon glyphicon-plus"></span> Ajouter une transaction
              </a>
            </td>
            <td colspan="2" class="category-subtotal">
              <span ng-class="{true:'positive', false:'negative'}[total('cash', $index) >= 0]">
                {{formatCurrency(total('cash', $index))}}
              </span>
            </td>
            <td colspan="2" class="category-subtotal">
              <span ng-class="{true:'positive', false:'negative'}[total('bank', $index) >= 0]">
                {{formatCurrency(total('bank', $index))}}
              </span>
            </td>
            <td colspan="2"></td>
          </tr>
          <tr>
            <td></td>
            <td colspan="2">
              <a href='' ng-click='sortList($index)' title='Trier par date' ng-show="canEdit">
                <span class="glyphicon glyphicon-sort-by-attributes"></span> Trier par date
              </a>
            </td>
            <td colspan="4" class="category-subtotal">
              <span ng-class="{true:'positive', false:'negative'}[total('both', $index) >= 0]">
                {{formatCurrency(total('both', $index))}}
              </span>
            </td>
          </tr>
        </tbody>
        <tbody>
          <tr class="add-category-row">
            <td></td>
            <td colspan="2">
              <a href='' ng-click='addCategory()' title='Ajouter une catégorie' ng-show="canEdit">
                <span class="glyphicon glyphicon-plus"></span> Ajouter une catégorie
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
