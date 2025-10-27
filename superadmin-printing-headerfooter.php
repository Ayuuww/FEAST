<?php
include 'conn/conn.php'; // Make sure path is correct

class PDF_EXTENDED extends FPDF
{
  public $department;
  private $conn;

  function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $conn = null)
  {
    parent::__construct($orientation, $unit, $size);
    $this->conn = $conn;
  }

  function Header()
  {
    // Logos
    $logo_left  = 'pics/bagong_pilipinas.jpg';
    $logo_right = 'pics/DMMMSUlogo_header.png';

    // Default values
    $college_name = 'COLLEGE';
    $college_website = 'www.dmmmsu.edu.ph';
    $college_phone = '';
    $college_email = '';

    // Get department info dynamically
    if ($this->conn && !empty($this->department)) {
      $dept = mysqli_real_escape_string($this->conn, $this->department);
      $query = "SELECT * FROM department_info WHERE department_name = '$dept' LIMIT 1";
      $result = $this->conn->query($query);
      if ($result && $row = $result->fetch_assoc()) {
        $college_name = strtoupper($row['college_name']);
        $college_website = $row['website'] ?: $college_website;
        $college_phone = $row['phone'] ?: $college_phone;
        $college_email = $row['email'] ?: $college_email;
      }
    }

    // Insert logos
    $this->Image($logo_left, 9, 10, 32);
    $this->Image($logo_right, 35, 10, 20);

    // University name
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(190, 7, 'DON MARIANO MARCOS MEMORIAL STATE UNIVERSITY', 0, 1, 'C');
    $this->Cell(195, 2, 'NORTH LA UNION CAMPUS Bacnotan, La Union, Philippines', 0, 1, 'C');

    // College name
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(220, 7, strtoupper($college_name), 0, 1, 'C');

    // Website, phone, email
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
      $max_width = 190;
      $img_width = min($page_width - ($margin * 2), $max_width);
      $x = ($page_width - $img_width) / 2;
      $this->Image($logo, $x, $this->GetY() + 3, $img_width);
    }
  }

  // Helper: Calculate multicell height
  function GetMultiCellHeight($w, $h, $txt)
  {
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;
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
      if ($c == ' ') $sep = $i;
      $l += $cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j) $i++;
        } else $i = $sep + 1;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else $i++;
    }
    return $nl * $h;
  }
}
