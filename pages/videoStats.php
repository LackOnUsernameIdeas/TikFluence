<?php

    //Вмъкване на нужните файлове
    include "../selectDate.php";
    include '../includes/databaseManager.php';
    include '../includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в songs.php
    $vid = isset($_GET["vid"]) && ctype_digit($_GET['vid']) ? intval($_GET["vid"]) : -1;
    if($vid < 0) redirect("additionalStats.php");
    
    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    //Запазваме данните за видеото в променлива
    $videoDatapoints = $db->getVideoData($vid);

    //Осигуряваме си необходимите данни

    $selectDate = isset($_SESSION["setDate"]) ? $_SESSION["setDate"] : date("Y-m-d");

    $videoDataForSpecificDate = $db->getVideoDataForSpecificDate($vid, $selectDate);


    $dates = [];

    $shares = [];
    $likes = [];
    $views = [];

    foreach($videoDatapoints as $dp){

        $timestamp = new DateTime($dp["fetch_date"]);
        $dates[] = $timestamp->format('Y-m-d');

        $shares[] = $dp["shares_count"];
        $likes[] = $dp["likes_count"];
        $views[] = $dp["plays_count"];
    }
    
    $vidUrl = $videoDatapoints[0]["video_url"];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Статистики за видео</title>
    <!-- Favicon-->
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="../plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="../plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="../css/themes/all-themes.css" rel="stylesheet" />
</head>

<body class="theme-purple">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Моля изчакайте...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="../index.php">NOIT - НОИТ 2023</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">

                <ul class="nav navbar-nav navbar-right">
                    <!-- TOP RIGHT -->
                    <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">invert_colors</i></a></li>
                </ul>

            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="body m-l-85 m-t-25">

                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">ГЛАВНО МЕНЮ</li>
                    <li>
                        <a href="../index.php">
                            <i class="material-icons">home</i>
                            <span>НАЧАЛО</span>
                        </a>
                    </li>
                    <li>
                        <a href="songs.php">
                            <i class="material-icons">music_note</i>
                            <span>ПЕСНИ</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">trending_down</i>
                            <span>Multi Level Menu</span>
                        </a>
                        <ul class="ml-menu">
                            <li>
                                <a href="javascript:void(0);">
                                    <span>Menu Item</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <span>Menu Item - 2</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="menu-toggle">
                                    <span>Level - 2</span>
                                </a>
                                <ul class="ml-menu">
                                    <li>
                                        <a href="javascript:void(0);">
                                            <span>Menu Item</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="menu-toggle">
                                            <span>Level - 3</span>
                                        </a>
                                        <ul class="ml-menu">
                                            <li>
                                                <a href="javascript:void(0);">
                                                    <span>Level - 4</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="individualStats.php">
                            <i class="material-icons">person_outline</i>
                            <span>СТАТИСТИКИ ЗА ПОТРЕБИТЕЛЯ</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="additionalStats.php">
                            <i class="material-icons">insert_chart</i>
                            <span>ОЩЕ СТАТИСТИКИ</span>
                        </a>
                    </li>
                    <li>
                        <a href="changelogs.php">
                            <i class="material-icons">update</i>
                            <span>Changelogs</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    <a href="javascript:void(0);"><a href="privacyPolicy.php">Privacy Policy</a> ,</a>
                </div>
                <div class="copyright">
                    <a href="javascript:void(0);"><a href="termsAndConditions.php">Terms and Conditions</a></a>
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        <aside id="rightsidebar" class="right-sidebar">
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                <li role="presentation" class="active"><a href="#skins" data-toggle="tab">ФОН</a></li>
                <li role="presentation"><a href="#settings" data-toggle="tab">НАСТРОЙКИ</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                <ul class="demo-choose-skin">
                        <li data-theme="purple" class="active">
                            <div class="purple"></div>
                            <span>Лилаво</span>
                        </li>
                        <li data-theme="red">
                            <div class="red"></div>
                            <span>Червено</span>
                        </li>
                        <li data-theme="pink">
                            <div class="pink"></div>
                            <span>Розово</span>
                        </li>
                        <li data-theme="deep-purple">
                            <div class="deep-purple"></div>
                            <span>Тъмно Лилаво</span>
                        </li>
                        <li data-theme="indigo">
                            <div class="indigo"></div>
                            <span>Индиго</span>
                        </li>
                        <li data-theme="blue">
                            <div class="blue"></div>
                            <span>Синьо</span>
                        </li>
                        <li data-theme="light-blue">
                            <div class="light-blue"></div>
                            <span>Светло синьо</span>
                        </li>
                        <li data-theme="cyan">
                            <div class="cyan"></div>
                            <span>Циан</span>
                        </li>
                        <li data-theme="teal">
                            <div class="teal"></div>
                            <span>Синьозелен</span>
                        </li>
                        <li data-theme="green">
                            <div class="green"></div>
                            <span>Зелено</span>
                        </li>
                        <li data-theme="light-green">
                            <div class="light-green"></div>
                            <span>Светло зелено</span>
                        </li>
                        <li data-theme="lime">
                            <div class="lime"></div>
                            <span>Лайм</span>
                        </li>
                        <li data-theme="yellow">
                            <div class="yellow"></div>
                            <span>Жълто</span>
                        </li>
                        <li data-theme="amber">
                            <div class="amber"></div>
                            <span>Кехлибарено</span>
                        </li>
                        <li data-theme="orange">
                            <div class="orange"></div>
                            <span>Оранжево</span>
                        </li>
                        <li data-theme="deep-orange">
                            <div class="deep-orange"></div>
                            <span>Тъмно оранжево</span>
                        </li>
                        <li data-theme="brown">
                            <div class="brown"></div>
                            <span>Кафяво</span>
                        </li>
                        <li data-theme="grey">
                            <div class="grey"></div>
                            <span>Сиво</span>
                        </li>
                        <li data-theme="blue-grey">
                            <div class="blue-grey"></div>
                            <span>Тъмно сиво</span>
                        </li>
                        <li data-theme="black">
                            <div class="black"></div>
                            <span>Черно</span>
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="settings">
                    <div class="demo-settings">
                        <p>GENERAL SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Report Panel Usage</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Email Redirect</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>SYSTEM SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Notifications</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Auto Updates</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>ACCOUNT SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Offline</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Location Permission</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
        <!-- #END# Right Sidebar -->
    </section>
    
    <section class="content">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="body">
                    <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='additionalStats.php'">
                        <i class="material-icons">arrow_back</i>
                        <span>НАЗАД</span>
                    </button>
                    <div class="block-header">
                        <div class="card">
                            <div class="body">
                                <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                                <ol class="breadcrumb breadcrumb-col-black">
                                    <li onclick="window.location.href='../index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                    <li onclick="window.location.href='additionalStats.php'"><a href="javascript:void(0);"><i class="material-icons">insert_chart</i>ОЩЕ СТАТИСТИКИ</a></li>
                                    <li class="active"><i class="material-icons">insert_chart</i>СТАТИСТИКИ ЗА ВИДЕОТО</li>
                                </ol>
                            </div>
                        </div>
                    </div>
       
                    <div class="block-header">
                        <div class="card">
                            <div class="body">
                                <h2>Изберете дата за която искате да видите данни:</h2>
                                <div class="btn-group">
                                    <button type="button" class="btn bg-purple dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only" id="setDateButton"><?php echo $selectDate ?></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <?php if($dates):?>
                                            <?php foreach(array_slice($dates, 1) as $date):?>
                                                <li data-id="<?php echo $date ?>" data-role="setDate"><a href="javascript:void(0);" class="waves-effect waves-block"><?php echo $date?></a></li>
                                                <li role="separator" class="divider"></li>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </ul>
                                
                                </div>
                            </div>
                        </div>
                    </div>
            
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">equalizer</i>
                        </div>
                        <div class="content">
                            <div class="text">ГЛЕДАНИЯ</div>
                            <div class="number"><?php echo number_format($videoDataForSpecificDate["plays_count"]) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-brown hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">equalizer</i>
                        </div>
                        <div class="content">
                            <div class="text">СПОДЕЛЯНИЯ</div>
                            <div class="number"><?php echo number_format($videoDataForSpecificDate["shares_count"]) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-deep-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">equalizer</i>
                        </div>
                        <div class="content">
                            <div class="text">ХАРЕСВАНИЯ</div>
                            <div class="number"><?php echo number_format($videoDataForSpecificDate["likes_count"]) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="card">
                <div class="header">
                    <h2>
                        ИЗМЕНЕНИЕ НА ГЛЕДАНИЯТА НА ВИДЕОТО
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="content">
                        <canvas id="ViewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="card">
                <div class="body">
                    <div class="content">
                        <blockquote class="tiktok-embed" cite="<?php echo $vidUrl ?>" data-video-id="<?php echo substr($vidUrl, -19) ?>" style="max-width: 360px;min-width: 325px;border: 0px; max-height: 585px;"> 
                            <section></section> 
                        </blockquote> 
                        <script async src="https://www.tiktok.com/embed.js"></script>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="card">
                <div class="header">
                    <h2>
                        ИЗМЕНЕНИЕ НА СПОДЕЛЯНИЯТА НА ВИДЕОТО
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="content">
                        <canvas id="SharesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="card">
                <div class="header">
                    <h2>
                        ИЗМЕНЕНИЕ НА ХАРЕСВАНИЯТА НА ВИДЕОТО
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="content">
                        <canvas id="LikesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Статистики -->
    <script>
        //Данни за ползване
        let dates =  JSON.parse('<?php echo json_encode($dates) ?>');

        let shares = JSON.parse('<?php echo json_encode($shares) ?>');
        let likes = JSON.parse('<?php echo json_encode($likes) ?>');
        let views = JSON.parse('<?php echo json_encode($views) ?>');


        //Статистика за проследяване на споделянията на дадена песен
        new Chart(document.getElementById('SharesChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Споделяния',
                        data: shares, //y
                        borderColor: 'rgba(160, 82 ,45, 1)',
                        backgroundColor: 'rgba(160, 82, 45, 0.3)',
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: "left"
                    }
                }
            }
        });

        //Статистика за проследяване на харесванията на дадена песен
        new Chart(document.getElementById('LikesChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Харесвания',
                        data: likes, //y
                        borderColor: 'rgba(255, 148, 112, 1)',
                        backgroundColor: 'rgba(255, 148, 112, 0.3)',
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: "left"
                    }
                }
            }
        });

        
        //Статистика за проследяване на гледанията на дадена песен
        new Chart(document.getElementById('ViewsChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Гледания',
                        data: views, //y
                        borderColor: 'rgba(255, 99, 132, 0.9)',
                        backgroundColor: 'rgba(255, 99, 132, 0.3)',
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: "left"
                    }
                }
            }
        });
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $(document).on("click", "li[data-role=setDate]", function(){

                let date = $(this).data('id');

                $.ajax({
                    type: "POST",
                    url: "../selectDate.php",
                    data: {setDate: date},
                    success: function(data){
                        $("#setDateButton").html(data);
                        window.location.reload();
                    }
                });

            });
        });

    </script>

<!-- Jquery Core Js -->
<script src="../plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap Core Js -->
<script src="../plugins/bootstrap/js/bootstrap.js"></script>

<!-- Select Plugin Js -->
<script src="../plugins/bootstrap-select/js/bootstrap-select.js"></script>

<!-- Slimscroll Plugin Js -->
<script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

<!-- Waves Effect Plugin Js -->
<script src="../plugins/node-waves/waves.js"></script>

<!-- Custom Js -->
<script src="../js/admin.js"></script>

<!-- Demo Js -->
<script src="../js/demo.js"></script>