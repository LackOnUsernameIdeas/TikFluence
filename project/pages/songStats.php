﻿<?php

    //Вмъкване на нужните файлове
    include '../includes/databaseManager.php';
    include '../includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в songs.php
    $sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
    if($sid < 0) redirect("songs.php");

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    //Взимаме записите за всяка песен
    $dataPoints = $db->getDatapointsForSong($sid);
    if($dataPoints === false) redirect("songs.php");

    //Взимаме необходимата информация и я превръщаме където е необходимо в проценти

    $todayYesterdayData = $db->getTodayYesterdayData($sid);
    $songData = $db->getSongData($sid);


    $dates = [];

    $syPercent = [];
    $ttPercent = [];
    $ytPercent = [];

    $ttNums = [];
    $ytNums = [];
    $syNums = [];

    foreach($dataPoints as $dp){
        $timestamp = new DateTime($dp["fetch_date"]);
        $dates[] = $timestamp->format('Y-m-d');

        $syNums[] = $dp["spotify_popularity"];
        $syPercent[] = $dp["spotify_popularity"];

        $ttNums[] = $dp["number_of_videos_last_14days"];
        $ttPercent[] = $dp["number_of_videos_last_14days"];

        $ytNums[] = $dp["youtube_views"];
        $ytPercent[] = $dp["youtube_views"];
    }

    $maxTiktok = max($ttPercent);
    $maxYoutube = max($ytPercent);
    $maxSpotify = max($syPercent);

    for($i=0; $i<count($ttPercent); $i++){
        $ttPercent[$i] = $maxTiktok ? ($ttPercent[$i] * 100)/$maxTiktok : null;
    }

    for($i=0; $i<count($ytPercent); $i++){
        $ytPercent[$i] = $maxYoutube ? ($ytPercent[$i] * 100)/$maxYoutube : null;
    }

    for($i=0; $i<count($syPercent); $i++){
        $syPercent[$i] = $maxSpotify ? ($syPercent[$i] * 100)/$maxSpotify : null;
    }



    //Взимаме необходимите данни(числа)
    $ttLastTwoDaysPercents = [];
    $ttLastTwoDaysNums = [];

    $ytLastTwoDaysPercents = [];
    $ytLastTwoDaysNums = [];

    $syLastTwoDays = [];

    foreach($todayYesterdayData as $d){
        $ttLastTwoDaysPercents[] = $d["number_of_videos_last_14days"];
        $ttLastTwoDaysNums[] = $d["number_of_videos_last_14days"];

        $ytLastTwoDaysPercents[] = $d["youtube_views"];
        $ytLastTwoDaysNums[] = $d["youtube_views"];

        $syLastTwoDays[] = $d["spotify_popularity"];
    }

    // Превръщаме числата в проценти

    $todayYesterdayTTDataArray = [];

    foreach($ttLastTwoDaysPercents as $TT){
        array_push($todayYesterdayTTDataArray, ($TT * 100)/$ttLastTwoDaysPercents[0]);
    }

    $todayYesterdayYTDataArray = [];

    foreach($ytLastTwoDaysPercents as $YT){
        if($ytLastTwoDaysPercents[0] != null){ 
            array_push($todayYesterdayYTDataArray, ($YT * 100)/$ytLastTwoDaysPercents[0]);
        } else {
            array_push($todayYesterdayYTDataArray, 0);
        }
    }

    $todayYesterdaySYDataArray = [];

    foreach($syLastTwoDays as $SY){
        if($syLastTwoDays[0] != null){ 
            array_push($todayYesterdaySYDataArray, ($SY * 100)/$syLastTwoDays[0]);
        } else {
            array_push($todayYesterdaySYDataArray, 0);
        }
    }
    
    // Изчисляваме разликата между днес и вчера:

    //TikTok
    $subtractionTTPercents = $todayYesterdayTTDataArray[1] - $todayYesterdayTTDataArray[0];
    $subtractionTTNums = $ttLastTwoDaysNums[1] - $ttLastTwoDaysNums[0];

    //YouTube
    if($ytLastTwoDaysPercents[0] != null){ 
        $subtractionYTPercents = $todayYesterdayYTDataArray[1] - $todayYesterdayYTDataArray[0];
        $subtractionYTNums = $ytLastTwoDaysNums[1] - $ytLastTwoDaysNums[0];
    } else { 
        $subtractionYTPercents = 0;
    }

    //Spotify
    if($syLastTwoDays[0] != null){
        $subtractionSY = $todayYesterdaySYDataArray[1] - $todayYesterdaySYDataArray[0];
    } else { 
        $subtractionSY = 0;
    }

    //Избираме подходяща икона за кутийките:
    
    $chooseIconTT = "";
    $chooseIconYT = "";
    $chooseIconSY = "";

    if($subtractionTTPercents == 0){
        $chooseIconTT = "trending_flat";
    } else if($subtractionTTPercents > 0){
        $chooseIconTT = "trending_up";
    } else {
        $chooseIconTT = "trending_down";
    }

    if($subtractionYTPercents == 0){
        $chooseIconYT = "trending_flat";
    } else if($subtractionYTPercents > 0){
        $chooseIconYT = "trending_up";
    } else {
        $chooseIconYT = "trending_down";
    }

    if($subtractionSY > 0){
        $chooseIconSY = "trending_up";
    } else if($subtractionSY == 0){
        $chooseIconSY = "trending_flat";
    } else {
        $chooseIconSY = "trending_down";
    }


    //Взимаме днешните данни и ги показваме:

    //TikTok
    $todayTT = $ttLastTwoDaysNums[1];

    //YouTube
    if($ytLastTwoDaysPercents[0] != null){ 
        $todayYT = $ytLastTwoDaysPercents[1];
    } else { 
        $todayYT = "-";
    }

    //Spotify
    if($syLastTwoDays[0] != null){ 
        $todaySY = $syLastTwoDays[1];
    } else { 
        $todaySY = "-";
    }


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Статистики за <?= htmlspecialchars($songData["song_name"]); ?></title>
    <!-- Favicon-->
    <link rel="icon" href="../favicon1.ico" type="image/x-icon">

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
                <a class="navbar-brand" href="../index.php">TIKFLUENCE - НОИТ 2023</a>
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
                    <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='songs.php'">
                        <i class="material-icons">arrow_back</i>
                        <span>НАЗАД</span>
                    </button>
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
                    <li class="active">
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
                    <li>
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
                    &copy; 2016 - 2017 <a href="javascript:void(0);">AdminBSB - Material Design</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0.5
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
        <div class="container-fluid">

            <div class="col-lg-14 col-md-14 col-sm-14 col-xs-14">
                <div class="card">
                    <div class="body">
                        <div class="block-header">
                            <div class="card">
                                <div class="body">
                                    <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                                    <ol class="breadcrumb breadcrumb-col-black">
                                        <li onclick="window.location.href='../index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                        <li onclick="window.location.href='songs.php'"><a href="javascript:void(0);"><i class="material-icons">music_note</i>ПЕСНИ</a></li>
                                        <li class="active"><i class="material-icons">music_note</i>СТАТИСТИКИ ЗА <?php echo $songData["song_name"]?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="body">
                                <div class="block-header">
                                    <h2>СТАТИСТИКИ ЗА:</h2>
                                    <h1><?php echo $songData["song_name"] ?> </h1><h2>ОТ</h2>
                                    <h1><?php echo $songData["artist_name"] ?></h1>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-deep-purple hover-zoom-effect">
                    <div class="icon">
                        <i class="material-icons">music_note</i>
                    </div>
                    <div class="content">
                        <div class="text">TikTok видея</div>
                        <div class="number"><?php echo $todayTT ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-red hover-zoom-effect">
                    <div class="icon">
                        <i class="material-icons">play_circle_outline</i>
                    </div>
                    <div class="content">
                        <div class="text">YouTube гледания</div>
                        <div class="number"><?php echo $todayYT ?></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-green hover-zoom-effect">
                    <div class="icon">
                        <i class="material-icons">playlist_play</i>
                    </div>
                    <div class="content">
                        <div class="text">Spotify популярност</div>
                        <div class="number"><?php echo $todaySY ?></div>
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В ОТДЕЛНИТЕ ПЛАТФОРМИ
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
                                <canvas id="graphChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Промяна в популярност от вчера:</h2>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box-3 bg-deep-purple hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">
                            <?php echo $chooseIconTT ?>
                        </i>
                    </div>
                    <div class="content">
                        <div class="text">TikTok</div>
                        <div class="number"><?php echo round($subtractionTTPercents, 3) ?>%</div>
                    </div>
                </div>
            </div>  
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box-3 bg-red hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">
                            <?php echo $chooseIconYT ?>
                        </i>
                    </div>
                    <div class="content">
                        <div class="text">YouTube</div>
                        <div class="number"><?php echo round($subtractionYTPercents, 3) ?>%</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box-3 bg-green hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">
                            <?php echo $chooseIconSY ?>
                        </i>
                    </div>
                    <div class="content">
                        <div class="text">Spotify</div>
                        <div class="number"><?php echo round($subtractionSY, 3) ?>%</div>
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В ОТДЕЛНИТЕ ПЛАТФОРМИ
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
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В ОТДЕЛНИТЕ ПЛАТФОРМИ
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
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В TIKTOK
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
                                <canvas id="barChart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Промяна в популярност от вчера:</h2>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-deep-purple hover-zoom-effect">
                    <div class="icon">
                        <i class="material-icons">music_note</i>
                    </div>
                    <div class="content">
                        <div class="text">TikTok</div>
                        <div class="number"><?php echo $subtractionTTNums ?></div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- LINE CHART -->
    <script>

        let dates =  JSON.parse('<?php echo json_encode($dates) ?>');
        let ytData = JSON.parse('<?php echo json_encode($ytPercent) ?>');
        let syData = JSON.parse('<?php echo json_encode($syPercent) ?>');
        let ttData = JSON.parse('<?php echo json_encode($ttPercent) ?>');

        new Chart(document.getElementById('graphChart'), {
        type: 'line',
        data: {
            labels: dates, //x
            datasets: [
                {
                    label: 'Tiktok popularity',
                    data: ttData, //y
                    borderColor: 'rgba(159, 90, 253, 1)',
                    backgroundColor: 'rgba(159, 90, 253, 0.3)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Youtube views',
                    data: ytData , //y
                    borderColor: 'rgba(255, 99, 132, 0.9)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Spotify popularity',
                    data: syData, //y
                    borderColor: 'rgba(147, 250, 165, 1)',
                    backgroundColor: 'rgba(147, 250, 165, 0.4)',
                    fill: true,
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

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: dates, //x
            datasets: [
                {
                    label: 'Tiktok popularity',
                    data: ttData, //y
                    borderColor: 'rgba(159, 90, 253, 1)',
                    backgroundColor: 'rgba(159, 90, 253, 0.3)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Youtube views',
                    data: ytData , //y
                    borderColor: 'rgba(255, 99, 132, 0.9)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Spotify popularity',
                    data: syData, //y
                    borderColor: 'rgba(147, 250, 165, 1)',
                    backgroundColor: 'rgba(147, 250, 165, 0.4)',
                    fill: true,
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
    <!-- BAR CHART -->
    <script>
        const element = document.getElementById('barChart2');

        let xValues = JSON.parse('<?php echo json_encode($dates) ?>');
        let yValues = JSON.parse('<?php echo json_encode($ttNums) ?>');
        let barColors = [
            'rgba(159, 90, 253, 0.4)'
        ];

        new Chart(element, {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                label: "TikTok популярност в цифри",
                backgroundColor: barColors,
                borderColor: [
                    'rgb(159, 90, 253)'
                ],
                borderWidth: 3,
                data: yValues
            }]
        },
        options: {
            legend: {display: true},
            title: {
            display: true,
            text: "TikTok популярност в цифри"
            }
        }
        });
    </script>
    <!-- RADAR CHART -->
    <script>
        new Chart(document.getElementById("radarChart"), {
            type: 'radar',
            data: {
                labels: JSON.parse('<?php echo json_encode($dates) ?>'),
                datasets: [{
                    label: "TikTok Data",
                    data: JSON.parse('<?php echo json_encode($ttPercent) ?>'),
                    borderColor: 'rgba(159, 90, 253, 1)',
                    backgroundColor: 'rgba(159, 90, 253, 0.3)',
                    pointBorderColor: 'rgba(159, 90, 253, 1)',
                    pointBackgroundColor: 'rgba(159, 90, 253, 1)',
                    pointBorderWidth: 1
                },
                {
                    label: "YouTube Data",
                    data: JSON.parse('<?php echo json_encode($ytPercent) ?>'),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    pointBorderColor: 'rgba(255, 99, 132, 1)',
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointBorderWidth: 1
                },
                {
                    label: "Spotify Data",
                    data: JSON.parse('<?php echo json_encode($syPercent) ?>'),
                    borderColor: 'rgba(147, 250, 165, 1)',
                    backgroundColor: 'rgba(147, 250, 165, 0.5)',
                    pointBorderColor: 'rgba(147, 250, 165, 1)',
                    pointBackgroundColor: 'rgba(147, 250, 165, 1)',
                    pointBorderWidth: 1
                }]
            },
            options: {
                responsive: true,
                legend: false
            }
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
</body>

</html>