$a = Get-Content -Encoding UTF8 C:\temp\product_bundle_tab.sql 
$d= Get-Date
$filename = "product_bundle_tab" + "_" + $d.Year.ToString() + "_" + $d.Month.ToString() + "_" + $d.Day.ToString()
$a = $a.Replace("product_bundle_tab" , $filename)
Set-Content -Encoding UTF8 -Path C:\temp\$filename.sql -Value $a
&cmd /c "C:\xampp\mysql\bin\mysql.exe --default-character-set=utf8 -h 172.16.6.200 -u root -P 3306 test < C:\temp\$filename.sql"
