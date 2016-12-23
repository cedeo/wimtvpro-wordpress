<?php


global $user;
global $wp,$wpdb, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$url_include = $parse_uri[0] . 'wp-load.php';

if (@file_get_contents($url_include)) {
    require_once($url_include);
}



$boxId = $_GET['b'];

$response = apiGetWimboxItem($boxId);
$arrayjson = json_decode($response);
$title = $arrayjson->title;
$tags = $arrayjson->tags;
$description = $arrayjson->description;
$thumbnailId = $arrayjson->thumbnailId;

?>
<form enctype="multipart/form-data" action="#" method="post" id="wimtvpro-edit_metadati" accept-charset="UTF-8">
       <p>

<p>
 <label for="edit-titlefile"><?php _e("Title"); ?> *</label>
 <input type="text" id="edit-titlefile" name="titlefile" value="<?php echo $title ?>" size="30" maxlength="200" class="form-text required" />
 
</p>
<p>
 <label for="edit-descriptionfile"><?php _e("Description","wimtvpro"); ?> </label></br>
<textarea id="edit-descriptionfile" name="descriptionfile" cols="30" rows="5"><?php echo $description ?></textarea>             

</p> 
<?php
if(isset($tags)){
foreach($tags as $tag){
?>

       <p id="parag_tags">
                    <label for="edit-tags"><?php echo "Tag" ?> </label>
                    <br/>
               
       <input type="text" id="edit-titlefile" name="video[]" value="<?php echo $tag?>" size="10" maxlength="200" class="form-text required" />
              
                 
        </p>
         <?php
}
}else{
    
?>
    <p id="parag_tags">
                    <label for="edit-tags"><?php echo "Tag" ?> </label>
                    <br/>
            
     <input type="text" id="edit-titlefile" name="video[]" value="<?php echo $tag?>" size="10" maxlength="200" class="form-text required" />
                 
                 
    </p>
<?php

}
?>
         
<input type="button" id="add_tag"  style="font-size:15px" value="<?php echo __("Add another tag","wimtvpro")?>"> <i class="fa fa-plus-square"></i></input>
<input type="hidden" name="thumbnailId" id="nameFunction" value="<?php echo $thumbnailId ?>" />
<input type="hidden" name="boxId_edit" id="boxId_edit" value="<?php echo $boxId ?>" />
<input type="hidden" name="namefunction" id="nameFunction" value="UpdateMetadati" />
<input type="hidden" name="boxId" id="boxId" value="<?php echo $boxId ?>" />
<?php submit_button(__("Update","wimtvpro"),"primary classupload") ?>   </p>
    </form>
<script type="text/javascript" >
jQuery(document).ready(function() {

 
jQuery("#wimtvpro-edit_metadati").submit(function(event) {
event.preventDefault();
        var $form = jQuery(this);
        var $inputs = $form.find("input, select, button, textarea");
        $inputs.prop("disabled", true);

       var formData = new FormData(jQuery("form")[0]);

   //jQuery.each(jQuery('#edit-thumbnailfile')[0].files, function(i, file) {
     //       formData.append('thumbnailFile', file);
       // });
     $inputs.each(function(index, element) { 
            formData.append(jQuery(this).attr("name"), jQuery(this).attr("value"));
        });// alert(jQuery('#edit-titlefile').val());

// alert(jQuery('#b016cd37-bd41-4ffa-96cc-6f37f205996a.span#wimtvpro-title-detail').jQuery('').attr('value'));return;
        jQuery.ajax({
            url: url_pathPlugin + "scripts.php",
            type: "POST",
            data: formData,
            cache: true,
            contentType: false,
            async: true,
            processData: false,
            enctype: 'multipart/form-data',
         
         
            success: function(response) {
            var title =  jQuery('#edit-titlefile').val();   
            var boxId = jQuery('#boxId_edit').val(); 
                if(response == 200 || response == "200"){jQuery.colorbox.close();
           
            jQuery('#'+boxId).find('span#wimtvpro-title-detail').html(title);
            alert("Video Updated");


                }else{ alert("Error");
         }
            },
            complete: function(response) {
             
            },
            error: function(request, error) {

              alert(request.responseText);
                jQuery("#message").html(request.responseText);
                $inputs.prop("disabled", false);
            }
            });
        });
        jQuery("#add_tag").click(function() {
           
               jQuery('#parag_tags').append('<br><input type="text" id="edit-titlefile" name="video[]" value="" size="10" maxlength="200" class="form-text required" />');
           

        }); 
   });
    </script>