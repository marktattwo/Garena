<?php
session_start();
include 'config/config.php';
$input = array(
	"dbServernameCFT" => $DATABASE_SERVERNAME_TH_CFT,
	"dbUsernameCFT" => $DATABASE_USERNAME_TH_CFT,
	"dbPasswordCFT" => $DATABASE_PASSWORD_TH_CFT,
	"dbNameCFT" => $DATABASE_NAME_TH_CFT,
	"dbTableNameCFT" => $DATABASE_NAME_TH_TABLE_CFT,
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

function connectDatabase($servername, $username, $password){
		// Create connection
		$conn = mysql_connect($servername, $username, $password);
		return $conn;
}

function closeDatabase($conn){
		//close the connection
		mysqli_close($conn);
}





function retailer_giro_data($conn_sg,$db_name,$retailer_id) {
	mysql_select_db($db_name,$conn_sg) or die(mysql_error());
	$sql = "
		SELECT
giro_user_tab.bank,
giro_user_tab.account,
retailer_tab.mobile,
retailer_tab.first_name,
retailer_tab.last_name

FROM
retailer_tab
INNER JOIN giro_user_tab ON giro_user_tab.retailer_id = retailer_tab.id
WHERE giro_user_tab.retailer_id='".$retailer_id."'";
		$GIRO_List = mysql_query($sql,$conn_sg) or die(mysql_error()); 
		$Bank_List = "";
	while($row = mysql_fetch_array($GIRO_List)){
		if($row["bank"]=="1"){
			$Bank_List .= "SCB (".$row['account'].")<br>";
		}else if($row["bank"]=="2"){
			$Bank_List .= "KTB (".$row['account'].")<br>";
		}else if($row["bank"]=="3"){
			$Bank_List .= "BBL (".$row['account'].")<br>";
		}else if($row["bank"]=="4"){
			$Bank_List .= "KBANK (".$row['account'].")<br>";
		}	
	}
	return $Bank_List;
}

function get_cafe_list($conn_sg,$db_name,$retailer_id) {
	mysql_select_db($db_name,$conn_sg) or die(mysql_error());
	$sql = "
		SELECT
cafe_tab.cybercafe_id
FROM
cafe_tab
WHERE owner_id='".$retailer_id."'";
	$Cafe_List = mysql_query($sql,$conn_sg) or die(mysql_error()); 
	return $Cafe_List;
}

function get_cafe_pc($conn,$db_name,$retailer_id,$cafelist) {
		$list = "";
		$numResults = "0";
		while($row = mysql_fetch_array($cafelist)){
			if(is_numeric($row["cybercafe_id"])){
				$list .= $row["cybercafe_id"].",";
			}
		}
		//echo $list;
		$list = substr($list, 0, -1);
		//echo $list;
		mysql_select_db($db_name,$conn) or die(mysql_error());
		mysql_query("SET character_set_results=utf8");
		mysql_query("SET character_set_client=utf8");
		mysql_query("SET character_set_connection=utf8");
		if($list!=""){
			$sql = "SELECT
			SUM(report_today.number_of_pc) as num_of_pc,
			SUM(report_today.max_online) as bill_online,
			SUM(report_today.diskless_pcs) as disk_online
			FROM
			report_today
			WHERE
			cafe_id IN (".$list.")";
			$Cafe_PC = mysql_query($sql,$conn) or die(mysql_error()); 
			//echo $sql;
			$numResults =  mysql_fetch_assoc($Cafe_PC);
			//echo "<br>".$numResults."<br>";
			if($numResults["num_of_pc"]!=null){
				$Cafe_PC = mysql_query($sql,$conn) or die(mysql_error()); 
				return $Cafe_PC;
			}else{
				//echo "number";
				return "0";
			}
			
		}else{
			return "0";
			
		}
}




if($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$retailer = $_POST["retailer"];
	
	$conn_sg = connectDatabase($input["dbServernameSg"], $input["dbUsernameSg"], $input["dbPasswordSg"]);
	if(!$conn_sg){
		die("Connection failed: " . mysqli_connect_error());
	}
	$conn_cft = connectDatabase($input["dbServernameCFT"], $input["dbUsernameCFT"], $input["dbPasswordCFT"]);
	// Check connection
	if(!$conn_cft){
		die("Connection failed: " . mysqli_connect_error());
	}
	
	
	mysql_select_db($input["dbNameSg"],$conn_sg) or die(mysql_error());
		mysql_query("SET character_set_results=utf8",$conn_sg);
		mysql_query("SET character_set_client=utf8",$conn_sg);
		mysql_query("SET character_set_connection=utf8",$conn_sg);
		$sql = "
		SELECT
Sum(if(
			CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') between DATE_SUB(NOW(), INTERVAL 1 MONTH) and NOW()
			, price, 0)) AS 1Month,
Sum(if(
			CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') between DATE_SUB(NOW(), INTERVAL 2 MONTH) and DATE_SUB(NOW(), INTERVAL 1 MONTH)
			, price, 0)) AS 2Month,
Sum(if(
			CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') between DATE_SUB(NOW(), INTERVAL 3 MONTH) and DATE_SUB(NOW(), INTERVAL 2 MONTH)
			, price, 0)) AS 3Month,
Sum(if(
			CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') between DATE_SUB(NOW(), INTERVAL 3 MONTH) and NOW()
			, price, 0)) AS AllMonth,
retailer_tab.id,
retailer_tab.mobile,
retailer_tab.first_name,
retailer_tab.last_name,
retailer_tab.country,
retailer_tab.location,
retailer_tab.address,
retailer_tab.balance,
retailer_tab.`status`,
retailer_tab.date_created,
cafe_tab.location,
cafe_tab.area
FROM
purchase_tab
INNER JOIN retailer_tab ON purchase_tab.retailer_id = retailer_tab.id
INNER JOIN cafe_tab ON cafe_tab.owner_id = retailer_tab.id

WHERE retailer_tab.id like '%".$retailer."%' OR retailer_tab.first_name like '%".$retailer."%' OR retailer_tab.last_name like '%".$retailer."%' OR retailer_tab.mobile like '%".$retailer."%' OR cafe_tab.location like '%".$retailer."%'  OR cafe_tab.area like '%".$retailer."%'
GROUP BY retailer_tab.id
";
//echo $sql;
	$Retailer_List = mysql_query($sql,$conn_sg) or die(mysql_error()); 

	}
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Finance Data Analysis System</title>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="codebase/dhtmlxcalendar.css"/>
	<link rel="stylesheet" type="text/css" href="form.css">
	<script src="codebase/dhtmlxcalendar.js"></script>
	<style>
		#calendar_input {
			border: 1px solid #909090;
			font-family: Tahoma;
			font-size: 12px;
		}
		#calendar_icon {
			vertical-align: middle;
			cursor: pointer;
		}
		#calendar_input2 {
			border: 1px solid #909090;
			font-family: Tahoma;
			font-size: 12px;
		}
		#calendar_icon2 {
			vertical-align: middle;
			cursor: pointer;
		}
	</style>
	<script>
		var myCalendar;
		function doOnLoad() {
			myCalendar = new dhtmlXCalendarObject({input: "calendar_input", button: "calendar_icon"});
			myCalendar2 = new dhtmlXCalendarObject({input: "calendar_input2", button: "calendar_icon2"});
		}
		
		
	</script>
	<script>
	$(document).ready(function() {
    $('#selectall').click(function(event) {  //on click 
        if(this.checked) { // check select status
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"               
            });
        }else{
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                       
            });         
        }
    });
    
});
	</script>
	
	<script>
	function CheckCheckboxes(form) {
    var
    i, counter = 0;

    for (i = 0; i < form.elements.length; ++i) {
        if ('checkbox' === form.elements[i].type && form.elements[i].checked) {
            ++counter;
			if(form.elements[i].value!=""){
				window.open('http://admin.cyberpay.in.th/cyberpay/erd/dtac/refund/' + form.elements[i].value, '_blank');
			}
        }
    }
    if (!counter) {
        alert('Please check at least one!');
    }
}
	</script>
  <!--
  <link rel="stylesheet" href="/resources/demos/style.css">-->


	<!--**************************
		start adding my code here
	**************************-->

	<!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="DataTables-1.10.7/media/css/jquery.dataTables.css">
      
    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="DataTables-1.10.7/media/js/jquery.js"></script>
      
    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="DataTables-1.10.7/media/js/jquery.dataTables.js"></script>

    <script>
      $(document).ready( function () {
        $('#my_table').DataTable();
      } );
    </script>

</head>
<body onload="doOnLoad();">
 

 <center>
<form action="index.php" method="post">
<p>
<input type="text" name="retailer" ><span></span>
</span> 
<!--
Refund Status :
<select name="refund_status">
	<option value="ALL">All</option>
	<option value="REFUND">Refunded</option>
	<option value="PENDING">Pending</option>
</select> -->
<input type="submit" name="submit" value="Search"> 
</p>
</form>
</center>
<br>

<div id="export_div">
      <?php echo "<a href='download.php?f=output_file.xlsx'><div class='buttonlink'><p>Export to xlsx</p></div></a>"; ?>
</div>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
echo "<form onsubmit=\"CheckCheckboxes(this); return false;\">";
echo "<table id='my_table'><thead>";
echo "<tr>";
echo "<th width='30'>ID</th>";
echo "<th width='120'>First Name</th>";
echo "<th width='120'>Last Name</th>";
echo "<th width='150'>Registation Date</th>";
echo "<th width='120'>GIRO</th>";
echo "<th width='120'>No. of PC(All Cafes)</th>";
echo "<th width='120'>Billing Active PCs</th>";
echo "<th width='90'>Disk/Update Active PCs</th>";
echo "<th width='90'>Remarks</th>";
echo "<th width='90'>Sales 3 month</th>";
echo "<th width='90'>Sales 2 month</th>";
echo "<th width='90'>Sales 1 month</th>";
echo "<th width='90'>Average Sales</th>";
echo "<th width='90'>Province</th>";

echo "</tr></thead><tbody>";
$totalsale = "0";
$month = "3";

$refunded_list  = array();
$value_list  = array();

/***********************
	For Export
***********************/
// load library
require 'PHPExcel.php';
//do stuff
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$rowCount = 1;

// for exporting
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "ID");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "First Name");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, "Last Name");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "Registation Date");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "GIRO");
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "No. of PC(All Cafes)");
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "Billing Active PCs");
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "Disk/Update Active PCs");
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "Remarks");
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, "Sales 3 month");
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, "Sales 2 month");
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, "Sales 1 month");
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, "Average Sales");
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, "Province");
$rowCount++;


while($row = mysql_fetch_array($Retailer_List)){

	echo "<tr>";
	echo "<td class='account' align='center'>".$row['id']."</td>";
	echo "<td align='left'>".$row['first_name']."</td>";
	echo "<td align='left'>".$row['last_name']."</td>";
	
	echo "<td class='account' align='left'>".$row['date_created']."</td>";
	$giro_list = retailer_giro_data($conn_sg,$input["dbNameSg"],$row['id']);
	
	//$billing_pc = get_cafe_pc($row['id'],"BILL");
	//$diskless_pc = get_cafe_pc($row['id'],"DISK");
	
	echo "<td class='account'>".$giro_list."</td>";
	$cafelist = get_cafe_list($conn_sg,$input["dbNameSg"],$row['id']);
	$cafe_pc = get_cafe_pc($conn_cft,$input["dbNameCFT"],$row['id'],$cafelist);
	if($cafe_pc=="0"){
		echo "<td class='account'>".$cafe_pc."</td>";
			echo "<td class='account'>".$cafe_pc."</td>";
			echo "<td class='account'>".$cafe_pc."</td>";

			//for exporting
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $cafe_pc);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $cafe_pc);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $cafe_pc);
		
	}else{
		while($numofpc = mysql_fetch_array($cafe_pc)){
			echo "<td class='account'>".$numofpc['num_of_pc']."</td>";
			echo "<td class='account'>".$numofpc['bill_online']."</td>";
			echo "<td class='account'>".$numofpc['disk_online']."</td>";

			//for exporting
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $numofpc['num_of_pc']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $numofpc['bill_online']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $numofpc['disk_online']);
		}
	}
	echo "<td class='account'></td>";
	echo "<td class='account'>".$row['3Month']."</td>";
	echo "<td class='account'>".$row['2Month']."</td>";
	echo "<td class='account'>".$row['1Month']."</td>";
	$average = $row['AllMonth']/3;
	$average = number_format((float)$average, 2, '.', '');
	echo "<td class='account'>".$average."</td>";
	echo "<td class='account'>".$row['location']."</td>";

	// for exporting
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row['id']);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['first_name']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['last_name']);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['date_created']);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $giro_list);
	// $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $numofpc['num_of_pc']);
	// $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "Billing Active PCs");
	// $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "Disk/Update Active PCs");
	// $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "Remarks");
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row['3Month']);
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row['2Month']);
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row['1Month']);
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $average);
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row['location']);
	$rowCount++;
}
	//check_refund_status($row['id']);
	echo "</td>";
	echo "</tr>";
	echo "</tbody></table><br><br><br>";	

	// for exporting
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	// $objWriter->save('output/'.$file_name);
	$objWriter->save('output/output_file.xlsx');

}
?>