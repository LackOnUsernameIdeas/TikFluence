<?php

    //Вмъкване на нужните файлове
    include "../selectDate.php";
    include '../includes/databaseManager.php';
    include '../includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в songs.php
    $sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
    if($sid < 0) redirect("songs.php");

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    $songData = $db->getSongData($sid);

        
    $fetchDatesForButton = $db->listDatesForCurrentSong($sid);

    $chooseDatesForButton = [];
    foreach($fetchDatesForButton as $date){    
        $timestamp = new DateTime($date["fetch_date"]);
        $chooseDatesForButton[] = $timestamp->format('Y-m-d');
    }

    //Взимаме необходимата информация и я превръщаме където е необходимо в проценти
    $selectDate = isset($_SESSION["setDate"]) ? $_SESSION["setDate"] : date("Y-m-d");
    
    if(end($chooseDatesForButton) != $selectDate){
        $selectDate = end($chooseDatesForButton);
    }

    $todayYesterdayData = $db->getTodayYesterdayData($sid, $selectDate);


    //Взимаме записите за всяка песен
    $dataPoints = $db->getDatapointsForSong($sid, $selectDate);
    
    if($dataPoints === false) redirect("songs.php");


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

        $ttNums[] = $dp["number_of_videos_last_14days"];
        $ttPercents[] = $dp["number_of_videos_last_14days"];

        $ytNums[] = $dp["youtube_views"];
        $ytPercents[] = $dp["youtube_views"];
    }


    $ttNulls = array_keys($ttNums, null);
    $ytNulls = array_keys($ytNums, null);
    $syNulls = array_keys($syNums, null);


    $maxTikTok = max($ttPercents);
    $maxYouTube = max($ytPercents);
    $maxSpotify = max($syPercents);

    for($i=0; $i<count($ttPercents); $i++){
        $ttPercents[$i] = $maxTikTok ? ($ttPercents[$i] * 100)/$maxTikTok : null;
    }

    for($i=0; $i<count($ytPercents); $i++){
        $ytPercents[$i] = $maxYouTube ? ($ytPercents[$i] * 100)/$maxYouTube : null;
    }

    for($i=0; $i<count($syPercents); $i++){
        $syPercents[$i] = $maxSpotify ? ($syPercents[$i] * 100)/$maxSpotify : null;
    }



    //Взимаме необходимите данни(числа) за последните 2 дни
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
    if(isset($ttLastTwoDaysPercents[0]) || $ttLastTwoDaysPercents[0] == 0){ 
        $subtractionTTPercents = $todayYesterdayTTDataArray[1] - $todayYesterdayTTDataArray[0];
        $subtractionTTNums = $ttLastTwoDaysNums[1] - $ttLastTwoDaysNums[0];
    } else { 
        $subtractionTTPercents = "-";
        $subtractionTTNums = "-";
    }

    //YouTube
    if($ytLastTwoDaysPercents[0] != null || $ytLastTwoDaysPercents[0] == 0){ 
        $subtractionYTPercents = $todayYesterdayYTDataArray[1] - $todayYesterdayYTDataArray[0];
        $subtractionYTNums = $ytLastTwoDaysNums[1] - $ytLastTwoDaysNums[0];
    } else { 
        $subtractionYTPercents = "-";
        $subtractionYTNums = "-";
    }

    //Spotify
    if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){
        $subtractionSY = $todayYesterdaySYDataArray[1] - $todayYesterdaySYDataArray[0];
    } else { 
        $subtractionSY = "-";
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

    

    //TikTok
    $yesterdayTT = $ttLastTwoDaysNums[0];

    //YouTube
    if($ytLastTwoDaysPercents[0] != null || $ytLastTwoDaysPercents[0] == 0){ 
        $yesterdayYT = $ytLastTwoDaysPercents[0];
    } else { 
        $yesterdayYT = "-";
    }

    //Spotify
    if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){ 
        $yesterdaySY = $syLastTwoDays[0];
    } else { 
        $yesterdaySY = "-";
    }


    // $averageTT = $db->getAverageTT($sid, $selectDate)[0][0];
    // $averageYT = $db->getAverageYT($sid, $selectDate)[0][0];
    // $averageSY = $db->getAverageSY($sid, $selectDate)[0][0];

    
    // if($todayTT <= $averageTT || $yesterdayTT <= $averageTT){
    //     $growthTT = false;
    // } else {
    //     $growthTT = true;
    // }

    // if($todayYT <= $averageYT || $yesterdayYT <= $averageYT){
    //     $growthYT = false;
    // } else {
    //     $growthYT = true;
    // }

    // if($todaySY <= $averageSY || $yesterdaySY <= $averageSY){
    //     $growthSY = false;
    // } else {
    //     $growthSY = true;
    // }

    // $setConclusionPerfect = false;
    // $setConclusionYT = false;
    // $setConclusionSY = false;
    // $setConclusionTT = false;

    // $setConclusionPerfectWithoutTT = false;
    // $setConclusionYTWithoutTT = false;
    // $setConclusionSYWithoutTT = false;

    // $setConclusionWithoutAnything = false;

    // if($growthTT){

    //     if($growthSY && $growthYT){
    //         $setConclusionPerfect = true;
    //     } elseif($growthSY == false || $growthYT){
    //         $setConclusionYT = true;
    //     } elseif($growthSY || $growthYT == false) {
    //         $setConclusionSY = true;
    //     } else {
    //         $setConclusionTT = true;
    //     }

    // } else {

    //     //Ако тази песен не е популярна последните два дни в TikTok
    //     if($growthSY && $growthYT){
    //         $setConclusionPerfectWithoutTT = true;
    //     } elseif($growthSY == false || $growthYT){
    //         $setConclusionYTWithoutTT = true;
    //     } elseif($growthSY || $growthYT == false){
    //         $setConclusionSYWithoutTT = true;
    //     } else {
    //         $setConclusionWithoutAnything = true;
    //     }

    // }

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
            <div class="collapse navbar-collapse" id="navbar-collapse">

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
                    <li class="active">
                        <a href="../index.php" class="toggled waves-effect waves-block">
                            <i class="material-icons">home</i>
                            <span>НАЧАЛО</span>
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
                                <a href="songs.php" class="menu-toggle waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО</span>
                                </a>
                                <ul class="ml-menu">
                                    <li>
                                        <a href="#" class="waves-effect waves-block">
                                            <span>СТАТИСТИКИ ЗА <?php echo $songData["song_name"] ?></span>
                                        </a>
                                    </li>
                                </ul>
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
                    <li>
                        <a href="feedback.php" class=" waves-effect waves-block">
                            <i class="material-icons">help</i>
                            <span>ПОВЕЧЕ ЗА НАС</span>
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
                        <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='../index.php'">
                            <i class="material-icons">arrow_back</i>
                            <span>НАЗАД</span>
                        </button>
                        <div class="block-header">
                            <div class="card">
                                <div class="body">
                                    <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                                    <ol class="breadcrumb breadcrumb-col-black">
                                        <li onclick="window.location.href='../index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                        <li class="active"><i class="material-icons">music_note</i>ПОВЛИЯВАНЕ НА: <?php echo $songData["song_name"]?> от TikTok</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="body">
                                <div class="block-header">
                                    <h3>ПОВЛИЯВАНЕ НА: <?php echo $songData["song_name"]?> на <?php echo $songData["artist_name"] ?> от TikTok</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="body">
                                <div class="block-header">
                                    <h2>Според нашата система, тази песен е претърпяла ефекта на повлияване от TikTok, защото пикът на популярност на <strong><?php echo $songData["song_name"] ?></strong> в TikTok е преди този на Spotify.</h2>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row clearfix">

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 m-b-10">
                    <div class="info-box bg-purple hover-zoom-effect">
                        <div class="icon">
                            <i class="material-icons">music_note</i>
                        </div>
                        <div class="content">
                            <div class="text">Брой видеа</div>
                            <div class="number"><?php echo number_format(end($ttNums)) ?></div> 
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 m-b-10">
                    <div class="info-box bg-green hover-zoom-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_play</i>
                        </div>
                        <div class="content">
                            <div class="text">Spotify популярност</div>
                            <div class="number"><?php echo number_format(end($syNums)) ?></div> 
                        </div>
                    </div>
                </div>
            </div>

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

    let ranks = JSON.parse('<?php echo json_encode($ranks) ?>');
    let likes = JSON.parse('<?php echo json_encode($likes) ?>');

    let ytDataNums = JSON.parse('<?php echo json_encode($ytNums) ?>');
    let syDataNumsDirty = JSON.parse('<?php echo json_encode($syNums) ?>');
    let ttDataNumsDirty = JSON.parse('<?php echo json_encode($ttNums) ?>');

    let ytDataPercents = JSON.parse('<?php echo json_encode($ytPercents) ?>');
    let syDataPercents = JSON.parse('<?php echo json_encode($syPercents) ?>');
    let ttDataPercents = JSON.parse('<?php echo json_encode($ttPercents) ?>');

    let ytDataNulls = JSON.parse('<?php echo json_encode($ytNulls) ?>');
    let syDataNulls = JSON.parse('<?php echo json_encode($syNulls) ?>');

    
    // Линейни статистики

        //Приравняваме стойностите, тъй като хостинга ги счита за стрингове, а localhost - за числа
        let syDataNums = syDataNumsDirty.map(x => String(x));
        let ttDataNums = ttDataNumsDirty.map(x => String(x)); 

        //TikTok

        let TTpointsColor = [];
 
        for(let i = 0; i < ttDataNums.length; i++){
            TTpointsColor.push("rgba(159, 90, 253, 1)")
        }
 
        let TTmax_value = String(Math.max.apply(null, ttDataNums));
        let TTmax_index = ttDataNums.indexOf(TTmax_value);
 
        TTpointsColor[TTmax_index] = "rgba(255, 0, 0, 1)";
 

        let tt = new Chart(document.getElementById('TikTokGraphChart'), {
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
                    tension: 0.4,
                    pointBackgroundColor: TTpointsColor
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



        //Spotify
        if(syDataNulls.length != syDataNums.length){
            
            let SYpointsColor = [];
 
            for(let i = 0; i < syDataNums.length; i++){
                SYpointsColor.push("rgba(147, 250, 165, 1)")
            }

 
            let SYmax_value = String(Math.max.apply(null, syDataNums));
            let SYmax_index = syDataNums.indexOf(SYmax_value);
 
            SYpointsColor[SYmax_index] = "rgba(255, 0, 0, 1)";
 

            let sy = new Chart(document.getElementById('SpotifyGraphChart'), {
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
                        spanGaps: true,
                        pointBackgroundColor: SYpointsColor
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