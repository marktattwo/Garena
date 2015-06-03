<#set name
$d = Get-Date
$dName = $d.Day.ToString() + "-" + $d.Month.ToString() + "-" + $d.Year.ToString()
#>

#start copying
$SrcServer = "112.121.158.92:6606" #Source Server Name
$SrcDatabase = "vpay" #Source Database Name
$SrcUser = "ops_ws" #Source Login : User Name
$SrcPwd = "L3sZtXjQNMP6ifxFnI" #Source Login : Password
 
$DestServer = "localhost" #Destination Server Name
$DestDatabase = "dbTester" #Destination Database Name
$DestUser = "root" #Destination Login : User Name
$DestPwd = "" #Destination Login : Password
 
$SrcTable = "product_tab" #Source Table Name
$DestTable = "product_tab2" #Destination Table Name
 
$BatchSize = 10 #Batch Size
$TimeOut = 180 #Timeout period
 
 Function ConnectionStringS ([string] $ServerName, [string] $DbName, [string] $User, [string] $Pwd)
 {
   "Server=$ServerName;uid=$User; pwd=$Pwd;Database=$DbName;Integrated Security=False;"
 }
 
 ########## Main body ############
 If ($DestDatabase.Length –eq 0) {
   $DestDatabase = $SrcDatabase
 }
 
 If ($DestTable.Length –eq 0) {
   $DestTable = $SrcTable
 }
 
 #If ($Truncate) {
   #$TruncateSql = "TRUNCATE TABLE " + $DestTable
  # Sqlcmd -S $DestServer -d $DestDatabase -Q $TruncateSql
 #}
 
 #$SrcConnStr = New-Object System.Data.SqlClient.SqlConnection
 #$SrcConn.ConnectionString  = "Server=$SrcServer;Database=$SrcDatabase; User Id=$SrcUser; Password=$SrcPwd;"
 $SrcConnStr = ConnectionStringS $SrcServer $SrcDatabase $SrcUser $SrcPwd
 $SrcConn  = New-Object System.Data.SqlClient.SQLConnection($SrcConnStr)
 $CmdText = "SELECT * FROM " + $SrcTable
 $SqlCommand = New-Object system.Data.SqlClient.SqlCommand($CmdText, $SrcConn)
 $SrcConn.Open()
 [System.Data.SqlClient.SqlDataReader] $SqlReader = $SqlCommand.ExecuteReader()
 
 Try
 {
   $DestConnStr = ConnectionStringS $DestServer $DestDatabase $DestUser $DestPwd
   $bulkCopy = New-Object Data.SqlClient.SqlBulkCopy($DestConnStr, [System.Data.SqlClient.SqlBulkCopyOptions]::KeepIdentity)
   $bulkCopy.DestinationTableName = $DestTable
   $bulkCopy.BatchSize = $BatchSize
   $bulkCopy.BulkCopyTimeout = $TimeOut
   $bulkCopy.WriteToServer($sqlReader)
 }
 Catch [System.Exception]
 {
   $ex = $_.Exception
   Write-Host $ex.Message
 }
 Finally
 {
   Write-Host "Bulk copy completed"
   $SqlReader.close()
   $SrcConn.Close()
   $SrcConn.Dispose()
   $bulkCopy.Close()
 }