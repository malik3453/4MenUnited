<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

$con = mysqli_connect("localhost", "root", "", "4men");

if (isset($_POST['tailor'])) {
    $name = mysqli_real_escape_string($con, $_POST['tname']);

    mysqli_query($con, "Insert into tailor (tailor) values ('$name')");
    header('Location: setting.php');
    exit();
}

if (isset($_POST['salesPerson'])) {
    $name = mysqli_real_escape_string($con, $_POST['sname']);
    mysqli_query($con, "Insert into salesperson (sName) values ('$name')");
    header('Location: setting.php');
    exit();
}
if (isset($_POST['addSupplier'])) {
    $email = $_POST['email'];
    $supplierPrefix = $_POST['supplierPrefix'];
    $supplierName = $_POST['supplierName'];
if (!empty($_POST['accYN'])) {
    $accYN = "Y";
}else{
    $accYN = "";
}

    $po = "PO".$supplierPrefix."1";
    $createTable = "CREATE TABLE `".$supplierName."` (
  `poID` text NOT NULL,
  `orderNumber` text NOT NULL,
  `customerID` text NOT NULL,
  `customerName` text NOT NULL,
  `items` text NOT NULL,
  `status` text NOT NULL,
  `notes` text NOT NULL,
  `notified` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    mysqli_query($con,$createTable);
    mysqli_query($con,"insert into purchaseorder (poID,status,brand) values ('$po','open','$supplierName')");
    mysqli_query($con,"insert into supplier (supplier, prefix, email, accessories) VALUES ('$supplierName','$supplierPrefix','$email','$accYN')");


    header('Location: setting.php');
    exit();
}
if (isset($_REQUEST['delTailor'])) {

    mysqli_query($con, "delete from tailor where tailorID =  {$_REQUEST['delTailorID']}");
    header('Location: setting.php');
    exit();
}
if (isset($_REQUEST['delSales'])) {

    mysqli_query($con, "delete from salesperson where salesID =  {$_REQUEST['delSalesID']}");
    header('Location: setting.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setting</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="custom.css">
    <link rel="stylesheet" href="setting.css">
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
            <a class="navbar-brand" href="session/homePage.php"><img src="img/logo.png" class="brandLogo"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Setting <span class="sr-only"></span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Weddings <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="weddingParty/weddingParty.php">Search Wedding Parties</a></li>
                        <li><a href="weddingParty/weddingForm.php">Wedding Form</a></li>


                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Customers <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="customer/customer.php">Search Customers</a></li>
                        <li><a href="customer/newCustomer.php">New Customer Form</a></li>


                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Orders <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="orders/search.php">Search Purchase Order</a></li>
                        <li role="separator" class="divider"></li>
                        <?php
                        $order = mysqli_query($con, 'Select supplier as s, prefix as p from supplier;');
                        if (mysqli_num_rows($order) > 0) {
                            foreach ($order as $items) {

                                ?>
                                <li><a href="orders/purchaseOrder.php?NAME=<?php

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


                        <li><a href="alteration/receiveAlts.php">Receive Alterations</a></li>
                        <li><a href="alteration/assign.php">Assign Tailor</a></li>


                    </ul>
                </li>
                <li><a href="section/sections.php">Sections</a></li>
                <li><a href="session/logout.inc.php">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container">
    <div>
        <div class="heading right">Add a Tailor</div>
        <?php
        $getTailor = mysqli_query($con, "select * from tailor");

        ?>
        <table>
            <thead>
            <tr>
                <th>Tailor Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php
                if (mysqli_num_rows($getTailor) > 0) {
                foreach ($getTailor

                as $items) {
                ?>
                <td><?= $items['tailor']; ?> </td>
                <?php echo "<td> <form method='post'>
                                    <input type='hidden'  name='delTailorID' value=" . $items['tailorID'] . ">
                                    <input type='submit' class='btn alt' onclick='return deleteConfirm()' name='delTailor' value='Delete'>
                                </form> </td>
                                
        
"; ?>
            </tr>
            </tbody>
            <?php
            }
            } else {
                ?>
                <tr>
                    <td colspan="3">No Record Found</td>
                </tr>
                <?php
            }


            ?>
            <tr>
                <form method="post" autocomplete="off">

                    <td><input id="fullName" name="tname" type="text" placeholder="Enter new Tailor"
                               class="form-control input-md"
                               required=""></td>
                    <td>
                        <button id="hold" type="submit" name="tailor" class="btn dalt">Add Tailor</button>
                    </td>
                </form>
            </tr>
        </table>
    </div>

    <div>
        <div class="heading right">Add a Sales Person</div>
        <?php
        $getTailor = mysqli_query($con, "select * from salesperson");

        ?>
        <table>
            <thead>
            <tr>
                <th>SalesPerson Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php
                if (mysqli_num_rows($getTailor) > 0) {
                foreach ($getTailor

                as $items) {
                ?>
                <td><?= $items['sName']; ?> </td>
                <?php echo "<td> <form method='post'>
                                    <input type='hidden'  name='delSalesID' value=" . $items['salesID'] . ">
                                    <input type='submit' class='btn alt' onclick='return deleteConfirm()' name='delSales' value='Delete'>
                                </form> </td>
                                
        
"; ?>
            </tr>
            </tbody>
            <?php
            }
            } else {
                ?>
                <tr>
                    <td colspan="3">No Record Found</td>
                </tr>
                <?php
            }


            ?>
            <tr>
                <form method="post" autocomplete="off">

                    <td><input id="fullName" name="sname" type="text" placeholder="Enter new Sales Person"
                               class="form-control input-md"
                               required=""></td>
                    <td>
                        <button id="hold" type="submit" name="salesPerson" class="btn dalt">Add Sales Person</button>
                    </td>
                </form>
            </tr>
        </table>
    </div>


    <div>
        <div class="heading right">Add a Supplier</div>
        <?php
        $getTailor = mysqli_query($con, "select * from supplier");

        ?>
        <table>
            <thead>
            <tr>
                <th>Supplier</th>
                <th>PreFix</th>
                <th>Email</th>
                <th>Accessories</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php
                if (mysqli_num_rows($getTailor) > 0) {
                foreach ($getTailor

                as $items) {
                ?>
                <td><?= $items['supplier']; ?> </td>
                <td><?= $items['prefix']; ?> </td>
                <td><?= $items['email']; ?> </td>
                <td><?= $items['accessories']; ?> </td>


            </tr>
            </tbody>
            <?php
            }
            } else {
                ?>
                <tr>
                    <td colspan="3">No Record Found</td>
                </tr>
                <?php
            }


            ?>
            <form method="post" autocomplete="off">
                <tr>


                    <td><input id="fullName" name="supplierName" type="text" placeholder="Enter Supplier's name"
                               class="form-control input-md"
                               required=""></td>
                    <td><input id="fullName" name="supplierPrefix" type="text"
                               placeholder="Enter Supplier's Prefix (3-Character only)"
                               class="form-control input-md"
                               required=""></td>
                    <td>
                        <input id="fullName" name="email" type="email" placeholder="Enter Supplier's email"
                               class="form-control input-md"
                               required="">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><input name="accYN[]" type="checkbox"></span>
                            <input id="" class="form-control" type="text"
                                   placeholder="Only check if they provide accessories" disabled>
                        </div>
                    </td>

            </tr>
            <tr>
               <td colspan="4">
                   <button id="hold" type="submit" name="addSupplier" class="btn dalt">Add Supplier</button>

               </td>
            </tr>
            </form>
        </table>
    </div>
    <br>
</body>
<script>
    function deleteConfirm() {
        return confirm("This cannot be undone, are you sure you want to delete?");
    }
</script>
<script src="jquery.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</html>

