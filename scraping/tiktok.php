<?php

//Вмъкваме нужните файлове
include '../includes/databaseManager.php';
include 'curlFunctions.php';

//Търсим песен по запис, ако я няма я създаваме. Връща се id като резултат

function findSongIdForDatapoint($db, $dp){

    $tiktok_platform_id = $dp['tiktok_platform_id'];
    $song = $db->findSongByTiktokId($tiktok_platform_id);
        
    if($song != false) {
        return $song['id'];
    } else {
        $id = $db->insertSongGlobal([
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

//Търсим песен по запис, ако я няма я създаваме. Връща се id като резултат

function findSongIdForDatapointBG($db, $dp){

    $tiktok_platform_id = $dp['tiktok_platform_id'];
    $song = $db->findSongByTiktokIdBG($tiktok_platform_id);
        
    if($song != false) {
        return $song['id'];
    } else {
        $id = $db->insertSongBG([
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

//Създаваме връзката с базата данни
$db = new DatabaseManager();

//Взимаме данните за днес и коя дата е
$dataPoints = fetchTiktokDatapoints();
$dataPointsBG = fetchTiktokDatapointsBG();
$tiktokers = fetchTiktokTopUsers();
$topvideos = fetchTiktokTopVideos();
$date = date("Y-m-d");

//Взимаме ключ за работа със Spotify API
$spotifyAccessToken = generateSpotifyToken();

//Качваме всичко в базата данни
foreach($dataPoints as $dp){


    $id = findSongIdForDatapoint($db, $dp);


    $song = $db->findSongByTiktokId($dp["tiktok_platform_id"]);

    if(isset($song["spotify_platform_id"])){
        $dp["spotify_popularity"] = fetchSpotifyDatapoints($song["spotify_platform_id"], $spotifyAccessToken);
    } elseif(isset($dp["spotify_platform_id"])){
        $dp["spotify_popularity"] = fetchSpotifyDatapoints($dp["spotify_platform_id"], $spotifyAccessToken);
    } else {
        $dp["spotify_popularity"] = null;
    }

    if(isset($song["youtube_platform_id"])){
        $dp["youtube_views"] = fetchYoutubeDatapoints($song["youtube_platform_id"]);
    } elseif(isset($dp["youtube_platform_id"])){
        $dp["youtube_views"] = fetchYoutubeDatapoints($dp["youtube_platform_id"]);
    } else {
        $dp["youtube_views"] = null;
    }

    $dp["fetch_date"] = $date;
    $dp["source"] = "https://chartex.com";
    $dp["song_id"] = $id;

    $db->insertDatapoint($dp);
}


//Качваме всичко в базата данни
foreach($dataPointsBG as $dp){


    $id = findSongIdForDatapointBG($db, $dp);



    $song = $db->findSongByTiktokIdBG($dp["tiktok_platform_id"]);

    if(isset($song["spotify_platform_id"])){
        $dp["spotify_popularity"] = fetchSpotifyDatapoints($song["spotify_platform_id"], $spotifyAccessToken);
    } elseif(isset($dp["spotify_platform_id"])){
        $dp["spotify_popularity"] = fetchSpotifyDatapoints($dp["spotify_platform_id"], $spotifyAccessToken);
    } else {
        $dp["spotify_popularity"] = null;
    }

    if(isset($song["youtube_platform_id"])){
        $dp["youtube_views"] = fetchYoutubeDatapoints($song["youtube_platform_id"]);
    } elseif(isset($dp["youtube_platform_id"])){
        $dp["youtube_views"] = fetchYoutubeDatapoints($dp["youtube_platform_id"]);
    } else {
        $dp["youtube_views"] = null;
    }

    $dp["fetch_date"] = $date;
    $dp["source"] = "https://chartex.com";
    $dp["song_id"] = $id;

    $db->insertDatapointBG($dp);
}

//Качваме всичко в базата данни
foreach($tiktokers as $tt){

    $tiktokerData = $db->findTikTokerById($tt["id"]);

    if($tiktokerData["thumbnail"] != null){
        $tt["thumbnail"] = $tiktokerData["thumbnail"];
    }

    $tt["fetch_date"] = $date;
    $db->insertTikTokerDatapoint($tt);

}

//Качваме всичко в базата данни
foreach($topvideos as $tt){
    $tt["fetch_date"] = $date;
    $db->insertTikTokVideo($tt);
}

echo "gotovo";