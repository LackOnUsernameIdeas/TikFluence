<?php

//Създаваме class, с помощта на който ще взаимодействаме с базата данни

/**
 * Class DB
 *
 * @property PDO $pdo
 */
class DatabaseManager {

    //Създаваме връзка с базата данни
    public function __construct(){
    	include("config.php");

		try {
			$this->pdo = new PDO('mysql:host='.$dbopts['db_host'].';port='.$dbopts['db_port'].';dbname='.$dbopts['db_name'], $dbopts['db_user'], $dbopts['db_pass']);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->exec("set names utf8");
		} catch (PDOException $e) {
			$this->pdo = null;
			die($e->getMessage());
		}
	}

    //Дърпаме информацията от Spotify за днешните топ 200
    public function fetchSpotify(){
        $sql = "SELECT spotify_platform_id, spotify_popularity, id, fetch_date 
                FROM tiktok_records 
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме информацията от YouTube за днешните топ 200
    public function fetchYoutube(){
        $sql = "SELECT youtube_views, id, youtube_platform_id, fetch_date 
                FROM tiktok_records 
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме информацията от таблицата tiktok_records(записите за всяка песен)
    public function fetchAllDataFromRecords(){
        $sql = "SELECT * 
                FROM tiktok_records";

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $songs = $query->fetchAll(PDO::FETCH_ASSOC);

        return $songs;
    }

    //Дърпаме информацията от таблицата tiktok_songs(всяка песен)
    public function fetchAllDataFromSongs(){
        $sql = "SELECT * 
                FROM tiktok_songs";

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $songs = $query->fetchAll(PDO::FETCH_ASSOC);

        return $songs;
    }

    //Дърпаме информацията за дадена песен за днес и за вчера
    public function getTodayYesterdayData($sid, $date){
        $sql = "SELECT * 
                FROM tiktok_records 
                WHERE DATE(`fetch_date`) >=
                IF(
                    (SELECT COUNT(*) FROM tiktok_records WHERE DATE(`fetch_date`) = ADDDATE(DATE(:date), INTERVAL -1 DAY) AND song_id = :sth) > 0,
                    ADDDATE(DATE(:date), INTERVAL -1 DAY),
                    (SELECT MAX(DATE(`fetch_date`)) FROM tiktok_records WHERE DATE(`fetch_date`) < DATE(:date) AND song_id = :sth)
                ) 
                AND song_id=:sth";
                

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sth', $sid);
        $query->bindValue('date', $date);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме информацията за дадена песен за днес и за вчера(за България)
    public function getTodayYesterdayDataBG($sid, $date){
        $sql = "SELECT * 
                FROM tiktok_records_bulgaria 
                WHERE DATE(`fetch_date`) >=
                IF(
                    (SELECT COUNT(*) FROM tiktok_records WHERE DATE(`fetch_date`) = ADDDATE(DATE(:date), INTERVAL -1 DAY) AND song_id = :sth) > 0,
                    ADDDATE(DATE(:date), INTERVAL -1 DAY),
                    (SELECT MAX(DATE(`fetch_date`)) FROM tiktok_records WHERE DATE(`fetch_date`) < DATE(:date) AND song_id = :sth)
                ) 
                AND song_id=:sth";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sth', $sid);
        $query->bindValue('date', $date);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме записите до определена дата за дадена песен
    public function getDatapointsForSong($sid, $date){
        $sql = "SELECT * 
                FROM `tiktok_records`
                WHERE song_id=:sid 
                AND `fetch_date` 
                BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) 
                AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме записите на дадена повлияна песен
    public function getDatapointsForInfluencedSong($sid){
        $sql = "SELECT * 
                FROM `tiktok_records`
                WHERE song_id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме записите до определена дата за дадена песен
    public function getDatapointsForSongBG($sid, $date){
        $sql = "SELECT * 
                FROM `tiktok_records_bulgaria`
                WHERE song_id=:sid AND `fetch_date` 
                BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) 
                AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички записи за дадена песен
    public function getEveryDatapointForSong($sid){
        $sql = "SELECT * 
                FROM `tiktok_records`
                WHERE song_id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме пийковите стойности за дадена песен
    public function getPeaks($sid){
        $sql = "SELECT MAX(`number_of_videos_last_14days`), MAX(`spotify_popularity`), song_id
                FROM `tiktok_records`
                WHERE song_id=:song_id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();
        $result_array = $query->fetch();

        return $result_array;
    }

    //Взимаме датата на пийк на дадена песен с помощта на пийковата и стойност в TikTok
    public function findSongByPeakTT($sid, $peak){
        $sql = "SELECT song_id, fetch_date 
                FROM tiktok_records 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE song_id=:song_id AND number_of_videos_last_14days =:peak";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);
        $query->bindValue('peak', $peak);

        $query->execute();

        $result_array = $query->fetch();

        return $result_array;
    }

    //Взимаме датата на пийк на дадена песен с помощта на пийковата и стойност в Spotify
    public function findSongByPeakSY($sid, $peak){
        $sql = "SELECT song_id, fetch_date 
                FROM tiktok_records 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE song_id=:song_id AND spotify_popularity =:peak";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);
        $query->bindValue('peak', $peak);

        $query->execute();

        $result_array = $query->fetch();

        return $result_array;
    }
    
    //Намираме песен по нейното id
    public function findSongById($sid){
        $sql = "SELECT * 
                FROM tiktok_songs 
                WHERE id=:song_id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    //Проверяваме дали дадена песен съществува по нейното id
    public function checkIfSongExists($sid){
        $sql = "SELECT *
                FROM influenced_songs
                WHERE song_id=:song_id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();

        $result = $query->fetch();

        return $result;
    }

    //Взимаме данните от пийковия запис на дадена песен като ги допълваме с тези от tiktok_songs за дадената песен
    public function findSongPeakDataTT($sid){
        $sql = "SELECT * 
                FROM tiktok_records 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE song_id=:song_id 
                GROUP BY `number_of_videos_last_14days`
                ORDER BY `number_of_videos_last_14days` DESC";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();

        $result = $query->fetch();

        return $result;
    }

    //Взимаме данните от пийковия запис на дадена песен като ги допълваме с тези от tiktok_songs за дадената песен
    public function findSongPeakDataSY($sid){
        $sql = "SELECT * 
                FROM tiktok_records 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE song_id=:song_id 
                GROUP BY `spotify_popularity`
                ORDER BY `spotify_popularity` DESC";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();

        $result = $query->fetch();

        return $result;
    }

    //Взимаме последния запис на дадена песен
    public function findSongLastSavedData($sid){
        $sql = "SELECT * 
                FROM tiktok_records 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE song_id=:song_id 
                ORDER BY `fetch_date` DESC";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();

        $result = $query->fetch();

        return $result;
    }

    //Намираме дадена песен и взимаме данните и от днес
    public function findSongAndSongsTodayDataById($sid){
        $sql = "SELECT * 
                FROM tiktok_songs 
                JOIN tiktok_records
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE tiktok_songs.id=:song_id
                ORDER BY fetch_date DESC";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('song_id', $sid);

        $query->execute();

        $result = $query->fetch();

        return $result;
    }

    //Взимаме всички дати, за които дадена песен има данни
    public function listDatesForCurrentSong($sid){
        $sql = "SELECT DISTINCT `fetch_date`  
                FROM `tiktok_records`
                WHERE song_id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме всички дати, за които дадена песен има данни
    public function listDatesForCurrentSongBG($sid){
        $sql = "SELECT DISTINCT `fetch_date`  
                FROM `tiktok_records_bulgaria`
                WHERE song_id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме всички дати, за които даден тиктокър има данни
    public function listDatesForCurrentTikToker($tid){
        $sql = "SELECT DISTINCT `fetch_date`  
                FROM `tiktokers`
                WHERE given_id=:id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('id', $tid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме всички дати, за които дадено видео има данни
    public function listDatesForCurrentVideo($vid){
        $sql = "SELECT DISTINCT `fetch_date`  
                FROM `tiktok_top_videos`
                WHERE user_id=:user_id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('user_id', $vid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за дадена песен
    public function getSongData($sid){
        $sql = "SELECT * 
                FROM `tiktok_songs`
                WHERE id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);

        $query->execute();
        $result_array = $query->fetch();

        if($result_array == false){
            $result_array = [];
        }

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за дадена песен
    public function getSongDataBG($sid){
        $sql = "SELECT * 
                FROM `tiktok_songs_bulgaria`
                WHERE id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);

        $query->execute();
        $result_array = $query->fetch(); //fetchAll(PDO::FETCH_ASSOC)

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за топ 200те песни
    public function listTop200Songs($date) {
        $sql = "SELECT * 
                FROM `tiktok_records` 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }
    
    //Дърпаме цялата информация за топ 200те песни
    public function listTop200SongsBG($date) {
        $sql = "SELECT * 
                FROM `tiktok_records_bulgaria` 
                JOIN tiktok_songs_bulgaria 
                ON tiktok_records_bulgaria.song_id = tiktok_songs_bulgaria.id
                WHERE DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за всички песни
    public function listSongs() {
        $sql = "SELECT * 
                FROM `tiktok_songs`";

        $query = $this->pdo->prepare($sql);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за всички повлияни песни
    public function listAffectedSongs() {
        $sql = "SELECT * 
                FROM `influenced_songs`
                ORDER BY `peaks_difference` DESC";

        $query = $this->pdo->prepare($sql);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за всички повлияни песни
    public function listAffectedSongsByDate($date) {
        $sql = "SELECT * 
                FROM `influenced_songs`
                WHERE DATE(`report_date`) <= DATE(:date)
                ORDER BY `peaks_difference` DESC";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за най-слушаната песен глобално
    public function listTheFirstSongGlobal($date) {
        $sql = "SELECT * 
                FROM tiktok_records 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE DATE(`fetch_date`) = DATE(:date) AND rank = 1";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetch();

        return $result_array;
    }

    //Дърпаме цялата информация за някои от първите песни
    public function listTopSongsGlobal($date) {
        $sql = "SELECT * FROM `tiktok_records` 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id 
                WHERE DATE(`fetch_date`) = DATE(:date)
                ORDER BY rank
                LIMIT 10";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за някои от първите песни
    public function listTopSongsBG($date) {
        $sql = "SELECT * FROM `tiktok_records_bulgaria` 
                JOIN tiktok_songs_bulgaria 
                ON tiktok_records_bulgaria.song_id = tiktok_songs_bulgaria.id 
                WHERE DATE(`fetch_date`) = DATE(:date)
                ORDER BY rank
                LIMIT 10";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички дати със записи
    public function listDatesSongs() {
        $sql = "SELECT DISTINCT `fetch_date` 
                FROM `tiktok_records`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички дати със записи
    public function listDatesSongsBG() {
        $sql = "SELECT DISTINCT `fetch_date` 
                FROM `tiktok_records_bulgaria`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички дати със записи
    public function listDatesTikTokers() {
        $sql = "SELECT DISTINCT `fetch_date` 
                FROM `tiktokers`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички дати със записи
    public function listDatesVideos() {
        $sql = "SELECT DISTINCT `fetch_date` 
                FROM `tiktok_top_videos`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }
    
    //Дърпаме всички дати със записи
    public function listDatesHashtagsAndSongsOnHomePage() {
        $sql = "SELECT DISTINCT `fetch_date` 
                FROM `tiktok_hashtags_7days`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Намираме песента по id
    public function findSongByTiktokId($sid){
        $sql = "SELECT * 
                FROM tiktok_songs 
                WHERE `tiktok_platform_id`=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    //Намираме песента по id
    public function findSongByTiktokIdBG($sid){
        $sql = "SELECT * 
                FROM tiktok_songs_bulgaria 
                WHERE `tiktok_platform_id`=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    //Намираме тиктокъра по id
    public function findTikTokerById($gid){
        $sql = "SELECT * 
                FROM tiktokers 
                WHERE `given_id`=:gid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('gid', $gid);
        $query->execute();

        $result = $query->fetch();

        return $result;
    }

    //Дърпаме цялата информация за най-следвания тиктокър
    public function listTheFirstTiktoker($date) {
        $sql = "SELECT * 
                FROM tiktokers
                WHERE DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetch();

        return $result_array;
    }

    //Дърпаме цялата информация за най-гледаното видео
    public function listTheFirstVideo($date) {
        $sql = "SELECT * 
                FROM tiktok_top_videos
                WHERE DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetch();

        return $result_array;
    }

    //Създаваме нова песен ако findSongByTiktokId не намери такава
    public function insertSongGlobal($object){
        $sql = "INSERT INTO `tiktok_songs` ( 
                    `song_name`, 
                    `artist_name`,
                    `tiktok_platform_id`,
                    `spotify_platform_id`, 
                    `youtube_platform_id`,
                    `itunes_platform_id`, 
                    `itunes_album_platform_id`,
                    `song_guid`
                ) VALUES (
                    :song_name, 
                    :artist_name,
                    :tiktok_platform_id,
                    :spotify_platform_id, 
                    :youtube_platform_id,
                    :itunes_platform_id, 
                    :itunes_album_platform_id,
                    :song_guid
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_name', $object['song_name']);
        $query->bindValue('artist_name', $object['artist_name']);
        $query->bindValue('tiktok_platform_id', $object['tiktok_platform_id']);
        $query->bindValue('spotify_platform_id', $object['spotify_platform_id']);
        $query->bindValue('youtube_platform_id', $object['youtube_platform_id']);
        $query->bindValue('itunes_platform_id', $object['itunes_platform_id']);
        $query->bindValue('itunes_album_platform_id', $object['itunes_album_platform_id']);
        $query->bindValue('song_guid', $object['song_guid']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Създаваме нова песен ако findSongByTiktokIdBG не намери такава
    public function insertSongBG($object){
        $sql = "INSERT INTO `tiktok_songs_bulgaria` ( 
                    `song_name`, 
                    `artist_name`,
                    `tiktok_platform_id`,
                    `spotify_platform_id`, 
                    `youtube_platform_id`,
                    `itunes_platform_id`, 
                    `itunes_album_platform_id`,
                    `song_guid`
                ) VALUES (
                    :song_name, 
                    :artist_name,
                    :tiktok_platform_id,
                    :spotify_platform_id, 
                    :youtube_platform_id,
                    :itunes_platform_id, 
                    :itunes_album_platform_id,
                    :song_guid
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_name', $object['song_name']);
        $query->bindValue('artist_name', $object['artist_name']);
        $query->bindValue('tiktok_platform_id', $object['tiktok_platform_id']);
        $query->bindValue('spotify_platform_id', $object['spotify_platform_id']);
        $query->bindValue('youtube_platform_id', $object['youtube_platform_id']);
        $query->bindValue('itunes_platform_id', $object['itunes_platform_id']);
        $query->bindValue('itunes_album_platform_id', $object['itunes_album_platform_id']);
        $query->bindValue('song_guid', $object['song_guid']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Добавяме id на дадена песен в колона с всички id-та в таблицата tiktok_records, за да има връзка между таблиците
    public function insertIdForDataPoint($object){
        $sql = "UPDATE `tiktok_records` 
                SET `song_id`=:song_id 
                WHERE `song_id` = NULL";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_id', $object['song_id']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Добавяме datapoint

    /**
     * @param array $object
     * @return string
     */
    public function insertDatapoint($object){
        $sql = "INSERT INTO `tiktok_records` (
                    `song_id`,
                    `rank`, 
                    `total_likes_count`, 
                    `number_of_videos`,
                    `number_of_videos_last_14days`,
                    `fetch_date`,
                    `source`,
                    `spotify_popularity`,
                    `youtube_views`
                ) VALUES (
                    :song_id,
                    :rank,  
                    :total_likes_count, 
                    :number_of_videos, 
                    :number_of_videos_last_14days, 
                    :fetch_date,
                    :source,
                    :spotify_popularity,
                    :youtube_views
				)";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_id', $object['song_id']);
        $query->bindValue('rank', $object['rank']);
        $query->bindValue('total_likes_count', $object['total_likes_count']);
		$query->bindValue('number_of_videos', $object['number_of_videos']);
		$query->bindValue('number_of_videos_last_14days', $object['number_of_videos_last_14days']);
		$query->bindValue('fetch_date', $object['fetch_date']);
		$query->bindValue('source', $object['source']);
		$query->bindValue('spotify_popularity', $object['spotify_popularity']);
        $query->bindValue('youtube_views', $object['youtube_views']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Добавяме datapoint
    public function insertDatapointBG($object){
        $sql = "INSERT INTO `tiktok_records_bulgaria` (
                    `song_id`,
                    `rank`, 
                    `total_likes_count`, 
                    `number_of_videos`,
                    `number_of_videos_last_14days`,
                    `fetch_date`,
                    `source`,
                    `spotify_popularity`,
                    `youtube_views`
                ) VALUES (
                    :song_id,
                    :rank,  
                    :total_likes_count, 
                    :number_of_videos, 
                    :number_of_videos_last_14days, 
                    :fetch_date,
                    :source,
                    :spotify_popularity,
                    :youtube_views
                    )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_id', $object['song_id']);
        $query->bindValue('rank', $object['rank']);
        $query->bindValue('total_likes_count', $object['total_likes_count']);
		$query->bindValue('number_of_videos', $object['number_of_videos']);
		$query->bindValue('number_of_videos_last_14days', $object['number_of_videos_last_14days']);
		$query->bindValue('fetch_date', $object['fetch_date']);
		$query->bindValue('source', $object['source']);
		$query->bindValue('spotify_popularity', $object['spotify_popularity']);
        $query->bindValue('youtube_views', $object['youtube_views']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }
    
    //Добавяме нов тиктокър
    public function insertTikTokerDatapoint($object){
        $sql = "INSERT INTO `tiktokers` ( 
                    `given_id`, 
                    `platform_name`,
                    `thumbnail`,
                    `tiktoker`, 
                    `nationality`,
                    `followers_count`, 
                    `followers_this_year`,
                    `fetch_date`
                ) VALUES (
                    :given_id, 
                    :platform_name,
                    :thumbnail,
                    :tiktoker, 
                    :nationality,
                    :followers_count, 
                    :followers_this_year,
                    :fetch_date
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('given_id', $object['id']);
        $query->bindValue('platform_name', $object['platform_name']);
        $query->bindValue('thumbnail', $object['thumbnail']);
        $query->bindValue('tiktoker', $object['name']);
        $query->bindValue('nationality', $object['nationality']);
        $query->bindValue('followers_count', $object['followers_count']);
        $query->bindValue('followers_this_year', $object['followers_this_year']);
        $query->bindValue('fetch_date', $object['fetch_date']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Добавяме ново видео
    public function insertTikTokVideo($object){
        $sql = "INSERT INTO `tiktok_top_videos` ( 
                    `user_id`, 
                    `video_url`,
                    `likes_count`,
                    `platform_name`, 
                    `tiktoker_thumbnail`,
                    `shares_count`, 
                    `plays_count`,
                    `song_name`,
                    `artist_name`,
                    `tiktok_platform_id`,
                    `fetch_date`
                ) VALUES (
                    :user_id, 
                    :video_url,
                    :likes_count,
                    :platform_name, 
                    :tiktoker_thumbnail,
                    :shares_count, 
                    :plays_count,
                    :song_name,
                    :artist_name,
                    :tiktok_platform_id,
                    :fetch_date
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('user_id', $object['id']);
        $query->bindValue('video_url', $object['video_url']);
        $query->bindValue('likes_count', $object['likes_count']);
        $query->bindValue('platform_name', $object['platform_name']);
        $query->bindValue('tiktoker_thumbnail', $object['tiktoker_thumbnail']);
        $query->bindValue('shares_count', $object['shares_count']);
        $query->bindValue('plays_count', $object['plays_count']);
        $query->bindValue('song_name', $object['song_name']);
        $query->bindValue('artist_name', $object['artist_name']);
        $query->bindValue('tiktok_platform_id', $object['tiktok_platform_id']);
        $query->bindValue('fetch_date', $object['fetch_date']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Качваме песните, на които се вижда ефекта от повлияване на TikTok
    public function insertInfluencedSong($object){
        $sql = "INSERT INTO `influenced_songs` ( 
                    `song_id`, 
                    `song_name`,
                    `artist_name`,
                    `tiktok_peak_date`, 
                    `spotify_peak_date`,
                    `peaks_difference`,
                    `report_date`
                ) VALUES (
                    :song_id, 
                    :song_name,
                    :artist_name,
                    :tiktok_peak_date, 
                    :spotify_peak_date,
                    :peaks_difference,
                    :report_date
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_id', $object['song_id']);
        $query->bindValue('song_name', $object['song_name']);
        $query->bindValue('artist_name', $object['artist_name']);
        $query->bindValue('tiktok_peak_date', $object['tiktok_peak_date']);
        $query->bindValue('spotify_peak_date', $object['spotify_peak_date']);
        $query->bindValue('peaks_difference', $object['peaks_difference']);
        $query->bindValue('report_date', $object['report_date']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }


    //Добавяме популярност в Spotify на нашите записи

	/**
     * @param array $object
     * @return string
     */
    public function updateSpotifyPopularity($id, $newPopularity){
        $sql = "UPDATE `tiktok_records` 
                SET `spotify_popularity`=:spotify_popularity 
                WHERE `id`=:id";

        $query = $this->pdo->prepare($sql);

        $query->bindValue("id", $id, PDO::PARAM_INT);
        $query->bindValue("spotify_popularity", $newPopularity, PDO::PARAM_INT);

        $query->execute();

		return ($query->rowCount() > 0 ? true : false);
    }

    //Добавяме гледания в YouTube на нашите записи

	/**
     * @param array $object
     * @return string
     */
    public function updateYoutubeViews($id, $newPopularity){
        $sql = "UPDATE `tiktok_records` 
                SET `youtube_views`=:youtube_views 
                WHERE `id`=:id";

        $query = $this->pdo->prepare($sql);

        $query->bindValue("id", $id, PDO::PARAM_INT);
        $query->bindValue("youtube_views", $newPopularity, PDO::PARAM_INT);

        $query->execute();

		return ($query->rowCount() > 0 ? true : false);
    }

    //Изтриваме предния регистър на песен, която е претърпяла промяна

    /**
     * @param $Code
     * @return false|array
     */
    public function deleteInfluencedSong($sid) {
        $sql = "DELETE FROM `influenced_songs` WHERE `song_id`=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->execute();

        return ($query->rowCount() > 0 ? true : false);
    }

    //Взимаме информацията за топ 200 тиктокъри за определена дата
    public function getTiktokersTodayData($date){
        $sql = "SELECT * 
                FROM `tiktokers` 
                WHERE DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме информацията за топ 10 тиктокъри за определена дата
    public function getTiktokersTodayDataTop($date){
        $sql = "SELECT * 
                FROM `tiktokers` 
                WHERE DATE(`fetch_date`) = DATE(:date) LIMIT 10";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме информацията за топ 200 най-гледани видеа за определена дата
    public function getTopVideosTodayData($date){
        $sql = "SELECT * 
                FROM `tiktok_top_videos` 
                WHERE DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Изчисляваме средната стойност спрямо всички данни за TikTok популярност на дадена песен 
    public function getAverageTT($sid, $date){
        $sql = "SELECT AVG(`number_of_videos_last_14days`)
                FROM `tiktok_records`
                WHERE song_id=:sid AND DATE(`fetch_date`) BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll();

        return count($result_array) > 0 ? $result_array : false;
    }

    //Изчисляваме средната стойност спрямо всички данни за YouTube популярност на дадена песен 
    public function getAverageYT($sid, $date){
        $sql = "SELECT AVG(`youtube_views`)
                FROM `tiktok_records`
                WHERE song_id=:sid AND DATE(`fetch_date`) BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll();

        return count($result_array) > 0 ? $result_array : false;
    }

    //Изчисляваме средната стойност спрямо всички данни за Spotify популярност на дадена песен 
    public function getAverageSY($sid, $date){
        $sql = "SELECT AVG(`spotify_popularity`)
                FROM `tiktok_records`
                WHERE song_id=:sid AND DATE(`fetch_date`) BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll();

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за топ 200 песни за днес и за вчера
    public function getTodayYesterdayGlobalData($sid, $date){
        $sql = "SELECT * 
                FROM tiktok_records
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id 
                WHERE DATE(`fetch_date`) >= ADDDATE(DATE(:date), INTERVAL -1 DAY) 
                AND song_id=:sid";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sid', $sid);
        $query->bindValue('date', $date);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме данните за дадено видео
    public function getVideosData($vid, $date){
        $sql = "SELECT * 
                FROM `tiktok_top_videos`
                WHERE user_id=:user_id AND `fetch_date` 
                BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) 
                AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('user_id', $vid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if($result_array == false) $result_array = [];
        
        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за дадено видео за определен ден
    public function getVideoDataForSpecificDate($vid, $date){
        $sql = "SELECT * 
                FROM `tiktok_top_videos`
                WHERE user_id=:user_id AND DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('user_id', $vid);
        $query->bindValue('date', $date);

        $query->execute();
        $result = $query->fetch();

        return $result;
    }

    //Дърпаме главната информация за даден тиктокър
    public function getTikTokerData($tid, $date){
        $sql = "SELECT * 
                FROM `tiktokers`
                WHERE given_id=:given_id AND DATE(`fetch_date`) <= DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('given_id', $tid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetch();

        if($result_array == false) $result_array = [];

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за даден тиктокър за определен ден
    public function getTikTokerDataForSpecificDate($tid, $date){
        $sql = "SELECT * 
                FROM `tiktokers`
                WHERE given_id=:given_id AND DATE(`fetch_date`) = DATE(:date)";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('given_id', $tid);
        $query->bindValue('date', $date);
        $query->execute();
        $result_array = $query->fetch();

        if($result_array == false) $result_array = [];

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички данни до определена дата за даден тиктокър
    public function getTikTokerDatapoints($tid, $date){
        $sql = "SELECT * 
                FROM `tiktokers`
                WHERE given_id=:given_id AND `fetch_date` 
                BETWEEN DATE_SUB(:date, INTERVAL 39 DAY) 
                AND :date";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('given_id', $tid);
        $query->bindValue('date', $date);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Качваме данните за най-използваните хаштагове за последните 7 дни
    public function insertHashtagForTheLast7Days($object){
        $sql = "INSERT INTO `tiktok_hashtags_7days` ( 
                    `rank`, 
                    `hashtag_name`,
                    `publish_cnt`,
                    `fetch_date`
                ) VALUES (
                    :rank, 
                    :hashtag_name,
                    :publish_cnt,
                    :fetch_date
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('rank', $object['rank']);
        $query->bindValue('hashtag_name', $object['hashtag_name']);
        $query->bindValue('publish_cnt', $object['publish_cnt']);
        $query->bindValue('fetch_date', $object['fetch_date']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Качваме данните за най-използваните хаштагове за последните 120 дни
    public function insertHashtagForTheLast120Days($object){
        $sql = "INSERT INTO `tiktok_hashtags_120days` ( 
                    `rank`, 
                    `hashtag_name`,
                    `publish_cnt`,
                    `fetch_date`
                ) VALUES (
                    :rank, 
                    :hashtag_name,
                    :publish_cnt,
                    :fetch_date
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('rank', $object['rank']);
        $query->bindValue('hashtag_name', $object['hashtag_name']);
        $query->bindValue('publish_cnt', $object['publish_cnt']);
        $query->bindValue('fetch_date', $object['fetch_date']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Взимаме данните за най-използваните хаштагове за последните 7 дни
    public function getHashtagsForTheLast7Days(){
        $sql = "SELECT * 
                FROM `tiktok_hashtags_7days`
                WHERE DATE(`fetch_date`) = DATE(NOW())
                ORDER BY `rank`
                LIMIT 10";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if($result_array == false) $result_array = [];

        return count($result_array) > 0 ? $result_array : false;
    }
    
    //Взимаме данните за най-използваните хаштагове за последните 120 дни
    public function getHashtagsForTheLast120Days(){
        $sql = "SELECT * 
                FROM `tiktok_hashtags_120days`
                WHERE DATE(`fetch_date`) = DATE(NOW())
                ORDER BY `rank`
                LIMIT 10";

        $query = $this->pdo->prepare($sql);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        if($result_array == false) $result_array = [];

        return count($result_array) > 0 ? $result_array : false;
    }
}