<?php

    //Вмъкване на нужните файлове
    include "./selectDate.php";
    include './includes/databaseManager.php';
    include './includes/common.php';

    //Ако няма такова id за песен, потребителят е върнат в tiktokers.php
    $tid = isset($_GET["tid"]) && ctype_digit($_GET['tid']) ? intval($_GET["tid"]) : -1;
    if($tid < 0) redirect("tiktokers.php");
    
    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    //Слагаме избраната дата в променлива и с нея издърпваме нужните данни
    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] >= '2023-01-08' ? $_SESSION["setDate"] : date("Y-m-d");

    if($selectDate == "2023-01-13"){
        $selectDate = "2023-01-12";
        $whyIsThis = true;
    }
    
    //Запазваме данните за тиктокъра в променливи
    $tiktokerMainData = $db->getTikTokerData($tid, $selectDate);
    $tiktokerDatapoints = $db->getTikTokerDatapoints($tid, $selectDate);

    //Ако няма данни за такъв тиктокър, потребителят е върнат в tiktokers.php
    if($tiktokerMainData == false) redirect("tiktokers.php");

    //Осигуряваме си необходимите данни
    $tiktokerDataForSpecificDate = $db->getTikTokerDataForSpecificDate($tid, $selectDate);

    $dates = [];

    $followers = [];
    $followersThisYear = [];

    foreach($tiktokerDatapoints as $dp){
        $timestamp = new DateTime($dp["fetch_date"]);
        $dates[] = $timestamp->format('Y-m-d');

        $followers[] = $dp["followers_count"];
        $followersThisYear[] = $dp["followers_this_year"];
    }

    $fetchDatesForButton = $db->listDatesForCurrentTikToker($tid);

    $chooseDatesForButton = [];
    foreach($fetchDatesForButton as $date){    
        $timestamp = new DateTime($date["fetch_date"]);
        $chooseDatesForButton[] = $timestamp->format('Y-m-d');
    }

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Статистики за <?php echo $tiktokerMainData["tiktoker"] ?></title>
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
        .tiktokerBox{
            width: 100%;
            min-height: 600px;
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
                            <li>
                                <a href="songsBG.php" class="waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="tiktokers.php" class="menu-toggle waves-effect waves-block">
                                    <i class="material-icons">person</i>
                                    <span>ТОП 200 НАЙ-ИЗВЕСТНИ ТИКТОКЪРИ</span>
                                </a>
                                <ul class="ml-menu">
                                    <li class="active">
                                        <a href="#" class="waves-effect waves-block">
                                            <span>СТАТИСТИКИ ЗА <?php echo $tiktokerMainData["tiktoker"] ?></span>
                                        </a>
                                    </li>
                                </ul>
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
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="body">
                    <button type="button" class="btn bg-purple waves-effect card" onclick="window.location.href='tiktokers.php'">
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
                                    <li onclick="window.location.href='tiktokers.php'"><a href="javascript:void(0);"><i class="material-icons">person</i>ТОП 200 НАЙ-ИЗВЕСТНИ ТИКТОКЪРИ</a></li>
                                    <li class="active"><i class="material-icons">person</i>СТАТИСТИКИ ЗА <?php echo $tiktokerMainData["tiktoker"] ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="body">
                            <div class="block-header">
                                <h2>СТАТИСТИКИ ЗА:</h2>
                                <h1><?php if($tiktokerMainData["thumbnail"]):?><img src="<?php echo $tiktokerMainData["thumbnail"]?>" alt="Prof pic" width="62" height="62" style="vertical-align:bottom"><?php endif;?>&nbsp;<a href="https://www.tiktok.com/@<?php echo $tiktokerMainData["platform_name"] ?>" target="_blank"><?php echo $tiktokerMainData["tiktoker"]?></a></h1>
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

                    <?php if(isset($whyIsThis) && $whyIsThis):?>
                        <div class="card">
                            <div class="body">
                                <div class="block-header">
                                    <h2>Извиняваме се, но за 13 януари 2023 година липсват данни! Моля изберете друга дата.</h2>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
            
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-deep-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person</i>
                        </div>
                        <div class="content">
                            <div class="text">ПОСЛЕДОВАТЕЛИ</div>
                            <div class="number"><?php echo number_format($tiktokerDataForSpecificDate["followers_count"]) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-deep-purple hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person_outline</i>
                        </div>
                        <div class="content">
                            <div class="text">ПОСЛЕДОВАТЕЛИ ОТ <?php echo date("Y") ?></div>
                            <div class="number"><?php echo number_format($tiktokerDataForSpecificDate["followers_this_year"]) ?></div>
                        </div>
                    </div>
                </div>
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
            </div>
        </div>

        <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
            <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                <div class="panel panel-primary">
                    <div class="panel-heading" role="tab" id="headingOne_1">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="true" aria-controls="collapseOne_1" class="">
                            ИЗМЕНЕНИЕ НА ПОСЛЕДОВАТЕЛИТЕ<i class="material-icons">keyboard_arrow_down</i>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne_1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_1" aria-expanded="true">
                        <div class="body tiktokerBox" style="padding:1%">
                            <canvas id="FollowersChart"></canvas>
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
                            ИЗМЕНЕНИЕ НА ПОСЛЕДОВАТЕЛИТЕ ОТ <?php echo date("Y") ?> <i class="material-icons">keyboard_arrow_down</i>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne_2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_2" aria-expanded="true">
                        <div class="body tiktokerBox" style="padding:1%">
                            <canvas id="FollowersThisYearChart"></canvas>
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
    </section>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Статистики -->
    <script>
        //Данни за ползване
        let dates =  JSON.parse('<?php echo json_encode($dates) ?>');

        let followers = JSON.parse(`<?php echo json_encode($followers) ?>`);
        let followersThisYear = JSON.parse(`<?php echo json_encode($followersThisYear) ?>`);

        //Последователи тази година
        new Chart(document.getElementById('FollowersThisYearChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Последователи от тази година',
                        data: followersThisYear, //y
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
                    },
                maintainAspectRatio: false
                }
            });

        //Последователи
        new Chart(document.getElementById('FollowersChart'), {
            type: 'line',
            data: {
                labels: dates, //x
                datasets: [
                    {
                        label: 'Последователи',
                        data: followers, //y
                        borderColor: 'rgba(255, 148, 112, 1)',
                        backgroundColor: 'rgba(255, 148, 112, 0.3)',
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
                    },
                maintainAspectRatio: false
                }
            });
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