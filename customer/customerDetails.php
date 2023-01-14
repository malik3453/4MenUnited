<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$altStatus = "";
$con = mysqli_connect("localhost", "root", "", "4men");
$ID = $_GET['ID'];


$double = mysqli_query($con, "SELECT * from customerhistroy where id=(
    SELECT max(id) FROM customerhistroy)");
$dCheck= 0;



while ($rowData = ($double)->fetch_assoc()) {
    $dCheck = $rowData['customerID'];
}




if ($dCheck !== $ID){

    $getHistroy = mysqli_query($con,"select * from customerhistroy");
    if (mysqli_num_rows($getHistroy)>3){
        mysqli_query($con,"delete from customerhistroy where id = (
    SELECT min(id) FROM customerhistroy)");
        mysqli_query($con,"insert into customerhistroy (customerID) values ('$ID')");
    }else{
        mysqli_query($con,"insert into customerhistroy (customerID) values ('$ID')");

    }



}





$section = "Hold";
$sectionID = "0";
$getSection = mysqli_query($con, "Select w2.section, sectionID from customerdetails join weddingpartyattr w on customerdetails.customerID = w.customerID join weddingform w2 on w.weddingID = w2.weddingID join sectionattr s on w2.section = s.name where customerdetails.customerID = '$ID'");
while ($rowData = ($getSection)->fetch_assoc()) {
    $section = $rowData['section'];
    $sectionID = $rowData['sectionID'];
}
$customerSql = mysqli_query($con, "SELECT * FROM customerdetails WHERE customerID=" . $ID);
while ($rowData = ($customerSql)->fetch_assoc()) {
    $customerID = $rowData['customerID'];
    $customerName = $rowData['customerName'];
    $customerNumber = $rowData['customerNumber'];
    $alternationID = $rowData['alternationID'];
    $status = $rowData['status'];
    $date = $rowData['date'];
    $notes = $rowData['notes'];
    $histroy = $rowData['histroy'];
    $salesPerson = $rowData['salesPerson'];
    $type = $rowData['type'];
    $suit = $rowData['suit'];
    $suitColor = $rowData['suitColor'];
    $suitSize = $rowData['suitSize'];
    $fit = $rowData['suitFit'];
    $pantSize = $rowData['pantSize'];
    $shirtType = $rowData['shirtType'];
    $shirtSize = $rowData['shirtSize'];
    $vestS = $rowData['vest'];
}

if ($pantSize === 0 || $pantSize === null || $pantSize === "None" || $pantSize === "") {
    $pantSize = "-";
}
if ($shirtType === 0 || $shirtType === null || $shirtType === "None" || $shirtType === "") {
    $shirtType = "-";
}
if ($shirtSize === 0 || $shirtSize === null || $shirtSize === "None" || $shirtSize === "") {
    $shirtSize = "-";
}
if ($suit === 0 || $suit === null || $suit === "None" || $suit === "") {
    $suit = "-";
}
if ($type === 0 || $type === null || $type === "None" || $type === "") {
    $type = "-";
}
if ($fit === 0 || $fit === null || $fit === "None" || $fit === "") {
    $fit = "-";
}
if ($suitColor === 0 || $suitColor === null || $suitColor === "None" || $suitColor === "") {
    $suitColor = "-";
}
if ($vestS === 0 || $vestS === null || $vestS === "None" || $vestS === "") {
    $vestS = "-";
};


$sqlCount = mysqli_query($con, "Select count(customerID) as 'c' from weddingpartyattr where customerID=" . $ID);
while ($rowData = ($sqlCount)->fetch_assoc()) {

    $count = $rowData['c'];
}
$partyReq = mysqli_query($con, "SELECT * FROM weddingpartyattr WHERE customerID=" . $ID);
while ($rowData = ($partyReq)->fetch_assoc()) {

    $individual = $rowData['individual'];
    $wID = $rowData['weddingID'];
    $partyReq2 = mysqli_query($con, "SELECT * FROM weddingform WHERE weddingID=" . $wID);
    while ($rowData = ($partyReq2)->fetch_assoc()) {

        $groom = $rowData['groomName'];
    }
}

$alterationTicket = mysqli_query($con, "select * from alteration where customerID =" . $ID);
$cost = "";
$alt = "";
while ($items = ($alterationTicket)->fetch_assoc()) {
    $altID = $items['alterationID'];
    $alt = $items['alteration'];
    $cost = $items['cost'];
    $totalCost = $items['totalCost'];
    $tailorD = $items['tailorDate'];
    $pickup = $items['pickUpDate'];
    $altNotes = $items['notes'];
    $tailorID = $items['tailor'];
    $altStatus = $items['status'];
}
$ralterationTicket = mysqli_query($con, "select * from realt where customerID =" . $ID);
$rcost = "";
$ralt = "";
$rtailorID = "";
$tailorID = "";
$raltStatus = "";
while ($items = ($ralterationTicket)->fetch_assoc()) {
    $raltID = $items['realtID'];
    $ralt = $items['realt'];
    $rcost = $items['cost'];
    $rtotalCost = $items['totalCost'];
    $rtailorD = $items['tailorDate'];
    $rpickup = $items['pickUpDate'];
    $raltNotes = $items['notes'];
    $rtailorID = $items['tailor'];
    $raltStatus = $items['status'];
}
if ($tailorID != "") {
    $getTailorName = mysqli_query($con, "Select * from tailor where tailorID =" . $tailorID);
    while ($items = ($getTailorName)->fetch_assoc()) {
        $tailor = $items['tailor'];

    }
} else {
    $tailor = "Default";
}

if ($rtailorID != "") {
    $getTailorName = mysqli_query($con, "Select * from tailor where tailorID =" . $rtailorID);
    while ($items = ($getTailorName)->fetch_assoc()) {
        $rtailor = $items['tailor'];

    }
} else {
    $rtailor = "Default";
}
$altArray = explode(',', $alt);
$costArray = explode(',', $cost);
$raltArray = explode(',', $ralt);
$rcostArray = explode(',', $rcost);
if (isset($_POST['alt'])) {
//    mysqli_query($con, "delete from alteration where customerID = '$customerID'");
    mysqli_query($con, "delete from section where customerId =" . $customerID);
    mysqli_query($con,"Delete from alteration where customerID='$customerID'");
    $sql = "update customerdetails set status = 'In-Transit- Redo Alteration', alternationID = 0 where customerID=" . $ID;
    mysqli_query($con, $sql);

    header('Location: ../alteration/alteration.php?ID=' . $ID);
    exit();
}
if (isset($_POST['realt'])) {
//    mysqli_query($con, "delete from realt where customerID = '$ID'");
    mysqli_query($con, "delete from realt where customerID = '$ID'");
    $sql = "update customerdetails set status = 'Completed' where customerID=" . $ID;
    mysqli_query($con, $sql);
    header('Location: ../alteration/realt.php?ID=' . $ID);
    exit();
}
if (isset($_POST['updateNotes'])) {
    $newNotes= $_POST['newNotes'];
    mysqli_query($con,"update customerdetails set notes = '$newNotes' where customerID = ". $ID);
    header('Location: ../customer/customerDetails.php?ID=' . $ID);
    exit();
}
if (isset($_POST['deleteCustomer'])) {
    $getBrands = mysqli_query($con, "Select * from supplier");
    if (mysqli_num_rows($getBrands) > 0) {
        foreach ($getBrands as $brands) {
            $brand = $brands['supplier'];
            mysqli_query($con,"update `$brand` set notes = concat(notes,' - deleted customer'),customerID = 'deleted' where  customerID = ".$ID);
            mysqli_query($con,"update `$brand` set notes = concat(notes,' - deleted customer'),customerID = 'deleted' where  customerID like '%WID".$ID."%'");

        }
    }
    $sql = "delete from customerdetails where customerID=" . $ID;
    $sql1 = "delete from alteration where customerID=" . $ID;
    $sql2 = "delete from tempalt where cusID=" . $ID;
    mysqli_query($con, $sql);
    mysqli_query($con, $sql1);
    mysqli_query($con, $sql2);
    mysqli_query($con, "delete from section where customerId = '$ID'");
    mysqli_query($con, "delete from realt where customerID=" . $ID);
    mysqli_query($con, "delete from retempalt where cusID=" . $ID);
    header('Location: customer.php');

    exit();

}

use PHPMailer\PHPMailer\PHPMailer;

//Load Composer's autoloader
require '../orders/vendor/autoload.php';
if (isset($_POST['send'])) {
    $message = $_POST['sendMessage'];
    if ($message != "") {


//Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'abdultest813@gmail.com';                     //SMTP username
        $mail->Password = 'ufauhlieqcugelgp';                               //SMTP password
        $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
        $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
        $mail->setFrom('abdultest813@gmail.com', '4MenUnited');
        $cusNumberMail = $customerNumber . '@txt.bellmobility.ca';
        $mail->addAddress($cusNumberMail, $customerName);     //Add a recipient
        $mail->isHTML(true);
        $mail->Body = "Hello $customerName \n" . $message . "\nDo Not Reply";
        $mail->send();
    }
    header('Location: customerDetails.php?ID=' . $ID);


}
$getCustomerDetails = mysqli_query($con, "Select * from customerdetails where customerID =" . $ID);

while ($rowData = ($getCustomerDetails)->fetch_assoc()) {
    $customerNumber = $rowData['customerNumber'];
    $Style = $rowData['suitColor'];
    $SuitSize = $rowData['suitSize'];
    $suitFit = $rowData['suitFit'];
    $type = $rowData['type'];
    $PantSize = $rowData['pantSize'];
    $vest = $rowData['vest'];


}

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
if (isset($_POST['deleteAlteration'])) {


    $section = "Hold";
    $sectionID = "0";
    $getSection = mysqli_query($con, "Select w2.section, sectionID from customerdetails join weddingpartyattr w on customerdetails.customerID = w.customerID join weddingform w2 on w.weddingID = w2.weddingID join sectionattr s on w2.section = s.name where customerdetails.customerID = '$ID'");
    while ($rowData = ($getSection)->fetch_assoc()) {
        $section = $rowData['section'];
        $sectionID = $rowData['sectionID'];
    }
    mysqli_query($con, "Insert into section (sectionID, sectionName, customerId, customerName, items) VALUES ('$sectionID','$section','$ID','$customerName','$item')");
    $sql = "update customerdetails set status = '$section',histroy  =  concat(histroy,',','Alteration Deleted - $section'),alternationID = 0 where customerID=" . $ID;
    $sql1 = "delete from alteration where customerID=" . $ID;
    $sql2 = "delete from tempalt where cusID=" . $ID;

    mysqli_query($con, $sql);
    mysqli_query($con, $sql1);
    mysqli_query($con, $sql2);
    header('Location: customerDetails.php?ID=' . $ID);


}

if (isset($_POST['deleteReAlteration'])) {

    $sql = "update customerdetails set status = 'Completed',histroy  =  concat(histroy,',','Re-alt Deleted- Completed') where customerID=" . $ID;
    $sql1 = "delete from realt where customerID=" . $ID;
    $sql2 = "delete from retempalt where cusID=" . $ID;

    mysqli_query($con, $sql);
    mysqli_query($con, $sql1);
    mysqli_query($con, $sql2);
    header('Location: customerDetails.php?ID=' . $ID);


}
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $customerName ?>'s Details</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="css/super-hero-bootstrap.min.css" rel="stylesheet">-->
    <!-- Optional theme -->
    

    <link href="../custom.css" rel="stylesheet">
    <link href="customer.css" rel="stylesheet">


 <script type="text/javascript">
        


        


    </script></head>

<body>
<nav class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="../session/homePage.php"><img src="../img/logo.png" class="brandLogo"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">    <?php if ($count > 0) {


                            if ($individual != "Groom") {
                                echo $customerName, " - ($groom 's Party: $individual) ";
                            } else {
                                echo $customerName . ": " . $individual;
                            }

                        } else {
                            echo $customerName;
                        }


                        ?><span class="sr-only"></span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Weddings <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="../weddingParty/weddingParty.php">Search Wedding Parties</a></li>
                        <li><a href="../weddingParty/weddingForm.php">Wedding Form</a></li>


                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Customers <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="../customer/customer.php">Search Customers</a></li>
                        <li><a href="../customer/newCustomer.php">New Customer Form</a></li>


                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Orders <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="../orders/search.php">Search Purchase Order</a></li>
                        <li role="separator" class="divider"></li>
                        <?php
                        $order = mysqli_query($con, 'Select supplier as s, prefix as p from supplier;');
                        if (mysqli_num_rows($order) > 0) {
                            foreach ($order as $items) {

                                ?>
                                <li><a href="../orders/purchaseOrder.php?NAME=<?php

                                    echo $items['p'];
                                    ?>"><?= $items['s']; ?> orders</a></li>

                                <?php
                            }
                        }
                        ?>


                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Alterations<span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        
                        <li><a href="../alteration/receiveAlts.php">Receive Alterations</a></li>
                        <li><a href="../alteration/assign.php">Assign Tailor</a></li>


                    </ul>
                </li>
                <li><a href="../section/sections.php">Sections</a></li>
                <li><a href="../session/logout.inc.php">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container">
    <div class="right">

        <?php

        if ($count > 0) {
            ?>
            <a class="right" href="../weddingParty/weddingPartyAttr.php?ID=<?php echo $wID; ?>">
                <button  type="button"  class="btn btn-lg"><?= $groom ?>'s Party
                </button>
            </a>
            <?php
        } else { ?>
            <a class="right" href="customer.php">
                <button id="weddingParty" type="button" name="weddingParty" class="btn btn-lg">Customer Search</button>
            </a>
        <?php } ?>

    </div>
    <h4 class="text-center heading"><?= $customerName ?></h4>

    <table class="det">
        <thead class="det">
        <tr class="det">
            <th scope="col" class="det">Status</th>
            <th scope="col" class="det">Number</th>
            <th scope="col" class="det">Sales Person</th>
            <?php if ($count > 0) {
                echo "<th scope='col' class='det'>Individual</th>";
                if ($individual != "Groom") {
                    echo "<th scope='col' class='det'>Groom Name</th>";
                }
            } ?>
            <th scope="col" class="det">Type</th>
            <th scope="col" class="det">Brand</th>
            <th scope="col" class="det">Color / Style</th>
            <th scope="col" class="det">Size</th>
            <th scope="col" class="det">Pant Size</th>
            <th scope="col" class="det">Shirt Type</th>
            <th scope="col" class="det">Shirt Size</th>
            <th scope="col" class="det">Vest</th>
        </tr>
        </thead>
        <tbody class="det">
        <tr class="det">

            <td class="det" data-label="Status"><?= $status ?></td>
            <td class="det" data-label="Number"><?= $customerNumber ?></td>
            <td class="det" data-label="Sales Person"><?= $salesPerson ?></td>
            <?php if ($count > 0) {


                echo '<td class="det" data-label="Individual">'. $individual .'</td>';
                if ($individual != "Groom") {
                    echo '<td class="det" data-label="Groom">'. $groom .'</td>';
                }

            } ?>


            <td class="det" data-label="Type"><?= $type ?></td>
            <td class="det" data-label="Brand"><?= $suit ?></td>
            <td class="det" data-label="Color / Style"><?= $suitColor ?></td>
            <td class="det" data-label="Size"><?= $SuitSize . ' ' . $fit ?></td>
            <td class="det" data-label="Pant Size"><?= $PantSize ?></td>
            <td class="det" data-label="Shirt Type"><?= $shirtType ?></td>
            <td class="det" data-label="Shirt Size"><?= $shirtSize ?></td>
            <td class="det" data-label="Vest"><?= $vestS ?></td>

        </tr>


        </tbody>
    </table>
    <div class="customerDetails">
        <p class="word"><button type="button" data-toggle="modal" class="btn palt" data-target="#editNotes">Edit </button> <b>Notes:</b> <?= $notes ?> </p>
        <div class="modal" id="editNotes" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content text-center">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalLabelLarge">Edit notes</h4>
                    </div>
                    <br>
                    <form method="post">
                        <textarea class="textbox" name="newNotes"><?= $notes  ?></textarea><br>
                        <button type="submit" class="btn palt" name="updateNotes">Update Notes</button><br><br></form>
                </div>
            </div>

        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-8">
            <div class="text-center">
                <?php
                if ($altStatus === "Altering") {
                    echo "<i>Only use the following Button if changes need to make to the orignal alteration</i>";
                    ?>
                    <form method="post" onsubmit="return comfirmDeleteCustomer()">
                        <button type="submit" class="btn alt" name="alt">Alteration</button>
                    </form>
                    <?php
                } else if ($altStatus === "") {
                    ?>
                    <form method="post" onsubmit="return comfirmDeleteCustomer()">
                        <button type="submit" class="btn alt" name="alt">Alteration</button>
                    </form>
                    <?php
                } else if ($altStatus === "Completed") {
                    ?>
                    <form method="post" onsubmit="return comfirmRealtCustomer()">
                        <button type="submit" class="btn alt" name="realt">Realter the Customer Items</button>
                    </form>

                    <?php

                }
                if ($raltStatus === "Re-Altering") {
                    echo "<i>Only use the following Button if changes need to make to the orignal re-alteration</i>";

                }
                if ($altStatus === "in-received" || $raltStatus === "in-received") {
                    echo "<i>Alteration is in transit to recieve- Cannot make any updated until completed</i>";

                }
                ?>
            </div>
            <div>
                <?php
                $sqlCheck = mysqli_query($con, "Select count(customerID) as 'c' from realt where customerID=" . $ID);
                while ($rowData = ($sqlCheck)->fetch_assoc()) {
                    $rcheck = $rowData['c'];
                }
                if ($rcheck > 0){

                $rcombined = array($raltArray, $rcostArray);
                ?>

                <section>
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="heading2">Re-Alteration Preview</h2>

                            <table class="table table-hover altTable det">
                                <thead>
                                <th>Alterations</th>
                                <th>Cost</th>
                                </thead>
                                <tbody>
                                <?php
                                for ($i = 0; $i < count($rcombined[0]); $i++) {
                                    echo "<tr>";
                                    echo "<td>" . $rcombined[0][$i] . "</td>";
                                    echo "<td>" . $rcombined[1][$i] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                <tr>

                                    <td>Total Cost:</td>
                                    <td>$<?= $rtotalCost; ?> </td>

                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4 altTable">
                            <div class="altInfo">


                                Tailor Date: <?php echo $rtailorD ?><br>
                                Pickup Date: <?php echo $rpickup ?><br>
                                Assigned Tailor: <?php echo $rtailor ?><br>
                                Notes: <?php echo $raltNotes ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="text-center"><br>
                <?php
                if ($raltStatus === "Completed") {
                    echo "<i>The re-alt cannot be deleted because it has been completed. To make further adjustment, click the realt button</i>";


                } else {
                    ?>
                    <form method="post"><input type="submit" id="deleteReAlteration" class="btn dalt"
                                               name="deleteReAlteration" value="Delete Re-Alteration">
                    </form>
                    <?php
                }

                ?>
                <br>
                <a href="../alteration/printReAltTickets.php?ID=<?php echo $ID ?>" target="_blank">
                    <input class="btn palt" type="button" value="Print Re-Alteration Ticket">
                </a>
                <br>
                <?php
                }
                ?>
            </div>

            <hr>
            <div>
                <?php
                if ($altStatus === "Completed") {
                    echo "<p class='customerDetails'><b>Original Alteration Status: Completed</b></p>";
                }
                $sqlCheck = mysqli_query($con, "Select count(customerID) as 'c' from alteration where customerID =" . $ID);
                while ($rowData = ($sqlCheck)->fetch_assoc()) {
                    $check = $rowData['c'];
                }
                if ($check > 0){

                $combined = array($altArray, $costArray);
                ?>

                <section>
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="heading2">Alteration Preview</h2>

                            <table class="table table-hover altTable">
                                <thead>
                                <th>Alterations</th>
                                <th>Cost</th>
                                </thead>
                                <tbody>
                                <?php
                                for ($i = 0; $i < count($combined[0]); $i++) {
                                    echo "<tr>";
                                    echo "<td>" . $combined[0][$i] . "</td>";
                                    echo "<td>" . $combined[1][$i] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                <tr>

                                    <td>Total Cost:</td>
                                    <td>$<?= $totalCost; ?> </td>

                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4 h4 altTable altInfo">
                            Tailor Date: <?php echo $tailorD ?><br>
                            Pickup Date: <?php echo $pickup ?><br>
                            Assigned Tailor: <?php echo $tailor ?><br>
                            Notes: <?php echo $altNotes ?>
                        </div>
                    </div>
                </section>
            </div>


            <div class="text-center">
                <?php
                if ($altStatus === "Altering") {

                    ?>
                    <form method="post"><input type="submit" id="deleteAlteration" class="btn dalt"
                                               name="deleteAlteration" value="Delete Alteration">
                    </form><br>
                    <?php
                } else {
                    echo "<i>This alteration cannot be deleted because it has been completed</i><br>";
                }

                ?>
                <a href="../alteration/printAltTickets.php?ID=<?php echo $ID ?>" target="_blank">
                    <input class="btn palt" type="button" value="Print Alteration Ticket"><br>
                </a>

                <?php
                }
                ?>
            </div>

            <?php
            $checkSection = mysqli_query($con, "Select * from section where customerId = '$ID'");
            $cID = "Check";
            while ($rowData = ($checkSection)->fetch_assoc()) {
                $cID = $rowData['customerId'];
            }
            if ($cID != "Check") {


                ?>
                <div class="text-center">
                    <a href="print.php?ID=<?php echo $ID ?>" target="_blank">
                        <button class="btn dalt">Print Section Ticket</button>
                    </a>
                </div>

                <?php

            }
            ?>

        </div>

        <br>
        <div class="col-sm-4 customerD">
            <h3 class="heading2">Customer Histroy</h3>
            <div class="cDetails">
                <ol reversed>
                    <?php
                    $histroy = explode(',', $histroy);
                    $histroy = array_reverse($histroy);
                    foreach ($histroy as $value) {
                        echo "<li>" . $value . "</li>";
                    }

                    ?>
                </ol>
            </div>
            <h3 class="heading2">Send Message</h3>
            <form class="text-center form-ta" autocomplete="off" method="post"
                  onsubmit="return sendMessageToCustomer()">
                <p class="customerDetails">Hello <?php echo $customerName ?></p>
                <textarea type="text"
                          id="sendMessage"
                          name="sendMessage"></textarea><br>
                <p class="customerDetails">Do not Reply</p>

                <button id="send" name="send" class="btn btn-warning">Send</button>

            </form>
        </div>

    </div>
    <br>
    <hr>
    <div class="text-center">

        <?php
        if ($count > 0) {
            echo "<br>This cannot be deleted because its part of a wedding party. To delete this customer, delete it from the wedding file";
        } else { ?>
            <form method="post" onsubmit="return comfirmDeleteCustomer()"><input type="submit" id="deleteCustomer"
                                                                                 class="btn btn-danger"
                                                                                 name="deleteCustomer"
                                                                                 value="Delete Customer">
            </form>
        <?php } ?>
        <br>


    </div>


</div>
</body>
<script>
    function comfirmDeleteCustomer() {
        return confirm("Are you sure, this will remove it from the Section if applicable?");
    }
</script>
<script>
    function comfirmRealtCustomer() {
        return confirm("Are you sure you want to perform a realt");
    }
</script>
<script>
    function sendMessageToCustomer() {
        $message = document.getElementById("sendMessage").value;
        if ($message !== "") {
            $message = "Are you sure you want to send the following message: \n" + $message + "\nClick OK to Send";
            return confirm($message);
        } else {
            alert("Message box can't be empty");
        }

    }

</script>
<script src="../jquery.js"></script>

<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>
