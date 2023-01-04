<?php

function fetchTiktokDatapoints(){

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

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    return $decoded["results"];
}

function fetchSpotifyDatapoints($spotify_id){
    
    $accessToken = 'BQDFlIWVK10SkLBMImdyLEjXS3BaSW2KXMCCrVFN0ulJ7poeuLv9qlYiMoTnCURdHHVkaAKMfwVW8jsGERqayO_h1E3ULMb-RmwSqD3U34AhhUo6QpmeY2LdRWa4dJAV3sNiKaAs8FN69Vt_BnztvLBbxYDWl74wJvDNY6a6w_m4UaRwgC4-zrq7iVlacxV7Qrihp1I0TsyZyYRu53NAsFOPJMW3FrE5TuFqAiRfWP3yI-0S_2XFOgLMHks3CYZqn9xsdqRambtBVg';
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.spotify.com/v1/tracks/$spotify_id",
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
        return null;
    }

    $data = json_decode($response, true);

    if(isset($data["error"])){
        var_dump($data["error"]);
        die();
    }

    return $data['popularity'];
}

function fetchYoutubeDatapoints($youtube_id){
    $accessToken = '';

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://youtube.googleapis.com/youtube/v3/videos?part=statistics&id=".$youtube_id."&key=AIzaSyDqUez1TEmLSgZAvIaMkWfsq9rSm0kDjIw",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $accessToken"
      )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err){
        return null;
    }

    $data = json_decode($response, true);

    if(isset($data["error"])){
        var_dump($data["error"]);
        die();
    }
        
    return $data['items'][0]["statistics"]["viewCount"];
}