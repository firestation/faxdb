<?php


$redis = new Redis();
//Connecting to Redis
$redis->connect('127.0.0.1');

foreach($_GET as $ip=>$limit) {
	$ip = str_replace("limit_","",$ip);
	$ip = str_replace("_",".",$ip);
	
	echo set_limit($redis,$ip,$limit);
	echo $ip . " ". $limit . "<br>";
}

function set_limit($redis,$ip,$limit) {
    if ($ip == "total.INVITE") {
	$redis->hset("totalcpslimit","all","$limit");	
    } else {
	$redis->hset("ipcpslimit","$ip","$limit");
	var_dump($redis->hget("ipcpslimit","$ip"));
    }
#    echo $ip;

}

header("Refresh:0; url=limits.php");


?>
