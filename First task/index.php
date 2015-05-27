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
              mysql_connect("112.121.158.92:6606", "ops_ws", "L3sZtXjQNMP6ifxFnI") or die(mysql_error()); 
              mysql_select_db("vpay") or die(mysql_error()); 
              
              mysql_query("SET character_set_results=utf8");
              mysql_query("SET character_set_client=utf8");
              mysql_query("SET character_set_connection=utf8");

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
    
  </body>
</html>