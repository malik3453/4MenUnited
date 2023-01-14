<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$ID = $_GET['ID'];

require('fpdf184/fpdf.php');


$getSection = mysqli_query($con, "select * from section where customerId like '$ID'");
while ($rowData = ($getSection)->fetch_assoc()) {
    $section = $rowData['sectionName'];
    $sectionID = $rowData['sectionID'];
    $items = $rowData['items'];
    $customerName = $rowData['customerName'];
}
$pdf = new FPDF('p', 'mm', array(55, 55));
$pdf->SetMargins(2, 6, 2);
$pdf->AddPage();
$pdf->SetFont('times', '', 12);
$items = 'Items: ' . $items;
$pdf->MultiCell(0, 4, $customerName, 0, 'l', false);

if ($section === 'Hold') {
    $pdf->MultiCell(0, 4, $section, 0, 'l');
} else {
    $getSectionParty = mysqli_query($con, "select * from section join weddingpartyattr w on section.customerId = w.customerID join weddingform w2 on w.weddingID = w2.weddingID where section.customerId  like '$ID'");
    while ($rowData = ($getSectionParty)->fetch_assoc()) {
        $party = $rowData['individual'] . ' - ' . $rowData['groomName'] . "'s Party";
    }
    $pdf->MultiCell(0, 4, $party, 0, 'l');
    $pdf->MultiCell(0, 4, $section, 0, 'l');
}
$pdf->MultiCell(0, 4, $items, 0, 'l', false);
$pdf->Output();
?>