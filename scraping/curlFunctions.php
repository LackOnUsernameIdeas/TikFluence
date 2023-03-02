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
        'cookie: passport_csrf_token=f8045416e24503c36a4471a7326d85f8; passport_csrf_token_default=f8045416e24503c36a4471a7326d85f8; cookie-consent={%22ga%22:true%2C%22af%22:true%2C%22fbp%22:true%2C%22lip%22:true%2C%22bing%22:true%2C%22ttads%22:true%2C%22reddit%22:true%2C%22criteo%22:true%2C%22version%22:%22v9%22}; i18n_redirected=en; _abck=499E3E103FAB873B199B3E0C2C640523~-1~YAAQiy7HFwjHYsqFAQAA9t41BAn6fRuidKfx8tNVeid8zER0uPiluyIELJpPHiKtZRqp5NC0XfSbA3h5k89RYsjaBOe7xhRE3ZfFUByZRpN9E7ZwkaB2tCp/I/ALOnazaeOQhlEchnHBf6iri7VNzW1tCEFtDrSxFH69Q7UK1OZlIPcM1qw309/GCR3266EXwxnDqR9X85eQ2ximdv75Vk4wKeB02gtxFNcBK3zrAzkLnxze9wC7O0zksATm5Rtborn17TgWhz+9ILs4YbLLMdQR/QeCE9g7g46SLQXKUVEzLiOZ+W8XOeW+3BskXoJ63Y5To5DDMcLK5PYlMtLINlDLx0tvRSP7KAH3cIAzOwia5XoDd2F2ZqSRtY0I1Vkd2jq0FoN8Z3D9OA==~-1~-1~-1; bm_sz=A84892BB035B77A9D65ECB58900F7EAB~YAAQiy7HFwnHYsqFAQAA9t41BBIzqrxX8rMLNsDdG1KZS/ltXjEelp2FyRkzL4NA7iXlhwsciqwjmpHucPIPWQvuLadmudU4FtmDr57HDI8R2sk/vnpC8lCDy2pstRcn9dpGuOwWX9HAGR7JsCVW8ksp2k2LefNE2W6xO8Jj/w8TPcm34MbGNetNhwIrD857iuTKWWTpmJAI+mufJVNSREZVxRthMqwGZc+OhrJ6VC5iZG0OdE0pOMmAHRRj1tT9plaCCbfNa1b2U52knUvEldbXh6+TaaoUsrdLLUDT0XcmRdc=~3617605~3553078; _ttp=2L3mHC3A4OuPzIwLJXNCthvGd1x; tt_csrf_token=crGtgmph-FlDdLetwKL0pkvNmYVIecPvcBV4; tt_chain_token=BliKZqI5IxOs+CiLUIPfsg==; ak_bmsc=0C040BDAE30D46828968856881C2622A~000000000000000000000000000000~YAAQUC0+F3OUvPeFAQAAKdyGBBIynwxPKnIcEYF/9i9ZQM/byUiKtYAcCvc3EazhEuRAH5M7JIpx3XpgtveNrPgNEnkjp+qKp4/F6PC5/QKNHJFrKqGu97tT3qALkE+HOHZL4moN1taeET8puE3VEc3wiXUmeYfSSbzZ9wUXpv+NcOTSdPGhJF6nrsk4hKUVHC2g8xx5nGr5yMv3t08NxZkhgdd2NcLAUKgZGsb6U2qiGLjhuiwC4/b2pK56zhVtGm29JW0L4mOGRphOmyglaGqZZf0gdcTApNtmay/nZRdvlMNM6UZBlgUTHTdQ9GeA8eyoOLq/pejHYPgqMI6g5u+Atu2rEUfKESvvqDivZAe+HEPy4jEQsIhG08nzR3xQwvAOITerJnGbviqscDSIketTlIS6VDNHARp27xh64KoPD4tmHLGYK6p+4Hd+krFmdv50iuxtFeuycyPb7bD/cxlwpDbWRG50JWIcPc13Dg==; _ga=GA1.1.376874686.1675114070; lang_type=en; MONITOR_WEB_ID=48c72bcb-6ac7-4e2f-9ba8-0f9c347d16a9; s_v_web_id=verify_ldjbo5nc_GPvDc12g_CBjN_4DCu_9OHz_Y7l5kU9BxSCl; s_v_web_id=verify_ldjca1s1_vD4Z1LMh_kVLk_4Wrx_8mkQ_tfuqw0RAVdtq; cmpl_token=IGNORE; sid_guard=db879b13076e3f8d63dc849956cef0d7%7C1675115118%7C5184000%7CFri%2C+31-Mar-2023+21%3A45%3A18+GMT; uid_tt=9185b2fe03ba4c915bf4b8e9c71e39f26ca4af9b83c8722c40f231abffbe1dcf; uid_tt_ss=9185b2fe03ba4c915bf4b8e9c71e39f26ca4af9b83c8722c40f231abffbe1dcf; sid_tt=db879b13076e3f8d63dc849956cef0d7; sessionid=db879b13076e3f8d63dc849956cef0d7; sessionid_ss=db879b13076e3f8d63dc849956cef0d7; sid_ucp_v1=1.0.0-KDFmYTQ5N2RhMWQ5NTE0NTVmYzFkZTBjMzIzMzMxNzBmODIyOGNiYjUKIAiGiKe00P2hvF0Q7vTgngYYswsgDDDskOLrBTgEQOoHEAMaBm1hbGl2YSIgZGI4NzliMTMwNzZlM2Y4ZDYzZGM4NDk5NTZjZWYwZDc; ssid_ucp_v1=1.0.0-KDFmYTQ5N2RhMWQ5NTE0NTVmYzFkZTBjMzIzMzMxNzBmODIyOGNiYjUKIAiGiKe00P2hvF0Q7vTgngYYswsgDDDskOLrBTgEQOoHEAMaBm1hbGl2YSIgZGI4NzliMTMwNzZlM2Y4ZDYzZGM4NDk5NTZjZWYwZDc; store-idc=maliva; store-country-code=bg; store-country-code-src=uid; tt-target-idc=useast1a; tt-target-idc-sign=tDjFRuaFQWBuZx_Y2ba-r7I4nVsMw-ZiszAPkav29uKcN-bxHvVUKTsRKEe0-ExxPeyfTvkEhW5Dwku58u0E5mZUFeKXJdOLG4jq5-BVUtVxwbuX2bwd0zawGT3107BrnzC6FWZptFvvvOikz8LO0HKlsEn_7MUp_aTZSBgZTb_62ctqg0X3UNu6climAmqnUqtk8wNJU-Us_4iJ4Th-H8t1MfvmWiYj-pUC2pOJdur-dyer5gQnqiSegTE798qJTwxZKZQ1j_TNy0feqDQP-2yO36de3IQNeHN1iaMIIKo_UvoVYCAJ3lUC1MAgfw3G4hy1wfjXGXWeLyNm0dspnbtn-Q_895fQwUh0VPRSzSiLY7boA355mT3-9YD-vOO4hD9Zd4nJ3rUvG5wAPR77wXAmw5RDfZ30BEepKUQr-Cf6HdEiAwcWYf7AieS6fvM4Gw40BUj8CZNPnSqwS9wngtrJR1GfLW0_JcQgK8RSbHo_SLCVCdlHJC6iq6vKl-6q; sso_uid_tt_ads=17ac809bc57df7bd51fb74a6bf21a4f2ab642ced9b547c44a0ae0c5bbd6b669b; sso_uid_tt_ss_ads=17ac809bc57df7bd51fb74a6bf21a4f2ab642ced9b547c44a0ae0c5bbd6b669b; toutiao_sso_user_ads=e30f1903510c3a3570adc029379f86c7; toutiao_sso_user_ss_ads=e30f1903510c3a3570adc029379f86c7; sid_ucp_sso_v1_ads=1.0.0-KGI5MTU5NjIzNmFlZmYzMjQ4NTZmODc0M2RiMTE4OWE3NmVhZjAyZGIKHwiBiK6OuIOR4mMQgfXgngYYzCQgDDC9iJGeBjgIQCkQARoDc2cxIiBlMzBmMTkwMzUxMGMzYTM1NzBhZGMwMjkzNzlmODZjNw; ssid_ucp_sso_v1_ads=1.0.0-KGI5MTU5NjIzNmFlZmYzMjQ4NTZmODc0M2RiMTE4OWE3NmVhZjAyZGIKHwiBiK6OuIOR4mMQgfXgngYYzCQgDDC9iJGeBjgIQCkQARoDc2cxIiBlMzBmMTkwMzUxMGMzYTM1NzBhZGMwMjkzNzlmODZjNw; msToken=liQHFr60mrOh-rl5g1X6Hv32haxPdzvQackeha6Rk97zCvuoO2lhd5acXL_9V7w1fQ5qeIyDHS5OaYhXdf-R24PRRWaD4sHed3FpMgRUuAXm3wK7T9nNj9QeTvBoM__EOPEW5eAi5Ra-lG0=; odin_tt=c41a484240cf3a5eedf7e7f47e111b9336966114aeb085edbc5d37e7f4c2ba1b5aab11a3c499efe887fc04894ae509d8077f2378b5eb574b60118bb7d06b7609; passport_auth_status_ads=3cb741488c0a4a12ed59cd738afc80f0%2C15b97486d1ad1071cd47387cce812f65; passport_auth_status_ss_ads=3cb741488c0a4a12ed59cd738afc80f0%2C15b97486d1ad1071cd47387cce812f65; sid_guard_ads=4def942185288aeae2ba15b11205bf66%7C1675115138%7C5183999%7CFri%2C+31-Mar-2023+21%3A45%3A37+GMT; uid_tt_ads=1600d9f75f34f3a922091bffa8f9e1283d14a4bd982c56404a8f00124a9a6a74; uid_tt_ss_ads=1600d9f75f34f3a922091bffa8f9e1283d14a4bd982c56404a8f00124a9a6a74; sid_tt_ads=4def942185288aeae2ba15b11205bf66; sessionid_ads=4def942185288aeae2ba15b11205bf66; sessionid_ss_ads=4def942185288aeae2ba15b11205bf66; sid_ucp_v1_ads=1.0.0-KDM3ODRkYjg3NDA5YmJkNDZkM2QyNzg3YTUwZjc4NjAxMWQxNjYwYmIKGQiBiK6OuIOR4mMQgvXgngYYzCQgDDgIQCkQARoDc2cxIiA0ZGVmOTQyMTg1Mjg4YWVhZTJiYTE1YjExMjA1YmY2Ng; ssid_ucp_v1_ads=1.0.0-KDM3ODRkYjg3NDA5YmJkNDZkM2QyNzg3YTUwZjc4NjAxMWQxNjYwYmIKGQiBiK6OuIOR4mMQgvXgngYYzCQgDDgIQCkQARoDc2cxIiA0ZGVmOTQyMTg1Mjg4YWVhZTJiYTE1YjExMjA1YmY2Ng; ac_csrftoken=41029d546d7240b2885a57eb057fa450; ttwid=1%7CvFYayYVt8T0HJiHiPf-070zB5RAGCNzTuJnmafqsR_Q%7C1675115758%7Cb31d84ad7379d2a9e994ebe16e07bf41ef55bb68b5ffb4dc7d1136f5224ca2a2; _ga_QQM0HPKD40=GS1.1.1675114069.1.1.1675115761.0.0.0; msToken=s4B_7cyeHJjMhVSSEV8HoiiYGa0r7xVWYocmt-meayZvy7vq5Fklupz0FJVefhGeLuUHlHEaj5DzMtyWhGzGovy0djvmZDaIhifwURx0_CE257iLTbFx8YJInonCn-eeAuiz4GehmCCThQE='
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

// Взимаме данни за най-използваните хаштагове за последните 120 дни
function fetchTopHashtagsForTheLast120Days(){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://ads.tiktok.com/creative_radar_api/v1/popular_trend/hashtag/list?period=120&page=1&limit=20&sort_by=popular");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'cookie: passport_csrf_token=f8045416e24503c36a4471a7326d85f8; passport_csrf_token_default=f8045416e24503c36a4471a7326d85f8; cookie-consent={%22ga%22:true%2C%22af%22:true%2C%22fbp%22:true%2C%22lip%22:true%2C%22bing%22:true%2C%22ttads%22:true%2C%22reddit%22:true%2C%22criteo%22:true%2C%22version%22:%22v9%22}; i18n_redirected=en; _ttp=2L3mHC3A4OuPzIwLJXNCthvGd1x; _ga=GA1.1.376874686.1675114070; MONITOR_WEB_ID=48c72bcb-6ac7-4e2f-9ba8-0f9c347d16a9; cmpl_token=IGNORE; sid_guard=db879b13076e3f8d63dc849956cef0d7%7C1675115118%7C5184000%7CFri%2C+31-Mar-2023+21%3A45%3A18+GMT; uid_tt=9185b2fe03ba4c915bf4b8e9c71e39f26ca4af9b83c8722c40f231abffbe1dcf; uid_tt_ss=9185b2fe03ba4c915bf4b8e9c71e39f26ca4af9b83c8722c40f231abffbe1dcf; sid_tt=db879b13076e3f8d63dc849956cef0d7; sessionid=db879b13076e3f8d63dc849956cef0d7; sessionid_ss=db879b13076e3f8d63dc849956cef0d7; sid_ucp_v1=1.0.0-KDFmYTQ5N2RhMWQ5NTE0NTVmYzFkZTBjMzIzMzMxNzBmODIyOGNiYjUKIAiGiKe00P2hvF0Q7vTgngYYswsgDDDskOLrBTgEQOoHEAMaBm1hbGl2YSIgZGI4NzliMTMwNzZlM2Y4ZDYzZGM4NDk5NTZjZWYwZDc; ssid_ucp_v1=1.0.0-KDFmYTQ5N2RhMWQ5NTE0NTVmYzFkZTBjMzIzMzMxNzBmODIyOGNiYjUKIAiGiKe00P2hvF0Q7vTgngYYswsgDDDskOLrBTgEQOoHEAMaBm1hbGl2YSIgZGI4NzliMTMwNzZlM2Y4ZDYzZGM4NDk5NTZjZWYwZDc; store-idc=maliva; store-country-code=bg; store-country-code-src=uid; tt-target-idc=useast1a; tt-target-idc-sign=tDjFRuaFQWBuZx_Y2ba-r7I4nVsMw-ZiszAPkav29uKcN-bxHvVUKTsRKEe0-ExxPeyfTvkEhW5Dwku58u0E5mZUFeKXJdOLG4jq5-BVUtVxwbuX2bwd0zawGT3107BrnzC6FWZptFvvvOikz8LO0HKlsEn_7MUp_aTZSBgZTb_62ctqg0X3UNu6climAmqnUqtk8wNJU-Us_4iJ4Th-H8t1MfvmWiYj-pUC2pOJdur-dyer5gQnqiSegTE798qJTwxZKZQ1j_TNy0feqDQP-2yO36de3IQNeHN1iaMIIKo_UvoVYCAJ3lUC1MAgfw3G4hy1wfjXGXWeLyNm0dspnbtn-Q_895fQwUh0VPRSzSiLY7boA355mT3-9YD-vOO4hD9Zd4nJ3rUvG5wAPR77wXAmw5RDfZ30BEepKUQr-Cf6HdEiAwcWYf7AieS6fvM4Gw40BUj8CZNPnSqwS9wngtrJR1GfLW0_JcQgK8RSbHo_SLCVCdlHJC6iq6vKl-6q; sso_uid_tt_ads=17ac809bc57df7bd51fb74a6bf21a4f2ab642ced9b547c44a0ae0c5bbd6b669b; sso_uid_tt_ss_ads=17ac809bc57df7bd51fb74a6bf21a4f2ab642ced9b547c44a0ae0c5bbd6b669b; toutiao_sso_user_ads=e30f1903510c3a3570adc029379f86c7; toutiao_sso_user_ss_ads=e30f1903510c3a3570adc029379f86c7; sid_ucp_sso_v1_ads=1.0.0-KGI5MTU5NjIzNmFlZmYzMjQ4NTZmODc0M2RiMTE4OWE3NmVhZjAyZGIKHwiBiK6OuIOR4mMQgfXgngYYzCQgDDC9iJGeBjgIQCkQARoDc2cxIiBlMzBmMTkwMzUxMGMzYTM1NzBhZGMwMjkzNzlmODZjNw; ssid_ucp_sso_v1_ads=1.0.0-KGI5MTU5NjIzNmFlZmYzMjQ4NTZmODc0M2RiMTE4OWE3NmVhZjAyZGIKHwiBiK6OuIOR4mMQgfXgngYYzCQgDDC9iJGeBjgIQCkQARoDc2cxIiBlMzBmMTkwMzUxMGMzYTM1NzBhZGMwMjkzNzlmODZjNw; msToken=liQHFr60mrOh-rl5g1X6Hv32haxPdzvQackeha6Rk97zCvuoO2lhd5acXL_9V7w1fQ5qeIyDHS5OaYhXdf-R24PRRWaD4sHed3FpMgRUuAXm3wK7T9nNj9QeTvBoM__EOPEW5eAi5Ra-lG0=; passport_auth_status_ads=3cb741488c0a4a12ed59cd738afc80f0%2C15b97486d1ad1071cd47387cce812f65; passport_auth_status_ss_ads=3cb741488c0a4a12ed59cd738afc80f0%2C15b97486d1ad1071cd47387cce812f65; sid_guard_ads=4def942185288aeae2ba15b11205bf66%7C1675115138%7C5183999%7CFri%2C+31-Mar-2023+21%3A45%3A37+GMT; uid_tt_ads=1600d9f75f34f3a922091bffa8f9e1283d14a4bd982c56404a8f00124a9a6a74; uid_tt_ss_ads=1600d9f75f34f3a922091bffa8f9e1283d14a4bd982c56404a8f00124a9a6a74; sid_tt_ads=4def942185288aeae2ba15b11205bf66; sessionid_ads=4def942185288aeae2ba15b11205bf66; sessionid_ss_ads=4def942185288aeae2ba15b11205bf66; sid_ucp_v1_ads=1.0.0-KDM3ODRkYjg3NDA5YmJkNDZkM2QyNzg3YTUwZjc4NjAxMWQxNjYwYmIKGQiBiK6OuIOR4mMQgvXgngYYzCQgDDgIQCkQARoDc2cxIiA0ZGVmOTQyMTg1Mjg4YWVhZTJiYTE1YjExMjA1YmY2Ng; ssid_ucp_v1_ads=1.0.0-KDM3ODRkYjg3NDA5YmJkNDZkM2QyNzg3YTUwZjc4NjAxMWQxNjYwYmIKGQiBiK6OuIOR4mMQgvXgngYYzCQgDDgIQCkQARoDc2cxIiA0ZGVmOTQyMTg1Mjg4YWVhZTJiYTE1YjExMjA1YmY2Ng; lang_type=en; tt_csrf_token=Wr18CFBb-uwmVcsli9LuK88cbxqWfMhAN3DA; tt_chain_token=BliKZqI5IxOs+CiLUIPfsg==; bm_sz=AF144BFBA129F5C92460E3FDB93433B5~YAAQrWbXFw4hnRCGAQAArd2JExJiMvRrN6WDMy+w6Wg05S8GBZzlzlMJVB/SFH/dZAA9X2M4Bu5OJv/h+9hHgg2eskMU8kHgK0Zpyx4k6r8e1oQpu7RUJw8d2/BlLYsxM+SwT1Mde0Nc3SWg3mis0BdPzMpWUi+UyZmehYxnSfhk46zHwkVCaiwF0yqsMsLug1lOVlkVQ3krUiBUz0387Jd42W+P5rymM9IUHEnGVjpV/qyewYYrWRfMkkfzUjKPhatgJ6QV84oE5FqFrcWXozz8fNQRdoX1DmHW+JfO0N4Kido=~3355974~4535107; _abck=499E3E103FAB873B199B3E0C2C640523~0~YAAQhJoXAqYBWsqFAQAAa63iEwld9hkqyGCxTDwiZwPcKvs+3wV4eIswQv2j7ejSeKy9d5Ye2tGkJwt+kcXPfQGlqGUVsUG7oYVkd8YsA7vltAI7gqeBI4QyDr5ZTkTvYXjtbesZnHTL5YwSHqhFqkQYOZ0tPOsJGRFiuw5Xma6duDxHFTucCXUBGhflgE1d7LLjUFuq4OuWu6TBAF4J/tuCy+Oj2MI4n0JJ0R4aQRsOSj4FFCFHKy91qxWePETTugiKIuNUodt5vrvW+9Q/p6DQ61XAKWsraC4Pp4bYlUZ1e5dZxyAl0kNIu+oJ7LjxaqjkyY0omInO/ZQFCM40RTy5Kz8WnmmKR24ajPoiK1o+riRrJqFbYgR2RoYa4jCtAnO3emzJyVychVke845xSCbtGANh1Enp~-1~||1-nEUKTktHaN-1-10-1000-2||~-1; odin_tt=a3f85b344be0d16617dc5b83de8145dae97fc8f304dbf2d78bdda781f3987f4c06087b80cb121a520ce6650c32f1f57458106f276743c2fd0075867f529053913746b3dd1014439d5aa33c777af7e025; ak_bmsc=C608B8B50093D06E69C3025D34848294~000000000000000000000000000000~YAAQhpoXAnDKN86FAQAAA+8hFBJMQRZhtrAgLxlUAABAYjzxF4GFntiWrR4k68N+CPToFLLbA93aoIpu8LxC4TC2oCMtradg/6QXTSiW7aKlDqZVImK2tbFnxA3OUzo7KHm5aq4BKjvoNlRtutMvv4YPew1VRvwqC69HHgWSks0mG9MawOFdd01kLFCdybtUtlCz4uO+BA8IClW1AbD8CVwpyf9rJhWIJZbtdSSeS7Lxxt/7v7gbQShDHKpvfew0fLhuLPJUI6WkaR1OObXleeFH27goUlk4oDa3NjExG3UpghoLhhCJsclnK1cjItAKWXjLvc8kXz8dwWkTm+wdIf9uJ/WS+uxVJ5J9ZHh/hdaUUYsjkVH+iSTS75PeeVtveKAnVAR2g1GkzcI=; ttwid=1%7CvFYayYVt8T0HJiHiPf-070zB5RAGCNzTuJnmafqsR_Q%7C1675375059%7Cc1f876f37bcf1a9cd186d5c7331121d5e8eb729ce0513263f1f86d16eadf3f15; _ga_QQM0HPKD40=GS1.1.1675375031.3.1.1675375066.0.0.0; msToken=TiLY0LJ768Oh5qJ6KhIm3NJpREE4NYJHFSjUTA8YpQ3vC1zVS1pQSipj9WMh_Dc2KJIzzEAaDnkXdwE2GZBEIGf6a5DsZxt-QTlMh50X400RSYwjEikMjegVWdZiIre7Y05MXM_eaIXLHu4='
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

    return isset($decoded["data"]["access_token"]) ? $decoded["data"]["access_token"] : false;

}

// Генерираме си redirect link за да вземем потребителско име от TikTok API
// function generateTikTokRedirectLink($profLink){

//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $profLink);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//     $result = curl_exec($ch);

//     return $result;

// }

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
