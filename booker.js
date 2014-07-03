function get_sections_positions(book_filename){
	$.ajax({
	  type: "GET",
	  url: "loader.php",
	  data: { book: "hp1.fb2", action: "get_sections_positions" }
	}).done(function( result ) {
	  var array_of_sections = result.split("<br>");
	  return array_of_sections;
	});
}

function get_chapter(chapter_number, book_file){
	$.ajax({
	  type: "GET",
	  url: "loader.php",
	  data: { book: book_file, action: "get_chapter", chapter_number: chapter_number }
	}).done(function( result ) {
	  $.booker_vars.chapter_text = result;
	});
}

function get_section_start_and_offset( number_of_section ){
	$.ajax({
		type: "GET",
		url: "loader.php",
		data: { book: "hp1.fb2", action: "get_sections_positions" }
	}).done(function( result ) {
		var array_of_sections = result.split("<br>");
		
		var array_of_sections_starts = array_of_sections[0].split(" ");
		var array_of_sections_ends = array_of_sections[1].split(" ");
		
		var section_start = array_of_sections_starts[number_of_section];
		var section_offset = array_of_sections_ends[number_of_section] - array_of_sections_starts[number_of_section]
		
		var start_and_offset = [section_start, section_offset];
		
		$("#results").append(section_start);
	});
}