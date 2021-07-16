<?php
		$sql_calls_in_progress =  "SELECT COUNT(id) from reports where try_id = '$try_id' and updated = null";
		$sql_calls_answered =  "SELECT COUNT(id) from reports where try_id = '$try_id' and sip_reason like '%200%'";
		$sql_calls_failed =  "SELECT COUNT(id) from reports where try_id = '$try_id' and sip_reason not like '%200%'";
		$sql_fax_sent = "SELECT COUNT(id) from reports where try_id = '$try_id' and faxstatus not like '%FAILED%'";
		$sql_fax_failed = "SELECT COUNT(id) from reports where try_id = '$try_id' and faxstatus like '%FAILED%'";
		$cmp_duration = "SELECT timediff(updated,created) from reports where try_id = '$try_id'";
?>