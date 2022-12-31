<?php

include 'databaseManager.class2.php';

function findSongIdForDatapoint($dp){

        $db = new DatabaseManager();

        $tiktok_platform_id = $dp['tiktok_platform_id'];
        $songForThisDP = $db->findSongById($tiktok_platform_id);

        if($songForThisDP) {
            return $songForThisDP[0]['tiktok_platform_id'];
        } else {
            $db->createSong([
                'song_name' => $dp['song_name'],
                'artist_name' => $dp['artist_name'],
                'tiktok_platform_id' => $dp['tiktok_platform_id'],
                'spotify_platform_id' => $dp['spotify_platform_id'],
                'youtube_platform_id' => $dp['youtube_platform_id'],
                'itunes_platform_id' => $dp['itunes_platform_id'],
                'itunes_album_platform_id' => $dp['itunes_album_platform_id'],
                'song_guid' => $dp['song_guid']
            ]);
        }   
}

$db = new DatabaseManager();
$records = $db->fetchAllData();

//echo "<ol>";
foreach($records as $dp){
    $songId = findSongIdForDatapoint($dp);
    //echo "<li><br>" . $songId . "</li>";
}
//echo "</ol>";