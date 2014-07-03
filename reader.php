<!DOCTYPE HTML>
<html>
	<head>
		<title>B O O K E R</title>
		<link rel="stylesheet" href="style.css">
		<script src='jquery.js'></script>
	</head>
	
	<script language='javascript'>
		$(document).ready(function() {	// когда страница полностью загрузилась
		  	$( "html" ).dblclick(function() {	// начинаем обрабатывать событие "двойной клик"

		  		$.my_vars = {
		  			selected_text: ''
		  		};
		  		
		  		$.my_vars.selected_text = String(window.getSelection());
		  		
		  		if ( /^[a-zA-Z0-9]+$/.test($.my_vars.selected_text) ) {	// проверяем не пустая ли строка
				    $.ajax({	// посылаем аякс-запрос на добавление нового слова в БД
					  type: "GET",
					  url: "words.php",
					  data: { selection: $.my_vars.selected_text }
					}).done(function( result ) {
						return;
					});
				}
			});
		});
	</script>
	
	<?
	
	//если есть закладка на эту книгу, мы ее щас достанем
	include("mysql_connect.php");
	
	$filename = $_GET['book'];
	$query = "SELECT * FROM `bookmarks` WHERE `book` LIKE '{$_GET['book']}'";
	$result = mysql_query($query) or die(mysql_error()); 
	
	if(mysql_num_rows($result)){
		$rows = mysql_fetch_array($result);
		$chapter = $rows['chapter'];
		$page = $rows['page'];
	}else{
		$chapter = 1;
		$page = 1;
	}
	
	?>
	
	<script>
		
		$.booker_vars = { 
			<?
			echo "filename: '$filename',";
			echo "chapter_number: $chapter,";
			echo "page: $page,";
			?>
			
			chapter_text: '',
			pages_in_chapter: 1
		};
		
		function set_bookmark(){
			$.ajax({
			  type: "GET",
			  url: "bookmark.php",
			  data: { book: $.booker_vars.filename, chapter: $.booker_vars.chapter_number, page: $.booker_vars.page }
			}).done(function( result ) {
				return;
			});
		}
		
		function pages(event) {
			if (event.keyCode==37){
				if($.booker_vars.page != 1){
					$.booker_vars.page = $.booker_vars.page - 1;
					$(".reader_text").scrollTop(($.booker_vars.page - 1)*800);
					$("#page_number").html($.booker_vars.page);
					set_bookmark();
				}else{
					if($.booker_vars.chapter_number != 1){
						get_text($.booker_vars.filename, $.booker_vars.chapter_number - 1, 0);
					}
				}
				
			}
			if (event.keyCode==39){
				if($.booker_vars.page == $.booker_vars.pages_in_chapter){
					get_text($.booker_vars.filename, $.booker_vars.chapter_number + 1, 999);
				}else{
					$(".reader_text").scrollTop(($.booker_vars.page)*800);
					$.booker_vars.page = $.booker_vars.page + 1;
					$("#page_number").html($.booker_vars.page);
					set_bookmark();
				}
			}
		}
		
		// функция, которая грузит текст
		function get_text(book_file, chapter_number, page){
			$.ajax({
			  type: "GET",
			  url: "loader.php",
			  data: { book: book_file, action: "get_chapter", chapter_number: chapter_number }
			}).done(function( result ) {
			  $.booker_vars.chapter_text = result;
			}).done(function() {
				
			  $.booker_vars.chapter_number = chapter_number;	// устанавливаем номер текущей главы
			  $("#chapter_number").html($.booker_vars.chapter_number);
			  $(".reader_text").html($.booker_vars.chapter_text);	// загружаем полученный текст главы в div
			  var text_height = $(".reader_text")[0].scrollHeight;	//	узнаем высоту всего контента в div
			  
			  // узнаем количество страниц в главе
			  $.booker_vars.pages_in_chapter = Math.ceil( text_height / 800);	
			  
			  // если мы возвращались назад по книге, то при подгрузке новой главы надо показать последнюю страницу
			  if(page == 999){
				  $(".reader_text").scrollTop(0);
				  $.booker_vars.page = 1;
				  $("#page_number").html($.booker_vars.page);
			  	  set_bookmark();
			  }else if(page == 0){
				  $(".reader_text").scrollTop(text_height);
				  $.booker_vars.page = $.booker_vars.pages_in_chapter;
				  $("#page_number").html($.booker_vars.page);
			  // если же нет, то показываем первую страницу
			  	  set_bookmark();
			  }else{
				  $(".reader_text").scrollTop(($.booker_vars.page - 1)*800);
				  $.booker_vars.page = page;
				  $("#page_number").html($.booker_vars.page);
			  // если же нет, то показываем первую страницу
			  	  set_bookmark();
			  }
			  
			});
		}
		
		$(document).ready(function(){
		
			$.ajax({
				type: "GET",
				url: "loader.php",
				data: { book: $.booker_vars.filename, action: "get_chapters" }
			}).done(function( result ) {
				
				var array_of_chapters = result.split("<separator>");
				
				for (var key in array_of_chapters) {
				  $("#chapters").append(array_of_chapters[key]);
				}
				
			}).done(function() {
				// скрываем ссылки на главы, оставляем только название книги
				$("#chapters p").not(":first-child").hide();
				
				// чтобы было красиво, ставим ширину одинаковую у все параграфов внутри дива с ссылками на главы
				var width = $("#title p").css("width");
		        $("#chapters p").not(":first-child").css("width", width);
				
				// заменяем название книги на иконку оглавления, чтобы плашка своей шириной не мешала читать книгу на небольших разрешениях экрана
				// + кое что подправляем со стилями, чтобы стало совсем красиво и хорошо
				$("#chapters p:eq(0)").css({"font-family": "icons", "font-size": "20px", "padding": "5px 7px 10px 12px"});
				$("#chapters p:eq(0)").html(":");
				
				// здесь по клике на ссылку подгружаем новую главу
				$('#chapters p').not(":first-child").click(function (){
			        get_text($.booker_vars.filename, $('#chapters p').index(this), 999);
			    });
			});
			
			// показ и скрытие оглавления по клику на название книги
			$('#chapters').on('click', '#title', function (){
		        $('#chapters p').not(":first-child").toggle( 0 );
		    });
		    
		    
		    
		    //начинаем работать с текстом
		    get_text($.booker_vars.filename, $.booker_vars.chapter_number, $.booker_vars.page);
			$("#page_number").html($.booker_vars.page);
		    
		});
	</script>

	<body onkeydown="pages(event)">
		
		<div class="reader_text"></div>
		
		<span id="chapters"></span>
		
		<span id='chapter_number'>0</span>
		
		<span id='page_number'></span>
		
		<a href='http://avreolko.tmweb.ru/booker/'><span class="back"><p>&larr;</p></span></a>
	</body>
</html>

