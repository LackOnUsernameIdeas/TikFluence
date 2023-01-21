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
    	$dbopts = [
    		'db_host' => 'localhost',
    		'db_name' => 'tikfluence',
    		'db_user' => 'root',
    		'db_pass' => '',
            'db_port' => 3306
    	];

		try {
			$this->pdo = new PDO('mysql:host='.$dbopts['db_host'].';port='.$dbopts['db_port'].';dbname='.$dbopts['db_name'], $dbopts['db_user'], $dbopts['db_pass']);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->exec("set names utf8");
		} catch (PDOException $e) {
			$this->pdo = null;
			die($e->getMessage());
		}
	}

    //Дърпаме информацията от Spotify
    public function fetchSpotify(){
        $sql = "SELECT spotify_platform_id, spotify_popularity, id, fetch_date 
                FROM tiktok_records 
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме информацията от YouTube
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

    //Дърпаме информацията за топ 200 песни за днес и за вчера
    public function getTodayYesterdayData($sid){
        $sql = "SELECT * 
                FROM tiktok_records 
                WHERE DATE(`fetch_date`) >= ADDDATE(DATE(CURRENT_TIMESTAMP()), INTERVAL -1 DAY) 
                AND song_id=:sth";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sth', $sid);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме информацията за топ 200 песни за днес и за вчера(за България)
    public function getTodayYesterdayDataBG($sid){
        $sql = "SELECT * 
                FROM tiktok_records_bulgaria 
                WHERE DATE(`fetch_date`) >= ADDDATE(DATE(CURRENT_TIMESTAMP()), INTERVAL -1 DAY) 
                AND song_id=:sth
        ";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('sth', $sid);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //Дърпаме всички записи за дадена песен
    public function getDatapointsForSong($sid){
        $sql = "SELECT * 
                FROM `tiktok_records`
                WHERE song_id=:mandja";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('mandja', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за дадена песен
    public function getSongData($sid){
        $sql = "SELECT * 
                FROM `tiktok_songs`
                WHERE id=:mandja";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('mandja', $sid);

        $query->execute();
        $result_array = $query->fetch();

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме информацията за дадена песен
    public function getSongDataBG($sid){
        $sql = "SELECT * 
                FROM `tiktok_songs_bulgaria`
                WHERE id=:mandja";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('mandja', $sid);

        $query->execute();
        $result_array = $query->fetch(); //fetchAll(PDO::FETCH_ASSOC)

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички записи за дадена песен(топ 200 за България)
    public function getDatapointsForSongBG($sid){
        $sql = "SELECT * 
                FROM `tiktok_records_bulgaria`
                WHERE song_id=:mandja";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('mandja', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за топ 200те песни
    public function listTop200Songs() {
        $sql = "SELECT * 
                FROM `tiktok_records` 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за топ 200те песни(за България)
    public function listTop200SongsBG() {
        $sql = "SELECT * 
                FROM `tiktok_records_bulgaria` 
                JOIN tiktok_songs_bulgaria 
                ON tiktok_records_bulgaria.song_id = tiktok_songs_bulgaria.id
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за някои от първите песни
    public function listTopSongs() {
        $sql = "SELECT * FROM `tiktok_records` 
                JOIN tiktok_songs 
                ON tiktok_records.song_id = tiktok_songs.id 
                WHERE DATE(`fetch_date`) = DATE(NOW()) 
                ORDER BY rank
                LIMIT 46";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме цялата информация за някои от първите песни(за България)
    public function listTop50songsBG() {
        $sql = "SELECT * 
                FROM `tiktok_records_bulgaria` 
                JOIN tiktok_songs_bulgaria 
                ON tiktok_records_bulgaria.song_id = tiktok_songs_bulgaria.id
                WHERE DATE(`fetch_date`) = DATE(NOW()) 
                LIMIT 50";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Дърпаме всички дати със записи
    public function listDates() {
        $sql = "SELECT DISTINCT `fetch_date` 
                FROM `tiktok_records`";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Намираме песента на която отговаря конкретния запис
    public function findSongByTiktokId($pesho){
        $sql = "SELECT * 
                FROM tiktok_songs 
                WHERE `tiktok_platform_id`=:nababatifurchiloto";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('nababatifurchiloto', $pesho);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    //Намираме песента на която отговаря конкретния запис
    public function findSongByTiktokIdBG($pesho){
        $sql = "SELECT * 
                FROM tiktok_songs_bulgaria 
                WHERE `tiktok_platform_id`=:nababatifurchiloto";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('nababatifurchiloto', $pesho);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
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

    //Добавяме id на дадена песен в колона с всички id-та към записите за да има връзка между таблиците
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
                    :ushitesamigolemi,
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
		$query->bindValue('ushitesamigolemi', $object['spotify_popularity']);
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
                    :ushitesamigolemi,
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
		$query->bindValue('ushitesamigolemi', $object['spotify_popularity']);
        $query->bindValue('youtube_views', $object['youtube_views']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }

    //Добавяме популярност в Spotify за нашите записи
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

    //Добавяме гледания в YouTube за нашите записи
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

    //Намираме потребител с неговото id
    /**
     * @return false|array
     */
    public function getUserById($id){
        $sql = "SELECT * 
                FROM users
                WHERE id=:id";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('id', $id);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    //Проверяваме дали пощата е валидна
    // public function validateEmail(){
    //     $sql = "SELECT * 
    //             FROM users
    //             WHERE email = '%s'";

    //     sprintf($sql, $this->pdo->quote($_GET["email"]));

    //     $query = $this->pdo->prepare($sql);
    //     $query->execute();

    //     return $query->rowCount() === 0;
    // }

    //Проверяваме дали пощата не е заета
    public function validateEmailForLogIn(){
        $sql = "SELECT * 
                FROM users
                WHERE email =:email";//'%s'

        $query = $this->pdo->prepare($sql);
        $query->bindValue('email', $_POST["email"]);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    //Регистрираме новия потребител
    public function insertUser($name, $email, $pass){
        $sql = "INSERT INTO `users` (
                    `name`,
                    `email`, 
                    `password_hash`
                ) VALUES (
                    :name,
                    :email,  
                    :password_hash
                    )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('name', $name);
        $query->bindValue('email', $email);
        $query->bindValue('password_hash', $pass);

        $query->execute();

        return ($query->rowCount() > 0 ? true : false);
    }

    //Добавяме нов тиктокър
    public function insertTikToker($object){
        $sql = "INSERT INTO `tiktokers` ( 
                    `given_id`, 
                    `platform_name`,
                    `thumbnail`,
                    `tiktoker`, 
                    `nationality`,
                    `followers_count`, 
                    `followers_this_year`
                ) VALUES (
                    :given_id, 
                    :platform_name,
                    :thumbnail,
                    :tiktoker, 
                    :nationality,
                    :followers_count, 
                    :followers_this_year
                )";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('given_id', $object['id']);
        $query->bindValue('platform_name', $object['platform_name']);
        $query->bindValue('thumbnail', $object['thumbnail']);
        $query->bindValue('tiktoker', $object['name']);
        $query->bindValue('nationality', $object['nationality']);
        $query->bindValue('followers_count', $object['followers_count']);
        $query->bindValue('followers_this_year', $object['followers_this_year']);

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

    //Взимаме информацията за топ 200 тиктокъри
    public function getTiktokersTodayData(){
        $sql = "SELECT * 
                FROM `tiktokers` 
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме информацията за топ 50 тиктокъри
    public function getTiktokersTodayDataTop50(){
        $sql = "SELECT * 
                FROM `tiktokers` 
                WHERE DATE(`fetch_date`) = DATE(NOW()) LIMIT 50";

        $query = $this->pdo->prepare($sql);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    //Взимаме информацията за топ 200 най-гледани видея
    public function getTopVideosTodayData(){
        $sql = "SELECT * 
                FROM `tiktok_top_videos` 
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }
}