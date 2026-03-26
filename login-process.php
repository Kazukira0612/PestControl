<?php
session_start();
include ('db_conn/db_conn.php');

if (isset($_POST['btn-login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $category = $_POST['category'];

    if ($category == "admin") {
        $sql = "SELECT * FROM admin WHERE adminId = '$username' AND adminPass = '$password'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['id'] = $row['adminId'];
            $_SESSION['cat'] = "admin";
            header("location: admin/dashboard.php");
        }
        else {
            echo "<script>alert('Invalid username or password');
            window.location.href = 'login.php';
            </script>";
        }
    }
    else if ($category == "employee") {
        $sql = "SELECT * FROM employee WHERE empId = '$username' AND empPass = '$password'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['id'] = $row['empId'];
            $_SESSION['cat'] = "employee";
            header("location: employee/dashboard.php");
        }
        else {
            echo "<script>alert('Invalid username or password');
            window.location.href = 'login.php';
            </script>";
        }
    }
}
mysqli_close($conn);
?>