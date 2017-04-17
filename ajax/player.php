<?php
/**
 *  Model wrapper for player
 */
class Player
{

  private static $mysqli;
  private $connection;
  private $playerId;
  private $name;
  private $gamblingGroup;

  function __construct($id, $group)
  {
    this->id = $id;
    this->group = $group;

  }

   static function selectPlayer($id, $group,$d){
    Player::$mysqli = new mysqli($d[0],$d[1],$d[2],$d[3]);
    $instance = new self($id, $group);
    $instance->updatePlayer();
    return $instance;
  }
  static function createPlayer($name){
    $mysqli = Player::$mysqli;
    $mysqli->$mysqli->query("INSERT INTO `spieler` (`id`, `name`, `score`, `tip_count`, `dick`,`group`) VALUES (NULL, '".$name."', '', '', '0', '".$group."');");
  }
  function updatePlayer($id, $group){
    $mysqli = Player::$mysqli;
    //do mysql get SQLiteUnbuffered
    return true;
  }

function checkConnectionForErrors(){
  if (Player::$mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);

  } elseif (Player::$mysqli->connect_errno == 0) {
    printf("mysqli static in Player returned no errors what so ever");
  }
}


}
$d = array("localhost", "root", "", "tipper");
$player = Player::selectPlayer(34,10,$d);
$player->checkConnectionForErrors();




Class Group {

private $id;
private $player;
private $matches;

function __construct($id){
  $this->id = $id;
  $this->collectPlayers();
}
  private function collectPlayers(){
    if($matchSql = $mysqli->query("SELECT * FROM spieler WHERE session_id = '".$sessionId."'")) {
        while ($row = $matchSql->fetch_array(MYSQL_ASSOC)) {
            $count= $mysqli->query("SELECT count(*) AS total FROM tips WHERE spieler_id = ".$row["id"]."")->fetch_assoc()['total'];
             $data["spieler"][$row["id"]] = array($row['name'], $row['score'], $row['dick'], $count);
        }
    }

  }
}



 ?>
