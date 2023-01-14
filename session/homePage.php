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
    <title>Home Page</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="homepage.css">

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
            <a class="navbar-brand" href="homePage.php"><img src="../img/logo.png" class="brandLogo"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Homepage <span class="sr-only"></span></a></li>
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
    <div class="row right">
        <h1 class="heading">Recent Accessed Customers</h1>
        <?php
        $getCustomerDetails = mysqli_query($con,"select * from customerhistroy join customerdetails c on customerhistroy.customerID = c.customerID order by id desc");

        ?>
        <table>
            <thead>
            <tr>
                <th>Customer Name</th>
                <th>Number</th>
                <th>Status</th>
            </tr></thead><tbody>
            <tr>
                <?php
                if (mysqli_num_rows($getCustomerDetails) > 0) {
                foreach ($getCustomerDetails as $items) {
                ?>
            <tr <?php echo "data-href='../customer/customerDetails.php?ID={$items['customerID']}'"; ?>>
                <td><?= $items['customerName']; ?></td>
                <td><?= $items['customerNumber']; ?> </td>
                <td><?= $items['status']; ?> </td>
            </tr></tbody>
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
        </table>

    </div>
<div class="row right">
<h1 class="heading">Quick Access</h1>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../customer/newCustomer.php"><button class="home-btn">Add a Customer</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../customer/customer.php"><button class="home-btn">Search Customer</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../weddingParty/weddingForm.php"><button class="home-btn">Add a Party</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../weddingParty/weddingParty.php"><button class="home-btn">Look up a party</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../section/sections.php"><button class="home-btn">Sections</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../alteration/receiveAlts.php"><button class="home-btn">Receive Alterations</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../alteration/assign.php"><button class="home-btn">Assign Alterations</button> </a></div>
    <div class="col-xs-4 col-sm-3 col-md-2"><a href="../setting.php"><button class="home-btn">Settings</button> </a></div>

</div>
    <div class="row right">
    <h1 class="heading">Orders</h1>
        <div class="col-xs-4 col-sm-3 col-md-2"><a href="../orders/search.php"><button class="home-btn">Search Purchase Order</button> </a></div>

        <?php
    $brands = mysqli_query($con, 'Select supplier as s, prefix as p from supplier;');
    if (mysqli_num_rows($brands) > 0) {
        foreach ($brands as $items) {
            ?>
                <div class="col-xs-4 col-sm-3 col-md-2">
            <a href="../orders/purchaseOrder.php?NAME=<?php echo $items['p'];?>"><button class="home-btn"><?= $items['s']; ?></button> </a> <br>
                </div>
            <?php
        }
    }

    ?>






</div>








</div>
</body>
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
<script src="../jquery.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>
