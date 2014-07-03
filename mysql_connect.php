<?

/* Переменные для соединения с базой данных */ 
$hostname = "localhost"; 
$username = "avreolko"; 
$password = "Valentin"; 
$dbName = "avreolko_db";

/* создать соединение */ 
mysql_connect($hostname,$username,$password) OR DIE("Не могу создать соединение "); 
/* выбрать базу данных. Если произойдет ошибка - вывести ее */ 
mysql_select_db($dbName) or die(mysql_error());
?>