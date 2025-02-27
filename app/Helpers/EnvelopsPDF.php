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

/**
 * This class provides a function that outputs the members' addresses
 * in envelop format
 */
class EnvelopsPDF {
  
  /**
   * Outputs the envelops with the addresses of the members of the specified sections
   * in PDF format
   * 
   * @param array $sections  The list of section to include
   * @param string $format  The envelop format ("c6" (C6) or "c5_6" (C5/6))
   */
  public static function downloadEnvelops($sections, $format) {
    // Parameters
    if ($format == "c6") {
      $width = 162;
      $startX = 62;
      $height = 114;
    } else {
      $width = 229;
      $startX = 100;
      $height = 114;
    }
    $cellWidth = $width - $startX - 10;
    $skipLeaders = false;
    $skipScouts = false;
    // Create pdf document
    $pdf = new TCPDF('L','mm', array($width, $height));
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetFont('Helvetica','', 16);
    // Get members
    $query = Member::where('validated', '=', true)
            ->where(function($query) use ($sections) {
              foreach ($sections as $section) {
                $query->orWhere('section_id', '=', $section->id);
              }
            });
    if ($skipLeaders) $query->where('is_leader', '=', false);
    if ($skipScouts) $query->where('is_leader', '=', true);
    $query->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name');
    $members = $query->get();
    // List of addresses already processed
    $usedAddresses = array();
    // Process members
    foreach ($members as $member) {
      // Skip addresses already added
      $addrShort = strtolower(trim($member->address) . "+" . trim($member->postcode));
      if (!$member->is_leader && in_array($addrShort, $usedAddresses)) {
        // Skip member, they have already been processed through a sibling
        continue;
      }
      if (!$member->is_leader) {
        $usedAddresses[] = $addrShort;
        $siblings = Member::where('validated', '=', true)
                ->where('is_leader', '=', false)
                ->where('address', '=', $member->address)
                ->where('postcode', '=', $member->postcode)
                ->where(function($query) use ($sections) {
                  foreach ($sections as $section) {
                    $query->orWhere('section_id', '=', $section->id);
                  }
                })->orderBy('is_leader', 'ASC')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        $names = array();
        foreach ($siblings as $sibling) {
          if (!isset($names[$sibling->last_name])) $names[$sibling->last_name] = array();
          $names[$sibling->last_name][] = $sibling->first_name;
        }
        $nameList = "";
        foreach ($names as $lastName=>$firstNames) {
          $firstNameList = "";
          $count = 0;
          foreach ($firstNames as $firstName) {
            $count++;
            $firstNameList = $firstName . ($count == 1 ? "" : ($count == 2 ? " et " : ", ")) . $firstNameList;
          }
          $nameList .= ($nameList == "" ? "" : ", ") . $firstNameList . " " . $lastName;
        }
        $nameList = "Parents de " . $nameList;
      } else {
        // Leader
        $nameList = $member->getFullName();
      }
      // Generate envelop
      $pdf->AddPage();
      $pdf->SetXY($startX, $height / 2 - 5);
      $pdf->MultiCell($cellWidth, 1, $nameList, 0, 'L');
      $pdf->Ln(5);
      $pdf->SetX($startX);
      $pdf->MultiCell($cellWidth, 1, $member->address, 0, 'L');
      $pdf->Ln(5);
      $pdf->SetX($startX);
      $pdf->MultiCell($cellWidth, 1, $member->postcode . " " . $member->city, 0, 'L');
    }
    // Output pdf
    if (count($sections) == 1) {
      $sectionSlug = $sections[0]->slug;
    } else {
      $sectionSlug = "unite";
    }
    $pdf->Output("Enveloppes $sectionSlug.pdf", "D");
  }
  
}