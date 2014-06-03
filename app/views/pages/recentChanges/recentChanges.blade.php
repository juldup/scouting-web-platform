@extends('base')
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
?>

@section('title')
  Changements récents
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Changements récents sur le site</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover">
      <?php $currentDate = ""; ?>
      @foreach ($recent_changes as $change)
        @if ($change['date'] != $currentDate && $change != $recent_changes[0])
        <tr><td colspan="4">&nbsp;</td></tr>
        @endif
        <tr class="clickable-no-default">
          <td>
            @if ($change['date'] != $currentDate)
              <strong>{{{ Helper::dateToHuman($change['date']) }}}</strong>
            @endif
          </td>
          <td>
            {{{ $change['section']->name }}}
          </td>
          <td>
            {{{ $change['type'] }}}
          </td>
          <td>
            <a href="{{ $change['url'] }}">{{{ $change['item'] }}}</a>
          </td>
        </tr>
        <?php $currentDate = $change['date']; ?>
      @endforeach
      </table>
      @if (!count($recent_changes))
        <p>Rien de neuf&nbsp;&nbsp;:-(</p>
      @endif
    </div>
  </div>
@stop
