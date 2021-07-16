<?php

//by default stript takes only cmp_id port for start company
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//echo ini_get('max_execution_time')."\n"; 

$mtime_config = filemtime('/etc/faxweb.ini');

$ini_array = parse_ini_file("/etc/faxweb.ini");
//var_dump($_POST);
$interval           = $ini_array["interval"];          //seconds
$cmp_iterate_limit  = $ini_array["cmp_iterate_limit"]; //how much calls will be service per one iterate due to problem with max execution time
$current_calls = [];
//echo $cmp_iterate_limit;
extract($_POST);


//$cmp_id = '224';
//$post_action = 'start_cmp';



if (!isset($cmp_id)) exit;

$run_file = "/tmp/$cmp_id.runing";
$last_server_tried = 0;
$last_prefix_tried = 0;

$servers = $ini_array["servers"];

//STOP\START\PAUSE service with files
$mysqli = new mysqli($ini_array["host"], $ini_array["user"], $ini_array["password"], $ini_array["db"]);
if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

##############################
# ACTIONS
###############################


if ($post_action == "stop_cmp") {
    echo "Company $cmp_id stoped";
    unlink($run_file);
    $up_res = $mysqli->query("UPDATE companies set status = 'stoped' where idcompanies = '$cmp_id'");
    check_sql_res($mysqli,'set status to stoped');
    exit;
}

if ($post_action == "restart") {
    echo "Company $cmp_id restarted";
    unlink($run_file);
    sleep(5);
    $up_res = $mysqli->query("UPDATE companies set status = 'restarted' where idcompanies = '$cmp_id'");
    check_sql_res($mysqli,'set status to restarted');

    //exit;
}


if (file_exists($run_file)) {
    echo "Company $cmp_id alredy started";
    exit;
}


//default is run companie
//create run_file
file_put_contents($run_file,"$cmp_id running");
$up_res = $mysqli->query("UPDATE companies set status = 'running' where idcompanies = '$cmp_id'");
check_sql_res($mysqli,'set status to running');

$try_id      = bin2hex(random_bytes(5));
$try_created = date("Y-m-d H:i:s");

//echo "1";
$cmp_res = $mysqli->query("SELECT * from companies WHERE idcompanies = '$cmp_id'");
check_sql_res($mysqli);
$cmp = $cmp_res->fetch_assoc();
$trunk_prefix   = $cmp["trunk_prefix"];
$trunk_prefix_arr = explode(",",$trunk_prefix);
$force_g711     = $cmp["force_g711"];

echo "cmp started: ".$cmp["name"]."\n";

$global_blacklist_res = $mysqli->query("SELECT number from global_blacklist");
check_sql_res($mysqli);

while ($row = $global_blacklist_res->fetch_assoc()) {
    $global_blacklist[] = $row["number"];
}



$full_number_list = $mysqli->query("SELECT count(*) as allnumbers from numbers WHERE companieid = '$cmp_id' and status is NULL");
check_sql_res($mysqli);

$count = $full_number_list->fetch_assoc();
$pages = $count["allnumbers"]%$cmp_iterate_limit + 1;
if ($count["allnumbers"] == '0')
    $pages = -1;



for ($a=0; $a <= $pages; $a++) {
    $offset = $a*$cmp_iterate_limit;
    $number_list = $mysqli->query("SELECT * from numbers WHERE companieid = '$cmp_id' and status is NULL limit $cmp_iterate_limit offset $offset");
    check_sql_res($mysqli);

    while ( $row = $number_list->fetch_assoc() ) {
        $number = $row["number"];

        clearstatcache('/etc/faxweb.ini');
        if (filemtime('/etc/faxweb.ini') != $mtime_config) {
            $mtime_config = filemtime('/etc/faxweb.ini');
            $ini_array = parse_ini_file('/etc/faxweb.ini');
            $interval = $ini_array["interval"];

            

        }



        usleep($interval*1000000);
        
        $isblacklisted = false;
        foreach($global_blacklist as $blacklisted){
            
            if (preg_match("/$blacklisted/",$row["number"]) > 0) {
                $isblacklisted = true;
                break;
            }
            
        }
        
        if ($isblacklisted == true) {
            $full_number_list = $mysqli->query("UPDATE numbers SET status = 'blacklisted' WHERE number = '$number' and companieid = '$cmp_id'");
            check_sql_res($mysqli,'blacklisted number');

            file_put_contents('/tmp/blacklisted.history',"blacklisted number: cmp: $cmp_id num: $number black: $blacklisted \n",FILE_APPEND);

            continue;
        }
    
        $res = make_post($cmp_id,$cmp["faxfile"],$number,$force_g711);
        
        //print_r($res); echo "\n";
        if (file_exists($run_file)) {
            //    echo "Файл $run_file существует";
                $a=1;
            } else {
              //  echo "Файл $run_file не существует";
              $up_res = $mysqli->query("UPDATE companies set status = 'stoped' where idcompanies = '$cmp_id'");
              check_sql_res($mysqli,'set status to stoped in progress');
              exit();
              //break;
            }
    }


    $str ="curl -F \"post_action=stop_cmp\" -F \"cmp_id=$cmp_id\" http://127.0.0.1/stat/cmp_manage.php"; 
    exec($str);
    echo "CMP restarted";

    $str ="curl -F \"post_action=restart\" -F \"cmp_id=$cmp_id\" http://127.0.0.1/stat/cmp_manage.php"; 
    exec($str);
    echo "CMP restarted";
    
    exit;
}




//$number_list = $mysqli->query("SELECT * from numbers WHERE companieid = '$cmp_id' and status is NULL limit 20");
//check_sql_res($mysqli);

###############################
# start sending curl to fax servers
###############################



$up_res = $mysqli->query("UPDATE companies set status = 'stoped' where idcompanies = '$cmp_id'");
check_sql_res($mysqli,'set status to stoped at the end');

unlink($run_file);
echo "CMP ended";
/*
$str ="curl -F \"post_action=start_cmp\" -F \"cmp_id=$cmp_id\" http://127.0.0.1/stat/cmp_manage.php"; 
exec($str);
*/
function make_post($companie_id, $faxfilename, $number,$force_g711 = 1) {
    //$date = "start_1: ".date("Y-m-d H:i:s".substr((string)microtime(), 1, 8));
    //file_put_contents("/tmp/runtime_$companie_id",$date."\n",FILE_APPEND);
    global $mysqli;
    global $try_id;
    global $try_created;
    global $last_server_tried;
    global $last_prefix_tried;
    global $servers;
    global $trunk_prefix_arr;



    $rand = bin2hex(random_bytes(10));
    $number = preg_replace("/\+/",'',$number);
    $host = $servers[$last_server_tried];    
    $trunk_prefix = $trunk_prefix_arr[$last_prefix_tried];

    ###### save sended data into reports
    $sql = "INSERT INTO reports (
        cmp_call_id,
        cmp_id,
        number,
        try_id,
        try_created,
        trunk_prefix
    ) 
    values 
    (
        '" . $rand . "',
        '" . $companie_id . "',
        '" . $number . "',
        '" . $try_id . "',
        '" . $try_created . "',
        '" . $trunk_prefix . "'
    )";

    //echo $sql."\n";
    $res = $mysqli->query($sql);
    check_sql_res($mysqli);
    //$date = "start_2: ".date("Y-m-d H:i:s".substr((string)microtime(), 1, 8));
    //file_put_contents("/tmp/runtime_$companie_id",$date."\n",FILE_APPEND);
    //$str ="curl -F \"cmp_call_id=$rand\" -F \"num=05$number\" -F \"pdf=@files/$faxfilename\" -F \"force_g711=1\" http://".$host."/sendfax"; 
    
    if ( $trunk_prefix == "08") $force_g711 = 1;



    $str ="curl -F \"amd=off\" -F \"cmp_call_id=$rand\" -F \"num=$trunk_prefix"."$number\" -F \"pdf=@files/$faxfilename\" -F \"force_g711=$force_g711\" http://".$host."/sendfax &"; 
    //file_put_contents("/tmp/runtime_$companie_id",$str."\n",FILE_APPEND);
    //exec($str,$output,$output_code);

    $descriptorspec = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w')
    //    2 => array('pipe', 'w'),
      //  3 => array('pipe', 'w'),
        // И можно тыкать даже больше дескрипторов. Лишь бы ваша
        // дочерняя команда знала, как правильно ими пользоваться!
      );
      
      $pipes = array();
      $process = proc_open("$str", $descriptorspec, $pipes);
      // Теперь $process – это дескриптор на процесс дочерней команды.
      // $pipes – массив файл дескрипторов согласно $desciptorspec
      // спецификации. Отсюда мы можем читать или писать в
      // зависимости от направления pipe, таким образом общаясь
      // между основным PHP процессом и нашей дочерней командой.
      
      $meta_info = proc_get_status($process);
      // Теперь $meta_info содержит кучу полезной информации о
      // дочернем процессе, среди прочего текущий статус (исполняется
      // ли) и PID дочернего процесса). Детали смотрите здесь
      // http://php.net/manual/en/function.proc-get-status.php
      
      // Сообщим то, что мы хотели сообщить дочерней команде в ее STDIN.
      //fwrite($pipes[0], $stdin);
      //fclose($pipes[0]);
      
      //do_something_while_asynchronous_command_works();
      
      // Предположим, мы уже хотим знать результаты работы дочерней
      // команды.
      //$stdout = stream_get_contents($pipes[1]);
      //$stderr = stream_get_contents($pipes[2]);
      // Таким же самым образом можно считать и другие файл
      // дескрипторы, если они были открыты на чтение.
      // $fdX = stream_get_contents($pipes[$x]);
      
      foreach ($pipes as $pipe) {
        if (is_resource($pipe)) {
          fclose($pipe);
        }
      }
      
      $exit_code = proc_close($process);
      $exit_code = $meta_info['running'] ? $exit_code : $meta_info['exitcode'];

    


    //exec($str);
    //echo `$str`;
    //$date = "end___1: ".date("Y-m-d H:i:s".substr((string)microtime(), 1, 8));
    //file_put_contents("/tmp/runtime_$companie_id",$date."\n",FILE_APPEND);
    
    ####handle error from command line
   
    if ($output_code > 0) {
        Echo "host: $host - result: $output_code \n";
                
        $sql = "UPDATE reports 
                SET faxstatus = 'FAILED',
                    faxerror = 'E: $output_code H: $host',
                    sip_reason = 'SIP 601 Dial failed',
                    updated = now()

                WHERE
                cmp_call_id = '$rand' ";

    //echo $sql."\n";
    //unset($servers[$last_server_tried]);
    $res = $mysqli->query($sql);
    check_sql_res($mysqli,"Error API updatetable");
    
    }

    $last_server_tried += 1;
    if ($last_server_tried == count($servers)) $last_server_tried = 0;

    $last_prefix_tried += 1;
    if ($last_prefix_tried == count($trunk_prefix_arr)) $last_prefix_tried = 0;

    #### update current status numbers 
    $sql = "UPDATE numbers 
        SET status = 'sent'
    
    WHERE 
        companieid = '$companie_id' AND 
        number = '$number'";

    //echo $sql."\n";
    $res = $mysqli->query($sql);
    check_sql_res($mysqli,"update $number");

    //$date = "end___3: ".date("Y-m-d H:i:s".substr((string)microtime(), 1, 8));
    //file_put_contents("/tmp/runtime_$companie_id",$date."\n",FILE_APPEND);

    
    return $output_code;

}

function check_sql_res($mysqli_link, $place = 'default') {

    if ($mysqli_link->error) { 

        file_put_contents('/tmp/common_errors',"$place : " . $mysqli_link->error);


    }

}


?>