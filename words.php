<?
$selection = $_GET["selection"];

include("mysql_connect.php");
$date = date("Y-m-d"); 

$query = "SELECT MAX(counter) + 1 FROM words";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
$counter = $row[0];


$query = "INSERT INTO words (word, date, counter) VALUES ('$selection', '$date', '$counter')";
$result = mysql_query($query) or die(mysql_error());

echo $result;
?>