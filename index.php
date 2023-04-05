<?php
    //Вмъкване на нужните файлове
    include "scraping/curlFunctions.php";
    include "includes/databaseManager.php";

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();


    //Сдобиваме се с данни за най-използваните хаштагове за последните 7 дни
    function getHashtagsForTheLast7Days(){
        //Взимаме необходимите данни
        $hashtagsDataForTheLast7Days = fetchTopHashtagsForTheLast7Days();
        if($hashtagsDataForTheLast7Days == false){return [];}

        //Запазваме данните в масив
        $hashtagsForTheLast7Days = [];

        for($i=0;$i<count($hashtagsDataForTheLast7Days);$i++){
            if($i == 6){
                break;
            }
            array_push($hashtagsForTheLast7Days, $hashtagsDataForTheLast7Days[$i]["hashtag_name"]);
        }
        
        return $hashtagsForTheLast7Days;
    }

    //Сдобиваме се с данни за най-използваните хаштагове за последните 120 дни
    function getHashtagsForTheLast120Days(){
        //Взимаме необходимите данни
        $hashtagsDataForTheLast120Days = fetchTopHashtagsForTheLast120Days();
        if($hashtagsDataForTheLast120Days == false){return [];}

        //Запазваме данните в масив
        $hashtagsForTheLast120Days = [];

        for($i=0;$i<count($hashtagsDataForTheLast120Days);$i++){
            if($i == 6){
                break;
            }
            array_push($hashtagsForTheLast120Days, $hashtagsDataForTheLast120Days[$i]["hashtag_name"]);
        }

        return $hashtagsForTheLast120Days;
    }

    //Запазваме данните за най-използваните хаштагове в променливи
    $hashtagsDataForTheLast7Days = $db->getHashtagsForTheLast7Days();
    $hashtagsDataForTheLast120Days = $db->getHashtagsForTheLast120Days();

    //АЛГОРИТЪМ НА ПОВЛИЯВАНЕ

    function getPeaks($db){
        //Взимаме всички песни от таблицата tiktok_songs
        $songs = $db->listSongs();

        //Взимаме най-големите стойности от всички запазени за тикток и спотифай популярност
        $peaks = [];
        foreach($songs as $sg){
            $peaks[] = $db->getPeaks($sg["id"]);
        }
    
        //Комбинираме данните от двете таблици tiktok_records и tiktok_songs и слагаме всички данни за конкретния пийков запис в масива peaksWithData
        $peaksWithData = [];
        foreach($peaks as $pk){
            $peaksWithData[]["Spotify"] = $db->findSongByPeakSY($pk["song_id"], $pk["MAX(`spotify_popularity`)"]);
            $peaksWithData[]["TikTok"] = $db->findSongByPeakTT($pk["song_id"], $pk["MAX(`number_of_videos_last_14days`)"]);
        }


        return $peaksWithData;
    }

    //Изпълняваме функцията за да се сдобием с данните за пийковете на песните
    $peaksWithData = getPeaks($db);

    //peaksWithData масива е конструиран така:

    // [четен индекс] => Масив
    //     (
    //         [Spotify] => Масив
    //             (
    //                 [song_id] => 208
    //                 [0] => 208
    //                 [fetch_date] => 2023-01-03
    //                 [1] => 2023-01-03
    //             )

    //     )

    // [нечетен индекс] => Масив
    // (
    //     [TikTok] => Масив
    //         (
    //             [song_id] => 208
    //             [0] => 208
    //             [fetch_date] => 2023-01-03
    //             [1] => 2023-01-03
    //         )

    // )



    //Махаме песните от масива, които имат разлика в пийковите дати по-малка от 0 за да получим само тези песни, които са повлияни
    $songsWithDays = [];

    for($i=0;$i<count($peaksWithData);$i+=2){

        $datediff = isset($peaksWithData[$i]["Spotify"]["fetch_date"]) ? 
        strtotime($peaksWithData[$i]["Spotify"]["fetch_date"]) - strtotime($peaksWithData[$i + 1]["TikTok"]["fetch_date"]) : false;

        if($datediff != false && $datediff > 0){
            $songsWithDays[$peaksWithData[$i]["Spotify"]["song_id"]] = $datediff / (60 * 60 * 24);
        }

    }

    //Подреждаме песните в масива по низходящ ред
    arsort($songsWithDays);

    //Махаме песните от масива, които не са претърпяли никаква промяна в популярността си в TikTok повече от 10 дни.
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


    //Приготвяме данни за widget-ите, показващи кои са топ 3 най-повлияни песни
    $songsWithDaysForWidgets = $songsWithDays;

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
    
    //Слагаме необходимите данни за widget-ите в масив, който ще използваме за да покажем информацията
    $influencedSongsData = [];

    foreach($songsWithDaysForWidgets as $songId => $days){
        $influencedSongsData[] = $db->findSongAndSongsTodayDataById($songId);
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="google-site-verification" content="tQMYDP8q6UH_zU17EdVY3_8xQa5TZRTC2dCCShMYYgg" />
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

                    <li>
                        <a href="feedback.php" class=" waves-effect waves-block">
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
            <!-- Body Copy -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h1>
                                ДОБРЕ ДОШЛИ в TikFluence!
                            </h1>
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

                <div class="block-header">
                    <h2>ТОП 3 НА НАЙ-ПОВЛИЯНИТЕ ПЕСНИ ОТ TIKTOK ЗА ДНЕС И ВКЛЮЧЕНИ В ТОП 200 НА ПЛАТФОРМАТА И ТЕХНИТЕ ВИДЕА НАПРАВЕНИ НАСКОРО:</h2>
                </div>
                <!-- Widgets -->

                <?php if(isset($influencedSongsData[0])): ?>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./influencedSong.php?sid=<?= $influencedSongsData[0]["song_id"] ?>`">
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
                <?php endif; ?>

                <?php if(isset($influencedSongsData[1])): ?>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./influencedSong.php?sid=<?= $influencedSongsData[1]["song_id"] ?>`">
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
                <?php endif; ?>

                <?php if(isset($influencedSongsData[2])): ?>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./influencedSong.php?sid=<?= $influencedSongsData[2]["song_id"] ?>`">
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
                <?php endif; ?>

            </div>
            <!-- #END# Widgets -->

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                <div class="card">
                    <div class="header">
                        <h2>TikFluence засече тези най-повлияни от TikTok песни</h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-hover dashboard-task-infos" style="font-size:14px;">
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
                                        
                                        <tr onClick="window.location.href=`./influencedSong.php?sid=<?= $songData[0]["id"] ?>`">
                                            <td><?= $iteration?></td>
                                            <td><?= $songData[0]["song_name"] ?></td>
                                            <td><?= $songData[0]["artist_name"] ?></td>
                                            <th><?= $songPeakDataTT["fetch_date"] ?></th>
                                            <th><?= $songPeakDataSY["fetch_date"] ?></th>
                                            <th><?= $songLastSavedData["number_of_videos_last_14days"] ?></th>
                                            <th><?= $songLastSavedData["spotify_popularity"] ?></th>
                                            <td><a href='./influencedSong.php?sid=<?= $songData[0]["id"] ?>' class="btn bg-purple waves-effect" style="font-size:14px;">Вижте повече</a></td>
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

            <?php if($hashtagsDataForTheLast7Days != false && $hashtagsDataForTheLast120Days != false): ?>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                НАЙ-ИЗПОЛЗВАНИТЕ ХАШТАГОВЕ
                            </h2>
                        </div>
                        <div class="body">
                            <div class="row clearfix">
                                <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                    <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading" role="tab" id="headingOne_1">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="false" aria-controls="collapseOne_1" class="collapsed">
                                                        ПОНАСТОЯЩЕМ <i class="material-icons">keyboard_arrow_down</i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseOne_1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_1" aria-expanded="false" style="height: 0px;">
                                                <div class="panel-body body bg-cyan">
                                                    <ul class="dashboard-stat-list">
                                                        <?php foreach($hashtagsDataForTheLast7Days as $ht):?>
                                                            <li>
                                                                #<?php echo $ht["hashtag_name"] ?>
                                                            </li>
                                                        <?php endforeach;?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel panel-primary">
                                            <div class="panel-heading" role="tab" id="headingTwo_1">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseTwo_1" aria-expanded="false" aria-controls="collapseTwo_1">
                                                        ЗА ПОСЛЕДНИТЕ 120 ДНИ <i class="material-icons">keyboard_arrow_down</i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseTwo_1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_1" aria-expanded="false">
                                                <div class="panel-body body bg-cyan">
                                                    <ul class="dashboard-stat-list">
                                                        <?php foreach($hashtagsDataForTheLast120Days as $ht):?>
                                                            <li>
                                                                #<?php echo $ht["hashtag_name"] ?> 
                                                            </li>
                                                        <?php endforeach;?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <!-- Footer -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="body">
                      
                        <div class="legal">
                            <?php include 'footer.php';?>
                        </div>
                                
                    </div>
                </div>
            </div>
            <!-- #Footer -->

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