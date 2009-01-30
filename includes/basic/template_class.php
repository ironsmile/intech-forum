<?php
/*
*
*	template class examples:
*	
*	$tpl = new template("example");
*	$tpl->append_content("NAV","<a href='#'>Nav1</a>");
*	$tpl->append_content("MAIN","Some text here");
*	print $tpl->render();
*
*/

class template{
	
	var $template_str;
	var $sections_array;
	var $template_name;
	
	function template( $temp_name ){
		$this->sections_array = array();
		$this->template_name = $temp_name;
		$this->load_file($temp_name);
	}
	
	/*
	*	$temp_name - imeto na templata v papka $templates_dir
	*	avtomati4no pribavq ".tpl" kym imeto
	*/
	function load_file( $temp_name ){
		
		$templates_dir = defined("TEMPLATES_DIR") ? TEMPLATES_DIR : "templates/";
		
		$full_name = $templates_dir.$temp_name.".tpl";
		$this->template_str = is_file($full_name) ? 
			file_get_contents( $full_name ) : "<p>No Such Template: <q>$full_name</q></p>" ;
	}
	
	function append_content( $section, $string ){
		if( !isset( $this->sections_array[$section] ) )
			$this->sections_array[$section] = array();
		$this->sections_array[$section][] = $string;
	}
	
	/*
	*	tyrsi secii ot tipa <!-- @EXAMPLE --> v templata
	*	i gi zamenq s syzurjanieto na $sections_array. V slu4aq s $sections_array['EXAMPLE']
	*/
	function render(){
		$ret_str = $this->template_str;
		foreach( $this->sections_array as $section => $strings_arr ){
			$section = "<!-- @".$section." -->";
			$ret_str = str_replace($section,implode("",$strings_arr).$section,$ret_str);
		}
		
		return $ret_str;
	}

};
?>