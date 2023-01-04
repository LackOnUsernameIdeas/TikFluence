<?php

// include "./includes/databaseManager.class2.php";
// $db = new DatabaseManager();

// $isFullList = true;

// if(isset($_GET['sid']) && ctype_digit($_GET['sid'])){
//     //details
//     $isFullList = false;
//     $sid = intval($_GET['sid']);

//     $prepared = $db->getDetails($sid);
// } else {
//     //full list
//     $prepared = $db->getFullList();
// }

// // if(isset($_GET['sid2']) && ctype_digit($_GET['sid2'])){
// //     $sid2 = intval($_GET['sid2']);
// // }
// ?>
// <!DOCTYPE html>
// <html lang="en">
// <head>
//     <meta charset="UTF-8">
//     <meta http-equiv="X-UA-Compatible" content="IE=edge">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <link rel="stylesheet" href="./index.css">
//     <title>Songs</title>
// </head>
// <body>
//     <header>
//         <h1>TOP 200 TIKTOK SONGS FOR TODAY</h1>
//     </header>
//     <main>
//         <?php
//         if($isFullList){
//             $date = date("Y-m-d");

//             echo '<h2>Today is '.$date.'</h2>';
//             //list of song names with links
//             if($prepared->rowCount() > 0){
//                 $db->fetchMode($prepared);

//                 echo "<ol>";

//                 while($row = $prepared->fetch()){
//                     echo "<li><p class='line'>Song: " . $row["song_name"] . "<br>" . "Artist: " . $row['artist_name'];
//                     echo "<a class='btn' href='masterDetails.php?sid=" . $row["id"] . "'>";
//                     echo "View Details</a></p></li>";     
//                 }  

//                 echo "</ol>";
//             } else {
//                 // no products
//                 echo "<p>No songs currently available.</p>";
//             }
//         } else {

//                 echo '<script type="text/javascript">',
//                     'jsfunction();',
//                      '</script>';

//             while($row = $prepared->fetch()){
//                 echo "<h2>Song details for: <h1>".$row["song_name"]."</h1>by<h1>".$row["artist_name"]."</h1></h2>";
//             }

//             readfile("./chart.php");

//             echo "<p><a href='masterDetails.php'>&lt;&lt; Back to full song list</a></p>";
//         }
        ?>
    </main>
</body>
</html>