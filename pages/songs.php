<?php

    //Вмъкване на нужните файлове
    include "../selectDate.php";
    include "../includes/databaseManager.php";
    include "../includes/common.php";

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();
    
    //Осигуряваме си необходимите данни
    $dates = $db->listDatesSongs();
    $datesArray = [];

    foreach($dates as $date){
        $timestamp = new DateTime($date["fetch_date"]);
        $datesArray[] = $timestamp->format('Y-m-d');
    }


    $selectDate = isset($_SESSION["setDate"]) ? $_SESSION["setDate"] : date("Y-m-d");


    $top200SongsGlobal = $db->listTop200Songs($selectDate);
    $top200SongsBG = $db->listTop200SongsBG($selectDate);

    $topSongsGlobal = $db->listTopSongs($selectDate);


    $songsNamesGlobal = [];
    $songsPopularitiesGlobal = [];

    if($topSongsGlobal != false){
        foreach($topSongsGlobal as $song){
            $songsNamesGlobal[] = $song["song_name"];
            $songsPopularitiesGlobal[] = $song["number_of_videos_last_14days"];
        }    
    }

    function setGrowth($sid, $db, $selectDate) {

        //Взимаме необходимите данни(числа) за последните 2 дни

        $todayYesterdayDataGlobal = $db->getTodayYesterdayGlobalData($sid, $selectDate);

        $ttLastTwoDaysPercents = [];
        $ttLastTwoDaysNums = [];

        $ytLastTwoDaysPercents = [];
        $ytLastTwoDaysNums = [];

        $syLastTwoDays = [];

        foreach($todayYesterdayDataGlobal as $d){
            $ttLastTwoDaysPercents[] = $d["number_of_videos_last_14days"];
            $ttLastTwoDaysNums[] = $d["number_of_videos_last_14days"];

            $ytLastTwoDaysPercents[] = $d["youtube_views"];
            $ytLastTwoDaysNums[] = $d["youtube_views"];

            $syLastTwoDays[] = $d["spotify_popularity"];
        }




        //TikTok
        $yesterdayTT = $ttLastTwoDaysNums[0];

        //YouTube
        if($ytLastTwoDaysNums[0] != null || $ytLastTwoDaysNums[0] == 0){ 
            $yesterdayYT = $ytLastTwoDaysNums[0];
        } else { 
            $yesterdayYT = "-";
        }

        //Spotify
        if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){ 
            $yesterdaySY = $syLastTwoDays[0];
        } else { 
            $yesterdaySY = "-";
        }


        if(isset($ttLastTwoDaysNums[1])){
            //TikTok
            $todayTT = $ttLastTwoDaysNums[1];
        } else {
            //TikTok
            $todayTT = null;
        }

        if(isset($ytLastTwoDaysNums[1])){
            //YouTube
            if($ytLastTwoDaysNums[0] != null || $ytLastTwoDaysNums[0] == 0){ 
                $todayYT = $ytLastTwoDaysNums[1];
            } else { 
                $todayYT = "-";
            }
        } else {
            //TikTok
            $todayYT = null;
        }

        if(isset($syLastTwoDays[1])){
            //Spotify
            if($syLastTwoDays[0] != null || $syLastTwoDays[0] == 0){ 
                $todaySY = $syLastTwoDays[1];
            } else { 
                $todaySY = "-";
            }
        } else {
            //TikTok
            $todaySY = null;
        }
    


        $averageTT = $db->getAverageTT($sid)[0][0];
        $averageYT = $db->getAverageYT($sid)[0][0];
        $averageSY = $db->getAverageSY($sid)[0][0];

        
        if($todayTT <= $averageTT || $yesterdayTT <= $averageTT){
            $growthTT = false;
        } else {
            $growthTT = true;
        }

        if($todayYT <= $averageYT || $yesterdayYT <= $averageYT){
            $growthYT = false;
        } else {
            $growthYT = true;
        }

        if($todaySY <= $averageSY || $yesterdaySY <= $averageSY){
            $growthSY = false;
        } else {
            $growthSY = true;
        }


        if($growthTT) {
            if($growthSY || $growthYT) {
                return "Вижте нарастване";
            }
        }
        // return "Вижте детайли";
        return "Вижте детайли";
    }

    
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>ПЕСНИ</title>
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

    <!-- JQuery DataTable Css -->
    <link href="../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

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
                <div class="body">

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
                        <a href="#">
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
                        <a href="./changelogs.php">
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
        <div class="container-fluid">
            <div class="block-header">
                <div class="body">
                    <div class="block-header card p-t-10 p-l-10">
                        <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                        <ol class="breadcrumb breadcrumb-col-black">
                            <li onclick="window.location.href='../index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                            <li class="active"><i class="material-icons">music_note</i>ПЕСНИ</li>
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
                                <?php if($datesArray):?>
                                    <?php foreach($datesArray as $date):?>
                                        <li data-id="<?php echo $date ?>" data-role="setDate"><a href="javascript:void(0);" class="waves-effect waves-block"><?php echo $date?></a></li>
                                        <li role="separator" class="divider"></li>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </ul>
                        
                        </div>

                    </div>
                </div>
            </div>

            <?php if($topSongsGlobal != false):?>
                <!-- Exportable Table -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ТОП 200 TIKTOK ПЕСНИ ГЛОБАЛНО
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
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover dataTable js-exportable" id="globalDataTable">
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
                                        <tfoot>
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
                                        </tfoot>
                                        <tbody>
                                            <?php if($top200SongsGlobal):?>
                                                <?php foreach($top200SongsGlobal as $st):?>
                                                    <?php $show = setGrowth($st["song_id"], $db, $selectDate)?>
                                                    <tr>
                                                        <th><?php echo $st["rank"]?></th>
                                                        <th><?php echo $st["song_name"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.tiktok.com/music/-<?php echo $st["tiktok_platform_id"] ?>" target="_blank"><img src="../images/tiktok.png" width="24px" title="Вижте песента в TikTok"></a></th>
                                                        <th><?php echo $st["artist_name"]?></th>
                                                        <th><?php echo number_format($st["number_of_videos_last_14days"])?></th>
                                                        <th><?php echo number_format($st["total_likes_count"])?></th>
                                                        <th><?php echo number_format($st["youtube_views"])?></th>
                                                        <th><?php echo $st["spotify_popularity"]?></th>
                                                        <th><a href='./songStats.php?sid=<?php echo $st["song_id"]?>' class="btn bg-deep-purple waves-effect"><?php echo $show?></a></th>
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
                <!-- #END# Exportable Table -->

                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                        <div class="header">
                                <h2>
                                    СРАВНЕНИЕ МЕЖДУ НЯКОИ ОТ ПЪРВИТЕ ПЕСНИ
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
                                <div class="body">
                                    <canvas id="barChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="body">
                                Все още няма данни за топ 200 песни глобално за днес :(
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <?php if($top200SongsBG != false):?>
                <!-- Second Exportable table -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ
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
                                        <tfoot>
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
                                        </tfoot>
                                        <tbody>
                                            <?php if($top200SongsBG):?>
                                                <?php foreach($top200SongsBG as $st):?>
                                                    <tr>
                                                        <th><?php echo $st["rank"]?></th>
                                                        <th><?php echo $st["song_name"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.tiktok.com/music/-<?php echo $st["tiktok_platform_id"] ?>" target="_blank"><img src="../images/tiktok.png" width="24px" title="Вижте песента в TikTok"></a></th>
                                                        <th><?php echo $st["artist_name"]?></th>
                                                        <th><?php echo number_format($st["number_of_videos_last_14days"])?></th>
                                                        <th><?php echo number_format($st["total_likes_count"])?></th>
                                                        <th><?php echo number_format($st["youtube_views"])?></th>
                                                        <th><?php echo $st["spotify_popularity"]?></th>
                                                        <th><a href='./songStatsBG.php?sid=<?php echo $st["id"]?>' class="btn bg-deep-purple waves-effect">Вижте детайли</a></th>
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
                <!-- #END# Second Exportable table -->
            <?php else:?>
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="body">
                                Все още няма данни за топ 200 песни за България за днес :(
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

        </div>
    </section>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // съставяне 
        const data = {
            labels: JSON.parse('<?php echo json_encode($songsNamesGlobal) ?>'),
            datasets: [{
                label: 'ПОПУЛЯРНОСТ',
                data: JSON.parse('<?php echo json_encode($songsPopularitiesGlobal) ?>'),
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
        const config = {
            type: 'bar',
            data: data,
            options: {
                indexAxis: 'y',
                    scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // слагаме статистиката в html елемента
        const myChart = new Chart(
            document.getElementById('barChart2'),
            config
        );

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

    <!-- Jquery DataTable Plugin Js -->
    <script src="../plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="../plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

    <!-- Custom Js -->
    <script src="../js/admin.js"></script>
    <script src="../js/pages/tables/jquery-datatable.js"></script>

    <!-- Demo Js -->
    <script src="../js/demo.js"></script>
</body>

</html>