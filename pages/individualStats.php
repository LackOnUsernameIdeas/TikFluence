<?php

    // session_start();

    //Вмъкване на нужните файлове
    include '../includes/databaseManager.php';
    include '../includes/common.php';
    include '../scraping/curlFunctions.php';

    //Създаваме връзката с базата данни
    $db = new DatabaseManager();

    // // Ако сте влезли в профила си, можете да продължите
    // if (isset($_SESSION["user_id"])) {
    //     $user_id = $_SESSION["user_id"];
    //     $user = $db->getUserById($user_id);
    // } else {
    //     redirect("../logIn.php");
    // }

    // //Излиза информацията за потребителя
    // if(isset($_SESSION['tiktokUsername'])){
    //     if($_SESSION['tiktokUsername'] != null){
    //         $username = htmlspecialchars($_SESSION['tiktokUsername']);
    //         $userData = getUserData($username);
    //     }
    // }

    if(isset($_GET['tiktokUser'])){
        if($_GET['tiktokUser'] != null){
            $username = htmlspecialchars($_GET['tiktokUser']);
            $userData = getUserData($username);
        }
    }

    //Взимаме информация за потребителя и я показваме

    function getUserData($username){
        //Взимаме id на потребителя за да можем да вземем информацията за него

        // $id = fetchTikTokUserId($username);
        $sec_uid = fetchTikTokUserSecUid($username);

        //Връщаме информацията за потребителя като краен резултат

        // return fetchTikTokUserData($id);
        return fetchTikTokUserMoreDescriptiveData($sec_uid);
    }

    //Показваме профилната снимка на потребителя ако е въвел името си

    $isVerified = false;

    if(isset($userData)){
        if($userData["author"]["verified"] == true){
            $isVerified = $userData["author"]["verified"];
        }

        $videosCount = [];

        $likes = [];
        $views = [];
        $shares = [];
        $comments = [];

        for($i=0; $i<count($userData["videos"]);$i++){
            array_push($videosCount, $i + 1);

            array_push($likes, $userData["videos"][$i]["likes"]);
            array_push($views, $userData["videos"][$i]["plays"]);
            array_push($shares, $userData["videos"][$i]["shares"]);
            array_push($comments, $userData["videos"][$i]["comments"]);
        }

        $hashtags = [];
        $hashtagsTimesUsed = [];

        foreach($userData["hashtags"] as $ht){
            $hashtags[] = $ht["name"];
            $hashtagsTimesUsed[] = $ht["count"];
        }

        $mentions = [];
        $timesPeopleAreMentioned = [];

        foreach($userData["mentions"] as $ht){
            $mentions[] = $ht["name"];
            $timesPeopleAreMentioned[] = $ht["count"];
        }
        
    }


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>СТАТИСТИКИ ЗА ПОТРЕБИТЕЛЯ</title>

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

    <!-- Morris Chart Css-->
    <link href="../plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="../css/themes/all-themes.css" rel="stylesheet" />
</head>

<body class="theme-purple">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Моля изчакайте...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
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
                    <!-- <li><a href="javascript:void(0);" class="js-search" data-close="true"><button type="button" class="btn bg-deep-purple waves-effect" onclick="window.location.href='../logOut.php'">ИЗЛЕЗ ОТ ПРОФИЛА</button></a></li> -->
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
                    <li>
                        <a href="songs.php">
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
                    <li class="active">
                        <a href="#">
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
                        <a href="changelogs.php">
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
                            <li class="active"><i class="material-icons">person_outline</i>СТАТИСТИКИ ЗА ПОТРЕБИТЕЛЯ</li>
                        </ol>
                    </div>
                </div>

                <form action="#" method="GET">
                    <label for="tiktokUser">TikTok потребител: </label>
                    <input type="text" id="tiktokUser" name="tiktokUser"><br><br>
                    <button>Get Data</button>
                </form> 

                <br>
                <?php if(isset($userData)): ?>
                    <div class="row clearfix">

                        <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8">
                            <div class="card">
                                <div class="body">
                                    <!-- User Info -->
                                    <div class="row clearfix">
                                        <div class="container-fluid">

                                            <div class="user-info">
                                                <div class="body">
                                                    
                                                        <div class="image">
                                                            <img src="<?php echo $userData["author"]["avatarLarger"]?>" width="68" height="68" alt="User" />
                                                        </div>
                                                        
                                                        <div class="info-container">
                                                            <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?= isset($userData) ? $userData["author"]["uniqueId"] : null ?> <img src="<?= $isVerified ? "../images/verified.png" : ""?>" width="10px" height="10px">
                                                            </div>
                                                            <div class="email" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?= isset($userData) ? $userData["author"]["nickname"] . " |" : null ?> <?= isset($userData) ? $userData["author"]["country"] : null ?>
                                                            </div>
                                                        </div>
                                                </div>
                                                
                                            </div>

                                        </div>
                                    </div>
                                    <!-- #User Info -->
                                </div>
                            </div>
                        </div>                            
                    
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-deep-purple hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">person</i>
                                </div>
                                <div class="content">
                                    <div class="text">Последователи</div>
                                    <!-- <div class="number">wcw</div>  -->
                                    <div class="number count-to" data-from="0" data-to="<?php echo $userData["author"]["followerCount"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-red hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">person_outline</i>
                                </div>
                                <div class="content">
                                    <div class="text">Последвани</div>
                                    <div class="number count-to" data-from="0" data-to="<?php echo $userData["author"]["followingCount"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-deep-orange hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">video_library</i>
                                </div>
                                <div class="content">
                                    <div class="text">Брой видеа</div>
                                    <div class="number count-to" data-from="0" data-to="<?php echo $userData["author"]["videoCount"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-yellow hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">thumb_up</i>
                                </div>
                                <div class="content">
                                    <div class="text">Брой харесвания</div>
                                    <div class="number count-to" data-from="0" data-to="<?php echo $userData["author"]["heartCount"] ?>" data-speed="3000" data-fresh-interval="20"></div> 
                                </div>
                            </div>
                        </div>
        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        ХАРЕСВАНИЯ НА СКОРО КАЧЕНИ ВИДЕА
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
                                        <canvas id="LikesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        ГЛЕДАНИЯ НА СКОРО КАЧЕНИ ВИДЕА
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
                                        <canvas id="ViewsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        СПОДЕЛЯНИЯ НА СКОРО КАЧЕНИ ВИДЕА
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
                                        <canvas id="SharesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        КОМЕНТАРИ НА СКОРО КАЧЕНИ ВИДЕА
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
                                        <canvas id="CommentsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        НАЙ-ИЗПОЛЗВАНИТЕ ХАШТАГОВЕ (СПОРЕД ПОСЛЕДНИТЕ ВИДЕА)
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
                                        <canvas id="HashtagsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        НАЙ-ОТБЕЛЯЗВАНИТЕ ПОТРЕБИТЕЛИ (СПОРЕД ПОСЛЕДНИТЕ ВИДЕА)
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
                                        <canvas id="MentionsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                <?php endif;?>

            </div>

        </div>
    </section>


</body>   

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Статистики -->
<script>

    //Данни за ползване
    let videosCount =  JSON.parse('<?php echo json_encode($videosCount) ?>');

    let likes =  JSON.parse('<?php echo json_encode($likes) ?>');
    let views =  JSON.parse('<?php echo json_encode($views) ?>');
    let comments =  JSON.parse('<?php echo json_encode($comments) ?>');
    let shares =  JSON.parse('<?php echo json_encode($shares) ?>');

    let hashtags = JSON.parse('<?php echo json_encode($hashtags) ?>');
    let hashtagsTimesUsed = JSON.parse('<?php echo json_encode($hashtagsTimesUsed) ?>');

    let mentions = JSON.parse('<?php echo json_encode($mentions) ?>');
    let timesPeopleAreMentioned = JSON.parse('<?php echo json_encode($timesPeopleAreMentioned) ?>');


    //Харесвания
    new Chart(document.getElementById('LikesChart'), {
        type: 'bar',
        data: {
            labels: videosCount, //x
            datasets: [
                {
                    label: 'Харесвания',
                    data: likes, //y
                    borderColor: 'rgb(255, 240, 0)',
                    backgroundColor: 'rgba(255, 240, 0, 0.7)',
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
            }
        }
    });

    //Гледания
    new Chart(document.getElementById('ViewsChart'), {
        type: 'bar',
        data: {
            labels: videosCount, //x
            datasets: [
                {
                    label: 'Гледания',
                    data: views, //y
                    borderColor: 'rgb(159, 90, 253)',
                    backgroundColor: 'rgba(159, 90, 253, 0.7)',
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
            }
        }
    });

    //Споделяния
    new Chart(document.getElementById('SharesChart'), {
        type: 'bar',
        data: {
            labels: videosCount, //x
            datasets: [
                {
                    label: 'Споделяния',
                    data: views, //y
                    borderColor: 'rgb(255, 0, 0)',
                    backgroundColor: 'rgba(255, 0, 0, 0.7)',
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
            }
        }
    });

    //Коментари
    new Chart(document.getElementById('CommentsChart'), {
        type: 'bar',
        data: {
            labels: videosCount, //x
            datasets: [
                {
                    label: 'Коментари',
                    data: comments, //y
                    borderColor: 'rgb(241, 90, 34)',
                    backgroundColor: 'rgba(241, 90, 34, 0.7)',
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
            }
        }
    });

    //Хаштагове
    new Chart(document.getElementById('HashtagsChart'), {
        type: 'bar',
        data: {
            labels: hashtags, //x
            datasets: [
                {
                    label: 'Хаштагове',
                    data: hashtagsTimesUsed, //y
                    borderColor: 'rgb(139, 69, 19)',
                    backgroundColor: 'rgb(139, 69, 19, 0.7)',
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
            }
        }
    });

    //Отбелязвания
    new Chart(document.getElementById('MentionsChart'), {
        type: 'bar',
        data: {
            labels: mentions, //x
            datasets: [
                {
                    label: 'Отбелязвания',
                    data: timesPeopleAreMentioned, //y
                    borderColor: 'rgb(149, 53, 83)',
                    backgroundColor: 'rgb(149, 53, 83)',
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
            }
        }
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

<!-- Jquery CountTo Plugin Js -->
<script src="../plugins/jquery-countto/jquery.countTo.js"></script>

<!-- Morris Plugin Js -->
<script src="../plugins/raphael/raphael.min.js"></script>
<script src="../plugins/morrisjs/morris.js"></script>

<!-- ChartJs -->
<script src="../plugins/chartjs/Chart.bundle.js"></script>

<!-- Flot Charts Plugin Js -->
<script src="../plugins/flot-charts/jquery.flot.js"></script>
<script src="../plugins/flot-charts/jquery.flot.resize.js"></script>
<script src="../plugins/flot-charts/jquery.flot.pie.js"></script>
<script src="../plugins/flot-charts/jquery.flot.categories.js"></script>
<script src="../plugins/flot-charts/jquery.flot.time.js"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="../plugins/jquery-sparkline/jquery.sparkline.js"></script>

<!-- Custom Js -->
<script src="../js/admin.js"></script>
<script src="../js/pages/index.js"></script>

<!-- Demo Js -->
<script src="../js/demo.js"></script>

</html>