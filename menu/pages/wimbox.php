<?php
/**
 * Written by walter at 24/10/13
 */
header('Content-type: text/html');
//WimBox
include_once('modules/wimbox.php');

/// LOGICA
function wimtvpro_wimbox () {

    $view_page = wimtvpro_alert_reg();
    if (!$view_page){
        die();
    }

    $sql_order = "";
    $sql_where = "";
    $titleVideo = isset($_POST['titleVideo']) ? $_POST['titleVideo'] : "";
    $orderTitleVideo = isset($_POST['ordertitleVideo']) ? $_POST['ordertitleVideo'] : "";
    $orderdateVideo = isset($_POST['orderdateVideo']) ? $_POST['orderdateVideo'] : "";

    if ($titleVideo != "") {
        $sql_where  = " AND title LIKE '%" . $titleVideo . "%' ";
    }
    if ($orderTitleVideo != "") {
        $sql_order  = " title " . $orderTitleVideo;
        $orderdateVideo = "";
    }
    if ($orderdateVideo != "") {
        $orderTitleVideo = "";
        $sql_order  .= " mytimestamp " . $orderdateVideo;
    }

    $videos= wimtvpro_getVideos(FALSE,TRUE,FALSE,'',$sql_where, $sql_order);
?>

    <!----- HTML ----->
    <div class='wrap'>
    <?php  echo  wimtvpro_link_help();?>
        <h2>WimBox <a href='admin.php?page=WimTV_Upload' class="add-new-h2"><?php _e('Upload Video',"wimtvpro");?></a></h2>
        <p>
            <?php _e("Here you find all videos you have uploaded. If you wish to post one of these videos on your site, move it to WimVod by clicking the corresponding icon", "wimtvpro"); ?>
        </p>
        
        <div class='action'><span class='icon_sync0 button-primary' title='Synchronize'><?php _e("Synchronize","wimtvpro"); ?></span></div>

        <?php if ($videos != "") { ?>
            <form method="post" action="#">
                <b><? echo __("Search") ?></b>
                <label for="title"><?php echo __("video title","wimtvpro") ?>:</label>
                <input type='text' value='<?php echo $titleVideo ?>' name='titleVideo' />
                <input type="submit" class="button button-primary" value="<?php echo __("Search") ?>">
                <br/>
                <b><?php echo __("Order","wimtvpro")  ?></b>
                <label for="title"><?php echo __("by title","wimtvpro") ?>:</label>
                <select name="ordertitleVideo">
                    <option value=""
                        <?php if ($orderTitleVideo=="") echo ' selected="selected"'?>
                        >---
                    </option>
                    <option value="ASC"
                        <?php if ($orderTitleVideo=="ASC") echo ' selected="selected"'?>
                        >ASC
                    </option>
                    <option value="DESC"
                        <?php if ($orderTitleVideo=="DESC") echo ' selected="selected"'?>
                        ><?php echo __("DESC","wimtvpro") ?>
                    </option>
                </select>
                <input type="submit" class="button button-primary" value="<?php echo __("Order","wimtvpro") ?>">
            </form>

        <?php } ?>
        <br/>
        <table  id='FALSE' class='items wp-list-table widefat fixed pages'>
            <thead>
                <tr style='width:100%'>
                    <th  style='width:30%'>Video</th>
                    <th style='width:30%'><?php echo __("Posted","wimtvpro") ?></th>
                    <th style='width:30%'>Download</th>
                    <th style='width:20%'><?php echo __("Preview") ?></th>
                    <th style='width:20%'><?php echo __("Remove") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $videos  ?>
            </tbody>
        </table>
        <div class='loaderTable'></div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function(){

            jQuery(".icon_download").click(function() {
                var id = jQuery(this).attr("id").split("|");
                var uri =  url_pathPlugin + "scripts.php?namefunction=downloadVideo&id=" + id[0];
                jQuery("body").append("<iframe src=\"" + uri + "\" style=\"display:none;\" />");
            });


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

    <script>

        jQuery(".box_search").click(function(){
            var search = jQuery(".search2");
            jQuery(search).fadeToggle();

            if (jQuery(search).css("opacity") == 0)
                jQuery(this).html("<?php echo __("Close") ?>");
            else
                jQuery(this).html("<?php echo __("Search") ?>");

        });

    </script>

<?php
}
?>