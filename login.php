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
        <form class="login-screen" action ="login-process.php" method="POST">
        <div class="login-card">
            <div class="login-logo">JC</div>
            <h2>Login</h2>
            <p>JC Pest &amp; Hygiene Services</p>
            <div class="login-form">
            <div class="field-group">
                <label>Username</label>
                <input type="text" id="loginUser" name="username" placeholder="username" autocomplete="username">
            </div>
            <div class="field-group">
                <label>Password</label>
                <input type="password" id="loginPass" name="password" placeholder="••••••••" autocomplete="current-password">
            </div>
            <div class="category">
                <input type="radio" id="admin" name="category" value="admin" required>
                <label for="admin">Admin</label>
                <input type="radio" id="employee" name="category" value="employee">
                <label for="employee">Employee</label>
            </div>
            <div id="loginError" class="login-error"></div>
            <button type="submit" name="btn-login" class="btn-login" >Login →</button>
            </div>
        </div>
        </form>
</body>
</html>