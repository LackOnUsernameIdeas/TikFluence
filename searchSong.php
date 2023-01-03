<?php

//include 'databaseManager.class2.php';

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
$records = $db->fetchAllDataFromRecords();

//echo "<ol>";
foreach($records as $dp){
    $songId = findSongIdForDatapoint($db, $dp);
    // $db->insertIdForDataPoint([
    //     'song_id' => $songId
    // ]);
    //echo "<li><br>" . $songId . "</li>";
}
//echo "</ol>";