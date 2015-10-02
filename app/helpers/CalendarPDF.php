<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * This class presents a method that outputs the calendar of one or several sections
 * to PDF
 */
class CalendarPDF {
  
  /**
   * Outputs the calendar in PDF format
   * 
   * @param array $sections  The list of sections to take into account
   * @param boolean $firstSemester  Whether the first semester's events (August-December) have to be incorporated
   * @param boolean $secondSemester  Whether the second semester's events (Januari-July) have to be incorporated
   */
  public static function downloadCalendarFor($sections, $firstSemester, $secondSemester) {
    // Parameters
    $nbSections = count($sections);
    $year = date('Y');
    $month =  date('m');
    $firstDate = ($firstSemester ? ($month >= 8 ? $year : $year - 1) . "-08-01" : ($month >= 8 ? $year + 1 : $year) . "-01-01");
    $lastDate = (!$secondSemester ? ($month >= 8 ? $year : $year - 1) . "-12-31" : ($month >= 8 ? $year + 1 : $year) . "-07-31");
    // Get items to display on the calendar
    $calendarItems = CalendarItem::visibleToAllMembers()
            ->where('start_date', '<=', $lastDate)
            ->where('end_date', '>=', $firstDate)
            ->where(function($query) use ($sections) {
              $query->where('section_id', '=', 1);
              foreach ($sections as $section) {
                $query->orWhere('section_id', '=', $section->id);
              }
            })->get();
    // Create event list and used date list
    $usedDates = array();
    $eventList = array();
    foreach ($calendarItems as $calendarItem) {
      $usedDates[$calendarItem->start_date] = true;
      $usedDates[$calendarItem->end_date] = true;
      $eventList[] = array(
          'start' => $calendarItem->start_date,
          'end' => $calendarItem->end_date, 
          'section' => $calendarItem->section_id,
          'event' => $calendarItem->event,
          'sectionName' => $calendarItem->getSection()->name,
      );
      // Dummy event for end date
      $eventList[] = array(
          'start' => $calendarItem->end_date,
          'end' => $calendarItem->end_date,
          'section' => null,
          'event' => null,
          'sectionName' => null,
      );
    }
    // Sort events per start date
    function compareEvents($a, $b) {
      return strcmp($a['start'], $b['start']);
    }
    usort($eventList, "compareEvents");
    // Sort used dates
    ksort($usedDates);
    // Find conflicts between events
    foreach ($eventList as $index1=>$event1) {
      foreach ($eventList as $index2=>$event2) {
      if ($index1 < $index2 && $event1['section'] != null && $event2['section'] != null &&
        ($event1['section'] == $event2['section'] || $event1['section'] == 1 || $event2['section'] == 1) &&
        ($event1['start'] <= $event2['end'] && $event1['end'] >= $event2['start'])) {
          if ($event1['start'] == $event2['start'] && $event1['end'] == $event2['end'] && $event1['section'] == $event2['section']) {
            // Case 1 : 2 events of the same section at the same time ==> merge them
            $eventList[$index1]['event'] = $event1['event'] . " + " . $event2['event'];
            $eventList[$index2]['section'] = null;
          } else if ($event1['start'] <= $event2['start'] && $event1['end'] >= $event2['end'] && $event1['section'] == $event2['section']) {
            // Case 2 : 2 events of the same section, the second one is happening during the first one
            $eventList[$index1]['event'] = $event1['event'] . " (+ " . $event2['event'] . 
                ($event2['start'] == $event2['end'] ? " le " . self::dateToHuman($event2['start']) : " du " . self::dateToHuman($event2['start'])
                . " au " . self::dateToHuman($event2['end'])) . ")";
            $eventList[$index2]['section'] = null;
          } else if ($event2['start'] <= $event1['start'] && $event2['end'] >= $event1['end'] && $event1['section'] == $event2['section']) {
            // Case 3 : 2 events of the same sections, the first one is happening during the second
            $eventList[$index2]['event'] = $event2['event'] . " (+ " . $event1['event'] . 
                ($event1['start'] == $event1['end'] ? " le " . self::dateToHuman($event1['start']) : " du " . self::dateToHuman($event1['start'])
                . " au " . self::dateToHuman($event1['end'])) . ")";
            $eventList[$index1]['section'] = null;
          } else if ($event1['section'] == 1 && $event2['section'] != 1) {
            // Case 4 : event 1 belongs to Unit, event 2 to another section
            $eventList[$index1]['event'] = $event1['event'] . " (+ " . $event2['sectionName'] . " : " . $event2['event'] . 
                ($event2['start'] == $event2['end'] ? " le " . self::dateToHuman($event2['start']) : " du " . self::dateToHuman($event2['start'])
                . " au " . self::dateToHuman($event2['end'])) . ")";
            $eventList[$index2]['section'] = null;
          } else if ($event2['section'] == 1 && $event1['section'] != 1) {
            // Case 5 : event 1 belongs to another section, event 2 belongs to Unit
            $eventList[$index2]['event'] = $event2['event'] . " (+ " . $event1['sectionName'] . " : " . $event1['event'] . 
                ($event1['start'] == $event1['end'] ? " le " . self::dateToHuman($event1['start']) : " du " . self::dateToHuman($event1['start'])
                . " au " . self::dateToHuman($event1['end'])) . ")";
            $eventList[$index1]['section'] = null;
          } else {
            // Case 6 : any other case
            $eventList[$index1]['event'] = $event1['event'] . " (" . 
                ($event1['start'] == $event1['end'] ? "le " . self::dateToHuman($event1['start']) : 
                "du " . self::dateToHuman($event1['start']) . " au " . self::dateToHuman($event2['end'])) . ") + " . $event2['event'] . " (" .
                ($event2['start'] == $event2['end'] ? "le " . self::dateToHuman($event2['start']) : 
                "du " . self::dateToHuman($event2['start']) . " au " . self::dateToHuman($event2['end'])) . ")";
            $eventList[$index2]['section'] = null;
            if ($event2['start'] < $event1['start']) $eventList[$index1]['start'] = $event2['start'];
            if ($event2['end'] > $event1['end']) $eventList[$index1]['end'] = $event2['end'];
          }
        }
      }
    }
    // Position in the list of sections by id
    $sectionIndex = array(1 => 0);
    $index = 0;
    foreach ($sections as $section) {
      $sectionIndex[$section->id] = $index++;
    }
    // PDF parameters
    $cellHeight = 14;
    $cellWidth = 538 / ($nbSections+1);
    $margin = 30;
    // Repeat while the whole calendar does not fit in one sheet
    while(true) {
      // Pass 1: compute positions of rows
      // Create PDF document with a title and a table header
      $pdf = self::generatePDFWithTitle($month, $year, $sections, $margin, $cellWidth, $cellHeight);
      // Init variables
      $nextY = $pdf->GetY();
      $currentDate = 0;
      $dateStartingPosition = array();
      $dateEndingPosition = array();
      $textHeight = array();
      // Print events on the calendar
      foreach ($eventList as $index=>$event) {
        if ($currentDate < $event['start']) {
          // Next date, new line
          $currentDate = $event['start'];
          $dateStartingPosition[$currentDate] = $nextY;
          $currentY = $nextY;
          $pdf->SetY($currentY);
          $pdf->SetX($margin);
          $pdf->MultiCell($cellWidth, $cellHeight, self::dateToHuman($event['start']), 0, '');
          $nextY = $pdf->GetY();
        }
        // Print event
        if ($event['section'] != null) {
          $pdf->SetY($currentY);
          $pdf->SetX($margin + $cellWidth * ($sectionIndex[$event['section']]+1));
          if ($event['section'] != 1)
            $pdf->MultiCell($cellWidth, $cellHeight, $event['event'], 0, 'C');
          else
            $pdf->MultiCell($cellWidth * $nbSections, $cellHeight, $event['event'], 0, 'C');
          $textHeight[$index] = $pdf->GetY() - $currentY;
          $nextY = max($nextY, $pdf->GetY());
        }
        $dateEndingPosition[$currentDate] = $nextY;
      }
      // Count pages and stop if there is only one page (or if the size is becoming too small)
      if ($pdf->PageNo() == 1 || $cellHeight <= 5) {
        break;
      }
      // There is more than one page, try agin with a smaller font
      $cellHeight--;
    }
    // Pass 2: actually write pdf
    // Generate PDF document with a title and a table header
    $pdf = self::generatePDFWithTitle($month, $year, $sections, $margin, $cellWidth, $cellHeight);
    $pdf->setFillColor(255);
    // Reduce bottom margin (default is 56.7) to avoid page break
    $pdf->SetAutoPageBreak(true, 20);
    // Draw borders and text, date by date
    foreach ($usedDates as $date=>$val) {
      $positionY = $dateStartingPosition[$date] + ($dateEndingPosition[$date] - $dateStartingPosition[$date] - $cellHeight) / 2.0;
      $pdf->SetY($positionY);
      $pdf->SetY($positionY);
      $pdf->SetX($margin);
      // Print date
      $pdf->MultiCell($cellWidth, $cellHeight, self::dateToHuman($date), 0, '', 1);
      // Print date cell top border
      $pdf->Line($margin, $dateStartingPosition[$date], $margin + $cellWidth, $dateStartingPosition[$date]);
      // Print top border of cells
      foreach ($sections as $section) {
        // Get whether there is an item overlapping the current cell
        $calendarItem = CalendarItem::visibleToAllMembers()
                ->where('start_date', '<', $date)
                ->where('end_date', '>=', $date)
                ->where(function($query) use ($section) {
                  $query->where('section_id', '=', 1);
                  $query->orWhere('section_id', '=', $section->id);
                })->first();
        // Draw top border if there is no item overlapping the previous cell and the current cell
        if (!$calendarItem) {
          $i = $sectionIndex[$section->id];
          $pdf->Line($margin + ($i+1)*$cellWidth, $dateStartingPosition[$date], $margin + ($i+2)*$cellWidth, $dateStartingPosition[$date]);
        }
      }
      // Get global (unit) event
      $calendarItem = CalendarItem::visibleToAllMembers()
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date)
              ->where('section_id', '=', 1)->first();
      if ($calendarItem) {
        // Draw left and right borders for an event related to all sections
        // Right border
        $pdf->Line($margin , $dateStartingPosition[$date], $margin, $dateEndingPosition[$date]);
        // Border between date cell and section columns
        $pdf->Line($margin + $cellWidth, $dateStartingPosition[$date], $margin + $cellWidth, $dateEndingPosition[$date]);
        // Left border
        $pdf->Line($margin + ($nbSections+1)*$cellWidth, $dateStartingPosition[$date], $margin + ($nbSections+1)*$cellWidth, $dateEndingPosition[$date]);
      } else {
        // Draw vertical borders if section activities are strictly separated
        for ($i = 0; $i <= ($nbSections + 1); $i++)  {
          $pdf->Line($margin + $i*$cellWidth, $dateStartingPosition[$date], $margin + $i*$cellWidth, $dateEndingPosition[$date]);
        }
      }
    }
    // Draw the bottom border of the table
    $pdf->Line($margin, $nextY, $margin + ($nbSections+1) * $cellWidth, $nextY);
    // Print event names
    foreach ($eventList as $index=>$event) {
      if ($event['section'] != null) {
        if ($event['section'] != 1) {
          // Global event
          $positionY = $dateStartingPosition[$event['start']] + ($dateEndingPosition[$event['end']] - $dateStartingPosition[$event['start']] - $textHeight[$index]) / 2.0;
          $pdf->SetY($positionY);
          $pdf->SetX($margin + $cellWidth * ($sectionIndex[$event['section']]+1));
          $pdf->MultiCell($cellWidth, $cellHeight, $event['event'], 0, 'C', 0);
        } else {
          // Section event
          $positionY = $dateStartingPosition[$event['start']] + ($dateEndingPosition[$event['end']] - $dateStartingPosition[$event['start']] - $textHeight[$index]) / 2.0;
          $pdf->SetY($positionY);
          $pdf->SetX($margin + $cellWidth);
          $pdf->MultiCell($cellWidth * $nbSections, $cellHeight, $event['event'], 0, 'C', 0);
        }
      }
    }
    // Output pdf
    $pdf->Output("Ephemerides " . ($month >= 8 ? $year . '-' . ($year+1) : ($year-1) . '-' . $year) . ".pdf", "D");
  }
  
  /**
   * Transforms a 'YYYY-MM-DD' date in a human-friendly format
   */
  protected static function dateToHuman($date) {
    $days = array(1=>'Lu', 2=>'Ma', 3=>'Me', 4=>'Je', 5=>'Ve', 6=>'Sa', 7=>'Di');
    $months = array("Janv.", "Fév.", "Mars", "Avril", "Mai", "Juin", "Juill.", "Août", "Sept.", "Oct.", "Nov.", "Déc.");
    return $days[date("N", strtotime($date))] . " " . date("d", strtotime($date)) . " " . $months[date("m", strtotime($date))-1];
  }
  
  /**
   * Creates a PDF document with a title and a table header
   * 
   * @param type $month  The current month
   * @param type $year  The current year
   * @param type $sections  The list of sections
   * @param type $margin  The margins' width
   * @param type $cellWidth  The cells' width
   * @param type $cellHeight  The cells' height
   */
  protected static function generatePDFWithTitle($month, $year, $sections, $margin, $cellWidth, $cellHeight) {
    // Create empty PDF document
    $pdf = new TCPDF('P', 'pt', 'A4');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->SetFont('Helvetica','B',25);
    // Print page title
    $delasection = "de l'unité";
    if (count($sections) == 1) $delasection = $sections[0]->de_la_section;
    $pdf->MultiCell(538,30,'Éphémérides ' . ($month >= 8 ? $year . '-' . ($year+1) : ($year-1) . '-' . $year) . " " . $delasection, 0, 'C');
    // Print document date (today)
    $pdf->SetFont('Helvetica','',12);
    $pdf->MultiCell(538,11,"Version du " . Helper::dateToHuman(date('Y-m-d')), 0, 'C');
    $pdf->Ln(30);
    // Print warning message
    $pdf->SetFont('Helvetica','I',12);
    $pdf->MultiCell(538,11,"Attention, le calendrier est susceptible de changer. Tenez-vous informés.", 0, 'C');
    $pdf->Ln(15);
    // Print section headers
    $pdf->SetFont('Helvetica','',$cellHeight);
    $pdf->SetX($margin + $cellWidth);
    foreach ($sections as $section) {
      $pdf->Cell($cellWidth, $cellHeight, $section->name, 1, 0, 'C');
    }
    $pdf->Ln();
    // Return PDF document
    return $pdf;
  }
  
}
