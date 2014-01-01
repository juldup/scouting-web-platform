@extends('base')

@section('title')
  Calendrier
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/calendar.js"></script>
  <script>
    var currentMonth = {{ $month }};
    var currentYear = {{ $year }};
    var currentSection = {{ $user->currentSection->id }};
    var events = new Array();
    @foreach ($calendar_items as $item)
      events[{{ $item->id }}] = {
        'start_day': {{ $item->getStartDay() }},
        'start_month': {{ $item->getStartMonth() }},
        'start_year': {{ $item->getStartYear() }},
        'duration': {{ $item->getDuration() }},
        'event_name': "{{ Helper::sanitizeForJavascript($item->event) }}",
        'description': "{{ Helper::sanitizeForJavascript($item->description) }}",
        'type': "{{ $item->type }}",
        'section': {{ $item->section_id }},
        'delete_url': "{{ URL::route('manage_calendar_delete', array('event_id' => $item->id, 'year' => $year, 'month' => $month, 'section_slug' => $user->currentSection->slug)) }}"
      };
    @endforeach
  </script>
@stop

@section('content')
  
  @if ($can_edit)
    @if ($editing)
      <div class="row">
        <p class='pull-right management'>
          <a class='button' href='{{ $page_url }}'>
            Retour à la page
          </a>
        </p>
      </div>
    @else
      <div class="row">
        <p class='pull-right management'>
          <a class='button' href='{{ $edit_url }}'>
            Modifier cette page
          </a>
        </p>
      </div>
    @endif
  @endif
  
  <div class="row">
    <div class="col-lg-12">
  
      <h1>Calendrier {{ $user->currentSection->de_la_section }}</h1>
      <?php if (Session::has('success_message')): ?>
        <p class='alert alert-success'><?php echo Session::get('success_message'); ?></p>
      <?php endif; ?>
      <?php if (Session::has('error_message')): ?>
        <p class='alert alert-danger'><?php echo Session::get('error_message'); ?></p>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      
      @if ($editing)
        <div id="calendar_event_form"
             @if (!Session::has('error_message')) style="display: none;" @endif
             >
          {{ Form::open(array('url' => URL::route('manage_calendar_submit', array('year' => $year, 'month' => $month, 'section_slug' => $user->currentSection->slug)))) }}
            {{ Form::hidden('event_id', 0) }}
            <p>
              Date de début :
              {{ Form::text('start_date_day', '', array('size' => '2')) }} /
              {{ Form::text('start_date_month', '', array('size' => '2')) }} /
              {{ Form::text('start_date_year', '', array('size' => '2')) }}
            </p>
            <p>
              Durée (jours) :
              {{ Form::text('duration_in_days', '', array('size' => '2')) }}
            <span>(compte le premier et le dernier jour de l'activité)</span>
            </p>
            <p>
              Activité :
              {{ Form::text('event_name', '', array('size' => '35', 'placeholder' => "Nom de l'activité")) }}
            </p>
            <p>
              Description :
              {{ Form::textarea('description', '', array('cols' => '35', 'rows' => 3, 'placeholder' => "Description, horaire, infos pratiques")) }}
            </p>
            <p>
              Type d'événement :
              {{ Form::select('event_type', $event_types) }}
            </p>
            <p>
              Section :
              {{ Form::select('section', $sections, $user->currentSection->id) }}
            </p>
            <p>
              {{ Form::submit('Enregistrer') }}
              <a id='delete_link' href="">Supprimer</a>
              <a href="javascript:dismissEvent()">Fermer</a>
            </p>

          {{ Form::close() }}
        </div>
      @endif
      
      @if ($year != $today_year || $month != $today_month)
        <a href="{{ URL::route($route_month, array('month' => $today_month, 'year' => $today_year, "section_slug" => $user->currentSection->slug)) }}">
          Retour à aujourd'hui
        </a>
      @endif
      
      <table id="calendar">
        <tr>
          {{-- Month header --}}
          <th class='month' colspan="7">

            {{-- Links to the 4 previous months --}}
            @for ($i = 4; $i >= 1; $i--)
              <span class='otherMonth'>
                <a href="{{ URL::route($route_month, array('month' => (($month - $i + 11) % 12 + 1), 'year' => ($month - $i <= 0 ? $year - 1 : $year))) }}">
                  {{ $months_short[($month - $i + 11) % 12] }} &lt;
                </a>
              </span>
            @endfor

            {{ $months[$month-1] }} {{ $year }}

            {{-- Links to the 4 next months --}}
            @for ($i = 1; $i <= 4; $i++)
              <span class='otherMonth'>
                <a href="{{ URL::route($route_month, array('month' => (($month + $i + 11) % 12 + 1), 'year' => ($month + $i >= 13 ? $year + 1 : $year))) }}">
                  &gt; {{ $months_short[($month + $i - 1) % 12] }}
                </a>
              </span>
            @endfor
          </th>
        </tr>
        <tr>
          {{-- Names of the days --}}
          @for ($x = 0; $x <= 6; $x++)
            <th>{{ $days[$x] }}</th>
          @endfor
        </tr>
        <tr>

          {{-- Blank days at the beginning of the month --}}
          @for ($x = 0; $x < $blank_days_before; $x++)
            <td></td>
          @endfor

          {{-- Days of the month --}}
          @for ($day = 1; $day <= $days_in_month; $day++)

            {{-- Next row at the end of the week --}}
            @if (($day + $blank_days_before - 1) % 7 == 0)
              </tr>
              <tr>
            @endif

            {{-- Day --}}
            <td class='day'>

              {{-- Number of the day --}}
              @if ($editing)
                <p><a href='javascript:addEvent({{ $day }})'>{{ $day }}</a></p>
              @else
                <p>{{ $day }}</p>
              @endif

              {{-- Events of the day --}}
              @foreach ($events[$day] as $event)
              @if ($editing) <a href="javascript:editEvent({{ $event->id }})"> @endif
              <p title="{{{ $event->description }}}" style="color: {{$event->getSection()->color}};">
                <img src="{{ $event->getIcon() }}" />
                {{ $event->event }}
              </p>
              @if ($editing) </a> @endif
              @endforeach
            </td>

          @endfor

          @for ($x = 0; $x < $blank_days_after; $x++)
            <td></td>
          @endfor

        </tr>
      </table>

      @if (Parameter::get(Parameter::$CALENDAR_DOWNLOADABLE) == "true")
        <p>Vous pouvez télécharger les éphémérides de l'unité&nbsp;: voir
          <a href='{{ URL::route('documents', array('section_slug' => 'unite')) }}'>documents</a>.
        </p>
      @endif
      
    </div>
  </div>
  
@stop