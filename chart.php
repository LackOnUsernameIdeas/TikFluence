<?php

include './includes/common.php';
include './includes/databaseManager.class2.php';

$sid = isset($_GET["sid"]) && ctype_digit($_GET['sid']) ? intval($_GET["sid"]) : -1;
if($sid < 0) redirect("./index.php");

$db = new DatabaseManager();
$dataPoints = $db->getDatapointsForSong($sid);

if($dataPoints === false) redirect("./index.php");


// [object, object{
//  spotify_popularity, tiktok_popularity  
//}]

// [spotify_popularity_day1, spotify_popularity_day2]
// [tiktok_popularity_day1, tiktok_popularity_day2]

$sy = [];
$tt = [];
$yt = [];

foreach($dataPoints as $dp){
    $sy[] = $dp["spotify_popularity"];
    $tt[] = $dp["number_of_videos_last_14days"];
    $yt[] = $dp["youtube_views"];
}

$maxTiktok = max($tt);
$maxYoutube = max($yt);

for($i=0; $i<count($tt); $i++){
    $tt[$i] = ($tt[$i] * 100)/$maxTiktok;
}
for($i=0; $i<count($yt); $i++){
    $yt[$i] = ($yt[$i] * 100)/$maxYoutube;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChartJS</title>
</head>
<body>
    <div id="content">
        <canvas id="myChart"></canvas>
    </div>
    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart');

        let ytData = JSON.parse('<?php echo json_encode($yt) ?>');
        let syData = JSON.parse('<?php echo json_encode($sy) ?>');
        let ttData = JSON.parse('<?php echo json_encode($tt) ?>');

        new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['2023-01-03', '2023-01-04', '2023-01-05', '2023-01-06', '2023-01-07'], //x
            datasets: [
                {
                label: 'Youtube views',
                data: ytData , //y
                //yAxesID: 'A',
                borderColor: 'rgba(255, 99, 132, 0.2)',
                //fill: true,
                tension: 0.4
                },
                {
                label: 'Spotify popularity',
                data: syData, //y
                //yAxesID: 'B',
                borderColor: 'rgba(147, 250, 165, 1)',
                //fill: true,
                tension: 0.4
                },
                {
                label: 'Tiktok popularity',
                data: ttData, //y
                //yAxesID: 'A',
                borderColor: 'rgba(159, 90, 253, 1)',
                //fill: true,
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
</body>
</html>