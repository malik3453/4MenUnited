<?php

// Create connection
$con = mysqli_connect("localhost", "root", "", "4men");
$ID = $_GET['ID'];
$type = substr($ID, 0, 3);
$ID = substr($ID, 3);


if ($type === "ALT") {
    $sql = "SELECT * FROM alterationtype WHERE alterationtype.aName LIKE '" . $_POST['name'] . "%'";
    $result = mysqli_query($con, $sql);
    $url = 'tempAlt.php?NAME=' . $ID . '+';
    ?>
    <tr>
        <th>Alteration</th>
        <th>Cost</th>
    </tr>
    <?php


    while ($row = mysqli_fetch_assoc($result)) {
        if (mysqli_num_rows($result) > 0) {
            ?>
            <tr>
                <td><a href="<?php echo $url . $row['alterationTypeID']; ?>"> <?php echo $row['aName'] ?></a></td>
                <td><?php echo $row['aPrice'] ?></td>

            </tr>
            <?php
        } else {
            echo "<tr><td>0 result's found</td></tr>";
        }
    }
}

if ($type === "RAT") {
    $sql = "SELECT * FROM alterationtype WHERE alterationtype.aName LIKE '" . $_POST['name'] . "%'";
    $result = mysqli_query($con, $sql);
    $url = 'realtTemp.php?NAME=' . $ID . '+';
    ?>

    <tr>
        <th>Alteration</th>
        <th>Cost</th>

    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        if (mysqli_num_rows($result) > 0) {
            ?>
            <tr>
                <td><a href="<?php echo $url . $row['alterationTypeID']; ?>"> <?php echo $row['aName'] ?></a></td>
                <td><?php echo $row['aPrice'] ?></td>

            </tr>
            <?php
        } else {
            echo "<tr><td>0 result's found</td></tr>";
        }
    }
}
if ($type === "REC") {
    $sql = "SELECT * FROM alteration join customerdetails on alteration.customerID = customerdetails.customerID left join realt r on customerdetails.customerID = r.customerID WHERE (alteration.status = 'Altering' or r.status = 'Re-Altering') AND (customerName like '%" . $_POST['name'] . "%' or r.realtID like '%" . $_POST['name'] . "%'  or alteration.alterationID LIKE '%" . $_POST['name'] . "%')";
    $result = mysqli_query($con, $sql);
    $url = 'recTemp.php?NAME=';
    ?>

    <tr>
        <th>Alteration Number</th>
        <th>Customer Name</th>

    </tr>
    <?php

    while ($row = mysqli_fetch_assoc($result)) {
        if (mysqli_num_rows($result) > 0) {

            $getType = $row['realtID'];
            if ($getType === null) {
                $x = 'AT' . $row['alterationID'];
                $url = 'recTemp.php?NAME=';

            } else {
                $x = 'RT' . $row['alterationID'];
                $url = 'recReTemp.php?NAME=';

            }


            ?>
            <tr>
                <td><a href="<?php echo $url . $row['alterationID']; ?>"> <?php echo $x ?></a></td>
                <td><?php echo $row['customerName'] ?></td>

            </tr>
            <?php
        } else {
            echo "<tr><td>0 result's found</td></tr>";
        }
    }
}

if ($type === "ASN") {
    $sql = "SELECT * FROM alteration join customerdetails on alteration.customerID = customerdetails.customerID left join realt r on customerdetails.customerID = r.customerID WHERE (alteration.status = 'Altering' or r.status = 'Re-Altering') AND (customerName like '%" . $_POST['name'] . "%' or r.realtID like '%" . $_POST['name'] . "%'  or alteration.alterationID LIKE '%" . $_POST['name'] . "%')";
    $result = mysqli_query($con, $sql);
    $url = 'assignTemp.php?NAME=';
    ?>
    <tr>
        <th>Alteration Number</th>
        <th>Customer Name</th>

    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        if (mysqli_num_rows($result) > 0) {

            $getType = $row['realtID'];
            if ($getType === null) {
                $x = 'AT' . $row['alterationID'];
                $url = 'assignTemp.php?NAME=';

            } else {
                $x = 'RT' . $row['alterationID'];
                $url = 'assignReTemp.php?NAME=';
            }


            ?>
            <tr>
                <td><a href="<?php echo $url . $row['alterationID']; ?>"> <?php echo $x ?></a></td>
                <td><?php echo $row['customerName'] ?></td>

            </tr>
            <?php
        } else {
            echo "<tr><td>0 result's found</td></tr>";
        }
    }

}


?>


