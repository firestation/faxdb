<?php

//get IP and his accounts and clients name
$array = file ( 'accounts.csv');

foreach ($array as $string) {
    
    $str_arr = explode(',',$string);
    for ($i=6;$i<30;$i++) {
        if ($str_arr[$i] == "") 
            break;
    
            $str_arr[$i] =  preg_replace("/ /",'', $str_arr[$i]);
      $clients[$str_arr[$i]] = [  "account_id" => $str_arr[0], 
                                    "account_name" => $str_arr[5], 
                                    "client_name" => $str_arr[1]
                                ];
    }
    
}

//var_dump($clients);

$url = "http://127.0.0.1:8000/json";
#$url = "http://54.85.124.142:8000/json";
#echo "Server: " . $url . "<br>";
#echo "host: ". $_SERVER["REMOTE_ADDR"];

#if ($_SERVER["REMOTE_ADDR"] == "92.63.110.57")

$data_limits = array(
  'jsonrpc'	=> "2.0",
  'method'      => "dlg_list",
  //'params' 	=> array(array("load:")),
  'id'    	=> 13,
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
$response = json_decode( $result , true);

#require "show_table.php";
#print_r($response_load["result"]['load:load']);
#var_dump($response["result"]["Dialogs"]);
foreach ($response["result"]["Dialogs"] as $call) {
    if ($call["state"] != 4 ) 
        continue;
        

        $from_array = preg_split("/[@:;]/",$call["from_uri"]);
        $to_array = preg_split("/[@:;]/",$call["to_uri"]);
    


      $call_line = array (
        
        
        $call["datestart"],
        $call["from_ip"] = $from_array[2],
        $call["from_client"] = $clients[$call["from_ip"]]["client_name"],
        $call["from_acc"] = $clients[$call["from_ip"]]["account_name"],
        $call["Duration"] = time() - $call["timestart"],
        $call["from"] = $from_array[1],
        $call["to"] = $to_array[1],
        
    );
    $online_calls[] = $call_line;

    $online_calls_full[] = $call;
}
$count_online_calls = count($online_calls);

//var_dump($online_calls_full);
/*foreach($res_array as $line) {
    echo implode("|",$line)."\n";
}*/



/*
[0]=>
array(15) {
  ["ID"]=>
  string(12) "208302313022"
  ["state"]=>
  int(5)
  ["user_flags"]=>
  int(0)
  ["timestart"]=>
  int(0)
  ["timeout"]=>
  int(0)
  ["callid"]=>
  string(38) "2952700963616c6c1d38e317@78.47.166.106"
  ["from_uri"]=>
  string(29) "sip:61787524536@78.47.166.106"
  ["to_uri"]=>
  string(29) "sip:61395344047@54.85.124.142"
  ["caller_tag"]=>
  string(16) "342596d9205739dc"
  ["caller_contact"]=>
  string(34) "sip:61787524536@78.47.166.106:5060"
  ["callee_cseq"]=>
  string(1) "0"
  ["caller_route_set"]=>
  string(0) ""
  ["caller_bind_addr"]=>
  string(22) "udp:172.31.53.103:5060"
  ["caller_sdp"]=>
  string(261) "v=0^M
o=- 43653 43653 IN IP4 162.211.125.78^M
s=VOS3000^M
c=IN IP4 162.211.125.78^M
t=0 0^M
m=audio 23784 RTP/AVP 0 18 101^M
a=rtpmap:0 PCMU/8000^M
a=rtpmap:18 G729/8000^M
a=fmtp:18 annexb=no^M
a=ptime:20^M
a=rtpmap:101 telephone-event/8000^M
a=fmtp:101 0-15^M
a=sendrecv^M
"
  ["CALLEES"]=>
  array(2) {
    [0]=>
    array(5) {
      ["callee_tag"]=>
      string(0) ""
      ["callee_contact"]=>
      string(0) ""
      ["caller_cseq"]=>
      string(0) ""
      ["callee_route_set"]=>
      string(0) ""
      ["callee_bind_addr"]=>
      NULL
    }
    [1]=>
    array(6) {
      ["callee_tag"]=>
      string(37) "7ea4-799f9f69c0cbd354ae74be1cf627305a"
      ["callee_contact"]=>
      string(0) ""
      ["caller_cseq"]=>
      string(5) "21600"
      ["callee_route_set"]=>
      string(0) ""
      ["callee_bind_addr"]=>
      string(22) "udp:172.31.53.103:5060"
      ["callee_sent_sdp"]=>
      string(277) "v=0^M
o=- 43653 43653 IN IP4 54.144.166.98^M
s=VOS3000^M
c=IN IP4 54.144.166.98^M
t=0 0^M
m=audio 29762 RTP/AVP 0 18 101^M
a=rtpmap:0 PCMU/8000^M
a=rtpmap:18 G729/8000^M
a=fmtp:18 annexb=no^M
a=ptime:20^M
a=rtpmap:101 telephone-event/8000^M
a=fmtp:101 0-15^M
a=sendrecv^M
a=nortpproxy:yes^M
"
      }
    }
  }
*/
?>