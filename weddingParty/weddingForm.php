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


$sqlSuits = "Select supplier as s from supplier";
$all_sqlSuits = mysqli_query($con, $sqlSuits);
$all_sqlSuits1 = mysqli_query($con, $sqlSuits);
$all_sqlSuits2 = mysqli_query($con, $sqlSuits);
$all_sqlSuits3 = mysqli_query($con, $sqlSuits);


if (isset($_POST['submit'])) {
    // Store the Product name in a "name" variable
    $groomName = mysqli_real_escape_string($con, $_POST['groomName']);
    $groomName = mysqli_real_escape_string($con, $_POST['groomName']);
    $section = mysqli_real_escape_string($con, $_POST['section']);

    $weddingDate = mysqli_real_escape_string($con, $_POST['weddingDate']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $pnumber = mysqli_real_escape_string($con, $_POST['pnumber']);
    $groomSuit = mysqli_real_escape_string($con, $_POST['groomSuit']);
    $groomStyle = mysqli_real_escape_string($con, $_POST['groomStyle']);
    $groomsmenSuit = mysqli_real_escape_string($con, $_POST['groomsmenSuit']);
    $groomsmanStyle = mysqli_real_escape_string($con, $_POST['groomsmanStyle']);
    $fatherOfTheGroom = mysqli_real_escape_string($con, $_POST['fatherOfTheGroom']);
    $fatherOfTheGroomStyle = mysqli_real_escape_string($con, $_POST['fatherOfTheGroomStyle']);
    $fatherOfTheBrideSuit = mysqli_real_escape_string($con, $_POST['fatherOfTheBrideSuit']);
    $fatherOfTheBrideStyle = mysqli_real_escape_string($con, $_POST['fatherOfTheBrideStyle']);
    $salesPerson = mysqli_real_escape_string($con, $_POST['salesPerson']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);

    $date = $_POST['weddingDate'];
    $s = substr($date, 5, 2);

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
    $weddingMonth = mysqli_real_escape_string($con, $month);
    // Creating an insert query using SQL syntax and
    // storing it in a variable.
    $wedIDSQL = mysqli_query($con, "SELECT MAX(weddingID) as 'wed' FROM weddingform");

    while ($rowData = ($wedIDSQL)->fetch_assoc()) {

        $wedID = $rowData['wed'];
        $wedID = $wedID + 1;


        $sql_insert = "INSERT INTO `weddingform` (`weddingID`,`groomName`, `weddingDate`, `weddingMonth`, `email`, `Pnumber`, `groomSuit`, `groomStyle`, `groomsmenSuit`, `groomsmanStyle`, `fatherOfTheGroom`, `fatherOfTheGroomStyle`, `fatherOfTheBrideSuit`, `fatherOfTheBrideStyle`, `salesPerson`, `notes`,`section`) VALUES ('$wedID','$groomName', '$weddingDate', '$weddingMonth', '$email', '$pnumber', '$groomSuit', '$groomStyle', '$groomsmenSuit', '$groomsmanStyle', '$fatherOfTheGroom', '$fatherOfTheGroomStyle', '$fatherOfTheBrideSuit', '$fatherOfTheBrideStyle', '$salesPerson', '$notes','$section');";

        // The following code attempts to execute the SQL query
        // if the query executes with no errors
        // a javascript alert message is displayed
        // which says the data is inserted successfully
        if (mysqli_query($con, $sql_insert)) {

            header("Location: weddingPartyAttr.php?ID=" . $wedID);
            exit();
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
    <title>Wedding Form</title>
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
                <li class="active"><a href="#">Wedding Form <span class="sr-only"></span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Weddings <span class="caret"></span></a>
                    <ul class="dropdown-menu">

                        <li><a href="weddingParty.php">Search Wedding Parties</a></li>
                        <li><a href="#">Wedding Form</a></li>


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
    <form method="post" autocomplete="off" class="form-horizontal">
        <fieldset>
            <!-- Form Name -->
            <div class="right">
                <a class="right" href="weddingParty.php">
                    <button id="weddingParty" type="button" name="weddingParty" class="btn btn-lg">Wedding Search</button>
                </a>
            </div>
            <h4 class="text-center heading">Wedding Form</h4>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="groomName">Groom's Full name:</label>
                <div class="col-md-4">
                    <input id="groomName" name="groomName" type="text" placeholder="E.g.  John Smith"
                           class="form-control input-md" required="">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="weddingDate">Wedding Date:</label>
                <div class="col-md-4">
                    <input id="weddingDate" name="weddingDate" type="date" placeholder="" class="form-control input-md"
                           required="">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="email">Email:</label>
                <div class="col-md-4">
                    <input id="email" name="email" type="email" placeholder="johnsmith@gmail.com"
                           class="form-control input-md">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="pnumber">Phone Number:</label>
                <div class="col-md-4">
                    <input id="pnumber" name="pnumber" type="number" placeholder="9056051490"
                           class="form-control input-md" required="">
                    <span class="help-block">Enter 10-digits only</span>
                </div>
            </div>

            <!-- Select Basic -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="groomSuit">Groom's Suit:</label>
                <div class="col-md-4">
                    <select id="groomSuit" onchange="ShowHideDiv('groomSuit','gS')" name="groomSuit"
                            class="form-control">
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

            <div class="form-group" id="gS" style="display: none">
                <label class="col-md-4 control-label" for="groomStyle">Groom's Color/ Style:</label>
                <div class="col-md-4">
                    <input id="groomStyle" name="groomStyle" type="text" placeholder="E.g.  Black, 201-1, etc.."
                           class="form-control input-md">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="groomsmenSuit">Groomsman's Suit:</label>
                <div class="col-md-4">
                    <select id="groomsmenSuit" onchange="ShowHideDiv('groomsmenSuit','gmS')" name="groomsmenSuit"
                            class="form-control">
                        <option value="None">None</option>
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
            <div class="form-group" id="gmS" style="display: none">
                <label class="col-md-4 control-label" for="groomsmanStyle">Groomsman's Color/ Style:</label>
                <div class="col-md-4">
                    <input id="groomsmanStyle" name="groomsmanStyle" type="text" placeholder="E.g.  Black, 201-1, etc.."
                           class="form-control input-md">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="fatherOfTheGroom">Father of the Groom's Suit:</label>
                <div class="col-md-4">
                    <select id="fatherOfTheGroom" name="fatherOfTheGroom"
                            onchange="ShowHideDiv('fatherOfTheGroom','FOGS')" class="form-control">
                        <option value="None">None</option>
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
            <div class="form-group" id="FOGS" style="display: none">
                <label class="col-md-4 control-label" for="fatherOfTheGroomStyle">Father of the Groom's Color/
                    Style:</label>
                <div class="col-md-4">
                    <input id="fatherOfTheGroomStyle" name="fatherOfTheGroomStyle" type="text"
                           placeholder="E.g.  Black, 201-1, etc.." class="form-control input-md">

                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="fatherOfTheBrideSuit">Father of the Bride's Suit:</label>
                <div class="col-md-4">
                    <select id="fatherOfTheBrideSuit" name="fatherOfTheBrideSuit"
                            onchange="ShowHideDiv('fatherOfTheBrideSuit','FOBS')" class="form-control">
                        <option value="None">None</option>
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
            <div class="form-group" id="FOBS" style="display: none">
                <label class="col-md-4 control-label" for="fatherOfTheBrideStyle">Father of the Bride's Color/
                    Style:</label>
                <div class="col-md-4">
                    <input id="fatherOfTheBrideStyle" name="fatherOfTheBrideStyle" type="text"
                           placeholder="E.g.  Black, 201-1, etc.." class="form-control input-md">

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
            <div class="form-group">
                <label class="col-md-4 control-label" for="notes">Notes:</label>
                <div class="col-md-4">
                    <textarea class="form-control" id="notes" name="notes"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="section">Section:</label>
                <div class="col-md-4">
                    <select id="section" name="section" class="form-control">
                        <?php

                        $sqlSection = mysqli_query($con, "select * from sectionattr where sectionID != 0 and sectionID != 100");
                        // use a while loop to fetch data
                        // from the $all_categories variable
                        // and individually display as an option
                        while ($category = mysqli_fetch_array(
                            $sqlSection, MYSQLI_ASSOC)):
                            ?>
                            <option value="<?php echo $category["name"];
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

            <div class="form-group">
                <label class="col-md-4 control-label" for="submit"></label>
                <div class="col-md-8">
                    <button id="submit" type="submit" name="submit" class="btn dalt">Submit</button>
                    <button id="reset" type="reset" name="reset" class="btn palt">Reset</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>

</body>

<script type="text/javascript">
    function ShowHideDiv($id, $idn) {
        let a = document.getElementById($id);
        let b = document.getElementById($idn);
        b.style.display = a.value !== "None" ? "block" : "none";
    }

</script>
<script src="../jquery.js"></script>

<script src="../bootstrap/js/bootstrap.min.js"></script>
</html>