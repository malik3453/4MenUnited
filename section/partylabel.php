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

$getCustomerDetails = mysqli_query($con, "Select * from customerdetails join weddingpartyattr w on customerdetails.customerID = w.customerID join weddingform w2 on w.weddingID = w2.weddingID join section s on customerdetails.customerID = s.customerId where w.weddingID=" . $ID);

$pdf = new FPDF('p', 'mm', array(55, 55));
if (mysqli_num_rows($getCustomerDetails) > 0) {
    foreach ($getCustomerDetails as $rowData) {
        $pdf->SetMargins(2, 6, 2);
        $customerName = $rowData['customerName'];
        $cusID = $rowData['customerID'];
        $customerNumber = $rowData['customerNumber'];
        $Style = $rowData['suitColor'];
        $SuitSize = $rowData['suitSize'];
        $suitFit = $rowData['suitFit'];
        $type = $rowData['type'];
        $PantSize = $rowData['pantSize'];
        $vest = $rowData['vest'];
        $groomName = $rowData['groomName'];
        $individual = $rowData['individual'];
        $notes = $rowData['individual'] . ' - ' . $rowData['groomName'] . "'s Party";
        $section = $rowData['section'];

        $item = $Style . " " . $SuitSize . " " . $suitFit;

        switch ($type) {
            case "Suit":
                if ($PantSize === "") {
                    $item = $item . " + " . ((int)$SuitSize - 7) . ' Waist ';
                } else {
                    $item = $item . " + " . $PantSize . " " . 'Waist ';

                }
                if ($vest !== "None") {
                    $item = $item . " + " . $vest . ' Vest ';
                }
        }

        $pdf->AddPage();
        $pdf->SetFont('times', '', 12);
        $items = 'Items: ' . $item;
        $pdf->MultiCell(0, 4, $customerName, 0, 'l', false);
        $getSection = mysqli_query($con, "select * from section where customerId like '$cusID'");
        $pdf->MultiCell(0, 4, $notes, 0, 'l');
        $pdf->MultiCell(0, 4, $section, 0, 'l');

        $pdf->MultiCell(0, 4, $items, 0, 'l', false);


    }
}
$pdf->Output();


?>