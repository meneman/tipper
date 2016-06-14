<?php
//$d = array("localhost", "web308", "Muc9tAeM", "usr_web308_1");
$d = array("localhost", "root", "", "tipper");


$mysqli = new mysqli($d[0],$d[1],$d[2],$d[3]);

if($mysqli->connect_errno){
    echo "Failed to connect.";
}
$data = array();
// Now pull all the data from the database. 

$mysqli->query("SELECT * FROM matches");
$mysqli->query("SELECT * FROM spieler");
$mysqli->query("SELECT * FROM tips");
$mysqli->query("SELECT * FROM settings");


if($matchSql = $mysqli->query("SELECT * FROM spieler")) {
    while ($row = $matchSql->fetch_array(MYSQL_ASSOC)) {
        $count= $mysqli->query("SELECT count(*) AS total FROM tips WHERE spieler_id = ".$row["id"]."")->fetch_assoc()['total'];  
         $data["spieler"][$row["id"]] = array($row['name'], $row['score'], $row['dick'], $count);
    }
}

if($matchSql = $mysqli->query("SELECT * FROM matches")) {
    while ($row = $matchSql->fetch_array(MYSQL_ASSOC)) {
         $data["matches"][$row["id"]] = $row;
        
    }
}

if($matchSql = $mysqli->query("SELECT * FROM tips")) {
    while ($row = $matchSql->fetch_array(MYSQL_ASSOC)) {
        $currentMatch = $data["matches"][$row['match_id']];
        $currentPlayer = $data["spieler"][$row['spieler_id']];
            $row['spieler_id'] = $data["spieler"][$row['spieler_id']];
            $row['match_id'] = $data["matches"][$row['match_id']];
         $data["tips"][$row["id"]] = array("match" => $currentMatch, "spieler" =>  $currentPlayer);
    }
}

if($_GET){
    $res = $_GET['res'];
    $id = $_GET['bid'];
    
    echo json_encode($data[$res][$id]);
} else {
    echo json_encode($data);
}



