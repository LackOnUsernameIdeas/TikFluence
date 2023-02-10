﻿<?php

    //Вмъкване на нужните файлове
    include "../selectDate.php";
    include '../includes/databaseManager.php';
    include '../includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в songs.php
    $sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
    if($sid < 0) redirect("songs.php");

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    
    $fetchDatesForButton = $db->listDatesForCurrentSongBG($sid);

    $chooseDatesForButton = [];
    foreach($fetchDatesForButton as $date){    
        $timestamp = new DateTime($date["fetch_date"]);
        $chooseDatesForButton[] = $timestamp->format('Y-m-d');
    }

    //Взимаме необходимата информация и я превръщаме където е необходимо в проценти
    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] > $chooseDatesForButton[0] ? $_SESSION["setDate"] : date("Y-m-d");
    
    if($selectDate == "2023-01-13"){
        $selectDate = "2023-01-12";
    }

    //Взимаме записите за всяка песен
    $dataPoints = $db->getDatapointsForSongBG($sid, $selectDate);



    
    if($dataPoints === false) redirect("songs.php");

    $songData = $db->getSongDataBG($sid);


    $todayYesterdayData = $db->getTodayYesterdayDataBG($sid, $selectDate);


    $dates = [];

    $ranks = [];

    $likes = [];

    $syPercents = [];
    $ttPercents = [];
    $ytPercents = [];

    $ttNums = [];
    $ytNums = [];
    $syNums = [];

    foreach($dataPoints as $dp){
        $timestamp = new DateTime($dp["fetch_date"]);
        $dates[] = $timestamp->format('Y-m-d');

        $ranks[] = $dp["rank"];

        $likes[] = $dp["total_likes_count"];

        $syNums[] = $dp["spotify_popularity"];
        $syPercents[] = $dp["spotify_popularity"];

        $ttNums[] = $dp["number_of_videos"];
        $ttPercents[] = $dp["number_of_videos"];

        $ytNums[] = $dp["youtube_views"];
        $ytPercents[] = $dp["youtube_views"];
    }

    
    $ttNulls = array_keys($ttNums, null);
    $ytNulls = array_keys($ytNums, null);
    $syNulls = array_keys($syNums, null);


    $maxTiktok = max($ttPercents);
    $maxYoutube = max($ytPercents);
    $maxSpotify = max($syPercents);

    for($i=0; $i<count($ttPercents); $i++){
        $ttPercents[$i] = $maxTiktok ? ($ttPercents[$i] * 100)/$maxTiktok : null;
    }

    for($i=0; $i<count($ytPercents); $i++){
        $ytPercents[$i] = $maxYoutube ? ($ytPercents[$i] * 100)/$maxYoutube : null;
    }

    for($i=0; $i<count($syPercents); $i++){
        $syPercents[$i] = $maxSpotify ? ($syPercents[$i] * 100)/$maxSpotify : null;
    }





    //Взимаме необходимите данни(числа)

    $ttLastTwoDaysPercents = [];
    $ttLastTwoDaysNums = [];

    $ytLastTwoDaysPercents = [];
    $ytLastTwoDaysNums = [];

    $syLastTwoDays = [];

    foreach($todayYesterdayData as $d){
        $ttLastTwoDaysPercents[] = $d["number_of_videos"];
        $ttLastTwoDaysNums[] = $d["number_of_videos"];

        $ytLastTwoDaysPercents[] = $d["youtube_views"];
        $ytLastTwoDaysNums[] = $d["youtube_views"];

        $syLastTwoDays[] = $d["spotify_popularity"];
    }

    // Превръщаме числата в проценти

    $todayYesterdayTTDataArray = [];

    foreach($ttLastTwoDaysPercents as $TT){
        if($ttLastTwoDaysPercents[0] != null){
            array_push($todayYesterdayTTDataArray, ($TT * 100)/$ttLastTwoDaysPercents[0]);
        } else {
            array_push($todayYesterdayTTDataArray, 0);
        }
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

    if($subtractionTTPercents > 0){
        $chooseIconTT = "trending_up";
    } else if($subtractionTTPercents == 0){
        $chooseIconTT = "trending_flat";
    } else {
        $chooseIconTT = "trending_down";
    }

    if($subtractionYTPercents > 0){
        $chooseIconYT = "trending_up";
    } else if($subtractionYTPercents == 0){
        $chooseIconYT = "trending_flat";
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
    if($ytLastTwoDaysPercents[0] != null || $ytLastTwoDaysPercents[0] == 0){ 
        $todayYT = $ytLastTwoDaysPercents[1];
    } else { 
        $todayYT = "-";
    }

    //Spotify
    if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){ 
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
                <a class="navbar-brand" href="../index.php">TIKFLUENCE</a>
            </div>

        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">

            </div>
            <!-- #User Info -->
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
                    <li class="active">
                        <a href="javascript:void(0);" class="menu-toggle waves-effect waves-block">
                            <i class="material-icons">insert_chart</i>
                            <span>СТАТИСТИКИ</span>
                        </a>
                        <ul class="ml-menu">
                            <li>
                                <a href="songs.php" class="waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО</span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="songsBG.php" class="menu-toggle waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</span>
                                </a>
                                <ul class="ml-menu">
                                    <li class="active">
                                        <a href="#" class="waves-effect waves-block">
                                            <span>СТАТИСТИКИ ЗА <?php echo $songData["song_name"] ?></span>
                                        </a>
                                    </li>
                                </ul>
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
                    <li>
                        <a href="individualStats.php" class=" waves-effect waves-block">
                            <i class="material-icons">person_outline</i>
                            <span>МОИТЕ СТАТИСТИКИ В TIKTOK</span>
                        </a>
                    </li>
                </ul><div class="slimScrollBar" style="background: rgba(0, 0, 0, 0.5); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 584px;"></div><div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
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
 
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="col-lg-14 col-md-14 col-sm-14 col-xs-14">
                <div class="card">
                    <div class="body">
                        <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='songsBG.php'">
                            <i class="material-icons">arrow_back</i>
                            <span>НАЗАД</span>
                        </button>

                        <div class="block-header">
                            <div class="card">
                                <div class="body">
                                    <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                                    <ol class="breadcrumb breadcrumb-col-black">
                                        <li onclick="window.location.href='../index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                        <li><a href="javascript:void(0);"><i class="material-icons">insert_chart</i>СТАТИСТИКИ</a></li>
                                        <li onclick="window.location.href='songsBG.php'"><a href="javascript:void(0);"><i class="material-icons">music_note</i>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</a></li>
                                        <li class="active"><i class="material-icons">music_note</i>СТАТИСТИКИ ЗА: <?php echo $songData["song_name"]?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="block-header">
                            <div class="card">
                                <div class="body">
                                    <h2>Изберете дата за която искате да видите данни:</h2>
                                        <?php if($chooseDatesForButton):?>
                                            <input type="date" id="start" name="trip-start"
                                            value="<?php echo $selectDate ?>"
                                            min="<?php echo $chooseDatesForButton[1] ?>" max="<?php echo end($chooseDatesForButton) ?>" onchange=" window.location.replace('../selectDate.php?setDate=' + this.value + '&redirectURI=' + window.location.href)">
                                        <?php endif;?>
                        
                                </div>
                            </div>
                        </div>
                        
                        <?php if($_SESSION["setDate"] == "2023-01-13"):?>
                            <div class="card">
                                <div class="body">
                                    <div class="block-header">
                                        <h2>Извиняваме се, но за 13 януари 2023 година липсват данни! Моля изберете друга дата.</h2>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>


                        <div class="card">
                            <div class="body">
                                <div class="block-header">
                                    <h2>СТАТИСТИКИ ЗА:</h2>
                                    <h1><a href="https://www.tiktok.com/music/-<?php echo $songData["tiktok_platform_id"] ?>" target="_blank"><?php echo $songData["song_name"]?></a></h1><h2>ОТ</h2>
                                    <h1><?php echo $songData["artist_name"] ?></h1>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="card">
                        <div class="header">
                            <h2>
                                ИЗМЕНЕНИЕ НА РАНГА НА: <?php echo $songData["song_name"] ?>
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
                                <canvas id="RankChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="card">
                        <div class="header">
                            <h2>
                                ИЗМЕНЕНИЕ НА ХАРЕСВАНИЯТА НА: <?php echo $songData["song_name"] ?>
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
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-deep-purple hover-zoom-effect">
                    <div class="icon">
                        <i class="material-icons">music_note</i>
                    </div>
                    <div class="content">
                        <div class="text">TikTok видеа</div>
                        <div class="number"><?php echo number_format($todayTT) ?></div>
                    </div>
                </div>
            </div>

            <?php if(count($ytNulls) != count($ytNums)):?>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-red hover-zoom-effect">
                        <div class="icon">
                            <i class="material-icons">play_circle_outline</i>
                        </div>
                        <div class="content">
                            <div class="text">YouTube гледания</div>
                            <div class="number"><?php echo number_format($todayYT) ?></div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <?php if(count($syNulls) != count($syNums)):?>
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
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            <?php else:?>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            <?php endif;?>

            <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
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
                                <canvas id="TikTokGraphChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
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
                                <canvas id="TikTokBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
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
                                <canvas id="TikTokRadarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            
                    <h2>Промяна в популярност от <?php echo date('Y-m-d', mktime(0, 0, 0, date(substr($selectDate, 5, 2)), (date(substr($selectDate, 8, 2))-1), date(substr($selectDate, 0, 4)) ));?>:</h2>

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

                    <div class="info-box-3 bg-deep-purple hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">
                                <?php echo $chooseIconTT ?>
                            </i>
                        </div>
                        <div class="content">
                            <div class="text">TikTok</div>
                            <div class="number"><?php echo $subtractionTTNums ?></div>
                        </div>
                    </div>

                </div>
            </div>

            <?php if(count($ytNulls) != count($ytNums)):?>
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В YOUTUBE
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
                                    <canvas id="YouTubeGraphChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В YOUTUBE
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
                                    <canvas id="YouTubeBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В YOUTUBE
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
                                    <canvas id="YouTubeRadarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                
                        <h2>Промяна в популярност от <?php echo date('Y-m-d', mktime(0, 0, 0, date(substr($selectDate, 5, 2)), (date(substr($selectDate, 8, 2))-1), date(substr($selectDate, 0, 4)) ));?>:</h2>

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

                        <div class="info-box-3 bg-red hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">
                                    <?php echo $chooseIconYT ?>
                                </i>
                            </div>
                            <div class="content">
                                <div class="text">YouTube</div>
                                <div class="number"><?php echo number_format($subtractionYTNums) ?></div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif;?>
            
            <?php if(count($syNulls) != count($syNums)):?>
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В SPOTIFY
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
                                    <canvas id="SpotifyGraphChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В SPOTIFY
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
                                    <canvas id="SpotifyBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В SPOTIFY
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
                                    <canvas id="SpotifyRadarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                
                        <h2>Промяна в популярност от <?php echo date('Y-m-d', mktime(0, 0, 0, date(substr($selectDate, 5, 2)), (date(substr($selectDate, 8, 2))-1), date(substr($selectDate, 0, 4)) ));?>:</h2>
                        
                        <div class="info-box-3 bg-green hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">
                                    <?php echo $chooseIconSY ?>
                                </i>
                            </div>
                            <div class="content">
                                <div class="text">Spotify</div>
                                <div class="number"><?php echo round($subtractionSY, 3)?> %</div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif;?>

            <?php if($ttPercents[0] != null && $ytPercents[0] != null):?>
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    СРАВНЕНИЕ МЕЖДУ TIKTOK И YOUTUBE
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
                                    <canvas id="TikTokYouTubeGraphChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    СРАВНЕНИЕ МЕЖДУ TIKTOK И YOUTUBE
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
                                    <canvas id="TikTokYouTubeBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    СРАВНЕНИЕ МЕЖДУ TIKTOK И YOUTUBE
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
                                    <canvas id="TikTokYouTubeRadarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

        </div>
    </section>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Статистики -->
    <script>

    //Данни за ползване
    let dates =  JSON.parse('<?php echo json_encode($dates) ?>');

    let ranks = JSON.parse('<?php echo json_encode($ranks) ?>');
    let likes = JSON.parse('<?php echo json_encode($likes) ?>');

    let ytDataNums = JSON.parse('<?php echo json_encode($ytNums) ?>');
    let syDataNums = JSON.parse('<?php echo json_encode($syNums) ?>');
    let ttDataNums = JSON.parse('<?php echo json_encode($ttNums) ?>');

    let ytDataPercents = JSON.parse('<?php echo json_encode($ytPercents) ?>');
    let syDataPercents = JSON.parse('<?php echo json_encode($syPercents) ?>');
    let ttDataPercents = JSON.parse('<?php echo json_encode($ttPercents) ?>');

    let ytDataNulls = JSON.parse('<?php echo json_encode($ytNulls) ?>');
    let syDataNulls = JSON.parse('<?php echo json_encode($syNulls) ?>');

    
    // Линейни статистики

        //Статистика за проследяване на ранга на дадена песен
        new Chart(document.getElementById('RankChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Ранг',
                        data: ranks, //y
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
                        position: "left",
                        reverse: true
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

        //TikTok
        new Chart(document.getElementById('TikTokGraphChart'), {
        type: 'line',
        data: {
            labels: dates, //x
            datasets: [
                {
                    label: 'TikTok видеа',
                    data: ttDataNums, //y
                    borderColor: 'rgba(159, 90, 253, 1)',
                    backgroundColor: 'rgba(159, 90, 253, 0.3)',
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

        //YouTube
        if(ytDataNulls.length != ytDataNums.length){
            new Chart(document.getElementById('YouTubeGraphChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'YouTube гледания',
                        data: ytDataNums , //y
                        borderColor: 'rgba(255, 99, 132, 0.9)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4,
                        spanGaps: true
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
        }

        //Spotify
        if(syDataNulls.length != syDataNums.length){
            new Chart(document.getElementById('SpotifyGraphChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Spotify популярност',
                        data: syDataNums, //y
                        borderColor: 'rgba(147, 250, 165, 1)',
                        backgroundColor: 'rgba(147, 250, 165, 0.4)',
                        fill: true,
                        tension: 0.4,
                        spanGaps: true
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
        }

        //TikTok и YouTube сравнение
        if(ytDataNulls.length != ytDataNums.length){
            new Chart(document.getElementById('TikTokYouTubeGraphChart'), {
                type: 'line',
                data: {
                    labels: dates, //x
                    datasets: [
                        {
                            label: 'YouTube гледания',
                            data: ytDataNums , //y
                            borderColor: 'rgba(255, 99, 132, 0.9)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: true
                        },
                        {
                            label: 'TikTok видеа',
                            data: ttDataNums, //y
                            borderColor: 'rgba(159, 90, 253, 1)',
                            backgroundColor: 'rgba(159, 90, 253, 0.3)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: true
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
        }

    </script>

    <script>

    //Стълбчести статистики    

        //TikTok
        new Chart(document.getElementById('TikTokBarChart'), {
            type: 'bar',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Tiktok видеа',
                        data: ttDataNums, //y
                        borderColor: 'rgb(159, 90, 253)',
                        backgroundColor: 'rgba(159, 90, 253, 0.3)',
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

        //YouTube
        if(ytDataNulls.length != ytDataNums.length){
            new Chart(document.getElementById('YouTubeBarChart'), {
                type: 'bar',
                data: {
                    labels: dates, //x
                    datasets: [
                        {
                            label: 'Youtube гледания',
                            data: ytDataNums , //y
                            borderColor: 'rgba(255, 99, 132, 0.9)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: true
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
        }

        //Spotify
        if(syDataNulls.length != syDataNums.length){
            new Chart(document.getElementById('SpotifyBarChart'), {
                type: 'bar',
                data: {
                    labels: dates, //x
                    datasets: [
                        {
                            label: 'Spotify популярност',
                            data: syDataNums, //y
                            borderColor: 'rgba(147, 250, 165, 1)',
                            backgroundColor: 'rgba(147, 250, 165, 0.4)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: true
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
        }

        //TikTok и YouTube сравнение
        if(ytDataNulls.length != ytDataNums.length){
            new Chart(document.getElementById('TikTokYouTubeBarChart'), {
                type: 'bar',
                data: {
                    labels: dates, //x
                    datasets: [
                        {
                            label: 'Tiktok видеа',
                            data: ttDataNums, //y
                            borderColor: 'rgba(159, 90, 253, 1)',
                            backgroundColor: 'rgba(159, 90, 253, 0.3)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: true
                        },
                        {
                            label: 'Youtube гледания',
                            data: ytDataNums , //y
                            borderColor: 'rgba(255, 99, 132, 0.9)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.4,
                            spanGaps: true
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
        }
    </script>

    <script>
        
    //Радарни статистики    

        //TikTok
        new Chart(document.getElementById("TikTokRadarChart"), {
            type: 'radar',
            data: {
                labels: dates,
                datasets: [{
                    label: "TikTok видеа",
                    data: ttDataNums,
                    borderColor: 'rgba(159, 90, 253, 1)',
                    backgroundColor: 'rgba(159, 90, 253, 0.3)',
                    pointBorderColor: 'rgba(159, 90, 253, 1)',
                    pointBackgroundColor: 'rgba(159, 90, 253, 1)',
                    pointBorderWidth: 1
                }]
            },
            options: {
                responsive: true,
                legend: false,
            }
        });
        
        //YouTube
        if(ytDataNulls.length != ytDataNums.length){
            new Chart(document.getElementById("YouTubeRadarChart"), {
                type: 'radar',
                data: {
                    labels: dates,
                    datasets: [
                    {
                        label: "YouTube гледания",
                        data: ytDataNums,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        pointBorderColor: 'rgba(255, 99, 132, 1)',
                        pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        pointBorderWidth: 1,
                        spanGaps: true
                    }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            });
        }

        //Spotify
        if(syDataNulls.length != syDataNums.length){
            new Chart(document.getElementById("SpotifyRadarChart"), {
                type: 'radar',
                data: {
                    labels: dates,
                    datasets: [
                    {
                        label: "Spotify популярност",
                        data: syDataNums,
                        borderColor: 'rgba(147, 250, 165, 1)',
                        backgroundColor: 'rgba(147, 250, 165, 0.5)',
                        pointBorderColor: 'rgba(147, 250, 165, 1)',
                        pointBackgroundColor: 'rgba(147, 250, 165, 1)',
                        pointBorderWidth: 1,
                        spanGaps: true
                    }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            });
        }

        //TikTok и YouTube
        if(ytDataNulls.length != ytDataNums.length){
            new Chart(document.getElementById("TikTokYouTubeRadarChart"), {
                type: 'radar',
                data: {
                    labels: dates,
                    datasets: [
                        {
                        label: "TikTok видеа",
                        data: ttDataNums,
                        borderColor: 'rgba(159, 90, 253, 1)',
                        backgroundColor: 'rgba(159, 90, 253, 0.3)',
                        pointBorderColor: 'rgba(159, 90, 253, 1)',
                        pointBackgroundColor: 'rgba(159, 90, 253, 1)',
                        pointBorderWidth: 1,
                        spanGaps: true
                    },
                    {
                        label: "YouTube гледания",
                        data: ytDataNums,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        pointBorderColor: 'rgba(255, 99, 132, 1)',
                        pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        pointBorderWidth: 1,
                        spanGaps: true
                    }
                    ]
                },
                options: {
                    responsive: true,
                    legend: false,
                }
            });
        }

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
</body>

</html>