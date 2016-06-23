<?php
session_start();
$sessionId = $_SESSION['sessionID'];
//$d = array("localhost", "web308", "Muc9tAeM", "usr_web308_1");
$d = array("localhost", "root", "", "tipper");


$mysqli = new mysqli($d[0],$d[1],$d[2],$d[3]);

$jobs = array('deletePlayer', 'addPlayer', 'addGame', 'addResults', 'deleteGame', 'TipGame');
if(@$_POST){
    $job = $_POST['job'];

    $job($mysqli);
    
} else {

    exit;
}



if($mysqli->connect_errno){
    echo "Failed to connect.";
    exit;
}


function deletePlayer($mysqli){
    
    global $_POST, $mysqli, $sessionId;
    $Pi = $_POST['selectedPlayer'];
    
    if($mysqli->query("DELETE FROM spieler WHERE id = '".$Pi."' AND session_id = '".$sessionId."'")){
        echo "A player was removed <br>";
    }
    if($mysqli->query("DELETE FROM tips WHERE spieler_id = '".$Pi."' AND session_id = '".$sessionId."'")){
        echo "All his bets were removed";
    }
}


function deleteGame($mysqli){
    
    global $_POST, $mysqli, $sessionId;
    $Pi = $_POST['selectedGame'];
    
    if($mysqli->query("DELETE FROM matches WHERE id = '".$Pi."' AND session_id = '".$sessionId."'")){
        echo "A player was removed <br>";
    }
    if($mysqli->query("DELETE FROM matches WHERE spieler_id = '".$Pi."' AND session_id = '".$sessionId."'")){
        echo "All his bets were removed";
    }
}

function addPlayer($mysqli) {
    global $_POST, $sessionId;
    
    $name = $_POST['InputName'];
 return   $mysqli->query("INSERT INTO `spieler` (`id`, `name`, `score`, `tip_count`, `dick`,`session_id`) VALUES (NULL, '".$name."', '', '', '0', '".$sessionId."');");
    
}

function addGame($mysqli){
        global $_POST, $sessionId;
    
        $date = date('Y-m-d');
            return $mysqli->query("INSERT INTO `matches` (`name_first`, `name_secound`, `finished`, `spieldatum`,`session_id`) VALUES ( '". $_POST['mannschaftone']."', '". $_POST['mannschafttwo']."', '0', '".$date."' , '".$sessionId."');");
    
    
}

// TODO
function tipGame($mysqli){
        global $_POST, $sessionId;
      

            return $mysqli->query("INSERT INTO `tips` (`id`, `spieler_id`, `match_id`, `tip_first`, `tip_secound`, `session_id`) VALUES (NULL, '". $_POST['selectedPlayer']."', '". $_POST['match']."', '". $_POST['TipFirst']."', '". $_POST['TipSecound']."', '".$sessionId."');");
    
    
}



function addResult($mysqli){
        global $_POST, $sessionId;
        
        $rs = (bool) $mysqli->query("SELECT EXISTS (SELECT 1 FROM matches WHERE id = '".$_POST['match']."')")->fetch_row()[0];
        if($rs){
            $result = $mysqli->query("UPDATE `matches` SET score_first = '".$_POST['ResultFirst']."', score_secound = '".$_POST['ResultSecound']."', finished = '1' WHERE id = '". $_POST['match']."';");
        }
    
    if($result) {
         givePoints($mysqli,$_POST['match']);
    }
    
}



function calculatePoints($sf,$ss,$bf,$bs){
    
    //Punkteverteiling
// 3 Punkte: Sieger richtig und tore richtig
// 2 Punkte: Sieger richtig, differenz richtig
// 1 Punkt: sieger richtig, tore flasch
    echo  $sf.$ss.$bf.$bs;
    if($sf == $bf AND $ss == $bs){
       
     return 3;
    }
    
    
    if( ($sf <= $ss AND $bf <= $bs) OR ($sf >= $ss AND $bf >= $bs)){
        $diffsc = $ss - $sf;
        $diffbett = $bs - $bf;
        if($diffsc == $diffbett){
            return 2;
        } else {
           if(($diffsc == 0 or $diffbett == 0) AND ($diffsc != 0 or $diffbett =! 0)){

               return 0;
           } else {
               return 1;
           }
            

        }
    }else
       {
        return 0;
        }
        error_log("<br>Error in calculate points with the following parameters".$sf.$ss.$bf.$bs, 3, "../log/error.log");
       

    
}

function givePoints($mysqli, $matchId){
 global  $sessionId;
    $match = $mysqli->query("SELECT * FROM matches WHERE id = '".$matchId."'")->fetch_row();
    var_dump($match);
    $scoreFi = $match[4];
    $scoreSe = $match[5];
    $selectbets = "SELECT * FROM tips WHERE match_id = '".$matchId."' AND session_id = '".$sessionId."'";
    $result = $mysqli->query($selectbets);

    while($row = $result->fetch_array(MYSQL_ASSOC)){
       // var_dump($scoreFi." ".$scoreSe." ". $row['tip_first']." ". $row['tip_secound']);
        $points = calculatePoints($scoreFi,$scoreSe, $row['tip_first'], $row['tip_secound']);
        echo "Points: ".    $points;
       $playerId = $row['spieler_id'];
        $updateSql = "UPDATE `spieler` SET `score` = `score` + ".$points." WHERE `spieler`.`id` = '".$playerId."'";
        echo $updateSql;
        $mysqli->query($updateSql);
      echo $mysqli->error;
        //var_dump(mysql_error());
    }

}
