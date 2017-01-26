<div id="play_<?php echo $id ?>" ></div>
<div id="pay_video<?php echo $id ?>" 
     style="display:none;margin:0px 0px 10px 0px;" >  
    <div id="videoPAYVod<?php echo $id ?>"
         style="width:<?php echo $width ?> px;height:<?php echo $height ?>px;">
        <img id="icon_play_vod<?php echo $id ?>" 
             src="<?php echo site_url() ?>/wp-content/plugins/wimtvpro/images/play.png" 
             style="max-width:10%;z-index:10;display:block;position: relative;top: 55%;left: 45%;" />
        <img id="icon_thumb_play_vod<?php echo $id ?>"
             src="<?php _e("API_URL", "wimtvpro") ?>asset/thumbnail/<?php echo $thumbnailId ?>"
             style="width:<?php echo $width ?> px;height:<?php echo $height ?>px;z-index: -10;" />
    </div>
</div>

<script>
jQuery(document).ready(function() {
    var vid = "<?php echo $id ?>";
    var pricePerView = "<?php echo $pricePerView ?>";
    var thumbnailId = "<?php echo $thumbnailId ?>";
    var width = "<?php echo $width ?>";
    var height = "<?php echo $height ?>";

    if (localStorage.getItem(vid) === null) {

        var url_pathPlugin = "<?php echo plugin_dir_url(__FILE__) ?>";
        var ourLocation = document.URL;
        jQuery.ajax({
            context: this,
            url: url_pathPlugin + '/embedded_shortcode_streaming.php',
            type: 'POST',
            dataType: 'html',
            async: false,
            data: 'name_action=PAY&current_url=' + ourLocation + '&id=' + vid + '&price=' + pricePerView,
            success: function(response) {
                var json = jQuery.parseJSON(response);
                var url = json.url;
                var trackingId = json.trackingId;
                localStorage.setItem(vid, trackingId);
                jQuery('div#pay_video' + vid).css('display', 'block');
                jQuery('img#icon_play_vod' + vid).click(function() {
                    jQuery.colorbox({
                        width: '400px',
                        height: '100px',
                        onComplete: function() {
                            jQuery(this).colorbox.resize();
                            jQuery('a#paga_' + vid).attr('href', url);
                        },
                        onLoad: function() {
                            jQuery('#cboxClose').remove();

                        },
                        html: '<h2><?php _e("Event fee", "wimtvpro") ?></br><?php echo str_replace("'", "\'", __("The event has a cost of", "wimtvpro")) ?>' + pricePerView + '€ </br></h2><h2><a id="paga_' + vid + '"><?php _e("Pay to Paypal", "wimtvpro") ?></a> | <a onClick="jQuery(this).colorbox.close();" href="#"><?php _e("Cancel", "wimtvpro") ?></a></h2>'
                    });
                });
            }
        });
    } else {
        var track = localStorage.getItem(vid);
        var url_pathPlugin = "<?php echo plugin_dir_url(__FILE__) ?>";
        var ourLocation = document.URL;
        jQuery.ajax({
            context: this,
            url: url_pathPlugin +  '/embedded_shortcode_streaming.php',
            type: 'POST',
            dataType: 'html',
            async: false,
            data: 'name_action=PLAY&current_url=' + ourLocation + '&thumbnailId=' + thumbnailId + '&price=' + pricePerView + '&height=' + height + '&width=' + width + '&id=' + vid + '&trackingId=' + track,
            success: function(response) {
                var json = jQuery.parseJSON(response);

                if (json.result === 'PLAY') {
                    var res = json.res_html;
                    jQuery('div#play_' + vid).html(res);
                } else {

                    localStorage.removeItem(vid);
                    localStorage.setItem(vid, json.trackingId);
                    jQuery('div#pay_video' + vid).css('display', 'block');
                    var url = json.url;
                    jQuery('img#icon_play_vod' + vid).click(function() {
                        jQuery.colorbox({
                            width: '400px',
                            height: '100px',
                            onComplete: function() {
                                jQuery(this).colorbox.resize();
                                jQuery('a#paga_' + vid).attr('href', url);
                            },
                            onLoad: function() {
                                jQuery('#cboxClose').remove();
                            },
                            html: '<h2><?php _e('Event fee', 'wimtvpro') ?></br><?php echo str_replace("'", "\'", __("The event has a cost of", "wimtvpro")) ?>' + pricePerView + '€ </br></h2><h2><a id=\"paga_' + vid + '\"><?php _e("Pay to Paypal", "wimtvpro") ?></a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\"><?php _e("Cancel", "wimtvpro") ?></a></h2>'
                        });
                    });
                }
            }
        });
    }});
</script>