<?php

/**
 * Class DB
 *
 * @property PDO $pdo
 */
class DatabaseManager {
    public function __construct(){
    	$dbopts = [
    		'db_host' => 'localhost',
    		'db_name' => 'songs',
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

    /**
     * @return false|array
     */
    // public function listUsers() {
    //     // Order by is not necessary here, it can be removed if not needed.
    //     $sql = "SELECT * FROM `users` ORDER BY `username`";

    //     $query = $this->pdo->prepare($sql);
    //     $query->execute();
    //     $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

    //     return count($result_array) > 0 ? $result_array : false;
    // }

    public function fetchSpotify(){
        $sql = "SELECT spotify_platform_id, spotify_popularity, id, fetch_date FROM tiktok_records WHERE DATE(`fetch_date`) = DATE(NOW())"; //spotify_popularity = null AND date > DATEADD(day, -1, GETDATE()) AND date < DATEADD(day, 1, GETDATE())"

        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchYoutube(){
        $sql = "SELECT youtube_views, id, youtube_platform_id, fetch_date FROM tiktok_records WHERE DATE(`fetch_date`) = DATE(NOW())"; //WHERE id > 200,400,600... DATE(`fetch_date`) = DATE(NOW())

        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllDataFromRecords(){
        $sql = "SELECT * FROM tiktok_records"; //  WHERE DATE(`fetch_date`) = DATE(NOW())

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $songs = $query->fetchAll(PDO::FETCH_ASSOC);

        return $songs;
    }

    public function fetchAllDataFromSongs(){
        $sql = "SELECT * FROM tiktok_songs"; //  WHERE DATE(`fetch_date`) = DATE(NOW())

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $songs = $query->fetchAll(PDO::FETCH_ASSOC);

        return $songs;
    }

    public function getDatapointsForSong($sid){
        $sql = "SELECT * FROM `tiktok_records`
                WHERE song_id=:mandja";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('mandja', $sid);

        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    public function listTop200Songs() {
        $sql = "SELECT * FROM `tiktok_records` 
                JOIN tiktok_songs ON tiktok_records.song_id = tiktok_songs.id
                WHERE DATE(`fetch_date`) = DATE(NOW())";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($result_array) > 0 ? $result_array : false;
    }

    public function fetchMode($prepared){
        $prepared->setFetchMode(PDO::FETCH_ASSOC);
    }

    public function findSongByTiktokId($pesho){
        $sql = "SELECT * FROM tiktok_songs WHERE `tiktok_platform_id`=:nababatifurchiloto";

        $query = $this->pdo->prepare($sql);
        $query->bindValue('nababatifurchiloto', $pesho);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function createSong($object){
        $sql = "
            INSERT INTO `tiktok_songs` ( 
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
            )
        ";

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

    public function insertIdForDataPoint($object){
        $sql = "UPDATE `tiktok_records` SET `song_id`=:song_id WHERE `song_id` = NULL";

        $query = $this->pdo->prepare($sql);

        $query->bindValue('song_id', $object['song_id']);

        $query->execute();

        return $this->pdo->lastInsertId();
    }
 //    private function initTables() {
 //        $this->pdo->exec(
 //            "CREATE TABLE IF NOT EXISTS User ( ".
 //            "Code INT(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,".
 //            "Name VARCHAR(33) NOT NULL,".
 //            "Kind INT(4) NOT NULL,".
 //            "Sum DECIMAL(9,2) NOT NULL)"
 //        );

 //        if($this->countUsers() === 0) {
 //            $this->pdo->exec(
 //                "INSERT INTO User(Code,Name,Kind,Sum) VALUES ".
 //                "(1,'Hotel Maritza',1,222.22),".
 //                "(2,'Banka BAKB',2,111.11)"
 //            );
 //        }
 //    }

	// // ------------------ Generic stuff ---------------------------

    // /**
    //  * @param int $id
    //  * @param array $object
    //  * @return bool
    //  */
    // public function updateUser($id, $object){
    //     $params = array();
    //     $sql = "UPDATE `tiktok_songs` SET ";
    //     $i = 1;
    //     $arr_count = count($object);
    //     foreach ($object as $key => $value) {
    //         $sql .= "`" . $key . "`=?";
    //         $params[] = $value;
    //         $sql .= ($i < $arr_count ? ", " : " ");
    //         $i++;
    //     }
    //     $sql .= "WHERE `code`=?";

    //     $params[] = $id;
    //     $query = $this->pdo->prepare($sql);
    //     $query->execute($params);

    //     return ($query->rowCount() > 0 ? true : false);
    // }

 //    // ------------------ Authentication --------------------------
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
			)
			VALUES (
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

	/**
     * @param array $object
     * @return string
     */
    public function updateSpotifyPopularity($id, $newPopularity){
        $sql = "UPDATE `tiktok_records` SET `spotify_popularity`=:spotify_popularity WHERE `id`=:id";

        $query = $this->pdo->prepare($sql);

        $query->bindValue("id", $id, PDO::PARAM_INT);
        $query->bindValue("spotify_popularity", $newPopularity, PDO::PARAM_INT);

        $query->execute();

		return ($query->rowCount() > 0 ? true : false);
    }

	/**
     * @param array $object
     * @return string
     */
    public function updateYoutubeViews($id, $newPopularity){
        $sql = "UPDATE `tiktok_records` SET `youtube_views`=:youtube_views WHERE `id`=:id";

        $query = $this->pdo->prepare($sql);

        $query->bindValue("id", $id, PDO::PARAM_INT);
        $query->bindValue("youtube_views", $newPopularity, PDO::PARAM_INT);

        $query->execute();

		return ($query->rowCount() > 0 ? true : false);
    }
 //    /**
 //     * @param $Code
 //     * @return false|array
 //     */
 //    public function getUserByCode($Code) {
 //        $sql = "SELECT * FROM `User` WHERE `Code`=:Code";

 //        $query = $this->pdo->prepare($sql);
 //        $query->bindValue('Code', $Code);
 //        $query->execute();
 //        $result_array = $query->fetch(PDO::FETCH_ASSOC);

 //        return count($result_array) > 0 ? $result_array : false;
 //    }

 //    /**
 //     * @param $Code
 //     * @return false|array
 //     */
 //    public function getUsersByKind($Kind) {
 //        $sql = "SELECT * FROM `User` WHERE `Kind`=:Kind";

 //        $query = $this->pdo->prepare($sql);
 //        $query->bindValue('Kind', $Kind);
 //        $query->execute();
 //        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

 //        return count($result_array) > 0 ? $result_array : false;
 //    }

 //    /**
 //     * @param $Code
 //     * @return false|array
 //     */
 //    public function findSumHigher($Sum) {
 //        $sql = "SELECT * FROM `User` WHERE `Sum`>:Sum AND `Kind`='2'";

 //        $query = $this->pdo->prepare($sql);
 //        $query->bindValue('Sum', $Sum);
 //        $query->execute();
 //        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

 //        return count($result_array) > 0 ? $result_array : false;
 //    }

 //    /**
 //     * @param $Code
 //     * @return false|array
 //     */
 //    public function nextCode() {
 //        $sql = "SELECT `Code` FROM `User` ORDER BY `Code` DESC";

 //        $query = $this->pdo->prepare($sql);
 //        $query->execute();
 //        $result_array = $query->fetch(PDO::FETCH_ASSOC);

 //        return count($result_array) > 0 ? $result_array['Code'] + 1 : false;
 //    }

 //    /**
 //     * @param $Code
 //     * @return false|array
 //     */
 //    public function countUsers() {
 //        $sql = "SELECT COUNT(`Code`) as `count` FROM `User`";

 //        $query = $this->pdo->prepare($sql);
 //        $query->execute();
 //        $result_array = $query->fetch(PDO::FETCH_ASSOC);

 //        return (int) $result_array['count'];
 //    }

 //    /**
 //     * @return false|array
 //     */
 //    public function listUsersNameAsc() {
 //        $sql = "SELECT * FROM `User` ORDER BY `Name` ASC";

 //        $query = $this->pdo->prepare($sql);
 //        $query->execute();
 //        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

 //        return count($result_array) > 0 ? $result_array : false;
 //    }

 //    /**
 //     * @return false|array
 //     */
 //    public function listUsersFieldAsc($field) {
 //        $sql = "SELECT * FROM `User` ORDER BY ".$field." ASC";

 //        $query = $this->pdo->prepare($sql);
 //        $query->execute();
 //        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);

 //        return count($result_array) > 0 ? $result_array : false;
 //    }

 //    /**
 //     * @param $Code
 //     * @return false|array
 //     */
 //    public function deleteUser($Code) {
 //        $sql = "DELETE FROM `User` WHERE `Code`=:Code";

 //        $query = $this->pdo->prepare($sql);
 //        $query->bindValue('Code', $Code);
 //        $query->execute();

 //        return ($query->rowCount() > 0 ? true : false);
 //    }
}