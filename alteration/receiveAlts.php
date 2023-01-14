<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit;
}

$con = mysqli_connect("localhost", "root", "", "4men");
require '../orders/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_REQUEST['receive'])) {
    $tailor = mysqli_real_escape_string($con, $_POST['tailorID']);
    if ($tailor !== "None") {

        $sqlPrintRecord = mysqli_query($con, "Select c.customerID,a.alterationID,c.customerName,c.customerNumber ,r.realtID from receivetemp join alteration a on receivetemp.alterationID = a.alterationID join customerdetails c on a.customerID = c.customerID left join realt r on receivetemp.realtID = r.realtID");
        if (mysqli_num_rows($sqlPrintRecord) > 0) {
            foreach ($sqlPrintRecord as $items) {
                if ($items['realtID'] === $items['alterationID']) {
                    $check = "RT";
                    mysqli_query($con, "update realt set status = 'Completed', tailor = '$tailor' where realtID =" . $items['alterationID']);

                } else {
                    $check = "AT";
                    mysqli_query($con, "update alteration set status = 'Completed', tailor = '$tailor' where alterationID =" . $items['alterationID']);

                }
                mysqli_query($con, "update customerDetails set status = 'Completed',histroy  =  concat(histroy,',','Completed') where customerID= " . $items['customerID']);
//Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = 'abdultest813@gmail.com';                     //SMTP username
                $mail->Password = 'ufauhlieqcugelgp';                               //SMTP password
                $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
                $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
                $mail->setFrom('abdultest813@gmail.com', '4MenUnited');
                $cusNumberMail = $items['customerNumber'] . '@txt.bellmobility.ca';
                $mail->addAddress($cusNumberMail, $items['customerName']);     //Add a recipient
//Content
                $customerName = $items['customerName'];
                $mail->isHTML(true);                                  //Set orders format to HTML
                $mail->Body = "Hello $customerName \n" . "This is a message from 4 Men United notifying you that your items being altered under " . $check . $items['alterationID'] . " are complete and ready for pick up. 
" . "\nDo Not Reply";

            $mail->send();
                mysqli_query($con, "delete from receivetemp where alterationID = " . $items['alterationID']);

            }
            header('Location: receiveAlts.php');
        }
    }else{
        echo "<script>alert('Select a Tailor')</script>";
    }


}


if (isset($_REQUEST['delete'])) {


    $getReAlt = mysqli_query($con, "Select * from receivetemp where alterationID = {$_REQUEST['deleteID']}");


    while ($rowData = ($getReAlt)->fetch_assoc()) {
        $realtID = $rowData['realtID'];
    }

    if ($realtID !== $_REQUEST['deleteID']) {
        mysqli_query($con, "update customerDetails set status = 'Altering',histroy  =  concat(histroy,',','Altering') where alternationID = {$_REQUEST['deleteID']}");
        mysqli_query($con, "update alteration set status = 'Altering' where alterationID = {$_REQUEST['deleteID']}");

    } else {
        mysqli_query($con, "update customerDetails set status = 'Re-Altering',histroy  =  concat(histroy,',','Re-Altering') where alternationID = {$_REQUEST['deleteID']}");
        mysqli_query($con, "update realt set status = 'Re-Altering' where realtID = {$_REQUEST['deleteID']}");
    }

    $deleteTempAlt = "delete from receivetemp where alterationID = {$_REQUEST['deleteID']}";
    $deleteReTempAlt = "delete from receivetemp where realtID = {$_REQUEST['deleteID']}";
    mysqli_query($con, $deleteTempAlt);
    mysqli_query($con, $deleteReTempAlt);

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receive Alterations </title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../custom.css">
    <link rel="stylesheet" href="alteration.css">
    <script src="../jquery3.5.js"></script>


    <script src="../popper.js"></script>


    


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
                <li class="active"><a href="#">Receive Alterations<span class="sr-only"></span></a></li>
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
    <form class="form-horizontal" method="post"
          onsubmit="return confirm('The selected alterations will be received and customers will be notified.')">

        <h4 class="heading">Receive Alteration</h4>

        <div class="right">
            <a href="search.php">
                <button id="weddingParty" type="submit" name="receive" class="btn btn-lg">Receive
                </button>
            </a>
        </div>
        <br>
        <fieldset>


            <!-- Select Basic -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="tailorID">Select tailor:</label>
                <div class="col-md-4">
                    <select id="tailorID" name="tailorID" class="form-control">
                        <option value="None">Select from Below</option>
                        <?php
                        $getTailors = mysqli_query($con, "select * from tailor");
                        // use a while loop to fetch data
                        // from the $all_categories variable
                        // and individually display as an option
                        while ($category = mysqli_fetch_array(
                            $getTailors, MYSQLI_ASSOC)):
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

        </fieldset>


    </form>
    <div class="row">

        <div class="col-md-6">
            <input autocomplete="off" type="text" class="form-control" id="search" placeholder="Search by Alteration Digit or Customer Name">
            <table class="table table-hover">

                <tbody id="output">
                </tbody>
            </table>
        </div>


        <div class="col-md-6">

            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Alteration Number</th>
                    <th>Customer Name</th>

                </tr>
                </thead>
                <tbody>
                <?php
                $sqlPrintRecord = mysqli_query($con, "Select * from receivetemp join alteration a on receivetemp.alterationID = a.alterationID join customerdetails c on a.customerID = c.customerID left join realt r on r.realtID = receivetemp.realtID");
                if (mysqli_num_rows($sqlPrintRecord) > 0) {
                    foreach ($sqlPrintRecord as $items) {

                        if ($items['realtID'] === null) {
                            $x = "AT";
                        } else {
                            $x = "RT";
                        }

                        ?>
                        <tr>

                            <td><?= $x . $items['alterationID']; ?></td>
                            <td><?= $items['customerName']; ?> </td>
                            <?php echo "<td> <form method='post'>
                                    <input type='hidden' name='deleteID' value=" . $items['alterationID'] . ">
                                    <input type='submit' class='btn alt' name='delete' value='Delete'>
                                </form> </td>

"; ?>
                        </tr>
                    <?php }
                } else {
                    ?>

                    <?php

                }

                ?>
                </tbody>
            </table>

            <?php
            ?>
        </div>
    </div>

</div>
</body>
<script type="text/javascript">
    $(document).ready(function () {
        $("#search").keypress(function () {
            $.ajax({
                type: 'POST',
                url: '../alteration/search.php?ID=REC',
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
</html>
