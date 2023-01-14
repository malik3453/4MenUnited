<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$ID = $_GET['ID'];
$partyReq = mysqli_query($con, "SELECT * FROM weddingpartyattr WHERE customerID=" . $ID);
$partyPerson = "";
$gName = "";
$partyCusID = "";

while ($items = ($partyReq)->fetch_assoc()) {
    $partyCusID = $items['customerID'];
    $partyPerson = $items['individual'];
    $wID = $items['weddingID'];
    $gNameSQL = mysqli_query($con, 'Select groomName from weddingform where weddingID=' . $wID);
    while ($sqlRun = ($gNameSQL)->fetch_assoc()) {
        $gName = $sqlRun['groomName'];
    }
}


$alterationTicket = mysqli_query($con, "select * from realt where customerID = " . $ID);
while ($items = ($alterationTicket)->fetch_assoc()) {
    $altID = $items['realtID'];
    $alt = $items['realt'];
    $cost = $items['cost'];
    $totalCost = $items['totalCost'];
    $tailorD = $items['tailorDate'];
    $pickup = $items['pickUpDate'];
    $altNotes = $items['notes'];
    $tailorID = $items['tailor'];
}
$getTailorName = mysqli_query($con, "Select * from tailor where tailorID like '$tailorID'");
while ($items = ($getTailorName)->fetch_assoc()) {
    $tailor = $items['tailor'];
}

$altTic = mysqli_query($con, "Select * from customerdetails where customerID = " . $ID);
while ($items = ($altTic)->fetch_assoc()) {
    $cname = $items['customerName'];
    $cnumber = $items['customerNumber'];
    $suit = $items['suit'];
    $color = $items['suitColor'];
    $size = $items['suitSize'];
    $pant = $items['pantSize'];
}


$altArray = explode(',', $alt);
$costArray = explode(',', $cost);

require('fpdf184/fpdf.php');
$pdf = new FPDF('p', 'in', array(4, 6));
$pdf->AddPage();
$pdf->SetLeftMargin(0.15);
$pdf->SetAutoPageBreak(false);
$pdf->SetFont('Arial', 'BU', 15);
$pdf->Cell(2.33, -0.25, '4MEN Alteration Request', 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(3.75, 0.25, 'RT' . $altID, 0, 1, 'R');


$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(3, 0.25, 'Pickup Date: ' . $pickup, 0, 0);
$pdf->Cell(0.72, 0.25, 'Tailor Date: ' . $tailorD, 0, 1, 'R');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3, 0.2, 'Name: ' . $cname, 0, 1);
$pdf->Cell(3, 0.2, 'Number: ' . $cnumber, 0, 1);
$pdf->Cell(3, 0.2, 'Items: ' . $suit . " " . $color . " " . " " . $size . " + " . $pant . " Waist", 0, 1);
if ($partyCusID == $ID) {
    $pdf->Cell(3, 0.2, 'Wedding Party: ' . $gName . "'s Party (" . $partyPerson . ')', 0, 1);
}


$num = count($altArray);
$pdf->SetFont('Arial', 'U', 8);

$pdf->Cell(3, 0.175, 'Alteration', 1, 0, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0.7, 0.175, 'Cost', 1, 1, 'C');
for ($x = 0; $x < $num; $x++) {
    $pdf->Cell(3, 0.175, $altArray[$x], 1, 0, 'C');
    $pdf->Cell(0.7, 0.175, $costArray[$x], 1, 1, 'C');
}

$pdf->SetFont('Arial', 'U', 8);
$pdf->Cell(3, 0.175, 'Total Cost', 1, 0, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0.7, 0.175, $totalCost, 1, 1, 'C');
$pdf->SetFont('Arial', 'BI', 10);
if ($tailorID == "") {
    $pdf->Cell(3, 0.4, 'Tailor: ________________________________________ ', 0, 1);

} else {
    $pdf->Cell(3, 0.4, 'Tailor: ___' . $tailor . '____________________ ', 0, 1);

}

$pdf->SetFont('Arial', '', 8);

$pdf->MultiCell(3, 0.15, 'Notes: ' . $altNotes, 0, 'L');

$pdf->SetFont('Arial', 'i', 10);

$pdf->Cell(0, 0.3, '--------------------------------------------Tailor cut here-----------------------------------------', 0, 1, 'C');

$pdf->SetLeftMargin(0.15);
$pdf->SetFont('Arial', 'BU', 15);
$pdf->Cell(2.53, 0.5, '4MEN Alteration Request', 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(3.75, -0.25, 'RT' . $altID, 0, 1, 'R');
$pdf->SetFont('Arial', 'BUI', 10);
$pdf->Cell(0.78, 0.5, 'Tailor Copy', 0, 1, 'R');

$pdf->SetFont('Arial', 'U', 8);

$pdf->Cell(3, 0.175, 'Alteration', 1, 0, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0.7, 0.175, 'Cost', 1, 1, 'C');
for ($x = 0; $x < $num; $x++) {
    $pdf->Cell(3, 0.175, $altArray[$x], 1, 0, 'C');
    $pdf->Cell(0.7, 0.175, $costArray[$x], 1, 1, 'C');
}
$pdf->SetFont('Arial', 'U', 8);
$pdf->Cell(3, 0.175, 'Total Cost', 1, 0, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0.7, 0.175, $totalCost, 1, 1, 'C');
$pdf->MultiCell(3, 0.2, 'Notes: ' . $altNotes, 0, 'L');

$pdf->Output();

?>
