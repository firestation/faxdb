<?php
export($_POST);

if (!$fail2ok) exit;
require_once('vendor/autoload.php');
session_start();

$ini_array = parse_ini_file("/etc/faxweb.ini");

$mysqli = new mysqli($ini_array["host"], $ini_array["user"], $ini_array["password"], $ini_array["db"]);
if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$res = $mysqli->qeury("SELECT * from numbers where idcompanie = '$cmp_id");
$res_failed = 
?>