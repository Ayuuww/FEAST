<?php

class PDF_EXTENDED extends FPDF
{
  public $department; // <- NEW

  function Header()
  {
    // Logos
    $logo_left  = 'pics/bagong_pilipinas.jpg';
    $logo_right = 'pics/DMMMSUlogo_header.png';

    // Department info
    $dept_info = [
      'CIS' => [
        'college' => 'COLLEGE OF INFORMATION SYSTEMS',
        'website' => 'www.dmmmsu.edu.ph',
        'phone'   => '+63960-8182660',
        'email'   => 'cis.nluc@dmmmsu.edu.ph'
      ],
      'BPED' => [
        'college' => 'COLLEGE OF PHYSICAL EDUCATION',
        'website' => 'www.dmmmsu.edu.ph',
        'phone'   => '+63912-3456789',
        'email'   => 'bped.nluc@dmmmsu.edu.ph'
      ],
      'CVM' => [
        'college' => 'COLLEGE OF VETERINARY MEDICINE',
        'website' => 'www.dmmmsu.edu.ph',
        'phone'   => '+63923-4567890',
        'email'   => 'cvm.nluc@dmmmsu.edu.ph'
      ],
      'CAS' => [
        'college' => 'COLLEGE OF ARTS AND SCIENCE',
        'website' => 'www.dmmmsu.edu.ph',
        'phone'   => '+63923-4567890',
        'email'   => 'cas.nluc@dmmmsu.edu.ph'
      ],
      'CAFF' => [
        'college' => 'COLLEGE OF AGROFORESTRY AND FORESTRY',
        'website' => 'www.dmmmsu.edu.ph',
        'phone'   => '+63923-4567890',
        'email'   => 'caff.nluc@dmmmsu.edu.ph'
      ]
    ];

    // Use faculty’s department instead of session
    $department = $this->department;
    $college_name    = $dept_info[$department]['college'] ?? 'COLLEGE';
    $college_website = $dept_info[$department]['website'] ?? 'www.dmmmsu.edu.ph';
    $college_phone   = $dept_info[$department]['phone'] ?? '';
    $college_email   = $dept_info[$department]['email'] ?? '';

    // Insert logos
    $this->Image($logo_left, 9, 10, 32);
    $this->Image($logo_right, 35, 10, 20);

    // University header text
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(190, 7, 'DON MARIANO MARCOS MEMORIAL STATE UNIVERSITY', 0, 1, 'C');
    $this->Cell(195, 2, 'NORTH LA UNION CAMPUS Bacnotan, La Union, Philippines', 0, 1, 'C');

    // College name
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(173, 7, strtoupper($college_name), 0, 1, 'C');

    // Website | Phone | Email
    $this->SetFont('Arial', '', 9);
    $this->SetTextColor(0, 0, 255);
    $this->Cell(195, 5, $college_website . ' | ' . $college_phone . ' | ' . $college_email, 0, 1, 'C');
    $this->SetTextColor(0, 0, 0);

    $this->Ln(8);
  }

  function Footer()
  {
    $this->SetY(-22);
    $logo = 'pics/footer-header.png';

    if (file_exists($logo)) {
      $page_width = $this->GetPageWidth();
      $margin = 13;

      // Max allowed width (works well for both Portrait & Landscape)
      $max_width = 190; // you can tweak this number

      // Calculate width: either fit to page minus margins OR limit to max_width
      $img_width = min($page_width - ($margin * 2), $max_width);

      // Center horizontally
      $x = ($page_width - $img_width) / 2;

      $this->Image($logo, $x, $this->GetY() + 3, $img_width);
    }
  }

  // Helper function for table cell height
  function GetMultiCellHeight($w, $h, $txt)
  {
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0)
      $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 && $s[$nb - 1] == "\n")
      $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ')
        $sep = $i;
      $l += $cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j)
            $i++;
        } else
          $i = $sep + 1;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else
        $i++;
    }
    return $nl * $h;
  }
}
