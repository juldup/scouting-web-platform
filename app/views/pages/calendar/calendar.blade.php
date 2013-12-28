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
      <th class='month' colspan="7">
        @for ($i = 4; $i >= 1; $i--)
          <span class='otherMonth'>
            <a href="javascript:getCalendar">Month</a>
          </span>
        @endfor
        {{ $months[$month-1] }} {{ $year }}
        @for ($i = 1; $i <= 4; $i++)
          <span class='otherMonth'>
            <a href="javascript:getCalendar">Month</a>
          </span>
        @endfor
      </th>
    </tr>
    <tr>
      @for ($x = 0; $x <= 6; $x++)
        <th>{{ $days[$x] }}</th>
      @endfor
    </tr>
    <tr>
    
    @for ($x = 0; $x < $blank_days_before; $x++)
      <td></td>
    @endfor
    
    @for ($day = 1; $day <= $days_in_month; $day++)
      
      @if (($day + $blank_days_before - 1) % 7 == 0)
        </tr>
        <tr>
      @endif
      
      <td class='day'>
        <p>{{ $day }}</p>
        @foreach ($events[$day] as $event)
          <p>{{ $event->event }}</p>
        @endforeach
      </td>
      
    @endfor
    
    @for ($x = 0; $x < $blank_days_after; $x++)
      <td></td>
    @endfor
    
    </tr>
  </table>
<!--      // Date en format SQL (YYYY-MM-DD)
      $xDateStr = $curYear . "-" . ($curMonth < 10 ? "0" : "") . $curMonth . "-" . ($curDay < 10 ? "0" : "") . $curDay;
      // Recherche de tous les événements de ce jour (commençant avant ou égal et finissant après ou égal)
      $mysql_result = mysql_query("SELECT * FROM calendrier, sections
        WHERE startDate <= '" . $curYear . "-" . $curMonth . "-" . $curDay . "'
        AND endDate >= '" . $curYear . "-" . $curMonth . "-" . $curDay . "' " .
        ($sectionId == 0 ? "" : "AND (section = '" . formatSQL($section) . "' OR section='Unité') ") .
        "AND (section = nom OR (section='Unité' AND id='1')) " . 
        (canDo("Accéder au coin des animateurs", "Unité") ? "" : "AND type != 'animateurs' AND type != 'toilettes'") . "
        ORDER BY startDate, id", $con);
      // Affichage des événements
      while ($row = mysql_fetch_array($mysql_result)) {
        if ($row['section'] == 'Unité') $row['couleur'] = substr(getParam("Couleur_Texte_Unite"), 1);
        $type = $row['type'];
        echo "<br /><span style='color: #" . $row['couleur'] . "; " .
            "font-weight: bold;"
            . "' title=\"" . formatQuotToApos($row['description']) . 
            "\"><img src='images/event_$type.gif' height='20'> " . ($row['startDate'] == $xDateStr ? $row['event'] : "" . $row['event'] . "") . "</span>";
      }
      echo "</td>";
    }
    
    echo "</tr>";
  echo "</table>";-->

  
  @if (Parameter::get(Parameter::$CALENDAR_DOWNLOADABLE) == "true")
    <p>Vous pouvez télécharger les éphémérides de l'unité&nbsp;: voir <a href='documents.php?section=0'>documents</a>.</p>
  @endif
  
@stop