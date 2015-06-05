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
$Query = "SELECT * FROM cafe_tab"

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
-- Table structure for ``cafe_tab``
-- ----------------------------
DROP TABLE IF EXISTS ``cafe_tab``;
CREATE TABLE ``cafe_tab`` (
  ``id`` int(11) NOT NULL AUTO_INCREMENT,
  ``name`` varchar(255) NOT NULL,
  ``address`` varchar(255) NOT NULL,
  ``location`` varchar(255) NOT NULL,
  ``owner_id`` int(11) DEFAULT NULL,
  ``cybercafe_id`` varchar(255) NOT NULL,
  ``date_created`` datetime NOT NULL,
  ``is_active`` tinyint(1) NOT NULL DEFAULT '1',
  ``release_version`` int(10) unsigned NOT NULL DEFAULT '0',
  ``area`` varchar(255) NOT NULL DEFAULT '',
  ``business`` int(11) NOT NULL DEFAULT '0',
  ``latitude`` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  ``longitude`` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  ``business_type`` int(11) DEFAULT NULL,
  ``cafe_remark`` varchar(255) NOT NULL DEFAULT '',
  ``post_code`` varchar(5) NOT NULL DEFAULT '',
  ``selected_for_default_app_seller`` tinyint(4) DEFAULT NULL,
  ``is_address_verified`` tinyint(1) NOT NULL DEFAULT '0',
  ``cafe_opening_time`` time NOT NULL DEFAULT '00:00:00',
  ``cafe_closing_time`` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (``id``),
  UNIQUE KEY ``cybercafe_id`` (``cybercafe_id``),
  UNIQUE KEY ``selected_for_default_app_seller`` (``selected_for_default_app_seller``,``owner_id``),
  KEY ``cafe_tab_5d52dd10`` (``owner_id``),
  KEY ``area`` (``area``),
  KEY ``location`` (``location``),
  CONSTRAINT ``____owner_id_refs_id_c22f87a7`` FOREIGN KEY (``owner_id``) REFERENCES ``retailer_tab`` (``id``)
) ENGINE=InnoDB AUTO_INCREMENT=18273 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cafe_tab
-- ----------------------------
"

#-----Insert Row
$x = 0
while($DataSet.Tables[0].Rows[$x] -ne $null)
#for($x = 0; $x -lt 5; $x++)
{
$content += "INSERT INTO ``cafe_tab`` VALUES ("

    for($i = 0; $i -lt 19; $i++){
        $content += "'"
        $content += $DataSet.Tables[0].Rows[$x][$i]
        $content += "', "
    }
$content += "'"
$content += $DataSet.Tables[0].Rows[$x][19]
$content += "'"

$content += ");
"
$x++
}

#-----Save File
Set-Content -Encoding UTF8 -Path C:\temp\header.txt -Value $content

#-----ETC
}

Catch {
  Write-Host "ERROR : Unable to run query : $query `n$Error[0]"
}

Finally {
  $Connection.Close()
}
