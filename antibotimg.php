<?php

	define("IMG_HEIGHT", 100);
	define("IMG_WIDTH", IMG_HEIGHT*2);
	define("LTTER_IMG_SIZE", 20);
	
	define("NO_DATABASE_ACTIONS", true);
	include_once("includes/common.include.php");
	
	function arr_to_color($arr){
		global $img;
		return imagecolorexact($img, $arr[0], $arr[1], $arr[2]);
	}
	
	if( !function_exists("imagerotate") ):
	function imagerotate(&$image, $int, $clr){
		return $image;
	}
	endif;

	$img = imagecreatetruecolor(IMG_WIDTH, IMG_HEIGHT);
	
	$white = imagecolorexact($img, 255, 255, 255);
	imagefilledrectangle($img, 0,0,IMG_WIDTH, IMG_HEIGHT, $white);
	
	$colors = array_map("arr_to_color", array( array(255,0,0), array(0,255,0), array(0,0,255), array(126,126,126), array(100,20,230) ));
	$rand_max = count($colors)-1;
	
	$str = anti_bot_str($_GET['str']);
	
	$pos = 0;
	foreach( str_split($str) as $letter ){
		$letimg = imagecreatetruecolor(LTTER_IMG_SIZE,LTTER_IMG_SIZE);
		imagepalettecopy($letimg,$img);
		imagefilledrectangle($letimg, 0,0,LTTER_IMG_SIZE, LTTER_IMG_SIZE, $white);
		imagestring($letimg, 5, 0, 0, $letter, $colors[rand(0,$rand_max)]);
		$rotate = imagerotate($letimg,rand(-90,90),$white);
		
		imagecopymerge($img, $rotate, 10+$pos, IMG_HEIGHT/2-10, 0,0, LTTER_IMG_SIZE,LTTER_IMG_SIZE, 100 );
		
		imagedestroy($letimg);
		imagedestroy($rotate);
		$pos += LTTER_IMG_SIZE;
	}
	
	header("Content-Type: image/png");
	imagepng($img);
	
	imagedestroy($img);
?>