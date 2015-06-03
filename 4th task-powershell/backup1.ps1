$d = Get-Date
$dString = $d.Day.ToString() + "-" + $d.Month.ToString() + "-" + $d.Year.ToString()
$backupFilePath = "C:\temp\" + $dString + ".sql"
$cmd =  "& 'C:\xampp\mysql\bin\mysqldump.exe' -u root test > " + $backupFilePath
Write-Host $cmd
Invoke-Expression $cmd | Out-Null