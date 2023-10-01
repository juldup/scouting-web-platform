@if (Parameter::get(Parameter::$CALENDAR_DOWNLOADABLE) == "true")
  <div class="row">
    <div class="col-md-12">
      <a id="download-calendar-button" class="btn-sm btn-default">Télécharger les éphémérides</a>
      <div class="form-horizontal" id="download-calendar-form" style="display: none;">
        <h3>Télécharger les éphémérides</h3>
        {{ Form::open(array('url' => URL::route('download_calendar'))) }}
          <div class="form-group">
            <div class="col-md-12">
              {{ Form::label(null, 'Inclure les éphémérides de :') }}
            </div>
          </div>
          <div class="form-group">
            @foreach ($sectionList as $section)
              <div class="col-xs-7 col-sm-4 col-md-3 text-right">
                {{ Form::label('section_' . $section->id, $section->name) }}
                {{ Form::checkbox('section_' . $section->id, 1, $user->currentSection->id == 1 || $user->currentSection->id == $section->id) }}
              </div>
            @endforeach
          </div>
          <div class="form-group">
            <div class="col-md-12">
              {{ Form::label(null, 'Inclure les événenements du :') }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-7 col-sm-4 col-md-3 text-right">
              {{ Form::label('semester_1', "Premier semestre") }}
              {{ Form::checkbox('semester_1', 1, true) }}
            </div>
            <div class="col-xs-7 col-sm-4 col-md-3 text-right">
              {{ Form::label('semester_2', "Second semestre") }}
              {{ Form::checkbox('semester_2', 1, $include_second_semester_by_default) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-7 col-sm-8 col-md-9 text-right">
              {{ Form::submit('Télécharger', array('class' => 'btn btn-primary')) }}
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@endif
