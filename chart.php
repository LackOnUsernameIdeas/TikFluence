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

        let ytData = [98,99.4, 96.5, 100];
        let syData = [38,38,40,50];
        let ttData = [97,99.4,100,98.7];

        new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['24.12.2022', '26.12.2022', '28.12.2022', '30.12.2022'], //x
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