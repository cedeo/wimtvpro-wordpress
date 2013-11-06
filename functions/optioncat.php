<?php
function wimtvpro_readOptionCategory(){
	$category="";
	$response = apiGetVideoCategories();
	$category_json = json_decode($response);

	foreach ($category_json as $cat) {
	  foreach ($cat as $sub) {
		$category .= '<optgroup label="' . $sub->name . '">';
		foreach ($sub->subCategories as $subname) {
		  $category .= '<option value="' . $sub->name . '|' . $subname->name . '">' . $subname->name . '</option>';
		}
		$category .= '</optgroup>';
	  }
	}
	return  $category;
}
?>