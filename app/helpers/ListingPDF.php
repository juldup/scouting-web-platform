<?php

class ListingPDF {
  
  protected $output;
  protected $exportPrivateData;
  
  protected $normalStyle = null;
  protected $headerStyle = null;
  protected $titleStyle = null;
  
  public static function downloadListing($sections, $output = 'pdf', $exportPrivateData = false) {
    if ($output != "excel" && $output != "csv") $output = "pdf";
    if ($output != "pdf" && $exportPrivateData) $exportPrivateData = false;
    $listingExcel = new ListingPDF();
    $listingExcel->doDownloadListing($sections, $output, $exportPrivateData);
  }
  
  protected function doDownloadListing($sections, $output, $exportPrivateData) {
    $this->output = $output;
    $this->exportPrivateData = $exportPrivateData;
    // Determine title part
    if (count($sections) == 1) {
      $delasection = $sections[0]->de_la_section;
      $sectionSlug = $sections[0]->slug;
    } else {
      $delasection = Section::find(1)->de_la_section;
      $sectionSlug = "unite";
    }
    if ($this->output == 'excel') {
      // Create Excel document
      $excelDocument = $this->createExcelFile($delasection);
      // Create a sheet for each section
      $currentSheet = 0;
      foreach ($sections as $section) {
        $this->fillInSheetForSection($excelDocument, $currentSheet, $section);
        $currentSheet++;
      }
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $excelDocument->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($excelDocument, 'Excel5');
      header("Content-type: application/vnd.ms-excel");
      header("Content-Transfer-Encoding: Binary");
      header("Content-disposition: attachment; filename=\"listing_$sectionSlug.xls\"");
      $objWriter->save("php://output");
    } else if ($this->output == 'csv') {
      $excelDocument = $this->createExcelFile($delasection);
      // Create a sheet for each section
      foreach ($sections as $section) {
        // Create excel document
        $this->fillInSheetForSection($excelDocument, 0, $section, true);
      }
      // Export to csv
      $objWriter = PHPExcel_IOFactory::createWriter($excelDocument, 'CSV');
      header("Content-type: application/text");
      header("Content-disposition: attachment; filename=\"listing_$sectionSlug.csv\"");
      $objWriter->save("php://output");
    } else {
      // Init pdf rendering
      $rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
      $rendererLibraryPath = __DIR__ . '/../../vendor/tecnick.com/tcpdf/';
      if (!PHPExcel_Settings::setPdfRenderer(
              $rendererName,
              $rendererLibraryPath
          )) {
        throw Exception("Une erreur est survenue.");
      }
      // Create pdf document
      $pdfDocument = new FPDI('L', 'pt', 'A4');
      $pdfDocument->setPrintHeader(false);
      $pdfDocument->setPrintFooter(false);
      // Add each section data to the pdf
      foreach ($sections as $section) {
        // Create excel document
        $excelDocument = $this->createExcelFile($section->de_la_section);
        $this->fillInSheetForSection($excelDocument, 0, $section);
        $excelDocument->getActiveSheet()->setShowGridLines(false);
        // Output excel document to a temporary pdf
        $objWriter = new PHPExcel_Writer_PDF($excelDocument);
        $objWriter->writeAllSheets();
        $filename = tempnam(sys_get_temp_dir(), "listing.pdf");
        $objWriter->save($filename);
        // Insert temporary pdf to final pdf document
        $pageCount = $pdfDocument->setSourceFile($filename);
        for ($i = 1; $i <= $pageCount; $i++) {
          $template = $pdfDocument->importPage($i);
          $size = $pdfDocument->getTemplateSize($template);
          $pdfDocument->AddPage('L', array($size['w'], $size['h']));
          $pdfDocument->useTemplate($template);
        }
      }
      // Output pdf
      $pdfDocument->Output("listing_$sectionSlug.pdf", "D");
    }
  }
  
  protected function createExcelFile($delasection) {
    // Create new PHPExcel object
    $excelDocument = new PHPExcel();
    // Set properties
    $excelDocument->getProperties()->setCreator("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setLastModifiedBy("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setTitle(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing $delasection");
    $excelDocument->getProperties()->setSubject(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing $delasection");
    $excelDocument->getProperties()->setDescription("Listing des scouts $delasection");
    // Define styles
    if (!$this->normalStyle) {
      $this->normalStyle = new PHPExcel_Style();
      $this->normalStyle->applyFromArray(
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
      $this->titleStyle = new PHPExcel_Style();
      $this->titleStyle->applyFromArray(
        array('alignment' => array(
                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
            'font'    => array(
                      'bold'      => true,
                      'size'    => 20
                    )
           ));
      $this->headerStyle = new PHPExcel_Style();
      $this->headerStyle->applyFromArray(
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
    }
    return $excelDocument;
  }
  
  protected function fillInSheetForSection($excelDocument, $sheetIndex, $section, $csvMode = false) {
    // Create sheet(s) to match index
    while ($excelDocument->getSheetCount() < $sheetIndex + 1) {
      $excelDocument->createSheet();
    }
    // Select sheet
    $excelDocument->setActiveSheetIndex($sheetIndex);
    // Set row height
    $excelDocument->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
    // Start at first row
    $row = 1;
    if ($csvMode) {
      $row = $excelDocument->getActiveSheet()->getHighestRow();
      if ($row != 1) $row++;
    }
    // Check whether this section has subgroups and/or totems
    $hasSubgroup = $csvMode ? true :
      $hasSubgroup = Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->where('section_id', '=', $section->id)
              ->whereNotNull('subgroup')
              ->where('subgroup', '!=', '')
              ->first() ? true : false;
    $hasTotem = $csvMode ? true :
      Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->where('section_id', '=', $section->id)
              ->whereNotNull('totem')
              ->where('totem', '!=', '')
              ->first() ? true : false;
    // Columns
    $titles = array();
    $titles[] = "N°";
    if ($csvMode) {
      $titles[] = "Section";
    }
    $titles[] = "Nom";
    $titles[] = "Prénom";
    if ($hasTotem) $titles[] = "Totem";
    if ($hasSubgroup) $titles[] = ($csvMode ? "Sous-groupe" : $section->subgroup_name);
    $titles[] = "DDN";
    $titles[] = "Adresse";
    $titles[] = "CP";
    $titles[] = "Localité";
    $titles[] = "Téléphone";
    if ($this->exportPrivateData) {
      // TODO
      $title[] = "Handicap";
      $title[] = "Quali";
    }
    if ($csvMode) {
      $colSizes = Array(4,10,25,20,13,40,6,22,17);
    } elseif (!$hasTotem && !$hasSubgroup) {
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
    if ($this->output == "pdf") {
      $excelDocument->getActiveSheet()->setCellValue("A$row", "Listing " . $section->de_la_section);
      $excelDocument->getActiveSheet()->setSharedStyle($this->titleStyle, "A$row");
      $excelDocument->getActiveSheet()->mergeCells("A$row:$lastColumn$row");
      $row++;
      $row++;
    }
    // Column headers
    if (!$csvMode || $row == 1) {
      $letter = 'A';
      foreach ($titles as $title) {
        $excelDocument->getActiveSheet()->setCellValue("$letter$row", "$title");
        $letter++;
      }
      $excelDocument->getActiveSheet()->setSharedStyle($this->headerStyle, "A$row:$lastColumn$row");
      $row++;
    }
    // Get listing
    $members = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->where('section_id', '=', $section->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    // Write member rows
    $counter = 1;
    if ($csvMode) $counter = $row - 1;
    foreach ($members as $member) {
      $letter = 'B';
      // Print row number
      $excelDocument->getActiveSheet()->setCellValue("A$row", $counter);
      $counter++;
      // Print column information
      if ($csvMode) {
        $excelDocument->getActiveSheet()->setCellValue("$letter$row", $section->name); $letter++;
      }
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->last_name); $letter++;
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->first_name); $letter++;
      if ($hasTotem) {
        $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->totem); $letter++;
      }
      if ($hasSubgroup) {
        $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->subgroup); $letter++;
      }
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", Helper::dateToHuman($member->birthdate)); $letter++;
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->address); $letter++;
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->postcode); $letter++;
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->city); $letter++;
      $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->getPublicPhone()); $letter++;
      // Set style
      $excelDocument->getActiveSheet()->setSharedStyle($this->normalStyle, "A$row:$lastColumn$row");
      // Increment row
      $row++;
    }
    // Set column widths
    $letter = 'A';
    foreach ($colSizes as $size) {
      $excelDocument->getActiveSheet()->getColumnDimension($letter)->setWidth($size);
      $letter++;
    }
    // Set header and footer. When no different headers for odd/even are used, odd header is assumed.
    $excelDocument->getActiveSheet()->getHeaderFooter()->setOddHeader("");
    $excelDocument->getActiveSheet()->getHeaderFooter()->setOddFooter("");
    // Set page orientation and size
    $excelDocument->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $excelDocument->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    // Rename sheet
    $excelDocument->getActiveSheet()->setTitle($section->name);
  }
  
}