<?php
include ('db_conn.php');

$comid = $_POST['comid'];
$factorno = $_POST['factorno'];
$srnum = $_POST['srnum'];
$purposetreat = $_POST['purposetreat'];
$plandate = $_POST['plandate'];
$premisetype = $_POST['premisetype'];
$treattime = $_POST['treattime'];
$servadd = $_POST['servadd'];
$specinstruct = $_POST['specinstruct'];
$remarks = $_POST['remarks'];
$custfeedback = $_POST['custfeedback'];


mysqli_close($conn);
?>