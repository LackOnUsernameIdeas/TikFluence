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

// Генерираме си ключ за достъп до Spotify API
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

// Взимаме данните за най-гледаните видеа
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

// Взимаме id на потребител с потребителско име
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

// Взимаме данни за потребителя с id
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

// Взимаме sec_id на потребител с потребителско име
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

    if(isset($decoded["sec_uid"])){
        return $decoded["sec_uid"];
    } elseif(isset($decoded["message"])){
        return $decoded["message"];
    }

}

// Взимаме данни за потребителя със sec_id
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

// Взимаме данни за най-използваните хаштагове за последните 7 дни
function fetchTopHashtagsForTheLast7Days(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://ads.tiktok.com/creative_radar_api/v1/popular_trend/hashtag/list?period=7&page=1&limit=20&sort_by=popular");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'cookie: passport_csrf_token=ceacf96c15bb32dd5a53cab38d4a4309; passport_csrf_token_default=ceacf96c15bb32dd5a53cab38d4a4309; cookie-consent={%22ga%22:true%2C%22af%22:true%2C%22fbp%22:true%2C%22lip%22:true%2C%22bing%22:true%2C%22ttads%22:true%2C%22reddit%22:true%2C%22criteo%22:true%2C%22version%22:%22v9%22}; _abck=F5EE78E382E9D140EA5093E645401A37~-1~YAAQj6Auua799diGAQAAPHpA4QmwMJ5r+Uu29xmfEUnRE10aGirtDzQjQd7IPOhAIcKZpqUy/Pl7hJ5zM5tIDi7eVPxx/tFgXByTSihGJbeNHvkWhZQbkTnJl5d3fwjGxoh/QmTOPx8cbygS2pwiWsoW2bWYx3w2vraJZPSaUI0iUvcw4EbitFRpDFC31pp/WMG4gtVStrnL++c3RArS3FTj7uZLLWYQNWwXiOiJeBfCdlJmu1tQBmwHQnqKa0z3N+gGmxryuiIeEGB7Hyu8WSmYEKRJxMKPhHLKSHAzfmmC9C6SzlaReTXyWMazFkLVydHBYPuD8myNP9ZuDcCkU2BRffKozGWgNHfRGorgidfzci3IesZLK74K+oWqdZVtqBDUbVqSdM2zLQ==~-1~-1~-1; lang_type=en; pre_country=BG; part=stable; csrftoken=eHUtCzPzg8S1tv6P1ZRAzX0zOHGz0koP; tta_attr_id=0.1680384339.7217195782418481154; tta_attr_id_mirror=0.1680384339.7217195782418481154; MONITOR_WEB_ID=24211fa7-3ed4-49f9-b29c-920749c6ffda; tt_csrf_token=fRiAX9p6-0gKPIjEEoKhgXAnXjKC1AHVVmiM; s_v_web_id=verify_lfyhjzuf_T04kstdy_yijI_444f_9ZRP_PZGGJI7w8GBF; cmpl_token=AgQQAPPdF-RO0tM_bF0_-Bk5-fwEqolMv4QGYMjA_A; sid_guard=c13e224a3641f9d02237b6b84725de8d%7C1680384518%7C5184000%7CWed%2C+31-May-2023+21%3A28%3A38+GMT; uid_tt=55dd449b3c696273e36b0405da9b5ca48556a68a8bea9c90680c2c2f998e197d; uid_tt_ss=55dd449b3c696273e36b0405da9b5ca48556a68a8bea9c90680c2c2f998e197d; sid_tt=c13e224a3641f9d02237b6b84725de8d; sessionid=c13e224a3641f9d02237b6b84725de8d; sessionid_ss=c13e224a3641f9d02237b6b84725de8d; sid_ucp_v1=1.0.0-KDRiMzE2MGVhN2NjMDI4N2FmMDljZjc0NDA1MDhjYjQ1NGRhNDhlZGQKIAiAgKu-q9_ggAMQhsSioQYYswsgDDCMvfjZBTgBQOoHEAMaBm1hbGl2YSIgYzEzZTIyNGEzNjQxZjlkMDIyMzdiNmI4NDcyNWRlOGQ; ssid_ucp_v1=1.0.0-KDRiMzE2MGVhN2NjMDI4N2FmMDljZjc0NDA1MDhjYjQ1NGRhNDhlZGQKIAiAgKu-q9_ggAMQhsSioQYYswsgDDCMvfjZBTgBQOoHEAMaBm1hbGl2YSIgYzEzZTIyNGEzNjQxZjlkMDIyMzdiNmI4NDcyNWRlOGQ; store-idc=useast2a; store-country-code=bg; store-country-code-src=uid; tt-target-idc=useast2a; tt-target-idc-sign=qnh2FDyU02NDt8Qym4gqGC1UfIT_sCZ1BogirdV6v0abDiyo-RUI2YY9wNcBGU_jXnQw5Zttg3Raofr3v2xWA9Wvh6a7edQU2P_IVPBzC_amAU-GDE6hUBkCwAUhAM4pjQ17dwMHtO_XYJhCHi87zjWZmctig7UQ31JaN-2jt0bIAa9NowwLUlVCnuO1RwjsSb4p_SH9A6dWsQNmQhqtJZAYWJ5ICpnKZ-v7dVVZPG2zUGAAjjZaCYqdPVqNsfi8M1Tp3k-pdMEHHGy0Hd_t-sF1vTJY-0MF2pXA9ilv-VIk8trE8kdIPe6NuxjsD3nl5Ov3JFiYkyH6CbN1JD3eR6UbKQK9L7eDxPXMkxvYGwsObfhZqJCAkN0U8g_MKcErZ8lJITkBjNDBQx5ZQkWEC6TN7WY_LZePdMbz9Gmwqa9p_DwqfebJHVc8F5G8KfEbVrobNSBBHgKl6YTmn-_CfIQkW2weSrktE1cCj_26aqjVWsiOtXUr5uxZ_6N8CFuf; s_v_web_id=verify_lfyhjzuf_T04kstdy_yijI_444f_9ZRP_PZGGJI7w8GBF; sso_uid_tt_ads=20e35b78f837b8350336e626a7a2608bbd05397ac0bdf2e6904372d683afec25; sso_uid_tt_ss_ads=20e35b78f837b8350336e626a7a2608bbd05397ac0bdf2e6904372d683afec25; toutiao_sso_user_ads=4f45ed4a5d55c73657ec5740c6822ab9; toutiao_sso_user_ss_ads=4f45ed4a5d55c73657ec5740c6822ab9; sid_ucp_sso_v1_ads=1.0.0-KDdkYzRiZTQ5ZTBlMjUzZDA3ZTM0NzI5MzgyZThhMzAxY2Q4MTBkODUKHwiBiJzc9dColGQQqcWioQYYzCQgDDCnxaKhBjgIQCkQAxoDc2cxIiA0ZjQ1ZWQ0YTVkNTVjNzM2NTdlYzU3NDBjNjgyMmFiOQ; ssid_ucp_sso_v1_ads=1.0.0-KDdkYzRiZTQ5ZTBlMjUzZDA3ZTM0NzI5MzgyZThhMzAxY2Q4MTBkODUKHwiBiJzc9dColGQQqcWioQYYzCQgDDCnxaKhBjgIQCkQAxoDc2cxIiA0ZjQ1ZWQ0YTVkNTVjNzM2NTdlYzU3NDBjNjgyMmFiOQ; odin_tt=0cc534d0b51612127dd803b8c6b5a6222ca1b978f8f54b46436b4d0e93f780711cb00b1a30a64011b114391f5fc6965c79f2b462983b973855e5b49bb592b459; passport_auth_status_ads=48f510e5ad62c9e4243e5c67e1478431%2C84c90a7b2c3d3779e8439b32ea330df5; passport_auth_status_ss_ads=48f510e5ad62c9e4243e5c67e1478431%2C84c90a7b2c3d3779e8439b32ea330df5; sid_guard_ads=732619740aab68ba649a2bf929a8d775%7C1680384681%7C5183999%7CWed%2C+31-May-2023+21%3A31%3A20+GMT; uid_tt_ads=73f8dbb8d96a9813b3a086043c189d597d4a4d4c5bf95da92b048ab0d20802b3; uid_tt_ss_ads=73f8dbb8d96a9813b3a086043c189d597d4a4d4c5bf95da92b048ab0d20802b3; sid_tt_ads=732619740aab68ba649a2bf929a8d775; sessionid_ads=732619740aab68ba649a2bf929a8d775; sessionid_ss_ads=732619740aab68ba649a2bf929a8d775; sid_ucp_v1_ads=1.0.0-KGJiMGZiNmE1YWE5NmEwODA5MTRiMmJlYmY4YjZkMWFjMTMzNDhjZTcKGQiBiJzc9dColGQQqcWioQYYzCQgDDgIQCkQAxoDc2cxIiA3MzI2MTk3NDBhYWI2OGJhNjQ5YTJiZjkyOWE4ZDc3NQ; ssid_ucp_v1_ads=1.0.0-KGJiMGZiNmE1YWE5NmEwODA5MTRiMmJlYmY4YjZkMWFjMTMzNDhjZTcKGQiBiJzc9dColGQQqcWioQYYzCQgDDgIQCkQAxoDc2cxIiA3MzI2MTk3NDBhYWI2OGJhNjQ5YTJiZjkyOWE4ZDc3NQ; ac_csrftoken=ee4efb54309945cd8779cd6c211c9896; ttwid=1%7CCmBkMU0aApJ4ziBTALVQrQrcmGCOK-woNyFpAWMcPzw%7C1680384685%7C3cfa4b728bfeb6085d31b5237ba72310a332da29c52a54870146d402d89fe1e7; x-creative-csrf-token=2P7cceU2-85FUDGVmfvFqrwEom548YSUGWmU; msToken=J74x-3dCwbCqgDg7qdDqUxmbU9ia3lKvn_EIGlVss7HRIDeDlYILtjJKHhxqlsaLWkFp292y0E73gWVqnkdJP08rfqJLhT81lSyao4FaBitQzd2Xr3bW9ZYBZv1-_boeCulcxwE='
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    if(isset($decoded["data"]["list"])){
        return $decoded["data"]["list"];
    } else {
        return false;
    }
}

// Взимаме данни за най-използваните хаштагове за последните 120 дни
function fetchTopHashtagsForTheLast120Days(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://ads.tiktok.com/creative_radar_api/v1/popular_trend/hashtag/list?period=120&page=1&limit=20&sort_by=popular");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'cookie: passport_csrf_token=ceacf96c15bb32dd5a53cab38d4a4309; passport_csrf_token_default=ceacf96c15bb32dd5a53cab38d4a4309; cookie-consent={%22ga%22:true%2C%22af%22:true%2C%22fbp%22:true%2C%22lip%22:true%2C%22bing%22:true%2C%22ttads%22:true%2C%22reddit%22:true%2C%22criteo%22:true%2C%22version%22:%22v9%22}; _abck=F5EE78E382E9D140EA5093E645401A37~-1~YAAQj6Auua799diGAQAAPHpA4QmwMJ5r+Uu29xmfEUnRE10aGirtDzQjQd7IPOhAIcKZpqUy/Pl7hJ5zM5tIDi7eVPxx/tFgXByTSihGJbeNHvkWhZQbkTnJl5d3fwjGxoh/QmTOPx8cbygS2pwiWsoW2bWYx3w2vraJZPSaUI0iUvcw4EbitFRpDFC31pp/WMG4gtVStrnL++c3RArS3FTj7uZLLWYQNWwXiOiJeBfCdlJmu1tQBmwHQnqKa0z3N+gGmxryuiIeEGB7Hyu8WSmYEKRJxMKPhHLKSHAzfmmC9C6SzlaReTXyWMazFkLVydHBYPuD8myNP9ZuDcCkU2BRffKozGWgNHfRGorgidfzci3IesZLK74K+oWqdZVtqBDUbVqSdM2zLQ==~-1~-1~-1; lang_type=en; pre_country=BG; part=stable; csrftoken=eHUtCzPzg8S1tv6P1ZRAzX0zOHGz0koP; tta_attr_id=0.1680384339.7217195782418481154; tta_attr_id_mirror=0.1680384339.7217195782418481154; MONITOR_WEB_ID=24211fa7-3ed4-49f9-b29c-920749c6ffda; tt_csrf_token=fRiAX9p6-0gKPIjEEoKhgXAnXjKC1AHVVmiM; s_v_web_id=verify_lfyhjzuf_T04kstdy_yijI_444f_9ZRP_PZGGJI7w8GBF; cmpl_token=AgQQAPPdF-RO0tM_bF0_-Bk5-fwEqolMv4QGYMjA_A; sid_guard=c13e224a3641f9d02237b6b84725de8d%7C1680384518%7C5184000%7CWed%2C+31-May-2023+21%3A28%3A38+GMT; uid_tt=55dd449b3c696273e36b0405da9b5ca48556a68a8bea9c90680c2c2f998e197d; uid_tt_ss=55dd449b3c696273e36b0405da9b5ca48556a68a8bea9c90680c2c2f998e197d; sid_tt=c13e224a3641f9d02237b6b84725de8d; sessionid=c13e224a3641f9d02237b6b84725de8d; sessionid_ss=c13e224a3641f9d02237b6b84725de8d; sid_ucp_v1=1.0.0-KDRiMzE2MGVhN2NjMDI4N2FmMDljZjc0NDA1MDhjYjQ1NGRhNDhlZGQKIAiAgKu-q9_ggAMQhsSioQYYswsgDDCMvfjZBTgBQOoHEAMaBm1hbGl2YSIgYzEzZTIyNGEzNjQxZjlkMDIyMzdiNmI4NDcyNWRlOGQ; ssid_ucp_v1=1.0.0-KDRiMzE2MGVhN2NjMDI4N2FmMDljZjc0NDA1MDhjYjQ1NGRhNDhlZGQKIAiAgKu-q9_ggAMQhsSioQYYswsgDDCMvfjZBTgBQOoHEAMaBm1hbGl2YSIgYzEzZTIyNGEzNjQxZjlkMDIyMzdiNmI4NDcyNWRlOGQ; store-idc=useast2a; store-country-code=bg; store-country-code-src=uid; tt-target-idc=useast2a; tt-target-idc-sign=qnh2FDyU02NDt8Qym4gqGC1UfIT_sCZ1BogirdV6v0abDiyo-RUI2YY9wNcBGU_jXnQw5Zttg3Raofr3v2xWA9Wvh6a7edQU2P_IVPBzC_amAU-GDE6hUBkCwAUhAM4pjQ17dwMHtO_XYJhCHi87zjWZmctig7UQ31JaN-2jt0bIAa9NowwLUlVCnuO1RwjsSb4p_SH9A6dWsQNmQhqtJZAYWJ5ICpnKZ-v7dVVZPG2zUGAAjjZaCYqdPVqNsfi8M1Tp3k-pdMEHHGy0Hd_t-sF1vTJY-0MF2pXA9ilv-VIk8trE8kdIPe6NuxjsD3nl5Ov3JFiYkyH6CbN1JD3eR6UbKQK9L7eDxPXMkxvYGwsObfhZqJCAkN0U8g_MKcErZ8lJITkBjNDBQx5ZQkWEC6TN7WY_LZePdMbz9Gmwqa9p_DwqfebJHVc8F5G8KfEbVrobNSBBHgKl6YTmn-_CfIQkW2weSrktE1cCj_26aqjVWsiOtXUr5uxZ_6N8CFuf; s_v_web_id=verify_lfyhjzuf_T04kstdy_yijI_444f_9ZRP_PZGGJI7w8GBF; sso_uid_tt_ads=20e35b78f837b8350336e626a7a2608bbd05397ac0bdf2e6904372d683afec25; sso_uid_tt_ss_ads=20e35b78f837b8350336e626a7a2608bbd05397ac0bdf2e6904372d683afec25; toutiao_sso_user_ads=4f45ed4a5d55c73657ec5740c6822ab9; toutiao_sso_user_ss_ads=4f45ed4a5d55c73657ec5740c6822ab9; sid_ucp_sso_v1_ads=1.0.0-KDdkYzRiZTQ5ZTBlMjUzZDA3ZTM0NzI5MzgyZThhMzAxY2Q4MTBkODUKHwiBiJzc9dColGQQqcWioQYYzCQgDDCnxaKhBjgIQCkQAxoDc2cxIiA0ZjQ1ZWQ0YTVkNTVjNzM2NTdlYzU3NDBjNjgyMmFiOQ; ssid_ucp_sso_v1_ads=1.0.0-KDdkYzRiZTQ5ZTBlMjUzZDA3ZTM0NzI5MzgyZThhMzAxY2Q4MTBkODUKHwiBiJzc9dColGQQqcWioQYYzCQgDDCnxaKhBjgIQCkQAxoDc2cxIiA0ZjQ1ZWQ0YTVkNTVjNzM2NTdlYzU3NDBjNjgyMmFiOQ; odin_tt=0cc534d0b51612127dd803b8c6b5a6222ca1b978f8f54b46436b4d0e93f780711cb00b1a30a64011b114391f5fc6965c79f2b462983b973855e5b49bb592b459; passport_auth_status_ads=48f510e5ad62c9e4243e5c67e1478431%2C84c90a7b2c3d3779e8439b32ea330df5; passport_auth_status_ss_ads=48f510e5ad62c9e4243e5c67e1478431%2C84c90a7b2c3d3779e8439b32ea330df5; sid_guard_ads=732619740aab68ba649a2bf929a8d775%7C1680384681%7C5183999%7CWed%2C+31-May-2023+21%3A31%3A20+GMT; uid_tt_ads=73f8dbb8d96a9813b3a086043c189d597d4a4d4c5bf95da92b048ab0d20802b3; uid_tt_ss_ads=73f8dbb8d96a9813b3a086043c189d597d4a4d4c5bf95da92b048ab0d20802b3; sid_tt_ads=732619740aab68ba649a2bf929a8d775; sessionid_ads=732619740aab68ba649a2bf929a8d775; sessionid_ss_ads=732619740aab68ba649a2bf929a8d775; sid_ucp_v1_ads=1.0.0-KGJiMGZiNmE1YWE5NmEwODA5MTRiMmJlYmY4YjZkMWFjMTMzNDhjZTcKGQiBiJzc9dColGQQqcWioQYYzCQgDDgIQCkQAxoDc2cxIiA3MzI2MTk3NDBhYWI2OGJhNjQ5YTJiZjkyOWE4ZDc3NQ; ssid_ucp_v1_ads=1.0.0-KGJiMGZiNmE1YWE5NmEwODA5MTRiMmJlYmY4YjZkMWFjMTMzNDhjZTcKGQiBiJzc9dColGQQqcWioQYYzCQgDDgIQCkQAxoDc2cxIiA3MzI2MTk3NDBhYWI2OGJhNjQ5YTJiZjkyOWE4ZDc3NQ; ac_csrftoken=ee4efb54309945cd8779cd6c211c9896; ttwid=1%7CCmBkMU0aApJ4ziBTALVQrQrcmGCOK-woNyFpAWMcPzw%7C1680384685%7C3cfa4b728bfeb6085d31b5237ba72310a332da29c52a54870146d402d89fe1e7; x-creative-csrf-token=2P7cceU2-85FUDGVmfvFqrwEom548YSUGWmU; msToken=J74x-3dCwbCqgDg7qdDqUxmbU9ia3lKvn_EIGlVss7HRIDeDlYILtjJKHhxqlsaLWkFp292y0E73gWVqnkdJP08rfqJLhT81lSyao4FaBitQzd2Xr3bW9ZYBZv1-_boeCulcxwE='
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $decoded = json_decode($resp, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["data"]["list"];
}

// Генерираме си ключ за достъп до TikTok API
function generateTikTokAccessToken($code){

    $client_key = 'awntkz3ma9o5eetl';
    $client_secret = '5fa3ec534215877a15f3fa444976b5b5';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://open-api.tiktok.com/oauth/access_token/?client_key=$client_key&client_secret=$client_secret&code=$code&grant_type=authorization_code");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

    $result = curl_exec($ch);
    $decoded = json_decode($result, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return isset($decoded["data"]["access_token"]) ? $decoded["data"] : false;

}

function refreshTikTokAccessToken($refreshToken){

    $client_key = 'awntkz3ma9o5eetl';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://open-api.tiktok.com/oauth/refresh_token/?client_key=$client_key&grant_type=refresh_token&refresh_token=$refreshToken");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

    $result = curl_exec($ch);
    $decoded = json_decode($result, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return isset($decoded["data"]["access_token"]) ? $decoded["data"]["access_token"] : false;

}

//Сдобиваме се с главната информация за потребителя
function getUserBasicData($accessToken){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://open.tiktokapis.com/v2/user/info/?fields=open_id,union_id,avatar_url,display_name,bio_description,is_verified,follower_count,following_count,likes_count,profile_deep_link");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        "Authorization: Bearer $accessToken"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $decoded = json_decode($result, true);

    return isset($decoded["data"]["user"]) ? $decoded["data"]["user"] : false;

}

//Взимаме информацията за видеата на потребителя
function getUserVideoData($accessToken){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://open.tiktokapis.com/v2/video/list/?fields=create_time,like_count,comment_count,share_count,view_count,cover_image_url,video_description,duration,title,embed_link");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"max_count": 20}'); 

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $decoded = json_decode($result, true);

    return isset($decoded["data"]["videos"]) ? $decoded["data"]["videos"] : false;

}

// Сдобиваме се с потребителско име от TikTok API
function generateTikTokUsername($profLink){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $profLink);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);


    return $result;

}

// Взимаме open id-то на потребителя със access token
function getUserOpenId($accessToken){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://open.tiktokapis.com/v2/user/info/?fields=open_id');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

    $headers = [
        "Authorization: $accessToken"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $decoded = json_decode($result, true);

    $error_message = curl_error($ch);
    if($error_message != ''){
        die($error_message);
    };

    curl_close($ch);

    return $decoded["data"]["user"]["open_id"];

}
