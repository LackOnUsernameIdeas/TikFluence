<?php

    //Вмъкване на нужните файлове
    include "./selectDate.php";
    include './includes/databaseManager.php';
    include './includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в songsBG.php
    $sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
    if($sid < 0) redirect("songsBG.php");

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    
    //Взимаме всички дати, за които дадена песен има данни. Слагаме избраната дата в променлива и с нея издърпваме нужните данни
    $fetchDatesForButton = $db->listDatesForCurrentSongBG($sid);

    $chooseDatesForButton = [];
    foreach($fetchDatesForButton as $date){    
        $timestamp = new DateTime($date["fetch_date"]);
        $chooseDatesForButton[] = $timestamp->format('Y-m-d');
    }

    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] > $chooseDatesForButton[0] ? $_SESSION["setDate"] : date("Y-m-d");
    
    if($selectDate == "2023-01-13"){
        $selectDate = "2023-01-12";
    }

    //Взимаме данните от записите за всяка песен и ги запазваме в масиви
    $dataPoints = $db->getDatapointsForSongBG($sid, $selectDate);
    if($dataPoints === false) redirect("songsBG.php");

    $songData = $db->getSongDataBG($sid);


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

    //Запазваме колко стойности null има в масивите
    $ttNulls = array_keys($ttNums, null);
    $ytNulls = array_keys($ytNums, null);
    $syNulls = array_keys($syNums, null);

    //Определяме най-големите стойности в масивите
    $maxTiktok = max($ttPercents);
    $maxYoutube = max($ytPercents);
    $maxSpotify = max($syPercents);

    //На база всички стойности, които имаме за популярност, изчисляваме средната стойност и я запазваме в променлива. Това се отнася и за трите платформи.
    $averageTT = $db->getAverageTT($sid, $selectDate)[0][0];
    $averageYT = $db->getAverageYT($sid, $selectDate)[0][0];
    $averageSY = $db->getAverageSY($sid, $selectDate)[0][0];
        
    //Взимаме информацията от масивите ttPercents, ytPercents, syPercents и я превръщаме в проценти
    for($i=0; $i<count($ttPercents); $i++){
        $ttPercents[$i] = $maxTiktok ? ($ttPercents[$i] * 100)/$maxTiktok : null;
    }

    for($i=0; $i<count($ytPercents); $i++){
        $ytPercents[$i] = $maxYoutube ? ($ytPercents[$i] * 100)/$maxYoutube : null;
    }

    for($i=0; $i<count($syPercents); $i++){
        $syPercents[$i] = $maxSpotify ? ($syPercents[$i] * 100)/$maxSpotify : null;
    }


    //Взимаме необходимите данни за последните 2 дни

    $todayYesterdayData = $db->getTodayYesterdayDataBG($sid, $selectDate);

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

    // Превръщаме данните за последните 2 дни в проценти и ги запазваме в масиви.

    $todayYesterdayTTDataArray = [];

    foreach($ttLastTwoDaysPercents as $TT){
        if($ttLastTwoDaysPercents[0] != null && $ttLastTwoDaysPercents[0] != 0){
            array_push($todayYesterdayTTDataArray, ($TT * 100)/$ttLastTwoDaysPercents[0]);
        } else {
            array_push($todayYesterdayTTDataArray, 0);
        }
    }

    $todayYesterdayYTDataArray = [];

    foreach($ytLastTwoDaysPercents as $YT){
        if($ytLastTwoDaysPercents[0] != null && $ytLastTwoDaysPercents[0] != 0){ 
            array_push($todayYesterdayYTDataArray, ($YT * 100)/$ytLastTwoDaysPercents[0]);
        } else {
            array_push($todayYesterdayYTDataArray, 0);
        }
    }

    $todayYesterdaySYDataArray = [];

    foreach($syLastTwoDays as $SY){
        if($syLastTwoDays[0] != null && $syLastTwoDays[0] != 0){ 
            array_push($todayYesterdaySYDataArray, ($SY * 100)/$syLastTwoDays[0]);
        } else {
            array_push($todayYesterdaySYDataArray, 4);
        }
    }
    
    // Изчисляваме разликата между днес и вчера в числови стойности и в проценти, за да покажем с колко се е променила от предната дата в сравнение с избраната дата дадена песен.

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

    //Избираме подходяща икона на уиджетите за измяна на популярност:

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


    //Взимаме данните за датата, която сме избрали в страницата

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
    <link rel="icon" href="./favicon1.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="./plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="./plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="./plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="./css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="./css/themes/all-themes.css" rel="stylesheet" />
    
    <style>
        .songBox{
            width: 1480px;
            min-height: 400px;
            max-height: 600px;
            max-width: 85vw;
        }
    </style>
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
            <p>Моля изчакайте..</p>
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
                <a class="navbar-brand" href="./index.php">TIKFLUENCE</a>
            </div>

        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <img src="./images/logo.jpeg" width="300"> 

            <!-- Menu -->
            <div class="menu">
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;"><ul class="list" style="overflow: hidden; width: auto; height: 584px;">
                    <li>
                        <a href="./index.php" class="toggled waves-effect waves-block">
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
                    <li class="active">
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
                        <a href="feedback.php" class=" waves-effect waves-block">
                            <i class="material-icons">info</i>
                            <span>ЗА КОНТАКТ</span>
                        </a>
                    </li>
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
                                        <li onclick="window.location.href='./index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                        <li><a href="javascript:void(0);"><i class="material-icons">insert_chart</i>ОЩЕ СТАТИСТИКИ</a></li>
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
                                        min="<?php echo $chooseDatesForButton[1] ?>" max="<?php echo end($chooseDatesForButton) ?>" onchange=" window.location.replace('./selectDate.php?setDate=' + this.value + '&redirectURI=' + window.location.href)">
                                    <?php endif;?>
                                    <i class="material-icons" data-toggle="modal" data-target="#defaultModal" style="cursor: pointer;display: inline-block;vertical-align: middle;">help_outline</i>
                                    <div class="info"> 
                                        <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="defaultModalLabel"></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        Имайте предвид, че избирате да видите данните от датата, която сте избрали и 39 дни назад от нея. Общо можете да виждате данни до не повече от 40 дни.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn bg-purple btn-link waves-effect" data-dismiss="modal">ЗАТВОРИ</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(isset($_SESSION["setDate"]) && $_SESSION["setDate"] == "2023-01-13"):?>
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
            <?php endif;?>

            <div class="info"> 
                <i class="material-icons" data-toggle="modal" data-target="#defaultModal2" style="cursor: pointer;display: inline-block;">help_outline</i>
                    <div class="modal fade" id="defaultModal2" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="defaultModalLabel"></h4>
                                </div>
                                <div class="modal-body">
                                    Имайте предвид, че данните може да са закръглени, тъй като при надвишаване на определени стойности, създателите на TikTok са направили така че именно това да се случва.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-purple btn-link waves-effect" data-dismiss="modal">ЗАТВОРИ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                <div class="panel-group" id="accordion_3" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-primary">
                        <div class="panel-heading" role="tab" id="headingOne_3">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion_3" href="#collapseOne_3" aria-expanded="true" aria-controls="collapseOne_3" class="">
                                ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В TIKTOK <i class="material-icons">keyboard_arrow_down</i>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne_3" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_3" aria-expanded="true">
                            <div class="body songBox" style="padding:1%">
                                <canvas id="TikTokGraphChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(count($ytNulls) != count($ytNums)):?>
                <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                    <div class="panel-group" id="accordion_4" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-primary">
                            <div class="panel-heading" role="tab" id="headingOne_4">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_4" href="#collapseOne_4" aria-expanded="true" aria-controls="collapseOne_4" class="">
                                        ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В YOUTUBE <i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_4" aria-expanded="true">
                                <div class="body songBox" style="padding:1%">
                                    <canvas id="YouTubeGraphChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            
            <?php if(count($syNulls) != count($syNums)):?>
                <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                    <div class="panel-group" id="accordion_5" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-primary">
                            <div class="panel-heading" role="tab" id="headingOne_5">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_5" href="#collapseOne_5" aria-expanded="true" aria-controls="collapseOne_5" class="">
                                        ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В SPOTIFY <i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_5" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_5" aria-expanded="true">
                                <div class="body songBox" style="padding:1%">
                                    <canvas id="SpotifyGraphChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-primary">
                        <div class="panel-heading" role="tab" id="headingOne_1">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="true" aria-controls="collapseOne_1" class="">
                                ИЗМЕНЕНИЕ НА МЯСТО В КЛАСАЦИЯТА <i class="material-icons">keyboard_arrow_down</i>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne_1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_1" aria-expanded="true">
                            <div class="body songBox" style="padding:1%">
                                <canvas id="RankChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                <div class="panel-group" id="accordion_2" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-primary">
                        <div class="panel-heading" role="tab" id="headingOne_2">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion_2" href="#collapseOne_2" aria-expanded="true" aria-controls="collapseOne_2" class="">
                                ИЗМЕНЕНИЕ НА ХАРЕСВАНИЯТА <i class="material-icons">keyboard_arrow_down</i>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne_2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_2" aria-expanded="true">
                            <div class="body songBox" style="padding:1%">
                                <canvas id="LikesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="min-height:80px">
                <div class="card">
                    <div class="body bg-purple" style="font-size:160%;">
                        Промяна в популярност от <b><?php echo date('Y-m-d', mktime(0, 0, 0, date(substr($selectDate, 5, 2)), (date(substr($selectDate, 8, 2))-1), date(substr($selectDate, 0, 4)) ));?></b>:
                    </div>
                </div>
            </div>
            
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
                <div class="info-box-3 bg-deep-purple hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">
                            <?php echo $chooseIconTT ?>
                        </i>
                    </div>
                    <div class="content">
                        <div class="text">TikTok видеа</div>
                        <div class="number"><?php echo number_format($subtractionTTNums) ?></div>
                    </div>
                </div>
            </div>

            <?php if(count($ytNulls) != count($ytNums)):?>
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

            <?php endif;?>

            <?php if(count($syNulls) != count($syNums)):?>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    
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
            <?php endif;?>

        </div>
        <!-- Footer -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="body">
                    
                    <div class="legal">
                        <?php include './footer.php';?>
                    </div>
                            
                </div>
            </div>
        </div>
        <!-- #Footer -->
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

    //Приравняваме стойностите, тъй като хостинга ги счита за стрингове, а localhost - за числа
    let syDataNums = syDataNumsDirty.map(x => String(x));
    let ttDataNums = ttDataNumsDirty.map(x => String(x)); 

    
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
                },
                maintainAspectRatio: false
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
                },
                maintainAspectRatio: false
            }
        });


        let TTpointsColor = [];
 
        for(let i = 0; i < ttDataNums.length; i++){
            TTpointsColor.push("rgba(159, 90, 253, 1)")
        }
 
        let TTmax_value = String(Math.max.apply(null, ttDataNums));
        let TTmax_index = ttDataNums.indexOf(TTmax_value);
 
        TTpointsColor[TTmax_index] = "rgba(255, 0, 0, 1)";


        //TikTok
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
                },
                maintainAspectRatio: false
            }
        });

        
        const ttDataAvg = JSON.parse('<?php echo json_encode(array_sum($ttNums) / count($ttNums)) ?>');

        const avgLineTT = {
            label: 'Средноаритметична стойност',
            data: [{x: dates[0], y: ttDataAvg}, {x: dates[dates.length - 1], y: ttDataAvg}],
            type: 'line',
            borderColor: 'blue',
            borderDash: [5, 5],
            fill: false,
        };

        tt.data.datasets.push(avgLineTT);
        tt.update();


        //YouTube
        if(ytDataNulls.length != ytDataNums.length){
            let yt = new Chart(document.getElementById('YouTubeGraphChart'), {
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
                    },
                    maintainAspectRatio: false
                }
            });

            
            const ytDataAvg = JSON.parse('<?php echo json_encode(array_sum($ytNums) / count($ytNums)) ?>');

            const avgLineYT = {
                label: 'Средноаритметична стойност',
                data: [{x: dates[0], y: ytDataAvg}, {x: dates[dates.length - 1], y: ytDataAvg}],
                type: 'line',
                borderColor: 'blue',
                borderDash: [5, 5],
                fill: false,
            };

            yt.data.datasets.push(avgLineYT);
            yt.update();

        }


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
                    },
                    maintainAspectRatio: false
                }
            });


            const syDataAvg = JSON.parse('<?php echo json_encode(array_sum($syNums) / count($syNums)) ?>');

            const avgLineSY = {
                label: 'Средноаритметична стойност',
                data: [{x: dates[0], y: syDataAvg}, {x: dates[dates.length - 1], y: syDataAvg}],
                type: 'line',
                borderColor: 'blue',
                borderDash: [5, 5],
                fill: false,
            };

            sy.data.datasets.push(avgLineSY);
            sy.update();

        }

    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $(document).on("click", "li[data-role=setDate]", function(){

                let date = $(this).data('id');

                $.ajax({
                    type: "POST",
                    url: "./selectDate.php",
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
    <script src="./plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="./plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="./plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="./plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="./plugins/node-waves/waves.js"></script>

    <!-- Custom Js -->
    <script src="./js/admin.js"></script>

    <!-- Demo Js -->
    <script src="./js/demo.js"></script>
</body>

</html>