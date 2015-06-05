Add-Type -Path 'C:\Program Files (x86)\MySQL\MySQL Connector Net 6.9.6\Assemblies\v4.5\MySql.Data.dll'
$MySQLAdminUserName = 'ops_ws'
$MySQLAdminPassword = 'L3sZtXjQNMP6ifxFnI'
$MySQLDatabase = 'vpay'
$MySQLHost = '112.121.158.92'
$ConnectionString = "server=" + $MySQLHost + ";port=6606;uid=" + $MySQLAdminUserName + ";pwd=" + $MySQLAdminPassword + ";database="+$MySQLDatabase


$Query = "SELECT * FROM cafe_tab"

Try {
  [void][System.Reflection.Assembly]::LoadWithPartialName("MySql.Data")
  $Connection = New-Object MySql.Data.MySqlClient.MySqlConnection
  $Connection.ConnectionString = $ConnectionString
  $Connection.Open()

  $Command = New-Object MySql.Data.MySqlClient.MySqlCommand($Query, $Connection)
  $DataAdapter = New-Object MySql.Data.MySqlClient.MySqlDataAdapter($Command)
  $DataSet = New-Object System.Data.DataSet
  $RecordCount = $dataAdapter.Fill($dataSet,"data")
  $DataSet.Tables[0] | Out-File c:\temp\go.txt
}

Catch {
  Write-Host "ERROR : Unable to run query : $query `n$Error[0]"
}

Finally {
  $Connection.Close()
}
