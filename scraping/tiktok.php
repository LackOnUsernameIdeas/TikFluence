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
$hashtagsForTheLast7Days = fetchTopHashtagsForTheLast7Days();
$hashtagsForTheLast120Days = fetchTopHashtagsForTheLast120Days();
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



//Запазване на повлияните песни в масива - songsWithDaysPositive
    function getPeaks($db){
        $songs = $db->listSongs();

        $peaks = [];
        foreach($songs as $sg){
            $peaks[] = $db->getPeaks($sg["id"]);
        }

        $peaksWithData = [];
        foreach($peaks as $pk){
            $peaksWithData[]["Spotify"] = $db->findSongByPeakSY($pk["song_id"], $pk["MAX(`spotify_popularity`)"]);
            $peaksWithData[]["TikTok"] = $db->findSongByPeakTT($pk["song_id"], $pk["MAX(`number_of_videos_last_14days`)"]);
        }


        return $peaksWithData;
    }


    $peaksWithData = getPeaks($db);

    $songsWithDaysPositive = [];
    $songsWithDaysNegative = [];

    for($i=0;$i<count($peaksWithData);$i+=2){

        $datediff = isset($peaksWithData[$i]["Spotify"]["fetch_date"]) ? 
        strtotime($peaksWithData[$i]["Spotify"]["fetch_date"]) - strtotime($peaksWithData[$i + 1]["TikTok"]["fetch_date"]) : false;

        if($datediff != false && $datediff > 0){
            $songsWithDaysPositive[$peaksWithData[$i]["Spotify"]["song_id"]] = $datediff / (60 * 60 * 24);
        } elseif($datediff < 0){
            $songsWithDaysNegative[$peaksWithData[$i]["Spotify"]["song_id"]] = $datediff / (60 * 60 * 24);
        }

    }

//Промени, които масива - songsWithDaysPositive претърпява, за да съдържа само данните на песните, които имат плато, по-малко от 10 дни
    arsort($songsWithDaysPositive);


    foreach($songsWithDaysPositive as $key => $value){
        $datapoints = $db->getEveryDatapointForSong($key);
        
        $ttNums = [];
        $dates = [];

        foreach($datapoints as $dp){
            $ttNums[] = $dp["number_of_videos_last_14days"];
            $dates[] = $dp["fetch_date"];
        }

        $plateauIndex = 0;
        $previousVal = 0;
        foreach($ttNums as $val){
            if($val == $previousVal){
                $plateauIndex++;
            } else {
                $plateauIndex = 0;
            }
            if($plateauIndex >= 10){
                unset($songsWithDaysPositive[$key]);
            } 
            $previousVal = $val;
        }
    }

//Как изглежда всеки индекс от масива - songsWithDaysPositive:
//[id на песен] => int(разликата на датите на пийковете в 2те платформи)


//Запазване на нужната информацията за повлияните песни в масива - influencedSongsData
    $influencedSongsData = [];

    foreach($songsWithDaysPositive as $songId => $days){

        $songData = $db->findSongById($songId)[0];

        array_push($influencedSongsData, [
            "song_id" => $songId,
            "song_name" => $songData["song_name"],
            "artist_name" => $songData["artist_name"],
            "tiktok_peak_date" => $db->findSongPeakDataTT($songId)["fetch_date"],
            "spotify_peak_date" => $db->findSongPeakDataSY($songId)["fetch_date"],
            "peaks_difference" => $days,
            "report_date" => $date
        ]);
    }

//Качваме песните в базата данни като записваме и за коя дата отговарят данните
    foreach($influencedSongsData as $is){

        $song = $db->checkIfSongExists($is["song_id"]);

        if($song == false || $song["peaks_difference"] != $is["peaks_difference"]){
            $db->deleteInfluencedSong($is["song_id"]);
            $db->insertInfluencedSong($is);
        }
        
    }

    
//Проверяваме дали има отрицателни стойности в разликите в пийковете на повлияните песни и ако има, изтриваме дадената песен, защото вече не я считаме за повлияна
    foreach($songsWithDaysNegative as $sid=>$datediff){

        $song = $db->checkIfSongExists($sid);

        if($song != false && $song["peaks_difference"] != $datediff){
            $db->deleteInfluencedSong($sid);
        }
    }

    
//Качваме данните за най-използваните хаштагове за последните 7 дни
    foreach($hashtagsForTheLast7Days as $hashtag){

        $db->insertHashtagForTheLast7Days([
            'hashtag_name' => $hashtag["hashtag_name"],
            'rank' => $hashtag["rank"],
            'publish_cnt' => $hashtag["publish_cnt"],
            'fetch_date' => $date
        ]);
    }

//Качваме данните за най-използваните хаштагове за последните 120 дни
    foreach($hashtagsForTheLast120Days as $hashtag){
        
        $db->insertHashtagForTheLast120Days([
            'rank' => $hashtag["rank"],
            'hashtag_name' => $hashtag["hashtag_name"],
            'publish_cnt' => $hashtag["publish_cnt"],
            'fetch_date' => $date
        ]);
    }

echo "gotovo";