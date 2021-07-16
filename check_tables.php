<?php

$ini_array = parse_ini_file("/etc/faxweb.ini");

$mysqli = new mysqli($ini_array["host"], $ini_array["user"], $ini_array["password"], $ini_array["db"]);
if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$global_blacklist_res = $mysqli->query("SELECT number from numbers where  companieid = '226'");

while ($row = $global_blacklist_res->fetch_assoc()) {
    $global_blacklist[] = $row["number"];
}
echo "get blacklist:".count($global_blacklist)."\n";

$white_list_res = $mysqli->query("SELECT number from numbers where  companieid = '228'");

while ($row = $white_list_res->fetch_assoc()) {
    $white_list[] = $row["number"];
}

foreach($global_blacklist as $blacklisted) {
    //echo "search:". $blacklisted."\n";
    $a++;
    echo "$a\n";
    foreach($white_list as $whited){

        $isblacklisted = false;

            
            if (preg_match("/$blacklisted/",$whited) > 0) {
                $isblacklisted = true;
                
            }
            
        }
        
        if ($isblacklisted == true) {
            file_put_contents('/tmp/filter',$row["number"]."-".$blacklisted."\n",FILE_APPEND);
            break;
        }
        


    }


    ?>