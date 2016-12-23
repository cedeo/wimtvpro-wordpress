<?php
//require_once('/var/www/htdocs/wimtvPlugin/wordpress4/wp-load.php');


if (isset($_GET["isAdmin"])&& isset($_GET["id"])) {
    $id = $_GET['id'];
    echo includePlaylist($id);
}

function includePlaylist($playlist_id, $width = null, $height = null) {

    $is_admin  =isset($_GET["isAdmin"]);
//    if (isset($_GET["isAdmin"])) {
//        $is_admin = true;
//
//    } else {
//        $is_admin = false;
//    }
    
//    if ($is_admin) {
//        $parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
//        $url_include = $parse_uri[0] . 'wp-load.php';
//        require_once($url_include);
//    } else {
//        $is_admin = false;
//    }
    
   
    $width = isset($width) ? $width : get_option('wp_widthPreview');
    $height = isset($height) ? $height : get_option('wp_heightPreview');
//$width = 500;
//$height = 280;
//var_dump("AAA");exit;
    ob_start();
    if ($is_admin) {
        
        $parse_uri = explode('index.php', $_SERVER['SCRIPT_FILENAME']);
      
//        $parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
//        $url_include = $parse_uri[0] . '/wp-load.php';       
        $url_include = $parse_uri[0] . '/wp-load.php';
             
        require_once("../../../../wp-load.php");
//    var_dump($url_include);exit; 
        ?>
        <div style='text-align:center;'><h3><?php echo $title ?></h3>
    <?php } else { ?>
            <div style='text-align:center;width:100%;'>
            <?php
        }
//            <div id='container-<?php echo $playlist_id ? >' style='margin:0;padding:0 10px;'></div>

        echo configurePlayer_PlaylistJS($playlist_id, $width, $height);
        if ($is_admin) {
            ?>
                <div style='float:left; width:50%;'>
                    Embedded:
                    <textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus();
                                    this.select();'>
                              <?php echo htmlentities($code) ?>
                    </textarea>
                </div>
                <div style='float:left; width:50%;'>
                    Shortcode:
                    <textarea style='resize: none; width:90%;height:70px;font-size:20px' readonly='readonly' onclick='this.focus();
                                    this.select();'>
                        [playlistWimtv id='<?php echo $playlist_id ?>']
                    </textarea>
                </div>
    <?php } ?>
        </div>
            <?php
            return ob_get_clean();
        }