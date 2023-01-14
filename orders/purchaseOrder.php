<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");
$NAME = $_GET['NAME'];
$prefix = substr($NAME, 0, 3);
$getName = mysqli_query($con, "Select supplier from supplier where prefix like '$NAME'");

while ($rowData = ($getName)->fetch_assoc()) {
    $supplier = $rowData['supplier'];
}

$getOpenOrder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$supplier') and status = 'open'");
while ($rowData = ($getOpenOrder)->fetch_assoc()) {
    $poID = $rowData['poid'];
}
$brand = strtolower($supplier);

$openOrder = mysqli_query($con, "SELECT `$brand`.customerName,`$brand`.customerID,salesID,`$brand`.notes,`$brand`.status, `$brand`.items, `$brand`.orderNumber FROM `$brand` left join salesperson on salesID = `$brand`.customerID WHERE `$brand`.status like 'New' ");


if (isset($_POST['add'])) {
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $items = mysqli_real_escape_string($con, $_POST['items']);
    $salesPerson = mysqli_real_escape_string($con, $_POST['salesPerson']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);

    $sqlSalesPerson = mysqli_query($con, "Select * from salesperson where sName like '$salesPerson'");
    while ($rowData = ($sqlSalesPerson)->fetch_assoc()) {
        $sID = $rowData['salesID'];
    }
    $sqlCount = mysqli_query($con, "SELECT max(cast(substr(orderNumber,5,100) as integer)) as 'max' FROM `$supplier` WHERE poID like '$poID';");
    while ($rowData = ($sqlCount)->fetch_assoc()) {
        $max =  $rowData['max'];
    }

    $max += 1;
    $max = $prefix . "#" . $max;
    $items = $quantity . " x (" . $items . ")";
    $salesPerson = $salesPerson . "(Sales Person)";
    $addSql = mysqli_query($con, "insert into `$brand` values ('$poID','$max','Sales Person','$salesPerson  ','$items','New','$notes','')");
    if ($addSql) {
        $x = substr($poID, 2, 3);
        header('Location: ../orders/purchaseOrder.php?NAME=' . $x);
        exit();
    };
}

if (isset($_REQUEST['delete'])) {

    $value = explode("+", $_REQUEST['deleteID']);


    if ($value[1] !== null) {
        $deleteOrder = "delete from `$brand` where orderNumber  ='$value[0]' and status = 'New'";
        $updateCustomer = "update customerDetails set status = 'Order Deleted - New',histroy  =  concat(histroy,',','Order Deleted - New') where  customerID like '$value[1]'";
        mysqli_query($con, $deleteOrder);
        mysqli_query($con, $updateCustomer);
    } else {
        $deleteOrder = "delete from `$brand` where orderNumber  ='$value[0]'";
        mysqli_query($con, $deleteOrder);
    }
}
if (isset($_POST['placeOrder'])) {

    $getOpenOrder = mysqli_query($con, "select poid from purchaseorder where lower(brand)=lower('$supplier') and status = 'open'");
    $checkSql = mysqli_query($con, "Select count(poID) as c from `$brand` where status like 'New';");

    while ($rowData = ($checkSql)->fetch_assoc()) {
        $c = $rowData['c'];
    }
    if ($c != 0) {
        while ($rowData = ($getOpenOrder)->fetch_assoc()) {
            $poID = $rowData['poid'];
        }
        $brand = strtolower($supplier);
        $date = date("Y/m/d");
        $updatePurchaseOrder = "Update purchaseorder set status = 'Pending', datePlaced = '$date' where status like 'open' and lower(brand) like '$supplier'";
        $updateOrders = "Update `$brand` set status = 'Placed' where status = 'New'";
        $prefix = substr($poID, 2, 3);
        $suffix = substr($poID, 5);
        $suffix = (int)$suffix + 1;
        $prefix = "PO" . $prefix . $suffix;
        $createPO = "insert into purchaseorder (poID, brand, status) values ('$prefix','$supplier','open')";
        mysqli_query($con, $updatePurchaseOrder);
        mysqli_query($con, $createPO);
        mysqli_query($con, $updateOrders);
    } else {
    }
    $x = substr($poID, 2, 3);
    header('Location: ../orders/purchaseOrder.php?NAME=' . $x);
    exit();


}
$getPO = mysqli_query($con, "Select * from purchaseorder where poID like '$NAME'");
while ($rowData = ($getPO)->fetch_assoc()) {
    $mail = $rowData['email'];
}

?>

<!DOCTYPE html>
<html lang="en"><head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> <?php echo $supplier ?> Orders </title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    

    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="orders.css">

    



    <script type="text/javascript">
        document.documentElement.requestFullscreen();
    </script>


</head>

<body >
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
                <li class="active"><a href="#"><?php echo $supplier ?> Orders<span class="sr-only"></span></a></li>
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
        <a href="search.php">
            <button type="button" class="btn btn-lg">Search Purchase Order
            </button>
        </a>
    </div>
    <h4 class="heading"><?= $supplier ?></h4>

    <h2 class="heading2">Open Purchase Order (<?php echo $poID ?>)</h2>
    <form method="post" class="right">
        <button type="submit" class="btn palt" name="placeOrder">Place Order (<?php echo $poID ?>)</button>
    </form>
    <table class="table table-hover">
        <thead>
        <tr>

            <th>Order Number</th>
            <th>Customer Name</th>
            <th>Items</th>
            <th>Status</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
        <?php


        if (mysqli_num_rows($openOrder) > 0) {
            foreach ($openOrder

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


                    echo "<td> <form method='post'>
                                    <input type='hidden'  name='deleteID' value=" . $items['orderNumber'] . "+" . $a . ">
                                    <input type='submit' class='alt' name='delete' value='Delete'>
                                </form> </td>
                                
        
"; ?>       </tr>
                <?php
            }
        }
        ?>


        </tbody>
    </table>


    <h2 class="heading2">Custom Orders</h2>
    <form class="form-horizontal" autocomplete="off" method="post">
        <div class="form-group">
            <label class="col-md-4 control-label" for="salesPerson">Sales Person:</label>
            <div class="col-md-4">
                <select id="salesPerson" name="salesPerson" class="form-control">
                    <?php

                    $sqlSalesPerson = "SELECT * FROM `salesperson`";

                    $all_SalesPerson = mysqli_query($con, $sqlSalesPerson);

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
        <div class="form-group">
            <label class="col-md-4 control-label" for="items">Item:</label>
            <div class="col-md-4">
                <input id="items" name="items" type="text"
                       placeholder="Enter the item details..E.g: Black 44 Regular + 40 Vest"
                       class="form-control input-md"
                       required="">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="quantity">Quantity:</label>
            <div class="col-md-4">
                <input id="quantity" name="quantity" type="number" placeholder="Quantity" class="form-control input-md"
                       required="">

            </div>
        </div>

        <!-- Button -->

        <!-- Textarea -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="notes">Notes:</label>
            <div class="col-md-4">
                <textarea class="form-control" id="notes" name="notes"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="add"></label>
            <div class="col-md-4">
                <button id="add" name="add" class="btn dalt">Add to <?php echo $poID ?></button>
            </div>
        </div>

    </form>


    <div>


        <?php
        $query = "Select poID,datePlaced from purchaseorder where status like 'pending' and brand like '$brand' order by datePlaced,poID desc ";
        $query_run = mysqli_query($con, $query);
        if (mysqli_num_rows($query_run) > 0) {
        foreach ($query_run

        as $items) {
        ?>

        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
                <th scope="col">Order Number</th>
                <th scope="col">Customer Name</th>
                <th scope="col">Items</th>
                <th scope="col">Status</th>
                <th scope="col">Notes</th>

            </tr>
            </thead>
            <tbody>


            <?php
            $poID = mysqli_real_escape_string($con, $items['poID']);
            $datePlaced = mysqli_real_escape_string($con, $items['datePlaced']);

            echo "<h2 class='heading2'>Pending orders (" . $poID . ") - Placed on " . $datePlaced . "</h2>";
            ?>
            <a href="invoice.php?NAME=<?php echo $poID ?>" target="_blank">
                <button class="btn palt">Generate PDF</button>
            </a> &nbsp;
            <?php
            $getPO = mysqli_query($con, "Select * from purchaseorder where poID like '$poID'");
            while ($rowData = ($getPO)->fetch_assoc()) {
                $mail = $rowData['email'];
            }

            if ($mail == 0) {
                echo '<a href="mail.php?NAME=' . $poID . '"><button class="btn alt">Send Email</button></a>';
            } else {
                echo '<a href="mail.php?NAME=' . $poID . '"><button class="btn alt">Resend Email</button></a>';
            }
            echo ' &nbsp; <a href="receive.php?NAME=' . $poID . '"><button class="btn dalt">Recieve</button></a>';

            $cusID = mysqli_query($con, "SELECT * FROM `$brand` WHERE poID like '$poID' and status like 'Placed';
");

            if (mysqli_num_rows($cusID) > 0) {
                foreach ($cusID as $id) {

                    if ($id['customerName'] === null) {
                        $a = "nullValues";
                    } else {
                        $a = $id['customerID'];
                    }

                    ?>
                    <tr>
                        <td><?= $id['orderNumber']; ?></td>
                        <td><?php if ($id['customerName'] === null) {
                                echo $id['customerID'];
                            } else {
                                echo $id['customerName'];
                            }


                            ?></td>
                        <td><?= $id['items']; ?></td>
                        <td><?= $id['status']; ?></td>
                        <td><?= $id['notes']; ?></td>
                        <?php if ($id['customerID'] === "") {
                            $id['customerID'] = $id['salesID'];
                        }
                        ?>

                    </tr>


                <?php }
            }
            }

            } else {
                echo "<h2 class='heading2 text-center'>No Pending Orders</h2>";
            }
            ?>
            </tbody>
        </table>
    </div>


    <div>


        <?php
        $query = "Select poID,dateCompleted from purchaseorder where status like 'Completed' and brand like '$brand' order by dateCompleted desc ,cast(substr(poID,5)as integer ) desc LIMIT 3";
        $query_run = mysqli_query($con, $query);
        if (mysqli_num_rows($query_run) > 0) {
        foreach ($query_run

        as $items) {
        ?>

        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
                <th scope="col">Order Number</th>
                <th scope="col">Customer Name</th>
                <th scope="col">Items</th>
                <th scope="col">Status</th>
                <th scope="col">Notes</th>

            </tr>
            </thead>
            <tbody>


            <?php
            $poID = mysqli_real_escape_string($con, $items['poID']);
            $dateCompleted = mysqli_real_escape_string($con, $items['dateCompleted']);

            echo "<h2 class='heading2'>Completed (" . $poID . ") - Completed on " . $dateCompleted . "</h2>";
            ?>
            <a href="invoice.php?NAME=<?php echo $poID ?>" target="_blank">
                <button class="btn palt">Generate PDF</button>
            </a> &nbsp;
            <?php
            $cusID = mysqli_query($con, "SELECT * FROM `$brand` WHERE poID like '$poID' and (status like 'Received' or status like 'Out of Stock');");

            if (mysqli_num_rows($cusID) > 0) {
                foreach ($cusID as $id) {

                    if ($id['customerName'] === null) {
                        $a = "nullValues";
                    } else {
                        $a = $id['customerID'];
                    }

                    ?>
                    <tr>
                        <td><?= $id['orderNumber']; ?></td>
                        <td><?php if ($id['customerName'] === null) {
                                echo $id['customerID'];
                            } else {
                                echo $id['customerName'];
                            }


                            ?></td>
                        <td><?= $id['items']; ?></td>
                        <td><?= $id['status']; ?></td>
                        <td><?= $id['notes']; ?></td>
                        <?php if ($id['customerID'] === "") {
                            $id['customerID'] = $id['salesID'];
                        }
                        ?>

                    </tr>


                <?php }
            }
            }

            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
<script src="../jquery.js"></script>

<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>