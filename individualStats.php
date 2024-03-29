<?php

//Вмъкване на нужните файлове
include './includes/databaseManager.php';
include './includes/common.php';
include './scraping/curlFunctions.php';

//Създаваме връзката с базата данни
$db = new DatabaseManager();

//Извличаме името от данните, които ни дава TikTok API
$userBasicData = [];
$userVideoData = [];

if (isset($_GET["code"]) && !isset($_COOKIE["tiktok_access_token"])) { //Ако потребителят не е потвърдил все още

    //Генерираме си access token, когато потребителят влезе и потвърди от профила си
    $tokens = generateTikTokAccessToken($_GET["code"]);
    $accessToken = $tokens["access_token"];
    $refreshToken = $tokens["refresh_token"];

    $expirationIn = 3600;
    $expirationTime = time() + $expirationIn;

    setcookie('tiktok_access_token', $accessToken, $expirationTime);
    setcookie('tiktok_refresh_token', $refreshToken, time() + 86400);

    setcookie('tiktok_access_token_expiration', $expirationTime, $expirationTime);

    $userBasicData = getUserBasicData($accessToken);
    $userVideoData = getUserVideoData($accessToken);

    //Генерираме си подробни данни за потребителя, ако профилът му не е заключен и има видеа
    if ($accessToken != false) {
        $openUserId = getUserOpenId($accessToken);

        $usernameLink = generateTikTokUsername("https://open-api.tiktok.com/shortlink/profile/?open_id=$openUserId");
        $username = explode("?", explode('@', $usernameLink)[1])[0];
    }
} elseif (isset($_COOKIE["tiktok_access_token"])) { //Ако потребителят веднъж е потвърдил

    $accessToken = $_COOKIE["tiktok_access_token"];
    $refreshToken = $_COOKIE["tiktok_refresh_token"];

    $expirationTime = $_COOKIE["tiktok_access_token_expiration"];
    $currentTime = time();

    //Проверяваме дали токена е изтекъл
    if ($currentTime > $expirationTime) {
        //Генерираме си нов access token
        $accessToken = refreshTikTokAccessToken($refreshToken);
        $expiresIn = 3600; // слагаме времето на изтичане отново да е 1 час
        $expirationTime = time() + $expiresIn;

        //Актуализираме бизквитката с новия токен и времето му на изтичане на валидността
        setcookie('tiktok_access_token', $accessToken, $expirationTime);
    }

    $userBasicData = getUserBasicData($accessToken);
    $userVideoData = getUserVideoData($accessToken);

    //Генерираме си подробни данни за потребителя, ако профилът му не е заключен и има видеа
    if ($accessToken != false) {
        $openUserId = getUserOpenId($accessToken);

        $usernameLink = generateTikTokUsername("https://open-api.tiktok.com/shortlink/profile/?open_id=$openUserId");
        $username = explode("?", explode('@', $usernameLink)[1])[0];
    }
}

//Показваме профилната снимка на потребителя ако е въвел името си. Подсигуряме си информацията за потребителя под формата на масиви.
$videosPublishDates = [];

$likes = [];
$views = [];
$shares = [];
$comments = [];

if (isset($userVideoData) && $userVideoData != false) {
    foreach ($userVideoData as $vid) {
        $videosPublishDates[] = gmdate("Y-m-d", $vid["create_time"]);

        $likes[] = $vid["like_count"];
        $views[] = $vid["view_count"];
        $shares[] = $vid["share_count"];
        $comments[] = $vid["comment_count"];
    }
}


//С помощта на това id, потребителят е сигурен, че не злоупотребяваме с неговите данни
$reqCallbackState = uniqid();

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>СТАТИСТИКИ ЗА ПОТРЕБИТЕЛЯ</title>

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

    <!-- Morris Chart Css-->
    <link href="./plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="./css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="./css/themes/all-themes.css" rel="stylesheet" />

    <style>
        .userChartsBox {
            width: 100%;
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
                <a class="navbar-brand" href="./index.php">TikFluence</a>
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
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 584px;">
                    <ul class="list" style="overflow: hidden; width: auto; height: 584px;">
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
                        <li class="active">
                            <a href="#" class=" waves-effect waves-block">
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
                    </ul>
                    <div class="slimScrollBar" style="background: rgba(0, 0, 0, 0.5); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 584px;"></div>
                    <div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
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
                    <div class="col">
                        <div class="block-header card p-t-10 p-l-10">
                            <h2>ВИЕ СЕ НАМИРАТЕ В:</h2>
                            <ol class="breadcrumb breadcrumb-col-black">
                                <li onclick="window.location.href='./index.php'"><a href="javascript:void(0);"><i class="material-icons">home</i>НАЧАЛО</a></li>
                                <li class="active"><i class="material-icons">person_outline</i>МОИТЕ СТАТИСТИКИ В TIKTOK</li>
                            </ol>
                        </div>
                    </div>
                    <?php if ($userBasicData != []) : ?>
                        <div class="col">
                            <a href="https://www.tiktok.com/logout?redirect_url=https://fluence.noit.eu/individualStats.php" class="btn bg-purple waves-effect" target="_blank" id="myLink">ИЗЛЕЗ ОТ ПРОФИЛА СИ</a>
                        </div>
                        <br>
                    <?php endif; ?>
                </div>

                <?php if ($userBasicData == []) : ?>

                    <div class="row clearfix">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">

                                <div class="body">
                                    <p class="lead" style="font-size: 170%;">
                                        В тази страница имате възможността да видите статистиките на вашия собствен профил!
                                    </p>
                                    <p>
                                        За максималко ваше улеснение, по-долу има предоставени стъпки за действие.
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>

                    <a href='https://www.tiktok.com/auth/authorize/?client_key=awntkz3ma9o5eetl&scope=user.info.basic,video.list&response_type=code&redirect_uri=https://fluence.noit.eu/individualStats.php&state=<?php echo $reqCallbackState ?>' target="_blank"><img src="./images/logInButton.png" style="border-radius:10px;max-width:100%"></a>
                    <br>
                    <br>

                    <div class="row clearfix">

                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong>1.</strong> След като натиснете бутонa, вие бивате изпратени в страница, в която трябва да изберете по какъв начин да влезете във вашия профил в TikTok. В случай че нямате профил, трябва да натиснете опцията - "Sign up", посочена с червена стрелка, най-отдолу и да си изберате начина на създаване на профил.
                                    </h2>
                                </div>
                                <div class="body">
                                    <img src="./images/firstStep.png" width="100%">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong>2.</strong> Ако вече имате направен профил и сте избрали опцията - "Use phone / email / username" от стъпка <strong>1</strong>, вие бивате изпратени в страница, в която можете да влезете чрез телефонния ви номер. Можете и да изберете опцията да влезете чрез имейл или потребителско име, посочена със стрелка на снимката.
                                    </h2>
                                </div>
                                <div class="body">
                                    <img src="./images/secondStep.png" width="100%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">

                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong>3.</strong> Въвеждате вашите данни за имейл/потребителско име и парола и натискате - "Log in", за да влезете във вашия профил.
                                    </h2>
                                </div>
                                <div class="body">
                                    <img src="./images/thirdStep.png" width="100%">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong>4.</strong> След като успешно сте влезли в профила си, натискате на - "Authorize" като по този начин се съгласявате, че TikFluence може да използва вашите данни за статистики.
                                    </h2>
                                </div>
                                <div class="body">
                                    <img src="./images/fourthStep.png" width="100%">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong>5.</strong> Ако сте се справили успешно, това трябва да е страницата, която виждате!
                                    </h2>
                                </div>
                                <div class="body">
                                    <img src="./images/fifthStep.png" width="100%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong>6.</strong> Не забравяйте накрая да излезете от профила си. След натискане на бутона за излизане ще бъдете препратени към страницата на TikTok. Можете да затворите страницата и да се върнете обратно към страницата на TikFluence!
                                    </h2>
                                </div>
                                <div class="body">
                                    <img src="./images/sixthStep.png" width="100%">
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if ($userBasicData != []) : ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row clearfix">

                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="min-width:50%;">
                                <div class="card">
                                    <div class="body">
                                        <!-- User Info -->
                                        <div class="row clearfix">
                                            <div class="container-fluid">

                                                <div class="user-info">
                                                    <div class="body">

                                                        <div class="image">
                                                            <img src="<?php echo $userBasicData["avatar_url"] ?>" width="68" height="68" alt="User" />
                                                        </div>

                                                        <div class="info-container">
                                                            <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?= $username ?> <img src="<?= $userBasicData["is_verified"] ? "./images/verified.png" : "" ?>" width="10px" height="10px">
                                                            </div>
                                                            <div class="email" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?= $userBasicData["display_name"] . " |" ?> <?= $userBasicData["bio_description"] ?>
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


                            <div class="row clearfix">

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                    <div class="info-box bg-deep-purple hover-zoom-effect">
                                        <div class="icon">
                                            <i class="material-icons">person</i>
                                        </div>
                                        <div class="content">
                                            <div class="text">Последователи</div>
                                            <div class="number count-to" data-from="0" data-to="<?php echo $userBasicData["follower_count"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                    <div class="info-box bg-red hover-zoom-effect">
                                        <div class="icon">
                                            <i class="material-icons">person_outline</i>
                                        </div>
                                        <div class="content">
                                            <div class="text">Последвани</div>
                                            <div class="number count-to" data-from="0" data-to="<?php echo $userBasicData["following_count"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                    <div class="info-box bg-deep-orange hover-zoom-effect">
                                        <div class="icon">
                                            <i class="material-icons">video_library</i>
                                        </div>
                                        <div class="content">
                                            <div class="text">Брой видеа</div>
                                            <div class="number count-to" data-from="0" data-to="<?php echo count($userVideoData) ?>" data-speed="3000" data-fresh-interval="20"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                    <div class="info-box bg-yellow hover-zoom-effect">
                                        <div class="icon">
                                            <i class="material-icons">thumb_up</i>
                                        </div>
                                        <div class="content">
                                            <div class="text">Брой харесвания</div>
                                            <div class="number count-to" data-from="0" data-to="<?php echo $userBasicData["likes_count"] ?>" data-speed="3000" data-fresh-interval="20"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">

                                <div class="body">
                                    <p>
                                        Имате възможност да видите данни до 10 минути на едно зареждане на страницата!
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php if ($userVideoData != false) : ?>
                        <div class="row clearfix">
                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingOne_1">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion_1" href="#collapseOne_1" aria-expanded="true" aria-controls="collapseOne_1" class="">
                                                    ПОСЛЕДОВАТЕЛИ В РЕАЛНО ВРЕМЕ <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_1" aria-expanded="true">
                                            <div class="body userChartsBox" style="padding:1%">
                                                <canvas id="FollowersRealtimeChart"></canvas>
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
                                                    ХАРЕСВАНИЯ В РЕАЛНО ВРЕМЕ <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_2" aria-expanded="true">
                                            <div class="body userChartsBox" style="padding:1%">
                                                <canvas id="LikesRealtimeChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion_3" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingOne_3">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion_3" href="#collapseOne_3" aria-expanded="true" aria-controls="collapseOne_3" class="">
                                                    ХАРЕСВАНИЯ НА СКОРО КАЧЕНИ ВИДЕА <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_3" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_3" aria-expanded="true">
                                            <div class="body userChartsBox" style="padding:1%">
                                                <canvas id="LikesChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion_4" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingOne_4">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion_4" href="#collapseOne_4" aria-expanded="true" aria-controls="collapseOne_4" class="">
                                                    ГЛЕДАНИЯ НА СКОРО КАЧЕНИ ВИДЕА <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_4" aria-expanded="true">
                                            <div class="body userChartsBox" style="padding:1%">
                                                <canvas id="ViewsChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion_5" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingOne_5">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion_5" href="#collapseOne_5" aria-expanded="true" aria-controls="collapseOne_5" class="">
                                                    СПОДЕЛЯНИЯ НА СКОРО КАЧЕНИ ВИДЕА <i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_5" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_5" aria-expanded="true">
                                            <div class="body userChartsBox" style="padding:1%">
                                                <canvas id="SharesChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group" id="accordion_6" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading" role="tab" id="headingOne_5">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion_6" href="#collapseOne_6" aria-expanded="true" aria-controls="collapseOne_6" class="">
                                                    КОМЕНТАРИ НА СКОРО КАЧЕНИ ВИДЕА<i class="material-icons">keyboard_arrow_down</i>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_6" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_6" aria-expanded="true">
                                            <div class="body userChartsBox" style="padding:1%">
                                                <canvas id="CommentsChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="card">
                                    <div class="body" style="font-size:18px;">
                                        <h2 style="text-align: center;">
                                            Трябва да имате качени видеа и отключен профил, за да можете да видите повече статистики!
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        </div>

                    <?php endif; ?>
                    <!-- Footer -->
                    <div class="col-xs-14 col-sm-14 col-md-14 col-lg-14">
                        <div class="card">
                            <div class="body">

                                <div class="legal">
                                    <?php include './footer.php'; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- #Footer -->
            </div>
    </section>


</body>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    document.getElementById("myLink").addEventListener("click", function(event) {
        event.preventDefault();

        //Изтриваме бисквитките от всички страници на приложението
        document.cookie = "tiktok_access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "tiktok_refresh_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "tiktok_access_token_expiration=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        // Отваря страницата на TikTok в нов прозорец
        window.open('https://www.tiktok.com/logout?redirect_url=https://fluence.noit.euindividualStats.php', '_blank');

        // Препраща текущия прозорец към https://fluence.noit.eu/individualStats.php след 3 секунди
        setTimeout(function() {
            window.location.href = "https://fluence.noit.eu/individualStats.php";
        }, 1000);
    });
</script>
<script src="https://cdn.socket.io/4.0.1/socket.io.js"></script>
<script>
    //Данни за ползване
    let videosPublishDates = JSON.parse('<?php echo json_encode($videosPublishDates) ?>');

    let likes = JSON.parse('<?php echo json_encode($likes) ?>');
    let views = JSON.parse('<?php echo json_encode($views) ?>');
    let comments = JSON.parse('<?php echo json_encode($comments) ?>');
    let shares = JSON.parse('<?php echo json_encode($shares) ?>');

    let followersLiveData = JSON.parse('<?php echo json_encode($userBasicData["follower_count"]) ?>');
    let likesLiveData = JSON.parse('<?php echo json_encode($userBasicData["likes_count"]) ?>');

    //Статистика за харесвания
    new Chart(document.getElementById('LikesChart'), {
        type: 'bar',
        data: {
            labels: videosPublishDates, //x
            datasets: [{
                label: 'Харесвания',
                data: likes, //y
                borderColor: 'rgb(255, 240, 0)',
                backgroundColor: 'rgba(255, 240, 0, 0.7)',
                fill: true,
                tension: 0.4
            }]
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

    //Статистика за гледания
    new Chart(document.getElementById('ViewsChart'), {
        type: 'bar',
        data: {
            labels: videosPublishDates, //x
            datasets: [{
                label: 'Гледания',
                data: views, //y
                borderColor: 'rgb(159, 90, 253)',
                backgroundColor: 'rgba(159, 90, 253, 0.7)',
                fill: true,
                tension: 0.4
            }]
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

    //Статистика за споделяния
    new Chart(document.getElementById('SharesChart'), {
        type: 'bar',
        data: {
            labels: videosPublishDates, //x
            datasets: [{
                label: 'Споделяния',
                data: shares, //y
                borderColor: 'rgb(255, 0, 0)',
                backgroundColor: 'rgba(255, 0, 0, 0.7)',
                fill: true,
                tension: 0.4
            }]
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

    //Статистика за коментари
    new Chart(document.getElementById('CommentsChart'), {
        type: 'bar',
        data: {
            labels: videosPublishDates, //x
            datasets: [{
                label: 'Коментари',
                data: comments, //y
                borderColor: 'rgb(241, 90, 34)',
                backgroundColor: 'rgba(241, 90, 34, 0.7)',
                fill: true,
                tension: 0.4
            }]
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

    //Статистика за последователи в реално време

    let date = new Date();
    let hours = date.getHours();
    let minutes = date.getMinutes();

    if (minutes < 10) {
        minutes = String(date.getMinutes()).padStart(2, '0');
    }

    let time = hours + ":" + minutes;

    let followersLive = new Chart(document.getElementById("FollowersRealtimeChart"), {
        type: 'line',
        data: {
            labels: [time],
            datasets: [{
                label: 'Последователи в реално време',
                data: [followersLiveData],
                backgroundColor: 'rgba(159, 90, 253, 0.2)',
                borderColor: 'rgb(159, 90, 253)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        unit: 'second'
                    }
                }],
                yAxes: [{
                    ticks: {
                        stepSize: 1,
                        beginAtZero: true,
                        min: followersLiveData - 10,
                        max: followersLiveData + 10
                    }
                }]
            },
            maintainAspectRatio: false
        }
    });

    //Статистика за харесвания в реално време

    let likesLive = new Chart(document.getElementById("LikesRealtimeChart"), {
        type: 'line',
        data: {
            labels: [time],
            datasets: [{
                label: 'Харесвания в реално време',
                data: [likesLiveData],
                backgroundColor: 'rgba(255, 240, 0, 0.2)',
                borderColor: 'rgb(255, 240, 0)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        unit: 'second'
                    }
                }],
                yAxes: [{
                    ticks: {
                        stepSize: 3,
                        beginAtZero: true,
                        min: likesLiveData - 10,
                        max: likesLiveData + 10
                    }
                }]
            },
            maintainAspectRatio: false
        }
    });

    //Запазваме токена, който ни е необходим, за да взимаме данни за диаграмите
    let accessToken = JSON.parse('<?php echo isset($_COOKIE["tiktok_access_token"]) ? json_encode($_COOKIE["tiktok_access_token"]) : json_encode($accessToken) ?>');

    const socket = io('https://fluence-api.noit.eu/realTimeStatisticData', {
        reconnection: false, 
        reconnectionAttempts: 1
    });

    socket.on('message', (data) => {
        console.log(data);
        socket.emit('sendAccessToken', accessToken);
    });
    
    socket.on('realTimeData', (data) => {
        //Задаваме точно време
        let date = new Date();
        let hours = date.getHours();
        let minutes = date.getMinutes();

        if (minutes < 10) {
            minutes = String(date.getMinutes()).padStart(2, '0');
        }

        let time = hours + ":" + minutes;

        //Запазваме необходимата информация в променливи
        let followers = data.data.user.follower_count;
        let likes = data.data.user.likes_count;

        //Актуализираме новите данни в диаграмите
        followersLive.data.labels.push(time);
        followersLive.data.datasets[0].data.push(followers);
        followersLive.update();

        likesLive.data.labels.push(time);
        likesLive.data.datasets[0].data.push(likes);
        likesLive.update();        
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

<!-- Jquery CountTo Plugin Js -->
<script src="./plugins/jquery-countto/jquery.countTo.js"></script>

<!-- Morris Plugin Js -->
<script src="./plugins/raphael/raphael.min.js"></script>
<script src="./plugins/morrisjs/morris.js"></script>

<!-- ChartJs -->
<script src="./plugins/chartjs/Chart.bundle.js"></script>

<!-- Flot Charts Plugin Js -->
<script src="./plugins/flot-charts/jquery.flot.js"></script>
<script src="./plugins/flot-charts/jquery.flot.resize.js"></script>
<script src="./plugins/flot-charts/jquery.flot.pie.js"></script>
<script src="./plugins/flot-charts/jquery.flot.categories.js"></script>
<script src="./plugins/flot-charts/jquery.flot.time.js"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="./plugins/jquery-sparkline/jquery.sparkline.js"></script>

<!-- Custom Js -->
<script src="./js/admin.js"></script>
<script src="./js/pages/index.js"></script>

<!-- Demo Js -->
<script src="./js/demo.js"></script>

</html>