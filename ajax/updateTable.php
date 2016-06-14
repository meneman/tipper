<?php
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

givePoints($mysqli,2);
exit;

function deletePlayer($mysqli){
    
    global $_POST, $mysqli;
    $Pi = $_POST['selectedPlayer'];
    
    if($mysqli->query("DELETE FROM spieler WHERE id = '".$Pi."'")){
        echo "A player was removed <br>";
    }
    if($mysqli->query("DELETE FROM tips WHERE spieler_id = '".$Pi."'")){
        echo "All his bets were removed";
    }
}


function deleteGame($mysqli){
    
    global $_POST, $mysqli;
    $Pi = $_POST['selectedGame'];
    
    if($mysqli->query("DELETE FROM matches WHERE id = '".$Pi."'")){
        echo "A player was removed <br>";
    }
    if($mysqli->query("DELETE FROM matches WHERE spieler_id = '".$Pi."'")){
        echo "All his bets were removed";
    }
}

function addPlayer($mysqli) {
    global $_POST;
    
    $name = $_POST['InputName'];
 return   $mysqli->query("INSERT INTO `spieler` (`id`, `name`, `score`, `tip_count`, `dick`) VALUES (NULL, '".$name."', '', '', '0');");
    
}

function addGame($mysqli){
        global $_POST;
    
        $date = date('Y-m-d');
            return $mysqli->query("INSERT INTO `matches` (`name_first`, `name_secound`, `finished`, `spieldatum`) VALUES ( '". $_POST['mannschaftone']."', '". $_POST['mannschafttwo']."', '0', '".$date."');");
    
    
}

// TODO
function tipGame($mysqli){
        global $_POST;
    

            return $mysqli->query("INSERT INTO `matches` (`name_first`, `name_secound`, `finished`, `spieldatum`) VALUES ( '". $_POST['firstName']."', '". $_POST['secoundName']."', '0', '".$date."');");
    
    
}



function addResult($mysqli){
        global $_POST, $mysqli;
        
        $rs = (bool) $mysqli->query("SELECT EXISTS (SELECT 1 FROM matches WHERE id = '".$_POST['matchId']."')")->fetch_row()[0];
            
       givePoints($_POST['matchId']);
          return  $mysqli->query("UPDATE `matches` SET `finished` = '1', `score_first` = '".$_POST['scoreFirst']."', `score_secound` = '".$_POST['scoreSecound']."' WHERE `matches`.`id` = '".$_POST['matchId']."';");
    
}



function calculatePoints($sf,$ss,$bf,$bs){
    
    //Punkteverteiling
// 3 Punkte: Sieger richtig und tore richtig
// 2 Punkte: Sieger richtig, differenz richtig
// 1 Punkt: sieger richtig, tore flasch
    if($sf == $bf AND $ss == $bs){
       
       error_log("/n Error in calculate points with the following parameters".$sf.$ss.$bf.$bs, 3, "../log/error.log");
       
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

    $match = $mysqli->query("SELECT * FROM matches WHERE id = '".$matchId."'")->fetch_row();
    $scoreFi = $match[4];
    $scoreSe = $match[5];
    $selectbets = "SELECT * FROM tips WHERE match_id = '".$matchId."'";
    $result = $mysqli->query($selectbets);

    while($row = $result->fetch_array(MYSQL_ASSOC)){
        $points = calculatePoints($scoreFi,$scoreSe, $row['tip_first'], $row['tip_secound']);
        //echo $points;
       $playerId = $row['spieler_id'];
        $updateSql = "UPDATE `spieler` SET `score` = `score` + ".$points." WHERE `spieler`.`id` = '".$playerId."'";
        echo $updateSql;
        $mysqli->query($updateSql);
      echo $mysqli->error;
        //var_dump(mysql_error());
    }

}
