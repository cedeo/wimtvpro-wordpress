<?php
/**
 * Written by walter at 24/10/13
 */
/**
 * Mostra la pagina WimBox presente nel menu laterale, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
header('Content-type: text/html');
//WimBox
include_once('modules/wimbox.php');

/// LOGICA
function wimtvpro_wimbox() {
    $view_page = wimtvpro_alert_reg();
    if (!$view_page) {
        die();
    }

    $sql_order = "";
    $sql_where = "";
    // NS: USING "GET" METHOD INSTEAD OF "POST"
    //    $titleVideo = isset($_POST['titleVideo']) ? $_POST['titleVideo'] : "";
    //    $orderTitleVideo = isset($_POST['ordertitleVideo']) ? $_POST['ordertitleVideo'] : "";
    //    $orderdateVideo = isset($_POST['orderdateVideo']) ? $_POST['orderdateVideo'] : "";
    $titleVideo = isset($_GET['titleVideo']) ? $_GET['titleVideo'] : "";
    $titleVideoNot = isset($_GET['titleVideoNot']) ? $_GET['titleVideoNot'] : "";
    $adminPage = isset($_GET['page']) ? $_GET['page'] : "";
    
    $orderTitleVideo = isset($_GET['ordertitleVideo']) ? $_GET['ordertitleVideo'] : "";
    $orderdateVideo = isset($_GET['orderdateVideo']) ? $_GET['orderdateVideo'] : "";

    if ($titleVideo != "") {
        $queryStart = "title";
        if ($titleVideoNot == "on") {
            $queryStart.=" NOT";
        }
        $sql_where = $queryStart . " LIKE '%" . $titleVideo . "%' ";
    }
    if ($orderTitleVideo != "") {
        $sql_order = " title " . $orderTitleVideo;
        $orderdateVideo = "";
    }
    if ($orderdateVideo != "") {
        $orderTitleVideo = "";
        $sql_order .= " mytimestamp " . $orderdateVideo;
    }

    $videos = wimtvpro_getVideos(FALSE, TRUE, FALSE, '', $sql_where, $sql_order);
    ?>

    <!----- HTML ----->
    <div class='wrap'>
        <?php echo wimtvpro_link_help(); ?>
        <h2>WimBox <a href='admin.php?page=WimTV_Upload' class="add-new-h2"><?php _e('Upload Video', "wimtvpro"); ?></a></h2>
        <p>
            <?php _e("Here you find all videos you have uploaded. If you wish to post one of these videos on your site, move it to WimVod by clicking the corresponding icon", "wimtvpro"); ?>
        </p>

        <div class='action'><span class='icon_sync0 button-primary' title='Synchronize'><?php _e("Synchronize", "wimtvpro"); ?></span></div>

        <?php if ($videos != "") { ?>
            <form method="get" action="#">
                <input type='hidden' value='<?php echo $adminPage ?>' name='page' />
                <table>
                    <tr>
                        <td><b><? echo __("Search") ?></b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><label for="title"><?php echo __("video title", "wimtvpro") ?>:</label></td>
                        <td><input type='text' value='<?php echo $titleVideo ?>' name='titleVideo' /></td>
                        <td><input type="submit" class="button button-primary" value="<?php echo __("Search") ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <?php $titleVideoNot = ($titleVideoNot == "on") ? "checked" : ""; ?>
                            <label for="titleVideoNot"><?php echo __("Not contains", "wimtvpro") ?></label>
                            <input type='checkbox' <?php echo $titleVideoNot ?> name='titleVideoNot' />
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td><b><? echo __("Order", "wimtvpro") ?></b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><label for="title"><?php echo __("by title", "wimtvpro") ?>:</label></td>
                        <td>
                            <select name="ordertitleVideo">
                                <option value=""
                                <?php if ($orderTitleVideo == "") echo ' selected="selected"' ?>
                                        >---
                                </option>
                                <option value="ASC"
                                <?php if ($orderTitleVideo == "ASC") echo ' selected="selected"' ?>
                                        >ASC
                                </option>
                                <option value="DESC"
                                <?php if ($orderTitleVideo == "DESC") echo ' selected="selected"' ?>
                                        ><?php echo __("DESC", "wimtvpro") ?>
                                </option>
                            </select>
                        </td>
                        <td><input type="submit" class="button button-primary" value="<?php echo __("Order", "wimtvpro") ?>"></td>
                    </tr>
                </table>
            </form>

        <?php } ?>
        <br/>
        <table  id='FALSE' class='items wp-list-table widefat fixed pages'>
            <thead>
                <tr style='width:100%'>
                    <th  style='width:20%'>Video</th>
                    <!-- NS: thumb -->
                    <th  style='width:10%'>Thumbnail</th>
                    <th style='width:10%'><?php echo __("Posted", "wimtvpro") ?></th>
                    <th style='width:10%'>Download</th>
                    <th style='width:10%'><?php echo __("Preview") ?></th>
                    <th style='width:10%'><?php echo __("Remove") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $videos ?>
            </tbody>
        </table>
        <div class='loaderTable'></div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function() {

            jQuery(".icon_download").click(function() {
                var id = jQuery(this).attr("id").split("|");
                var uri = url_pathPlugin + "scripts.php?namefunction=downloadVideo&id=" + id[0];
                jQuery("body").append("<iframe src=\"" + uri + "\" style=\"display:none;\" />");
            });


            jQuery("a.viewThumb").click(function() {
                var url = jQuery(this).attr("id");
                jQuery(this).colorbox({href: url});
            });

            jQuery("a.wimtv-thumbnail").click(function() {
                if (jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length) {
                    var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
                    jQuery(this).colorbox({href: url});
                }
            });

        });

    </script>

    <script>

        jQuery(".box_search").click(function() {
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