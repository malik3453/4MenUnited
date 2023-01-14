<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");


if (isset($_REQUEST['changeSection'])) {

    $weddingPartyID = mysqli_real_escape_string($con, $_POST['oldParty']);
    $newSection = mysqli_real_escape_string($con, $_POST['newSection']);
    if ($weddingPartyID === "None" || $newSection === "None") {

        echo "<script>alert('You must select the new party and new section')</script>";
    } else {
        $getSectionDetail = mysqli_query($con, "Select * from sectionattr where sectionID = '$newSection'");
        while ($rowData = ($getSectionDetail)->fetch_assoc()) {
            $sectionName = $rowData['name'];
        }
        $getAllPartyPeople = mysqli_query($con, "select * from weddingpartyattr where weddingID = '$weddingPartyID'");
        if (mysqli_num_rows($getAllPartyPeople) > 0) {
            foreach ($getAllPartyPeople as $items) {
                mysqli_query($con,"update customerDetails set status = '$sectionName', histroy = concat(histroy,',','Changed Section -','$sectionName') where customerID =" . $items['customerID'] );
                mysqli_query($con, "update section set sectionID = '$newSection',sectionName = '$sectionName' where customerId = " . $items['customerID']);
            }

        }

        mysqli_query($con, "update weddingForm set section = '$sectionName' where weddingID = '$weddingPartyID'");
        header('Location: sections.php');
    }
}

if (isset($_REQUEST['stealHold'])) {
    $getCustomerDetails = mysqli_query($con, "Select * from customerdetails join supplier ON suit = supplier where customerID = {$_REQUEST['stealHoldID']}");
    while ($rowData = ($getCustomerDetails)->fetch_assoc()) {
        $customerID = $rowData['customerID'];
        $customerName = $rowData['customerName'];
        $supplier = $rowData['suit'];
        $prefix = $rowData['prefix'];
    }

    $getItems = mysqli_query($con, "Select * from section where customerId like {$_REQUEST['stealHoldID']}");
    while ($rowData = ($getItems)->fetch_assoc()) {
        $itemsPO = $rowData['items'];
    }
    $sNotes = "Walk-in Customer (Reordered)";
    $getorder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$supplier') and status = 'open'");
    while ($rowData = ($getorder)->fetch_assoc()) {
        $newPO = $rowData['poid'];
    }

    $sqlCount = mysqli_query($con, "SELECT max(cast(substr(orderNumber,5,100) as integer)) as 'max' FROM `$supplier` WHERE poID like '$newPO';");
    while ($rowData = ($sqlCount)->fetch_assoc()) {
        $newON =  $rowData['max'];
    }

    $newON += 1;
    $newON = $prefix . "#" . $newON;
    $sqlOrder = "insert into `$supplier` values ('$newPO','$newON','$customerID','$customerName','$itemsPO','New','$sNotes','')";
    mysqli_query($con, $sqlOrder);
    $deleteFromSection = "delete from section where customerId = {$_REQUEST['stealHoldID']}";
    $status = mysqli_real_escape_string($con, "Re-Ordered " . $newPO . " (" . $newON . ")");
    mysqli_query($con, "Update customerDetails set status = '$status',histroy  =  concat(histroy,',Stolen,','$status') where customerID = '$customerID'");
    mysqli_query($con, $deleteFromSection);
    header('Location: ../orders/purchaseOrder.php?NAME=' . $prefix);
    exit();

}

if (isset($_REQUEST['steal'])) {
    $getCustomerDetails = mysqli_query($con, "Select * from customerdetails join supplier ON suit = supplier where customerID = {$_REQUEST['stealID']}");
    while ($rowData = ($getCustomerDetails)->fetch_assoc()) {
        $customerID = $rowData['customerID'];
        $customerName = $rowData['customerName'];
        $supplier = $rowData['suit'];
        $prefix = $rowData['prefix'];
    }
    $getPartyDetails = mysqli_query($con, "select individual,groomName from customerdetails join weddingpartyattr w on customerdetails.customerID = w.customerID join weddingform w2 on w.weddingID = w2.weddingID where customerdetails.customerID like '$customerID'");
    while ($rowData = ($getPartyDetails)->fetch_assoc()) {
        $individual = $rowData['individual'];
        $groom = $rowData ['groomName'];
    }

    $getItems = mysqli_query($con, "Select * from section where customerId like {$_REQUEST['stealID']}");
    while ($rowData = ($getItems)->fetch_assoc()) {
        $itemsPO = $rowData['items'];
    }
    $sNotes = $individual . " - " . $groom . "''s Party (Reordered)";
    $getorder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$supplier') and status = 'open'");
    while ($rowData = ($getorder)->fetch_assoc()) {
        $newPO = $rowData['poid'];
    }

    $sqlCount = mysqli_query($con, "SELECT max(cast(substr(orderNumber,5,100) as integer)) as 'max' FROM `$supplier` WHERE poID like '$newPO';");
    while ($rowData = ($sqlCount)->fetch_assoc()) {
        $newON =  $rowData['max'];
    }
    $newON += 1;
    $newON = $prefix . "#" . $newON;
    $sqlOrder = "insert into `$supplier` values ('$newPO','$newON','$customerID','$customerName','$itemsPO','New','$sNotes','')";
    mysqli_query($con, $sqlOrder);
    $deleteFromSection = "delete from section where customerId = {$_REQUEST['stealID']}";
    $status = mysqli_real_escape_string($con, "Re-Ordered " . $newPO . " (" . $newON . ")");
    mysqli_query($con, "Update customerDetails set status = '$status',histroy  =  concat(histroy,',Stolen,','$status') where customerID = '$customerID'");
    mysqli_query($con, $deleteFromSection);
    header('Location: ../orders/purchaseOrder.php?NAME=' . $prefix);
    exit();

}


?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sections</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="css/super-hero-bootstrap.min.css" rel="stylesheet">-->
    <!-- Optional theme -->
    

    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="section.css">

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
                <li class="active"><a href="#">Sections<span class="sr-only"></span></a></li>
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

        <button id="weddingParty" type="button" name="moveParty" class="btn btn-lg" data-toggle="modal"
                data-target="#largeShoes">Move Party
        </button>
    </div>
    <div class="modal" id="largeShoes" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalLabelLarge">Move Party Section</h4>
                </div>

                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4 control-label" for="type">Select Party:</label>
                                <div class="col-md-6">
                                    <select id="oldParty" name="oldParty" class="form-control">
                                        <option value="None">Select Party</option>
                                        <?php
                                        $types = mysqli_query($con, "Select distinct w.weddingID,groomName,section from section join weddingpartyattr w on section.customerId = w.customerID join weddingform w2 on w.weddingID = w2.weddingID");
                                        // use a while loop to fetch data
                                        // from the $all_categories variable
                                        // and individually display as an option
                                        while ($category = mysqli_fetch_array(
                                            $types, MYSQLI_ASSOC)):
                                            ?>
                                            <option value="<?php echo $category["weddingID"];
                                            // The value we usually set is the primary key
                                            ?>">
                                                <?php echo $category["groomName"] . " - " . $category['section'];
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
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4 control-label" for="type">Select New Section:</label>
                                <div class="col-md-6">
                                    <select id="newSection" name="newSection" class="form-control">
                                        <option value="None">New Section</option>
                                        <?php
                                        $types = mysqli_query($con, "Select * from sectionattr where sectionID != 0 and sectionID !=100");
                                        // use a while loop to fetch data
                                        // from the $all_categories variable
                                        // and individually display as an option
                                        while ($category = mysqli_fetch_array(
                                            $types, MYSQLI_ASSOC)):
                                            ?>
                                            <option value="<?php echo $category["sectionID"];
                                            // The value we usually set is the primary key
                                            ?>">
                                                <?php echo $category["name"];
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
                        </div>


                        <button id="changeSection" name="changeSection" class="btn palt">Process change</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <h4 class="text-center heading">Wedding Sections</h4>

    <div>
        <h2 class="heading2">Hold Section</h2>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Customer Name</th>
                <th>Items</th>
                <th>Number</th>
                <th>Action</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $getHold = mysqli_query($con, "Select * from section join customerdetails on section.customerId= customerDetails.customerID where sectionName = 'Hold'");
            if (mysqli_num_rows($getHold) > 0) {
                foreach ($getHold as $items) {
                    ?>
                    <tr>
                        <td><?= $items['customerName']; ?></td>
                        <td><?= $items['items']; ?></td>
                        <td><?= $items['customerNumber']; ?></td>
                        <?php echo "<td> <form method='post'>
                                    <input type='hidden' name='stealHoldID' value=" . $items['customerID'] . ">
                                    <input type='submit' class='btn btn-warning' name='stealHold' value='Steal'>
                                </form> </td>

"; ?>
                    </tr>
                    <?php
                }

            }


            ?>


            </tbody>
        </table>
    </div>
    <div>
        <h2 class="heading2">Accessories</h2>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Party Name</th>
                <th>Items</th>
                <th>Number</th>

            </tr>
            </thead>
            <tbody>
            <?php

            $getHold = mysqli_query($con, "Select * from section join weddingform on substring_index(customerId,'-',1) = concat('WID',weddingID) where sectionID = 100");
            if (mysqli_num_rows($getHold) > 0) {
                foreach ($getHold as $items) {
                    ?>
                    <tr>
                        <td><?= $items['customerName']; ?>'s Party</td>
                        <td><?= $items['items']; ?></td>
                        <td><?= $items['Pnumber']; ?></td>
                    </tr>
                    <?php
                }

            }


            ?>


            </tbody>
        </table>
    </div>
    <div>
        <?php
        $getSection = mysqli_query($con, "Select * from sectionAttr where sectionID != 0 and sectionID !=100");
        if (mysqli_num_rows($getSection) > 0) {
            foreach ($getSection
                     as $items) {
                $sectionName = $items['name'];
                echo "<h2 class='heading2'>" . $sectionName . "</h2>";
                ?>
                <table class="table table-hover" style="table-layout: fixed">
                    <thead>
                    <tr>
                        <th>Party</th>
                        <th>Wedding Date</th>
                        <th>Groom's Suit</th>
                        <th>Groomsmen's Suit</th>
                        <th>Groom's Father</th>
                        <th>Bride's Father</th>


                    </tr>
                    </thead>
                    <tbody>


                    <?php
                    $getGrooms = mysqli_query($con, "Select DISTINCT w2.weddingID,w2.groomName,w2.groomStyle,w2.groomsmanStyle,w2.fatherOfTheGroomStyle,w2.fatherOfTheBrideStyle, w2.weddingDate, w2.groomSuit,w2.groomsmenSuit,w2.fatherOfTheBrideSuit,w2.fatherOfTheGroom from section s right join weddingpartyattr w on s.customerId = w.customerID right join weddingform w2 on w.weddingID = w2.weddingID where sectionName like '$sectionName' ");
                    if (mysqli_num_rows($getGrooms) > 0) {
                        foreach ($getGrooms as $grooms) {
                            ?>
                            <tr class='expandChildTable'><?php
                                $wID = $grooms['weddingID'];
                                echo "<td>" . $grooms['groomName'] . "'s party</td>";
                                echo "<td>" . $grooms['weddingDate'] . "</td>";

                                if ($grooms['groomSuit'] != 'None') {
                                    echo "<td>" . $grooms['groomSuit'] . " (" . $grooms['groomStyle'] . ")</td>";
                                } else {
                                    echo '<td>None</td>';
                                }
                                if ($grooms['groomsmenSuit'] != 'None') {
                                    echo "<td>" . $grooms['groomsmenSuit'] . " (" . $grooms['groomsmanStyle'] . ")</td>";
                                } else {
                                    echo '<td>None</td>';
                                }
                                if ($grooms['fatherOfTheGroom'] != 'None') {
                                    echo "<td>" . $grooms['fatherOfTheGroom'] . " (" . $grooms['fatherOfTheGroomStyle'] . ")</td>";
                                } else {
                                    echo '<td>None</td>';
                                }
                                if ($grooms['fatherOfTheBrideSuit'] != 'None') {
                                    echo "<td>" . $grooms['fatherOfTheBrideSuit'] . " (" . $grooms['fatherOfTheBrideStyle'] . ")</td>";
                                } else {
                                    echo '<td>None</td>';
                                }

                                ?>
                            </tr>
                            <tr class="childTableRow">
                                <td colspan="6">
                                    <?php
                                    $getPartyPeople = mysqli_query($con, "Select * from section join  weddingpartyattr w on section.customerId = w.customerID where sectionID != 100 and weddingID=" . $wID );
                                    ?>
                                    <table class="table table-hover" style="table-layout: fixed">
                                        <thead>
                                        <tr>
                                            <td colspan="4"><a class="right"
                                                               href="../weddingParty/weddingPartyAttr.php?ID=<?php echo $wID; ?>">
                                                    <button type="submit" class="btn dalt"><?= $grooms['groomName'] ?>'s
                                                        Party
                                                    </button>

                                                </a>

                                                <a class="right"
                                                   href="partylabel.php?ID=<?php echo $wID; ?>" target="_blank">
                                                    <button type="submit" value="<?= $grooms['weddingID'] ?>"
                                                            class="btn palt">Print Party Label
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Individual</th>
                                            <th>Customer Name</th>
                                            <th>Items</th>
                                            <th>Action</th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php

                                        if (mysqli_num_rows($getGrooms) > 0) {
                                            foreach ($getPartyPeople as $individuals) {
                                                ?>
                                                <tr>
                                                    <td><?= $individuals['individual']; ?> </td>
                                                    <td><?= $individuals['customerName']; ?> </td>
                                                    <td><?= $individuals['items']; ?> </td>
                                                    <?php echo "<td> <form method='post'>
                                    <input type='hidden' name='stealID' value=" . $individuals['customerID'] . ">
                                    <input type='submit' class='btn btn-warning' name='steal' value='Steal'>
                                </form> </td>

"; ?>
                                                </tr>

                                                <?php
                                            }
                                        }
                                        ?>

                                        </tbody>
                                    </table>

                                </td>
                            </tr>

                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
        }
        ?>
    </div>

</div>
</body>
<script src="../jquery.js"></script>

<script src="../bootstrap/js/bootstrap.min.js"></script>
<script>
    $(function () {
        $(".expandChildTable").on('click', function () {
            $(this).toggleClass('selected').closest('tr').next().toggle();
        })
    });
</script>
</html>