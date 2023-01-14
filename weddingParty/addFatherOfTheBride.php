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
    $groomSuit = $rowData['groomSuit'];
    $groomsmenSuit = $rowData['groomsmenSuit'];
    $groomsmanStyle = $rowData['fatherOfTheBrideStyle'];
    $fatherOfTheGroom = $rowData['fatherOfTheGroom'];
    $fatherOfTheBrideSuit = $rowData['fatherOfTheBrideSuit'];
    $salesPerson = $rowData['salesPerson'];
    $notes = $rowData['notes'];
}
$sqlShirt = "SELECT * FROM `shirts`;";
$all_shirts = mysqli_query($con, $sqlShirt);


if (isset($_POST['submit'])) {
    $individual = mysqli_real_escape_string($con, "Father of the Bride");
    $name = mysqli_real_escape_string($con, $_POST['Name']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $suit = mysqli_real_escape_string($con, $_POST['suit']);
    $suitDetials = mysqli_real_escape_string($con, $_POST['Style']);
    $suitSize = mysqli_real_escape_string($con, $_POST['SuitSize']);
    $pantSize = mysqli_real_escape_string($con, $_POST['PantSize']);

    if ($pantSize == "") {
        $pantSize = $suitSize - 7;
    }
    $suitFit = mysqli_real_escape_string($con, $_POST['suitFit']);
    $suitSize = $suitSize . " " . $suitFit;
    $shirtType = mysqli_real_escape_string($con, $_POST['shirt']);
    $shirtSize = mysqli_real_escape_string($con, $_POST['shirtSize']);
    $vestSize = mysqli_real_escape_string($con, $_POST['VestSize']);
    if ($vestSize == "") {
        $vestSize = "None";
    }
    if ($suitSize == "0" || $suitSize == "") {
        $pantSize = 0;
    }
    $date = mysqli_real_escape_string($con, date("Y/m/d"));
    $cusIDSQL = mysqli_query($con, "SELECT MAX(customerID) as 'cus' FROM customerdetails");


    while ($rowData = ($cusIDSQL)->fetch_assoc()) {
        $cusID = $rowData['cus'];

    };
    $cusID = $cusID + 1;
    $item = $suitDetials . " " . $suitSize . " " . $suitFit;

    if ($pantSize === "") {
        $item = $item . " + " . ((int)$suitSize - 7) . ' Waist';
    } else {
        $item = $item . " + " . $pantSize . " " . 'Waist';

    }
    if ($vestSize !== "None") {
        $item = $item . " + " . $vestSize . ' Vest';
    }

    $getSection = mysqli_query($con, "Select sectionAttr.name, sectionID from sectionattr join weddingform on name = weddingform.section join weddingpartyattr w on weddingform.weddingID = w.weddingID where weddingform.weddingID = '$weddingID'");
    while ($rowData = ($getSection)->fetch_assoc()) {
        $section = $rowData['name'];
        $sectionID = $rowData['sectionID'];
    }
    $sql_insert = "insert into weddingpartyattr (customerID,weddingID, individual, name, number,date,notes) VALUES 
              ('$cusID','$ID','$individual','$name','$number','$date','$notes')";

    $date = mysqli_real_escape_string($con, date("Y/m/d"));
    $status = mysqli_real_escape_string($con, $section);
    $histroy = mysqli_real_escape_string($con,"New,". $status);

    $type = mysqli_escape_string($con, "Suit");
    $cus_insert = "insert into customerdetails (customerID, customerName, customerNumber, status, date,notes,salesPerson,suit,suitSize,suitColor,suitFit,pantSize,shirtType,shirtSize,vest,type,histroy)
VALUES ('$cusID','$name','$number','$status','$date','$notes','$salesPerson','$suit','$suitSize','$suitDetials','$suitFit','$pantSize','$shirtType','$shirtSize','$vestSize','$type','$histroy')";


    if (mysqli_query($con, $sql_insert)) {
        mysqli_query($con, "insert into section (sectionID, sectionName, customerId, customerName, items) VALUES ('$sectionID','$section','$cusID','$name','$item')");
        mysqli_query($con, $cus_insert);
        header("Location: weddingPartyAttr.php?ID=" . $ID);
        exit();
    };


}

if (isset($_POST['order'])) {
    $individual = mysqli_real_escape_string($con, "Father of the Bride");
    $name = mysqli_real_escape_string($con, $_POST['Name']);
    $suit = mysqli_real_escape_string($con, $_POST['suit']);
    $suitDetials = mysqli_real_escape_string($con, $_POST['Style']);
    $suitSize = mysqli_real_escape_string($con, $_POST['SuitSize']);
    $pantSize = mysqli_real_escape_string($con, $_POST['PantSize']);
    $suitFit = mysqli_real_escape_string($con, $_POST['suitFit']);
    $vest = mysqli_real_escape_string($con, $_POST['VestSize']);

    $notes = $groomName . "'s Party (Father of the Bride)";
    if ($pantSize == "") {
        $pantSize = $suitSize - 7;
    }
    if ($suitSize == "0" || $suitSize == "") {
        $pantSize = 0;
    }
    $getorder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$suit') and status = 'open'");
    while ($rowData = ($getorder)->fetch_assoc()) {
        $POID = $rowData['poid'];

        $cusIDSQL = mysqli_query($con, "SELECT MAX(customerID) as 'cus' FROM customerdetails");
        if ($pantSize == "") {
            $pantSize = $suitSize - 7;
        }
        while ($rowData = ($cusIDSQL)->fetch_assoc()) {

            $cusID = $rowData['cus'];


        }
        $cusID = $cusID + 1;

        $orderStatus = "New";
        $item = $suitDetials . " " . $suitSize . " " . $suitFit;

        if ($pantSize === "") {
            $item = $item . " + " . ((int)$suitSize - 7) . ' Waist';
        } else {
            $item = $item . " + " . $pantSize . " " . 'Waist';

        }
        if ($vest !== "") {
            $item = $item . " + " . $vest . ' Vest';
        }


        $brandT = strtolower($suit);


        $sqlPrefix = mysqli_query($con, "Select prefix from supplier where supplier = '$brandT'");
        while ($rowData = ($sqlPrefix)->fetch_assoc()) {
            $prefix = $rowData['prefix'];
        }


        $sqlCount = mysqli_query($con, "SELECT max(cast(substr(orderNumber,5,100) as integer)) as 'max' FROM `$brandT` WHERE poID like '$POID';");
        while ($rowData = ($sqlCount)->fetch_assoc()) {
            $max =  $rowData['max'];
        }



        $max += 1;


        $max = $prefix . "#" . $max;
        $orderNotes = mysqli_real_escape_string($con, "Father of the Bride - " . $groomName . "'s Party");;

        $sqlOrder = "insert into `$brandT` values ('$POID','$max','$cusID','$name','$item','$orderStatus','$orderNotes','')";
        mysqli_query($con, $sqlOrder);

    }
    $individual = mysqli_real_escape_string($con, "Father of the Bride");
    $name = mysqli_real_escape_string($con, $_POST['Name']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $suit = mysqli_real_escape_string($con, $_POST['suit']);
    $suitDetials = mysqli_real_escape_string($con, $_POST['Style']);
    $suitSize = mysqli_real_escape_string($con, $_POST['SuitSize']);
    $pantSize = mysqli_real_escape_string($con, $_POST['PantSize']);
    $number = mysqli_real_escape_string($con, $_POST['number']);


    if ($pantSize == "") {
        $pantSize = $suitSize - 7;
    }
    if ($suitSize == "0" || $suitSize == "") {
        $pantSize = 0;
    }


    $suitFit = mysqli_real_escape_string($con, $_POST['suitFit']);
    $suitSize = $suitSize . " " . $suitFit;
    $shirtType = mysqli_real_escape_string($con, $_POST['shirt']);
    $shirtSize = mysqli_real_escape_string($con, $_POST['shirtSize']);
    $vest = mysqli_real_escape_string($con, $_POST['VestSize']);
    if ($vest == "") {
        $vest = "None";
    }
    $date = mysqli_real_escape_string($con, date("Y/m/d"));
    $cusIDSQL = mysqli_query($con, "SELECT MAX(customerID) as 'cus' FROM customerdetails");


    while ($rowData = ($cusIDSQL)->fetch_assoc()) {
        $cusID = $rowData['cus'];
        $cusID = $cusID + 1;

        $sql_insert = "insert into weddingpartyattr (customerID,weddingID, individual, name, number,date,notes) VALUES 
              ('$cusID','$ID','$individual','$name','$number','$date','$notes')";

        $date = mysqli_real_escape_string($con, date("Y/m/d"));
        $status = mysqli_real_escape_string($con, "Ordered " . $POID . "(" . $max . ")");
        $histroy = mysqli_real_escape_string($con,"New,". $status);

        $type = mysqli_escape_string($con, "Suit");
        $cus_insert = "insert into customerdetails (customerID, customerName, customerNumber, status, date,notes,salesPerson,suit,suitSize,suitColor,suitFit,pantSize,shirtType,shirtSize,vest,type,histroy)
VALUES ('$cusID','$name','$number','$status','$date','$notes','$salesPerson','$suit','$suitSize','$suitDetials','$suitFit','$pantSize','$shirtType','$shirtSize','$vest','$type','$histroy')";


        if (mysqli_query($con, $sql_insert)) {
            mysqli_query($con, $cus_insert);
            header('Location: ../orders/purchaseOrder.php?NAME=' . $prefix);
            exit();
        };


    };
}

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
    <link rel="stylesheet" href="wedding.css">

 <script type="text/javascript">



        function noBack() {



        }
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
                <li class="active"><a href="#">Groomsman - <?php echo $groomName ?>'s party <span
                                class="sr-only"></span></a></li>
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
    <script type="text/javascript">

        function ShowHideDiv($id, $idn) {
            let a = document.getElementById($id);
            let b = document.getElementById($idn);
            b.style.display = a.value !== "None" ? "block" : "none";
        }


    </script>
    <form autocomplete="off" method="post" class="form-horizontal">
        <fieldset>

            <!-- Form Name -->
            <div class="right">
                <a class="right" href="weddingPartyAttr.php?ID=<?= $weddingID?>">
                    <button id="weddingParty" type="button" name="weddingParty" class="btn btn-lg"><?= $groomName?>'s Party</button>
                </a>
            </div>
            <h4 class="text-center heading">Add Bride's Father Details</h4>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="Name">Father of the Bride's Name:</label>
                <div class="col-md-4">
                    <input id="Name" name="Name" type="text" placeholder="" class="form-control input-md" required="">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="number">Father of the Bride's Number:</label>
                <div class="col-md-4">
                    <input id="number" name="number" type="number" placeholder="" class="form-control input-md"
                           required="">

                </div>
            </div>


            <?php
            $sqlSuits = "Select supplier as s from supplier";
            $all_sqlSuits = mysqli_query($con, $sqlSuits);
            ?>
            <div class="form-group">
                <label class="col-md-4 control-label" for="brand">Brand:</label>
                <div class="col-md-4">
                    <select id="suit" name="suit" class="form-control">
                        <option value="<?php echo $fatherOfTheBrideSuit ?>"><?php echo $fatherOfTheBrideSuit ?> -
                            Default
                        </option>
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

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="Style">Style / Color:</label>
                <div class="col-md-4">
                    <input id="Style" name="Style" type="text" value="<?php echo $groomsmanStyle ?>"
                           class="form-control input-md" required="">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="SuitSize">Suit Size: </label>
                <div class="col-md-4">
                    <input id="SuitSize" name="SuitSize" type="number" placeholder="" class="form-control input-md"
                           required="">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="suitFit">Suit Fit:</label>
                <div class="col-md-4">
                    <select id="suitFit" name="suitFit" class="form-control">
                        <option value="Regular">Regular</option>
                        <option value="Short">Short</option>
                        <option value="Tall">Tall</option>
                    </select>
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="PantSize">Pant Size:</label>
                <div class="col-md-4">
                    <input id="PantSize" name="PantSize" type="text" placeholder="Default 7 inch drop"
                           class="form-control input-md">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="shirt">Shirt Style:</label>
                <div class="col-md-4">
                    <select id="shirt" name="shirt" class="form-control" onchange="ShowHideDiv('shirt','SS')">
                        <?php
                        // use a while loop to fetch data
                        // from the $all_categories variable
                        // and individually display as an option
                        while ($category = mysqli_fetch_array(
                            $all_shirts, MYSQLI_ASSOC)):
                            ?>
                            <option value="<?php echo $category["shirtsBrand"];
                            // The value we usually set is the primary key
                            ?>">
                                <?php echo $category["shirtsBrand"];
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

            <!-- Text input-->
            <div class="form-group" id="SS" style="display: none">
                <label class="col-md-4 control-label" for="shirtSize">Shirt Size:</label>
                <div class="col-md-4">
                    <input id="shirtSize" name="shirtSize" type="text" placeholder="" class="form-control input-md">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="VestSize">Vest Size: </label>
                <div class="col-md-4">
                    <input id="VestSize" name="VestSize" type="number" placeholder="" class="form-control input-md">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="notes">Notes:</label>
                <div class="col-md-4">
                    <textarea class="form-control" id="notes" name="notes"></textarea>
                </div>
            </div>

            <!-- Button -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="submitGroom"></label>
                <div class="col-md-4">
                    <button id="submit" name="submit" class="btn palt">Place in Section</button>
                    <button id="order" name="order" class="btn dalt">Order Suit</button>

                </div>
            </div>


        </fieldset>
    </form>


</div>
</body>
</html>






