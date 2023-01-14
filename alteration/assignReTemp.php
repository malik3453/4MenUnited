<?php
$ID = $_GET['NAME'];
$con = mysqli_connect("localhost", "root", "", "4men");


mysqli_query($con,"Insert into assigntemp (alterationID, realtID) values ('$ID','$ID')");
mysqli_query($con,"update realt set status = 'in-assigned' where realtID = '$ID'");

header('Location: assign.php');
exit();