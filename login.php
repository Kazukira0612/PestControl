<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/dashboard.css">
    <title>Document</title>
</head>
<body>
        <!-- LOGIN SCREEN -->
        <form id="loginScreen" class="login-screen" action ="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
        <div class="login-card">
            <div class="login-logo">JC</div>
            <h2>Login</h2>
            <p>JC Pest &amp; Hygiene Services</p>
            <div class="login-form">
            <div class="field-group">
                <label>Username</label>
                <input type="text" id="loginUser" placeholder="username" autocomplete="username">
            </div>
            <div class="field-group">
                <label>Password</label>
                <input type="password" id="loginPass" placeholder="••••••••" autocomplete="current-password" >
            </div>
            <div id="loginError" class="login-error"></div>
            <button type="submit" name="btn-login" class="btn-login" >Login →</button>
            </div>
        </div>
        </form>
</body>
</html>

<?php 
    if(isset($_POST['btn-login']))
    {
        header("location: admin/dashboard.php");
    }
?>