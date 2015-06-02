<?php
$DATABASE_SERVERNAME = "localhost";
$DATABASE_USERNAME = "root";
$DATABASE_PASSWORD = "";

$DATABASE_NAME_TH = "vpay_th";
$DATABASE_NAME_TH_TABLE_TRANSACTION = "transaction";
$DATABASE_NAME_TH_TABLE_GATEWAY = "mst_gateway";

$DATABASE_SERVERNAME_SG = "112.121.158.92:6606";
$DATABASE_USERNAME_SG = "ops_ws";
$DATABASE_PASSWORD_SG = "L3sZtXjQNMP6ifxFnI";
//$DATABASE_SERVERNAME_SG = "localhost";
//$DATABASE_USERNAME_SG = "root";
//$DATABASE_PASSWORD_SG = "";

$DATABASE_NAME_SG = "vpay";
$DATABASE_NAME_SG_TABLE = "topup_tab";

$input = array(
	"dbServernameSg" => $DATABASE_SERVERNAME_SG,
	"dbUsernameSg" => $DATABASE_USERNAME_SG,
	"dbPasswordSg" => $DATABASE_PASSWORD_SG,
	"dbServername" => $DATABASE_SERVERNAME,
	"dbUsername" => $DATABASE_USERNAME,
	"dbPassword" => $DATABASE_PASSWORD,
	"dbNameSg" => $DATABASE_NAME_SG,
	"dbTableNameSg" => $DATABASE_NAME_SG_TABLE,
	"dbName" => $DATABASE_NAME_TH,
	"dbTableName" => $DATABASE_NAME_TH_TABLE_TRANSACTION,
	"dbTableNameGateway" => $DATABASE_NAME_TH_TABLE_GATEWAY
);
			
if(isset($_POST["method"]) && !empty($_POST["method"])){
	if($_POST['method'] == "confirm"){
		confirmFile();
	}else if($_POST['method'] == "match"){
		if(isset($_POST["matchDate"])){
			$fromDate = DateTime::createFromFormat("d-m-Y", $_POST["matchDate"]);
			$fromDateStr = $fromDate -> format('Y-m-d');
			
			$toDate = $fromDate -> modify('+1 day');
			$toDateStr = $toDate -> format('Y-m-d');
			
			//echo $fromDateStr;
			//echo " " . $toDateStr;
			$data_matching = matchQuery($fromDateStr,$toDateStr,$_POST["gatewayMId"],$input);
			$dataTable = generateMatchingTable($data_matching);
			
			$return_data["data"] = $dataTable;
			
			echo json_encode($return_data);
		}
	}else if($_POST['method'] == "confirmMatch"){
		if(isset($_POST["uuid"]) && isset($_POST["topup_id"]) && isset($_POST["topup_time"])){
			$result = matchConfirm($_POST["uuid"],$_POST["topup_id"],$_POST["topup_time"],$input);
			$return_data["error"] = $result;
			$return_data["uuid"] = $_POST["uuid"];
			$return_data["topup_id"] = $_POST["topup_id"];
			$return_data["topup_time"] = $_POST["topup_time"];
			
			echo json_encode($return_data);
		}
	}else if($_POST['method'] == "mstgateway"){
		$selectList["data"] = queryMstGateway($input);
		
		echo json_encode($selectList);
	}else if($_POST['method'] == "export"){
		if(isset($_POST["exportDate"])){
			$fromDate = DateTime::createFromFormat("d-m-Y", $_POST["exportDate"]);
			$fromDateStr = $fromDate -> format('Y-m-d');
			
			$data_export = exportQuery($fromDateStr,$_POST["gatewayEId"],$input);
			$dataTable = generateExportingTable($data_export);
			
			$return_data["data"] = $dataTable;
			
			echo json_encode($return_data);
		}
	}
}

function readDataFromFile($file, $gatewayFee){
	// Reading a .txt file line by line
	$line_number = 1;
	$index = 0;
	$datetime = new DateTime();
	$totalAmount = 0;
	$totalFee = 0;

	while(!feof($file)) {
		$data = fgets($file). "";
		
		if(substr($data,0,1) == "D"){
			$date = substr($data,20,8);
			$year = substr($date,4,4);
			$month = substr($date,2,2);
			$day = substr($date,0,2);
			$hour = substr($data,28,2);
			$minute = substr($data,30,2);
			$second = substr($data,32,2);
			
			$datetime = date("Y-m-d H:i:s", strtotime($year."".$month."".$day));
			$datetime = date("Y-m-d H:i:s",strtotime("+".$hour." hour +".$minute." minutes +".$second." seconds",strtotime($datetime)));
			
			$amount = substr($data,156,24)/100000;
			
			$customername = substr($data,34,50);
			$customernname =  iconv("TIS-620","UTF-8",$customername);
			
			$topup_amount = $amount - $gatewayFee;
			//$amount = number_format($amount,2);
			
			//$topup_amount = number_format($topup_amount,2);
			
			$ref1 = trim(substr($data,84,20));
			/*$check_ref = substr($ref1,0,1);
			if($check_ref == "0"){
				$ref1 = "66" . substr($ref1,1,strlen($ref1)-1);
			}*/
			
			$data_file[$index] = array(
			"nodeType" => substr($data,0,1),
			"seq" => intval(substr($data,1,6)),
			"companyId" => substr($data,7,13),
			"transDate" => $datetime,
			"customerName" => $customernname,
			"customerDBName" => $customername,
			"reference1" => $ref1,
			"reference2" => substr($data,104,20),
			"reference3" => substr($data,133,10),
			"fromAccount" => substr($data,144,8),
			"paymentType" => substr($data,152,4),
			"amount" => number_format($amount,2),
			"topup_amount" => number_format($topup_amount,2),
			"amountDB" => $amount,
			"topup_amountDB" => $topup_amount,
			"createDate" => date("Y-m-d H:i:s"),
			"raw_Data" => $data,
			"UUID" => trim(substr($data,20,14))."_".trim($ref1)."_".trim(substr($data,144,8))."_".trim(substr($data,156,24))
			);
			
			$totalAmount += substr($data,156,24)/100000;
			
			$index++;
		}else if(substr($data,0,1) == "T"){
			$amount = substr($data,42,13)/100000;
			/* All Record Header and Footer */
			$recordcount = intval(substr($data,55,3));
			$topup_amount = $amount-($recordcount * $gatewayFee);
			$data_file[$index] = array(
			"nodeType" => substr($data,0,1),
			"companyId" => substr($data,7,13),
			"transDate" => $datetime,
			"customerName" => "",
			"reference1" => "",
			"reference2" => "",
			"reference3" => "",
			"fromAccount" => "",
			"paymentType" => "Total Amount",
			"amount" => $amount,
			"topup_amount" => $topup_amount,
			"seq" => intval(substr($data,55,3)),
			"createDate" => date("Y-m-d H:i:s"),
			"raw_Data" => $data,
			"UUID" => ""
			);
			
			$index++;
		}
	}
	$totalFee = ($index-1) * $gatewayFee;
	$data = array("data_file" => $data_file,"totalAmount" => $totalAmount,"totalFee" => $totalFee);
	
	return $data;
}

function generateTable($data_file, $totalAmount, $totalFee){
	$dataTable = "";

	for($i=0; $i<count($data_file); $i++){
		$dataTable.= "<tr>";
		$dataTable.= "<td>". $data_file[$i]["nodeType"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["seq"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["transDate"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["customerName"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["reference1"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["reference2"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["reference3"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["fromAccount"] ."</td>";
		$dataTable.= "<td>". $data_file[$i]["paymentType"] ."</td>";
		$dataTable.= "<td align=\"right\">". $data_file[$i]["amount"] ."</td>";
		$dataTable.= "<td align=\"right\">". $data_file[$i]["topup_amount"] ."</td>";
		$dataTable.= "</tr>";
	}
	
	$dataTable .= "<tr><td colspan=\"8\" align=\"right\">Total all records</td><td align=\"right\">". $totalAmount ."</td><td align=\"right\">Total Fee</td><td align=\"right\">". $totalFee ."</td></tr>";
	
	return $dataTable;
}

function matchQuery($fromDate,$toDate,$gatewayId,$input){
	$conn_sg = connectDatabase($input["dbServernameSg"], $input["dbUsernameSg"], $input["dbPasswordSg"]);
	// Check connection
	if(!$conn_sg){
		die("Connection failed: " . mysqli_connect_error());
	}
	$conn_th = connectDatabase($input["dbServername"], $input["dbUsername"], $input["dbPassword"]);
	// Check connection
	if(!$conn_th){
		die("Connection failed: " . mysqli_connect_error());
	}
	//echo "===== Connected successfully =====";
	//echo "<br>";
	// Connect DB TH
	mysql_connect($input["dbServername"], $input["dbUsername"], $input["dbPassword"]) or die(mysql_error());
	mysql_select_db($input["dbName"]) or die(mysql_error());
	
	$sql_th = "SELECT * FROM ". $input["dbTableNameGateway"] ." WHERE gateway_id = ". $gatewayId;
	$result_th = mysql_query($sql_th) or die(mysql_error());
	$gatewayCode = "";
	while($row_th = mysql_fetch_array($result_th)){
		$gatewayCode = $row_th["gateway_code"];
	}
	
	$sql_th = "SELECT * FROM ". $input["dbTableName"] ." WHERE payment_type != 'CODD' AND gateway_id ='".$gatewayId."' AND (`transaction_date` BETWEEN '".$fromDate." 00:00:00' AND '". 
			$fromDate." 23:59:59') ORDER BY transaction_date, reference_1";
			//echo $sql_th;
	$result_th = mysql_query($sql_th) or die(mysql_error());
	
	// Connect DB SG
	mysql_connect($input["dbServernameSg"], $input["dbUsernameSg"], $input["dbPasswordSg"]) or die(mysql_error());
	mysql_select_db($input["dbNameSg"]) or die(mysql_error());
	
	$index = 0;
	while($row_th = mysql_fetch_array($result_th)){
		$data_table[$index] = array(
			"tableLevel" => "H",
			"vpay_uuid" => $row_th["UUID"],
			"mobile_no" => $row_th["reference_1"],
			"vpay_amount" => number_format($row_th["amount"],2),
			"vpay_topup_amount" => number_format($row_th["topup_amount"],2),
			"vpay_topup_id" => $row_th["topup_tab_id"],
			"vpay_match_status" => $row_th["match_status"],
			"vpay_time" => $row_th["transaction_date"]
		);
		//print_r($row);
		//echo "<br>";
		$index++;
		
		$uuid = $row_th["UUID"];
		$mobileNo = $row_th["reference_1"];
		$check_ref = substr($mobileNo,0,1);
		$check_ref2 = substr($mobileNo,0,3);
			if($check_ref == "0"){
				$mobileNo = "66" . substr($mobileNo,1,strlen($mobileNo)-1);
			}
			if($check_ref2 == "660"){
				$mobileNo = "66" . substr($mobileNo,3,strlen($mobileNo)-3);
			}
		$sql = "SELECT topup_tab.id, topup_tab.mobile, topup_tab.account_type, CONVERT_TZ(topup_tab.time_stamp,'+00:00','+07:00'), "
		."topup_tab.amount, topup_tab.currency, topup_tab.exchange_rate, topup_tab.rebate_rate, topup_tab.status, topup_tab.gateway, "
		."topup_tab.txn_no, topup_tab.message, topup_tab.addition_data, topup_tab.new_bal, topup_tab.previous_bal, topup_tab.retailer_id, "
		."topup_tab.dealer_id, CONVERT_TZ(topup_tab.txn_time,'+00:00','+07:00') as txn_time FROM ". $input["dbTableNameSg"] 
		." WHERE (CONVERT_TZ(txn_time,'+00:00','+07:00') BETWEEN '".$fromDate." 00:00:00.000' AND '". $toDate." 23:59:59.000') AND "
		." mobile = '". $mobileNo ."' AND amount = ". $row_th["topup_amount"] ." AND gateway = '". $gatewayCode ."' ORDER BY mobile, txn_time";
		$result = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_array($result)){
			$data_table[$index] = array(
				"tableLevel" => "I",
				"vpay_uuid" => $uuid,
				"topup_id" => $row["id"],
				"mobile_no" => $row["mobile"],
				"topup_amount" => number_format($row["amount"],2),
				"topup_gateway" => $row["gateway"],
				"topup_txn_no" => $row["txn_no"],
				"topup_new_balance" => number_format($row["new_bal"],2),
				"topup_previous_balance" => number_format($row["previous_bal"],2),
				"topup_time" => $row["txn_time"],
				"topup_retailer" => $row["retailer_id"],
				"topup_dealer" => $row["dealer_id"]
			);
			//print_r($row_th);
			//echo "<br>";
			$index++;
		}
	}
	
	closeDatabase($conn_sg);
	closeDatabase($conn_th);
	
	if($index == 0){
		return null;
	}else{
		return $data_table;
	}
}

function generateMatchingTable($data_matching){
	$dataTable = "";
	
	if(isset($data_matching) && !empty($data_matching)){
		for($i=0; $i<count($data_matching); $i++){
			$dataTable.= "<tr>";
			if($data_matching[$i]["tableLevel"] == "H"){
				if(($i+1<count($data_matching)) && ($data_matching[$i+1]["tableLevel"] == "I")){
					$dataTable.= "<td class=\"MATCH_HEADER\">". $data_matching[$i]["tableLevel"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\">". ($data_matching[$i]["vpay_match_status"] == "0" ? 
						"<img src='images/edit.png' width='25px' height='25px'/>" : "<img src='images/green_check.png' width='25px' height='25px'/>") ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\">". $data_matching[$i]["vpay_uuid"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\">". $data_matching[$i]["vpay_topup_id"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\">". $data_matching[$i]["mobile_no"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\" style=\"text-align: right;\">". $data_matching[$i]["vpay_topup_amount"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\">&nbsp;</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\" style=\"text-align: right;\">". $data_matching[$i]["vpay_amount"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER\">". $data_matching[$i]["vpay_time"] ."</td>";
				}else if(($i+1<count($data_matching)) && ($data_matching[$i+1]["tableLevel"] == "H")){
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["tableLevel"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". ($data_matching[$i]["vpay_match_status"] == "0" ? 
						"<img src='images/edit.png' width='25px' height='25px'/>" : "<img src='images/green_check.png' width='25px' height='25px'/>") ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["vpay_uuid"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["vpay_topup_id"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["mobile_no"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\" style=\"text-align: right;\">". $data_matching[$i]["vpay_topup_amount"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">&nbsp;</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\" style=\"text-align: right;\">". $data_matching[$i]["vpay_amount"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["vpay_time"] ."</td>";		
				}else if($i+1<=count($data_matching)){
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["tableLevel"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". ($data_matching[$i]["vpay_match_status"] == "0" ? 
						"<img src='images/edit.png' width='25px' height='25px'/>" : "<img src='images/green_check.png' width='25px' height='25px'/>") ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["vpay_uuid"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["vpay_topup_id"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["mobile_no"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\" style=\"text-align: right;\">". $data_matching[$i]["vpay_topup_amount"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">&nbsp;</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\" style=\"text-align: right;\">". $data_matching[$i]["vpay_amount"] ."</td>";
					$dataTable.= "<td class=\"MATCH_HEADER_ALERT\">". $data_matching[$i]["vpay_time"] ."</td>";
				}
			}else{
				$dataTable.= "<td>". $data_matching[$i]["tableLevel"] ."</td>";
				$dataTable.= "<td><input type=\"button\" value=\"Match\" onclick=\"javascript:confirmMatching('". 
					$data_matching[$i]["vpay_uuid"]."','".$data_matching[$i]["topup_id"]."','".$data_matching[$i]["topup_time"]."');\"></td>";
				$dataTable.= "<td>&nbsp;</td>";
				$dataTable.= "<td>". $data_matching[$i]["topup_id"] ."</td>";
				$dataTable.= "<td>". $data_matching[$i]["mobile_no"] ."</td>";
				$dataTable.= "<td style=\"text-align: right;\">". $data_matching[$i]["topup_amount"] ."</td>";
				$dataTable.= "<td style=\"text-align: right;\">". $data_matching[$i]["topup_previous_balance"] ."</td>";
				$dataTable.= "<td style=\"text-align: right;\">". $data_matching[$i]["topup_new_balance"] ."</td>";
				$dataTable.= "<td>". $data_matching[$i]["topup_time"] ."</td>";
			}
			$dataTable.= "</tr>";
		}
	}else{
		$dataTable.= "<tr><td colspan=9 style=\"text-align: center;\">=== No Data Found ===</td></tr>";
	}
	
	return $dataTable;
}

function matchConfirm($uuid, $topup_id, $topup_time, $input){
	$return_data = "";
	
	$conn_th = connectDatabase($input["dbServername"], $input["dbUsername"], $input["dbPassword"]);
	// Check connection
	if(!$conn_th){
		die("Connection failed: " . mysqli_connect_error());
	}
	//echo "===== Connected successfully =====";
	//echo "<br>";
	// Connect DB TH
	mysql_connect($input["dbServername"], $input["dbUsername"], $input["dbPassword"]) or die(mysql_error());
	mysql_select_db($input["dbName"]) or die(mysql_error());
	
	$sql = "UPDATE ". $input["dbTableName"] ." SET topup_tab_id = '". $topup_id ."', topup_tab_time = '". 
		$topup_time ."', match_status = '1' WHERE UUID = '". $uuid ."'";
	if(mysql_query($sql)){
		//echo "Record created successfully : UUID = ". $data_file[$i]["UUID"]. "<br>";
		//echo "UUID = ". $sql . "<br>";
		$return_data = "Record updated successfully : Topup Id = ". $topup_id;
	}else{
		//echo "Can not INSERT : QUERY <br>". $sql . "<br>" . mysql_error();
		$return_data = "Can not INSERT : QUERY <br>". $sql . "<br>" . mysql_error();
	}
	
	closeDatabase($conn_th);
	
	return $return_data;
}

function queryMstGateway($input){
	$return_data = "";
	
	$conn_th = connectDatabase($input["dbServername"], $input["dbUsername"], $input["dbPassword"]);
	// Check connection
	if(!$conn_th){
		die("Connection failed: " . mysqli_connect_error());
	}
	//echo "===== Connected successfully =====";
	// Connect DB TH
	mysql_connect($input["dbServername"], $input["dbUsername"], $input["dbPassword"]) or die(mysql_error());
	mysql_select_db($input["dbName"]) or die(mysql_error());
	
	$sql = "SELECT * FROM ". $input["dbTableNameGateway"];
	$result = mysql_query($sql) or die(mysql_error());
	
	while($row = mysql_fetch_array($result)){
		$return_data .= "<option value=\"". $row["gateway_id"] ."\">". $row["gateway_name"] ."</option>";
	}
	
	closeDatabase($conn_th);
	
	return $return_data;
}

function exportQuery($fromDate,$gatewayId,$input){
	$conn_th = connectDatabase($input["dbServername"], $input["dbUsername"], $input["dbPassword"]);
	// Check connection
	if(!$conn_th){
		die("Connection failed: " . mysqli_connect_error());
	}
	//echo "===== Connected successfully =====";
	// Connect DB TH
	mysql_connect($input["dbServername"], $input["dbUsername"], $input["dbPassword"]) or die(mysql_error());
	mysql_select_db($input["dbName"]) or die(mysql_error());
	
	$sql_th = "SELECT * FROM ". $input["dbTableName"] ." WHERE (transaction_date BETWEEN '".$fromDate." 00:00:00.000' and '". 
			$fromDate." 23:59:59.000') AND gateway_id = ". $gatewayId ." ORDER BY transaction_date";
	$result_th = mysql_query($sql_th) or die(mysql_error());
	
	$index = 0;
	while($row_th = mysql_fetch_array($result_th)){
		$datetime = DateTime::createFromFormat("Y-m-d H:i:s", $row_th["transaction_date"]);
		if($row_th["match_status"]){
			$cyberpayref = "Cyberpay_".$row_th["topup_tab_id"];
		}else{
			$cyberpayref = "-";
		}
		$data_table[$index] = array(
			"tran_date" => $datetime -> format('Y-m-d'),
			"tran_time" => $datetime -> format('H:i:s'),
			"ref1" => $row_th["reference_1"],
			"ref2" => $row_th["reference_2"],
			"retailer_name" => $row_th["customer_name"],
			"report_amount" => number_format($row_th["amount"],2),
			"cbp_amount" => number_format($row_th["topup_amount"],2),
			"method" => $row_th["payment_type"],
			"status" => $row_th["match_status"],
			"topup_date" => $row_th["topup_tab_time"],
			"cbp_ref" => $cyberpayref
		);
		//print_r($row);
		//echo "<br>";
		$index++;
	}
	
	closeDatabase($conn_th);
	
	if($index == 0){
		return null;
	}else{
		return $data_table;
	}
}

function generateExportingTable($data){
	$dataTable = "";
	
	if(isset($data) && !empty($data)){
		for($i=0; $i<count($data); $i++){
			$dataTable.= "<tr>";
			
			$dataTable.= "<td>". $data[$i]["tran_date"] ."</td>";
			$dataTable.= "<td>". $data[$i]["tran_time"] ."</td>";
			$dataTable.= "<td>". $data[$i]["ref1"] ."</td>";
			$dataTable.= "<td>". $data[$i]["ref2"] ."</td>";
			$dataTable.= "<td>". $data[$i]["retailer_name"] ."</td>";
			$dataTable.= "<td style=\"text-align: right;\">". $data[$i]["report_amount"] ."</td>";
			$dataTable.= "<td style=\"text-align: right;\">". $data[$i]["cbp_amount"] ."</td>";
			$dataTable.= "<td>". $data[$i]["method"] ."</td>";
			$dataTable.= "<td>&nbsp;</td>";
				$dataTable.= "<td>". $data[$i]["status"] ."</td>";
				$dataTable.= "<td>". $data[$i]["topup_date"] ."</td>";
				$dataTable.= "<td>". $data[$i]["cbp_ref"] ."</td>";

			$dataTable.= "</tr>";
		}
	}else{
		$dataTable.= "<tr><td colspan=12 style=\"text-align: center;\">=== No Data Found ===</td></tr>";
	}
	
	return $dataTable;
}

function connectDatabase($servername, $username, $password){
	// Create connection
	$conn = mysqli_connect($servername, $username, $password);
	return $conn;
}

function closeDatabase($conn){
	//close the connection
	mysqli_close($conn);
}
?>
