<?php
/**
 * Written by walter at 24/10/13
 */
/**
 * Mostra la pagina WimVod presente nel menu laterale, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function wimtvpro_mystreaming() {
    /*$user = wp_get_current_user();

    $idUser = $user->ID;
    $userRole = $user->roles[0];
    if ($user->roles[0] == "administrator"){
      $title .= "<span class='icon_save' id='save'>" . __("Save") . "</span>";
    }*/
	
	$view_page = wimtvpro_alert_reg();
    if (!$view_page){
        die();
    }

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){

          /*SORTABLE*/
          jQuery( ".items tbody" ).sortable({
              placeholder: "ui-state-highlight",
              handle : ".icon_moveThumbs",

              out: function( event, ui ) {
                    var ordina =	jQuery(".items tbody").sortable("toArray") ;

                    jQuery.ajax({
                        context: this,
                        url:  url_pathPlugin + "scripts.php",
                        type: "GET",
                        dataType: "html",
                        data: "namefunction=ReSortable&ordina=" + ordina,
                        error: function(request,error) {
                            alert(request.responseText);
                        }
                    });
              }

          });
      });
        jQuery(document).ready(function(){

           jQuery("a.viewThumb").click( function(){
            var url = jQuery(this).attr("id");
            jQuery(this).colorbox({href:url});
           });
           jQuery("a.wimtv-thumbnail").click( function(){
              if( jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length  ) {
                var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
                jQuery(this).colorbox({href:url});
              }
            });
         });

    </script>

    <div class='wrap'>
    	<?php  echo  wimtvpro_link_help();?>
        <h2>WimVod</h2>
        <p><?php echo __("Here you can","wimtvpro") . " " . __("Manage the videos you want to publish, both in posts and widgets","wimtvpro") ?></p>
        <p><?php _e("Shortcode to post all videos:","wimtvpro");?><b>[wimvod]<b></p>
        
        <div class='action'>
            <span class='icon_sync0 button-primary' title='Synchronize'><?php echo __("Synchronize","wimtvpro") ?></span>
        </div>
        <div id='post-body' class='metabox-holder columns-2'>
            <div id='post-body-content'>
                <table  id='TRUE' class='items wp-list-table widefat fixed pages'>
                    <thead>
                    <tr style='width:100%'>
                        <th  style='width:20%'>Video</th>
                        <th style='width:15%'><?php echo __("Posted","wimtvpro") ?></th>
                        <th style='width:20%'><?php echo __("Change position","wimtvpro") ?></th>
                        <th style='width:20%'>Privacy</th>
                         <th style='width:20%'>Shortcode</th>
                        <th style='width:20%'>Download</th>
                        <th style='width:15%'><?php echo __("Preview") ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php echo wimtvpro_getVideos(TRUE) ?>
                    </tbody>
                </table>
                <div class='loaderTable'></div>
            </div>
        </div>
    </div>
<?php
}
?>