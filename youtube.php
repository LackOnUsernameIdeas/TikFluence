<?php

//1. Update the fetchYoutube() to retrieve the exact rows you need to update

//include 'databaseManager.class2.php';

// function connection(){
//   $dns = "mysql:host=localhost; dbname=songs";
//   $user = "root";
//   $password = "";

//   return $conn = new PDO($dns, $user, $password);

//   if(!$conn){
//       die("Connection failed");
//   }
// }

function getData(){
    $db = new DatabaseManager();
    $row = $db->fetchYoutube();
    print_r($row);
    // $sql_query = "SELECT youtube_views, id, youtube_platform_id FROM tiktok_songs WHERE id > 200";
    // $stmt = connection()->prepare($sql_query);
    // $stmt->execute();
  
    // $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
    foreach($row as $value){
  
        $videoId = $value["youtube_platform_id"];
        $accessToken = '';
    
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://youtube.googleapis.com/youtube/v3/videos?part=statistics&id=".$videoId."&key=AIzaSyDqUez1TEmLSgZAvIaMkWfsq9rSm0kDjIw",
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

        if ($err) {
            echo "cURL Error #:" . $err;
          } else {
            $data = json_decode($response, true);

            $viewCount = $data['items'][0]["statistics"]["viewCount"];
            $songId = $value["id"];
          }
          // echo $popularity . "<br>";
            $db->updateYoutubeViews($songId, $viewCount);
    }
}
getData();