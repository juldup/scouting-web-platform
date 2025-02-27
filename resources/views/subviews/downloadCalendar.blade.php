<?php
use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;
?>

@if (Parameter::get(Parameter::$CALENDAR_DOWNLOADABLE) == "true")
  <div class="row">
    <div class="col-md-12">
      <a id="download-calendar-button" class="btn-sm btn-default">Télécharger les éphémérides</a>
      <div class="form-horizontal" id="download-calendar-form" style="display: none;">
        <h3>Télécharger les éphémérides</h3>
        <form method="post" action="{{ URL::route('download_calendar') }}">
          @csrf
          <div class="form-group">
            <div class="col-md-12">
              <label>Inclure les éphémérides de :</label>
            </div>
          </div>
          <div class="form-group">
            @foreach ($sectionList as $section)
              <div class="col-xs-7 col-sm-4 col-md-3 text-right">
                <label for="{{ 'section_' . $section->id }}">{{ $section->name }}</label>
                <input type="checkbox" name="{{ 'section_' . $section->id }}" value="1"
                       @if ($user->currentSection->id == 1 || $user->currentSection->id == $section->id) checked @endif />
              </div>
            @endforeach
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label>Inclure les événenements du :</label>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-7 col-sm-4 col-md-3 text-right">
              <label for="semester_1">Premier semestre</label>
              <input name="semester_1" type="checkbox" value="1" checked />
            </div>
            <div class="col-xs-7 col-sm-4 col-md-3 text-right">
              <label for="semester_2">Second semestre</label>
              <input name="semester_2" type="checkbox" value="1"
                     @if ($include_second_semester_by_default) checked @endif />
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-7 col-sm-8 col-md-9 text-right">
              <input type="submit" class="btn btn-primary" value="Télécharger" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
