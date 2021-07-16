<?php

$url = "http://127.0.0.1:8000/json";
#$url = "http://54.85.124.142:8000/json";
#echo "Server: " . $url . "<br>";
#echo "host: ". $_SERVER["REMOTE_ADDR"];

#if ($_SERVER["REMOTE_ADDR"] == "92.63.110.57")

$data_limits = array(
  'jsonrpc'	=> "2.0",
  'method'      => "get_statistics",
  'params' 	=> array(array("load:")),
  'id'    	=> 12,
);

$options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => "\r\n" . json_encode( $data_limits ) . "\r\n",
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"  .
                "Host: 92.63.111.30\r\n"
    )
);

$context  = stream_context_create( $options );
$result   = file_get_contents( $url, false, $context );
$response_load = json_decode( $result , true);

#require "show_table.php";
#print_r($response_load["result"]['load:load']);
#var_dump($response);

?>