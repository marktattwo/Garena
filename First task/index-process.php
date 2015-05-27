<?php 
  mysql_connect("112.121.158.92:6606", "ops_ws", "L3sZtXjQNMP6ifxFnI") or die(mysql_error()); 
  mysql_select_db("vpay") or die(mysql_error()); 
  
  mysql_query("SET character_set_results=utf8");
  mysql_query("SET character_set_client=utf8");
  mysql_query("SET character_set_connection=utf8");

  $data1 = mysql_query("SELECT 
    product_tab.category,
    product_tab.denomination
    FROM product_tab
    WHERE product_tab.id=".$_POST['product']) or die(mysql_error()); 


  $info = mysql_fetch_array( $data1 );
  $file_name = $info['category'].$info['denomination']." from ".$_POST['start_date']." to ".$_POST['end_date']." at ".time().".xlsx"

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Table</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
  </head>
  <body>

    <!--
      Header
    -->

    <header>
      <form action="index-process.php" method="POST" name="my_form">
    
        <!--
          Start Date
        -->

        <div id="start_date_div" class="date">
          <label for="start_date">Start:</label>
          <input type="date" name="start_date" id="start_date">
        </div>
        
        <!--
          End Date
        -->

        <div id="end_date_div" class="date">
          <label for="end_date">End:</label>
          <input type="date" name="end_date" id="end_date">
        </div>

        <!--
          Product list
        -->

        <div id="product_div">
          <label for="product">Product:</label>
          <select name="product" id="product">
            <option>Choose Product</option>

            <?php
            	$data1 = mysql_query("SELECT 
                product_tab.name,
                product_tab.base_price,
                product_tab.id
                FROM product_tab") or die(mysql_error()); 


            	while($info = mysql_fetch_array( $data1 )) 
             { 
                echo "<option value='".$info['id']."'>".$info['name'] ." Price: ".$info['base_price']. "</option>"; 
             }  
            ?>

          </select>
        </div>

        <!--
          Button
        -->

        <div id="button_div">
          <input type="submit" name="search" value="Search" id="search">
          <!-- <input type="submit" name="export" value="Export" id="export"> -->
        </div>

      </form>


    </header>

    <!-- <h1>
    	<?php echo var_dump($_POST);?>
    </h1> -->
    <div id="detail">
      <h1>
      	<?php echo "From (Y:M:D): ".$_POST["start_date"]." To: ".$_POST["end_date"];?>
      </h1>
    </div>

    <div id="export_div">
      <?php echo "<a href='download.php?f=".$file_name."'><div class='buttonlink'><p>Export to xlsx</p></div></a>"; ?>
    </div>
    <!--
      Table div
    -->
    <div id="table_div">
      <table border="1" align="center">
        <thead id="table_header">
          <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Price</th>
            <th>Time</th>
            <th>Base Price</th>
            <th>Reference</th>
            <th>Buyer Phone</th>
            <th>Mobile</th>
            <th>First Name</th>
            <th>Last Name</th>
          </tr>
        </thead>

        <tbody id="table_body">
          <?php  
          	$start_date=$_POST["start_date"];
          	$end_date=$_POST["end_date"];
          	$id=$_POST["product"];

          	/***********************
							Row for Export
        	***********************/
        		// load library
						require 'PHPExcel.php';
						//do stuff
          	$objPHPExcel = new PHPExcel();
						$objPHPExcel->setActiveSheetIndex(0);
						$rowCount = 1;
          	
            $data = mysql_query("SELECT 
              product_tab.name,
              purchase_tab.price,
              CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') as sell_time ,
              product_tab.base_price,
              product_tab.category,
              product_tab.denomination,
              purchase_tab.reference,
              purchase_tab.buyer_phone,
              retailer_tab.mobile,
              retailer_tab.first_name,
              retailer_tab.last_name
              FROM
              product_tab
              INNER JOIN purchase_tab ON product_tab.id = purchase_tab.product_id
              INNER JOIN retailer_tab ON retailer_tab.id = purchase_tab.retailer_id
              WHERE  
              purchase_tab.product_id='$id'
              AND CONVERT_TZ(purchase_tab.time_stamp,'+00:00','+07:00') BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
              ORDER BY purchase_tab.product_id,purchase_tab.time_stamp ASC") or die(mysql_error()); 
           
            // for indexing
            $count = 0;
						

						// for exporting
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "name");
				    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "price");
				    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, "sell_time");
				    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "base_price");
				    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "reference");
				    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "buyer_phone");
				    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "mobile");
				    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "first_name");
				    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "last_name");
            $rowCount++;
            
            while($info = mysql_fetch_array( $data )) 
            { 	
            	// for exporting
          		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $info['name']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $info['price']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $info['sell_time']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $info['base_price']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $info['reference']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $info['buyer_phone']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $info['mobile']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $info['first_name']);
					    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $info['last_name']);
					    $rowCount++;      

              // for indexing
              $count = $count + 1;

              //for creating table
              echo "<tr><td>".$count. "</td>"; 
              echo "<td>".$info['name'] . "</td>"; 
              echo "<td>".$info['price'] . "</td>"; 
              echo "<td>".$info['sell_time'] . "</td>";
              echo "<td>".$info['base_price'] . "</td>"; 
              echo "<td>".$info['reference'] . "</td>"; 
              echo "<td>".$info['buyer_phone'] . "</td>"; 
              echo "<td>".$info['mobile'] . "</td>"; 
              echo "<td>".$info['first_name'] . "</td>"; 
              echo "<td>".$info['last_name'] ."</td></tr>"; 
            }   

            // for exporting
          	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
						$objWriter->save('output/'.$file_name);
            
          ?> 

        </tbody>
      </table>
    </div>
  </body>
</html>