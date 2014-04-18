@extends('base')

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/leader-corner.js"></script>
@stop

@section('content')
  <h1>Coin des animateurs</h1>
  
  <div class="vertical-divider"></div>
  
  <div class="row">
    @foreach ($operations as $operationCategory=>$ops)
      <div class="col-lg-3 col-md-4">
        <div class="list-group clickable-list-group">
          <div class="list-group-item active">
            {{{ $operationCategory }}}
          </div>
          @foreach ($ops as $operationName=>$operationData)
            <div class="list-group-item clickable leader-help-item" data-leader-help="{{ $operationData['help'] }}">
              <a href="{{ $operationData['url'] }}"></a>
              {{{ $operationName }}}
              <a href="#{{ $operationData['help'] }}" class="help-badge"></a>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
  
  @foreach ($help_sections as $help)
    <div class="leader-corner-help" data-leader-help="{{ $help }}" style="display: none;">
      @include('subviews.leaderHelp', array('help' => $help, 'show_title' => true))
    </div>
  @endforeach
  
@stop