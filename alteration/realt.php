<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$totalCost = 0;
$con = mysqli_connect("localhost", "root", "", "4men");
$ID = $_GET['ID'];



if (isset($_REQUEST['altButtons'])) {
    $altButtons = mysqli_real_escape_string($con, $_POST['altButtons']);
    mysqli_query($con, "insert into retempalt (cusID, alterationTypeID) VALUES ('$ID','$altButtons')");
    header('Location: realt.php?ID=' . $ID);
}
$sqlName = mysqli_query($con, "Select customerName from customerdetails where customerID =" . $ID);
while ($rowData = ($sqlName)->fetch_assoc()) {
    $name = $rowData['customerName'];
}

$sqlPrintRecord = mysqli_query($con, "select * from alterationtype join retempalt t on alterationtype.alterationTypeID = t.alterationTypeID where t.cusID=" . $ID);
$total = 0;

$cusIDSQL = mysqli_query($con, "SELECT * from alteration where customerID = '$ID'");
while ($rowData = ($cusIDSQL)->fetch_assoc()) {
    $AltID = $rowData['alterationID'];
}

if (isset($_REQUEST['saveAlt'])) {

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

    mysqli_query($con,"Delete from realt where customerID = '$ID'");
    mysqli_query($con, "update customerDetails set status='Completed',histroy  =  concat(histroy,',','Completed') where customerID = '$ID'");
    header('Location: ../customer/customerDetails.php?ID=' . $ID);
}


if (isset($_REQUEST['delete'])) {
    $deleteTempAlt = "delete from retempalt where alterationTypeID = {$_REQUEST['deleteID']}";
    mysqli_query($con, $deleteTempAlt);
}
$sqlCount = mysqli_query($con, "Select count(customerID) as 'c' from realt where customerID=" . $ID);
while ($rowData = ($sqlCount)->fetch_assoc()) {
    $count = $rowData['c'];
}

if (isset($_POST['submit'])) {
    mysqli_query($con,"delete from realt where customerID = ". $ID);
    $aPrice = array();
    $aName = array();
    $query = mysqli_query($con, "Select alterationTypeID from retempalt where cusID = " . $ID);

    if (mysqli_num_rows($query) > 0) {
        foreach ($query as $items) {
            $query2 = mysqli_query($con, 'Select * from alterationtype where alterationTypeID =' . $items['alterationTypeID']);
            while ($rowData = ($query2)->fetch_assoc()) {
                $alt = $rowData['aName'];
                $cost = $rowData['aPrice'];
            }
            $aPrice[] = (int)$cost;
            $aName[] = $alt;
            $totalCost += (int)$cost;
        }
    }
    $totalCost = array_sum($aPrice);
    $aPrice = implode(",", $aPrice);
    $aName = implode(",", $aName);


    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $tailorID = mysqli_real_escape_string($con, $_POST['tailorID']);
    if ($tailorID == "default") {
        $tailorID = "";
    }
    $pickupDate = mysqli_real_escape_string($con, $_POST['pickupDate']);
    $tailorDate = mysqli_real_escape_string($con, $_POST['taylorDate']);
    $sqlInsertAlt = "insert into realt values ('$AltID','$ID','$aName','$aPrice','$totalCost','$tailorDate','$pickupDate','$notes','Re-Altering','$tailorID')";
    if ($count > 0) {
        mysqli_query($con, "update realt set realt ='$aName', cost = '$aPrice', totalCost = '$total', tailorDate = '$tailorDate', pickUpDate = '$pickupDate', notes = '$notes', status='Re-Altering', tailor = '$tailorID' WHERE `realt`.`customerID`= " . $ID);
        mysqli_query($con, "delete from retempalt where cusID=" . $ID);
        mysqli_query($con, "update customerdetails set status = 'Re-Altering',histroy  =  concat(histroy,',','Re-Altering') WHERE `customerdetails`.`customerID` =" . $ID);
        header('Location: ../customer/customerDetails.php?ID=' . $ID);

    } else {
        if (mysqli_query($con, $sqlInsertAlt)) {
            mysqli_query($con, "delete from retempalt where cusID=" . $ID);
            mysqli_query($con, "update customerdetails set status = 'Re-Altering',histroy  =  concat(histroy,',','Re-Altering') WHERE `customerdetails`.`customerID` = '$ID'");
            header('Location: ../customer/customerDetails.php?ID=' . $ID);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $name ?>'s Re-Alteration</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="css/super-hero-bootstrap.min.css" rel="stylesheet">-->
    <!-- Optional theme -->
    

    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="alteration.css">
    <script src="../jquery3.5.js"></script>


    <script src="../popper.js"></script>


    <script>
        function myFunction() {
            return "Write something clever here...";
        }
    </script>
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
                <li class="active"><a href="#">    <?php echo $name ?>'s Re-Alteration <span class="sr-only"></span></a>
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
        <form method="post" onsubmit="return goBack()">
            <button id="weddingParty" type="submit" name="saveAlt" class="btn btn-lg">Save current selection -
                Go Back
            </button>
        </form>
    </div>
    <h4 class="text-center heading">Re-Alteration Selection</h4>

    <div class="row">

        <div class="col-md-6">
            <input autocomplete="off" type="text" class="form-control" id="search" placeholder="Start typing the name of the Alteration">
            <table class="table table-hover">
<!--     -->
                <tbody id="output">


                </tbody>
            </table>
            <div class="altTypes">
            <form method="post" class="text-center">
                <?php
                $getAltButtons = mysqli_query($con, "select * from alterationtype");
                if (mysqli_num_rows($getAltButtons) > 0) {
                    foreach ($getAltButtons as $items) {
                        ?>
                        <button name="altButtons" class="btn altButtons" value="<?= $items['alterationTypeID'] ?>"><?= $items['aName']?><br>$<?= $items['aPrice']?></button>&nbsp;
                        <?php

                    }
                }
                ?>
            </form></div>
        </div>


        <div class="col-md-6">

            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Alteration</th>
                    <th>Cost</th>

                </tr>
                </thead>
                <tbody>
                <?php

                if (mysqli_num_rows($sqlPrintRecord) > 0) {
                    foreach ($sqlPrintRecord as $items) {
                        ?>
                        <tr>

                            <td><?= $items['aName']; ?></td>
                            <td>$<?= $items['aPrice']; ?> </td>
                            <?php echo "<td> <form method='post'>
                                    <input type='hidden' name='deleteID' value=" . $items['alterationTypeID'] . ">
                                    <input type='submit' class='btn dalt    ' name='delete' value='Delete'>
                                </form> </td>

"; ?>
                        </tr>

                        <?php
                        $total += (int)$items['aPrice'];

                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="2">No Record Found</td>
                    </tr>
                    <?php

                }

                ?>
                <tr>

                    <td>Total Cost:</td>
                    <td>$<?= $total; ?> </td>

                </tr>
                </tbody>
            </table>

            <?php
            ?>
        </div>
    </div>
    <div class="row">
        <form class="form-horizontal" method="post">
            <fieldset>


                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="taylorDate">Tailor Date:</label>
                    <div class="col-md-4">
                        <input id="taylorDate" name="taylorDate" type="date" placeholder=""
                               class="form-control input-md" required="">

                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label" for="pickupDate">Pickup Date:</label>
                    <div class="col-md-4">
                        <input id="pickupDate" name="pickupDate" type="date" placeholder=""
                               class="form-control input-md">

                    </div>
                </div>
                <div class="form-group" id="tailor">
                    <label class="col-md-4 control-label" for="shirt">Assign Tailor:</label>
                    <?php
                    $allTailor = mysqli_query($con, "select * from tailor");
                    ?>
                    <div class="col-md-4">
                        <select id="tailor" name="tailorID" class="form-control">
                            <option value="default">Default</option>
                            <?php
                            // use a while loop to fetch data
                            // from the $all_categories variable
                            // and individually display as an option
                            while ($category = mysqli_fetch_array(
                                $allTailor, MYSQLI_ASSOC)):
                                ?>
                                <option value="<?php echo $category["tailorID"];
                                // The value we usually set is the primary key
                                ?>">
                                    <?php echo $category["tailor"];
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
                    <label class="col-md-4 control-label" for="notes">Notes</label>
                    <div class="col-md-4">
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </div>
                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="submit"></label>

                </div>

            </fieldset>
            <div class="text-center">
                <button id="submit" name="submit" class="btn palt">Submit Re-Alterations</button>
            </div>
        </form>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#search").keypress(function () {
            $.ajax({
                type: 'POST',
                url: '../alteration/search.php?ID=RAT<?php echo $ID?>',
                data: {
                    name: $("#search").val(),
                },
                success: function (data) {
                    $("#output").html(data);
                }
            });
        });
    });

    $(document).ready(function add(c) {
        let $names = [];
        $names.push(c);
    });


</script>
<script src="../jquery.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script>
    function comfirmDeleteCustomer() {
        return confirm("Are you sure, this will delete the Temporary re-alt?");
    }
</script>
</body>
</html>