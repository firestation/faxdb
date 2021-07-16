<?php
$script_name = " faxapi: ";
$date = date("Y-m-d H:i:s");
$log_start = $date . $script_name;
session_start();
//var_dump($_POST);

$ini_array = parse_ini_file("/etc/faxweb.ini");

$mysqli = new mysqli($ini_array["host"], $ini_array["user"], $ini_array["password"], $ini_array["db"]);
if ($mysqli->connect_error) {
    $msg[] = 'Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
    die('Connection Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$date = date("Y-m-d H:i:s");
$msg[] = print_r($_POST,true);
    if (filter_var($_POST['ring_time'],FILTER_VALIDATE_INT) === false)
        $_POST['ring_time'] = '0';

    if (filter_var($_POST['talk_time'],FILTER_VALIDATE_INT) === false)
        $_POST['talk_time'] = '0';

    if ( $_POST['sip_reason'] == 'SIP 200 OK' && $_POST['talk_time'] == '0' )
         $_POST['talk_time'] == '1';

    $sql = "UPDATE reports SET
    
            uniqueid   = '" . $_POST['uniqueid'] . "', 
            faxstatus  = '" . $_POST['faxstatus'] . "',
            faxmode    = '" . $_POST['faxmode'] . "',
            faxpages   = '" . $_POST['faxpages'] . "',
            faxerror   = '" . $_POST['faxerror'] . "',
            host_id    = '" . $_POST['host_id'] . "',
            sip_code   = '" . $_POST['sip_code'] . "' ,
            sip_reason = '" . $_POST['sip_reason'] . "',
            ring_time = '" . $_POST['ring_time'] . "',
            talk_time = '" . $_POST['talk_time'] . "',
            updated    = '" . $date . "'
        WHERE 
            cmp_call_id = '". $_POST['cmp_call_id'] . "'";

    //$msg[] = $sql;
    $res = $mysqli->query($sql);
    check_sql_res($mysqli);


    file_put_contents('/tmp/faxapi_new.log',$msg,FILE_APPEND);

function check_sql_res($mysqli_link, $place = 'default') {
    global $msg;
    if ($mysqli_link->error) { 
        $msg[] = $place.":".$mysqli_link->error;
        //echo $place.":".$mysqli_link->error;

    }

}

//echo "OK";

?>