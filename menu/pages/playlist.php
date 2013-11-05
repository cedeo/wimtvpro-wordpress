<?php
/**
 * Written by walter at 24/10/13
 */
 
include ("modules/playlist-logica.php");
 
function wimtvpro_playlist() {
    $view_page = wimtvpro_alert_reg();
    if (!$view_page){
     die();
    }

    global $wpdb;
    $nameFunction = isset($_GET["namefunction"]) ? $_GET["namefunction"] : "";
    $updated = false;
    $linkReturn = "";

	if ($nameFunction=="modPlaylist"){
        $linkReturn =  "<a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=listPlaylist' class='add-new-h2'>" . __( 'Return to list', 'wimtvpro') . "</a> ";
        $table_name = $wpdb->prefix . 'wimtvpro_playlist';
        if (isset($_POST["modPlaylist"]) && $_POST["modPlaylist"] =="true"){
            $sql = "UPDATE " . $table_name  . " SET name='" . $_POST["namePlaylist"] . "' ,listvideo='" . $_POST["listVideo"] . "' WHERE id='" . $_GET["id"] . "'";
            $wpdb->query($sql);
            $updated = true;
        }
		$playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' AND id=" . $_GET["id"]);
			
        if (count($playlist)>0) {
          $option = $playlist[0]->option;
          $array_option = explode(",",$option);
          $options = array();
          foreach ($array_option as $value){
            $array = explode(":",$value);
            if ($array[0]!="")
              $options[$array[0]] = $array[1];
          }
        } else {
            $options["loop"] = "";
            $playlist[0]->listVideo = "";
        }
    } else {
        //Count playlist saved in DB
        $table_name = $wpdb->prefix . 'wimtvpro_playlist';
        $array_playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "'  ORDER BY name ASC");
        $numberPlaylist=count($array_playlist);
        $playlists = array();
        if ($numberPlaylist>0) {
            foreach ($array_playlist as $record_new) {
                $record_new->listVideo = explode(",", $record_new->listVideo);
                if ($record_new->listVideo[0]=="") {
                    $record_new->countVideo = 0;
                } else {
                    $record_new->countVideo = count($record_new->listVideo);
                }
                array_push($playlists, $record_new);
            }
        }
    }

?>
    <div class='wrap'>
    <h2>Playlist <?php echo $linkReturn ?></h2>
    <p><?php echo __("Create a playlist of videos (ONLY FREE videos are possible) to be posted to your website","wimtvpro") ?></p>
    <p><?php echo __("Move videos from left to right","wimtvpro") ?></p>
    <?php if ($updated) {
        echo '<div class="updated"><p><strong>';
        _e("Update successful","wimtvpro");
        echo '</strong></p></div>';
    }
    if ($nameFunction == "modPlaylist") { ?>
        <form method="post" action="#">
            <input type="submit" class="icon_sync0 button-primary" value="<?php echo __("Update","wimtvpro") ?>" />

            <input type="hidden" class="list" name="listVideo" value="<?php echo $playlist[0]->listVideo ?>" />
            <input type="hidden" name="modPlaylist" value="true" />
            <input type="hidden" name="idPlaylist" value="<?php echo $_GET["id"] ?>" />
            <div id='post-body' class='metabox-holder columns-2'>
                <div id='post-body-content'>
                    <div id='titlediv'>
                        <div id='titlewrap'>
                            <input type='text' id='title' class='title' name='namePlaylist' value='<?php echo $playlist[0]->name ?>' />
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class='sortable1'>
            <?php echo __("All video","wimtvpro") ?>
            <table class='items_playlist' id='droptrue'>
                <?php echo str_replace("<td></td>","",wimtvpro_getThumbs_playlist($playlist[0]->listVideo,TRUE,TRUE,FALSE,"",FALSE)) ?>
                <tr class='appoggio'></tr>
            </table>
        </div>
        <div class='sortable2'>
            <b><?php echo __("Playlist","wimtvpro") ?></b>
            <table class='items_playlist' id='dropfalse'>
                <?php echo  str_replace("<td></td>","",wimtvpro_getThumbs_playlist($playlist[0]->listVideo,TRUE,TRUE,FALSE,"",TRUE)) ?>
                <tr class='appoggio'></tr>
            </table>
        </div>
    <?php } else { ?>
        <table  id='tablePlaylist' class='items wp-list-table widefat fixed pages'>
        <thead>
            <tr style='width:100%'>
                <th  style='width:30%'><?php echo __("Title") ?></th>
                <th style='width:30%'>N. Video</th>
                <th style='width:20%'><?php echo __("Preview") ?></th>
                <th style='width:20%'><?php echo __("Modify","wimtvpro") ?></th>
                <th style='width:20%'><?php echo __("Remove") ?></th>
            </tr>
        </thead>
        <tbody>

        <?php foreach($playlists as $index => $record) {?>

            <tr class="playlist" id="playlist_<?php echo $index ?>" rel="<?php echo $record->id ?>">

                <td><?php echo $record->name ?></td>
                <td><?php echo $record->countVideo ?></td>
                <td>
                    <span class="icon_viewPlay" id="<?php echo $record->id ?>"></span>
                </td>
                <td>
                    <a href="?page=WimTV_Playlist&namefunction=modPlaylist&id=<?php echo $record->id ?>">
                        <span class="icon_modPlay"></span>
                    </a>
                </td>
                <td>
                    <span class="icon_deletePlay"></span>
                </td>
            </tr>
        <?php
          }
        ?>
        <tr class="playlist new" id="playlist_<?php echo $numberPlaylist ?>" rel="">
            <td>
                <input type="text" value="Playlist <?php echo $numberPlaylist ?>" />
                <span class="icon_createPlay"></span>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
        </table>
    </div>
<?php
    }
}
?>