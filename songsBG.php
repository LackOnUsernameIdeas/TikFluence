<?php

    //Вмъкване на нужните файлове.
    include "./selectDate.php";
    include "./includes/databaseManager.php";
    include "./includes/common.php";

    //Създаваме връзката с базата данни.
    $db = new DatabaseManager();
    
    //Осигуряваме си необходимите данни.
    $dates = $db->listDatesSongsBG();
    $currentDate = date("Y-m-d");
    $datesArray = [];

    foreach($dates as $date){
        $timestamp = new DateTime($date["fetch_date"]);
        $datesArray[] = $timestamp->format('Y-m-d');
    }

    //Слагаме избраната дата в променлива и с нея си осигуряваме нужните данни.
    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] >= '2023-01-08' ? $_SESSION["setDate"] : date("Y-m-d");

    //Осигуряваме си необходимите данни.
    $top200SongsBG = $db->listTop200SongsBG($selectDate);
    $topSongsBG = $db->listTopSongsBG($selectDate);

    $songsNamesBG = [];
    $songsPopularitiesBG = [];

    if($topSongsBG != false){
        foreach($topSongsBG as $song){
            $songsNamesBG[] = $song["song_name"];
            $songsPopularitiesBG[] = $song["number_of_videos"];
        }    
    }
    
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>ПЕСНИ</title>
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

    <!-- JQuery DataTable Css -->
    <link href="./plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Custom Css -->
    <link href="./css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="./css/themes/all-themes.css" rel="stylesheet" />

    <!-- Cloudflare -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .songsBox{
            width: 100%;
            height: 600px;
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
                                <a href="songsBG.php" class="waves-effect waves-block">
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
            <div class="block-header">
                <div class="body">
                    <div class="block-header card p-t-10 p-l-10">
                        <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                        <ol class="breadcrumb breadcrumb-col-black">
                            <li onclick="window.location.href='./index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                            <li><a href="javascript:void(0);"><i class="material-icons">insert_chart</i>ОЩЕ СТАТИСТИКИ</a></li>
                            <li class="active"><i class="material-icons">music_note</i>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</li>
                        </ol>
                    </div>
                </div>
            </div>

            <?php if($top200SongsBG != false):?>
                <div class="col-lg-14 col-md-14 col-sm-14 col-xs-14">
                    <div class="card">

                        <div class="body">
                            <p class="lead" style="font-size: 170%;">
                                В тази страница имате възможността да се запознаете с топ 200 на най-слушаните песни за БЪЛГАРИЯ!
                            </p>
                            <p>
                                Под таблицата, можете да видите и сравнение между първите 10 песни от класацията.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <div class="block-header">
                <div class="card">
                    <div class="body">
                        <h2>Изберете дата, за която искате разгледате данните:</h2>
                        <?php if($datesArray):?>
                            <input type="date" id="start" name="trip-start"
                            value="<?php echo $selectDate ?>"
                            min="<?php echo $datesArray[1] ?>" max="<?php echo end($datesArray) ?>" onchange=" window.location.replace('./selectDate.php?setDate=' + this.value + '&redirectURI=' + window.location.href)">
                        <?php endif;?>
                    </div>
                </div>
            </div>

            <?php if($top200SongsBG != false):?>
                
                <!-- Second Exportable table -->
                <div class="col-xs-14 col-sm-14 col-md-14 col-lg-14">
                    <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-primary">
                            <div class="panel-heading" role="tab" id="headingOne_1">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="true" aria-controls="collapseOne_1" class="">
                                        ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ<i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_1" aria-expanded="true">
                                <div class="body" style="padding:2%">

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                            <thead>
                                                <tr>
                                                    <th>РАНГ</th>
                                                    <th>ПЕСЕН</th>
                                                    <th>АВТОР НА ПЕСЕНТА</th>
                                                    <th>ВИДЕА НАПРАВЕНИ НАСКОРО</th>
                                                    <th>TIKTOK ХАРЕСВАНИЯ</th>
                                                    <th>YOUTUBE ГЛЕДАНИЯ</th>
                                                    <th>SPOTIFY ПОПУЛЯРНОСТ</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($top200SongsBG):?>
                                                    <?php foreach($top200SongsBG as $st):?>
                                                        <?php $songData = $db->getDatapointsForSongBG($st["song_id"], $selectDate); ?>
                                                        <tr>
                                                            <th><?php echo $st["rank"]?></th>
                                                            <th><?php echo $st["song_name"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.tiktok.com/music/-<?php echo $st["tiktok_platform_id"] ?>" target="_blank"><i class="fa fa-eye" title="Вижте песента в TikTok"></i></a></th>
                                                            <th><?php echo $st["artist_name"]?></th>
                                                            <th><?php echo number_format($st["number_of_videos_last_14days"])?></th>
                                                            <th><?php echo number_format($st["total_likes_count"])?></th>
                                                            <th><?php if($st["youtube_platform_id"] != null):?><?php echo number_format($st["youtube_views"])?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.youtube.com/watch?v=<?php echo $st["youtube_platform_id"] ?>" target="_blank"><i class="fa fa-eye" title="Вижте песента в YouTube"></i></a><?php endif;?></th>
                                                            <th><?php if($st["spotify_platform_id"] != null):?><?php echo $st["spotify_popularity"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://open.spotify.com/track/<?php echo $st["spotify_platform_id"] ?>" target="_blank"><i class="fa fa-eye" title="Вижте песента в Spotify"></i></a><?php endif;?></th>
                                                            <th><?php if(count($songData) > 1):?><a href='./songStatsBG.php?sid=<?php echo $st["song_id"]?>' class="btn bg-purple waves-effect">Вижте детайли</a><?php endif;?></th>
                                                        </tr>
                                                    <?php endforeach;?>
                                                <?php endif;?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>               
                <!-- #END# Second Exportable table -->

                <div class="col-xs-14 ol-sm-14 col-md-14 col-lg-14">
                    <div class="panel-group" id="accordion_2" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-primary">
                            <div class="panel-heading" role="tab" id="headingOne_2">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_2" href="#collapseOne_2" aria-expanded="true" aria-controls="collapseOne_2" class="">
                                    СРАВНЕНИЕ МЕЖДУ ПЪРВИТЕ 10 ПЕСНИ<i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_2" aria-expanded="true">
                                <div class="body songsBox" style="padding:1%">
                                    <canvas id="barChartBG"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <?php if($selectDate != "" && $selectDate == $currentDate):?>
                                <div class="body">
                                    Все още няма данни за топ 200 песни за България за днес :(
                                </div>
                            <?php elseif($selectDate == ""):?>
                                <div class="body">
                                    Трябва да изберете валидна дата!
                                </div>
                            <?php else: ?>
                                <div class="body">
                                    Съжаляваме за причиненото неудобство, но нямаме данни за тази дата. Моля, изберете друга!
                                </div>
                            <?php endif;?>
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
    <script>

    //Статистика за някои от първите песни глобално

        // съставяне 
        const dataBG = {
            labels: JSON.parse(`<?php echo json_encode($songsNamesBG) ?>`),
            datasets: [{
                label: 'ПОПУЛЯРНОСТ',
                data: JSON.parse(`<?php echo json_encode($songsPopularitiesBG) ?>`),
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
        const configBG = {
            type: 'bar',
            data: dataBG,
            options: {
                indexAxis: 'y',
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                maintainAspectRatio: false
            }
        };

        // слагаме статистиката в html елемента
        const myChartBG = new Chart(
            document.getElementById('barChartBG'),
            configBG
        );

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

    <!-- Jquery DataTable Plugin Js -->
    <script src="./plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="./plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="./plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

    <!-- Custom Js -->
    <script src="./js/admin.js"></script>
    <script src="./js/pages/tables/jquery-datatable.js"></script>

    <!-- Demo Js -->
    <script src="./js/demo.js"></script>
</body>

</html>