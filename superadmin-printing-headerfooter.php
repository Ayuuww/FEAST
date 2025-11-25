<?php
include 'conn/conn.php'; // Make sure path is correct

class PDF_EXTENDED extends FPDF
{
  public $college;
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

    // Get college info dynamically
    if ($this->conn && !empty($this->college)) {
      // Use prepared statement to prevent SQL injection
      $stmt = $this->conn->prepare("SELECT * FROM college_info WHERE college_name = ? LIMIT 1");
      $stmt->bind_param("s", $this->college);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result && $row = $result->fetch_assoc()) {
        $college_name = strtoupper($row['college_name']); // Using college_name as requested
        $college_website = $row['website'] ?: $college_website;
        $college_phone = $row['phone'] ?: $college_phone;
        $college_email = $row['email'] ?: $college_email;
      }
      $stmt->close();
    }

    // Insert logos
    $this->Image($logo_left, 9, 10, 32);
    $this->Image($logo_right, 35, 10, 20); // This logo ends at X=55


    // --- ✅ START OF FIX ---

    // Set a left margin for all text, to the right of the logos
    $text_x_position = 58;
    // Calculate remaining width: 210mm (A4) - 10mm (right margin) - 58mm (left start)
    $text_width = 142;

    // University name
    $this->SetFont('Arial', 'B', 10);
    $this->SetX($text_x_position); // Set X position
    // Use MultiCell to allow text to wrap if it's somehow too long
    $this->MultiCell($text_width, 7, 'DON MARIANO MARCOS MEMORIAL STATE UNIVERSITY', 0, 'L');

    // Campus name
    $this->SetX($text_x_position); // Set X position
    $this->MultiCell($text_width, 2, 'NORTH LA UNION CAMPUS Bacnotan, La Union, Philippines', 0, 'L');

    // College name
    $this->SetFont('Arial', 'B', 12);
    $this->SetX($text_x_position); // Set X position
    // Use MultiCell. This will make long names wrap to the next line.
    $this->MultiCell($text_width, 7, strtoupper($college_name), 0, 'L');

    // Contact info
    $contact_parts = [];
    if (!empty($college_website)) $contact_parts[] = $college_website;
    if (!empty($college_phone))   $contact_parts[] = $college_phone;
    if (!empty($college_email))   $contact_parts[] = $college_email;
    $contact_string = implode(' | ', $contact_parts);

    $this->SetFont('Arial', '', 9);
    $this->SetTextColor(0, 0, 255);
    $this->SetX($text_x_position); // Set X position
    $this->MultiCell($text_width, 5, $contact_string, 0, 'L');
    $this->SetTextColor(0, 0, 0);

    // --- ✅ END OF FIX ---

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
