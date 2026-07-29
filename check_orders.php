<?php
$db_host = 'localhost';
$db_username = 'apfkgyek_whmc291';
$db_password = 't0pp65)SX!';
$db_name = 'apfkgyek_whmc291';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check what status values exist in orders
$sql = "SELECT DISTINCT status FROM tblorders";
$result = $conn->query($sql);

echo "Order statuses in database:<br>";
while($row = $result->fetch_assoc()) {
    echo "- " . $row['status'] . "<br>";
}

echo "<br>";

$sql2 = "SELECT COUNT(*) as cnt, status FROM tblorders GROUP BY status";
$result2 = $conn->query($sql2);

echo "Order counts by status:<br>";
while($row = $result2->fetch_assoc()) {
    echo $row['status'] . ": " . $row['cnt'] . "<br>";
}

echo "<br>--- PENDING ORDERS ---<br>";

$sql3 = "SELECT o.id, o.ordernum, o.date, o.amount, o.status, o.userid, c.firstname, c.lastname, c.companyname 
        FROM tblorders o 
        LEFT JOIN tblclients c ON o.userid = c.id 
        WHERE o.status = 'Pending' 
        ORDER BY o.date DESC";
$result3 = $conn->query($sql3);

echo "Found " . $result3->num_rows . " pending orders<br>";
while($row = $result3->fetch_assoc()) {
    $name = trim($row['firstname'] . ' ' . $row['lastname']);
    if (empty($name)) $name = $row['companyname'] ?: 'User #' . $row['userid'];
    echo "- " . $name . " - $" . number_format($row['amount'], 2) . " (" . $row['date'] . ")<br>";
}

$conn->close();
?>