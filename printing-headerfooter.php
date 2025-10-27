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
      $stmt = $conn->prepare("SELECT college_name, website, phone, email FROM department_info WHERE department_name = ? LIMIT 1");
      $stmt->bind_param("s", $department);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
        $college_name = $row['college_name'];
        $website = $row['website'] ?: $website;
        $phone = $row['phone'] ?: '';
        $email = $row['email'] ?: '';
      }
      $stmt->close();
    }

    // --- Header Layout ---
    $this->Image($logo_left, 9, 10, 32);
    $this->Image($logo_right, 35, 10, 20);

    $this->SetFont('Arial', 'B', 10);
    $this->Cell(190, 7, 'DON MARIANO MARCOS MEMORIAL STATE UNIVERSITY', 0, 1, 'C');
    $this->Cell(195, 2, 'NORTH LA UNION CAMPUS Bacnotan, La Union, Philippines', 0, 1, 'C');

    $this->SetFont('Arial', 'B', 12);
    $this->Cell(220, 7, strtoupper($college_name), 0, 1, 'C');

    $this->SetFont('Arial', '', 9);
    $this->SetTextColor(0, 0, 255);
    $this->Cell(195, 5, $website . ' | ' . $phone . ' | ' . $email, 0, 1, 'C');
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
}
