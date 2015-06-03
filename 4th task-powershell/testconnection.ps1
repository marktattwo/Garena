<#
$dataSource = “112.121.158.92 6606”
$user = “ops_ws”
$pwd = “L3sZtXjQNMP6ifxFnI”
$database = “vpay”
$connectionString = “Server=$dataSource;uid=$user; pwd=$pwd;Database=$database;Integrated Security=False;”

$connection = New-Object System.Data.SqlClient.SqlConnection
$connection.ConnectionString = $connectionString

$connection.Open()

#>

<#$dataSource = “localhost”
$user = “root”
$pwd = “”
$database = “test”
$connectionString = “Server=$dataSource;uid=$user; pwd=$pwd;Database=$database;Integrated Security=False;”#>

<#
$conn = New-Object System.Data.SqlClient.SqlConnection
$conn.ConnectionString = "Server=localhost;Database=test;User ID=root;Password=;"
$conn.Open()
$sql = "SELECT EMP_STATUS FROM test_table"
$cmd = New-Object System.Data.SqlClient.SqlCommand($sql,$conn)
$rdr = $cmd.ExecuteReader()
$test = @()
while($rdr.Read())
{
    $test += ($rdr["EMP_STATUS"].ToString())
}
Write-Output $test
#>


#$conn.ConnectionString = "Data Source=112.121.158.92,6606;Network Library=DBMSSOCN;Initial Catalog=vpay;User ID=ops_ws;Password=L3sZtXjQNMP6ifxFnI"
#$conn.ConnectionString = "Server=112.121.158.92,6606;Database=vpay;User ID=ops_ws;Password=L3sZtXjQNMP6ifxFnI;"
#"Data Source=112.121.158.92,6606;Network Library=DBMSSOCN;Initial Catalog=vpay;User ID=ops_ws;Password=L3sZtXjQNMP6ifxFnI"
#$conn.ConnectionString = "Data Source=112.121.158.92,6606;Network Library=DBMSSOCN;Initial Catalog=vpay;User ID=ops_ws;Password=L3sZtXjQNMP6ifxFnI"
#$connString = "data source=SQLServer,1433;Initial catalog=NorthWind;uid=NorthwindUser;pwd=NorthwindUser;"
#$conn.ConnectionString = "data source=112.121.158.92,1433;Initial catalog=vpay;uid=ops_ws;pwd=L3sZtXjQNMP6ifxFnI;"

$conn = New-Object System.Data.SqlClient.SqlConnection
$conn.ConnectionString = "data source=112.121.158.92,6606;Initial catalog=vpay;uid=ops_ws;pwd=L3sZtXjQNMP6ifxFnI;"
$conn.Open()
$sql = "SELECT category FROM product_tab"
$cmd = New-Object System.Data.SqlClient.SqlCommand($sql,$conn)
$rdr = $cmd.ExecuteReader()
$test = @()
while($rdr.Read())
{
    $test += ($rdr["category"].ToString())
}
Write-Output $test
