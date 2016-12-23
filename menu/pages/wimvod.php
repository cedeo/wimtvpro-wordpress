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
    /* $user = wp_get_current_user();

      $idUser = $user->ID;
      $userRole = $user->roles[0];
      if ($user->roles[0] == "administrator"){
      $title .= "<span class='icon_save' id='save'>" . __("Save") . "</span>";
      } */
//    NS2016   $param = array(
//            
//            'public' => 'false',//opzionale, Whether the WimVod item has to be visible in public pages or not. 
//            'licenseType' => 'FREE'
//          
//        );
            
// NS2016    $response = apiPublishOnShowtime('6ff954e5-9e34-473d-b3eb-cac502b911b1', $param);
    
    
//        $param = array(
//            
//            'pageSize' => '20',//opzionale, Whether the WimVod item has to be visible in public pages or not. 
//            'pageIndex' => '0'
//          
//        );
//    apiGetDetailsShowtime('3f03bde2-a590-4e36-8c1a-19243e1d0b85'); 
//   NS2016 $params = array(
//            
//            'vodId' => '0b0e9665-15a2-450a-a5ac-06af9c02c8fd',
//             'licenseType' => 'FREE'
//        );
//    
//    $response =  apiPlayWimVodItem($params);
//     var_dump($response);exit;

    $view_page = wimtvpro_alert_reg();
    if (!$view_page) {
        die();
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {

            /*SORTABLE*/
            jQuery(".items tbody").sortable({
                placeholder: "ui-state-highlight",
                handle: ".icon_moveThumbs",
                out: function(event, ui) {
                    var ordina = jQuery(".items tbody").sortable("toArray");

                    jQuery.ajax({
                        context: this,
                        url: url_pathPlugin + "scripts.php",
                        type: "GET",
                        dataType: "html",
                        data: "namefunction=ReSortable&ordina=" + ordina,
                        error: function(request, error) {
                            alert(request.responseText);
                        }
                    });
                }

            });
        });
        jQuery(document).ready(function() {

            jQuery("a.viewThumb").click(function() {
                var url = jQuery(this).attr("id");
                jQuery(this).colorbox({href: url,width: '530px',scrolling: false,
                onComplete: function() {
                    jQuery(this).colorbox.resize();            
                }});
            });
            jQuery("a.wimtv-thumbnail").click(function() {
                if (jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length) {
                    var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
                    jQuery(this).colorbox({href: url,width: '530px',scrolling: false,
                onComplete: function() {
                    jQuery(this).colorbox.resize();            
                }});
                }
            });
        });

    </script>

    <div class='wrap'>
        <?php echo wimtvpro_link_help(); ?>
        <h2><?php _e('WIMVOD_pageTitle', "wimtvpro"); ?></h2>
        <p><?php echo __("Here you can", "wimtvpro") . " " . __("Manage the videos you want to publish, both in posts and widgets", "wimtvpro") ?></p>
       

                    <div class='action'>
                        <span class='icon_sync_vod button-primary' title='Synchronize'><?php echo __("Synchronize", "wimtvpro") ?></span>
                    </div>
                    <div id='post-body' class='metabox-holder columns-2'>
                        <div id='post-body-content'>
                            <table  id='TRUE' class='items wp-list-table widefat fixed pages'>
                                <thead>
                                    <tr style='width:100%'>
                                        <th  style='width:20%'>Video</th>
                                        <?php if (current_user_can("administrator")) { ?>
                                            <th style='width:15%'><?php echo __("Posted", "wimtvpro") ?></th>
                                            <th style='width:20%'><?php echo __("Change position", "wimtvpro") ?></th>
                                            <!--<
                                            // NS: HIDE PRIVACY
                                            th style='width:20%'>Privacy</th>
                                            -->
                                        <?php } ?>
                                        <th style='width:20%'><?php _e("License","wimtvpro"); ?></th>
                                        <th style='width:25%'>Shortcode</th>
                                        <th style='width:10%'><?php echo __("Preview") ?></th>
                                        <th style='width:0%'></th>
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