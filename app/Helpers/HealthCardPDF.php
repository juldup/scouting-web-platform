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
use App\Models\Section;
use TCPDF;
use Illuminate\Support\Facades\URL;

/**
 * This class provides method to print the health cards and health card
 * summary in PDF format
 */
class HealthCardPDF {
  
  /**
   * Outputs the summary of the given health cards in PDF format
   * 
   * @param type $healthCards
   * @param Section $section
   */
  public static function healthCardsToSummaryPDF($healthCards, Section $section) {
    $pdf = new HealthCardPDF();
    // Init page
    $pdf->pdf->AddPage();
    $pdf->pdf->SetAutoPageBreak(true, 10);
    $pdf->pdf->SetFont('Helvetica','B',16);
    $pdf->pdf->Cell(100, 8, "Résumé des fiches santé " . $section->de_la_section);
    $pdf->pdf->Ln(12);
    $nothingSpecial = "";
    // Add cards' summary
    foreach ($healthCards as $healthCard) {
      $hasSpecialData = $pdf->addCardSummary($healthCard);
      if (!$hasSpecialData) {
        $nothingSpecial .= ($nothingSpecial ? ", " : "") . $healthCard->getMember()->first_name
                . " " . $healthCard->getMember()->last_name;
      }
      $pdf->pdf->Ln(self::$INTERLINE);
    }
    // Print 'nothing special' section
    if ($nothingSpecial) {
      $pdf->pdf->SetFont("Helvetica","B",11);
      $pdf->pdf->Cell(185, 5, "Rien à signaler pour :");
      $pdf->pdf->Ln(self::$INTERLINE);
      $pdf->pdf->SetFont("Helvetica","","11");
      $pdf->multiline($nothingSpecial);
    }
    // Output pdf
    $filename = "Résumé des fiches santes " . $section->de_la_section;
    $pdf->outputPDF($filename);
  }
  
  // Constants
  protected static $LINE_HEIGHT = 4.2;
  protected static $INTERLINE = 6;
  protected static $SMALL_INTERLINE = 1.5;
  
  // The current PDF document
  protected $pdf;
  
  /**
   * (Helper function)
   * Adds a cell with the exact width of the contained text
   */
  protected function cell($str) {
    $this->pdf->Cell($this->pdf->GetStringWidth($str." "), 4, $str);
  }
  
  /**
   * (Helper function)
   * Adds a title cell
   */
  protected function title($str) {
    $this->pdf->SetFont("Helvetica","","10");
    $this->cell($str);
  }
  
  /**
   * (Helper function)
   * Adds a content cell with fixed width
   */
  protected function content($length, $str) {
    // If $str starts with |, this symbol is not displayed but the text is bold and undelined
    $this->pdf->SetFont("Helvetica","I" . (substr($str,0,1) == "|" ? "BU" : ""),"10");
    $this->pdf->Cell($length, self::$LINE_HEIGHT, (substr($str,0,1) == "|" ? substr($str,1) : $str));
  }
  
  /**
   * (Helper function)
   * Adds a couple of title and value
   */
  protected function entry($title, $length, $value) {
    $this->title($title);
    $this->content($length, $value);
  }
  
  /**
   * (Helper function)
   * Adds an entry cell with a title and a fixed width content
   */
  protected function fixedEntry($titleLength, $title, $length, $value) {
    $this->pdf->SetFont("Helvetica", "", "10");
    $this->pdf->Cell($titleLength, self::$LINE_HEIGHT, $title);
    $this->content($length, $value);
  }
  
  /**
   * (Helper function)
   * Adds a multiline title
   */
  protected function titleMultiline($str, $length = 185) {
    $this->pdf->SetFont("Helvetica", "", "10");
    $this->pdf->MultiCell($length, self::$LINE_HEIGHT, $str . "\n");
  }
  
  /**
   * (Helper function)
   * Adds a multiline cell
   */
  protected function multiline($str, $length = 185) {
    // If $str starts with |, this symbol is not displayed but the text is bold and undelined
    $this->pdf->SetFont("Times", "I" . (substr($str,0,1) == "|" ? "BU" : ""), "10");
    $this->pdf->MultiCell($length, self::$LINE_HEIGHT, (substr($str,0,1) == "|" ? substr($str,1) : $str) . "\n");
  }
  
  /**
   * (Helper function)
   * Adds a small horizontal gap to indent the next entry
   */
  protected function indent() {
    $this->pdf->Cell($this->pdf->GetStringWidth("• "), 4, "");
  }
  
  /**
   * (Constructor)
   * Starts a new PDF document
   */
  public function __construct() {
    $this->pdf = new TCPDF('P', 'mm', 'A4');
    $this->pdf->setPrintHeader(false);
    $this->pdf->setPrintFooter(false);
  }
  
  /*
   * Adds data summary and returns whether data has been added or not
   */
  protected function addCardSummary($healthCard) {
    $member = $healthCard->getMember();
    // French grammar for masculine/feminine words
    $e = ($member->gender == "F" ? "e" : "");
    $il = ($member->gender == "F" ? "elle" : "il");
    // Get important data from health card
    $importantData = array();
    if ($healthCard->can_swim == "Difficilement")
      $importantData[] = "Nage difficilement";
    if ($healthCard->can_swim == "Pas du tout")
      $importantData[] = "Ne sait pas nager";
    if (!$healthCard->has_no_constrained_activities) 
      $importantData[] = "Ne peut pas participer à toutes les activités : " . $healthCard->constrained_activities_details;
    if ($healthCard->medical_data)
      $importantData[] = $healthCard->medical_data;
    if ($healthCard->has_allergy) {
      $importantData[] = "Allergies : " . $healthCard->allergy_details;
      if ($healthCard->allergy_consequences)
        $importantData[] = "Conséquences des allergies: " . $healthCard->allergy_consequences;
    }
    if ($healthCard->has_special_diet)
      $importantData[] = "Régime alimentaire : " . $healthCard->special_diet_details;
    if ($healthCard->has_drugs) {
      $preString = ($healthCard->drugs_autonomy ? "Médicaments (autonome) : " : "Médicaments : ");
      $importantData[] = $preString . $healthCard->drugs_details;
    }
    if (!$healthCard->has_tetanus_vaccine)
      $importantData[] = "N'est pas vacciné$e contre le tétanos";
    if ($healthCard->other_important_information)
      $importantData[] = $healthCard->other_important_information;
    if ($healthCard->comments)
      $importantData[] = "Commentaires : " . $healthCard->comments;
    if ($healthCard->covid_19_risk_group) {
      $importantData[] = "Fait partie d'un groupe à risques du covid-19";
      if (!$healthCard->covid_19_physician_agreement)
        $importantData[] = "et n'a pas reçu d'avis favorable du médecin quant à la participation aux activités scoutes";
    }
    // Print important data (if any)
    if (count($importantData)) {
      // Display name
      $this->pdf->SetFont("Times","B",11);
      $this->pdf->Cell(185, 5, $member->getFullName());
      $this->pdf->Ln(self::$INTERLINE);
      // Display important data
      foreach ($importantData as $data) {
        $this->multiline($data);
      }
      // Important data was printed
      return true;
    } else {
      // Nothing was printed
      return false;
    }
  }
  
  /**
   * Outputs the PDF document for download
   */
  protected function outputPDF($filename) {
    $this->pdf->Output($filename . ".pdf", "D");
  }
  
}
