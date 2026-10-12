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

use App\Models\Parameter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * This class provides functions to export data in various format
 */
class UserExporter {
  public static function exportUserList($users) {
    $exporter = new UserExporter();
    $exporter->users = $users;
    $exporter->doExport();
  }

  public $users;
  public $titles;
  public $rows;
  
  private function doExport() {
    $this->prepareData();
    $this->exportXlsx();
  }
  
  private function prepareData() {
    $titles = array();
    $titles[] = "Id";
    $titles[] = "Utilisateur";
    $titles[] = "Email";
    $titles[] = "Vérifié";
    $titles[] = "Last visit";

    $this->titles = $titles;

    $this->rows = array();
    foreach($this->users as $user) {
      $row = array(
        "Id" => $user->id,
        "Utilisateur" => $user->username,
        "Email" => $user->email,
        "Vérifié" => $user->verified ? 'oui' : 'non',
        "Last visit" => date('d/m/Y', $user->last_visit)
      );
      
      $this->rows[] = $row;
    }
  }
  
  private function exportXlsx(): void {
    $excelDocument = $this->createExcelDocument();
    
    $objWriter = new Xlsx($excelDocument);
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"export_utilisateurs.xlsx\"");
    $objWriter->save("php://output");
  }

  private function createExcelDocument(): Spreadsheet {
    $titles = $this->titles;
    $rows = $this->rows;
    
    $excelDocument = new Spreadsheet();
    $excelDocument->getProperties()->setCreator("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setLastModifiedBy("Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME));
    $excelDocument->getProperties()->setTitle(utf8_decode(Parameter::get(Parameter::$UNIT_SHORT_NAME) . " - Utilisateurs"));

    $excelDocument->disconnectWorksheets();

    $sheet = $excelDocument->createSheet();
    $sheet->setTitle("Utilisateurs");
  
    for ($i = 0; $i < sizeof($titles); $i++) {
      $sheet->setCellValue([$i+1, 1], $titles[$i]);
      $sheet->getColumnDimensionByColumn($i+1)->setAutoSize(true);
    }
    
    $j = 1;
    foreach($this->rows as $row) {
      $this->writeRow($row, $sheet, $j++);
    }
  
    $sheet->setSelectedCell('A1');
  
    $tableStyle = new TableStyle();
    $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
    $tableStyle->setShowFirstColumn(false);
    $tableStyle->setShowRowStripes(true);
  
    $table = new Table([1, 1, sizeof($titles), $j], "T_utilisateurs");
    $table->setStyle($tableStyle);
  
    $sheet->addTable($table);

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
}
