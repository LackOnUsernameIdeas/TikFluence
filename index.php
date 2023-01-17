<?php

    session_start();

    include "./includes/databaseManager.class2.php";

    $db = new DatabaseManager();

    if (isset($_SESSION["user_id"])) {
        $user = $db->getUserById(isset($_SESSION["user_id"]));
    }

    $is_invalid = false;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $user2 = $db->validateEmailForLogIn();

        if ($user2) {
            
            if (password_verify($_POST["password"], $user2["password_hash"])) {
                
                session_start();
                
                session_regenerate_id();
                
                $_SESSION["user_id"] = $user2["id"];
                
                header("Location: ./home.php");
                exit;
            }
        }
        
        $is_invalid = true;

}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>TOKFLUENCE</title>
    <!-- Favicon-->
    <link rel="icon" href="./favicon1.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <small>ДОБРЕ ДОШЛИ В</small>
            <a href="javascript:void(0);"><b style="color:#04B790">TOK</b><b>Flu</b><b style="color:#FF4162">ence</b></a></a>
        </div>
        <div class="card">
            <div class="body">
                <?php if (isset($user)): ?>
                    <?=
                        header("Location: ./home.php");
                        exit; 
                    ?>
                <?php else: ?>

                    <?php if ($is_invalid): ?>
                        
                        <em><i class="material-icons">warning</i>Невалидно вписване</em>
                        <br>
                        <br>
                    <?php endif; ?>
                    <form id="sign_in" method="POST">
                        <div class="msg">Влезте във вашия профил</div>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="material-icons">person</i>
                            </span>
                            <div class="form-line">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Имейл" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required autofocus>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="material-icons">lock</i>
                            </span>
                            <div class="form-line">
                                <input type="password" class="form-control" name="password" placeholder="Парола" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-15 p-t-10">
                                <div class="col-xs-6">
                                    <a href="sign-up.php">Регистриране</a>
                                </div>
                                <div class="col-xs-6">
                                    <button class="btn btn-block bg-pink waves-effect" type="submit">ВЛИЗАНЕ</button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/examples/sign-in.js"></script>
</body>

</html>