<?php

/**
 * Mostra la pagina delle playlist presente nel menu laterale, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function wimtvpro_programming() {
    echo "<h3><b>Coming Soon</b></h3>";return;
    
//    if (!isConnectedToTestServer()) {
//        $imgUrl = get_option('wp_wimtvPluginPath') . "images/postit_blank.png";
//        $textMessage_EN = "<center><b>Currently not available</b></center>
//        We are working on a better version in next release.";
//
//        $textMessage_IT = "<center><b>Palinsensti disabilitati</b></center>
//        Stiamo lavorando ad una versione migliorata per la prossima release.";
//        print "<div style=\"
//                background: url('$imgUrl') no-repeat; 
//                width: 330px;
//                margin-top: 100px;    
//                margin-left: auto;    
//                margin-right: auto;    
//                    \">
//            <div style=\"
//                font-size: 1.3em !important;
//                position: relative;
//                top: 60px;
//                left: 60px;
//                width:250px;
//                height:354px;
//                overflow:hidden;
//                padding: 10px;
//                line-height: 2em;\">
//                $textMessage_IT <br/> $textMessage_EN 
//            </div>
//           </div>";
//        return;
//    }

    $view_page = wimtvpro_alert_reg();
    if (!$view_page) {
        die();
    }
    ?>

    <div class='wrap'>
        <?php
        echo wimtvpro_link_help();


        $page = isset($_GET['namefunction']) ? $_GET['namefunction'] : "";
        switch ($page) {
            case "newProgramming" || "modProgramming":
                if ($page == "newProgramming") {
                    _e("New Programming", "wimtvpro");
                    $progID = "";
                } else {
                    _e("Modify Programming", "wimtvpro");
                    $progID = isset($_GET["progId"]) ? $_GET["progId"] : "";
                }
                ?>
                <h2><a href='?page=<?php _e('SCHEDULES_urlLink', "wimtvpro"); ?>' class='add-new-h2'><?php echo __('Return to list', 'wimtvpro') ?></a></h2>
                <?php
                $locale = get_locale();
                $locale_parts = split("_", $locale);
                $locale = (sizeof($locale_parts) > 0) ? $locale_parts[0] : nul;
                echo apiProgrammingGetIframe($progID, $locale);

                break;
            default:

                if (isset($_GET["functionList"]) && ($_GET["functionList"] == "delete")) {
                    $idProgrammingDelete = isset($_GET["id"]) ? $_GET["id"] : "";
                    apiDeleteProgramming($idProgrammingDelete);
                }
                ?>

                <h2> <?php _e("Programmings", "wimtvpro"); ?> 
                    <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=newProgramming" ?>' class='add-new-h2'><?php echo __('New', 'wimtvpro') ?></a>
                </h2>

                <?php
                $response = apiGetProgrammings();
                $arrayjsonst = json_decode($response);
                ?>
                <table id='tableLive' class='wp-list-table widefat fixed pages'>
                    <thead>
                        <tr>
                            <th><?php _e("Title"); ?></th>
                            <th><?php _e("Modify", "wimtvpro"); ?></th>
                            <th><?php _e("Remove"); ?></th>
                            <th><?php _e("Shortcode", "wimtvpro"); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($arrayjsonst != null && $arrayjsonst->programmings != null) {
                            foreach ($arrayjsonst->programmings as $prog) {
                                if (!isset($prog->name))
                                    $titleProgramming = __("No title", "eventissimo");
                                else
                                    $titleProgramming = $prog->name;
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $titleProgramming; ?>
                                    </td>
                                    <td>
                                        <a href='?page=<?php _e('SCHEDULES_urlLink', "wimtvpro"); ?>&namefunction=modifyProgramming&title=<?php echo $titleProgramming; ?>&progId=<?php echo $prog->identifier; ?>' alt='<?php _e("Modify", "wimtvpro"); ?>' title='<?php _e("Modify", "wimtvpro"); ?>'><img src='<?php echo get_option('wp_wimtvPluginPath'); ?>images/mod.png'  alt='<?php _e("Modify", "wimtvpro"); ?>'></a>
                                    </td>
                                    <td>
                                        <a href='?page=<?php _e('SCHEDULES_urlLink', "wimtvpro"); ?>&functionList=delete&id=<?php echo $prog->identifier; ?>' alt='<?php _e("Remove"); ?>' title='<?php _e("Remove"); ?>'><img src='<?php echo get_option('wp_wimtvPluginPath'); ?>images/remove.png'  alt='<?php _e("Remove"); ?>'></a>
                                    </td>
                                    <td>
                                        <?php
                                        $height = get_option("wp_heightPreview");
                                        $width = get_option("wp_widthPreview");
                                        $shortcode = "[wimprog id=$prog->identifier width=$width height=$height]";
                                        ?>

                                        <textarea 
                                            style="resize: none; width:90%;height:100%;" 
                                            readonly='readonly' 
                                            onclick="this.focus();this.select();"
                                            ><?php echo $shortcode; ?>
                                        </textarea>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        echo "</tbody></table>";
                        ?>


                    <?php
                }
                echo "</div>";
            }
            ?>