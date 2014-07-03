<?

function isRussian($text) {
    return preg_match('/[\p{Cyrillic}]/u', $text);
}

function convert_smart_quotes($string) 
{ 
    $search = array(chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(151), 
                    chr(133),
                    chr(150)); 

    $replace = array("'", 
                     "'", 
                     '"', 
                     '"', 
                     '-', 
                     '...',
                     '--');
                     
    $string = str_replace('—', '&nbsp;', $string);
    $string = str_replace('•', '*', $string);
    
    return str_replace($search, $replace, $string);
}

	$action = $_GET['action'];
	
	if ($action == "get_sections_positions"){
		$book = $_GET['book'];
		$file = file_get_contents("books/$book", FILE_TEXT);
		
		$toFind = "<section>";
		$start = 0;
		
		$pos = 1;
		$i = 0;
		
		while( $pos !== false ) {
	        $i++;
	        
	        $start = $pos+1; // start searching from next position.
	        
	        $pos = strpos($file, $toFind, $start);
	        
	        if($pos != FALSE){
		        $farray[$i] = $pos;
	        
		        echo "$pos ";
	        }
	    }
	    
	    echo" <br> ";
	    
	    $toFind = "</section>";
		$start = 0;
		
		$pos = 1;
		$i = 0;
		
		while( $pos !== false ) {
	        $i++;
	        
	        $start = $pos+1; // start searching from next position.
	        
	        $pos = strpos($file, $toFind, $start);
	        
	        if($pos != FALSE){
		        $farray[$i] = $pos;
	        
		        echo "$pos ";
	        }
	    }
	    
	}else if ($action == "get_section"){
		
		$book = $_GET['book'];
		
		$from = $_GET['from'];
		$to = $_GET['to'];
		
		$number_of_characters = $to - $from;
		
		$file = file_get_contents("books/$book", NULL, NULL, $from, $number_of_characters);
		
		if(!isRussian($file)){
			$file = convert_smart_quotes($file);
		}
		
		echo $file;
	
	}else if ($action == "get_chapters"){
		
		$book = $_GET['book'];
		$file = file_get_contents("books/$book");
		
		$pattern = "'<title>(.*?)</title>'si";
		
		preg_match_all($pattern, $file, $matches);
		
		$matches[1][0] = "<div id='title'>".$matches[1][0]."</div>";
		$string = implode("<separator>", $matches[1]);
		
		if(!isRussian($string)){
			$string = convert_smart_quotes($string);
		}
		
		if(strpos($string, "empty-line")){
			$dom = new DOMDocument();
	
			// suppress the warnings, load HTML and clear errors
			libxml_use_internal_errors(true);
			$dom->loadHTML($string);
			libxml_clear_errors();
			
			$xpath = new DOMXPath($dom);
			foreach ($xpath->query('//empty-line') as $node) {
			    $node->parentNode->removeChild($node);
			}
			
			echo $dom->saveHTML();
		}else{
			echo $string;
		}
	
	}else if ($action == "get_chapter"){
		$book = $_GET['book'];
		$chapter_number = $_GET['chapter_number'];
		
		$file = file_get_contents("books/$book");
		
		// находим позиции глав
		$toFind = "<section>";
		$start = 0;
		
		$pos = 1;
		$i = 0;
		
		while( $pos !== false ) {
	        $i++;
	        
	        $start = $pos+1; // start searching from next position.
	        
	        $pos = strpos($file, $toFind, $start);
	        
	        if($pos != FALSE){
		        $array_of_sections[$i] = $pos;
	        }
	    }
	    
	    
	    $toFind = "</section>";
		$start = 0;
		
		$pos = 1;
		$i = 0;
		
		while( $pos !== false ) {
	        $i++;
	        
	        $start = $pos+1; // start searching from next position.
	        
	        $pos = strpos($file, $toFind, $start);
	        
	        if($pos != FALSE){
		        $array_of_end_sections[$i] = $pos;
	        }
	    }
	    // закончили находить позиции глав
	    
	    
		$chapter = file_get_contents("books/$book", NULL, NULL, $array_of_sections[$chapter_number], $array_of_end_sections[$chapter_number] - $array_of_sections[$chapter_number]);
		
		$chapter = str_replace("<title>", "<h3>", $chapter);
		$chapter = str_replace("</title>", "</h3>", $chapter);
		
		if(!isRussian($chapter)){
			$chapter = convert_smart_quotes($chapter);
		}
		
		echo $chapter;
	}
?>