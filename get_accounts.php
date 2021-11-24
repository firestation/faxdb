<?php

$array = file ( '/home/centos/accounts.csv' );

foreach ($array as $string) {
    $str_arr = explode(',',$string);
    for ($i=6;$i<30;$i++) {
        if ($str_arr[$i] == "") 
            break;
        $clients[$str_arr[$i]] = [   "account_id" => $str_arr[0], 
                                     "account_name" => $str_arr[5], 
                                     "client_name" => $str_arr[1]
                                ];
    }
    
}
 
var_dump($clients);


?>