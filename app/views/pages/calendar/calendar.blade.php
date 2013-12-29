@extends('base')

@section('content')
  
  @if ($can_edit)
    <div class="row">
      <p class='pull-right management'>
        <a class='button' href='{{ $edit_url }}'>
          Modifier cette page
        </a>
      </p>
    </div>
  @endif
  
  <h1>Calendrier {{ $user->currentSection->de_la_section }}</h1>
  
  <table id="calendar">
    <tr>
      {{-- Month header --}}
      <th class='month' colspan="7">
        
        {{-- Links to the 4 previous months --}}
        @for ($i = 4; $i >= 1; $i--)
          <span class='otherMonth'>
            <a href="{{ URL::route('calendar_month', array('month' => (($month - $i + 11) % 12 + 1), 'year' => ($month - $i <= 0 ? $year - 1 : $year))) }}">
              {{ $months_short[($month - $i + 11) % 12] }} &lt;
            </a>
          </span>
        @endfor
        
        {{ $months[$month-1] }} {{ $year }}
        
        {{-- Links to the 4 next months --}}
        @for ($i = 1; $i <= 4; $i++)
          <span class='otherMonth'>
            <a href="{{ URL::route('calendar_month', array('month' => (($month + $i + 11) % 12 + 1), 'year' => ($month + $i >= 13 ? $year + 1 : $year))) }}">
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
          <p>{{ $day }}</p>
          
          {{-- Events of the day --}}
          @foreach ($events[$day] as $event)
            <p title="{{{ $event->description }}}">{{ $event->event }}</p>
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
  
@stop