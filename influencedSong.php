<?php

    //Вмъкване на нужните файлове
    include "./selectDate.php";
    include './includes/databaseManager.php';
    include './includes/common.php';

    //Ако няма такова id за песен, потребителят бива върнат в affectedSongs.php
    $sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
    if($sid < 0) redirect("affectedSongs.php");

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    $songData = $db->getSongData($sid);

    //Ако няма данни за тази песен, потребителят бива върнат в affectedSongs.php
    if($songData == false) redirect("affectedSongs.php");

    //Слагаме избраната дата в променлива и с нея издърпваме нужните данни
    $selectDate = isset($_SESSION["setDate"]) ? $_SESSION["setDate"] : date("Y-m-d");


    //Взимаме всички данни за дадената песен и ако няма потребителят бива върнат в songs.php
    $dataPoints = $db->getDatapointsForSong($sid, $selectDate);
    if($dataPoints === false) redirect("songs.php");


    $dates = [];

    $ttNums = [];
    $syNums = [];


    foreach($dataPoints as $dp){
        
        $timestamp = new DateTime($dp["fetch_date"]);
        $dates[] = $timestamp->format('Y-m-d');

        $syNums[] = $dp["spotify_popularity"];
        $ttNums[] = $dp["number_of_videos_last_14days"];

    }


    $ttNulls = array_keys($ttNums, null);
    $syNulls = array_keys($syNums, null);

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
            <div class="collapse navbar-collapse" id="navbar-collapse">

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
                    <li>
                        <a href="./index.php" class="toggled waves-effect waves-block">
                            <i class="material-icons">home</i>
                            <span>НАЧАЛО</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="affectedSongs.php" class=" waves-effect waves-block">
                            <i class="material-icons">music_note</i>
                            <span>ПОВЛИЯНИ ПЕСНИ</span>
                        </a>
                        <ul class="ml-menu">
                            <li class="active">
                                <a href="#" class="menu-toggle waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>СТАТИСТИКИ ЗА <?php echo $songData["song_name"] ?></span>
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
                        <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='./index.php'">
                            <i class="material-icons">arrow_back</i>
                            <span>НАЗАД КЪМ НАЧАЛО</span>
                        </button>
                        <div class="block-header">
                            <div class="card">
                                <div class="body">
                                    <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                                    <ol class="breadcrumb breadcrumb-col-black">
                                        <li onclick="window.location.href='./index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                        <li onclick="window.location.href='./affectedSongs.php'"><a href="javascript:void(0);"><i class="material-icons">music_note</i>ПОВЛИЯНИ ПЕСНИ</a></li>
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
                                    <h2>Тук можете да видите ефекта на повлияване от TikTok в зависимост от пиковете на <strong><?php echo $songData["song_name"] ?></strong> в двете платформи. Тази песен се счита за повлияна, защото датата на пик в TikTok е преди датата на пик в Spotify.</h2>
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
                            <div class="text">Брой видеа в TikTok</div>
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

                <div class="info"> 
                    <i class="material-icons" data-toggle="modal" data-target="#defaultModal" style="cursor: pointer;display: inline-block;">help_outline</i>
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

            <div class="row clearfix">

                <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                    <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-primary">
                            <div class="panel-heading" role="tab" id="headingOne_1">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="true" aria-controls="collapseOne_1" class="">
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В TIKTOK<i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_1" aria-expanded="true">
                                <div class="body songBox" style="padding:1%">
                                    <canvas id="TikTokGraphChart"></canvas>
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
                                    ИЗМЕНЕНИЕ НА ПОПУЛЯРНОСТ В SPOTIFY<i class="material-icons">keyboard_arrow_down</i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne_2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_2" aria-expanded="true">
                                <div class="body songBox" style="padding:1%">
                                    <canvas id="SpotifyGraphChart"></canvas>
                                </div>
                            </div>
                        </div>
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

    let syDataNumsDirty = JSON.parse('<?php echo json_encode($syNums) ?>');
    let ttDataNumsDirty = JSON.parse('<?php echo json_encode($ttNums) ?>');

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
                },
                maintainAspectRatio: false
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
