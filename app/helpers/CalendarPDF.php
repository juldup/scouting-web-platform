<?php

class CalendarPDF {
  
  protected $firstDateTimestamp;

  
  public static function downloadCalendarFor($sections, $firstSemester, $secondSemester) {
    $calendarPDF = new CalendarPDF();
    $calendarPDF->downloadFor($sections, $firstSemester, $secondSemester);
  }
  
  public function __construct() {
    
  }
  
  protected function dateToHuman($date) {
    $days = array(1=>'Lu', 2=>'Ma', 3=>'Me', 4=>'Je', 5=>'Ve', 6=>'Sa', 7=>'Di');
    $months = array("Janv.", "Fév.", "Mars", "Avril", "Mai", "Juin", "Juill.", "Août", "Sept.", "Oct.", "Nov.", "Déc.");
    return $days[date("N", strtotime($date))] . " " . date("d", strtotime($date)) . " " . $months[date("m", strtotime($date))-1];
  }
  
  public function downloadFor($sections, $firstSemester, $secondSemester) {
    $nbSections = count($sections);
    $year = date('Y');
    $month =  date('m');
    $firstDate = ($firstSemester ? ($month >= 8 ? $year : $year - 1) . "-08-01" : ($month >= 8 ? $year + 1 : $year) . "-01-01");
    $lastDate = (!$secondSemester ? ($month >= 8 ? $year : $year - 1) . "-12-31" : ($month >= 8 ? $year + 1 : $year) . "-07-31");
    $this->firstDateTimestamp = round(strtotime($firstDate) / 60 / 60 / 24);
    
    $calendarItems = CalendarItem::where('start_date', '<=', $lastDate)
            ->where('end_date', '>=', $firstDate)
            ->where(function($query) use ($sections) {
              $query->where('section_id', '=', 1);
              foreach ($sections as $section) {
                $query->orWhere('section_id', '=', $section->id);
              }
            })->get();
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
    
    function compareEvents($a, $b) {
      return strcmp($a['start'], $b['start']);
    }
    
    usort($eventList, "compareEvents");
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
                ($event2['start'] == $event2['end'] ? " le " . $this->dateToHuman($event2['start']) : " du " . $this->dateToHuman($event2['start'])
                . " au " . $this->dateToHuman($event2['end'])) . ")";
            $eventList[$index2]['section'] = null;
          } else if ($event2['start'] <= $event1['start'] && $event2['end'] >= $event1['end'] && $event1['section'] == $event2['section']) {
            // Case 3 : 2 events of the same sections, the first one is happening during the second
            $eventList[$index2]['event'] = $event2['event'] . " (+ " . $event1['event'] . 
                ($event1['start'] == $event1['end'] ? " le " . $this->dateToHuman($event1['start']) : " du " . $this->dateToHuman($event1['start'])
                . " au " . $this->dateToHuman($event1['end'])) . ")";
            $eventList[$index1]['section'] = null;
          } else if ($event1['section'] == 1 && $event2['section'] != 1) {
            // Case 4 : event 1 belongs to Unit, event 2 to another section
            $eventList[$index1]['event'] = $event1['event'] . " (+ " . $event2['sectionName'] . " : " . $event2['event'] . 
                ($event2['start'] == $event2['end'] ? " le " . $this->dateToHuman($event2['start']) : " du " . $this->dateToHuman($event2['start'])
                . " au " . $this->dateToHuman($event2['end'])) . ")";
            $eventList[$index2]['section'] = null;
          } else if ($event2['section'] == 1 && $event1['section'] != 1) {
            // Case 5 : event 1 belongs to another section, event 2 belongs to Unit
            $eventList[$index2]['event'] = $event2['event'] . " (+ " . $event1['sectionName'] . " : " . $event1['event'] . 
                ($event1['start'] == $event1['end'] ? " le " . $this->dateToHuman($event1['start']) : " du " . $this->dateToHuman($event1['start'])
                . " au " . $this->dateToHuman($event1['end'])) . ")";
            $eventList[$index1]['section'] = null;
          } else {
            // Case 6 : any other case
            $eventList[$index1]['event'] = $event1['event'] . " (" . 
                ($event1['start'] == $event1['end'] ? "le " . $this->dateToHuman($event1['start']) : 
                "du " . $this->dateToHuman($event1['start']) . " au " . $this->dateToHuman($event2['end'])) . ") + " . $event2['event'] . " (" .
                ($event2['start'] == $event2['end'] ? "le " . $this->dateToHuman($event2['start']) : 
                "du " . $this->dateToHuman($event2['start']) . " au " . $this->dateToHuman($event2['end'])) . ")";
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
    
    $cellHeight = 14;
    $cellWidth = 538 / ($nbSections+1);
    $margin = 30;
    
    // Repeat while the whole calendar does not fit in one sheet
    while(true) {
      
      // Pass 1: compute positions of rows
      
      $pdf = self::generatePDFWithTitle($month, $year, $sections, $margin, $cellWidth, $cellHeight);
      
      $nextY = $pdf->GetY();
      
      $currentDate = 0;
      $dateStartingPosition = array();
      $dateEndingPosition = array();
      $textHeight = array();
      foreach ($eventList as $index=>$event) {
        if ($currentDate < $event['start']) {
          // Next date, new line
          $currentDate = $event['start'];
          $dateStartingPosition[$currentDate] = $nextY;
          $currentY = $nextY;
          $pdf->SetY($currentY);
          $pdf->SetX($margin);
          $pdf->MultiCell($cellWidth, $cellHeight, $this->dateToHuman($event['start']), 0, '');
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
      $cellHeight--;
    }
    
    // Pass 2: actually write pdf
    
    $pdf = self::generatePDFWithTitle($month, $year, $sections, $margin, $cellWidth, $cellHeight);

    $pdf->setFillColor(255);
    
    // Draw borders and text
    foreach ($usedDates as $date=>$val) {
      $positionY = $dateStartingPosition[$date] + ($dateEndingPosition[$date] - $dateStartingPosition[$date] - $cellHeight) / 2.0;
      $pdf->SetY($positionY);
      $pdf->SetY($positionY);
      $pdf->SetX($margin);
      $pdf->MultiCell($cellWidth, $cellHeight, $this->dateToHuman($date), 0, '', 1);
      $pdf->Line($margin, $dateStartingPosition[$date], $margin + $cellWidth, $dateStartingPosition[$date]);
      foreach ($sections as $section) {
        $calendarItem = CalendarItem::where('start_date', '<', $date)
                ->where('end_date', '>=', $date)
                ->where(function($query) use ($section) {
                  $query->where('section_id', '=', 1);
                  $query->orWhere('section_id', '=', $section->id);
                })->first();
        if (!$calendarItem) {
          $i = $sectionIndex[$section->id];
          $pdf->Line($margin + ($i+1)*$cellWidth, $dateStartingPosition[$date], $margin + ($i+2)*$cellWidth, $dateStartingPosition[$date]);
        }
      }
      $calendarItem = CalendarItem::where('start_date', '<=', $date)
          ->where('end_date', '>=', $date)
          ->where('section_id', '=', 1)->first();
      if ($calendarItem) {
        $pdf->Line($margin , $dateStartingPosition[$date], $margin, $dateEndingPosition[$date]);
        $pdf->Line($margin + $cellWidth, $dateStartingPosition[$date], $margin + $cellWidth, $dateEndingPosition[$date]);
        $pdf->Line($margin + ($nbSections+1)*$cellWidth, $dateStartingPosition[$date], $margin + ($nbSections+1)*$cellWidth, $dateEndingPosition[$date]);
      } else {
        for ($i = 0; $i <= ($nbSections + 1); $i++)  {
          $pdf->Line($margin + $i*$cellWidth, $dateStartingPosition[$date], $margin + $i*$cellWidth, $dateEndingPosition[$date]);
        }
      }
    }
    $pdf->Line($margin, $nextY, $margin + ($nbSections+1) * $cellWidth, $nextY);
    foreach ($eventList as $index=>$event) {
      if ($event['section'] != null) {
        if ($event['section'] != 1) {
          $positionY = $dateStartingPosition[$event['start']] + ($dateEndingPosition[$event['end']] - $dateStartingPosition[$event['start']] - $textHeight[$index]) / 2.0;
          $pdf->SetY($positionY);
          $pdf->SetX($margin + $cellWidth * ($sectionIndex[$event['section']]+1));
          $pdf->MultiCell($cellWidth, $cellHeight, $event['event'], 0, 'C', 0);
        } else {
          $positionY = $dateStartingPosition[$event['start']] + ($dateEndingPosition[$event['end']] - $dateStartingPosition[$event['start']] - $textHeight[$index]) / 2.0;
          $pdf->SetY($positionY);
          $pdf->SetX($margin + $cellWidth);
          $pdf->MultiCell($cellWidth * $nbSections, $cellHeight, $event['event'], 0, 'C', 0);
        }
      }
    }
    $pdf->Output("Ephemerides " . ($month >= 8 ? $year . '-' . ($year+1) : ($year-1) . '-' . $year) . ".pdf", "D");
    
  }
  
  protected static function generatePDFWithTitle($month, $year, $sections, $margin, $cellWidth, $cellHeight) {
    $pdf = new TCPDF('P', 'pt', 'A4');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    $pdf->AddPage();
    $pdf->SetFont('Helvetica','B',25);
    $delasection = "de l'unité";
    if (count($sections) == 1) $delasection = $sections[0]->de_la_section;
    $pdf->MultiCell(538,30,'Éphémérides ' . ($month >= 8 ? $year . '-' . ($year+1) : ($year-1) . '-' . $year) . " " . $delasection, 0, 'C');
    $pdf->Ln(35);
    
    $pdf->SetFont('Helvetica','I',12);
    $pdf->MultiCell(538,11,"Attention, le calendrier est susceptible de changer. Tenez-vous informés.", 0, 'C');
    $pdf->Ln(15);
    
    $pdf->SetFont('Helvetica','',$cellHeight);
    
    // Affiche les noms des sections
    $pdf->SetX($margin + $cellWidth);
    foreach ($sections as $section) {
      $pdf->Cell($cellWidth, $cellHeight, $section->name, 1, 0, 'C');
    }
    $pdf->Ln();
    return $pdf;
  }

}