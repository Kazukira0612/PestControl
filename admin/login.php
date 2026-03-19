<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Document</title>
</head>
<body>
        <!-- LOGIN SCREEN -->
        <div id="loginScreen" class="login-screen">
        <div class="login-card">
            <div class="login-logo">JC</div>
            <h2>Admin Login</h2>
            <p>JC Pest &amp; Hygiene Services</p>
            <div class="login-form">
            <div class="field-group">
                <label>Username</label>
                <input type="text" id="loginUser" placeholder="admin" autocomplete="username">
            </div>
            <div class="field-group">
                <label>Password</label>
                <input type="password" id="loginPass" placeholder="••••••••" autocomplete="current-password" onkeydown="if(event.key==='Enter') doLogin()">
            </div>
            <div id="loginError" class="login-error"></div>
            <button class="btn-login" onclick="">Login →</button>
            </div>
        </div>
        </div>
</body>
</html>