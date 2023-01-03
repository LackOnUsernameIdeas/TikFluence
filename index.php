<?php

include "./includes/databaseManager.class2.php";
$db = new DatabaseManager();

$top200 = $db->listTop200Songs();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./index.css">
    <title>Songs</title>
</head>
<body>
    <header>
        <h1>TOP 200 TIKTOK SONGS FOR TODAY</h1>
    </header>
    <main>
        <h2>Today is <?php echo date("Y-m-d")?></h2>
        <?php if($top200):?>
            <ol>
                <?php foreach($top200 as $st):?>
                    <li>
                        <p class='line'>
                            Song: <?php echo $st["song_name"]?> 
                            <br> 
                            Artist: <?php echo $st['artist_name']?>
                            <a class='btn' href='chart.php?sid=<?php echo $st["song_id"]?>'>"View Details</a>
                        </p>
                    </li>
                <?php endforeach;?>
            </ol>
        <?php else:?>
            <p>No songs currently available.</p>
        <?php endif;?>
        
    </main>
</body>
</html>