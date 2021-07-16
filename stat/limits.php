<?php
#where to get json stat
$url = "http://127.0.0.1:8000/json";
echo "Server: " . $url . "<br>";
echo "host: ". $_SERVER["REMOTE_ADDR"];

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



$data_load = array(
  'jsonrpc'     => "2.0",
  'method'      => "get_statistics",
  'params'      => array(array("load:")),
  'id'          => 12,
);

$options_load = array(
  'http' => array(
    'method'  => 'POST',
    'content' => "\r\n" . json_encode( $data_load ) . "\r\n",
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"  .
                "Host: 92.63.111.30\r\n"
    )
);
#create stream and get answer from opensips software
$context_load  = stream_context_create( $options_load );
$result_load   = file_get_contents( $url, false, $context_load );

#get load data
$response_load = json_decode( $result_load , true);



#get ip cps limits
$context  = stream_context_create( $options );
$result   = file_get_contents( $url, false, $context );

#get calls per second data into array
$response = json_decode( $result , true);

#show retreived data
require "show_table.php";
#var_dump($response);

?>