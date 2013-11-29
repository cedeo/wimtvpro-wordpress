<?php
/**
 * Written by walter at 06/11/13
 */
function update_page_wimvod(){
    if (get_option("wp_publicPage")=="Yes"){
        global $wpdb;
        $post_id  = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts WHERE post_name LIKE 'my_streaming_wimtv%'");
        $my_streaming_wimtv= array();
        $my_streaming_wimtv['ID'] = $post_id;
        $my_streaming_wimtv['post_content'] = "<div class='itemsPublic'>" . wimtvpro_getVideos(TRUE, FALSE, FALSE, "page") . "</div>";
        wp_update_post($my_streaming_wimtv);

        if (get_option("wp_publicPage")=="Yes"){
            change_post_status($post_id,'publish');
        } else {
            change_post_status($post_id,'private');
        }
    }
}

function change_post_status($post_id,$status){
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = $status;
    wp_update_post($current_post);
}

?>