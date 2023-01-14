<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';
$con = mysqli_connect("localhost", "root", "", "4men");
$con = mysqli_connect("localhost", "root", "", "4men");
$NAME = $_GET['NAME'];
$getPO = mysqli_query($con, "Select * from purchaseorder where poID like '$NAME'");
while ($rowData = ($getPO)->fetch_assoc()) {
    $email = $rowData['email'];
    $supplier = $rowData['brand'];
    $status = $rowData['status'];
    $datePlaced = $rowData['datePlaced'];
    $dateCompleted = $rowData['dateCompleted'];
}
$getOrder = mysqli_query($con, "select * from `$supplier` where poID like '$NAME'");

if (isset($_POST['notifyAll'])) {
    if (!empty($_POST['notify'])) {
        foreach ($_POST['notify'] as $value) {
            $details = mysqli_query($con, "Select * from `$supplier` where poID = '$NAME' and orderNumber = '$value'");
            while ($rowData = ($details)->fetch_assoc()) {
                $customerID = $rowData['customerID'];
                $status = $rowData['notes'];
                $customerItems = $rowData['items'];

            }
            if (str_contains($customerID,'WID')){
                $customerID = substr($customerID,3);
                $getGroom = mysqli_query($con, "Select groomName,Pnumber from weddingform where weddingID ='$customerID'");
                while ($rowData = ($getGroom)->fetch_assoc()) {
                    $customerName = $rowData['groomName'];
                    $customerNumber = $rowData['Pnumber'];
                }
            }else {
                $getCustomer = mysqli_query($con, "Select customerNumber,customerName from customerdetails where customerID ='$customerID'");
                while ($rowData = ($getCustomer)->fetch_assoc()) {
                    $customerName = $rowData['customerName'];
                    $customerNumber = $rowData['customerNumber'];
                }
            }
            $mail = new PHPMailer(true);
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'abdultest813@gmail.com';                     //SMTP username
            $mail->Password = 'ufauhlieqcugelgp';                               //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
            $address = $customerNumber . '@txt.bellmobility.ca';
            $mail->setFrom('abdultest813@gmail.com', '4MenUnited');
            $mail->addAddress($address, $customerName);     //Add a recipient


//Content
            $mail->isHTML(true);                                  //Set orders format to HTML
            $mail->Body = 'Hello ' . $customerName . ' this is a message from 4 Men United notifying you that your ' . $customerItems . ' on orders have arrived. 

If you are apart of a wedding party, remember to BOOK your fitting 2 MONTHS before your event by visiting our websites Live Calendar. Www.4menunited.com

Do not reply. 
';

            mysqli_query($con, "update `$supplier` set notified=1 where orderNumber = '$value' and poID = '$NAME'");
            $mail->send();
            header('Location: receive.php?NAME=' . $NAME);

        }

    }
}
if (isset($_POST['printAll'])) {
    $printItems = array();

    if (!empty($_POST['print'])) {
        foreach ($_POST['print'] as $value) {
            $printItems[] = substr($value, 4);
        }
    }
    $printItems[] = $NAME;
    $a = implode('%', $printItems);
    header("location: print.php?NAME=".$a);
    exit();

}


if (isset($_POST['receive'])) {
    $getorder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$supplier') and status = 'open'");
    while ($rowData = ($getorder)->fetch_assoc()) {
        $newPO = $rowData['poid'];
    }


    if (!empty($_POST['checked'])) {
        foreach ($_POST['checked'] as $value) {
            $oldSql = mysqli_query($con, "Select * from `$supplier` where poID = '$NAME' and orderNumber = '$value'");
            $section = "Hold";
            $sectionID = "0";

            while ($rowData = ($oldSql)->fetch_assoc()) {
                $customerID = $rowData['customerID'];
                $customerName = $rowData['customerName'];
                $itemsP = $rowData['items'];
                $notes = $rowData['notes'];
                $status = $rowData['status'];
                $orderNumber = $rowData['orderNumber'];

            }
            $getSection = mysqli_query($con, "select * from customerdetails join weddingpartyattr w on customerdetails.customerID = w.customerID join weddingform w2 on w.weddingID = w2.weddingID join sectionattr s on w2.section = s.name where customerdetails.customerID = '$customerID'");
            while ($rowData = ($getSection)->fetch_assoc()) {
                $section = $rowData['section'];
                $sectionID = $rowData['sectionID'];

            }
            $wid = substr($customerID,0,3);
            mysqli_query($con, "update `$supplier` set status='Received' where orderNumber like '$value' and poID = '$NAME'");
            if ($notes == 'Walk-in Customer' || $notes == 'Walk-in Customer (Reordered)') {
                mysqli_query($con, "update customerDetails set status = 'Hold',histroy  =  concat(histroy,',','Hold') where customerID = '$customerID'");
                mysqli_query($con, "insert into section (sectionID, sectionName, customerId, customerName, items) VALUES ('$sectionID','$section','$customerID','$customerName','$itemsP')");
            }elseif ($wid === "WID"){
                    $weddingID = substr($customerID,3);
                    $wedSectionID = $customerID;
                    $weddingID = explode('-',$weddingID);
                    $accID  = (int) $weddingID[1];
                    $weddingID = $weddingID[0];
                    $customerID = substr($customerID,0,3);
                    $getGroom = mysqli_query($con, "Select groomName,Pnumber from weddingform where weddingID ='$customerID'");
                    while ($rowData = ($getGroom)->fetch_assoc()) {
                        $customerName = $rowData['groomName'];
                        $customerNumber = $rowData['Pnumber'];
                    }

                    mysqli_query($con,"update accessories set status = 'Sectioned' where accID = '$accID'");
                    mysqli_query($con,"insert into section(sectionID, sectionName, customerId, customerName, items) VALUES (100,'Accessories','$wedSectionID','$customerName','$itemsP')");
            } elseif ($customerID != 'Sales Person') {
                $getParty = mysqli_query($con, "select * from weddingpartyattr where customerID like '$customerID'");
                while ($rowData = ($getParty)->fetch_assoc()) {
                    $wedID = $rowData['weddingID'];
                }
                $getSection = mysqli_query($con, "select section from weddingform where weddingID like '$wedID'");
                while ($rowData = ($getSection)->fetch_assoc()) {
                    $section = $rowData['section'];
                }
                $getSectionID = mysqli_query($con, "select * from sectionattr where name = '$section'");
                while ($rowData = ($getSectionID)->fetch_assoc()) {
                    $sectionID = $rowData['sectionID'];
                }

                mysqli_query($con, "insert into section (sectionID, sectionName, customerId, customerName, items) VALUES ('$sectionID','$section','$customerID','$customerName','$itemsP')");
                mysqli_query($con, "update customerDetails set status = '$section',histroy  =  concat(histroy,',','$section') where customerID ='$customerID'");
            }



        }
    }

    $getOOT = mysqli_query($con, "Select * from `$supplier` where poID ='$NAME' and status like 'Placed'");
    if (mysqli_num_rows($getOOT) > 0) {
        foreach ($getOOT as $items) {
            $orderNum = $items['orderNumber'];
            $oldSql = mysqli_query($con, "Select * from `$supplier` where poID = '$NAME' and orderNumber = '$orderNum'");
            while ($rowData = ($oldSql)->fetch_assoc()) {
                $customerID = $rowData['customerID'];
                $customerName = $rowData['customerName'];
                $itemsP = $rowData['items'];
                $notes = $rowData['notes'];
                $status = $rowData['status'];

            }
            $sqlPrefix = mysqli_query($con, "Select prefix from supplier where supplier = '$supplier'");
            while ($rowData = ($sqlPrefix)->fetch_assoc()) {
                $prefix = $rowData['prefix'];
            }
            $sqlCount = mysqli_query($con, "SELECT max(cast(substr(orderNumber,5,100) as integer)) as 'max' FROM `$supplier` WHERE poID like '$newPO';");
            while ($rowData = ($sqlCount)->fetch_assoc()) {
                $newON =  $rowData['max'];
            }

            $newON += 1;
            $newON = $prefix . "#" . $newON;
            $sqlOrder = "insert into `$supplier` values ('$newPO','$newON','$customerID','$customerName','$itemsP','New','$notes','')";

            $wid = substr($customerID,0,3);
            $customerID = explode('-',$customerID);
            $count = count($customerID);
            if ($count=2) {
                $accID =  $customerID[1];
            }
            $customerID = $customerID[0];
            if ($wid !== "WID"){
                mysqli_query($con, $sqlOrder);
                $status = mysqli_real_escape_string($con, "Re-Ordered " . $newPO . "(" . $newON . ")");
                mysqli_query($con,"update customerDetails set status = '$status',histroy  =  concat(histroy,',','$status') where customerID ='$customerID'");
            }else{
                mysqli_query($con,"update accessories set status = 'Out of Stock' where accID = '$accID'");
            }
            mysqli_query($con, "update `$supplier` set status='Out of Stock' where orderNumber like '$orderNum' and poID = '$NAME' and status like 'Placed'");

        }
    }
    $date = date("Y/m/d");
    mysqli_query($con, "update purchaseOrder set status = 'Completed', `dateCompleted` = '$date' where poID like '$NAME' ");
    header('Location: receive.php?NAME=' . $NAME);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchasing Order (<?php echo $NAME ?>) </title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="orders.css">

<!-- <script type="text/javascript">-->
<!--        

-->
<!--        function noBack() {-->
<!--            

-->
<!--        }-->
<!--    </script></head>-->

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
                <li class="active"><a href="#">Purchasing Order #<?php echo $NAME ?><span class="sr-only"></span></a>
                </li>
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

        <a class="right" href="search.php">
            <button id="" type="button" name="" class="btn btn-lg">Search Purchase Order
            </button>
        </a>

    </div>
    <?php if ($status == 'Pending') {
        ?>
        <form method="post" onsubmit="return confirm('This cannot be undone, make sure all that received are checked off!');">
            <h1>Receive Purchasing Order</h1>
            <button id="receive" name="receive" class="btn btn-danger">Receive
            </button>
            <table class="table table-hover">
                <thead>
                <tr>

                    <th>Order Number</th>
                    <th>Customer Name</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Receive</th>
                </tr>
                </thead>
                <tbody>
                <?php


                if (mysqli_num_rows($getOrder) > 0) {

                    foreach ($getOrder

                             as $items) {

                        if ($items['customerName'] === null) {
                            $a = "nullValues";
                        } else {
                            $a = $items['customerID'];
                        }

                        ?>
                        <tr>
                            <td><?= $items['orderNumber']; ?></td>
                            <td><?php if ($items['customerName'] === null) {
                                    echo $items['customerID'];
                                } else {
                                    echo $items['customerName'];
                                }


                                ?></td>
                            <td><?= $items['items']; ?></td>
                            <td><?= $items['status']; ?></td>
                            <td><?= $items['notes']; ?></td>
                            <?php if ($items['customerID'] === "") {
                                $items['customerID'] = $items['salesID'];
                            }


                            ?>

                            <td><input type="checkbox" name="checked[]" value="<?php echo $items['orderNumber'] ?>">
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>


                </tbody>
            </table>
        </form>

        <?php
    } else if ($status == 'Completed') {


        ?>
        <h1 class="heading">Status - Completed (<?php echo $dateCompleted ?>)</h1>
        <table class="table table-hover">
            <thead>
            <tr>

                <th>Order Number</th>
                <th>Customer Name</th>
                <th>Items</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Notified</th>
            </tr>
            </thead>
            <tbody>
            <?php


            if (mysqli_num_rows($getOrder) > 0) {

                foreach ($getOrder

                         as $items) {

                    if ($items['customerName'] === null) {
                        $a = "nullValues";
                    } else {
                        $a = $items['customerID'];
                    }

                    ?>
                    <tr>
                        <td><?= $items['orderNumber']; ?></td>
                        <td><?php if ($items['customerName'] === null) {
                                echo $items['customerID'];
                            } else {
                                echo $items['customerName'];
                            }


                            ?></td>
                        <td><?= $items['items']; ?></td>
                        <td><?= $items['status']; ?></td>
                        <td><?= $items['notes']; ?></td>
                        <td><?php if ($items['notified'] == 1) {
                                echo "Yes";
                            } else {
                                echo "No";
                            } ?></td>


                    </tr>
                    <?php
                }
            }
            ?>


            </tbody>
        </table>


        <form method="post">
            <h3 class="heading2">Select below to notify that their items have arrived</h3>
            <button id="notifyAll" type="submit" name="notifyAll" class="btn btn-danger">Notify
            </button>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Notify</th>
                    <th>Customer Name</th>
                    <th>Items</th>

                </tr>
                </thead>
                <tbody>
                <?php

                $getOrder = mysqli_query($con, "select * from `$supplier` where poID like '$NAME' and status = 'Received' and customerID !='Sales Person' and notified =0 and notes NOT Like '%Reordered%' and notes not like '%deleted customer%'");

                if (mysqli_num_rows($getOrder) > 0) {

                    foreach ($getOrder

                             as $items) {

                        if ($items['customerName'] === null) {
                            $a = "nullValues";
                        } else {
                            $a = $items['customerID'];
                        }


                        ?>
                        <tr>
                            <td><input type="checkbox" name="notify[]" value="<?php echo $items['orderNumber'] ?>"></td>
                            <td><?php if ($items['customerName'] === null) {
                                    echo $items['customerID'];
                                } else {
                                    echo $items['customerName'];
                                }


                                ?></td>
                            <td><?= $items['items']; ?></td>


                        </tr>
                        <?php
                    }
                }
                ?>


                </tbody>
            </table>
        </form>


        <form method="post">
            <h3 class="heading2">Print Tags</h3>
            <button id="printAll" type="submit" name="printAll" class="btn btn-danger">Print
            </button>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Print</th>
                    <th>Customer Name</th>
                    <th>Items</th>

                </tr>
                </thead>
                <tbody>
                <?php

                $getOrder = mysqli_query($con, "select * from `$supplier` where poID like '$NAME' and status = 'Received' and customerID !='Sales Person' and not notes like '%deleted customer%'");

                if (mysqli_num_rows($getOrder) > 0) {

                    foreach ($getOrder

                             as $items) {

                        if ($items['customerName'] === null) {
                            $a = "nullValues";
                        } else {
                            $a = $items['customerID'];
                        }

                        ?>
                        <tr>
                            <td><input type="checkbox" name="print[]" value="<?php echo $items['orderNumber'] ?>"></td>
                            <td><?php if ($items['customerName'] === null) {
                                    echo $items['customerID'];
                                } else {
                                    echo $items['customerName'];
                                }


                                ?></td>
                            <td><?= $items['items']; ?></td>


                        </tr>
                        <?php
                    }
                }
                ?>


                </tbody>
            </table>
        </form>

        <?php

    } else {
        $sqlPrefix = mysqli_query($con, "Select prefix from supplier where supplier = '$supplier'");
        while ($rowData = ($sqlPrefix)->fetch_assoc()) {
            $prefix = $rowData['prefix'];
        }
?>
    <script>
        window.location.href = 'purchaseOrder.php?NAME=<?=$prefix?>';

    </script>


    <?php
    } ?>


</div>
</body>
<script src="../jquery.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>
