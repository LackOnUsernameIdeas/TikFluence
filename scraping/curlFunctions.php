<?php

// Взимаме данните за TikTok глобално
function fetchTiktokDatapoints(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_songs/?pageSize=200&ordering=-videos_last_14days"); //за 14 дни
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'authorization: Token 3fc2a8c4624b8f6ff94ee3ca5b8ba9fd335024d2f3ee76e3a812aed3a0c55690' //за 14 дни
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };
    
    curl_close($ch);

    return $decoded["results"];
}

// Взимаме данните за TikTok за България
function fetchTiktokDatapointsBG(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://chartex.com/api/tiktok_songs/?pageSize=200&ordering=-videos_last_14days&nationality=481"); //за 14 дни    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'authorization: Token 3fc2a8c4624b8f6ff94ee3ca5b8ba9fd335024d2f3ee76e3a812aed3a0c55690' //за 14 дни
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["results"];
}

function generateSpotifyToken(){

    $client_id = '011ae0a5a92045b38edf6051aeb21370';
    $client_secret = 'f3d26c4f8b9d49cb96ea6561a5e5bf2d';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_POST, 1 );
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials' );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode($client_id.':'.$client_secret)));

    $result=curl_exec($ch);
    return json_decode($result)->access_token;

}

// Взимаме данните за Spotify
function fetchSpotifyDatapoints($spotify_id, $accessToken){

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

// Взимаме данните за YouTube
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

// Взимаме данните за най-известните тиктокъри
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

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["results"];
}

// Взимаме данните за най-гледаните видея
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

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["results"];
}

function fetchTikTokUserId($username){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://countik.com/api/exist/$username");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["id"];
}

function fetchTikTokUserData($id){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://countik.com/api/userinfo/$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded;
}

function fetchTikTokUserSecUid($username){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://countik.com/api/exist/$username");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["sec_uid"];
}

function fetchTikTokUserMoreDescriptiveData($sec_uid){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://countik.com/api/analyze/?sec_user_id=$sec_uid");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded;
}