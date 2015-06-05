#-----Set Connection
Add-Type -Path 'C:\Program Files (x86)\MySQL\MySQL Connector Net 6.9.6\Assemblies\v4.5\MySql.Data.dll'
$MySQLAdminUserName = 'ops_ws'
$MySQLAdminPassword = 'L3sZtXjQNMP6ifxFnI'
$MySQLDatabase = 'vpay'
$MySQLHost = '112.121.158.92'
$ConnectionString = "server=" + $MySQLHost + ";port=6606;uid=" + $MySQLAdminUserName + ";pwd=" + $MySQLAdminPassword + ";database="+$MySQLDatabase

#-----Set File Name
$d= Get-Date

#-----Query
$Query = "SELECT * FROM product_tab"

#-----Start Connection
Try {
  [void][System.Reflection.Assembly]::LoadWithPartialName("MySql.Data")
  $Connection = New-Object MySql.Data.MySqlClient.MySqlConnection
  $Connection.ConnectionString = $ConnectionString
  $Connection.Open()

#-----Get Data
  $Command = New-Object MySql.Data.MySqlClient.MySqlCommand($Query, $Connection)
  $DataAdapter = New-Object MySql.Data.MySqlClient.MySqlDataAdapter($Command)
  $DataSet = New-Object System.Data.DataSet
  $RecordCount = $dataAdapter.Fill($dataSet,"data")
  #$DataSet.Tables[0]

#-----Set Header
$dString = $d.Year.ToString() + "-" + $d.Month.ToString() + "-" + $d.Day.ToString() + " " + $d.Hour.ToString() + ":" + $d.Minute.ToString() + ":" + $d.Minute.ToString()
$content = ""
$content += "/*Date : $dString*/"
$content += "

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ``product_tab``
-- ----------------------------
DROP TABLE IF EXISTS ``product_tab``;
CREATE TABLE ``product_tab`` (
  ``id`` int(11) NOT NULL AUTO_INCREMENT,
  ``name`` varchar(128) NOT NULL,
  ``denomination`` int(10) unsigned NOT NULL,
  ``base_price`` decimal(15,2) NOT NULL,
  ``description`` varchar(2048) DEFAULT NULL,
  ``disabled`` tinyint(1) NOT NULL,
  ``category`` varchar(255) NOT NULL DEFAULT '',
  ``priority`` int(11) NOT NULL DEFAULT '0',
  ``default_discount_price`` decimal(15,2) DEFAULT NULL,
  ``category_group`` varchar(32) NOT NULL DEFAULT '',
  ``monitor_stock`` tinyint(1) NOT NULL DEFAULT '0',
  ``stock_lower_threshold`` int(10) unsigned NOT NULL DEFAULT '0',
  ``product_code`` varchar(50) DEFAULT '',
  ``need_delivery`` tinyint(1) NOT NULL DEFAULT '0',
  ``support_shopping_cart`` tinyint(1) NOT NULL DEFAULT '0',
  ``is_bundle`` tinyint(1) NOT NULL DEFAULT '0',
  ``bundle_size`` int(10) unsigned NOT NULL DEFAULT '0',
  ``accept_garena_account`` tinyint(1) NOT NULL DEFAULT '0',
  ``cafe_daily_quota_limit`` int(10) unsigned DEFAULT NULL,
  ``cafe_whole_quota_limit`` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (``id``),
  UNIQUE KEY ``category_denomination`` (``category``,``denomination``)
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product_tab
-- ----------------------------
"

#-----Insert Row
$x = 0
while($DataSet.Tables[0].Rows[$x] -ne $null)
{
$content += "INSERT INTO ``product_tab`` VALUES ("

    for($i = 0; $i -lt 19; $i++){
        $content += "'"
        $DataSet.Tables[0].Rows[$x][$i] = $DataSet.Tables[0].Rows[$x][$i].Replace("'" , "\'")
        $content += $DataSet.Tables[0].Rows[$x][$i]
        $content += "', "
    }
$content += "'"
$DataSet.Tables[0].Rows[$x][19] = $DataSet.Tables[0].Rows[$x][19].Replace("'" , "\'")
$content += $DataSet.Tables[0].Rows[$x][19]
$content += "'"

$content += ");
"
$x++
}

#-----Save File
Set-Content -Encoding UTF8 -Path C:\temp\product_tab.txt -Value $content

#-----ETC
}

Catch {
  Write-Host "ERROR : Unable to run query : $query `n$Error[0]"
}

Finally {
  $Connection.Close()
}
