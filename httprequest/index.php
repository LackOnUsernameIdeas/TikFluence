<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $mysqli = require __DIR__ . "./logIn/database.php";
    
    $sql = "SELECT * FROM users
            WHERE id = {$_SESSION["user_id"]}";
            
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    
    <?php if (isset($user)): ?>
        
        <!-- <p>Hello <?= htmlspecialchars($user["name"]) ?></p> -->

        
        <p><a href="./logIn/logout.php">Log out</a></p>
        
    <?php else: ?>
        
        <p><a href="./logIn/login.php">Log in</a> or <a href="./logIn/singup.html">sign up</a></p>
        
    <?php endif; ?>
</body>
</html>
    