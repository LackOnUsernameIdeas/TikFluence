<?php

//Вмъкваме нужните файлове
include '../includes/databaseManager.php';
include '../includes/curlFunctions.php';

//Създаваме връзката с базата данн
$db = new DatabaseManager();

//Взимаме данните за днес и коя дата е
$tiktokers = fetchTiktokTopVideos();
$date = date("Y-m-d");

//Качваме всичко в базата данни
foreach($tiktokers as $tt){
    $tt["fetch_date"] = $date;
    $db->insertTikTokVideo($tt);
}