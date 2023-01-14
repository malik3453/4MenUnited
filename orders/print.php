<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$NAME = $_GET['NAME'];
$arrayString = explode('%', $NAME);

$poID = array_pop($arrayString);
$prefix = substr($poID, 2, 3);
for ($x = 0; $x < count($arrayString); $x++) {
    $arrayString[$x] = $prefix . '#' . $arrayString[$x];

}


$getBrand = mysqli_query($con, "Select * from supplier where prefix like  '$prefix'");
while ($rowData = ($getBrand)->fetch_assoc()) {
    $supplier = $rowData['supplier'];
}

$num = count($arrayString);

require('fpdf184/fpdf.php');


$pdf = new FPDF('p', 'mm', array(55, 55));
for ($x = 0; $x < $num; $x++) {
    $pdf->SetMargins(2, 6, 2);
    $getCustomerDetails = mysqli_query($con, "Select * from `$supplier` where poID like '$poID' and orderNumber like '$arrayString[$x]'");
    while ($rowData = ($getCustomerDetails)->fetch_assoc()) {
        $customerName = $rowData['customerName'];
        $items = $rowData['items'];
        $notes = $rowData['notes'];
        $cusID = $rowData['customerID'];
    }

    $pdf->AddPage();
    $num = count($arrayString);
    $pdf->SetFont('times', '', 12);
    $items = 'Items: ' . $items;
    $pdf->MultiCell(0, 4, $customerName, 0, 'l', false);
    $getSection = mysqli_query($con, "select * from section where customerId like '$cusID'");
    while ($rowData = ($getSection)->fetch_assoc()) {
        $section = $rowData['sectionName'];
    }
    if ($notes == 'Walk-in Customer') {
        $pdf->Cell(0, 4, $section, 0, 1, 'l');
    }elseif ($section === "Accessories"){
        $pdf->MultiCell(0, 4, $notes, 0, 'l');
    }
    else {
        $pdf->MultiCell(0, 4, $notes, 0, 'l');
        $pdf->MultiCell(0, 4, $section, 0, 'l');

    }
    $pdf->MultiCell(0, 4, $items, 0, 'l', false);


}
$pdf->Output();


?>