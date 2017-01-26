<?php

/* 
Written By NetsenseSrl 2016
 */



include_once('modules/wimlive.php');
function wimtvpro_live() {
    
//   $channels_response =  wimtvpro_getChannels();
//   if($channels_response->code == 200){
//       $arrayjsonchannels = json_decode($channels_response);
//     
//   }
//     wimtvpro_elencoChannel("table", "");
    
 
     $view_page = wimtvpro_alert_reg();
    if (!$view_page) {
        die();
    }
    
//   $now = new DateTime();
// $now->format('Y-m-d H:i:s');    // MySQL datetime format
// $now->getTimestamp();   

    $page = isset($_GET['namefunction']) ? $_GET['namefunction'] : "";


    $noneElencoChannel = FALSE;
    $noneElencoLive = FALSE;
    switch ($page) {
        case "addChannel":
            $noneElencoChannel = TRUE;
            //aggiungere script per pickdata e pickhour
//            if(isset($_POST)){
//                var_dump($_POST);
//               var_dump($_FILES['thumbnailFile']['tmp_name']);
//            }
            if (isset($_POST["wimtvpro_live"])) {
                wimtvpro_savechannel("insert");
            }
            $name = "";
            $public = false;
            $description = "";
            $streamPath = "";
            break;

        case "modifyChannel":
//            var_dump("QUAAAAA");
            $noneElencoChannel = TRUE;
            if (isset($_POST["wimtvpro_live"])) {
                wimtvpro_savechannel("modify");
            }
            
             $dati = apiReadLiveChannel($_GET['id']);
             $arrayjson_channel = json_decode($dati);
             $name = $arrayjson_channel->name;
             $description = $arrayjson_channel->description;
             $streamPath = $arrayjson_channel->streamPath;
             $public = $arrayjson_channel->public;
             $channelId = $arrayjson_channel->channelId;
             $tags = $arrayjson_channel->tags;

            break;

        case "deleteChannel":
         
            $response = apiDeleteLiveChannel($_GET['id']);
            $json = json_decode($response);

            if($response->code == 204){
                  echo '<script language="javascript">
            <!--
            //window.location = "admin.php?page=WimLive";
            window.location = "admin.php?page=' . __("WIMLIVE_urlLink", "wimtvpro") . '"</script>';
               
            }else{
                  echo '<div class="error"><p><strong>';
            echo $json->message;
            echo '</strong></p></div>';
            }
            
            break;
        case "addLive":
            $noneElencoLive = TRUE;
            //aggiungere script per pickdata e pickhour
            if (isset($_POST["wimtvpro_live"])) {
                wimtvpro_savelive("insert");
            }
            $name = "";
            $payperview = "0";
            $channelId = $_GET['channelId'];
            $publicEvent = false;
            $recordEvent = false;
            $giornoStart = null;
            $oraStart = null;
            $giornoEnd = null;
            $oraEnd = null;
            break;

        case "modifyLive":
            $noneElencoLive = TRUE;
            if (isset($_POST["wimtvpro_live"])) {
                wimtvpro_savelive("modify");
            }


            $dati = apiGetLiveEvent($_GET['id']);

            $arraydati = json_decode($dati);

            $name = $arraydati->name;
            if ($arraydati->paymentMode == "FREE")
                $payperview = "0";
            else {
                $payperview = $arraydati->pricePerView;
            }


            $publicEvent = $arraydati->publicEvent;
            $recordEvent = $arraydati->recordEvent;
           
            $giornoStart = $arraydati->eventDate->date;
            $oraStart = $arraydati->eventDate->time;
            $giornoEnd = $arraydati->endDate->date;
            $oraEnd = $arraydati->endDate->time;
            $eventId = $arraydati->eventId;


         

   

            break;

        case "deleteLive":
            $response = apiDeleteLiveEvent($_GET['id']);
            break;
        default: 
            break;
    }
    ?>
     <script type="text/javascript">
        function clickImg(obj) {
            jQuery("a.clickWebProducer img").attr("src", "<?php echo get_option('wp_wimtvPluginPath') . 'images/webcam.png' ?>");
            jQuery(obj).attr("src", "<?php echo get_option('wp_wimtvPluginPath') . 'images/live_rec.gif' ?>");

        }
    </script>
   <?php
   if (!$noneElencoChannel && !$noneElencoLive) {
       ?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>          

    <div class='wrap'>
            <h2>WimLive
                <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=addChannel" ?>' class='add-new-h2'><?php echo __('New', 'wimtvpro') ?></a>
            </h2>
            <p>
                <?php echo _e("Here you can create and post live streaming events to your website.", "wimtvpro") ?>
                <br/>
                <?php echo _e("This service can be used in one of these two modalities:", "wimtvpro") ?>
            <ol>
                <li>
                    <?php echo _e("Install a third party video encoding software (e.g. Adobe Flash Media Live Encoder, Wirecast etc.) on your pc: this solution is recommended if you want to connect an external video camera to your pc", "wimtvpro") ?>
                </li>
                <li>
                    <?php echo _e("Use WimTV encoding software.", "wimtvpro") ?>
                </li>
            </ol>
        </p>


   <table id='tableLive' class='wp-list-table widefat fixed pages'> 

      <tbody>
                <?php  wimtvpro_elencoChannel("table"); ?>
            </tbody>
        </table>

</div>
    <?php

    }else  if ($noneElencoChannel && !$noneElencoLive){
    ?><script> 

   jQuery(document).ready(function() {
    jQuery('form input[type=file]').each(function(){

        jQuery(this).change(function(evt){
            var input = jQuery(this);
            var file = input.prop('files')[0];
            var regex = /^(image\/)(gif|(x-)?png|p?jpeg)$/i;
 
            if( file && file.size < 2 * 1048576 && file.type.search(regex) != -1 ) { // 2 MB (this size is in bytes)
 
 
            }else{
 
                alert( 'File non ammesso - Tipo: ' + file.type + '; dimensioni: ' + file.size );
 
                input.replaceWith( input.val('').clone(true) );
 
                evt.preventDefault();
            }   
        })
    });
      
           jQuery("#add_tag").click(function() {
        
                jQuery('#parag_tags').append('<br><input type="text" id="edit-titlefile" name="tags[]" value="" size="10" maxlength="200" class="form-text required" />');
           

        });
}); 
 
 
           </script>
  <div class='wrap'><h2>WimChannel
                <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=listLive" ?>' class='add-new-h2'><?php echo __('Return to list', 'wimtvpro') ?></a>
            </h2>
            <p>
                <?php echo _e("Please complete all the fields marked with an *", "wimtvpro") ?>

                <?php
//                $customThumbUrl = ($dati->body->customThumbUrl != NULL) ? $dati->body->customThumbUrl : "https://www.wim.tv/wimtv-webapp/images/getStarted/wimtv-live.png";
//                print getEditThumbnailControl($_GET['id'], $customThumbUrl, $page == "modifyLive");
                ?>

            <form action="#" method="post" id="wimtvpro-wimlive-form" accept-charset="UTF-8">
<!--                <p>
                    <label for="edit-thumbnailfile"><?php //echo "Select thumbnail"  ?></label>
                    <input type="file" id="edit-thumbnailfile" name="thumbnailFile" size="60" class="form-file" />
                </p>-->
                <p><label for="edit-name"><?php _e("Title"); ?> <span>*</span></label>
                    <input type="text" id="edit-name" name="name" value="<?php echo $name; ?>" size="90" maxlength="200"></p>
             

                <p><label for="edit-description"><?php echo __("Description","wimtvpro"); ?> <span>*</span></label></br>
                    <textarea  id="edit-description" name="description" cols="90" row="5" maxlength="200"> <?php echo $description; ?></textarea></p>
               

              
                <p><label for="edit-url">Url *</label>
                    <input type="text" id="edit-url" name="streampath" value="<?php echo $streamPath; ?>" size="90" maxlength="800" class="form-text required">
                </p>

<div class="description"><p id="urlcreate"><?php _e('You need the streaming server URL. Click “Obtain URL" button to get one', "wimtvpro"); ?>
                        <b class="button createUrl"><?php _e("Obtain URL", "wimtvpro"); ?></b></p>

</div>


                </div>

                <p>
                    <label for="edit-url"><?php _e("Event status", "wimtvpro"); ?> * </label><br/>
                    <?php _e("Public", "wimtvpro"); ?> <input type="radio" name="public" value="true" 
                    <?php if ($public || ($page == "AddChannel")) echo 'checked="checked"'; ?>
                           /> |
                    <?php _e("Private", "wimtvpro"); ?> <input type="radio" name="public" value="false"

                           <?php if (!$public) echo 'checked="checked"'; ?>

                           />
                <div class="description">
                    <?php _e("If you want to index your event, select \"Public\"", "wimtvpro");
                    ?>
                </div>
                </p>
</p> 

   
                <input type="hidden" name="channelId" value="<?php echo $channelId ?>" />
                <input type="hidden" name="wimtvpro_live" value="Y" />
                <input type="hidden" id="timelivejs" name="timelivejs" value="" />
                <input type="hidden" id="timezone" name="timezone"/>
                <?php
                if ($page == "AddChannel")
                    submit_button(__("Create", "wimtvpro"));
                else
                    submit_button(__("Update", "wimtvpro"));
                ?>
            </form>
        </div>
      
 <?php
    }else  if (!$noneElencoChannel && $noneElencoLive){
       
    ?> 
<!--  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->
  
     <script type="text/javascript">
         
          jQuery(document).ready(function() {
                jQuery(".pickatime").timepicker({defaultTime: "00:00"});
            });
       

            jQuery(document).ready(function() {
                jQuery(".pickadate").datepicker(
                        {dateFormat: "dd/mm/yy",
                            autoSize: true,
                            minDate: 0
                        });
            });
            jQuery(document).ready(function() {


            });

        </script>
   
         <div class='wrap'><h2>WimLive
                <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=listLive" ?>' class='add-new-h2'><?php echo __('Return to list', 'wimtvpro') ?></a>
            </h2>
            <p>
               
            <form action="#" method="post" id="wimtvpro-wimlive-form" accept-charset="UTF-8">

                <p><label for="edit-name"><?php _e("Title"); ?> <span>*</span></label>
                    <input type="text" id="edit-name" name="name" value="<?php echo $name; ?>" size="100" maxlength="200"></p>
                <div class="description"><?php _e("Please insert the title of the live event", "wimtvpro"); ?>*</div>

                <p><label for="edit-payperview"><?php _e("Set the price to access the event", "wimtvpro"); ?> *</label>
                    <input type="text" id="edit-payperview" name="payPerView" value="<?php echo $payperview; ?>" size="10" maxlength="5" class="form-text required"></p>
                <div class="description">
                    <?php _e("Please, set a price for viewing your event (set 0 for free of charge). Prices are expressed in &euro;", "wimtvpro"); ?></div>



                <p>
                    <label for="edit-url"><?php _e("Event status", "wimtvpro"); ?> * </label><br/>
                    <?php _e("Public", "wimtvpro"); ?> <input type="radio" name="publicEvent" value="true" 
                    <?php if ($publicEvent || ($page == "AddLive")) echo 'checked="checked"'; ?>
                           /> |
                    <?php _e("Private", "wimtvpro"); ?> <input type="radio" name="publicEvent" value="false"

                           <?php if (!$publicEvent) echo 'checked="checked"'; ?>

                           />
                <div class="description">
                    <?php
                     _e("If you want to index your event, select \"Public\"", 'wimtvpro');
                    ?>
                </div>
                </p>

                <p>
                    <label for="edit-record"><?php _e("Record event", "wimtvpro"); ?></label><br/>
                    <?php _e("Yes"); ?> <input type="radio" name="recordEvent" value="true"
                    <?php if ($recordEvent || ($page == "AddLive")) echo 'checked="checked"'; ?>
                           /> |
                    <?php _e("No", "wimtvpro"); ?> <input type="radio" name="recordEvent" value="false"
                    <?php if (!$recordEvent) echo 'checked="checked"'; ?>
                           />
                <div class="description"><?php _e("Select “Yes” if you want to record your event. The recorded video will be listed among your videos in WimBox", "wimtvpro"); ?></div>

                </p>
                <?php
               
                ?>
                <p><label for="edit-giorno"><?php _e("Start date", "wimtvpro"); ?> <?php _e("dd/mm/yy", "wimtvpro"); ?> *</label>
                    <input  type="text" class="pickadate" id="edit-giorno" name="eventDate[date]" value="<?php echo $giornoStart; ?>" size="10" maxlength="10">

               <label for="edit-ora"><?php _e("Start time", "wimtvpro"); ?> *</label>
                    <input class="pickatime" type="text" id="edit-ora" name="eventDate[time]" value="<?php echo $oraStart; ?>" size="10" maxlength="10">
                    
                </p>

                <p><label for="edit-giorno-fine"><?php echo _e("End Date","wimtvpro"); ?> <?php _e("dd/mm/yy", "wimtvpro"); ?> *</label>
                    <input  type="text" class="pickadate" id="edit-giorno-end" name="endDate[date]" value="<?php echo $giornoEnd; ?>" size="10" maxlength="10">

               <label for="edit-ora"><?php  echo _e("End Time","wimtvpro"); ?>  *</label>
                    <input class="pickatime" type="text" id="edit-ora-end" name="endDate[time]" value="<?php echo $oraEnd; ?>" size="10" maxlength="10">
                </p>
                 <?php  if(isset($eventId)){?>
<input type="hidden" name="eventId" value="<?php echo $eventId ?>" />
                 <?php }?>

                <input type="hidden" id="timezone" name="timezone"/>
                <input type="hidden" name="channelId" value="<?php echo $channelId ?>" />
                <input type="hidden" name="wimtvpro_live" value="Y" />
                <input type="hidden" id="timelivejs" name="timelivejs" value="" />

                <?php
                if ($page == "addLive")
                    submit_button(__("Create", "wimtvpro"));
                else
                    submit_button(__("Update", "wimtvpro"));
                ?>
            </form>
        </div>
   <?php
   }
       ?>        
     <script>
        jQuery(document).ready(function() { 
            var timezone = -(new Date().getTimezoneOffset() * 60 * 1000);
            jQuery("#timezone").attr("value",timezone);

       }); 
    </script>
<?php }

