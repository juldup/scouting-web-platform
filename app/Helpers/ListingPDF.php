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
use App\Models\Member;
use TCPDF;

/**
 * This class provides a function that outputs the members' listing in PDF, CSV or Excel format
 */
class ListingPDF {

  // The output format
  protected $output;
  
  /**
   * Outputs the member pictures in PDF for download
   */
  public static function downloadMemberPictures($sections) {
    ini_set('memory_limit', '1024M');
    $pdf = new TCPDF('P', 'mm', 'A4');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->setJPEGQuality(75);
    // Get members
    $query = Member::where('validated', '=', true)
            ->where(function($query) use ($sections) {
              foreach ($sections as $section) {
                $query->orWhere('section_id', '=', $section->id);
              }
            });
    $query->where('is_leader', '=', false);
    $query->where('is_guest', '=', false);
    $query->orderBy('section_id')
          ->orderBy('last_name')
          ->orderBy('first_name');
    $members = $query->get();
    // Process members
    $currentSection = "";
    $pageCount = 0;
    foreach ($members as $member) {
      if ($member->getSection()->name != $currentSection) {
        // New page with section title
        $pdf->AddPage();
        $pdf->SetXY(0,11);
        $pdf->SetFont('Helvetica','', 18);
        $pdf->MultiCell(210, 1, $member->getSection()->name . " (page 1)", 0, 'C');
        $pdf->SetFont('Helvetica','', 10);
        $currentSection = $member->getSection()->name;
        $count = 0;
        $pageCount = 1;
      } elseif ($count % 20 == 0) {
        $pdf->AddPage();
        $pageCount++;
        $pdf->SetXY(0,11);
        $pdf->SetFont('Helvetica','', 18);
        $pdf->MultiCell(210, 1, $member->getSection()->name . " (page $pageCount)", 0, 'C');
        $pdf->SetFont('Helvetica','', 10);
        $count = 0;
      }
      // Add picture
      $imageURL = ($member->has_picture ? $member->getPicturePath() : resource_path("images/no-picture.jpg"));
      $pdf->Image($imageURL, 12 + ($count % 4) * 48, 30 + floor($count / 4) * 50, 40, 40, 'JPG', 'test a', '', true, 150, '', false, false, 1, false, false, false);
      // Add name
      $pdf->SetXY(0 + ($count % 4) * 48, 25 + floor($count / 4) * 50);
      $pdf->MultiCell(64, 1, $member->getFullName(), 0, 'C');
      $count++;
    }
    // Output pdf
    if (count($sections) == 1) {
      $sectionSlug = $sections[0]->slug;
    } else {
      $sectionSlug = "unite";
    }
    $pdf->Output("Photos $sectionSlug.pdf", "D");
  }
  
}
