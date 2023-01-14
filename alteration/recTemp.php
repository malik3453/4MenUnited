<?php
$ID = $_GET['NAME'];
$con = mysqli_connect("localhost", "root", "", "4men");


mysqli_query($con,"Insert into receivetemp (alterationID) values ('$ID')");
mysqli_query($con,"update alteration set status = 'in-received' where alterationID = '$ID'");

header('Location: receiveAlts.php');
exit();