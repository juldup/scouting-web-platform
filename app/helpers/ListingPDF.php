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
 * This class provides a function that outputs the members' listing in PDF, CSV or Excel format
 */
class ListingPDF {
  
  /**
   * Outputs the listing for download
   * 
   * @param array $sections  The list of sections to include
   * @param string $output  The output format ('pdf', 'csv' or 'excel')
   * @param boolean $exportPrivateData  Whether the private data must be included in the listing (csv and pdf only)
   * @param boolean $includeScouts  Whether non-leader members are included in the listing
   * @param boolean $includeLeaders  Whether leaders are included in the listing
   * @param boolean $groupBySection  If true, each section will have a separate page/sheet for itself
   */
  public static function downloadListing($sections, $output = 'pdf', $exportPrivateData = false, $includeScouts = true, $includeLeaders = false, $groupBySection = true) {
    $sections = self::reorderSections($sections);
    if (count($sections) == 1) $groupBySection = true;
    if ($output != "excel" && $output != "csv") $output = "pdf";
    if ($output == "pdf" && $exportPrivateData) $exportPrivateData = false;
    $listingExcel = new ListingPDF();
    $listingExcel->doDownloadListing($sections, $output, $exportPrivateData, $includeScouts, $includeLeaders, $groupBySection);
  }
  
  /**
   * Reorders a list of sections by placing the Unit section at the end
   */
  private static function reorderSections($sections) {
    $newSections = array();
    $unit = null;
    foreach ($sections as $section) {
      if ($section->id == 1) {
        $unit = $section;
      } else {
        $newSections[] = $section;
      }
    }
    if ($unit) $newSections[] = $unit;
    return $newSections;
  }
  
  // The output format
  protected $output;
  
  // Whether the private data must be included
  protected $exportPrivateData;
  
  // Whether non-leader members must be included
  protected $includeScouts;
  
  // Whether leader members must be included
  protected $includeLeaders;
  
  // Whether members are grouped by section or mixed all together
  protected $groupBySection;
  
  // Member counter
  protected $memberCounter = 1;
  
  // Array containing the ids of all the selected sections
  protected $sectionIds;
  
  // PDF styles
  protected $normalStyle = null;
  protected $headerStyle = null;
  protected $titleStyle = null;
  
  /**
   * Outputs the listing for download
   */
  protected function doDownloadListing($sections, $output, $exportPrivateData, $includeScouts, $includeLeaders, $groupBySection) {
    $this->output = $output;
    $this->exportPrivateData = $exportPrivateData;
    $this->includeScouts = $includeScouts;
    $this->includeLeaders = $includeLeaders;
    $this->groupBySection = $groupBySection;
    // Create sectionIds array
    $this->sectionIds = array();
    foreach ($sections as $section) {
      $this->sectionIds[] = $section->id;
    }
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
      if (!$this->groupBySection) $sections = array(null);
      foreach ($sections as $section) {
        $this->fillInSheetForSection($excelDocument, $currentSheet, $section, false, $includeScouts, $includeLeaders);
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
      if ($this->groupBySection) {
        foreach ($sections as $section) {
          // Create excel document
          $this->fillInSheetForSection($excelDocument, 0, $section, true, $includeScouts, $includeLeaders);
        }
      } else {
        $this->fillInSheetForSection($excelDocument, 0, null, true, $includeScouts, $includeLeaders);
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
      if (!$groupBySection) {
        $sections = array(null);
      }
      foreach ($sections as $section) {
        // Create excel document
        $excelDocument = $this->createExcelFile("xxx");
        $this->fillInSheetForSection($excelDocument, 0, $section, false, $includeScouts, $includeLeaders);
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
  
  /**
   * Creates a template Excel file
   */
  protected function createExcelFile($delasection) {
    // Create new PHPExcel object
    $excelDocument = new PHPExcel();
    // Set properties
    $excelDocument->getProperties()->setCreator("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setLastModifiedBy("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setTitle(utf8_decode(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing " . (!$this->includeScouts ? "des animateurs " : "") . $delasection));
    $excelDocument->getProperties()->setSubject(utf8_decode(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing " . (!$this->includeScouts ? "des animateurs " : "") . $delasection));
    $excelDocument->getProperties()->setDescription("Listing des " . ($this->includeScouts ? "scouts " : "animateurs ") . $delasection);
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
  
  /**
   * Creates a sheet in the Excel document for a given section
   * 
   * @param type $excelDocument  The document to fill in
   * @param type $sheetIndex  The index of the sheet to create
   * @param type $section  The section
   * @param boolean $csvMode  Whether the output will be CSV
   * @param boolean $includeScouts  Whether non-leader members are included
   * @param boolean $includeLeaders  Whether leaders are included
   */
  protected function fillInSheetForSection($excelDocument, $sheetIndex, $section, $csvMode = false, $includeScouts, $includeLeaders) {
    $sectionIds = $section ? array($section->id) : $this->sectionIds;
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
      if ($row != 1) {
        $row += 2;
      }
    }
    // Check whether this section has subgroups and/or totems
    $hasSubgroup = $csvMode ? true :
      Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->whereIn('section_id', $sectionIds)
              ->whereNotNull('subgroup')
              ->where('subgroup', '!=', '')
              ->first() ? true : false;
    $hasTotem = $csvMode ? true :
      Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->whereIn('section_id', $sectionIds)
              ->whereNotNull('totem')
              ->where('totem', '!=', '')
              ->first() ? true : false;
    $hasQuali = $csvMode ? true :
      Member::where('validated', '=', 1)
              ->where('is_leader', '=', false)
              ->whereIn('section_id', $sectionIds)
              ->whereNotNull('quali')
              ->where('quali', '!=', '')
              ->first() ? true : false;
    // Columns
    $subgroupName = ($csvMode || !$section ? "Sous-groupe" : $section->subgroup_name);
    $titles = array();
    $titles[] = "N°";
    if ($csvMode || (!$this->groupBySection && $this->output != 'pdf')) {
      $titles[] = "Section";
    }
    $titles[] = "Nom";
    $titles[] = "Prénom";
    $titles[] = "Sexe";
    if ($hasTotem) $titles[] = "Totem";
    if ($this->output != 'pdf' && $hasQuali) $titles[] = "Quali";
    if ($hasSubgroup) $titles[] = $subgroupName;
    if ($this->output != 'pdf') $titles[] = "Nationalité";
    $titles[] = "DDN";
    if ($this->output != 'pdf') $titles[] = "Année";
    $titles[] = "Adresse";
    $titles[] = "CP";
    $titles[] = "Localité";
    if (!$this->exportPrivateData) {
      $titles[] = "Téléphone";
    } else {
      $titles[] = "Téléphone 1";
      $titles[] = "Téléphone 2";
      $titles[] = "Téléphone 3";
      $titles[] = "Téléphone personnel";
      $titles[] = "E-mail du scout";
      $titles[] = "E-mail";
    }
    if ($this->output != 'pdf' && $this->exportPrivateData) {
      $titles[] = "Cotisation payée";
      $titles[] = "Fiche santé";
    }
    if ($this->exportPrivateData) {
      $titles[] = "Handicap";
    }
    if ($this->output != 'pdf' && $this->exportPrivateData) $titles[] = "Date d'inscription";
    if ($this->output == 'pdf') {
      // Special column sizes for pdf output
      if (!$hasTotem && !$hasSubgroup) {
        $colSizes = Array(4,25,20,8,13,40,6,22,17);
      } elseif ($hasSubgroup && !$hasTotem) {
        $colSizes = Array(4,22,18,8,20,13,30,6,22,17);
      } elseif ($hasSubgroup && $hasTotem) {
        $colSizes = Array(4,16,16,8,14,15,13,32,6,22,17);
      } elseif ($hasTotem && !$hasSubgroup) {
        $colSizes = Array(4,20,18,8,15,13,35,6,22,17);
      }
    } else {
      $colSizes = array();
      foreach ($titles as $title) {
        if ($title == "N°") $colSizes[] = 4;
        elseif ($title == "Section") $colSizes[] = 10;
        elseif ($title == "Nom") $colSizes[] = 25;
        elseif ($title == "Prénom") $colSizes[] = 20;
        elseif ($title == "Sexe") $colSizes[] = 6;
        elseif ($title == "Nationalité") $colSizes[] = 12;
        elseif ($title == "Totem") $colSizes[] = 15;
        elseif ($title == $subgroupName) $colSizes[] = 15;
        elseif ($title == "DDN") $colSizes[] = 13;
        elseif ($title == "Année") $colSizes[] = 7;
        elseif ($title == "Adresse") $colSizes[] = 40;
        elseif ($title == "CP") $colSizes[] = 6;
        elseif ($title == "Localité") $colSizes[] = 22;
        elseif ($title == "Téléphone") $colSizes[] = 17;
        elseif ($title == "Quali") $colSizes[] = 20;
        elseif ($title == "Téléphone 1" || $title == "Téléphone 2" || $title == "Téléphone 3") $colSizes[] = 25;
        elseif ($title == "Téléphone personnel") $colSizes[] = 17;
        elseif ($title == "E-mail du scout") $colSizes[] = 25;
        elseif ($title == "E-mail") $colSizes[] = 60;
        elseif ($title == "Cotisation payée") $colSizes[] = 16;
        elseif ($title == "Fiche santé") $colSizes[] = 12;
        elseif ($title == "Handicap") $colSizes[] = 50;
        elseif ($title == "Date d'inscription") $colSizes[] = 18;
        else throw new Exception("Unknown column $title");
      }
    }
    // Letter of the last column
    $lastColumn = chr(64 + count($titles));
    // Title in the pdf
    if ($this->output == "pdf") {
      $excelDocument->getActiveSheet()->setCellValue("A$row", "Listing " . (!$includeScouts ? "des animateurs " : "") . ($section ? $section->de_la_section : ""));
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
    $query = Member::where('validated', '=', true)
            ->whereIn('section_id', $sectionIds)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name');
    if (!$includeLeaders || !$includeScouts) {
      $query->where('is_leader', '=', $includeLeaders ? true : false);
    }
    $members = $query->get();
    // Write member rows
    $nowShowingLeaders = !$includeScouts; // Becomes true when passing from scouts to leaders, to leave a blank line
    if (!$csvMode) $this->memberCounter = 1;
    foreach ($members as $member) {
      if (!$nowShowingLeaders && $member->is_leader) {
        $nowShowingLeaders = true;
        $row++;
        if ($this->output == 'pdf') {
          $excelDocument->getActiveSheet()->setCellValue("A$row", "Animateurs");
          $excelDocument->getActiveSheet()->mergeCells("A$row:$lastColumn$row");
          $excelDocument->getActiveSheet()->setSharedStyle($this->headerStyle, "A$row");
          $row++;
        }
      }
      $letter = 'A';
      // Print column information
      foreach ($titles as $title) {
        if ($title == "N°")
          $excelDocument->getActiveSheet()->setCellValue("A$row", $this->memberCounter++);
        elseif ($title == "Section")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->getSection()->name);
        elseif($title == "Nom")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->last_name);
        elseif($title == "Prénom")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->first_name);
        elseif ($title == "Sexe")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->gender);
        elseif ($title == "Nationalité")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->nationality);
        elseif($title == "Totem")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->totem);
        elseif($title == $subgroupName)
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->subgroup);
        elseif($title == "DDN")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", Helper::dateToHuman($member->birth_date));
        elseif ($title == "Année")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->year_in_section);
        elseif($title == "Adresse")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->address);
        elseif($title == "CP")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->postcode);
        elseif($title == "Localité")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->city);
        elseif($title == "Téléphone")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->getPublicPhone());
        elseif($title == "Quali")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->quali);
        elseif($title == "Téléphone 1")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->phone1 . ($member->phone1_owner ? " (" . $member->phone1_owner . ")" : ""));
        elseif($title == "Téléphone 2")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->phone2 . ($member->phone2_owner ? " (" . $member->phone2_owner . ")" : ""));
        elseif($title == "Téléphone 3")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->phone3 . ($member->phone3_owner ? " (" . $member->phone3_owner . ")" : ""));
        elseif($title == "Téléphone personnel")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->phone_member);
        elseif($title == "E-mail du scout")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->email_member);
        elseif($title == "E-mail")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->getAllEmailAddresses(", ", false));
        elseif ($title == "Cotisation payée")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->subscription_paid ? "Oui" : "Non");
        elseif ($title == "Fiche santé") {
          $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $healthCard ? "Oui" : "Non");
        }
        elseif ($title == "Handicap")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", $member->has_handicap ? $member->handicap_details : "");
        elseif ($title == "Date d'inscription")
          $excelDocument->getActiveSheet()->setCellValue("$letter$row", Helper::dateToHuman($member->created_at));
        else throw new Exception("Unknown title " . $title);
        $letter++;
      }
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
    if ($section) {
      $excelDocument->getActiveSheet()->setTitle($section->name);
    }
  }
  
}
