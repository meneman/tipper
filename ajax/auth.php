<?php

$d = array("localhost", "root", "", "tipper");



$jobs = array('create', 'login');
if(@$_POST){
    $job = $_POST['job'];

    $job($mysqli);
    
} else {

    exit;
}


$mysqli = new mysqli($d[0],$d[1],$d[2],$d[3]);

if($mysqli->connect_errno){
    echo "Failed to connect.";
}



function create($mysql){
    
    
}