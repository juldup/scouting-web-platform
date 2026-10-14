<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

namespace App\Helpers;

use App\Models\HealthCard;
use App\Models\Member;
use App\Models\Parameter;
use App\Models\Section;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * This class provides functions to export data in various format
 */
class ListingExporter {
  const UNIT_SLUG = "unite";
  
  public static function exportListing(
    $sections,
    string $format = 'pdf',
    bool $includePrivateData = false,
    bool $includeScouts = false,
    bool $includeLeaders = false,
    bool $groupBySection = false
  ) {
    $sections = self::reorderSections($sections);
    if (count($sections) == 1) $groupBySection = true;
    if ($format == "pdf") $includePrivateData = false;
    
    $exporter = new ListingExporter();
    $exporter->format = $format;
    $exporter->includePrivateData = $includePrivateData;
    $exporter->includeScouts = $includeScouts;
    $exporter->includeLeaders = $includeLeaders;
    $exporter->groupBySection = $groupBySection;
    
    $exporter->sections = array();
    foreach ($sections as $section) {
      $exporter->sections[$section->slug] = $section;
    }
    
    $exporter->doExport();
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
  
  private static function removeItems(array $source, array $items) {
    foreach ($items as $item) {
      $ndx = array_search($item, $source);
      
      if ($ndx) {
        array_splice($source, $ndx, 1);
      }
    }
    
    return $source;
  }


  public string $format = 'pdf';
  public bool $includePrivateData = false;
  public bool $includeScouts = true;
  public bool $includeLeaders = true;
  public bool $groupBySection = false;
  
  public $sections;
  public $titles;
  public $tables;
  
  private function doExport() {
    $this->createTables();

    switch ($this->format) {
      case 'excel':
        $this->exportXlsx();
        break;

      case 'csv':
        $this->exportCsv();
        break;

      case 'pdf':
        $this->exportPdf();
        break;
    }
  }
  
  private function createTables() {
    $titles = array();
    $titles[] = "N° DESK";
    $titles[] = "Section";
    $titles[] = "Fonction";
    $titles[] = "Nom";
    $titles[] = "Prénom";
    $titles[] = "Sexe";
    $titles[] = "Nationalité";
    $titles[] = "Sous-groupe";
    $titles[] = "Rôle";
    $titles[] = "Totem";
    $titles[] = "Quali";
    $titles[] = "DDN";
    $titles[] = "Année";
    $titles[] = "Adresse";
    $titles[] = "CP";
    $titles[] = "Localité";
    $titles[] = "Téléphone";
    $titles[] = "Téléphone 1";
    $titles[] = "Téléphone 2";
    $titles[] = "Téléphone 3";
    $titles[] = "Téléphone personnel";
    $titles[] = "E-mail du scout";
    $titles[] = "E-mail";
    $titles[] = "Cotisation payée";
    $titles[] = "Fiche santé";
    $titles[] = "Handicap";
    $titles[] = "Date d'inscription";

    if ($this->includePrivateData) {
      $titles = self::removeItems($titles, ["Téléphone"]);
    } else {
      $titles = self::removeItems($titles, [
        "Téléphone 1",
        "Téléphone 2",
        "Téléphone 3",
        "Téléphone personnel",
        "E-mail du scout",
        "E-mail",
        "Cotisation payée",
        "Fiche santé",
        "Handicap",
        "Date d'inscription",
      ]);
    }
    
    if ($this->format == 'pdf') {
      $titles = [
        "N° DESK",
        "Section",
        "Fonction",
        "Nom",
        "Prénom",
        "Sexe",
        "DDN",
        "Adresse",
        "CP",
        "Localité",
        "Téléphone",
      ];
    }

    $this->titles = $titles;
    
    $tables = array();
    foreach ($this->sections as $slug => $section) {
      $rows = $this->createTable($section);

      if (!empty($rows)) {
        if ($this->groupBySection) {
          $tables[$slug] = $rows;
        } else {
          if (!array_key_exists(self::UNIT_SLUG, $tables)) {
            $tables[self::UNIT_SLUG] = array();
          }

          $tables[self::UNIT_SLUG] = array_merge($tables[self::UNIT_SLUG], $rows);
        }
      }
    }
    
    $this->tables = $tables;
  }
  
  private function createTable(Section $section) {
    $query = Member::where('validated', '=', true)
      ->where('is_guest', '=', false)
      ->where('section_id', $section->id)
      ->orderBy('is_leader', 'ASC')
      ->orderBy('last_name')
      ->orderBy('first_name');
    
    if (!$this->includeLeaders || !$this->includeScouts) {
      $query->where('is_leader', '=', $this->includeLeaders);
    }
    
    $members = $query->get();

    $scouts = array();
    $leaders = array();
    
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      
      $fonction = 'Scout';
      if ($member->is_leader) {
        if ($member->getSection()->id == 1) {
          if ($member->leader_in_charge) {
            $fonction = Parameter::adaptAnUDenomination("Animateur d'unité");
          } else {
            $fonction = Parameter::adaptAsUDenomination("Équipier d'unité");
          }
        } else {
          if ($member->leader_in_charge) {
            $fonction = 'Animateur responsable';
          } else {
            $fonction = 'Animateur';
          }
        }
      }
      
      $rawdata = array(
        "N° DESK" => $member->organization_number,
        "Section" => $member->getSection()->name,
        "Fonction" => $fonction,
        "Nom" => $member->last_name,
        "Prénom" => $member->first_name,
        "Sexe" => $member->gender,
        "Nationalité" => $member->nationality,
        "Sous-groupe" => $member->subgroup,
        "Rôle" => $member->role,
        "Totem" => $member->totem,
        "Quali" => $member->quali,
        "DDN" => Helper::dateToHuman($member->birth_date),
        "Année" => $member->year_in_section,
        "Adresse" => $member->address,
        "CP" => $member->postcode,
        "Localité" => $member->city,
        "Téléphone" => $member->getPublicPhone(),
        "Téléphone 1" => $member->phone1 . ($member->phone1_owner ? " (" . $member->phone1_owner . ")" : ""),
        "Téléphone 2" => $member->phone2 . ($member->phone2_owner ? " (" . $member->phone2_owner . ")" : ""),
        "Téléphone 3" => $member->phone3 . ($member->phone3_owner ? " (" . $member->phone3_owner . ")" : ""),
        "Téléphone personnel" => $member->phone_member,
        "E-mail du scout" => $member->email_member,
        "E-mail" => $member->getAllEmailAddresses(", ", false),
        "Cotisation payée" => $member->subscription_paid ? "Oui" : "Non",
        "Fiche santé"  => $healthCard ? "Oui" : "Non",
        "Handicap" => $member->has_handicap ? $member->handicap_details : "",
        "Date d'inscription" => Helper::dateToHuman($member->created_at),
      );
      
      $row = array();
      foreach ($this->titles as $title) {
        if (array_key_exists($title, $rawdata)) {
          $row[$title] = $rawdata[$title];
        }
      }
      
      if ($member->is_leader) {
        $leaders[] = $row;
      } else {
        $scouts[] = $row;
      }
    }
    
    return [ "scouts" => $scouts, "leaders" => $leaders ];
  }

  private function exportXlsx(): void {
    $excelDocument = $this->createExcelDocument();
    $slug = $this->getDocumentSlug();
    
    $objWriter = new Xlsx($excelDocument);
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"listing_$slug.xlsx\"");
    $objWriter->save("php://output");
  }

  private function exportCsv(): void {
    $excelDocument = $this->createExcelDocument();
    $slug = $this->getDocumentSlug();

    $objWriter = new Csv($excelDocument);
    header("Content-type: text/csv");
    header("Content-disposition: attachment; filename=\"listing_$slug.csv\"");
    $objWriter->save("php://output");
  }

  private function exportPdf(): void {
    $html = $this->createHtml();
    $slug = $this->getDocumentSlug();
    
    $dompdf = new Dompdf();
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("listing_$slug.pdf", ['Attachment' => 1]);
  }
  
  
  private function createExcelDocument(): Spreadsheet {
    $titles = $this->titles;
    $tables = $this->tables;

    $excelDocument = new Spreadsheet();
    $excelDocument->getProperties()->setCreator("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setLastModifiedBy("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setTitle(utf8_decode(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Listing"));

    $excelDocument->disconnectWorksheets();

    foreach($tables as $slug => $rows) {
      $section = $this->sections[$slug];

      $sheet = $excelDocument->createSheet();
      $sheet->setTitle($section->name);

      $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
      $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

      for ($i = 0; $i < sizeof($titles); $i++) {
        $sheet->setCellValue([$i+1, 1], $titles[$i]);
        $sheet->getColumnDimensionByColumn($i+1)->setAutoSize(true);
      }
      
      $j = 1;
      foreach($rows['scouts'] as $row) {
        $this->writeRow($row, $sheet, $j++);
      }

      foreach($rows['leaders'] as $row) {
        $this->writeRow($row, $sheet, $j++);
      }

      $sheet->setSelectedCell('A1');

      $tableStyle = new TableStyle();
      $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
      $tableStyle->setShowFirstColumn(false);
      $tableStyle->setShowRowStripes(true);

      $table = new Table([1, 1, sizeof($titles), $j], "T_$section->section_type$section->section_type_number");
      $table->setStyle($tableStyle);

      $sheet->addTable($table);
    }

    $excelDocument->setActiveSheetIndex(0);
    
    return $excelDocument;
  }
  
  private function writeRow($row, $sheet, $j): void {
    for ($i = 0; $i < sizeof($this->titles); $i++) {
      $title = $this->titles[$i];
      $value = array_key_exists($title, $row) ? $row[$title] : '';
      $sheet->setCellValue([$i+1, $j+1], $value);
    }
  }

  private function createHtml() {
    return View::make('members.listing-export', ['data' => $this]);
  }

  private function getDocumentSlug(): string {
    $slug = 'unite';
    if (count($this->tables) == 1) {
      $slug = array_first($this->sections)->slug;
    }

    return $slug;
  }
}
