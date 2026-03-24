<?php
$db_host = "localhost";
$db_user = "root";
$db_pwd = "";
$db_name = "pest_control";

$conn = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);

if(!$conn) {
    die(mysqli_connect_error());
}else{
//echo "<br>Dah masuk database pest control yeayy <br>";
}
?>