<?php
#where to get json stat
#$url = "http://127.0.0.1:8000/json";


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



#get ip cps limits
$context  = stream_context_create( $options );
$result   = file_get_contents( $url, false, $context );

#get calls per second data into array
$response_limits = json_decode( $result , true);

#show retreived data
#require "show_table2.php";
#var_dump($response);

?>