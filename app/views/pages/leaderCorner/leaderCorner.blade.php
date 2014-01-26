@extends('base')

@section('content')
  <h1>Coin des animateurs</h1>
  
  <div class="vertical-divider"></div>
  
  <div class="row">
    @foreach ($operations as $operationCategory=>$ops)
      <div class="col-md-3">
        <div class="list-group clickable-list-group">
          <div class="list-group-item active">
            {{ $operationCategory }}
          </div>
          @foreach ($ops as $operationName=>$operationData)
            <div class="list-group-item clickable">
              <a href="{{ $operationData['url'] }}"></a>
              {{ $operationName }}
              <a href="#{{ $operationData['help'] }}" class="help-badge"></a>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
  
  @foreach ($help_sections as $help)
    @include('subviews.leaderHelp', array('help' => $help, 'show_title' => true))
  @endforeach
  
@stop