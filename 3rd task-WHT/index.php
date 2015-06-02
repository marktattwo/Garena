<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$file_name = $_POST['start_date']." to ".$_POST['end_date'].time().".xlsx";
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
    <title>WHT and Commission</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="DataTables-1.10.7/media/css/jquery.dataTables.css">
      
    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="DataTables-1.10.7/media/js/jquery.js"></script>
      
    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="DataTables-1.10.7/media/js/jquery.dataTables.js"></script>

    <link rel="stylesheet" type="text/css" href="css/main.css">

    <script>
      $(document).ready( function () {
        $('#my_table').DataTable();
      } );
    </script>


	</head>
	<body>
		<header>
			<form action="index.php" method="POST" name="my_form">
    
	      <!--
	        Start Date
	      -->
	      <label for="start_date">Start:</label>
	      <?php
	        if($_SERVER["REQUEST_METHOD"] == "POST"){
	          echo "<input type='date' name='start_date' id='start_date' value='".$_POST['start_date']."'>";
	        }
	        else{
	          echo "<input type='date' name='start_date' id='start_date'>";
	        }
	      ?>

	      
	      <!--
	        End Date
	      -->
	      <label for="end_date">End:</label>
	      <?php
	        if($_SERVER["REQUEST_METHOD"] == "POST"){
	          echo "<input type='date' name='end_date' id='end_date' value='".$_POST['end_date']."'>";
	        }
	        else{
	          echo "<input type='date' name='end_date' id='end_date'>";
	        }
	      ?>

	      <!--
	        Mobile Form
	      -->
	      <label for="mobile">Mobile:</label>
	      <?php
					if ($_SERVER["REQUEST_METHOD"] == "POST"){
						echo "<input type='text' id='mobile' name='mobile' value='".$_POST['mobile']."'>";
					}
					else{
						echo "<input type='text' id='mobile' name='mobile' >";
					}
				?>

	      <!--
	        Button
	      -->
	      <input type="submit" name="submit" value="Search" id="search">
	      <input type="submit" name="submit" value="Gen File" id="gen_file">

	    </form>
	  </header>


    <!--Table -->
    <?php 
    	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['submit'] == 'Search'){
		        
		    //table body
		    mysql_connect("112.121.158.92:6606", "ops_ws", "L3sZtXjQNMP6ifxFnI") or die(mysql_error()); 
			  mysql_select_db("vpay") or die(mysql_error()); 
			  
			  mysql_query("SET character_set_results=utf8");
			  mysql_query("SET character_set_client=utf8");
			  mysql_query("SET character_set_connection=utf8");

		    echo "<div id='table_div'>";
		    echo "<table border='1' align='center' id='my_table'>";  
		    echo "<thead id='table_header'>";  
		    echo "<tr>";
		    echo "<th>ID</th>";        
		    echo "<th>Mobile</th>";        
		    echo "<th>Title</th>";        
		    echo "<th>First Name</th>";        
		    echo "<th>Last Name</th>";        
		    echo "<th>Product Name</th>";        
		    echo "<th>Category</th>";        
		    echo "<th>Category Group</th>";        
		    echo "<th>Base Price</th>";        
		    echo "<th>Discounted Price</th>";
		    echo "<th>Count</th>";
		    echo "<th>Sum Price</th>";
		    echo "<th>Commission</th>";
		    echo "<th>WHT/unit</th>";
		    echo "<th>Total Commission</th>";
		    echo "<th>Total WHT</th>";       
		    echo "</tr>";      
		    echo "</thead>";    
		    echo "<tbody id='table_body'>";

			  $start_date=$_POST['start_date'];
			  $end_date=$_POST['end_date'];
			  $mobile=$_POST['mobile'];

		    if($_POST['mobile']==NULL) {
		    	$data = mysql_query("SELECT
							retailer_tab.identification,
							retailer_tab.mobile,
							retailer_tab.title,
							retailer_tab.first_name,
							retailer_tab.last_name,
							product_tab.`name` AS Product_Name,
							product_tab.category,
							product_tab.category_group,
							purchase_tab.base_price AS Base_Price,
							purchase_tab.price AS Discount_Price,
							Count(purchase_tab.product_id) AS Count,
							Sum(purchase_tab.price) AS Sum_Price

							FROM
							purchase_tab
							INNER JOIN product_tab ON product_tab.id = purchase_tab.product_id
							INNER JOIN retailer_tab ON retailer_tab.id = purchase_tab.retailer_id
							LEFT JOIN refund_tab ON refund_tab.purchase_id = purchase_tab.id
							WHERE CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' AND (refund_tab.purchase_id IS NULL) AND product_tab.category_group!='Cafe_Products'
							GROUP BY
							purchase_tab.retailer_id,product_id,purchase_tab.price
							ORDER BY
							retailer_tab.first_name,retailer_tab.last_name,product_tab.category,product_tab.base_price") or die(mysql_error());

					while($info = mysql_fetch_array( $data )) {       
			      //for creating table
			      echo "<tr><td>".$info['identification'] . "</td>"; 
			      echo "<td>".$info['mobile'] . "</td>"; 
			      echo "<td>".$info['title'] . "</td>"; 
			      echo "<td>".$info['first_name'] . "</td>"; 
			      echo "<td>".$info['last_name'] . "</td>"; 
			      echo "<td>".$info['Product_Name'] . "</td>"; 
			      echo "<td>".$info['category'] . "</td>"; 
			      echo "<td>".$info['category_group'] . "</td>"; 
			      echo "<td>".$info['Base_Price'] . "</td>"; 
			      echo "<td>".$info['Discount_Price'] . "</td>"; 
			      echo "<td>".$info['Count'] . "</td>"; 
			      echo "<td>".$info['Sum_Price'] . "</td>";
			      $commission=round(($info['Base_Price'] - $info['Discount_Price']) / 0.97,2);
			      $wht= round($commission * 0.03, 2);
			      $total_commission=$commission * $info['Count']; 
			      $total_wht=$wht * $info['Count'];
			      echo "<td>".$commission . "</td>"; 
			      echo "<td>".$wht . "</td>"; 
			      echo "<td>".$total_commission . "</td>"; 
			      echo "<td>".$total_wht . "</td></tr>"; 
			  	}   

			    echo "</tbody>";    
			    echo "</table>";
			    echo "</div>";	
		    }

		    else {
		    	$data = mysql_query("SELECT
							retailer_tab.identification,
							retailer_tab.mobile,
							retailer_tab.title,
							retailer_tab.first_name,
							retailer_tab.last_name,
							product_tab.`name` AS Product_Name,
							product_tab.category,
							product_tab.category_group,
							purchase_tab.base_price AS Base_Price,
							purchase_tab.price AS Discount_Price,
							Count(purchase_tab.product_id) AS Count,
							Sum(purchase_tab.price) AS Sum_Price

							FROM
							purchase_tab
							INNER JOIN product_tab ON product_tab.id = purchase_tab.product_id
							INNER JOIN retailer_tab ON retailer_tab.id = purchase_tab.retailer_id
							LEFT JOIN refund_tab ON refund_tab.purchase_id = purchase_tab.id
							WHERE (CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59') AND (refund_tab.purchase_id IS NULL) AND retailer_tab.mobile= $mobile AND product_tab.category_group!='Cafe_Products'
							GROUP BY
							purchase_tab.retailer_id,product_id,purchase_tab.price
							ORDER BY
							retailer_tab.first_name,retailer_tab.last_name,product_tab.category,product_tab.base_price") or die(mysql_error());

					//init total
					$sum_of_sum_price=0;
					$sum_of_total_commission=0;
					$sum_of_total_wht=0;
					$id="";
					$title="";
					$first_name="";
					$last_name="";


					while($info = mysql_fetch_array( $data )) {       
			      
			      //collect info for footer
			      $id=$info['identification'];
			      $mobile=$info['mobile'];
			      $title=$info['title'];
			      $first_name=$info['first_name'];
			      $last_name=$info['last_name'];

			      //for creating table
			      echo "<tr><td>".$info['identification'] . "</td>"; 
			      echo "<td>".$info['mobile'] . "</td>"; 
			      echo "<td>".$info['title'] . "</td>"; 
			      echo "<td>".$info['first_name'] . "</td>"; 
			      echo "<td>".$info['last_name'] . "</td>"; 
			      echo "<td>".$info['Product_Name'] . "</td>"; 
			      echo "<td>".$info['category'] . "</td>"; 
			      echo "<td>".$info['category_group'] . "</td>"; 
			      echo "<td>".$info['Base_Price'] . "</td>"; 
			      echo "<td>".$info['Discount_Price'] . "</td>"; 
			      echo "<td>".$info['Count'] . "</td>"; 
			      echo "<td>".$info['Sum_Price'] . "</td>";
			      $commission=round(($info['Base_Price'] - $info['Discount_Price']) / 0.97,2);
			      $wht= round($commission * 0.03, 2);
			      $total_commission=$commission * $info['Count']; 
			      $total_wht=$wht * $info['Count'];
			      echo "<td>".$commission . "</td>"; 
			      echo "<td>".$wht . "</td>"; 
			      echo "<td>".$total_commission . "</td>"; 
			      echo "<td>".$total_wht . "</td></tr>"; 


						//find total
			      $sum_of_sum_price+=$info['Sum_Price'];
						$sum_of_total_commission+=$total_commission;
						$sum_of_total_wht+=$total_wht;

			  	}   


			    echo "</tbody>";  

					//footer (total)
		    	if($id!=NULL){
		    		echo "<tr><td>"."total".$id. "</td>"; 
			      echo "<td>".$mobile . "</td>"; 
			      echo "<td>".$title . "</td>"; 
			      echo "<td>".$first_name . "</td>"; 
			      echo "<td>".$last_name . "</td>"; 
			      echo "<td></td>"; 
			      echo "<td></td>";
			      echo "<td></td>";
			      echo "<td></td>";
			      echo "<td></td>"; 
			      echo "<td></td>"; 
			      echo "<td>".$sum_of_sum_price . "</td>";
			      echo "<td></td>";
			      echo "<td></td>"; 
			      echo "<td>".$sum_of_total_commission . "</td>"; 
			      echo "<td>".$sum_of_total_wht . "</td></tr>"; 
		    	}

			    echo "</table>";
			    echo "</div>";	
		    }
		    	   
    	}

    	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['submit'] == 'Gen File'){

		    /***********************
	        Row for Export
	      ***********************/
	      // load library
	      require 'PHPExcel.php';
		        
		    //table body
		    mysql_connect("112.121.158.92:6606", "ops_ws", "L3sZtXjQNMP6ifxFnI") or die(mysql_error()); 
			  mysql_select_db("vpay") or die(mysql_error()); 
			  
			  mysql_query("SET character_set_results=utf8");
			  mysql_query("SET character_set_client=utf8");
			  mysql_query("SET character_set_connection=utf8");
			  //do stuff
	      $objPHPExcel = new PHPExcel();
	      $objPHPExcel->setActiveSheetIndex(0);
	      $rowCount = 1;
       
		    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "identification");
	      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "mobile");
	      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, "นาย");
	      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "first_name");
	      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "last_name");
	      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "Product_Name");
	      $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "category");
	      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "category_group");
	      $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "Base_Price");
	      $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, "Discount_Price");
	      $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, "Count");
	      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, "Sum_Price");
	      $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, "Commission");
	      $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, "WHT/unit");
	      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, "Total Commission");
	      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, "Total WHT");
	      $rowCount++;

			  $start_date=$_POST['start_date'];
			  $end_date=$_POST['end_date'];
			  $mobile=$_POST['mobile'];

		    if($_POST['mobile']==NULL) {
		    	$data = mysql_query("SELECT
							retailer_tab.identification,
							retailer_tab.mobile,
							retailer_tab.title,
							retailer_tab.first_name,
							retailer_tab.last_name,
							product_tab.`name` AS Product_Name,
							product_tab.category,
							product_tab.category_group,
							purchase_tab.base_price AS Base_Price,
							purchase_tab.price AS Discount_Price,
							Count(purchase_tab.product_id) AS Count,
							Sum(purchase_tab.price) AS Sum_Price

							FROM
							purchase_tab
							INNER JOIN product_tab ON product_tab.id = purchase_tab.product_id
							INNER JOIN retailer_tab ON retailer_tab.id = purchase_tab.retailer_id
							LEFT JOIN refund_tab ON refund_tab.purchase_id = purchase_tab.id
							WHERE CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' AND (refund_tab.purchase_id IS NULL) AND product_tab.category_group!='Cafe_Products'
							GROUP BY
							purchase_tab.retailer_id,product_id,purchase_tab.price
							ORDER BY
							retailer_tab.first_name,retailer_tab.last_name,product_tab.category,product_tab.base_price") or die(mysql_error());

					//init total
					$id="";
					$title="";
					$first_name="";
					$last_name="";
					$sum_of_sum_price=0;
					$sum_of_total_commission=0;
					$sum_of_total_wht=0;

					while($info = mysql_fetch_array( $data )) {       

						if($id==NULL){
							//data of new id
				      $id=$info['identification'];
				      $mobile=$info['mobile'];
				      $title=$info['title'];
				      $first_name=$info['first_name'];
				      $last_name=$info['last_name'];
						}

						if($info['identification'] != $id && $id != NULL) {
							//total of old id
							$id="total".$id;
			    		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $id);
				      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $mobile);
				      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $title);
				      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $first_name);
				      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $last_name);
				      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $sum_of_sum_price);
				      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $sum_of_total_commission);
				      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $sum_of_total_wht);
				      $rowCount++;

				      //data of new id
				      $id=$info['identification'];
				      $mobile=$info['mobile'];
				      $title=$info['title'];
				      $first_name=$info['first_name'];
				      $last_name=$info['last_name'];
				      $sum_of_sum_price=0;
							$sum_of_total_commission=0;
							$sum_of_total_wht=0;

						}

		    		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $info['identification']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $info['mobile']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $info['title']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $info['first_name']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $info['last_name']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $info['Product_Name']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $info['category']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $info['category_group']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $info['Base_Price']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $info['Discount_Price']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $info['Count']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $info['Sum_Price']);
			      $commission=round(($info['Base_Price'] - $info['Discount_Price']) / 0.97,2);
			      $wht= round($commission * 0.03, 2);
			      $total_commission=$commission * $info['Count']; 
			      $total_wht=$wht * $info['Count'];
			      $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $commission);
			      $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $wht);
			      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $total_commission);
			      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $total_wht);
			      $rowCount++;

			      //find total
			      $sum_of_sum_price+=$info['Sum_Price'];
						$sum_of_total_commission+=$total_commission;
						$sum_of_total_wht+=$total_wht;
				  }   

				  if($id!=NULL){
				  	$id="total".$id;
		    		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $id);
			      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $mobile);
			      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $title);
			      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $first_name);
			      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $last_name);
			      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $sum_of_sum_price);
			      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $sum_of_total_commission);
			      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $sum_of_total_wht);
		    	}

				  $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	      	$objWriter->save('output/'.$file_name);

		    }

		    else {
		    	$data = mysql_query("SELECT
							retailer_tab.identification,
							retailer_tab.mobile,
							retailer_tab.title,
							retailer_tab.first_name,
							retailer_tab.last_name,
							product_tab.`name` AS Product_Name,
							product_tab.category,
							product_tab.category_group,
							purchase_tab.base_price AS Base_Price,
							purchase_tab.price AS Discount_Price,
							Count(purchase_tab.product_id) AS Count,
							Sum(purchase_tab.price) AS Sum_Price

							FROM
							purchase_tab
							INNER JOIN product_tab ON product_tab.id = purchase_tab.product_id
							INNER JOIN retailer_tab ON retailer_tab.id = purchase_tab.retailer_id
							LEFT JOIN refund_tab ON refund_tab.purchase_id = purchase_tab.id
							WHERE (CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59') AND (refund_tab.purchase_id IS NULL) AND retailer_tab.mobile= $mobile AND product_tab.category_group!='Cafe_Products'
							GROUP BY
							purchase_tab.retailer_id,product_id,purchase_tab.price
							ORDER BY
							retailer_tab.first_name,retailer_tab.last_name,product_tab.category,product_tab.base_price") or die(mysql_error());
		    
					
					//init total
					$sum_of_sum_price=0;
					$sum_of_total_commission=0;
					$sum_of_total_wht=0;
					$id="";
					$title="";
					$first_name="";
					$last_name="";

					while($info = mysql_fetch_array( $data )) {       

						//collect info for footer
			      $id=$info['identification'];
			      $mobile=$info['mobile'];
			      $title=$info['title'];
			      $first_name=$info['first_name'];
			      $last_name=$info['last_name'];

						//create table
		    		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $info['identification']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $info['mobile']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $info['title']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $info['first_name']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $info['last_name']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $info['Product_Name']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $info['category']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $info['category_group']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $info['Base_Price']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $info['Discount_Price']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $info['Count']);
			      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $info['Sum_Price']);
			      $commission=round(($info['Base_Price'] - $info['Discount_Price']) / 0.97,2);
			      $wht= round($commission * 0.03, 2);
			      $total_commission=$commission * $info['Count']; 
			      $total_wht=$wht * $info['Count'];
			      $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $commission);
			      $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $wht);
			      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $total_commission);
			      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $total_wht);
			      $rowCount++;

			      //find total
			      $sum_of_sum_price+=$info['Sum_Price'];
						$sum_of_total_commission+=$total_commission;
						$sum_of_total_wht+=$total_wht;

				  }   

				  if($id!=NULL){
				  	$id="total".$id;
		    		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $id);
			      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $mobile);
			      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $title);
			      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $first_name);
			      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $last_name);
			      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $sum_of_sum_price);
			      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $sum_of_total_commission);
			      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $sum_of_total_wht);
		    	}

				  $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	      	$objWriter->save('output/'.$file_name);

		    } 
		    
    	}

    ?>

  <?php 
  	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['submit'] == 'Gen File'){
  		echo "<div id='load_div'>";
  		echo "<div class='buttonlink'><a href='download.php?f=".$file_name."'><p>Click Here To Download Your File</p></a></div>"; 
  		echo "</div>";
  	}
  ?>

	</body>
</html>