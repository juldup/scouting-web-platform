@extends('base')

@section('title')
  Gestion des privilèges des animateurs
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_leaders.js"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
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
      <h1>Privileges des animateurs {{ $user->currentSection->de_la_section }}</h1>
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
                {{ $leader->leader_name }}
              </th>
            @endforeach
          </tr>
          @foreach ($privilege_list as $sOrU => $privilege_sublist)
            <?php
              if ($sOrU == 'S' && $user->currentSection->id != 1) {
                $delasection = $user->currentSection->de_la_section;
                $lasection = $user->currentSection->la_section;
              } else {
                $delasection = "de toute l'unité";
                $lasection = "toute l'unité";
              }
            ?>
            @foreach ($privilege_sublist as $category_name => $category_privileges)
            <tr>
              <th colspan="{{ count($leaders) + 1 }}">
              {{ $category_name }}
              </th>
            </tr>
              @foreach ($category_privileges as $privilege)
                <tr>
                  <td>
                  {{ str_replace("#lasection", $lasection, str_replace("#delasection", $delasection, $privilege['text'])) }}
                  </td>
                  @foreach ($leaders as $leader)
                    <th>
                      <?php $checked = ($privilege_table[$privilege['id']][$leader->id] == "U" || ($sOrU == "S" && $privilege_table[$privilege['id']][$leader->id] == 'S')) ?>
                      <input type="checkbox" @if ($checked) checked @endif />
                    </th>
                  @endforeach
                </tr>
              @endforeach
            @endforeach
          @endforeach
        </table>
      @else
        <p>Il n'y a pas d'animateurs dans cette section.</p>
      @endif
    </div>
  </div>
  
@stop