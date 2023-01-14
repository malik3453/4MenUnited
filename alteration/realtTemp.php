<?php
$ID = $_GET['NAME'];
$con = mysqli_connect("localhost", "root", "", "4men");
$alterationTypeID = explode(" ", $ID);
$customerID = $alterationTypeID[0];
$alterationTypeID = $alterationTypeID[1];
mysqli_query($con, "insert into retempalt (cusID, alterationTypeID) VALUES ('$customerID','$alterationTypeID')");
header('Location: realt.php?ID=' . $customerID);
exit();


