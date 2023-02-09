<?php

    //Вмъкване на нужните файлове
    include "../selectDate.php";
    include "../includes/databaseManager.php";
    include "../includes/common.php";

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();
    
    //Осигуряваме си необходимите данни
    $dates = $db->listDatesSongsBG();
    $datesArray = [];

    foreach($dates as $date){
        $timestamp = new DateTime($date["fetch_date"]);
        $datesArray[] = $timestamp->format('Y-m-d');
    }


    $selectDate = isset($_SESSION["setDate"]) && $_SESSION["setDate"] >= '2023-01-08' ? $_SESSION["setDate"] : date("Y-m-d");


    $top200SongsBG = $db->listTop200SongsBG($selectDate);

    
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
                <a class="navbar-brand" href="../index.php">TIKFLUENCE</a>
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
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;"><ul class="list" style="overflow: hidden; width: auto; height: 584px;">
                    <li class="header">ГЛАВНО МЕНЮ</li>
                    <li>
                        <a href="../index.php" class="toggled waves-effect waves-block">
                            <i class="material-icons">home</i>
                            <span>НАЧАЛО</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="javascript:void(0);" class="menu-toggle waves-effect waves-block">
                            <i class="material-icons">insert_chart</i>
                            <span>СТАТИСТИКИ</span>
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
                            <li>
                                <a href="individualStats.php" class=" waves-effect waves-block">
                                    <i class="material-icons">person_outline</i>
                                    <span>ИНДИВИДУАЛНИ СТАТИСТИКИ ЗА ПОТРЕБИТЕЛ</span>
                                </a>
                            </li>
                        </ul>
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
                            <li><a href="javascript:void(0);"><i class="material-icons">insert_chart</i>СТАТИСТИКИ</a></li>
                            <li class="active"><i class="material-icons">music_note</i>ТОП TIKTOK ПЕСНИ ЗА БЪЛГАРИЯ</li>
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
                                        <tbody>
                                            <?php if($top200SongsBG):?>
                                                <?php foreach($top200SongsBG as $st):?>
                                                    <tr>
                                                        <th><?php echo $st["rank"]?></th>
                                                        <th><?php echo $st["song_name"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.tiktok.com/music/-<?php echo $st["tiktok_platform_id"] ?>" target="_blank"><i class="material-icons" title="Вижте песента в TikTok">remove_red_eye</i></a></th>
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