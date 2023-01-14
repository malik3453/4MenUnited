<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$ID = $_GET['ID'];
$partySql = mysqli_query($con, "SELECT * FROM weddingform WHERE weddingID=" . $ID);

while ($rowData = ($partySql)->fetch_assoc()) {
    $weddingID = $rowData['weddingID'];
    $groomName = $rowData['groomName'];
    $weddingDate = $rowData['weddingDate'];
    $weddingMonth = $rowData['weddingMonth'];
    $email = $rowData['email'];
    $number = $rowData['Pnumber'];
    $groomSuit = $rowData['groomSuit'];
    $groomStyle = $rowData['groomStyle'];
    $groomsmenSuit = $rowData['groomsmenSuit'];
    $groomsmanStyle = $rowData['groomsmanStyle'];
    $fatherOfTheGroom = $rowData['fatherOfTheGroom'];
    $fatherOfTheGroomStyle = $rowData['fatherOfTheGroomStyle'];
    $fatherOfTheBrideSuit = $rowData['fatherOfTheBrideSuit'];
    $fatherOfTheBrideStyle = $rowData['fatherOfTheBrideStyle'];
    $salesPerson = $rowData['salesPerson'];
    $notes = $rowData['notes'];

}



if (isset($_POST['order'])) {
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $style = mysqli_real_escape_string($con, $_POST['stylea']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $items = $quantity . " x (" . $style . ")";

    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $getorder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$brand') and status = 'open'");
    while ($rowData = ($getorder)->fetch_assoc()) {
        $POID = $rowData['poid'];
    }
    $getGroomName = mysqli_query($con, "select * from weddingform where weddingID = '$ID'");
    while ($rowData = ($getGroomName)->fetch_assoc()) {
        $groomName = $rowData['groomName'];
    }


    $brandT = strtolower($brand);
    $sqlPrefix = mysqli_query($con, "Select prefix from supplier where supplier = '$brand'");
    while ($rowData = ($sqlPrefix)->fetch_assoc()) {
        $prefix = $rowData['prefix'];
    }
    $sqlCount = mysqli_query($con, "SELECT max(cast(substr(orderNumber,5,100) as integer)) as 'max' FROM `$brandT` WHERE poID like '$POID';");
    while ($rowData = ($sqlCount)->fetch_assoc()) {
        $max = $rowData['max'];
    }

    $max += 1;
    $max = $prefix . "#" . $max;
    $sqlCount = mysqli_query($con, "SELECT max(accID) as 'max' FROM accessories;");
    while ($rowData = ($sqlCount)->fetch_assoc()) {
        $maxAcces = $rowData['max'];
    }
    $maxAcces += 1;
    $cusID = "WID" . $ID . "-" . $maxAcces;
    $acNotes = "Accessories" . "(" . $groomName . "''s Party)";
    $status = mysqli_real_escape_string($con, "Ordered " . $POID . "(" . $max . ")");

    $sqlOrder = "insert into `$brand` values ('$POID','$max','$cusID','$groomName','$items','New','$acNotes','')";
    mysqli_query($con, $sqlOrder);
    mysqli_query($con, "insert into accessories (brand,accID,weddingID, items, status) VALUES ('$brand','$maxAcces','$ID','$items','$status')");
    header('Location: ../orders/purchaseOrder.php?NAME=' . $prefix);
    exit();

}
if (isset($_POST['updateNotes'])) {
    $newNotes= $_POST['newNotes'];
    mysqli_query($con,"update weddingForm set notes = '$newNotes' where weddingID = ". $ID);
    header('Location: ../weddingParty/weddingPartyAttr.php?ID=' . $ID);
    exit();
}

if (isset($_POST['hold'])) {
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $style = mysqli_real_escape_string($con, $_POST['stylea']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $items = $quantity . " x (" . $style . ")";
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $sqlCount = mysqli_query($con, "SELECT max(accID) as 'max' FROM accessories;");
    while ($rowData = ($sqlCount)->fetch_assoc()) {
        $maxAcces = $rowData['max'];
    }
    $maxAcces += 1;
    $maxID = "WID" . $ID . "-" . $maxAcces;
    mysqli_query($con, "insert into section (sectionID, sectionName, customerId, customerName, items) VALUES (100,'Accessories','$maxID','$groomName','$items')");
    mysqli_query($con, "insert into accessories (brand,accID,weddingID, items, status) VALUES ('$brand','$maxAcces','$ID','$items','Sectioned')");
    header('Location: ../weddingParty/weddingPartyAttr.php?ID=' . $ID);
    exit();

}
if (isset($_POST['deleteParty'])) {
    $sql = mysqli_query($con, "Select customerdetails.customerID as 'c' from customerdetails join weddingpartyattr w on customerdetails.customerID = w.customerID");
    if (mysqli_num_rows($sql) > 0) {
        foreach ($sql as $items) {
            mysqli_query($con, "delete from customerdetails where customerID =" . $items['c']);
            $getBrands = mysqli_query($con, "Select * from supplier");
            if (mysqli_num_rows($getBrands) > 0) {
                foreach ($getBrands as $brands) {
                    $brand = $brands['supplier'];
                    mysqli_query($con, "update `$brand` set notes = concat(notes,' - deleted customer') where  customerID = " . $items['c']);
                    mysqli_query($con, "update `$brand` set notes = concat(notes,' - deleted customer'),customerID = 'deleted' where  customerID like '%WID" . $ID . "%'");

                }
            }
            mysqli_query($con, "delete from alteration where customerID=" . $items['c']);
            mysqli_query($con, "delete from tempalt where cusID= " . $items['c']);
            mysqli_query($con, "delete from section where sectionID != '100' and customerId =" . $items['c']);
        }
    }
    mysqli_query($con, "delete from accessories where weddingID = " . "$ID");
    mysqli_query($con, "delete from section where sectionID = '100' and customerId = " . $ID);
    mysqli_query($con, "delete from weddingpartyattr where weddingID=" . $ID);
    mysqli_query($con, "delete from weddingform where weddingID=" . $ID);
    header("Location: weddingParty.php");

}
if (isset($_REQUEST['delete'])) {
    $deleteTempAlt = "delete from customerdetails where customerID = {$_REQUEST['deleteID']}";
    $deleteTempAlt1 = "delete from weddingpartyattr where customerID = {$_REQUEST['deleteID']}";
    mysqli_query($con, "delete from section where customerId ={$_REQUEST['deleteID']}");
    mysqli_query($con, "delete from realt where customerID={$_REQUEST['deleteID']}");
    mysqli_query($con, "delete from retempalt where cusID={$_REQUEST['deleteID']}");
    mysqli_query($con, "delete from alteration where customerID={$_REQUEST['deleteID']}");
    mysqli_query($con, "delete from tempalt where cusID={$_REQUEST['deleteID']}");
    $getBrands = mysqli_query($con, "Select * from supplier");
    if (mysqli_num_rows($getBrands) > 0) {
        $getBrands = mysqli_query($con, "Select * from supplier");
        foreach ($getBrands as $brands) {
            $brand = $brands['supplier'];
            mysqli_query($con, "update `$brand` set notes = concat(notes,' - deleted customer'),customerID = 'deleted' where  customerID ={$_REQUEST['deleteID']}");
        }
    }
    mysqli_query($con, $deleteTempAlt);
    mysqli_query($con, $deleteTempAlt1);
}

if (isset($_REQUEST['deleteA'])) {

    $delA = "delete from accessories where accID = {$_REQUEST['deleteAID']}";
    mysqli_query($con, "delete from section where customerId =concat('WID','$ID','-',{$_REQUEST['deleteAID']})");
    $getBrands = mysqli_query($con, "Select * from supplier");
    if (mysqli_num_rows($getBrands) > 0) {
        $getBrands = mysqli_query($con, "Select * from supplier");
        foreach ($getBrands as $brands) {
            $brand = $brands['supplier'];
            mysqli_query($con, "update `$brand` set notes = concat(notes,' - deleted accessories'),customerID = 'deleted' where  customerID =concat('WID','$ID','-',{$_REQUEST['deleteAID']})");
        }
    }
    mysqli_query($con, $delA);
}
if (isset($_REQUEST['pickup'])) {
    $delA = "update accessories set status='picked up' where accID = {$_REQUEST['pickupID']}";
    mysqli_query($con, "delete from section where customerId =concat('WID','$ID-',{$_REQUEST['pickupID']})");
    $getBrands = mysqli_query($con, "Select * from supplier");
    if (mysqli_num_rows($getBrands) > 0) {
        $getBrands = mysqli_query($con, "Select * from supplier");
        foreach ($getBrands as $brands) {
            $brand = $brands['supplier'];
            mysqli_query($con, "update `$brand` set notes = concat(notes,' - stock (picked up) ') where  customerID =concat('WID','$ID','-',{$_REQUEST['pickupID']})");
        }
    }
    mysqli_query($con, $delA);
}
$sqlSuits = "Select supplier as s from supplier";
$all_sqlSuits = mysqli_query($con, $sqlSuits);
$all_sqlSuits1 = mysqli_query($con, $sqlSuits);
$all_sqlSuits2 = mysqli_query($con, $sqlSuits);
$all_sqlSuits3 = mysqli_query($con, $sqlSuits);
if (isset($_POST['submit'])) {
    // Store the Product name in a "name" variable
    $uweddingDate = mysqli_real_escape_string($con, $_POST['weddingDate']);
    $ugroomSuit = mysqli_real_escape_string($con, $_POST['groomSuit']);
    $ugroomStyle = mysqli_real_escape_string($con, $_POST['groomStyle']);
    $ugroomsmenSuit = mysqli_real_escape_string($con, $_POST['groomsmenSuit']);
    $ugroomsmanStyle = mysqli_real_escape_string($con, $_POST['groomsmanStyle']);
    $ufatherOfTheGroom = mysqli_real_escape_string($con, $_POST['fatherOfTheGroom']);
    $ufatherOfTheGroomStyle = mysqli_real_escape_string($con, $_POST['fatherOfTheGroomStyle']);
    $ufatherOfTheBrideSuit = mysqli_real_escape_string($con, $_POST['fatherOfTheBrideSuit']);
    $ufatherOfTheBrideStyle = mysqli_real_escape_string($con, $_POST['fatherOfTheBrideStyle']);
    $udate = $_POST['weddingDate'];






    $s = substr($udate, 5, 2);

    $month = "";
    switch ($s) {
        case "01":
            $month = "January";
            break;
        case "02":
            $month = "February";
            break;
        case "03":
            $month = "March";
            break;
        case "04":
            $month = "April";
            break;
        case "05":
            $month = "May";
            break;
        case "06":
            $month = "June";
            break;
        case "07":
            $month = "July";
            break;
        case "08":
            $month = "August";
            break;
        case "09":
            $month = "September";
            break;
        case "10":
            $month = "October";
            break;
        case "11":
            $month = "November";
            break;
        case "12":
            $month = "December";
            break;

    }
    $uweddingMonth = mysqli_real_escape_string($con, $month);
    if (mysqli_query($con,"update weddingform set groomSuit = '$ugroomSuit',groomStyle='$ugroomStyle',groomsmenSuit = '$ugroomsmenSuit',groomsmanStyle='$ugroomsmanStyle',fatherOfTheGroom='$ufatherOfTheGroom',fatherOfTheGroomStyle='$ufatherOfTheGroomStyle',fatherOfTheBrideSuit='$ufatherOfTheBrideSuit',fatherOfTheBrideStyle='$ufatherOfTheBrideStyle',weddingDate='$uweddingDate',weddingMonth='$uweddingMonth' where weddingID = '$ID'")) {
        header("Location: weddingPartyAttr.php?ID=" . $ID);
        exit();
    }
}




//
//if ($groomSuit === 0 || $groomSuit === null || $groomSuit === "None" || $groomSuit === "") {
//    $groomSuit = "-";
//}
//if ($groomStyle === 0 || $groomStyle === null || $groomStyle === "None" || $groomStyle === "") {
//    $groomStyle = "-";
//}
//if ($groomsmenSuit === 0 || $groomsmenSuit === null || $groomsmenSuit === "None" || $groomsmenSuit === "") {
//    $groomsmenSuit = "-";
//}
//if ($groomsmanStyle === 0 || $groomsmanStyle === null || $groomsmanStyle === "None" || $groomsmanStyle === "") {
//    $groomsmanStyle = "-";
//}
//if ($fatherOfTheGroom === 0 || $fatherOfTheGroom === null || $fatherOfTheGroom === "None" || $fatherOfTheGroom === "") {
//    $fatherOfTheGroom = "-";
//}
//if ($fatherOfTheGroomStyle === 0 || $fatherOfTheGroomStyle === null || $fatherOfTheGroomStyle === "None" || $fatherOfTheGroomStyle === "") {
//    $fatherOfTheGroomStyle = "-";
//}
//if ($fatherOfTheBrideSuit === 0 || $fatherOfTheBrideSuit === null || $fatherOfTheBrideSuit === "None" || $fatherOfTheBrideSuit === "") {
//    $fatherOfTheBrideSuit = "-";
//}
//if ($fatherOfTheBrideStyle === 0 || $fatherOfTheBrideStyle === null || $fatherOfTheBrideStyle === "None" || $fatherOfTheBrideStyle === "") {
//    $fatherOfTheBrideStyle = "-";
//};
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $groomName ?>'s Party</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="css/super-hero-bootstrap.min.css" rel="stylesheet">-->
    <!-- Optional theme -->
    <link rel="stylesheet" href="../custom.css">
    <link href="wedding.css" rel="stylesheet">


</head>

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
                <li class="active"><a href="#"><?php echo $groomName ?>'s party <span class="sr-only"></span></a></li>
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

        <a class="right" href="weddingParty.php">
            <button id="weddingParty" type="button" name="weddingParty" class="btn btn-lg">Search All Parties
            </button>
        </a>

    </div>
    <h4 class="text-center heading"><?= $groomName ?>'s Party</h4>

    <button type="button" data-toggle="modal" data-target="#editRight" class="btn palt">Edit</button><br><br>
    <div class="modal" id="editRight" tabindex="-1" role="dialog"
         aria-labelledby="modalLabelLarge"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalLabelLarge">Edit Information</h4>
                </div>
                <div class="modal-body">

                    <div class="">
                        <form method="post" autocomplete="off" class="form-horizontal">
                        <div class="form-group">
                                <label class="col-md-4 control-label" for="weddingDate">Wedding Date:</label>
                                <div class="col-md-4">
                                    <input id="weddingDate" name="weddingDate" type="date" value="<?= $weddingDate ?>"
                                           class="form-control input-md"
                                           required="">

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="groomSuit">Groom's Suit:</label>
                                <div class="col-md-4">
                                    <select id="groomSuit" onchange="ShowHideDiv('groomSuit','gS')" name="groomSuit"
                                            class="form-control">
                                        <option value="<?=$groomSuit ?>">Default - <?=$groomSuit?></option>
                                        <?php
                                        // use a while loop to fetch data
                                        // from the $all_categories variable
                                        // and individually display as an option
                                        while ($category = mysqli_fetch_array(
                                            $all_sqlSuits, MYSQLI_ASSOC)):
                                            ?>
                                            <option value="<?php echo $category["s"];
                                            // The value we usually set is the primary key
                                            ?>">
                                                <?php echo $category["s"];
                                                // To show the category name to the user
                                                ?>
                                            </option>
                                        <?php
                                        endwhile;
                                        // While loop must be terminated
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="gS">
                                <label class="col-md-4 control-label"  for="groomStyle">Groom's Color/ Style:</label>
                                <div class="col-md-4">
                                    <input id="groomStyle" name="groomStyle" type="text" value="<?= $groomStyle?>"
                                           class="form-control input-md">

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="groomsmenSuit">Groomsman's Suit:</label>
                                <div class="col-md-4">
                                    <select id="groomsmenSuit" onchange="ShowHideDiv('groomsmenSuit','gmS')" name="groomsmenSuit"
                                            class="form-control">
                                        <option value="<?=$groomsmenSuit ?>">Default - <?=$groomsmenSuit?></option>
                                        <?php
                                        // use a while loop to fetch data
                                        // from the $all_categories variable
                                        // and individually display as an option
                                        while ($category = mysqli_fetch_array(
                                            $all_sqlSuits2, MYSQLI_ASSOC)):
                                            ?>
                                            <option value="<?php echo $category["s"];
                                            // The value we usually set is the primary key
                                            ?>">
                                                <?php echo $category["s"];
                                                // To show the category name to the user
                                                ?>
                                            </option>
                                        <?php
                                        endwhile;
                                        // While loop must be terminated
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="gmS" >
                                <label class="col-md-4 control-label" for="groomsmanStyle">Groomsman's Color/ Style:</label>
                                <div class="col-md-4">
                                    <input id="groomsmanStyle" name="groomsmanStyle" value="<?= $groomsmanStyle ?>"  type="text" placeholder="E.g.  Black, 201-1, etc.."
                                           class="form-control input-md">

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="fatherOfTheGroom">Father of the Groom's Suit:</label>
                                <div class="col-md-4">
                                    <select id="fatherOfTheGroom" name="fatherOfTheGroom"
                                            onchange="ShowHideDiv('fatherOfTheGroom','FOGS')" class="form-control">
                                        <option value="<?= $fatherOfTheGroom ?>">Default - <?= $fatherOfTheGroom ?></option>
                                        <?php
                                        // use a while loop to fetch data
                                        // from the $all_categories variable
                                        // and individually display as an option
                                        while ($category = mysqli_fetch_array(
                                            $all_sqlSuits1, MYSQLI_ASSOC)):
                                            ?>
                                            <option value="<?php echo $category["s"];
                                            // The value we usually set is the primary key
                                            ?>">
                                                <?php echo $category["s"];
                                                // To show the category name to the user
                                                ?>
                                            </option>
                                        <?php
                                        endwhile;
                                        // While loop must be terminated
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="FOGS">
                                <label class="col-md-4 control-label" for="fatherOfTheGroomStyle">Father of the Groom's Color/
                                    Style:</label>
                                <div class="col-md-4">
                                    <input id="fatherOfTheGroomStyle" <?= $fatherOfTheGroomStyle ?>  name="fatherOfTheGroomStyle" type="text"
                                           placeholder="E.g.  Black, 201-1, etc.." class="form-control input-md">

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="fatherOfTheBrideSuit">Father of the Bride's Suit:</label>
                                <div class="col-md-4">
                                    <select id="fatherOfTheBrideSuit" name="fatherOfTheBrideSuit"
                                            onchange="ShowHideDiv('fatherOfTheBrideSuit','FOBS')" class="form-control">
                                        <option value="<?= $fatherOfTheBrideSuit ?>">Default - <?=$fatherOfTheBrideSuit?></option>

                                        <?php
                                        // use a while loop to fetch data
                                        // from the $all_categories variable
                                        // and individually display as an option
                                        while ($category = mysqli_fetch_array(
                                            $all_sqlSuits3, MYSQLI_ASSOC)):
                                            ?>
                                            <option value="<?php echo $category["s"];
                                            // The value we usually set is the primary key
                                            ?>">
                                                <?php echo $category["s"];
                                                // To show the category name to the user
                                                ?>
                                            </option>
                                        <?php
                                        endwhile;
                                        // While loop must be terminated
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="FOBS">
                                <label class="col-md-4 control-label" for="fatherOfTheBrideStyle">Father of the Bride's Color/
                                    Style:</label>
                                <div class="col-md-4">
                                    <input id="fatherOfTheBrideStyle"  value="<?= $fatherOfTheBrideStyle?>"name="fatherOfTheBrideStyle" type="text"
                                           placeholder="E.g.  Black, 201-1, etc.." class="form-control input-md">

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="submit"></label>
                                <div class="col-md-8">
                                    <button id="submit" type="submit" name="submit" class="btn dalt">Submit</button>
                                    <button id="reset" type="reset" name="reset" class="btn palt">Reset</button>
                                </div>
                            </div>

                        </form>


                    </div>


                </div>
            </div>
        </div>

    </div>


    <div class="row text-center">
        <a href="addGroom.php?ID=<?php echo $weddingID; ?>">
            <button type="button" id='g' class="btn dalt">Add Groom</button>
        </a>


        <a href="addGroomsman.php?ID=<?php echo $weddingID; ?>">
            <button type="button" id='gs' class="btn dalt">Add Groomsman</button>
        </a>


        <a href="addFatherOfTheGroom.php?ID=<?php echo $weddingID; ?>">
            <button type="button" id="FOG" class="btn dalt FOGB">Groom's Father</button>
        </a>


        <a href="addFatherOfTheBride.php?ID=<?php echo $weddingID; ?>">
            <button type="button" id="FOB" class=" btn dalt FOBB">Bride's Father</button>
        </a>
    </div>
    <div>
        <?php

        if ($groomSuit === 0 || $groomSuit === null || $groomSuit === "None" || $groomSuit === "") {
            $groomSuit = "-";
        }
        if ($groomStyle === 0 || $groomStyle === null || $groomStyle === "None" || $groomStyle === "") {
            $groomStyle = "-";
        }
        if ($groomsmenSuit === 0 || $groomsmenSuit === null || $groomsmenSuit === "None" || $groomsmenSuit === "") {
            $groomsmenSuit = "-";
        }
        if ($groomsmanStyle === 0 || $groomsmanStyle === null || $groomsmanStyle === "None" || $groomsmanStyle === "") {
            $groomsmanStyle = "-";
        }
        if ($fatherOfTheGroom === 0 || $fatherOfTheGroom === null || $fatherOfTheGroom === "None" || $fatherOfTheGroom === "") {
            $fatherOfTheGroom = "-";
        }
        if ($fatherOfTheGroomStyle === 0 || $fatherOfTheGroomStyle === null || $fatherOfTheGroomStyle === "None" || $fatherOfTheGroomStyle === "") {
            $fatherOfTheGroomStyle = "-";
        }
        if ($fatherOfTheBrideSuit === 0 || $fatherOfTheBrideSuit === null || $fatherOfTheBrideSuit === "None" || $fatherOfTheBrideSuit === "") {
            $fatherOfTheBrideSuit = "-";
        }
        if ($fatherOfTheBrideStyle === 0 || $fatherOfTheBrideStyle === null || $fatherOfTheBrideStyle === "None" || $fatherOfTheBrideStyle === "") {
            $fatherOfTheBrideStyle = "-";
        };



        ?>

        <table>
            <h2 class="heading2">Wedding Details</h2>
            <tr>
                <td><b>Handler:</b></td>
                <td colspan="3"><?= $salesPerson ?></td>
            </tr>

            <tr>
                <th>Groom Name</th>
                <th>Wedding Date</th>
                <th>Wedding Month</th>
                <th>Phone Number</th>
            </tr>

            <tbody>
            <tr class="info">
                <td><?= $groomName ?></td>
                <td><?= $weddingDate ?></td>
                <td><?= $weddingMonth ?></td>
                <td><?= $number ?></td>

            </tr>
            </tbody>
            <tr>
                <td><b>Email:</b></td>
                <td colspan="3"><a
                            href="mailto: <?php echo $email; ?>"> <?php echo $email; ?></a></td>
            </tr>
            <tr>
                <td><b>Notes: </b><button type="button" data-toggle="modal" class="btn palt" data-target="#editNotes">Edit</button> </td>
                <td colspan="3"><?= $notes ?></td>
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

            </tr>


        </table>
        <table>
            <h2 class="heading2">Suit Details</h2>
            <tr>
                <th>Groom's Suit</th>
                <th>Groomsman's Suit</th>
                <th>Groom's Father</th>
                <th>Bride's Father</th>
            </tr>
            <tr>
                <td><?= $groomSuit ?></td>
                <td><?= $groomsmenSuit ?></td>
                <td><?= $fatherOfTheGroom ?></td>
                <td><?= $fatherOfTheBrideSuit ?></td>
            </tr>
            <tr>
                <td><i><?= $groomStyle ?></i></td>
                <td><i><?= $groomsmanStyle ?></i></td>
                <td><i><?= $fatherOfTheGroomStyle ?></i></td>
                <td><i><?= $fatherOfTheBrideStyle ?></i></td>
            </tr>

        </table>


    </div>


    <!--            --><?php
    //            $query = "Select individual from weddingpartyattr where individual='Groom'and weddingID='$weddingID'";
    //            $query_run = mysqli_query($con, $query);
    //
    //            if (mysqli_num_rows($query_run) >= 1) {
    //
    //                echo "<script>document.getElementById('g').style.display = 'none';</script>";
    //            }
    //            else{
    //                echo "<script>document.getElementById('g').style.display = 'inline-block';</script>";
    //            }
    //
    //            $query1 = "Select individual from weddingpartyattr where individual='Father of the Groom' and weddingID='$weddingID'";
    //            $query_run1 = mysqli_query($con, $query1);
    //
    //            if (mysqli_num_rows($query_run1) >= 1) {
    //
    //                echo "<script>document.getElementById('FOG').style.display = 'none';</script>";
    //            }
    //            else{
    //                echo "<script>document.getElementById('FOG').style.display = 'inline-block';</script>";
    //            }
    //
    //            $query2 = "Select individual from weddingpartyattr where individual='Father of The Bride'and weddingID='$weddingID'";
    //            $query_run2 = mysqli_query($con, $query2);
    //
    //            if (mysqli_num_rows($query_run2) >= 1) {
    //
    //                echo "<script>document.getElementById('FOB').style.display = 'none';</script>";
    //            }
    //            else{
    //                echo "<script>document.getElementById('FOB').style.display = 'inline-block';</script>";
    //            }
    //


    switch ($fatherOfTheBrideSuit) {
        case '-':
            echo "<script>document.getElementById('FOB').style.display = 'none';</script>";
            break;
        default:
            break;
    }
    switch ($groomSuit) {
        case '-':
            echo "<script>document.getElementById('g').style.display = 'none';</script>";
            break;

        default:
            break;
    }
    switch ($groomsmenSuit) {
        case '-':
            echo "<script>document.getElementById('gs').style.display = 'none';</script>";
            break;
        default:
            break;
    }
    switch ($fatherOfTheGroom) {
        case '-':
            echo "<script>document.getElementById('FOG').style.display = 'none';</script>";
            break;
        default:
            break;
    }

    //            ?>
    <!---->

    <div>
        <table class="table">
            <h2 class="heading2">Customers</h2>

            <thead>
            <tr>
                <th scope="col">Individual</th>
                <th scope="col">Name</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $query = "Select * from weddingpartyattr join customerdetails c on weddingpartyattr.customerID = c.customerID where weddingID=" . $weddingID;
            $query_run = mysqli_query($con, $query);
            $p = 0;
            if (mysqli_num_rows($query_run) > 0) {
                foreach ($query_run as $items) {
                    ?>
                    <tr data-toggle="modal" data-target="#<?= $p . 'order' ?>">
                        <td><?= $items['individual']; ?></td>
                        <td><?= $items['name']; ?> </td>
                        <td><?= $items['status']; ?> </td>


                    </tr>
                    <div class="modal" id="<?= $p . 'order' ?>" tabindex="-1" role="dialog"
                         aria-labelledby="modalLabelLarge"
                         aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modalLabelLarge">Action</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row">
                                        <div class="col-xs-6">
                                            <a href="<?php echo "../customer/customerDetails.php?ID={$items['customerID']}"; ?>">
                                                <button class="btn palt">Customer Details</button>
                                            </a>
                                        </div>

                                        <div class="col-xs-6">
                                            <?php echo "<form method='post'>
                                    <input type='hidden' name='deleteID' value=" . $items['customerID'] . ">
                                    <input type='submit' class='btn  alt' onclick='return confirmDelete()' name='delete' value='Delete'>
                                </form> 

"; ?>
                                            <script>
                                                function confirmDelete() {
                                                    return confirm('Are you sure you want to delete this customer?');
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <?php $p += 1;
                }
            } else {
                ?>
                <tr>
                    <td colspan="3">No Record Found</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>


    </div>

    <form autocomplete="off" class="form-horizontal" method="post">

        <fieldset>
            <h4 class="heading2">Order Accessories</h4>

            <div class="form-group">
                <label class="col-md-4 control-label" for="quantity">Quantity:</label>
                <div class="col-md-4">
                    <input id="quantity" name="quantity" type="number" placeholder="Quantity"
                           class="form-control input-md">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="accessories">Brand:</label>

                <div class="col-md-4">
                    <select id="brand" name="brand" onchange="checkBrand()" class="form-control">
                        <option value="None">Select Below</option>
                        <?php
                        // use a while loop to fetch data
                        // from the $all_categories variable
                        // and individually display as an option
                        $sql = "SELECT * FROM `supplier` where accessories = 'Y'";
                        $all_sqlSuits1 = mysqli_query($con, $sql);
                        while ($category = mysqli_fetch_array(
                            $all_sqlSuits1, MYSQLI_ASSOC)):
                            ?>
                            <option value="<?php echo $category["supplier"];
                            // The value we usually set is the primary key
                            ?>">
                                <?php echo $category["supplier"];
                                // To show the category name to the user
                                ?>
                            </option>
                        <?php
                        endwhile;
                        // While loop must be terminated
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="stylea">Style:</label>
                <div class="col-md-4">
                    <input id="stylea" name="stylea" type="text" placeholder="Enter the Style"
                           class="form-control input-md"
                           required="">

                </div>
            </div>
            <div class="form-group" id="button">
                <label class="col-md-4 control-label" for="hold"></label>
                <div class="col-md-8">
                    <button id="hold" type="submit" name="hold" class="btn dalt" disabled>Place into Section</button>
                    <button class="btn dalt" type="button" onclick="inputName()" name="populate" id="populate"
                            data-toggle="modal" data-target="#largeShoes" disabled>
                        Order Accessories
                    </button>

                </div>
            </div>
            <div class="modal" id="largeShoes" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge"
                 aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="modalLabelLarge">Variants Confirmation</h4>
                        </div>
                        <div class="modal-body">
                            Groom Name: <?= $groomName ?><br>
                            Items: <span id="item"></span>

                            <br><br>
                            <button id="order" name="order" onclick="" class="btn palt">Add to Cart</button>
                        </div>
                    </div>
                </div>

            </div>
        </fieldset>
    </form>
    <div class="">
        <h4 class="heading2">Accessories Available</h4>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Brand</th>
                <th>Items</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $x = 0;
            $getHold = mysqli_query($con, "Select * from accessories where weddingID = '$ID'");
            if (mysqli_num_rows($getHold) > 0) {
                foreach ($getHold as $items) {
                    ?>
                    <tr data-toggle="modal" data-target="#<?= $x . 'acc' ?>">

                        <td><?= $items['brand']; ?></td>
                        <td><?= $items['items']; ?></td>
                        <td><?= $items['status']; ?></td>
                    </tr>
                    <div class="modal" id="<?= $x . 'acc' ?>" tabindex="-1" role="dialog"
                         aria-labelledby="modalLabelLarge"
                         aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modalLabelLarge">Action</h4>
                                </div>
                                <div class="modal-body">
                                    Item: <?= $items['items']; ?><br><br>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <?php echo "<form method='post'>
                                    <input type='hidden' name='deleteAID' value=" . $items['accID'] . ">
                                    <input type='submit' class='btn dalt' name='deleteA' value='Delete'>
                                </form> 

"; ?></div>

                                        <div class="col-xs-6">
                                            <?php echo "<form method='post'>
                                    <input type='hidden' name='pickupID' value=" . $items['accID'] . ">
                                    <input type='submit' class='btn palt' name='pickup' value='Pick-Up'>
                                </form>

"; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <?php
                    $x += 1;
                }
            }
            ?>


            </tbody>
        </table>
    </div>
    <script>
        function checkBrand() {
            $brand = document.getElementById('brand').value;
            if ($brand === "None") {
                document.getElementById('hold').disabled = true;
                document.getElementById('populate').disabled = true;
            } else {
                document.getElementById('populate').disabled = false;
                document.getElementById('hold').disabled = false;

            }

        }
    </script>

    <form method="post" class="right">
        <input name="deleteParty" type="submit" class="btn palt" value="Delete this Party">
    </form>
    <br>
</div>
</body>
<script>
    function inputName() {
        let quantity = document.getElementById("quantity").value;
        let style = document.getElementById("stylea").value;
        if (quantity === "" || style === "") {
            item = "Error, please fill out information";
        } else {
            item = quantity + " x (" + style + ")";
        }
        document.getElementById("item").innerHTML = item;
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const rows = document.querySelectorAll("tr[data-href]");
        rows.forEach(row => {
            row.addEventListener("click", () => {
                window.location.href = row.dataset.href;
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const rows = document.querySelectorAll("tr[data-target]");
        rows.forEach(row => {
            row.addEventListener("click", () => {
                window.location.target = row.dataset.target;
            });
        });
    });
</script>
<!--<script type="text/javascript">-->
<!--    function ShowHideDiv($id, $idn) {-->
<!--        let a = document.getElementById($id);-->
<!--        let b = document.getElementById($idn);-->
<!---->
<!---->
<!--        b.style.display = a.value !== "None" ? "block" : "none";-->
<!--    }-->
<!---->
<!--</script>-->
<script src="../jquery.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>

