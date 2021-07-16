<?php


#echo "1";
extract ($_GET);

#if ($refreshtimer == "stop") { 
#    header("refresh: 600");
#    echo "<br>AutoREFRESH: STOPED<br>";
#}

if ($refreshtimer == "start") {
    header("refresh: 3"); 
    echo "<br>AutoREFRESH: 3 sec<br>";
} else {
    header("refresh: 600");
    echo "<br>AutoREFRESH: STOPED<br>";
}

if ($showhistory == "on") {
    $history = file_get_contents('/tmp/history',false,NULL,-500,500);
    $history = $history . file_get_contents('/tmp/loadhistory',false,NULL,-500,500);
}

echo "<html>";
echo "<head>";
echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
echo "</head>";
echo "<body>";
echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>';
echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>';
echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>';

echo "Current time: ".date("Y-m-d H:m:s") . "<br>";
#var_dump($response_load["result"]);



echo "<table border = 1><th>load</th>";
foreach($response_load["result"] as $name=>$value) {
	     if (preg_match("/all/",$name)) {
		
		if ($value >= 30) {
			$string_load_save = date("Y-m-d H:m:s") . "\t" . $name . "\t" . $value . "<br>\r\n";
			file_put_contents('/tmp/loadhistory', $string_load_save, FILE_APPEND);
			$colored = "style=\"color:red\"";
		}
	    echo "<tr>";
	    echo "<td $colored>$name - $value</td>";
	    echo "</tr>";
    }
}	

echo "</table>";


echo "<table class=\"table\">";
echo "<th>IP</th><th>Limit</th><th>CPS</th>";


foreach($response["result"] as $item) {

    foreach($item as $head=>$val) {
	$limit_cell = $val["limit"];
	$string = "<form action=\"setlimit.php\" method=GET><input type=text name=limit_".$val["id"]." value=".$val[limit]."><input type=submit value=set></form>";
	echo "<tr>";
	$counter = $val["counter"];

	if ($val["limit"] - $val["counter"] < 2) { 
		$string_save = date("Y-m-d H:m:s") . "\t" . $val["id"] . "\t" . $counter . "<br>\r\n";
		file_put_contents('/tmp/history', $string_save, FILE_APPEND);
		$counter="<b>".$val["counter"]."</b>";
		}
	
	echo "<td>".$val["id"]."</td><td>".$string."</td><td align=center>".$counter."</td>";
	echo "</tr>";
    }

}


echo "</table>";

if ($showhistory == "on") {
    echo "History IPs over CPS limit:<br>";
    print_r($history);    
}


echo "<form method=GET><input type=hidden name=refreshtimer value=stop><input type=submit value=RefreshStop></form>";
echo "<form method=GET><input type=hidden name=refreshtimer value=start><input type=submit value=RefreshStart></form>";
echo "<form method=GET><input type=hidden name=showhistory value=on><input type=submit value=\"History\"></form>";
echo "</body>";
echo "</html>";
?>