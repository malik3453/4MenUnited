<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}
$con = mysqli_connect("localhost", "root", "", "4men");

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Purchasing Orders </title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    

    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="orders.css">

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
                <li class="active"><a href="#">Search Purchasing Orders<span class="sr-only"></span></a></li>
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
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="heading">Search All Purchase Orders</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">

                            <form action="" method="GET" autocomplete="off">
                                <div class="input-group lg-3 ">
                                    <label>
                                        <input type="search" name="search" size="50"
                                               value="<?php if (isset($_GET['search'])) {
                                                   echo $_GET['search'];
                                               } ?>" class="form-control"
                                               placeholder="Search all by Brand, Status, Prefix, PO-Digit">
                                    </label>

                                </div>
                                <button type="submit" class="btn dalt">Search</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>

                            <th>PO Number</th>
                            <th>Status</th>
                            <th>Dated Placed</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $leftJoins = "";
                        $supplierName = [];
                        $getAllSuppliers = mysqli_query($con, 'select * from supplier');
                        if (mysqli_num_rows($getAllSuppliers) > 0) {
                            foreach ($getAllSuppliers as $items) {
                                $suplier = $items['supplier'];
                                $suplier = strtolower($suplier);
                                $leftJoins= " left join `".$suplier."` on purchaseorder.poID = `".$suplier."`.poID";
                                $supplierName [] = "`" .$suplier. "`.`customerName`";
                            }
                        }
                        array_shift($supplierName);
                        $supplierName = implode(",",$supplierName);
                        $leftJoins = str_replace('soul of london',"soul of london 'sol'",$leftJoins);
                        $supplierName = str_replace('soul of london','sol',$supplierName);
                        if (isset($_GET['search'])) {
                            $filtervalues = $_GET['search'];


                            $query = "SELECT purchaseorder.poID,brand,purchaseorder.status,datePlaced FROM purchaseorder ".$leftJoins."  WHERE CONCAT(purchaseorder.poID,brand,purchaseorder.status) LIKE '%$filtervalues%' order by purchaseorder.status='open' desc, purchaseorder.status='pending' desc, dateCompleted desc, cast(substr(purchaseOrder.poID,5) as Integer) desc ";
                            $query_run = mysqli_query($con, $query);
                            if (mysqli_num_rows($query_run) > 0) {
                                foreach ($query_run as $items) {
                                    ?>
                                    <tr <?php echo "data-href='receive.php?NAME={$items['poID']}'"; ?>>

                                        <td><?= $items['poID']; ?></td>
                                        <td><?= $items['status']; ?> </td>
                                        <td><?php

                                            if ($items['datePlaced'] == '0000-00-00') {
                                                echo 'Not Placed Yet';
                                            } else {
                                                echo $items['datePlaced'];
                                            } ?> </td>

                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="3">No Record Found</td>
                                </tr>
                                <?php
                            }
                        }

                        ?>
                        </tbody>

                    </table>
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
                </div>
            </div>
        </div>
    </div>

</div>
</body>
<script src="../jquery.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>
