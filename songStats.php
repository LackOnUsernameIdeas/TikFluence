<?php

    //Вмъкване на нужните файлове
    include "./selectDate.php";
    include './includes/databaseManager.php';
    include './includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в songs.php
    $sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
    if($sid < 0) redirect("songs.php");

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    //Взимаме всички дати, за които дадената песен има данни
    $fetchDatesForButton = $db->listDatesForCurrentSong($sid);

    $datesArray = [];
    foreach($fetchDatesForButton as $date){
        $timestamp = new DateTime($date["fetch_date"]);
        $datesArray[] = $timestamp->format('Y-m-d');
    }

    //Слагаме избраната дата в променлива и с нея издърпваме нужните данни
    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] > $datesArray[0] ? $_SESSION["setDate"] : date("Y-m-d");
    
    if($selectDate == "2023-01-13"){
        $selectDate = "2023-01-12";
    }

    if(!(strtotime($selectDate) >= strtotime($datesArray[0]) && strtotime($selectDate) <= strtotime(end($datesArray)))){
        $selectDate = end($datesArray);
    }

    //Взимаме записите и данните за всяка песен
    $songData = $db->getSongData($sid);

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

    //Запазваме колко стойности null има в масивите
    $ttNulls = array_keys($ttNums, null);
    $ytNulls = array_keys($ytNums, null);
    $syNulls = array_keys($syNums, null);

    //Определяме най-големите стойности в масивите
    $maxTikTok = max($ttPercents);
    $maxYouTube = max($ytPercents);
    $maxSpotify = max($syPercents);

    //Взимаме информацията от масивите ttPercents, ytPercents, syPercents и я превръщаме в проценти
    for($i=0; $i<count($ttPercents); $i++){
        $ttPercents[$i] = $maxTikTok ? ($ttPercents[$i] * 100)/$maxTikTok : null;
    }

    for($i=0; $i<count($ytPercents); $i++){
        $ytPercents[$i] = $maxYouTube ? ($ytPercents[$i] * 100)/$maxYouTube : null;
    }

    for($i=0; $i<count($syPercents); $i++){
        $syPercents[$i] = $maxSpotify ? ($syPercents[$i] * 100)/$maxSpotify : null;
    }



    //Взимаме необходимите данни за последните 2 дни
    $todayYesterdayData = $db->getTodayYesterdayData($sid, $selectDate);

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

    // Превръщаме данните за последните 2 дни в проценти и ги запазваме в масиви.

    $todayYesterdayTTDataArray = [];

    foreach($ttLastTwoDaysPercents as $TT){
        array_push($todayYesterdayTTDataArray, ($TT * 100)/$ttLastTwoDaysPercents[0]);
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
            array_push($todayYesterdaySYDataArray, 0);
        }
    }
    
    // Изчисляваме разликата между днес и вчера в числови стойности и в проценти, за да покажем с колко се е променила от предната дата в сравнение с избраната дата дадена песен.

    //TikTok
    if(isset($ttLastTwoDaysPercents[1])){
        if(isset($ttLastTwoDaysPercents[0]) || $ttLastTwoDaysPercents[0] == 0){ 
            $subtractionTTPercents = $todayYesterdayTTDataArray[1] - $todayYesterdayTTDataArray[0];
            $subtractionTTNums = $ttLastTwoDaysNums[1] - $ttLastTwoDaysNums[0];
        } else { 
            $subtractionTTPercents = "-";
            $subtractionTTNums = "-";
        }
    }

    //YouTube
    if(isset($ytLastTwoDaysPercents[1])){
        if($ytLastTwoDaysPercents[0] != null || $ytLastTwoDaysPercents[0] == 0){ 
            $subtractionYTPercents = $todayYesterdayYTDataArray[1] - $todayYesterdayYTDataArray[0];
            $subtractionYTNums = $ytLastTwoDaysNums[1] - $ytLastTwoDaysNums[0];
        } else { 
            $subtractionYTPercents = "-";
            $subtractionYTNums = "-";
        }
    } else {

    }

    //Spotify
    if(isset($syLastTwoDays[1])){
        if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){
            $subtractionSY = $todayYesterdaySYDataArray[1] - $todayYesterdaySYDataArray[0];
        } else { 
            $subtractionSY = "-";
        }
    }



    //Избираме подходяща икона на уиджетите за измяна на популярност:
    
    $chooseIconTT = "";
    $chooseIconYT = "";
    $chooseIconSY = "";

    if(isset($subtractionTTPercents)){
        if($subtractionTTPercents == 0){
            $chooseIconTT = "trending_flat";
        } else if($subtractionTTPercents > 0){
            $chooseIconTT = "trending_up";
        } else {
            $chooseIconTT = "trending_down";
        }
    }

    if(isset($subtractionYTPercents)){
        if($subtractionYTPercents == 0){
            $chooseIconYT = "trending_flat";
        } else if($subtractionYTPercents > 0){
            $chooseIconYT = "trending_up";
        } else {
            $chooseIconYT = "trending_down";
        }
    }

    if(isset($subtractionSY)){
        if($subtractionSY > 0){
            $chooseIconSY = "trending_up";
        } else if($subtractionSY == 0){
            $chooseIconSY = "trending_flat";
        } else {
            $chooseIconSY = "trending_down";
        }
    }


    //Взимаме данните за датата и за датата преди датата, която сме избрали в страницата

    //TikTok
    if(isset($ttLastTwoDaysNums[1]) && isset($ttLastTwoDaysNums[0])){
        $todayTT = $ttLastTwoDaysNums[1];
        $yesterdayTT = $ttLastTwoDaysNums[0];    
    }

    //YouTube
    if(isset($ytLastTwoDaysPercents[1]) && isset($ytLastTwoDaysPercents[0])){
        if($ytLastTwoDaysPercents[0] != null || $ytLastTwoDaysPercents[0] == 0){ 
            $todayYT = $ytLastTwoDaysPercents[1];
        } else { 
            $todayYT = "-";
        }
    
        if($ytLastTwoDaysPercents[0] != null || $ytLastTwoDaysPercents[0] == 0){ 
            $yesterdayYT = $ytLastTwoDaysPercents[0];
        } else { 
            $yesterdayYT = "-";
        }
    }

    //Spotify
    if(isset($syLastTwoDays[1]) && isset($syLastTwoDays[0])){
        if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){ 
            $todaySY = $syLastTwoDays[1];
        } else { 
            $todaySY = "-";
        }
    
        if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){ 
            $yesterdaySY = $syLastTwoDays[0];
        } else { 
            $yesterdaySY = "-";
        }
    }

    //На база всички стойности, които имаме за популярност, изчисляваме средната стойност и я запазваме в променлива. Това се отнася и за трите платформи.
    $averageTT = $db->getAverageTT($sid, $selectDate)[0][0];
    $averageYT = $db->getAverageYT($sid, $selectDate)[0][0];
    $averageSY = $db->getAverageSY($sid, $selectDate)[0][0];

    
    //Ако днешната или вчерашната стойност в TikTok е по-малка или равна от средната стойност, няма нарастване.
    if(isset($todayTT) && isset($yesterdayTT)){
        if($todayTT <= $averageTT || $yesterdayTT <= $averageTT){
            $growthTT = false;
        } else {
            $growthTT = true;
        }
    } else {
        $growthTT = false;
    }

    //Ако днешната или вчерашната стойност в YouTube е по-малка или равна от средната стойност, няма нарастване.
    if(isset($todayYT) && isset($yesterdayYT)){
        if($todayYT <= $averageYT || $yesterdayYT <= $averageYT){
            $growthYT = false;
        } else {
            $growthYT = true;
        }
    } else {
        $growthYT = false;
    }

    //Ако днешната или вчерашната стойност в Spotify е по-малка или равна от средната стойност, няма нарастване.
    if(isset($todaySY) && isset($yesterdaySY)){
        if($todaySY <= $averageSY || $yesterdaySY <= $averageSY){
            $growthSY = false;
        } else {
            $growthSY = true;
        }
    } else {
        $growthSY = false;
    }

    //Според това къде има и къде няма нарастване се определя точното състояние на дадената песен. Това дали тя има нарастване и къде.

    $setConclusionPerfect = false;
    $setConclusionYT = false;
    $setConclusionSY = false;
    $setConclusionTT = false;

    $setConclusionPerfectWithoutTT = false;
    $setConclusionYTWithoutTT = false;
    $setConclusionSYWithoutTT = false;

    $setConclusionWithoutAnything = false;

    
    if($growthTT){
        //Ако тази песен има по-големи от средната стойности за последните два дни в TikTok:
        if($growthSY && $growthYT){
            $setConclusionPerfect = true;
        } elseif($growthSY == false && $growthYT){
            $setConclusionYT = true;
        } elseif($growthSY && $growthYT == false) {
            $setConclusionSY = true;
        } else {
            $setConclusionTT = true;
        }
    } else {
        //Ако тази песен няма по-големи от средната стойности за последните два дни в TikTok:
        if($growthSY && $growthYT){
            if($growthSY && $growthYT){
                $setConclusionPerfectWithoutTT = true;
            } elseif($growthSY == false && $growthYT){
                $setConclusionYTWithoutTT = true;
            } elseif($growthSY && $growthYT == false){
                $setConclusionSYWithoutTT = true;
            } else {
                $setConclusionWithoutAnything = true;
            }
        } else {
            $setConclusionWithoutAnything = true;
        }
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
            width: 1500px;
            min-height: 400px;
            max-height: 600px;
            max-width: 85vw;
        }
    </style>
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
                <a class="navbar-brand" href="./index.php">TIKFLUENCE</a>
            </div>

        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <img src="./images/logo.jpg" width="300"> 

            <!-- Menu -->
            <div class="menu">
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;"><ul class="list" style="overflow: hidden; width: auto; height: 584px;">
                    <li class="header">ГЛАВНО МЕНЮ</li>
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
                            <li class="active">
                                <a href="songs.php" class="menu-toggle waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО</span>
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
                        <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='songs.php'">
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
                                        <li onclick="window.location.href='songs.php'"><a href="javascript:void(0);"><i class="material-icons">music_note</i>ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО</a></li>
                                        <li class="active"><i class="material-icons">music_note</i>СТАТИСТИКИ ЗА: <?php echo $songData["song_name"]?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="block-header">
                            <div class="card">
                                <div class="body">
                                    <h2>Изберете дата за която искате да видите данни:</h2>
                                        <?php if($datesArray):?>
                                            <input type="date" id="start" name="trip-start"
                                            value="<?php echo $selectDate ?>"
                                            min="<?php echo $datesArray[1] ?>" max="<?php echo end($datesArray) ?>" onchange="window.location.replace('./selectDate.php?setDate=' + this.value + '&redirectURI=' + window.location.href)">
                                        <?php endif;?>
                        
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
                                    <h3>СТАТИСТИКИ ЗА: <?php echo $songData["song_name"]?> на <?php echo $songData["artist_name"] ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="body">
                                <div class="block-header">
                                    <?php if($setConclusionPerfect):?>
                                        <h2>Тази песен е претърпяла ефекта на нарастване от TikTok. Днешната и вчерашната популярност е по-голяма от средната за всички дни. За <?php echo $songData["song_name"] ?>, TikTok е повлиял на популярността на песента и в YouTube, и в Spotify.</h2>
                                    <?php elseif($setConclusionYT):?>
                                        <h2>Тази песен е претърпяла ефекта на нарастване от TikTok. Днешната и вчерашната популярност е по-голяма от средната за всички дни. За <?php echo $songData["song_name"] ?>, TikTok е повлиял на популярността на песента в YouTube, но не е повлиял на популярността на песента в Spotify.</h2>
                                    <?php elseif($setConclusionSY):?>
                                        <h2>Тази песен е претърпяла ефекта на нарастване от TikTok. Днешната и вчерашната популярност е по-голяма от средната за всички дни. За <?php echo $songData["song_name"] ?>, TikTok е повлиял на популярността на песента в Spotify, но не е повлиял на популярността на песента в YouTube.</h2>
                                    <?php elseif($setConclusionTT):?>
                                        <h2>Тук можем да видим нарастване само в TikTok.</h2>
                                    <?php elseif($setConclusionPerfectWithoutTT):?>
                                        <h2>Тук не можем да видим ефекта на нарастване от TikTok, но можем да видим нарастване на популярност в YouTube и Spotify.</h2>
                                    <?php elseif($setConclusionYTWithoutTT):?>
                                        <h2>Тук не можем да видим ефекта на нарастване от TikTok, но можем да видим нарастване на популярност само в YouTube, без Spotify.</h2>
                                    <?php elseif($setConclusionSYWithoutTT):?>
                                        <h2>Тук не можем да видим ефекта на нарастване от TikTok, но можем да видим нарастване на популярност само в Spotify, без YouTube.</h2>
                                    <?php elseif($setConclusionWithoutAnything):?>
                                        <h2>Тук не можем да видим ефекта на нарастване от TikTok, нито можем да видим нарастване на популярност в YouTube, нито в Spotify.</h2>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
     
            <div class="row clearfix">
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
            </div>
    
            <div class="row clearfix">

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

            </div> 


            <?php if(count($ytNulls) != count($ytNums)):?>
                <div class="row clearfix">

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

                </div>
            <?php endif;?>
            
            <?php if(count($syNulls) != count($syNums)):?>
                <div class="row clearfix">

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

                </div>
            <?php endif;?>

            <div class="col-xs-14 ol-sm-14 col-md-14 col-lg-14">
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

            <div class="col-xs-14 ol-sm-14 col-md-14 col-lg-14">
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


            <div class="row clearfix">

                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="min-height:80px">
                    <div class="card">
                        <div class="body bg-purple" style="font-size:168%;">
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
                            <div class="text">TikTok</div>
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
    let dates = JSON.parse('<?php echo json_encode($dates) ?>');

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
                },
                maintainAspectRatio: false
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
                    },
                    maintainAspectRatio: false
                }
            });
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