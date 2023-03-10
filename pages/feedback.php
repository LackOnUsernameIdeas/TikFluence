<?php

//PHP код да изпращане на съобщение

    $siteOwnersEmail = 'nemabizenesnemapari@gmail.com';

    $errorMessage = "";

    if($_POST) {

        $name = trim(stripslashes($_POST['first_name']));
        $family = trim(stripslashes($_POST['last_name']));
        $email = trim(stripslashes($_POST['email']));
        $contact_message = trim(stripslashes($_POST['message']));
        $error = [];


        if (strlen($name) < 3) {
            $error['name'] = "Въведете име.";
        }

        if (strlen($family) < 3) {
            $error['family'] = "Въведете фамилия.";
        }

        if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $email)) {
            $error['email'] = "Въведете валиден имейл адрес.";
        }

        if (strlen($contact_message) < 1) {
            $error['message'] = "Моля въведете вашето съобщение. То не трябва да бъде повече от 100 символа.";
        }

        // if ($subject == '') {
        //     $subject = "Contact Form Submission";
        // }

        $message = "";

        $message .= "Имейл от: " . $name . "<br />";
        $message .= "Имейл адрес: " . $email . "<br />";
        $message .= "Съобщение: <br />";
        $message .= $contact_message;
        $message .= "<br /> ----- <br /> Този имейл беше изпратен от сайта 'TikFluence - ПГИ - гр.Перник '. <br />";


        $from =  $name . " <" . $email . ">";


        $headers = "From: " . $from . "\r\n";
        $headers .= "Reply-To: ". $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        if (!$error) 
        {
            ini_set("sendmail_from", $siteOwnersEmail);
            $mail = mail($siteOwnersEmail, $message, $headers); //, $subject

            if ($mail) 
            { 
                $errorMessage = "<div class='col-lg-6 col-md-6 col-sm-6 form-group contact-forms'>Вашето съобщение е изпратено.</div>"; 
                
            }
            
            else 
            { 
                $errorMessage = "<p style='color: red'>Възникна грешка, моля опитайте отново по-късно!</p>"; 
                
            }
        }

        else 
        {
            $response = (isset($error['name'])) ? $error['name'] . "<br /> \n" : null;
            $response = (isset($error['family'])) ? $error['family'] . "<br /> \n" : null;
            $response .= (isset($error['email'])) ? $error['email'] . "<br /> \n" : null;
            $response .= (isset($error['message'])) ? $error['message'] . "<br />" : null;

            $errorMessage = $response;
        }

    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>ПОВЕЧЕ ЗА НАС</title>
    <!-- Favicon-->
    <link rel="icon" href="../favicon1.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="../plugins/node-waves/waves.css" rel="stylesheet">

    <!-- Animation Css -->
    <link href="../plugins/animate-css/animate.css" rel="stylesheet">

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="../css/themes/all-themes.css" rel="stylesheet">
    
</head>
<body class="theme-purple ls-closed">
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars" style="display: block;"></a>
                <!-- <img src="favicon.ico" width="37" height="37"> -->
                <a class="navbar-brand" href="index.php">TIKFLUENCE</a>
            </div>
            
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <img src="../images/logo.jpg" width="300"> 

            <!-- Menu -->
            <div class="menu">
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;"><ul class="list" style="overflow: hidden; width: auto; height: 584px;">
                    <li class="header">ГЛАВНО МЕНЮ</li>
                    <li>
                        <a href="../index.php" class="toggled waves-effect waves-block">
                            <i class="material-icons">home</i>
                            <span>НАЧАЛО</span>
                        </a>
                    </li>
                    <li>
                        <a href="affectedSongs.php" class=" waves-effect waves-block">
                            <i class="material-icons">music_note</i>
                            <span>ПОВЛИЯНИ ПЕСНИ</span>
                        </a>
                    </li>
                    <li>
                        <a href="individualStats.php" class=" waves-effect waves-block">
                            <i class="material-icons">person_outline</i>
                            <span>МОИТЕ СТАТИСТИКИ В TIKTOK</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="menu-toggle waves-effect waves-block">
                            <i class="material-icons">insert_chart</i>
                            <span>ОЩЕ СТАТИСТИКИ</span>
                        </a>
                        <ul class="ml-menu">
                            <li>
                                <a href="songs.php" class="waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО</span>
                                </a>
                            </li>
                            <li>
                                <a href="songsBG.php" class=" waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</span>
                                </a>
                            </li>
                            <li>
                                <a href="tiktokers.php" class="waves-effect waves-block">
                                    <i class="material-icons">person</i>
                                    <span>ТОП 200 НАЙ-ИЗВЕСТНИ ТИКТОКЪРИ</span>
                                </a>
                            </li>
                            <li>
                                <a href="topVideos.php" class="waves-effect waves-block">
                                    <i class="material-icons">play_circle_outline</i>
                                    <span>ТОП 200 НАЙ-ГЛЕДАНИ ВИДЕА В TIKTOK</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <li class="active">
                        <a href="#" class=" waves-effect waves-block">
                            <i class="material-icons">info</i>
                            <span>ЗА КОНТАКТ</span>
                        </a>
                    </li>
                    <!-- <li class="header"></li> -->
                </ul><div class="slimScrollBar" style="background: rgba(0, 0, 0, 0.5); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 584px;"></div><div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    <a href="javascript:void(0);"><a href="privacyPolicy.php">Политика за поверителност</a> ,</a>
                </div>
                <div class="copyright">
                    <a href="javascript:void(0);"><a href="termsAndConditions.php">Правила и Условия</a></a>
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->

    </section>

    <section class="content">

        <div class="container-fluid">
            <div class="block-header">
                
                <!-- Blockquotes -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h1>Свържете се с нас:</h1>
                                <h3 style="color:green;"><?php echo $errorMessage != "" ? $errorMessage : "" ?></h3>
                                <br>
                                <br>
                            </div>
                            <div class="body">
                                <!-- contact -->
                                    <div class="container py-lg-5 py-md-5 py-sm-4 py-3">
                                        <div class="row">
                                            <div class="col-lg-7 col-md-7">
                                                <form action="#" method="post">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 form-group contact-forms">
                                                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Име" required="">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group contact-forms">
                                                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Фамилия" required="">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group contact-forms">
                                                            <input type="email" class="form-control" id="email" name="email" placeholder="Е-мейл" required="">
                                                        </div>
                                                    </div>
                                                    <div class=" form-group contact-forms">
                                                        <textarea class="form-control" rows="8" id="message" name="message" placeholder="Напиши съобщение" required=""></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-primary sent-butnn btn-lg" style="font-size:18px;">Изпрати</button>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <!--//contact -->
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- #END# Blockquotes -->
                
                <!-- Body Copy -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h1>
                                    Информация за нас
                                </h1>
                            </div>
                            <div class="body" style="font-size:18px;">
                                <p>
                                    Ние сме ученици от Vlllб и IXб клас на Професионална гимназия по икономика - гр.Перник. Специалност - Икономическа информатика. 
                                </p>
                                <p>
                                    Калоян Костадинов се погрижи за софтуера, а Николай Георгиев за дизайна.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- #END# Body Copy -->
                <!-- Body Copy -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h1>
                                    Адрес: 2302, гр. Перник, ул. Г. Мамарчев 2 - ПГИ Перник 
                                </h1>
                            </div>
                            <div class="body">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1234.6835304503172!2d23.048159630807987!3d42.60646545927766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14aacadf4ba43b73%3A0x28028560258f88be!2sProfesionalna%20Gimnaziya%20PO%20Ikonomika!5e0!3m2!1sen!2sbg!4v1676395658037!5m2!1sen!2sbg" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- #END# Body Copy -->
 
            </div>
            
        </div>
        <!-- Footer -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="body">
                    
                    <div class="legal">
                        <?php include '../footer.php';?>
                    </div>
                            
                </div>
            </div>
        </div>
        <!-- #Footer -->  
    </section>

    <!-- Jquery Core Js -->
    <script async="" src="https://www.google-analytics.com/analytics.js"></script><script src="../plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="../plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="../plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="../plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="../plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="../plugins/raphael/raphael.min.js"></script>
    <script src="../plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="../plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="../plugins/flot-charts/jquery.flot.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="../plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="../js/admin.js"></script>
    <script src="../js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="../js/demo.js"></script>


<div id="torrent-scanner-popup" style="display: none;"></div>
</body>
</html>