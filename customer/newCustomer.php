<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$sqlSalesPerson = "SELECT sName FROM `salesperson`";
$all_SalesPerson = mysqli_query($con, $sqlSalesPerson);
$types = mysqli_query($con, "Select * from type");

if (isset($_POST['hold'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $salesPerson = mysqli_real_escape_string($con, $_POST['salesPerson']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $Style = mysqli_real_escape_string($con, $_POST['Style']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $vest = mysqli_real_escape_string($con, $_POST['vest']);
    $shirtSize = mysqli_real_escape_string($con, $_POST['shirtSize']);
    $shirt = mysqli_real_escape_string($con, $_POST['shirt']);
    $PantSize = mysqli_real_escape_string($con, $_POST['PantSize']);
    $SuitSize = mysqli_real_escape_string($con, $_POST['SuitSize']);
    $suitFit = mysqli_real_escape_string($con, $_POST['suitFit']);
    $cusIDSQL = mysqli_query($con, "SELECT MAX(customerID) as 'cus' FROM customerdetails");
    if ($PantSize == "" && $type == "Suit") {
        $PantSize = (int)$SuitSize - 7;
    }
    while ($rowData = ($cusIDSQL)->fetch_assoc()) {

        $cusID = $rowData['cus'];
        $cusID = $cusID + 1;
        $item = $Style . " " . $SuitSize . " " . $suitFit;

        switch ($type) {
            case "Suit":
                if ($PantSize === "") {
                    $item = $item . " + " . ((int)$SuitSize - 7) . ' Waist ';
                } else {
                    $item = $item . " + " . $PantSize . " " . 'Waist ';

                }
                if ($vest !== "") {
                    $item = $item . " + " . $vest . ' Vest ';
                }

        }
        $brandT = strtolower($brand);

        if ($type == "Suit") {
            $SuitSize = $SuitSize . " " . $suitFit;
        }
        $date = mysqli_real_escape_string($con, date("Y/m/d"));
        $status = mysqli_real_escape_string($con, "Hold");
        $histroy = mysqli_real_escape_string($con, "New," . $status);

        $cus_insert = "insert into customerdetails (customerID, customerName, customerNumber, type, 
                             suit, suitColor, suitSize, suitFit, pantSize, shirtType, shirtSize, vest, salesPerson, status, date, notes,histroy) VALUES 
                             ('$cusID','$name','$number','$type','$brand','$Style','$SuitSize','$suitFit','$PantSize','$shirt','$shirtSize','$vest','$salesPerson','$status','$date','$notes','$histroy')";

        if (mysqli_query($con, $cus_insert)) {
            mysqli_query($con, "Insert into section (sectionID, sectionName, customerId, customerName, items) VALUES ('0','Hold','$cusID','$name','$item')");
            header('Location: customerDetails.php?ID=' . $cusID);
            exit();
        };


    };


}

if (isset($_POST['order'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $salesPerson = mysqli_real_escape_string($con, $_POST['salesPerson']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $Style = mysqli_real_escape_string($con, $_POST['Style']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $vest = mysqli_real_escape_string($con, $_POST['vest']);
    $shirtSize = mysqli_real_escape_string($con, $_POST['shirtSize']);
    $shirt = mysqli_real_escape_string($con, $_POST['shirt']);
    $PantSize = mysqli_real_escape_string($con, $_POST['PantSize']);
    $SuitSize = mysqli_real_escape_string($con, $_POST['SuitSize']);
    $suitFit = mysqli_real_escape_string($con, $_POST['suitFit']);

    $getorder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$brand') and status = 'open'");
    while ($rowData = ($getorder)->fetch_assoc()) {
        $POID = $rowData['poid'];
    }

    $cusIDSQL = mysqli_query($con, "SELECT MAX(customerID) as 'cus' FROM customerdetails");
    if ($PantSize == "" && $type == "Suit") {
        $PantSize = (int)$SuitSize - 7;
    }
    while ($rowData = ($cusIDSQL)->fetch_assoc()) {

        $cusID = $rowData['cus'];
        $cusID = $cusID + 1;

        $orderStatus = "New";
        $item = $Style . " " . $SuitSize . " " . $suitFit;

        switch ($type) {
            case "Suit":
                if ($PantSize === "") {
                    $item = $item . " + " . ((int)$SuitSize - 7) . ' Waist ';
                } else {
                    $item = $item . " + " . $PantSize . " " . 'Waist ';

                }
                if ($vest !== "") {
                    $item = $item . " + " . $vest . ' Vest ';
                }

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

        $sqlOrder = "insert into `$brand` values ('$POID','$max','$cusID','$name','$item','$orderStatus','Walk-in Customer','')";
        mysqli_query($con, $sqlOrder);


        if ($type == "Suit") {
            $SuitSize = $SuitSize . " " . $suitFit;
        }
        $date = mysqli_real_escape_string($con, date("Y/m/d"));
        $status = mysqli_real_escape_string($con, "Ordered -> ({$POID})");
        $histroy = mysqli_real_escape_string($con, "New," . $status);
        $cus_insert = "insert into customerdetails (customerID, customerName, customerNumber, type, 
                             suit, suitColor, suitSize, suitFit, pantSize, shirtType, shirtSize, vest, salesPerson, status, date, notes,histroy) VALUES 
                             ('$cusID','$name','$number','$type','$brand','$Style','$SuitSize','$suitFit','$PantSize','$shirt','$shirtSize','$vest','$salesPerson','$status','$date','$notes','$histroy')";

        if (mysqli_query($con, $cus_insert)) {
            $prefix = substr($POID, 0, 3);
            $sqlPrefix = mysqli_query($con, "Select prefix from supplier where supplier = '$brand'");
            while ($rowData = ($sqlPrefix)->fetch_assoc()) {
                $prefix = $rowData['prefix'];
            }
            header('Location: ../orders/purchaseOrder.php?NAME=' . $prefix);
            exit();
        };


    };


}

if (isset($_POST['newAlt'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $salesPerson = mysqli_real_escape_string($con, $_POST['salesPerson']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $brand = mysqli_real_escape_string($con, $_POST['brand']);
    $Style = mysqli_real_escape_string($con, $_POST['Style']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $vest = mysqli_real_escape_string($con, $_POST['vest']);
    $shirtSize = mysqli_real_escape_string($con, $_POST['shirtSize']);
    $shirt = mysqli_real_escape_string($con, $_POST['shirt']);
    $PantSize = mysqli_real_escape_string($con, $_POST['PantSize']);
    $SuitSize = mysqli_real_escape_string($con, $_POST['SuitSize']);
    $suitFit = mysqli_real_escape_string($con, $_POST['suitFit']);

    $cusIDSQL = mysqli_query($con, "SELECT MAX(customerID) as 'cus' FROM customerdetails");
    if ($PantSize == "" && $type == "Suit") {
        $PantSize = (int)$SuitSize - 7;
    }

    while ($rowData = ($cusIDSQL)->fetch_assoc()) {

        $cusID = $rowData['cus'];
        $cusID = $cusID + 1;
        if ($type == "Suit") {
            $suitSize = $suitSize . " " . $suitFit;
        }
        $date = mysqli_real_escape_string($con, date("Y/m/d"));
        $status = mysqli_real_escape_string($con, "New");
        $histroy = mysqli_real_escape_string($con, $status);
        $cus_insert = "insert into customerdetails (customerID, customerName, customerNumber, type, 
                             suit, suitColor, suitSize, suitFit, pantSize, shirtType, 
                             shirtSize, vest, salesPerson, status, date, notes,histroy) VALUES 
                             ('$cusID','$name','$number','$type','$brand','$Style','$SuitSize','$suitFit','$PantSize','$shirt','$shirtSize','$vest','$salesPerson','$status','$date','$notes','$histroy')";

        if (mysqli_query($con, $cus_insert)) {
            header("Location: ../alteration/alteration.php?ID=" . $cusID);
            exit();
        };


    };


};
$sqlSuits = "Select supplier as s from supplier";
$all_sqlSuits = mysqli_query($con, $sqlSuits);
$sqlShirt = "SELECT * FROM `shirts`";
$all_shirts = mysqli_query($con, $sqlShirt);
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Customer Form</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="css/super-hero-bootstrap.min.css" rel="stylesheet">-->
    <!-- Optional theme -->


    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="customer.css">


    <script type="text/javascript">


        function noBack() {


        }
    </script>
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
                <li class="active"><a href="#">New Customer Form<span class="sr-only"></span></a></li>
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
                        <li><a href="#">New Customer Form</a></li>


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
    <form class="form-horizontal" autocomplete="off" method="post">
        <fieldset>

            <!-- Form Name -->
            <div class="right">
                <a class="right" href="customer.php">
                    <button id="weddingParty" type="button" name="weddingParty" class="btn btn-lg">Customer Search
                    </button>
                </a>
            </div>
            <h4 class="text-center heading">Add Customer</h4>


            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="name">Full name:</label>
                <div class="col-md-4">
                    <input id="fullName" name="name" type="text" placeholder="" class="form-control input-md"
                           required="">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="number">Phone Number:</label>
                <div class="col-md-4">
                    <input id="number" name="number" type="number" placeholder="" class="form-control input-md"
                           required="">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="type">Item:</label>
                <div class="col-md-4">
                    <select id="type" name="type" onchange="process()" class="form-control">
                        <?php
                        // use a while loop to fetch data
                        // from the $all_categories variable
                        // and individually display as an option
                        while ($category = mysqli_fetch_array(
                            $types, MYSQLI_ASSOC)):
                            ?>
                            <option value="<?php echo $category["type"];
                            // The value we usually set is the primary key
                            ?>">
                                <?php echo $category["type"];
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
            <div class="form-group" id="brand" style="display: none">
                <label class="col-md-4 control-label" for="brand">Brand:</label>
                <div class="col-md-4">
                    <select id="brand" name="brand" class="form-control">
                        <option value="None">None</option>
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

            <div class="form-group" id="color" style="display: none">
                <label class="col-md-4 control-label" for="Style">Style / Color:</label>
                <div class="col-md-4">
                    <input id="Style" name="Style" type="text" placeholder="E.g: 201-1, Black, Blue plaid "
                           class="form-control input-md" required="">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group" id="suit" style="display: none">
                <label class="col-md-4 control-label" id="label" for="SuitSize"></label>
                <div class="col-md-4">
                    <input id="SuitSize" name="SuitSize" type="number" placeholder="" class="form-control input-md">

                </div>
            </div>
            <div class="form-group" id="suitFit" style="display: none">
                <label class="col-md-4 control-label" for="suitFit">Suit Fit:</label>
                <div class="col-md-4">
                    <select id="suitFitT" name="suitFit" class="form-control">
                        <option value="Regular" selected>Regular</option>
                        <option value="Short">Short</option>
                        <option value="Tall">Tall</option>
                    </select>
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group" id="pant" style="display: none">
                <label class="col-md-4 control-label" for="PantSize">Pant Size:</label>
                <div class="col-md-4">
                    <input id="PantSize" name="PantSize" type="text" placeholder="" class="form-control input-md">

                </div>
            </div>
            <div class="form-group" id="shirt" style="display: none">
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
            <div class="form-group" id="vest" style="display: none">
                <label class="col-md-4 control-label" for="vest">Vest Size: </label>
                <div class="col-md-4">
                    <input id="vestT" name="vest" type="number" placeholder="Leave blank if not Applicable"
                           class="form-control input-md">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="salesPerson">Sales Person:</label>
                <div class="col-md-4">
                    <select id="salesPerson" name="salesPerson" class="form-control">
                        <?php
                        // use a while loop to fetch data
                        // from the $all_categories variable
                        // and individually display as an option
                        while ($category = mysqli_fetch_array(
                            $all_SalesPerson, MYSQLI_ASSOC)):
                            ?>
                            <option value="<?php echo $category["sName"];
                            // The value we usually set is the primary key
                            ?>">
                                <?php echo $category["sName"];
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
            <!-- Textarea -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="notes">Notes:</label>
                <div class="col-md-4">
                    <textarea class="form-control" id="notes" name="notes"></textarea>
                </div>
            </div>
            <!-- Button (Double) -->
            <div class="form-group" id="button" style="display: none">
                <label class="col-md-4 control-label" for="hold"></label>
                <div class="col-md-8">
                    <button id="hold" type="submit" name="hold" class="btn dalt">Hold</button>
                    <button id="newAlt" name="newAlt" class="btn dalt">New Alteration</button>
                    <button class="btn dalt" type="button" onclick="inputName()" name="populate"
                            data-toggle="modal" data-target="#largeShoes">
                        Order Suit
                    </button>

                </div>
            </div>

        </fieldset>
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
                        Customer Name: <span id="name"></span><br>
                        Items: <span id="items"></span>

                        <br><br>
                        <button id="order" name="order" class="btn palt">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

</body>

<script type="text/javascript">
    function inputName() {
        document.getElementById('name').innerHTML = document.getElementById('fullName').value;
        let type = document.getElementById("type").value;
        let suitSize = document.getElementById("SuitSize").value;
        let suitFit = document.getElementById("suitFitT").value;
        let pant = document.getElementById("PantSize").value;
        let vest = document.getElementById("vestT").value;
        let brand = document.getElementById("brand").value;
        let color = document.getElementById("Style").value;

        let item = color + " " + suitSize + " " + suitFit;
        switch (type) {
            case "Suit":
                if (pant === "") {
                    item = item + " + " + (parseInt(suitSize) - 7) + ' Waist ';
                } else {
                    item = item + " + " + pant + " " + 'Waist ';

                }
                if (vest !== "") {
                    item = item + " + " + vest + ' Vest ';
                }


        }

        document.getElementById("items").innerHTML = item;
    }

    function ShowHideDiv($id, $idn) {
        let a = document.getElementById($id);
        let b = document.getElementById($idn);
        b.style.display = a.value !== "None" ? "block" : "none";
    }

    function process() {

        let suit = document.getElementById("suit");
        let suitFit = document.getElementById("suitFit");
        let pant = document.getElementById("pant");
        let shirt = document.getElementById("shirt");
        let vest = document.getElementById("vest");
        let brand = document.getElementById("brand");
        let color = document.getElementById("color");
        let button = document.getElementById("button");

        let type = document.getElementById("type").value;

        switch (type) {
            case "Suit":
                suitFit.style.display = "block";
                pant.style.display = "block";
                suit.style.display = "block";
                shirt.style.display = "block";
                vest.style.display = "block";
                brand.style.display = "block";

                color.style.display = "block";
                button.style.display = "block";
                document.getElementById('label').innerHTML = "Suit Size: "
                document.getElementsByName('PantSize')[0].placeholder = "Default 7 inch drop";


                break;
            case "Blazzer":
                brand.style.display = "block";

                suitFit.style.display = "none";
                pant.style.display = "block";
                suit.style.display = "block";
                shirt.style.display = "block";
                vest.style.display = "block";
                color.style.display = "block";
                button.style.display = "block";
                document.getElementById('label').innerHTML = "Blazzer Size: ";

                document.getElementsByName('PantSize')[0].placeholder = "Enter pant size";


                break;
            case "Shirt":
                brand.style.display = "block";

                suitFit.style.display = "none";
                pant.style.display = "block";
                suit.style.display = "none";
                shirt.style.display = "block";
                vest.style.display = "block";
                color.style.display = "block";
                button.style.display = "block";
                document.getElementsByName('PantSize')[0].placeholder = "Enter pant size";


                break;
            case "Pant":
                brand.style.display = "block";

                suitFit.style.display = "none";
                pant.style.display = "block";
                suit.style.display = "none";
                shirt.style.display = "none";
                vest.style.display = "block";
                color.style.display = "block";
                button.style.display = "block";
                document.getElementsByName('PantSize')[0].placeholder = "Enter pant size";


                break;
            case "Vest":
                brand.style.display = "block";

                suitFit.style.display = "none";
                pant.style.display = "none";
                suit.style.display = "none";
                shirt.style.display = "none";
                vest.style.display = "block";
                color.style.display = "block";
                button.style.display = "block";
                break;

            default:
                brand.style.display = "block";

                suitFit.style.display = "none";
                pant.style.display = "none";
                suit.style.display = "none";
                shirt.style.display = "none";
                vest.style.display = "none";
                color.style.display = "none";
                button.style.display = "none";

                break;


        }
    }
</script>
<script src="../jquery.js"></script>

<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>