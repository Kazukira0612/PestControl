<?php
$db_host = "localhost";
$db_user = "root";
$db_pwd = "";
$db_name = "pestcontrol";

$conn = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);

if(!$conn) {
    die(mysqli_connect_error());
}
?>