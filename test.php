<?
			error_reporting(~0);
			ini_set('display_errors', 1);

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
			
				if ($handle = opendir('test_folder')) {
				$i = 0;
				    while (false !== ($entry = readdir($handle))) {
				    	
				        if ($entry != "." && $entry != "..") {
				            $i++;
				            $file = file_get_contents_utf8("test_folder/$entry");
				            
				            
				            
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
								echo 1;
								    if($binary->attributes()->id == 'cover.jpg'){
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