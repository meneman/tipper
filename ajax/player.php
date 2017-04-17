<?php
/**
 *  Model wrapper for player
 */


class Database {
  public static $mysqli;

  function __construct($d)
  {
      Database::$mysqli = new mysqli($d[0],$d[1],$d[2],$d[3]);

  }


  function checkConnectionForErrors(){
    if (Database::$mysqli->connect_errno) {
      printf("Connect failed: %s\n", $mysqli->connect_error);

    } elseif (Database::$mysqli->connect_errno == 0) {
      printf("mysqli static in Player returned no errors what so ever");
    }
  }

}


class Player extends Database
{

  private $connection;
  private $playerId;
  private $name;
  private $gamblingGroup;

  function __construct($id, $group)
  {
    $this->id = $id;
    $this->group = $group;

  }

   static function selectPlayer($id, $group,$d){
    $instance = new self($id, $group);
    $instance->updatePlayer();
    return $instance;
  }
  static function createPlayer($name){
    $mysqli = parent::$mysqli;
    $mysqli->$mysqli->query("INSERT INTO `spieler` (`id`, `name`, `score`, `tip_count`, `dick`,`group`) VALUES (NULL, '".$name."', '', '', '0', '".$group."');");
  }
  function updatePlayer(){
    $mysqli = parent::$mysqli;
    //do mysql get SQLiteUnbuffered
    return true;
  }


}
$d = array("localhost", "root", "", "tipper");
new Database($d);




Class Group extends Database{

private $id;
private $player = array();
private $matches = array();

function __construct($id){
  $this->id = $id;
}

  public function getPlayers(){
    if($matchSql = parent::$mysqli->query("SELECT * FROM spieler WHERE session_id = '".$this->id."'")) {
        while ($row = $matchSql->fetch_array(MYSQL_ASSOC)) {
            $count= parent::$mysqli->query("SELECT count(*) AS total FROM tips WHERE spieler_id = ".$row["id"]."")->fetch_assoc()['total'];
            $this->player[$row["id"]] = array($row['id'],$row['name'], $row['score'], $count);
        }
    }

    return $this->player;

  }

  public function getMatches(){
    if($matchSql = parent::$mysqli->query("SELECT * FROM matches WHERE session_id = '".$this->id."'")) {
        while ($row = $matchSql->fetch_array(MYSQL_ASSOC)) {
             $this->matches["matches"][$row["id"]] = $row;

        }
    }
  }

}

$group = new Group(10);
var_dump($group->getPlayers());





 ?>
