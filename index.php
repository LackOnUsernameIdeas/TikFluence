<?php

//Стартираме сесия
session_start();

//Вмъкване на нужните файлове
include "scraping/curlFunctions.php";
include "includes/databaseManager.php";

//Създаваме връзката с базата данни
$db = new DatabaseManager();

//Осигуряваме си необходимите данни
$dates = $db->listDatesHashtagsAndSongsOnHomePage();
$datesArray = [];

foreach($dates as $date){
    $timestamp = new DateTime($date["fetch_date"]);
    $datesArray[] = $timestamp->format('Y-m-d');
}

//Слагаме избраната дата в променлива и с нея издърпваме нужните данни
$selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] >= '2023-04-05' ? $_SESSION["setDate"] : date("Y-m-d");

//Запазваме данните за най-използваните хаштагове в променливи
$hashtagsDataForTheLast7Days = $db->getHashtagsForTheLast7Days();
$hashtagsDataForTheLast120Days = $db->getHashtagsForTheLast120Days();

//Осигуряваме си данни за диаграмите за хаштаговете
$hashtagsRanks7Days = [];
$hashtagsRanks120Days = [];

$hashtagsUses7Days = [];
$hashtagsUses120Days = [];

if($hashtagsDataForTheLast7Days != false || $hashtagsDataForTheLast120Days != false){

    foreach($hashtagsDataForTheLast7Days as $ht){
        $hashtagsRanks7Days[] = $ht["rank"];
        $hashtagsUses7Days[] = $ht["publish_cnt"];
    }

    foreach($hashtagsDataForTheLast120Days as $ht){
        $hashtagsRanks120Days[] = $ht["rank"];
        $hashtagsUses120Days[] = $ht["publish_cnt"];
    }
}

//Осигуряваме си данни за класацията за най-повлияните песни
$influencedSongs = $db->listAffectedSongsByDate($selectDate);

//Махаме песните от масива, които имат разлика в пиковите дати по-малка от 0 за да получим само тези песни, които са повлияни
$songsWithDays = [];

foreach($influencedSongs as $song){
    $songsWithDays[$song["song_id"]] = $song["peaks_difference"];
}

//Масивът songsWithDays изглежда така:
//[sid] => [peaks_diff]

//Приготвяме данни за widget-ите, показващи кои са топ 3 най-повлияни песни
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
        if($plateauIndex >= 10 || end($dates) != date("Y-m-d")){
            unset($songsWithDays[$key]);
        } 
        $previousVal = $val;
    }
}

//Слагаме необходимите данни за widget-ите в масив, който ще използваме за да покажем информацията
$influencedSongsData = [];

foreach($songsWithDays as $songId => $days){
    $influencedSongsData[] = $db->findSongAndSongsTodayDataById($songId);
}


function limitContentLength($content, $limit) {
    $trimmed_content = trim(strip_tags($content));
    if (strlen($trimmed_content) > $limit) {
      $trimmed_content = substr($trimmed_content, 0, $limit) . '...';
    }
    return $trimmed_content;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Запознайте се с ефекта на повлияване на TikTok и вижте вашите собствени статистики чрез нашето интерактивно приложение!">
    <meta name="keywords" content="influence, songs, tiktok, charts, statistics, повлияване, песни, статистики, диаграми">
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
    
    <style>
        .hashtagsBox{
            width: 100%;
            min-height: 200px;
            max-width: 93vw;
        }

        .hashtagsAccordionBox{
            width: 33.33%;
            min-height: 200px;
            max-width: 93vw;
        }

        @media only screen and (max-width: 991px) {
            .hashtagsAccordionBox{
                width: 100%;
                min-height: 200px;
            }
        }
    </style>
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
                                Проект, който изследва с авторски алгоритми ефекта на повлияване от TikTok върху различни музикални платформи, като се следи градацията и нарастващата популярност на публикуваните в него музикални и видеоклипове и се прави съпоставка с други подобни и известни приложения. 
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Body Copy -->

            <div class="block-header">
                <h2>ТОП 3 НА НАЙ-ПОВЛИЯНИТЕ ПЕСНИ ОТ TIKTOK ЗА ДНЕС И ВКЛЮЧЕНИ В ТОП 200 НА ПЛАТФОРМАТА:&nbsp;&nbsp;<i class="material-icons" data-toggle="modal" data-target="#defaultModal" style="cursor: pointer;display: inline-block;vertical-align:sub;">help_outline</i></h2>
                <div class="info"> 
                    <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
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
            <!-- Widgets -->

            <?php if(isset($influencedSongsData[0])): ?>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" onClick="window.location.href=`./influencedSong.php?sid=<?= $influencedSongsData[0]["song_id"] ?>`">
                    <div class="info-box bg-green hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">filter_1</i>
                        </div>
                        <div class="content">
                            <div class="text"><?= limitContentLength($influencedSongsData[0]["song_name"], 40) ?></div>
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
                            <div class="text"><?= limitContentLength($influencedSongsData[1]["song_name"], 40) ?></div>
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
                            <div class="text"><?= limitContentLength($influencedSongsData[2]["song_name"], 40) ?></div>
                            <div class="number count-to" data-from="0" data-to="<?= $influencedSongsData[2]["number_of_videos_last_14days"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
            <!-- #END# Widgets -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="block-header">
                    <div class="card">
                        <div class="body">
                            <h2>Изберете дата, за която искате да видите данни за ефекта на повлияване от TikTok:</h2>
                            <?php if($datesArray):?>
                                <input type="date" id="start" name="trip-start"
                                value="<?php echo $selectDate ?>"
                                min="<?php echo $datesArray[0] ?>" max="<?php echo end($datesArray) ?>" data-id="<?php echo $date ?>" data-role="setDate" onchange=" window.location.replace('./selectDate.php?setDate=' + this.value + '&redirectURI=' + window.location.href)">
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                            <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-primary">
                                    <div class="panel-heading" role="tab" id="headingOne_1">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="false" aria-controls="collapseOne_1" class="collapsed">
                                                TikFluence засече тези песни за най-повлияни от TikTok<i class="material-icons">keyboard_arrow_down</i>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne_1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_1">
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover dashboard-task-infos" style="font-size:14px;">
                                                    <thead>
                                                        <tr>
                                                            <th>Ранг</th>
                                                            <th>Песен</th>
                                                            <th>Артист</th>
                                                            <th>Дата на пик в TikTok</th>
                                                            <th>Дата на пик в Spotify</th>
                                                            <th>Разлика в пиковете в дни</th>
                                                            <th>Повлияване</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $iteration = 0;?>
                                                        <?php foreach($influencedSongs as $song):?>
                                                            <?php $iteration++?>
                                                            <?php if($iteration == 11):?>
                                                                <?php break;?>
                                                            <?php endif;?>

                                                            <tr onClick="window.location.href=`./influencedSong.php?sid=<?= $song["song_id"] ?>`">
                                                                <td><?= $iteration?></td>
                                                                <td><?= $song["song_name"] ?></td>
                                                                <td><?= $song["artist_name"] ?></td>
                                                                <th><?= $song["tiktok_peak_date"] ?></th>
                                                                <th><?= $song["spotify_peak_date"] ?></th>
                                                                <th><?= $song["peaks_difference"] ?></th>
                                                                <td><a href='./influencedSong.php?sid=<?= $song["song_id"] ?>' class="btn bg-purple waves-effect" style="font-size:14px;">Вижте повече</a></td>
                                                            </tr>
                                                        <?php endforeach;?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($hashtagsDataForTheLast7Days != false && $hashtagsDataForTheLast120Days != false): ?>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="body bg-purple">
                            НАЙ-АКТУАЛНИТЕ ХАШТАГОВЕ:
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">

                                <div class="panel-group" id="accordion_2" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingOne_2">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion_2" href="#collapseOne_2" aria-expanded="true" aria-controls="collapseOne_2" id="hashtagsForTheLast7Days">
                                                    ПОНАСТОЯЩЕМ <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_2">
                                            <div class="panel-body">
                                                <ul class="dashboard-stat-list">
                                                    <?php foreach($hashtagsDataForTheLast7Days as $ht):?>
                                                        <li>
                                                            <?php echo $ht["rank"] ?>. <b>#<?php echo $ht["hashtag_name"] ?></b>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;<?php echo number_format($ht["publish_cnt"]) ?> пъти е използван
                                                        </li>
                                                    <?php endforeach;?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingTwo_2">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion_2" href="#collapseTwo_2" aria-expanded="false" aria-controls="collapseTwo_2" id="hashtagsForTheLast120Days">
                                                    ЗА ПОСЛЕДНИТЕ 120 ДНИ <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo_2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_2">
                                            <div class="panel-body">
                                                <ul class="dashboard-stat-list">
                                                    <?php foreach($hashtagsDataForTheLast120Days as $ht):?>
                                                        <li>
                                                            <?php echo $ht["rank"] ?>. <b>#<?php echo $ht["hashtag_name"] ?></b>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;<?php echo number_format($ht["publish_cnt"]) ?> пъти е използван
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

                                                        
                <div class="col-xs-4 ol-sm-4 col-md-4 col-lg-4 hashtagsAccordionBox">
                    <div class="panel-group" id="accordion_3" role="tablist" aria-multiselectable="true">

                        <div class="panel panel-primary">
                            <div class="panel-heading" role="tab" id="headingOne_3">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_3" href="#collapseOne_3" aria-expanded="true" aria-controls="collapseOne_3" class="" id="hashtagsComparisonChartHeading">
                                        ПОНАСТОЯЩЕМ<i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_3" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_3" aria-expanded="true">
                                <div class="panel-body">
                                    <div class="body hashtagsBox">
                                        <canvas id="hashtagsChart"></canvas>
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

        </div>
    </section>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

    //Статистика за хаштаговете
    
    let hashtagsNames7Days = JSON.parse(`<?php echo json_encode($hashtagsRanks7Days) ?>`);
    let hashtagsNames120Days = JSON.parse(`<?php echo json_encode($hashtagsRanks120Days) ?>`);
    
    let hashtagsUses7Days = JSON.parse(`<?php echo json_encode($hashtagsUses7Days) ?>`);
    let hashtagsUses120Days = JSON.parse(`<?php echo json_encode($hashtagsUses120Days) ?>`);

        // съставяне 
        const dataGlobal = {
            labels: hashtagsNames7Days,
            datasets: [{
                label: 'Хаштагове',
                data: hashtagsUses7Days,
                backgroundColor: [
                    'rgba(255, 26, 104, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(0, 0, 0, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 26, 104, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 0, 0, 1)'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        // кофигуриране 
        const configGlobal = {
            type: 'bar',
            data: dataGlobal,
            options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false
            }
        };

        // слагаме статистиката в html елемента
        const myChartGlobal = new Chart(
            document.getElementById('hashtagsChart'),
            configGlobal
        );


    const hashtags7div = document.getElementById('hashtagsForTheLast7Days');
    const hashtags120div = document.getElementById('hashtagsForTheLast120Days');
    const hashtagsChartHeading = document.getElementById('hashtagsComparisonChartHeading');

    hashtags7div.addEventListener('click', () => {

        hashtagsChartHeading.innerHTML = "ПОНАСТОЯЩЕМ" + `<i class="material-icons">keyboard_arrow_down</i>`;

        // Update the chart data
        myChartGlobal.data.datasets[0].data = hashtagsUses7Days;
        myChartGlobal.data.labels = hashtagsNames7Days;

        // Redraw the chart with the new data
        myChartGlobal.update();
    });

    hashtags120div.addEventListener('click', () => {

        hashtagsChartHeading.innerHTML = "ЗА ПОСЛЕДНИТЕ 120 ДНИ" + `<i class="material-icons">keyboard_arrow_down</i>`;

        // Update the chart data
        myChartGlobal.data.datasets[0].data = hashtagsUses120Days;
        myChartGlobal.data.labels = hashtagsNames120Days;

        // Redraw the chart with the new data
        myChartGlobal.update();
    });
    </script>

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