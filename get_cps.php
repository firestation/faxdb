<?php

$url = "http://127.0.0.1:8000/json";
//echo "Server: " . $url . "<br>";
//echo "host: ". $_SERVER["REMOTE_ADDR"];

#prepare json request
$data_limits = array(
  'jsonrpc'	=> "2.0",
  'method'      => "rl_list",
  'id'    	=> 10,
);
#option for json request
$options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => "\r\n" . json_encode( $data_limits ) . "\r\n",
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);

$context  = stream_context_create( $options );
$result   = file_get_contents( $url, false, $context );

#get calls per second data into array
$response = json_decode( $result , false);

//var_dump($response->result->Pipes);

foreach($response->result->Pipes as $cps_item) {
    if ( $cps_item->id == "total_INVITE" ) 
        echo $cps_item->counter;
}

/*
var_dump($response["result"]["Pipes"]);
foreach($response["result"]["Pipes"] as $value) {
    $value["id"]
    #   var_dump($value["id"]);
        $string_cps_save = date("Y-m-d H:i:s") . "\t" . $value["id"] . "\t" . $value["limit"] . "\t" . $value["counter"] . "\r\n";
        echo $string_cps_save;
        file_put_contents('/var/www/history/cpshistory', $string_cps_save, FILE_APPEND);
    
    }
*/
?>