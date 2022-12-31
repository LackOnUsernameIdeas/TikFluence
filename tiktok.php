<?php

//1. Update the curlUrl and auth token to retrieve data

//include 'databaseManager.class2.php';
include 'spotify.php';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_songs/?pageSize=200&ordering=-videos_last_14days");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = [
    'authorization: Token 3fc2a8c4624b8f6ff94ee3ca5b8ba9fd335024d2f3ee76e3a812aed3a0c55690'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$resp = curl_exec($ch);
$decoded = json_decode($resp, true);

curl_close($ch);

if($e = curl_error($ch)){
    echo $e;
} else {
    $db = new DatabaseManager();
    // print_r($decoded);

    $date = date("Y-m-d");

    for($i=0; $i < count($decoded["results"]); $i++){
        $decoded["results"][$i]["fetch_date"] = $date;
        $decoded["results"][$i]["source"] = "https://chartex.com";

        $db->insertSong($decoded["results"][$i]);
    }
}
