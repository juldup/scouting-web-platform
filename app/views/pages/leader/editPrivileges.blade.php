@extends('base')

@section('title')
  Gestion des privilèges des animateurs
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-privileges.js') }}"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var commitPrivilegeChangesURL = "{{ URL::route('ajax_change_privileges') }}";
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('leaders', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour à la page des animateurs
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Privilèges des animateurs {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @if (count($leaders))
        <table class="table table-striped table-hover">
          <tr>
            <th></th>
            @foreach ($leaders as $leader)
              <th>
                {{{ $leader->leader_name }}}
              </th>
            @endforeach
          </tr>
          @foreach ($privilege_list as $category_name => $category_privileges)
            <tr class="privilege-category">
              <th>
                {{{ $category_name }}}
              </th>
              @foreach ($leaders as $leader)
                <th>
                  <a class="btn-sm btn-default privileges-check-all" href="" data-category="{{{ $category_name }}}" data-leader-id="{{ $leader->id }}" >
                    <span class="glyphicon glyphicon-check"></span>
                  </a>
                  <a class="btn-sm btn-default privileges-uncheck-all" href="" data-category="{{{ $category_name }}}" data-leader-id="{{ $leader->id }}" >
                    <span class="glyphicon glyphicon-unchecked"></span>
                  </a>
                </th>
              @endforeach
            </tr>
            @foreach ($category_privileges as $privilegeData)
              <?php
                $sOrU = $privilegeData['scope'];
                $privilege = $privilegeData['privilege'];
                if ($sOrU == 'S' && $user->currentSection->id != 1) {
                  $delasection = $user->currentSection->de_la_section;
                  $lasection = $user->currentSection->la_section;
                } else {
                  $delasection = "de toute l'unité";
                  $lasection = "toute l'unité";
                }
              ?>
              <tr>
                <td>
                  {{{ str_replace("#lasection", $lasection, str_replace("#delasection", $delasection, $privilege['text'])) }}}
                </td>
                @foreach ($leaders as $leader)
                  <th>
                    <?php $checked = ($sOrU == "U" && $privilege_table[$privilege['id']][$leader->id]['U']['state'])
                            || ($sOrU == "S" && $privilege_table[$privilege['id']][$leader->id]['S']['state']); ?>
                    <?php $disabled = !$privilege_table[$privilege['id']][$leader->id][$sOrU]['can_change'];?>
                      <input class="privilege-checkbox" type="checkbox" @if ($checked) checked @endif @if ($disabled) disabled @endif
                             data-privilege-id="{{ $privilege['id'] }}" data-category="{{{ $category_name }}}" data-scope="{{ $sOrU }}" data-leader-id="{{ $leader->id }}" />
                  </th>
                @endforeach
              </tr>
            @endforeach
          @endforeach
        </table>
      @else
        <p>Il n'y a pas d'animateurs dans cette section.</p>
      @endif
    </div>
  </div>
  
  <div id="pending-commit" style="display: none;"><span class="glyphicon glyphicon-refresh"></span></div>
@stop
