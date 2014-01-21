<?php

class ListingPDF {
  
  public static function downloadListing($sections, $output = 'pdf') {
    if ($output != "excel") $output = "pdf";
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Determine title part
    if (count($sections) == 1) {
      $delasection = $sections[0]->de_la_section;
      $sectionSlug = $sections[0]->slug;
    } else {
      $delasection = Section::find(1)->de_la_section;
      $sectionSlug = "unite";
    }
    // Set properties
    $objPHPExcel->getProperties()->setCreator("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $objPHPExcel->getProperties()->setLastModifiedBy("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $objPHPExcel->getProperties()->setTitle(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing $delasection");
    $objPHPExcel->getProperties()->setSubject(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing $delasection");
    $objPHPExcel->getProperties()->setDescription("Listing des scouts $delasection");
    // Define styles
    $normalStyle = new PHPExcel_Style();
    $normalStyle->applyFromArray(
      array('borders' => array(  
                    'top'  => array('style' => PHPExcel_Style_Border::BORDER_NONE),
                    'right'    => array('style' => PHPExcel_Style_Border::BORDER_NONE),
                    'left'    => array('style' => PHPExcel_Style_Border::BORDER_NONE),
                    'bottom'    => array('style' => PHPExcel_Style_Border::BORDER_NONE)
                  ),
          'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                  ),
          'font'    => array(
                    'bold'      => false
                  )
         ));
    $titleStyle = new PHPExcel_Style();
    $titleStyle->applyFromArray(
      array('alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  ),
          'font'    => array(
                    'bold'      => true,
                    'size'    => 20
                  )
         ));
    $headerStyle = new PHPExcel_Style();
    $headerStyle->applyFromArray(
      array('borders' => array(  
                    'top'  => array('style' => PHPExcel_Style_Border::BORDER_NONE),
                    'right'    => array('style' => PHPExcel_Style_Border::BORDER_NONE),
                    'left'    => array('style' => PHPExcel_Style_Border::BORDER_NONE),
                    'bottom'    => array('style' => PHPExcel_Style_Border::BORDER_NONE)
                  ),
          'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                  ),
          'font'    => array(
                    'bold'      => true
                  )
         ));
    // Create a sheet for each section
    $currentSheet = 0;
    foreach ($sections as $section) {
      // Create a sheet
      if ($currentSheet != 0) $objPHPExcel->createSheet($currentSheet);
      $objPHPExcel->setActiveSheetIndex($currentSheet);
      $currentSheet++;
      $row = 1;
      $hasSubgroup = Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->where('section_id', '=', $section->id)
              ->whereNotNull('subgroup')
              ->where('subgroup', '!=', '')
              ->first() ? true : false;
      $hasTotem = Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->where('section_id', '=', $section->id)
              ->whereNotNull('totem')
              ->where('totem', '!=', '')
              ->first() ? true : false;
      // Columns
      $titles = array();
      $titles[] = "N°";
      $titles[] = "Nom";
      $titles[] = "Prénom";
      if ($hasTotem) $titles[] = "Totem";
      if ($hasSubgroup) $titles[] = $section->subgroup_name;
      $titles[] = "DDN";
      $titles[] = "Adresse";
      $titles[] = "CP";
      $titles[] = "Localité";
      $titles[] = "Téléphone";
      if (!$hasTotem && !$hasSubgroup) {
        $colSizes = Array(4,25,20,13,40,6,22,17);
      } elseif ($hasSubgroup && !$hasTotem) {
        $colSizes = Array(4,22,18,20,13,30,6,22,17);
      } elseif ($hasSubgroup && $hasTotem) {
        $colSizes = Array(4,18,17,15,15,13,32,6,22,17);
      } elseif ($hasTotem && !$hasSubgroup) {
        $colSizes = Array(4,20,18,15,13,35,6,22,17);
      }
      // Letter of the last column
      $lastColumn = chr(64 + count($titles));
      // Title in the pdf
      if ($output == "pdf") {
        $objPHPExcel->getActiveSheet()->setCellValue("A$row", "Listing " . $section->de_la_section);
        $objPHPExcel->getActiveSheet()->setSharedStyle($titleStyle, "A$row");
        $objPHPExcel->getActiveSheet()->mergeCells("A$row:$lastColumn$row");
        $row++;
        $row++;
      }
      // Column headers
      $letter = 'A';
      foreach ($titles as $title) {
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", "$title");
        $letter++;
      }
      $objPHPExcel->getActiveSheet()->setSharedStyle($headerStyle, "A$row:$lastColumn$row");
      $row++;
      // Get listing
      $members = Member::where('validated', '=', true)
              ->where('is_leader', '=', false)
              ->where('section_id', '=', $section->id)
              ->orderBy('last_name')
              ->orderBy('first_name')
              ->get();
      // Write member rows
      $counter = 1;
      foreach ($members as $member) {
        $letter = 'B';
        // Print row number
        $objPHPExcel->getActiveSheet()->setCellValue("A$row", $counter);
        $counter++;
        // Print column information
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->last_name); $letter++;
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->first_name); $letter++;
        if ($hasTotem) {
          $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->totem); $letter++;
        }
        if ($hasSubgroup) {
          $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->subgroup); $letter++;
        }
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", Helper::dateToHuman($member->birthdate)); $letter++;
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->address); $letter++;
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->postcode); $letter++;
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->city); $letter++;
        $objPHPExcel->getActiveSheet()->setCellValue("$letter$row", $member->getPublicPhone()); $letter++;
        // Set style
        $objPHPExcel->getActiveSheet()->setSharedStyle($normalStyle, "A$row:$lastColumn$row");
        // Increment row
        $row++;
      }
      // Set column widths
      $letter = 'A';
      foreach ($colSizes as $size) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($letter)->setWidth($size);
        $letter++;
      }
      // Set header and footer. When no different headers for odd/even are used, odd header is assumed.
      $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader("");
      $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("");
      // Set page orientation and size
      $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
      // Rename sheet
      $objPHPExcel->getActiveSheet()->setTitle($section->name);
    }
    // Save Excel 2007 file
    if ($output == "pdf") {
      $objPHPExcel->getActiveSheet()->setShowGridLines(false);
      $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
      $objWriter->writeAllSheets();
      //$objWriter->save("documents/listing.pdf");
      header("Content-type: application/pdf");
      header("Content-Transfer-Encoding: Binary");
      header("Content-disposition: attachment; filename=\"listing_$sectionSlug.pdf\"");
      $objWriter->save("php://output");
    } else if ($output == "excel") {
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      header("Content-type: application/vnd.ms-excel");
      header("Content-Transfer-Encoding: Binary");
      header("Content-disposition: attachment; filename=\"listing_$sectionSlug.xls\"");
      $objWriter->save("php://output");
    }
  }
  
}