<?php

//1. Update the accessToken to retrieve data
//2. Update the fetchSpotify() to retrieve the exact rows you need to update

//include 'databaseManager.class2.php';

function getData(){
$db = new DatabaseManager();
$row = $db->fetchSpotify();
print_r($row);

  foreach($row as $value){

  $trackId = $value["spotify_platform_id"];
  $accessToken = 'BQA5MiIzZUA70cjkcOovaEbYx8KKeYw_qB7zkFH8nHcWoZ07XFNFXyZOUwDUH54vh7JR29upnAgREHYieYHMAmdkKc5Dk61olBHwD5zz9opr3jUXbxCSErOm8kOieytzHQwaOb4YQ5aT5cnc-cXjo-jpNskOZRKjjjdfBITM_EllqcuGtlPAjhPv4jcTEO4BoDNo-pOO0ooohtbsUUT1jKMJ6RAzJPdqTT_KiDkIfWM_VL4N-1JhxLAnGMrhejZxAeJG--_qTJTr9w';

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