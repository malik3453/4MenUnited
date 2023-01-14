<?php
$ID = $_GET['NAME'];
$con = mysqli_connect("localhost", "root", "", "4men");


mysqli_query($con,"Insert into receivetemp (alterationID, realtID) values ('$ID','$ID')");
mysqli_query($con,"update realt set status = 'in-received' where realtID = '$ID'");

header('Location: receiveAlts.php');
exit();