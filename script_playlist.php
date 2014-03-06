<?php
/**
 * Questo file viene chiamato via Ajax per fornire un'interfaccia con cui creare e modificare le playlist.
 * Attraverso il parametro GET 'namefunction' viene scelta l'azione da intraprendere
 */
global $user,$wpdb;
  include("../../../wp-blog-header.php");

  if (isset($_GET['namefunction']))
    $function= $_GET["namefunction"];

  if (isset($_GET['namePlayList']))
    $name= $_GET["namePlayList"];
  
  if (isset($_GET['idPlayList']))
    $idPlayList = $_GET["idPlayList"];
    
    if (isset($_GET['id']))
    $id = $_GET["id"];


  switch ($function) {
  
    case "AddVideoToPlaylist":
        /**
         * Richiede che vengano passati anche come parametri GET 'namePlayList', 'idPlayList' e 'id' (id del video).
         * Aggiunge un video alla playlist.
         */
    	$listVideo = "";


    	$playlist = dbExtractSpecificPlayist($idPlayList);

        foreach ($playlist as $record) {
			$listVideo = $record->listVideo;
			$name = $record->name;
		}
		
		//Check if this file exist

		if ( strpos($listVideo,trim($id))>-1) {
			echo "This video exist into " . $name . " playlist.";
	        die ();
		
		} else {
		
	    	// UPDATE into DB (campo listVideo)
	    	if ($listVideo=="")
	    		$listVideo = $id;
	    	else
	    		$listVideo = $listVideo . "," . $id;

            dbUpdatePlaylist($idPlayList, $listVideo);
	    	
	        die ();
		}
		
    break;
  
    case "createPlaylist":
        /**
         * Richiede che venga passato come parametro GET 'namePlayList'.
         * Crea una nuova playlist.
         */
        $uploads_info = wp_upload_dir();
        $directory = $uploads_info["basedir"] .  "/playlistWim";
		   if (!is_dir($directory)) {
			  $directory = mkdir($uploads_info["basedir"] . "/playlistWim");
			}

        dbInsertPlayist(get_option('wp_userwimtv'), $name);
	           	
        die();
    
    break;
    
    case "modTitlePlaylist":
        /**
         * Richiede che vengano passati anche come parametri GET 'namePlayList' e 'idPlayList'.
         * Modifica il titolo della playlist.
         */

        dbUpdatePlaylist($idPlayList, $name);

        die();
    
    break;

	case "removePlaylist":
        /**
         * Richiede che venga passato come parametro GET 'idPlayList'.
         * Rimuove la playlist.
         */
	    $uploads_info = wp_upload_dir();

        dbDeletePlayist($idPlayList);

        die();
    
    break;

    
    default:
        /**
         * Stampa "non entro" se il nome della funzione richiesta non corrisponde a nessuno di quelli elencati.
         * Scelta di Simona, vai a capire.
         */
        echo "Non entro";
        die();
  }
    
?>