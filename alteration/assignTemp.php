<?php
$ID = $_GET['NAME'];
$con = mysqli_connect("localhost", "root", "", "4men");


mysqli_query($con,"Insert into assigntemp (alterationID) values ('$ID')");
mysqli_query($con,"update alteration set status = 'in-assign' where alterationID = '$ID'");

header('Location: assign.php');
exit();