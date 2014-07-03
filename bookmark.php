<? 

if($_GET['book'] && $_GET['chapter'] && $_GET['page']){
	
	include("mysql_connect.php");
	
	$query = "SELECT * FROM `bookmarks` WHERE `book` LIKE '{$_GET['book']}'";
	
	$result = mysql_query($query) or die(mysql_error()); 
	
	
	if(mysql_num_rows($result)){
		$query = "UPDATE `avreolko_db`.`bookmarks` SET chapter='{$_GET['chapter']}', page='{$_GET['page']}' WHERE book='{$_GET['book']}';";
		echo"Закладка перенесена!";
	}else{
		$query = "INSERT INTO `avreolko_db`.`bookmarks` (`book`, `chapter`, `page`) VALUES ('{$_GET['book']}', '{$_GET['chapter']}', '{$_GET['page']}');";
		echo"Закладка создана!";
	}
	
	
	/* Выполнить запрос. Если произойдет ошибка - вывести ее. */
	mysql_query($query) or die(mysql_error());
	
	/* Закрыть соединение */ 
	mysql_close();

}
?> 