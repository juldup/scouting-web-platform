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
use TCPDF;
use Illuminate\Support\Facades\URL;

/**
 * This class provides method to print the health cards and health card
 * summary in PDF format
 */
class HealthCardPDF {
  
  /**
   * Outputs the given health card in PDF format
   */
  public static function healthCardToPDF(HealthCard $healthCard) {
    $pdf = new HealthCardPDF();
    $pdf->addCard($healthCard);
    $filename = "Fiche sante - " . $healthCard->getMember()->last_name . " " . $healthCard->getMember()->first_name;
    $pdf->outputPDF($filename);
  }
  
  /**
   * Outputs the given list of health cards in PDF format
   */
  public static function healthCardsToPDF($healthCards) {
    $pdf = new HealthCardPDF();
    foreach ($healthCards as $healthCard) {
      $pdf->addCard($healthCard);
    }
    $filename = "Fiches santes";
    $pdf->outputPDF($filename);
  }
  
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
  
  /**
   * Adds a new page with the content of the given card
   */
  protected function addCard(HealthCard $healthCard) {
    $expirationDate = date('Y-m-d', strtotime($healthCard->signature_date) + 365*24*3600);
    $member = $healthCard->getMember();
    // French grammar for masculine/feminine words
    $e = ($member->gender == "F" ? "e" : "");
    $il = ($member->gender == "F" ? "elle" : "il");
    // Create page
    $this->pdf->AddPage();
    $this->pdf->SetAutoPageBreak(true, 10);
    $this->pdf->SetFont('Helvetica','B',16);
    $this->pdf->Cell(100,8,'FICHE SANTÉ INDIVIDUELLE');
    $this->pdf->Ln(8);
    // Print legal stuff
    $this->pdf->SetFont('Times','I',8);
    $this->pdf->MultiCell(190,3,"Cette fiche a été complétée en ligne sur " . URL::route('health_card') . 
            " et doit être mise à jour avec précision au début de chaque année scoute, et avant chaque camp," . 
            " par les parents ou par un médecin. Les informations contenues dans la fiche santé sont confidentielles." .
            " Les animateurs à qui ces informations sont confiées sont tenus de respecter la loi du 8 décembre 1992" .
            " relative à la protection de la vie privée ainsi qu'à la loi du 19 juillet 2006 modifiant celle du 3" .
            " juillet 2005 relative aux droits des volontaires (notion de secret professionnel stipulée dans" .
            " l'article 458 du Code pénal). Les informations communiquées ici ne peuvent donc être divulguées" .
            " si ce n'est au médecin ou tout autre personnel soignant consulté. Conformément à la loi sur le" .
            " traitement des données personnelles, vous pouvez les consulter et les modifier à tout moment." .
            " Ces données seront détruites le " . Helper::dateToHuman($expirationDate) . " si aucun dossier" .
            " n'est ouvert et que la fiche n'est pas resignée entretemps.");
    $this->pdf->Ln(4);
    // Identity section
    $this->pdf->SetFont("Times","B",10);
    $this->pdf->Cell(185, self::$LINE_HEIGHT, "Identité du participant");
    $this->pdf->Ln(self::$INTERLINE);
    $this->entry("Nom : ", 60, $member->last_name);
    $this->entry("Prénom : ", 50, $member->first_name);
    $this->entry("Né$e le : ", 20, $member->getHumanBirthDate());
    $this->pdf->Ln();
    $this->entry("N° registre national : ", 35, $healthCard->national_id);
    $this->entry("Téléphone : ", 35, $member->getPersonalPhone());
    $this->entry("E-mail : ", 60, $member->email_member);
    $this->pdf->Ln();
    $this->entry("Adresse : ", 150, $member->address . "  ;  " . $member->postcode . "  " . $member->city);
    $this->pdf->Ln(self::$INTERLINE);
    // Contact section
    $this->pdf->SetFont("Times", "B", 10);
    $this->pdf->Cell(185, self::$LINE_HEIGHT, "Personnes à contacter en cas d'urgence");
    $this->pdf->Ln(self::$INTERLINE);
    $y = $this->pdf->GetY();
    $this->title("1 : ");
    $this->multiline($healthCard->contact1_name
            . ($healthCard->contact1_relationship ? " (" . $healthCard->contact1_relationship . ")" : "") . "\n"
            . $healthCard->contact1_address . "\n"
            . $healthCard->contact1_phone . "\n"
            . $healthCard->contact1_email
            . ($healthCard->contact1_comment ? "\n" . $healthCard->contact1_comment : ""), 90);
    $y2 = $this->pdf->GetY();
    $this->pdf->SetY($y);
    $this->pdf->SetX(110);
    $this->title("2 : ");
    $this->multiline($healthCard->contact2_name
            . ($healthCard->contact2_relationship ? " (" . $healthCard->contact2_relationship . ")" : "") . "\n"
            . $healthCard->contact2_address . "\n"
            . $healthCard->contact2_phone . "\n"
            . $healthCard->contact2_email
            . ($healthCard->contact2_comment ? "\n" . $healthCard->contact2_comment : ""), 90);
    $this->pdf->SetY(max($this->pdf->GetY(), $y2));
    $this->pdf->Ln(2);
    // Doctor section
    $this->pdf->SetFont("Times","B",10);
    $this->pdf->Cell(35, self::$LINE_HEIGHT, "Médecin traitant :");
    $this->multiline($healthCard->doctor_name . "\n" . $healthCard->doctor_address . "\n" . $healthCard->doctor_phone);
    $this->pdf->Ln(2);
    
    // Health data section
    $this->pdf->SetFont("Times","B",10);
    $this->pdf->Cell(185, self::$LINE_HEIGHT, "Informations confidentielles concernant la santé du participantf");
    $this->pdf->Ln(self::$INTERLINE);
    
    $this->entry("• Taille :", 30, $healthCard->height);
    $this->entry("Poids :", 30, $healthCard->weight);
    $this->entry("Sait-$il nager ?", 
        50, ($healthCard->can_swim == "Difficilement" ||
            $healthCard->can_swim == "Pas du tout" ? "|" : "") . $healthCard->can_swim);
    $this->pdf->Ln();
    $this->pdf->Ln(self::$SMALL_INTERLINE);

    $this->entry("• Peut-$il prendre part aux activités proposées ? (sport, excursions, jeux, baignade...)", 
        15, ($healthCard->has_no_constrained_activities ? "OUI" : "|NON"));
    $this->pdf->Ln();
    if ($healthCard->constrained_activities_details) {
      $this->indent();
      $this->title("Raisons : ");
      $this->multiline("|" . $healthCard->constrained_activities_details, 160);
    }
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->content(2.2, "•");
    $this->titleMultiline("Souffre-t-$il de manière permanente ou régulière de : diabète, asthme, épilepsie, mal des transports, rhumatisme, énurésie nocturne, affection cardiaque, affection cutanée, somnambulisme, handicap mental, handicap moteur, maux de tête / migraines, ...Indiquez la fréquence, la gravité et les actions à mettre en œuvre pour les éviter et/ou y réagir.", 186);
    $this->indent();
    $this->multiline(($healthCard->medical_data ? "|" . $healthCard->medical_data : "(néant)"));
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->titleMultiline("• Quelles sont les autres maladies importantes ou les interventions médicales qu'$il a dû subir ?");
    $this->indent();
    $this->multiline(($healthCard->medical_history ? $healthCard->medical_history : "(néant)"));
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->titleMultiline("• Autres renseignements que vous jugez importants pour le bon déroulement des activités / du camp :");
    $this->indent();
    $this->multiline(($healthCard->other_important_information ? "|" . $healthCard->other_important_information : "(néant)"));
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->entry("• Est-$il en ordre de vaccination contre le tétanos ?", "20", ($healthCard->has_tetanus_vaccine ? "OUI" : "|NON"));
    if ($healthCard->tetanus_vaccine_details) {
      $this->title("Date du dernier rappel : ");
      $this->multiline($healthCard->tetanus_vaccine_details, 60);
    } else $this->pdf->Ln();
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->entry("• Est-$il allergique à certaines substances, aliments ou médicaments ?", "20", ($healthCard->has_allergy ? "|OUI" : "NON"));
    $this->pdf->Ln();
    if ($healthCard->has_allergy || $healthCard->allergy_details || $healthCard->allergy_consequences) {
      $this->indent();
      $this->title("Lesquels : ");
      $this->multiline("|" . $healthCard->allergy_details, 160);
      $this->indent();
      $this->title("Conséquences : ");
      $this->multiline("|" . $healthCard->allergy_consequences, 150);
    }
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->entry("• A-t-$il un régime alimentaire particulier ?", "20", ($healthCard->has_special_diet ? "|OUI" : "NON"));
    $this->pdf->Ln();
    if ($healthCard->special_diet_details) {
      $this->indent();
      $this->title("Lequel ? ");
      $this->multiline("|" . $healthCard->special_diet_details, 165);
    }
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    $this->entry("• Doit-$il prendre des médicaments quotidiennement ?", "20", ($healthCard->has_drugs ? "|OUI" : "NON"));
    $this->pdf->Ln();
    if ($healthCard->has_drugs || $healthCard->drugs_details) {
      $this->indent();
      $this->title("Lesquels, quand et en quelle quantité : ");
      $this->multiline("|" . $healthCard->drugs_details, 120);
      $this->indent();
      $this->entry("Est-$il autonome dans la prise de ces médicaments ? ", 20, ($healthCard->drugs_autonomy ? "OUI" : "|NON"));
      $this->pdf->Ln();
    }
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    // Codiv-19
    $this->entry("• Fait-$il partie d’un groupe à risques du covid-19 ?", 10, ($healthCard->covid_19_risk_group ? "|OUI" : "NON"));
    $this->pdf->Ln();
    if ($healthCard->covid_19_risk_group) {
      $this->indent();
      $this->entry("- A-t-$il reçu un avis favorable de son médecin traitant quant à sa participation aux activités scoutes ?", 10, ($healthCard->covid_19_physician_agreement ? "OUI" : "|NON"));
      $this->pdf->Ln();
      $this->indent();
      $this->entry("- Les coordonnées complètes du médecin traitant ont été renseignées sur cette fiche santé :", 10, ($healthCard->covid_19_physician_agreement ? "OUI" : "|NON"));
      $this->pdf->Ln();
    }
    $this->pdf->Ln(self::$SMALL_INTERLINE);
    
    // Comments section
    if ($healthCard->comments) {
      $this->title("• Commentaires : ");
      $this->multiline("|" . $healthCard->comments, 153);
      $this->pdf->Ln(self::$SMALL_INTERLINE);
    }
    
    // Bottom notice
    $this->pdf->SetFont("Times","B",10);
    $this->pdf->Cell(35, self::$LINE_HEIGHT, "Remarques");
    $this->pdf->Ln(self::$INTERLINE);
    $this->pdf->SetFont("Helvetica", "", "8");
    $this->pdf->MultiCell(185, 3, "Les animateurs disposent d'une boite de premiers soins. Dans" .
            " le cas de situations ponctuelles ou dans l'attente de l'arrivée du médecin, ils" .
            " peuvent administrer les médicaments cités ci-dessous et ce à bon escient :\n");
    $this->pdf->SetFont("Times", "I", "8");
    $this->pdf->MultiCell(185, 3, "paracétamol, antiseptique (de type Chlorhexidine), pommade apaisante (sur avis médical ou du pharmacien).\n");
    $this->pdf->Ln(3);
    
    // Signature
    $this->pdf->SetFont("Times","I" ,"8");
    $this->pdf->MultiCell(185,3, "« Je marque mon accord pour que la prise en charge ou" .
            " les traitements estimés nécessaires soient entrepris durant le séjour de" .
            " mon enfant par le responsable de centre de vacances ou par le service médical" .
            " qui y est associé. J'autorise le médecin local à prendre les décisions qu'il" . 
            " juge urgentes et indispensables pour assurer l'état de santé de l'enfant, même" . 
            " s'il s'agit d'une intervention chirurgicale à défaut de pouvoir être contacté" . 
            " personnellement. »");
    $this->pdf->Ln();
    $this->entry("Date :", 50, Helper::dateToHuman($healthCard->signature_date));
    $this->entry("Signature électronique : ", 60, "\"" . $healthCard->signatory_email . "\"");
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