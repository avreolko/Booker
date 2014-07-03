<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>B O O K E R</title>
		<link rel="stylesheet" href="style.css">
	</head>
	
	<body>
		<div class="logo">B O O K E R</div>
		<div class="list_of_books">
			<?
			
			function file_get_contents_utf8($fn) {
			     $content = file_get_contents($fn);
			      return mb_convert_encoding($content, 'UTF-8',
			          mb_detect_encoding($content, 'UTF-8, ISO-8859-1, Windows-1251', true));
			}
			
			
			function data_uri($file, $mime) {  
			  $contents = file_get_contents($file);
			  $base64   = base64_encode($contents);
			  return ('data:' . $mime . ';base64,' . $base64);
			}
			
				if ($handle = opendir('books')) {
				    while (false != ($entry = readdir($handle))) {
				    	
				        if ($entry != "." && $entry != "..") {
				            $i++;
				            $file = file_get_contents_utf8("books/$entry");
				            
				            if($file){
			
								$pattern = "'<book-title>(.*?)</book-title>'si";
								preg_match($pattern, $file, $matches);
								$title=$matches[1];
								
								
								$pattern = "'<author>(.*?)</author>'si";
								preg_match($pattern, $file, $matches);
								$author=$matches[1];
								
								$xml = simplexml_load_string($file);
								foreach($xml->binary as $binary)
								{
								    if($binary->attributes()->id == 'cover.jpg' OR $binary->attributes()->id == 'pic_1.png'){
								    	echo "<a href='reader.php?book=$entry'><div class='cover_and_title'>";
									    echo "<img height=320 src='data:image/png;base64,$binary'>";
									    echo "<span class='title_of_book'>$title</span>";
									    echo "<span class='author_of_book'>$author</span>";
									    echo "</div></a>";
								    }
								       
								}
								
							}
				        }
				    }
				    closedir($handle);
				}
			?>
		</div>
	</body>
</html>