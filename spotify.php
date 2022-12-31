<?php

//1. Update the accessToken to retrieve data
//2. Update the fetchSpotify() to retrieve the exact rows you need to update

//include 'databaseManager.class2.php';

function getData(){
$db = new DatabaseManager();
$row = $db->fetchSpotify();

  foreach($row as $value){

  $trackId = $value["spotify_platform_id"];
  $accessToken = 'BQCbyBuUEVME45Ka8MNR6Gri3MM-0eojdhwZZK7oymxLF-J2hMFHlyGyTa8V94grKahUs6P3hPtyFdSFoc1INmTYRLD_XQkxEOqL1prbR91YWFody9wdv7AlJu56ChksWfzHWfmZWSdbf2PEC6-C6ex3ZR2MjyoNQfyX1tI9oX-6DqP5VkCAUg9ZMb2xPjUjENW5bDpXDjGasKIwMTUjrtu6Nuveo5hlj2z2vOUltpPxkYwUJFg5XCJYALnJoKLCRaKv6tvdT4SDPw';

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.spotify.com/v1/tracks/$trackId",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer $accessToken"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    $data = json_decode($response, true);
    $popularity = $data['popularity'];
    $songId = $value["id"];
  }
    //echo $popularity . "<br>";
    $db->updateSpotifyPopularity($songId, $popularity);
  }

}
getData();