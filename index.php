﻿<?php
    //Вмъкване на нужните файлове
    include "scraping/curlFunctions.php";
    include "includes/databaseManager.php";

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    //Взимаме необходимите данни
    $hashtagsDataForTheLast7Days = fetchTopHashtagsForTheLast7Days();
    $hashtagsDataForTheLast120Days = fetchTopHashtagsForTheLast120Days();


    $theFirstSongGlobal = $db->listTheFirstSongGlobal(date("Y-m-d"));
    $theMostFollowedTikToker = $db->listTheFirstTikToker(date("Y-m-d"));
    $theMostWatchedVideo = $db->listTheFirstVideo(date("Y-m-d"));


    $hashtagsForTheLast7Days = [];

    for($i=0;$i<count($hashtagsDataForTheLast7Days);$i++){
        if($i == 6){
            break;
        }
        array_push($hashtagsForTheLast7Days, $hashtagsDataForTheLast7Days[$i]["hashtag_name"]);
    }

    $hashtagsForTheLast120Days = [];

    for($i=0;$i<count($hashtagsDataForTheLast120Days);$i++){
        if($i == 6){
            break;
        }
        array_push($hashtagsForTheLast120Days, $hashtagsDataForTheLast120Days[$i]["hashtag_name"]);
    }

    function getPeaks($db){
        $songs = $db->listSongs();

        $peaks = [];
        foreach($songs as $sg){
            $peaks[] = $db->getPeaks($sg["id"]);
        }
    
        $peaksWithData = [];
        foreach($peaks as $pk){
            $peaksWithData[]["Spotify"] = $db->findSongByPeakSY($pk["song_id"], $pk["MAX(`spotify_popularity`)"]);
            $peaksWithData[]["TikTok"] = $db->findSongByPeakTT($pk["song_id"], $pk["MAX(`number_of_videos_last_14days`)"]);
        }


        return $peaksWithData;
    }


    $peaksWithData = getPeaks($db);

    $songsWithDays = [];


    $peaksDatesTT = [];

    for($i=0;$i<count($peaksWithData);$i+=2){

        $datediff = isset($peaksWithData[$i]["Spotify"]["fetch_date"]) ? 
        strtotime($peaksWithData[$i]["Spotify"]["fetch_date"]) - strtotime($peaksWithData[$i + 1]["TikTok"]["fetch_date"]) : false;

        $peaksDatesTT[] = $peaksWithData[$i + 1]["TikTok"]["fetch_date"];

        if($datediff != false && $datediff > 0){
            $songsWithDays[$peaksWithData[$i]["Spotify"]["song_id"]] = $datediff / (60 * 60 * 24);
        }

    }

    $songsWithDaysForWidgets = $songsWithDays;

    arsort($songsWithDays);

    //Взимаме данни за таблицата
    foreach($songsWithDays as $key => $value){
        $datapoints = $db->getEveryDatapointForSong($key);
        
        $ttNums = [];
        $dates = [];

        foreach($datapoints as $dp){
            $ttNums[] = $dp["number_of_videos_last_14days"];
            $dates[] = $dp["fetch_date"];
        }

        $plateauIndex = 0;
        $previousVal = 0;
        foreach($ttNums as $val){
            if($val == $previousVal){
                $plateauIndex++;
            } else {
                $plateauIndex = 0;
            }
            if($plateauIndex >= 10){
                unset($songsWithDays[$key]);
            } 
            $previousVal = $val;
        }
    }

    //Взимаме данни за widget-и
    foreach($songsWithDaysForWidgets as $key => $value){
        $datapoints = $db->getEveryDatapointForSong($key);
        
        $ttNums = [];
        $dates = [];

        foreach($datapoints as $dp){
            $ttNums[] = $dp["number_of_videos_last_14days"];
            $dates[] = $dp["fetch_date"];
        }

        $plateauIndex = 0;
        $previousVal = 0;
        foreach($ttNums as $val){
            if($val == $previousVal){
                $plateauIndex++;
            } else {
                $plateauIndex = 0;
            }
            if($plateauIndex >= 10 || end($dates) != date("Y-m-d")){
                unset($songsWithDaysForWidgets[$key]);
            } 
            $previousVal = $val;
        }
    }
    
    $influencedSongsData = [];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>НАЧАЛО</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon1.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet">

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet">

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet">
    
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

            <img src="images/logo.jpg" width="300"> 

            <!-- Menu -->
            <div class="menu">
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;"><ul class="list" style="overflow: hidden; width: auto; height: 584px;">
                    <li class="header">ГЛАВНО МЕНЮ</li>
                    <li class="active">
                        <a href="#" class="toggled waves-effect waves-block">
                            <i class="material-icons">home</i>
                            <span>НАЧАЛО</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/individualStats.php" class=" waves-effect waves-block">
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
                                <a href="pages/songs.php" class="waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО</span>
                                </a>
                            </li>
                            <li>
                                <a href="pages/songsBG.php" class=" waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</span>
                                </a>
                            </li>
                            <li>
                                <a href="pages/tiktokers.php" class="waves-effect waves-block">
                                    <i class="material-icons">person</i>
                                    <span>ТОП 200 НАЙ-ИЗВЕСТНИ ТИКТОКЪРИ</span>
                                </a>
                            </li>
                            <li>
                                <a href="pages/topVideos.php" class="waves-effect waves-block">
                                    <i class="material-icons">play_circle_outline</i>
                                    <span>ТОП 200 НАЙ-ГЛЕДАНИ ВИДЕА В TIKTOK</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="pages/feedback.php" class=" waves-effect waves-block">
                            <i class="material-icons">help</i>
                            <span>ПОВЕЧЕ ЗА НАС</span>
                        </a>
                    </li>
                    <!-- <li class="header"></li> -->
                </ul><div class="slimScrollBar" style="background: rgba(0, 0, 0, 0.5); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 584px;"></div><div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    <a href="javascript:void(0);"><a href="pages/privacyPolicy.php">Privacy Policy</a> ,</a>
                </div>
                <div class="copyright">
                    <a href="javascript:void(0);"><a href="pages/termsAndConditions.php">Terms and Conditions</a></a>
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->

    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Body Copy -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h1>
                                ДОБРЕ ДОШЛИ в TikFluence!
                            </h1>
                            <ul class="header-dropdown m-r--5">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="body">
                            <p class="lead">
                                Какво е TikFluence?
                            </p>
                            <p>
                                Проект, който изследва ефекта на TikTok върху различни музикални платформи, като се следи градацията и нарастващата популярност на публикуваните в него музикални и видео клипове и се прави съпоставка с други подобни и известни приложения. 
                            </p>
                            <p>
                                Данните са представени чрез интерактивни диаграми, таблици и статистики.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Body Copy -->

            <?php if($theFirstSongGlobal && $theMostFollowedTikToker && $theMostWatchedVideo):?>
                <div class="block-header">
                    <h2>ТОП 3 НА НАЙ-ПОВЛИЯНИТЕ ПЕСНИ ОТ TIKTOK ЗА ДНЕС И ВКЛЮЧЕНИ В ТОП 200 НА ПЛАТФОРМАТА И ТЕХНИТЕ ВИДЕА НАПРАВЕНИ НАСКОРО:</h2>
                </div>
                <!-- Widgets -->

                    <?php foreach($songsWithDaysForWidgets as $songId => $days):?>
                        <?php $influencedSongsData[] = $db->findSongAndSongsTodayDataById($songId) ?>
                    <?php endforeach;?>

                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./pages/influencedSong.php?sid=<?= $influencedSongsData[0]["song_id"] ?>`">
                            <div class="info-box bg-green hover-expand-effect">
                                <div class="icon">
                                    <i class="material-icons">filter_1</i>
                                </div>
                                <div class="content">
                                    <div class="text"><?= $influencedSongsData[0]["song_name"] ?></div>
                                    <div class="number count-to" data-from="0" data-to="<?= $influencedSongsData[0]["number_of_videos_last_14days"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./pages/influencedSong.php?sid=<?= $influencedSongsData[1]["song_id"] ?>`">
                            <div class="info-box bg-yellow hover-expand-effect">
                                <div class="icon">
                                    <i class="material-icons">filter_2</i>
                                </div>
                                <div class="content">
                                    <div class="text"><?= $influencedSongsData[1]["song_name"] ?></div>
                                    <div class="number count-to" data-from="0" data-to="<?= $influencedSongsData[1]["number_of_videos_last_14days"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./pages/influencedSong.php?sid=<?= $influencedSongsData[2]["song_id"] ?>`">
                            <div class="info-box bg-red hover-expand-effect">
                                <div class="icon">
                                    <i class="material-icons">filter_3</i>
                                </div>
                                <div class="content">
                                    <div class="text"><?= $influencedSongsData[2]["song_name"] ?></div>
                                    <div class="number count-to" data-from="0" data-to="<?= $influencedSongsData[2]["number_of_videos_last_14days"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                </div>
                            </div>
                        </div>


                </div>
                <!-- #END# Widgets -->
            <?php endif;?>

            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                <div class="card">
                    <div class="header">
                        <h2>TikFluence засече тези най-повлияни от TikTok песни</h2>
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-hover dashboard-task-infos">
                                <thead>
                                    <tr>
                                        <th>Ранг</th>
                                        <th>Песен</th>
                                        <th>Артист</th>
                                        <th>Дата на пик в TikTok</th>
                                        <th>Дата на пик в Spotify</th>
                                        <th>TikTok видеа последно</th>
                                        <th>Spotify популярност последно</th>
                                        <th>Повлияване</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $iteration = 0;?>
                                    <?php foreach($songsWithDays as $songId => $days):?>
                                        <?php $iteration++?>
                                        <?php if($iteration == 11):?>
                                            <?php break;?>
                                        <?php endif;?>

                                        <?php $songData = $db->findSongById($songId) ?>
                                        <?php $songPeakDataTT = $db->findSongPeakDataTT($songId) ?>
                                        <?php $songPeakDataSY = $db->findSongPeakDataSY($songId) ?>
                                        <?php $songLastSavedData = $db->findSongLastSavedData($songId) ?>
                                        
                                        <tr onClick="window.location.href=`./pages/influencedSong.php?sid=<?= $songData[0]["id"] ?>`">
                                            <td><?= $iteration?></td>
                                            <td><?= $songData[0]["song_name"] ?></td>
                                            <td><?= $songData[0]["artist_name"] ?></td>
                                            <th><?= $songPeakDataTT["fetch_date"] ?></th>
                                            <th><?= $songPeakDataSY["fetch_date"] ?></th>
                                            <th><?= $songLastSavedData["number_of_videos_last_14days"] ?></th>
                                            <th><?= $songLastSavedData["spotify_popularity"] ?></th>
                                            <td><a href='./pages/influencedSong.php?sid=<?= $songData[0]["id"] ?>' class="btn bg-purple waves-effect">Вижте повече</a></td>
                                            <!-- <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" style="width: 62%"></div>
                                                </div>
                                            </td> -->
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Social Trends -->
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="card">
                    <div class="body bg-cyan">
                        <div class="m-b--35 font-bold">НАЙ-ИЗПОЛЗВАНИТЕ ХАШТАГОВЕ В МОМЕНТА</div>
                        <ul class="dashboard-stat-list">
                            <?php foreach($hashtagsForTheLast7Days as $ht):?>
                                <li>
                                    #<?php echo $ht ?>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #END# Latest Social Trends -->
            <!-- Latest Social Trends -->
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="card">
                    <div class="body bg-cyan">
                        <div class="m-b--35 font-bold">НАЙ-ИЗПОЛЗВАНИТЕ ХАШТАГОВЕ ЗА ПОСЛЕДНИТЕ 120 ДНИ</div>
                        <ul class="dashboard-stat-list">
                            <?php foreach($hashtagsForTheLast120Days as $ht):?>
                                <li>
                                    #<?php echo $ht ?> 
                                    <!-- <span class="pull-right">
                                        <i class="material-icons">trending_up</i>
                                    </span> -->
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #END# Latest Social Trends -->

            <!-- Answered Tickets -->
            <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="card">
                    <div class="body bg-teal">
                        <div class="font-bold m-b--35">ANSWERED TICKETS</div>
                        <ul class="dashboard-stat-list">
                            <li>
                                TODAY
                                <span class="pull-right"><b>12</b> <small>TICKETS</small></span>
                            </li>
                            <li>
                                YESTERDAY
                                <span class="pull-right"><b>15</b> <small>TICKETS</small></span>
                            </li>
                            <li>
                                LAST WEEK
                                <span class="pull-right"><b>90</b> <small>TICKETS</small></span>
                            </li>
                            <li>
                                LAST MONTH
                                <span class="pull-right"><b>342</b> <small>TICKETS</small></span>
                            </li>
                            <li>
                                LAST YEAR
                                <span class="pull-right"><b>4 225</b> <small>TICKETS</small></span>
                            </li>
                            <li>
                                ALL
                                <span class="pull-right"><b>8 752</b> <small>TICKETS</small></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> -->
            <!-- #END# Answered Tickets -->

        </div>
    </section>

    <!-- Jquery Core Js -->
    <script async="" src="https://www.google-analytics.com/analytics.js"></script><script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="plugins/raphael/raphael.min.js"></script>
    <script src="plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="plugins/flot-charts/jquery.flot.js"></script>
    <script src="plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="js/demo.js"></script>


<div id="torrent-scanner-popup" style="display: none;"></div>
</body>
</html>