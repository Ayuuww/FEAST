<?php
class PDF_EXTENDED extends FPDF
{
  function Header()
  {
    // Logos
    $logo_left  = 'pics/bagong_pilipinas.jpg';
    $logo_right = 'pics/DMMMSUlogo_header.png';

    // --- Get Department Name ---
    $department = $_SESSION['department'] ?? null;

    // If not found in session, get it from admin_departments
    if (!$department && isset($_SESSION['idnumber'])) {
      global $conn;
      if (!isset($conn)) include 'conn/conn.php';
      $admin_id = $_SESSION['idnumber'];

      $stmt = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ? LIMIT 1");
      $stmt->bind_param("s", $admin_id);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
        $department = $row['department_name'];
      }
      $stmt->close();
    }

    // --- Fetch department info dynamically ---
    $college_name = 'COLLEGE';
    $website = 'www.dmmmsu.edu.ph';
    $phone = '';
    $email = '';

    if ($department) {
      global $conn;
      if (!isset($conn)) include 'conn/conn.php';
      $stmt = $conn->prepare("SELECT department_name, website, phone, email FROM department_info WHERE department_name = ? LIMIT 1");
      $stmt->bind_param("s", $department);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
        $college_name = $row['department_name'];
        $website = $row['website'] ?: $website;
        $phone = $row['phone'] ?: '';
        $email = $row['email'] ?: '';
      }
      $stmt->close();
    }

    // --- Header Layout ---
    $this->Image($logo_left, 9, 10, 32);
    $this->Image($logo_right, 35, 10, 20); // This logo ends at X coordinate 55 (35+20)


    // --- ✅ START OF FIX ---

    // Set a left margin for all text, starting just right of the logos
    $text_x_position = 58;

    // Calculate the remaining width for the text block
    // A4 page (210mm) - 10mm right margin - 58mm left start position = 142mm width
    $text_width = 142;

    // University name
    $this->SetFont('Arial', 'B', 10);
    $this->SetX($text_x_position); // Set X position
    $this->MultiCell($text_width, 7, 'DON MARIANO MARCOS MEMORIAL STATE UNIVERSITY', 0, 'L');

    // Campus name
    $this->SetX($text_x_position); // Set X position
    $this->MultiCell($text_width, 2, 'NORTH LA UNION CAMPUS Bacnotan, La Union, Philippines', 0, 'L');

    // College name
    $this->SetFont('Arial', 'B', 12);
    $this->SetX($text_x_position); // Set X position
    // MultiCell will make long college names wrap to the next line
    $this->MultiCell($text_width, 7, strtoupper($college_name), 0, 'L');

    // Build contact info string to avoid empty separators (e.g., " | | ")
    $contact_parts = [];
    if (!empty($website)) $contact_parts[] = $website;
    if (!empty($phone))   $contact_parts[] = $phone;
    if (!empty($email))   $contact_parts[] = $email;
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

  function GetMultiCellHeight($w, $h, $txt)
  {
    // Use '$this->' instead of '$pdf->'
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 and $s[$nb - 1] == "\n") $nb--;
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
        } else {
          $i = $sep + 1;
        }
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else {
        $i++;
      }
    }
    return $h * $nl;
  }

  function NbLines($w, $txt)
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
    return $nl;
  }
}
