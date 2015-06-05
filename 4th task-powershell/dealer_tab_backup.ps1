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
$Query = "SELECT * FROM dealer_tab"

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
-- Table structure for ``dealer_tab``
-- ----------------------------
DROP TABLE IF EXISTS ``dealer_tab``;
CREATE TABLE ``dealer_tab`` (
  ``id`` int(11) NOT NULL AUTO_INCREMENT,
  ``mobile`` varchar(32) NOT NULL,
  ``first_name`` varchar(255) NOT NULL,
  ``last_name`` varchar(255) NOT NULL,
  ``birthday`` date NOT NULL,
  ``identification`` varchar(255) NOT NULL,
  ``country`` varchar(64) NOT NULL,
  ``location`` varchar(255) NOT NULL,
  ``address`` varchar(255) NOT NULL,
  ``balance`` decimal(20,2) NOT NULL,
  ``status`` int(10) unsigned NOT NULL,
  ``date_created`` datetime NOT NULL,
  ``initial_balance`` decimal(20,2) NOT NULL,
  PRIMARY KEY (``id``),
  UNIQUE KEY ``mobile`` (``mobile``)
) ENGINE=InnoDB AUTO_INCREMENT=468 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dealer_tab
-- ----------------------------
"

#-----Insert Row
$x = 0
while($DataSet.Tables[0].Rows[$x] -ne $null)
#for($x = 0; $x -lt 5; $x++)
{
$content += "INSERT INTO ``dealer_tab`` VALUES ("

    for($i = 0; $i -lt 11; $i++){
        $content += "'"
        $content += $DataSet.Tables[0].Rows[$x][$i]
        $content += "', "
    }
$content += "'"
$content += $DataSet.Tables[0].Rows[$x][12]
$content += "'"

$content += ");
"
$x++
}

#-----Save File
Set-Content -Encoding UTF8 -Path C:\temp\dealer_tab.txt -Value $content

#-----ETC
}

Catch {
  Write-Host "ERROR : Unable to run query : $query `n$Error[0]"
}

Finally {
  $Connection.Close()
}
