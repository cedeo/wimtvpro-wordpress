<?php
/**
 * Questa funzione ritorna la lista delle categorie selezionabili per un video.
 * Viene utilizzata quando viene fatto l'upload di un video per permettere la scelta delle categorie.
 */
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