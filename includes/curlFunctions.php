<?php

function fetchTiktokDatapoints(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_songs/?pageSize=200&ordering=-number_videos");
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

function fetchTiktokDatapointsBG(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_songs/?pageSize=200&ordering=-number_videos&nationality=481");
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
    
    $accessToken = 'BQA9o8TJfpurQWdmD94wWHDY7VMq55a53Y83nGBSRszFVftJFX_PtGFYZGY0IMvaN2s2VRr4_PnDFwkAaLwkFF0Vt2FLpOYZECG64MGj_D8uNyh-kTmdA9W6Pb8qVgV1Rj7vUb0EslgmkoKPuyFsXbweqcNp01Nq_vXE7iMYNKZSzHIzf4blq01-kFdti5R4YHYApnPxXjZ-eeIe72A3DxpXpYzZqpfwbQO4xRR-5gjW5vir6I_pi2uw4WmgXtSzQ69yGlvtKcWLBQ';
    
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

    return $data["popularity"];
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

function fetchTiktokTopUsers(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_top_users/?pageSize=200&ordering=-followers_count");
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

function fetchTiktokTopVideos(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_all_songs/?pageSize=200&ordering=-plays_count");
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