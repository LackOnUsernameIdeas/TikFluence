<?php

    //Вмъкване на нужните файлове
    include "../selectDate.php";
    include "../includes/databaseManager.php";
    include "../includes/common.php";

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    //Осигуряваме си необходимите данни
    $dates = $db->listDatesTikTokers();
    $datesArray = [];

    foreach($dates as $date){
        $timestamp = new DateTime($date["fetch_date"]);
        $datesArray[] = $timestamp->format('Y-m-d');
    }
    
    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] >= '2023-01-08' ? $_SESSION["setDate"] : date("Y-m-d");

    //Запазваме данните в променлива
    $tiktokers = $db->getTiktokersTodayData($selectDate);
    $tiktokersTop = $db->getTiktokersTodayDataTop($selectDate);

    $topvideos = $db->getTopVideosTodayData($selectDate);

    //Осигуряваме си необходимите данни
    
    $tiktokersArray = [];
    $followers = [];

    if($tiktokers != false){
        foreach($tiktokersTop as $dp){
            $tiktokersArray[] = $dp["tiktoker"];
            $followers[] = $dp["followers_count"];
        }

        //Осигуряваме рангове за тиктокърите
        for($i=0;$i<count($tiktokers);$i++){
            $tiktokers[$i]["rank"] = $i + 1;
        }

    }

    if($topvideos != false){
        //Осигуряваме рангове за видеята
        for($i=0;$i<count($topvideos);$i++){
            $topvideos[$i]["rank"] = $i + 1;
        }
    }

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>ОЩЕ СТАТИСТИКИ</title>
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
                <a class="navbar-brand" href="../index.php">TIKFLUENCE</a>
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
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;"><ul class="list" style="overflow: hidden; width: auto; height: 584px;">
                    <li class="header">ГЛАВНО МЕНЮ</li>
                    <li>
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
                                <a href="songsBG.php" class=" waves-effect waves-block">
                                    <i class="material-icons">music_note</i>
                                    <span>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="#" class="waves-effect waves-block">
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

    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="body">
                    <div class="block-header card p-t-10 p-l-10">
                        <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                        <ol class="breadcrumb breadcrumb-col-black">
                            <li onclick="window.location.href='../index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                            <li><a href="javascript:void(0);"><i class="material-icons">insert_chart</i>СТАТИСТИКИ</a></li>
                            <li class="active"><i class="material-icons">person</i>ТОП 200 НАЙ-ИЗВЕСТНИ ТИКТОКЪРИ</li>
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
                                min="<?php echo $datesArray[0] ?>" max="<?php echo end($datesArray) ?>" onchange=" window.location.replace('../selectDate.php?setDate=' + this.value + '&redirectURI=' + window.location.href)">
                            <?php endif;?>
            
                    </div>
                </div>
            </div>

            <?php if($tiktokers != false):?>
                <!-- Second Exportable table -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    ТОП 200 НАЙ-ИЗВЕСТНИ ТИКТОКЪРИ
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
                                                <th>ПРОФИЛНА СНИМКА</th>
                                                <th>ТИКТОКЪР</th>
                                                <th>ИМЕ В ТИКТОК</th>
                                                <th>ПОСЛЕДОВАТЕЛИ ОБЩО</th>
                                                <th>ПОСЛЕДОВАТЕЛИ ОТ ТАЗИ ГОДИНА</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($tiktokers):?>
                                                <?php foreach($tiktokers as $st):?>
                                                    <tr>
                                                        <th><?php echo $st["rank"]?></th>
                                                        <th><?php if($st["thumbnail"]):?><img src="<?php echo $st["thumbnail"]?>" alt="Prof pic" width="42" height="42" style="vertical-align:bottom"><?php endif;?></th>
                                                        <th><?php echo $st["tiktoker"]?></th>
                                                        <th><?php echo $st["platform_name"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.tiktok.com/@<?php echo $st["platform_name"] ?>" target="_blank"><i class="material-icons" title="Вижте песента в TikTok">remove_red_eye</i></a></th>
                                                        <th><?php echo number_format($st["followers_count"])?></th>
                                                        <th><?php echo number_format($st["followers_this_year"])?></th>
                                                        <th><a href='./tiktoker.php?tid=<?php echo $st["given_id"]?>' class="btn bg-deep-purple waves-effect">Вижте повече</a></th>
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

                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    СРАВНЕНИЕ МЕЖДУ ПЪРВИТЕ 10 ТИКТОКЪРА
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
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <?php if($selectDate != "" && $selectDate != "2023-01-13"):?>
                                <div class="body">
                                    Все още няма данни за топ 200 песни глобално за днес :(
                                </div>
                            <?php elseif($selectDate == ""):?>
                                <div class="body">
                                    Трябва да изберете валидна дата!
                                </div>
                            <?php else: ?>
                                <div class="body">
                                    Съжаляваме за причиненото неудобство. Нямаме данни за 13 януари 2023 година!
                                </div>
                            <?php endif;?>
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
            labels: JSON.parse('<?php echo json_encode($tiktokersArray) ?>'),
            datasets: [{
                label: 'ПОСЛЕДОВАТЕЛИ',
                data: JSON.parse('<?php echo json_encode($followers) ?>'),
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
            document.getElementById('barChart'),
            config
        );

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