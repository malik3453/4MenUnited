<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$NAME = $_GET['NAME'];

$brandCode = substr($NAME, 2, 3);

$sqlBrand = mysqli_query($con, "Select * from supplier where prefix = '$brandCode'");
while ($rowData = ($sqlBrand)->fetch_assoc()) {
    $email = $rowData['email'];
    $supplier = $rowData['supplier'];
}


$getPO = mysqli_query($con, "Select * from purchaseorder where poID like '$NAME'");
while ($rowData = ($getPO)->fetch_assoc()) {
    $poID = $rowData['poID'];
    $status = $rowData['status'];
    $datePlaced = $rowData['datePlaced'];
}


$countOrder = mysqli_query($con, "Select count(orderNumber) as 'orders' from `$supplier` where poID = '$poID'");
while ($rowData = ($countOrder)->fetch_assoc()) {
    $rows = $rowData['orders'];

}

$pages = intdiv($rows, 35) + 1;


require('fpdf184/fpdf.php');


class PDF extends FPDF
{
    function Header()
    {
        global $datePlaced, $poID, $supplier, $email;
        // Title
        $this->Image('../img/logoW.png', 12, 10, 82);
        $this->SetTextColor(98, 42, 42);
        $this->SetFont('times', 'BU', 40);
        $this->Cell(195, 10, 'Purchase Order', 0, 1, 'R');
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('times', '', 15);
        $this->Cell(190, 15, 'Date: ' . $datePlaced, 0, 1, 'R');
        $this->Cell(190, -3, 'PO#: ' . $poID, 0, 1, 'R');
        $this->Cell(190, 15, 'From: 4MenUnited', 0, 1, 'R');
        $this->Cell(190, 0, 'To: ' . $supplier . ' (' . $email . ')', 0, 1, 'R');
        $this->Ln(5);
        $this->SetFont('times', 'BU', 10);

        $this->Cell(45, 8, 'Order Number', 1, 0, 'C');
        $this->Cell(150, 8, 'Items', 1, 1, 'C');
        // Ensure table header is printed
        parent::Header();
    }

    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('times', 'I', 10);
        // Print centered page number
        $this->Cell(0, 0, 'Fill all orders as possible- no backorders', 0, 1, 'C');
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' ' . 'of {nb}', 0, 0, 'C');
    }

}

$pdf = new PDF('p', 'mm', 'letter');
$pdf->AliasNbPages();
$pdf->AddPage();
//    $pdf->SetFont('times','BU',10);
//
//    $pdf ->Cell(45 ,8,'Order Number',1,0,'C');
//    $pdf ->Cell(150 ,8,'Items',1,1,'C');
$pdf->SetFont('times', '', 10);


$allOrders = mysqli_query($con, "Select * from `$supplier` where poID like '$poID'");
$i = 0;
if (mysqli_num_rows($allOrders) > 0) {
    foreach ($allOrders as $items) {
        $i++;
        $pdf->Cell(45, 5, $items['orderNumber'], 1, 0, 'C');
        $pdf->Cell(150, 5, $items['items'], 1, 1, 'C');
    }
}

$pdf->Output($poID . '.pdf', 'I');


?>