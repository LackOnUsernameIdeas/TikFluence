<?php

//1. Update the curlUrl and auth token to retrieve data

include '../includes/databaseManager.class2.php';
include '../includes/curlFunctions.php';

function findSongIdForDatapoint($db, $dp){

    $tiktok_platform_id = $dp['tiktok_platform_id'];
    $song = $db->findSongByTiktokId($tiktok_platform_id);
        
    if($song != false) {
        return $song['id'];
    } else {
        $id = $db->createSong([
            'song_name' => $dp['song_name'],
            'artist_name' => $dp['artist_name'],
            'tiktok_platform_id' => $dp['tiktok_platform_id'],
            'spotify_platform_id' => $dp['spotify_platform_id'],
            'youtube_platform_id' => $dp['youtube_platform_id'],
            'itunes_platform_id' => $dp['itunes_platform_id'],
            'itunes_album_platform_id' => $dp['itunes_album_platform_id'],
            'song_guid' => $dp['song_guid']
        ]);

        return $id;
    }
}

$db = new DatabaseManager();
$date = date("Y-m-d");

$dataPoints = fetchTiktokDatapoints();
foreach($dataPoints as $dp){
    var_dump($dp);
    echo "<hr>";
    $id = findSongIdForDatapoint($db, $dp);

    var_dump($dp);

    $dp["fetch_date"] = $date;
    $dp["source"] = "https://chartex.com";
    $dp["song_id"] = $id;
    $dp["spotify_popularity"] = isset($dp["spotify_platform_id"]) ? fetchSpotifyDatapoints($dp["spotify_platform_id"]) : null;
    $dp["youtube_views"] = isset($dp["youtube_platform_id"]) ? fetchYoutubeDatapoints($dp["youtube_platform_id"]) : null;

    $db->insertDatapoint($dp);
}
