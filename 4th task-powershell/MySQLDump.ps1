<#
$server         = "112.121.158.92"
$port           = "6606"
$user           = "ops_ws"
$password       = "L3sZtXjQNMP6ifxFnI"
$backupFolder   = "C:\temp\"
$dbName         = "vpay"
$MySQLDumpPath  = "C:\xampp\mysql\bin\mysqldump.exe"
$tableName      = "product_tab"
#>

<#
$server         = "localhost"
$port           = "6606"
$user           = "root"
$password       = "L3sZtXjQNMP6ifxFnI"
$backupFolder   = "C:\temp\"
$dbName         = "test"
$MySQLDumpPath  = "C:\xampp\mysql\bin\mysqldump.exe"
$tableName      = "hey"
#>

$server         = "172.16.6.200"
$port           = "6606"
$user           = "root"
$password       = "L3sZtXjQNMP6ifxFnI"
$backupFolder   = "C:\temp\"
$dbName         = "gcms"
$MySQLDumpPath  = "C:\xampp\mysql\bin\mysqldump.exe"
$tableName      = "cafe_tab"

$d= Get-Date
$dString = $d.Year.ToString() + "-" + $d.Month.ToString() + "-" + $d.Day.ToString() + "_" + $d.Hour.ToString() + "-" + $d.Minute.ToString() + "-" + $d.Minute.ToString()
$backupFilePath = "C:\temp\" + $dString + ".sql"
#$cmd = "& 'C:\xampp\mysql\bin\mysqldump.exe' -h $server -u $user -p $password -P $port $dbname $tableName"
$cmd = "& 'C:\xampp\mysql\bin\mysqldump.exe' -h $server -u $user $dbname $tableName"
Write-Host $cmd
Invoke-Expression $cmd | Out-File $backupFilePath -Encoding ASCII