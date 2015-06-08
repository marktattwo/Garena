Add-Type -Path 'C:\Program Files (x86)\MySQL\MySQL Connector Net 6.9.6\Assemblies\v4.5\MySql.Data.dll'
#Open connection
$connStr = "server=172.16.6.200;port=3306;uid=root;pwd="
$conn = new-object MySql.Data.MySqlClient.MySqlConnection($connStr)
$conn.Open()

#Name new DB
$d= Get-Date
$dday = $d.Day.ToString()
if($dday.Length -eq 1){
    $dday = "0" + $dday 
}

$dmonth = $d.Month.ToString()
if($dmonth.Length -eq 1){
    $dmonth = "0" + $dmonth 
}
$dbname = "vpay" + "_" + $d.Year.ToString() + "-" + $dmonth + "-" + $dday


[void][system.reflection.assembly]::LoadWithPartialName("MySql.Data")
    
# create database
$createmysqldatabase = 'CREATE DATABASE `' + $dbname + '` CHARACTER SET utf8'
$cmd = New-Object MySql.Data.MySqlClient.MySqlCommand($createmysqldatabase, $conn)
$cmd.ExecuteNonQuery()
    
<# grant privileges to user
$grantaccess = 'grant all on ' + $dbname + '.* to `myuser`@`localhost`'
$cmd = new-object MySql.Data.MySqlClient.MySqlCommand($grantaccess, $conn)
$cmd.ExecuteNonQuery()
#>

<#
$a = Get-Content -Encoding UTF8 C:\temp\product_bundle_tab.sql 
$a = $a.Replace("product_bundle_tab" , $filename)
Set-Content -Encoding UTF8 -Path C:\temp\$filename.sql -Value $a
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 test < C:\temp\$filename.sql"
#>

#Add new table
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_cafe_tab.sql"
Write-Host "cafe_tab finished `n"
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_dealer_tab.sql"
Write-Host "dealer_tab finished `n"
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_giro_user_tab.sql"
Write-Host "giro_user_tab finished `n"
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_product_bundle_tab.sql"
Write-Host "product_bundle_tab finished `n"
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_product_tab.sql"
Write-Host "product_tab finished `n"
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_retailer_tab.sql"
Write-Host "retailer_tab finished `n"
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 $dbname < C:\backup\today\obj_seller_tab.sql"
Write-Host "seller_tab finished `n"

#Rename Path 2015-06-08
$folderName = $d.Year.ToString() + "-" + $dmonth + "-" + $dday
Rename-Item C:\backup\today $folderName
New-Item C:\backup\today -type directory

#Close connection
$conn.Close()