@extends('base')

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
        <tr class="big-target">
          <td>
            @if ($change['date'] != $currentDate)
              <strong>{{ Helper::dateToHuman($change['date']) }}</strong>
            @endif
          </td>
          <td>
            {{ $change['section']->name }}
          </td>
          <td>
            {{ $change['type'] }}
          </td>
          <td>
            <a href="{{ $change['url'] }}">{{ $change['item'] }}</a>
          </td>
        </tr>
        <?php $currentDate = $change['date']; ?>
      @endforeach
      </table>
    </div>
  </div>
@stop
