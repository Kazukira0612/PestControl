<?php
session_start();

include ('db_conn/db_conn.php');

$id = $_SESSION['id'];
$cat = $_SESSION['cat'];

if ($cat == "admin") {
    $sql = "SELECT * FROM admin WHERE adminId = '$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $name = $row['adminName'];
    $id = $row['adminId'];
}
else if ($cat == "employee") {
    $sql = "SELECT * FROM employee WHERE empId = '$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $name = $row['empName'];
    $id = $row['empId'];
}
else {
    header("location: ../index.php");
}
?>