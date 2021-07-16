<?php
extract($_POST);

$redis = new Redis();
//Connecting to Redis
$redis->connect('127.0.0.1');


    if ($ip == "total.INVITE") {
	$redis->hset("totalcpslimit","all","$limit");	
    } else {
	$redis->hset("ipcpslimit","$ip","$limit");
	var_dump($redis->hget("ipcpslimit","$ip"));

#    echo $ip;
	}

//header("Refresh:0; url=limits.php");


?>
