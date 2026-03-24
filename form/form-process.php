<?php
include ('db_conn.php');

$treatmentPurpose = $_POST['treatmentPurpose'];
$plannedDate = $_POST['plannedDate'];
$typeOfPremise = $_POST['typeOfPremise'];
$treatmentTime = $_POST['treatmentTime'];
$serviceAddress = $_POST['serviceAddress'];
$specialinstruction = $_POST['specialinstruction'];
$remarks = $_POST['remarks'];
$pestType = $_POST['pestType'];
 
$mysql = "INSERT INTO customer
          (custId, treatmentPurpose, plannedDate,typeOfPremise"
 
mysqli_close($conn);
?>