<?php

function wimtvpro_programming_embedded($progId){

	$basePath = get_option("wp_basePathWimtv");
	$height = get_option("wp_heightPreview")+100;
	$width = get_option("wp_widthPreview");
	$iframe ='<div class="wrapperiframe" style="max-width:"' . $width . 'px" ><div class="h_iframe"><iframe src="' . $basePath . 'programming/' . $progId . '/embedded" frameborder="0" allowfullscreen style="overflow:hidden;" style="height:"' . $height . 'px;width:2px"></iframe></div></div>';
	return $iframe;
}

?>