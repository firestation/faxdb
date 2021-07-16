<?php
ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
ini_set('error_log','syslog');

//require_once('Smarty/Smarty.class.php');
require_once('vendor/autoload.php');
session_start();

$ini_array = parse_ini_file("/etc/faxweb.ini");


if ($ini_array === false) {
	echo "Error readin configuration file. Exit";
	exit;
}

//var_dump($_SESSION);
//var_dump($ppm_avg);
$ppm_avg = $ini_array["ppm_avg"];
$limit_try = 10;

//$show_only = 'CHECKED'; //to make default erport for companie

//var_dump($_POST);

extract($_POST);
extract($_GET);

if (!isset($show_disabled_cmp)) $show_disabled_cmp = ''; //to make default erport for companie

if ($_SESSION["error"]) echo '<p id = "error_message" style="color: red">'.$_SESSION["error"]."<p>";

//var_dump($_SERVER);
//var_dump($_FILES);
//var_dump($_FILES["filenums"]['name']);


//uploading files from add_new_form



if ($logout == "yes") {
	session_destroy();
	session_start();
}

$webuser 		 = 'admin';
$webuserpassword = 'AKb82C6FGSB1';

if ($password == $webuserpassword && $username == $webuser) {	
	$_SESSION['username']=$username;
}

if(!$_SESSION['username']) {
	$smarty = new Smarty();
	
	$smarty->assign('file_to_load','login.tpl');
	$smarty->display('Untitled-1.html');
	
	exit;
}



$ip2graph = "No IP choosen";



#define('SMARTY_DIR', '/usr/local/lib/php/Smarty-v.e.r/libs/');
//include "load.php";
//include "limits2.php";



$smarty = new Smarty();
//check if script take error
if ($error) {
	
	$smarty->assign("result","(Error) Some sql  error present at $error ");
	$smarty->display('result.tpl');

	exit;
}

$mysqli = new mysqli($ini_array["host"], $ini_array["user"], $ini_array["password"], $ini_array["db"],$ini_array["port"]);
if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
//$smarty->debugging = TRUE;

if ($post_action=="set_g711") {
	$res = $mysqli->query("update companies set force_g711 = '$input_set_g711' where idcompanies = $cmp_id");
	check_sql_res($mysqli, "Error when update companie $cmp_id");

	//header("Location: ".$_SERVER['HTTP_REFERER']);
	//exit;
}

#create array $loads [0,0,0]
if ($post_action=="set_prefix") {
	$res = $mysqli->query("update companies set trunk_prefix = '$input_set_prefix' where idcompanies = $cmp_id");
	check_sql_res($mysqli, "Error when update companie $cmp_id");

	//header("Location: ".$_SERVER['HTTP_REFERER']);
	//exit;
}

//delete companie
if ($post_action=="delete") {
	$res = $mysqli->query("delete from companies where idcompanies = $post_id");
	check_sql_res($mysqli, "Error when delete companie $post_id");
	
	$res = $mysqli->query("delete from numbers where companieid = $post_id");
	check_sql_res($mysqli, "Error to clean numebrs for companie $post_id");

	//header("Location: ".$_SERVER['HTTP_REFERER']);
	//exit;
}

//disable company

if ($post_action=="disable-enable") {
	$res = $mysqli->query("update companies set disabled = $post_data where idcompanies = '$post_id'");
	check_sql_res($mysqli, "enable-disable");
	
	//header("Location: ".$_SERVER['HTTP_REFERER']);
	
	//exit;
}
################################
## ADding new cmp with numbers and fax file
################################
//upload files and create new cmp

if ($post_action=="add_new_cmp") {

	$uploaddir = '/var/www/html/stat/files/';
	$uploadfile = $uploaddir . basename($_FILES["filenums"]['name']);
	
	if (move_uploaded_file($_FILES["filenums"]['tmp_name'], $uploadfile)) {
		echo "Файл корректен и был успешно загружен.\n";
	} else {
		echo "Возможная атака с помощью файловой загрузки! Numbers.".$_FILES["filenums"]['tmp_name'].":".$uploadfile;

		exit;
	}
	$numbers = file("$uploadfile");
	//var_dump($numbers);
	
	
	$uploadfile = $uploaddir . basename($_FILES["filefax"]['name']);
	if (move_uploaded_file($_FILES["filefax"]['tmp_name'], $uploadfile)) {
		echo "Файл корректен и был успешно загружен.<br>";
	} else {
		echo "Возможная атака с помощью файловой загрузки! Fax.<br>".$_FILES["filefax"]['tmp_name'].":".$uploadfile;
	
		exit;
	}

	$res = $mysqli->query("insert into companies (name,faxfile,status,trunk_prefix,force_g711) values ('".$cmp_name."','".$_FILES["filefax"]['name']."','created','".$add_trunk_prefix."','".$add_force_g711."')");
	$cmp_id = $mysqli->insert_id;
	check_sql_res($mysqli, "add companie");
	
	$str = "insert into numbers (number,companieid) values ('100','0')";
	foreach ($numbers as $num) {
		$num = filter_var($num,FILTER_SANITIZE_NUMBER_INT);
		

		$num = preg_replace('/^\+(\d+)/','$1',$num);
		$num = preg_replace('/\-/','',$num);

		
		//$check = $mysqli->fetch();
		
			$str .= ",('".$num."','".$cmp_id."')";

		
			//$res = $mysqli->query("insert into numbers (number,companieid) values ('".$num."','".$cmp_id."')");
			//echo "$num,$cmp_id<br>";
			//check_sql_res($mysqli, "add numbers for cmp id $cmp_id");
	}

	$mysqli->query($str);
	check_sql_res($mysqli, "add numbers for cmp id $cmp_id");

	header("Location: " . $_SERVER['PHP_SELF'] . "?main_page=companies");
	exit;
	
}

//prepare companies table

if ($main_page=="settings") {

	if ($post_action == "change_settings") {
		file_put_contents('/etc/faxweb.ini',$text_settings);
	}

	//echo "$text_settings";
	$settings = file_get_contents('/etc/faxweb.ini');
	$smarty->assign('settings',$settings);


}

if ($main_page=="companies") {

		$sql_cmp_add = ' AND disabled < 1';
	if ($show_disabled_cmp) $sql_cmp_add = '';
	//get online channels



	$res_companies = $mysqli->query("select * from companies WHERE 1=1 $sql_cmp_add order by created desc limit 5");
	//colums 
	check_sql_res($mysqli, "Get companies");

	while ($row = $res_companies->fetch_assoc()) {
		
		$sql = "select count(*) from numbers where companieid = ".$row["idcompanies"];
		$res_numbers = $mysqli->query($sql);
		check_sql_res($mysqli, "count of numbers");
		$count_numbers = $res_numbers->fetch_row();

		$sql = "select count(*) from numbers where companieid = ".$row["idcompanies"]." AND status is not null";
		$res_numbers = $mysqli->query($sql);
		check_sql_res($mysqli, "count of numbers");
		$count_sent_numbers = $res_numbers->fetch_row();

	    //printf("%s (%s)\n", $row["name"], $row["faxfile"]);
		if ($row["disabled"] == 1) $row["disabled"] = 'checked';

		$isstarted = "START";

		if(file_exists("/tmp/" . $row["idcompanies"] . ".runing"))
			$isstarted = 'STARTED';

		$cmp_table[] = [$row["idcompanies"],$row["name"], $row["faxfile"],$count_numbers[0],$row["status"],$row["disabled"],$row["created"],$row["trunk_prefix"],$row["force_g711"],'sent' => $count_sent_numbers[0],'started' => $isstarted];
		
	}


	//sleep(2);
	//header("Location: ".$_SERVER['REQUEST_URI']);

	$smarty->assign('cmp_table',$cmp_table);
}

###############################
#Show numbers
###############################


if ($main_page=="numbers") {
	//get cmp names
	$res_numbers = $mysqli->query("select idcompanies,name from companies");
	//colums 
	check_sql_res($mysqli, "Get cmp_names");
	while ($row = $res_numbers->fetch_assoc()) {
		$cmp_names[$row["idcompanies"]] = $row["name"]; 
	}




	$conditions = 'Where 1=1';
	if ($filter_num) $conditions .= " and number like '$filter_num%'";
	if ($filter_cmp) $conditions .= " and companieid like '$filter_cmp%'";
	//var_dump($_POST);


	//order by 

	//group condition
	if($group_by_num) $group_cols[] = "number";
	if($group_by_cmp) $group_cols[] = "companieid";

	if ($group_cols)
		$conditions .= " group by ".implode(",",$group_cols);
	


	$res_numbers = $mysqli->query("select * from numbers $conditions limit 10000");
	//colums 
	check_sql_res($mysqli, "Get numbers");
	
	while ($row = $res_numbers->fetch_assoc()) {
	
		//$sql = "select count(*) from numbers $conditions";
		//$res_numbers = $mysqli->query($sql);
		//check_sql_res($mysqli, "count of numbers");
		//$count_numbers = $res_numbers->fetch_row();
		
	
		//printf("%s (%s)\n", $row["name"], $row["faxfile"]);

		//$num_table[] = [$row["id"],$row["created"], $row["number"],$row["companieid"],$row["status"]];
		$num_table[] = ["id" 		=> $row["id"],
						"created" 	=> $row["created"], 
						"number" 	=> $row["number"],
						"companieid" => $row["companieid"],
						"status" 	=> $row["status"],
						"cmp_name"  => $cmp_names[$row["companieid"]]
					];
	}
			//sleep(2);
		//header("Location: ".$_SERVER['REQUEST_URI']);
		$smarty->assign('filter_num',$filter_num);
		$smarty->assign('filter_cmp',$filter_cmp);
		if ($group_by_cmp) $smarty->assign('group_by_cmp',"checked");
		if ($group_by_num) $smarty->assign('group_by_num',"checked");
		$smarty->assign('num_table',$num_table);
}	
	##########################
	## Report generate
	###########################

if ($main_page=="reports") {
		//take actions
		//delete task
		while (isset($del_try_id)) {
			$res_del = $mysqli->query("DELETE from reports where try_id = '$del_try_id'");
			check_sql_res($mysqli, "del tasks");
				
			break;
		}

		//Stop companie
		while (isset($stop_cmp)) {
			unlink("/tmp/$stop_cmp.running");
			break;
		}


		//exports calls
		while (isset($export_try_id)) {
			
			$res_export = $mysqli->query("SELECT * from reports where try_id = '$export_try_id'");
			check_sql_res($mysqli, "export tasks");

			header("Content-type: text/csv");
			header("Cache-Control: no-store, no-cache");
			header('Content-Disposition: attachment; filename="content_try.csv"');
			
			$file = fopen('php://output','w');
			$headers = $res_export->fetch_fields();
			foreach ($headers as $header)
				$fields[] = $header->name;

			fputcsv($file,$fields,";");
			while ($row = $res_export->fetch_row()) {
				//$calls[] = $row;
				fputcsv($file,$row,";");	
			}
			//fputcsv($file,$calls); // $arr is my array that contains the data to be parsed into CSV
			fclose($file);
			exit;	
			break;
		}
				//exports calls
		while (isset($export_cmp_id)) {
	
			$res_export = $mysqli->query("SELECT * from reports where cmp_id = '$export_cmp_id' and sip_reason = 'SIP 200 OK'");
			check_sql_res($mysqli, "export companie");

			header("Content-type: text/csv");
			header("Cache-Control: no-store, no-cache");
			header('Content-Disposition: attachment; filename="content_cmp_'.$export_cmp_id.'.csv"');
			
			$file = fopen('php://output','w');
			$headers = $res_export->fetch_fields();
			foreach ($headers as $header)
				$fields[] = $header->name;

			fputcsv($file,$fields,";");
			while ($row = $res_export->fetch_row()) {
				//$calls[] = $row;
				fputcsv($file,$row,";");	
			}
			//fputcsv($file,$calls); // $arr is my array that contains the data to be parsed into CSV
			fclose($file);
			
			exit;	
			break;
		}




		if (isset($try_id_report))
			$sql_condition = " AND try_id = '$try_id_report'";

		//get companie tasks started\finished

		if ($show_only == "running_try") {
	
			$limit_try = 1;
			$smarty->assign('show_running_try',"checked");

			$res_tasks = $mysqli->query("SELECT cmp_id,try_id, min(try_created) as started,max(updated) as finished 
										from reports 
										WHERE 1=1 $sql_condition 
										group by cmp_id,try_id 
										order by try_created desc limit $limit_try");
			
			check_sql_res($mysqli, "Get tasks");
			while ($row = $res_tasks->fetch_assoc()) {
				$add_stat = get_try_stat($row["try_id"]);
			
				$row = array_merge($row, $add_stat);
				$tasks[] = $row;
			
				}
		}



		if ($show_only == "try") {
			$smarty->assign('show_only_try',"checked");
			$res_tasks = $mysqli->query("SELECT cmp_id,try_id ,min(try_created) as started,max(updated) as finished 
										from reports 
										WHERE 1=1 $sql_condition 
										group by cmp_id,try_id 
										order by try_created desc limit $limit_try");
			
			check_sql_res($mysqli, "Get tasks");
			while ($row = $res_tasks->fetch_assoc()) {
				$add_stat = get_try_stat($row["try_id"]);
			
				$row = array_merge($row, $add_stat);
				$tasks[] = $row;
			
				}
		}
		
		
		if ($show_only == "companie") {
			$res_tasks = $mysqli->query("SELECT cmp_id, min(try_created) as started,max(updated) as finished 
										from reports 
										group by cmp_id 
										order by try_created desc");
			$smarty->assign('show_only_cmp',"checked");
			
		
			check_sql_res($mysqli, "Get tasks");
			while ($row = $res_tasks->fetch_assoc()) {

				if ($show_only == "companie") 
					$add_stat = get_cmp_stat($row["cmp_id"]);

				$row = array_merge($row, $add_stat);
				$tasks[] = $row;

			}
		}	
		
	

	
	
		while (isset($cmp_id_report)) {
			//get pagination for calls table
			//var_dump($_POST);
			$limit_per_page = $ini_array["limit_per_page"];
			
			$res_report = $mysqli->query("select count(*) as row_number from reports where cmp_id = '$cmp_id_report' and sip_reason = 'SIP 200 OK'");
			$count = $res_report->fetch_assoc();
			//var_dump($count);
			$maxpages = $count["row_number"]/$limit_per_page+1;
			
			if (!isset($set_page)) $set_page = "1";
			//var_dump($set_page);
			$set_page_offset = (($set_page-1) * $limit_per_page);
			$res_report = $mysqli->query("select * from reports where cmp_id = '$cmp_id_report' and sip_reason = 'SIP 200 OK' order by created desc limit $limit_per_page offset $set_page_offset");
			check_sql_res($mysqli, "Get reports");
			
			$a = 1;
			//echo $maxpages;
			while ($a <= $maxpages) {
				$pages[] = ["num" => $a ];
				$a++;
			}
		
			$prevision_page = $set_page;
			if ($set_page > 1)
				$prevision_page = $set_page-1;
			
			//colums 
			
			while ($row = $res_report->fetch_assoc()) {
				$reports[] = $row;
			}
			break;
		}

		//detailed_report_for_try
		while (isset($try_id_report)) {
			//get pagination for calls table
			
			

			$detail_report = $mysqli->query("SELECT count(*) as count,sum(talk_time)/60 as mins,faxstatus,faxerror,sip_reason 
											from reports 
											where try_id = '$try_id_report' 
											group by faxerror,faxstatus,sip_reason 
											order by mins desc");
			
					
			
			while ($details_row = $detail_report->fetch_assoc()) {
				$details[] = $details_row;
			}
			$smarty->assign('details',$details);
			break;
		}

		//table with calls
		while (isset($try_id_report)) {
			//get pagination for calls table
			
			$limit_per_page = $ini_array["limit_per_page"];

			$res_report = $mysqli->query("select count(*) as row_number from reports where try_id = '$try_id_report' AND faxstatus = 'SUCCESS'");
			$count = $res_report->fetch_assoc();
			//var_dump($count);
			$maxpages = $count["row_number"]/$limit_per_page+1;

			if (!isset($set_page)) $set_page = "1";
			//var_dump($set_page);
			$set_page_offset = (($set_page-1) * $limit_per_page);
			$res_report = $mysqli->query("select * from reports where try_id = '$try_id_report' AND faxstatus = 'SUCCESS' order by created desc limit $limit_per_page offset $set_page_offset");
			check_sql_res($mysqli, "Get reports");

			$a = 1;
			//echo $maxpages;
			while ($a <= $maxpages) {
				$pages[] = ["num" => $a ];
				$a++;
			}

			$prevision_page = $set_page;
			if ($set_page > 1)
				$prevision_page = $set_page-1;

			//colums 

			while ($row = $res_report->fetch_assoc()) {
				$reports[] = $row;
			}
			break;
		}
		

		$smarty->assign('filter_num',$filter_num);
		$smarty->assign('filter_cmp',$filter_cmp);
		if ($group_by_cmp) $smarty->assign('group_by_cmp',"checked");
		if ($group_by_num) $smarty->assign('group_by_num',"checked");
		$smarty->assign('pages',$pages);
		$smarty->assign('prevision_page',$prevision_page);
		$smarty->assign('tasks',$tasks);
		$smarty->assign('try_id',$try_id_report);
		$smarty->assign('cmp_id',$cmp_id_report);
		
		$smarty->assign('reports',$reports);
	}



	
		######################
		#main block to show pages
		#########################
 	if (!isset($main_page)) $main_page="dashboard";
	
	 
	unset($_SESSION["error"]);
	$main_page .= '.tpl';
	if (!file_exists($main_page)) $main_page = "dashboard.tpl";
	$smarty->assign('hide_disabled_cmp',$hide_disabled_cmp);
	$smarty->assign('main_page',$main_page);
	$smarty->assign('file_to_load','page.tpl');

	$smarty->display('Untitled-1.html');

function check_sql_res($mysqli_link, $place = 'default') {

		if ($mysqli_link->error) { 

			$_SESSION["error"] = $place.":".$mysqli_link->error;

		}

}
function get_cmp_stat ($cmp_id) {
	global $mysqli;
	global $ppm_avg;

		$sql_calls_in_progress =  "SELECT COUNT(id) as count from reports where cmp_id = '$cmp_id' and sip_reason is NULL";
		$sql_calls_answered =  "SELECT COUNT(id) as count from reports where cmp_id = '$cmp_id' and sip_reason like '%200%'";
		$sql_calls_failed =  "SELECT COUNT(id) as count from reports where cmp_id = '$cmp_id' and sip_reason not like '%200%'";
		$sql_fax_sent = "SELECT COUNT(id) as count from reports where cmp_id = '$cmp_id' and faxstatus not like '%FAILED%'";
		$sql_fax_failed = "SELECT COUNT(id) as count from reports where cmp_id = '$cmp_id' and faxstatus like '%FAILED%' and talk_time > 0";
		$cmp_duration = "SELECT timediff(max(updated),created) as cmp_duration from reports where cmp_id = '$cmp_id'";
		$cmp_talk_duration = "SELECT sum(talk_time) as cmp_talk_duration from reports where cmp_id = '$cmp_id'";


		$res_report = $mysqli->query($sql_calls_in_progress);
		check_sql_res($mysqli, "Get in progress");
		
		$res = $res_report->fetch_assoc();
	
		$result_array["calls_in_progress"] = $res["count"];
			
		$res_report = $mysqli->query($sql_calls_answered);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["calls_answered"] = $res["count"];
		
		$res_report = $mysqli->query($sql_calls_failed);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["calls_failed"] = $res["count"];
		
	
		$res_report = $mysqli->query($sql_fax_sent);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["fax_sent"] = $res["count"];
	
		$res_report = $mysqli->query($sql_fax_failed);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["fax_failed"] = $res["count"];

		$res_report = $mysqli->query($cmp_duration);
		check_sql_res($mysqli, "Get in progress");
				$res = $res_report->fetch_assoc();
				$result_array["cmp_duration"] = $res["cmp_duration"];

		$res_report = $mysqli->query($cmp_talk_duration);
		check_sql_res($mysqli, "Get talk time");
		$res = $res_report->fetch_assoc();

		$result_array["cmp_talk_duration"] = round($res["cmp_talk_duration"]/60,2);
		$result_array["cmp_total_cost"] = $result_array["cmp_talk_duration"]*$ppm_avg;
		$result_array["cmp_price_per_fax"] =  round($result_array["cmp_total_cost"]/$result_array["fax_sent"],5);
		if ($result_array["fax_sent"] == 0) 
			$result_array["cmp_price_per_fax"] = $result_array["cmp_total_cost"];
			
		$result_array["sent_percent"] = round($result_array["fax_sent"]/$result_array["calls_answered"]*100,4);
		$result_array["success_calls_percent"] = round($result_array["calls_answered"]/($result_array["calls_answered"]+$result_array["calls_failed"])*100,4);	
		
		return $result_array;
	}

function get_try_stat ($try_id_report) {
	global $mysqli;
	global $ppm_avg;

		$sql_calls_in_progress =  "SELECT COUNT(id) as count from reports where try_id = '$try_id_report' and sip_reason is NULL";
		$sql_calls_answered =  "SELECT COUNT(id) as count from reports where try_id = '$try_id_report' and sip_reason like '%200%'";
		$sql_calls_failed =  "SELECT COUNT(id) as count from reports where try_id = '$try_id_report' and sip_reason not like '%200%'";
		$sql_fax_sent = "SELECT COUNT(id) as count from reports where try_id = '$try_id_report' and faxstatus not like '%FAILED%'";
		$sql_fax_failed = "SELECT COUNT(id) as count from reports where try_id = '$try_id_report' and faxstatus like '%FAILED%' and talk_time > 0";
		$cmp_duration = "SELECT timediff(max(updated),created) as cmp_duration from reports where try_id = '$try_id_report'";
		$cmp_talk_duration = "SELECT sum(talk_time) as cmp_talk_duration from reports where try_id = '$try_id_report'";
	


		$res_report = $mysqli->query($sql_calls_in_progress);
		check_sql_res($mysqli, "Get in progress");
		
		$res = $res_report->fetch_assoc();
	
		$result_array["calls_in_progress"] = $res["count"];
			
		$res_report = $mysqli->query($sql_calls_answered);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["calls_answered"] = $res["count"];
		
		$res_report = $mysqli->query($sql_calls_failed);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["calls_failed"] = $res["count"];
		
	
		$res_report = $mysqli->query($sql_fax_sent);
		check_sql_res($mysqli, "Get sent faxes");
		$res = $res_report->fetch_assoc();
		$result_array["fax_sent"] = $res["count"];
	
		$res_report = $mysqli->query($sql_fax_failed);
		check_sql_res($mysqli, "Get in progress");
		$res = $res_report->fetch_assoc();
		$result_array["fax_failed"] = $res["count"];

		$res_report = $mysqli->query($cmp_duration);
		check_sql_res($mysqli, "Get in progress");
				$res = $res_report->fetch_assoc();
				$result_array["cmp_duration"] = $res["cmp_duration"];

		$res_report = $mysqli->query($cmp_talk_duration);
		check_sql_res($mysqli, "Get talk time");
		$res = $res_report->fetch_assoc();
		$result_array["cmp_talk_duration"] = round($res["cmp_talk_duration"]/60,2);
		$result_array["cmp_total_cost"] = $result_array["cmp_talk_duration"]*$ppm_avg;
		$result_array["cmp_price_per_fax"] =  round($result_array["cmp_total_cost"]/$result_array["fax_sent"],5);
		if ($result_array["fax_sent"] == 0) 
			$result_array["cmp_price_per_fax"] = $result_array["cmp_total_cost"];

		$result_array["sent_percent"] = round($result_array["fax_sent"]/$result_array["calls_answered"]*100,4);
		$result_array["success_calls_percent"] = round($result_array["calls_answered"]/($result_array["calls_answered"]+$result_array["calls_failed"])*100,4);

		return $result_array;
	}

?>