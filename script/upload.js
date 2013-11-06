function ProgressLoop(contentId) {
    this.contentIdentifier = contentId;
    this._stop = false;
    this.progress = 0;
    this.start = function () {
        var loop = this;
        var interval = setInterval(function() { getProgress(loop); }, 2000);
        function getProgress(loop) {
            if (loop._stop) {
                console.log("stopped");
                clearInterval(interval);
            } else {
                jQuery.ajax({
                    url:  url_pathPlugin + "functions/uploadProgress.php?contentIdentifier=" + loop.contentIdentifier,
                    type: "GET",

                    success: function(response) {
                        loop.progress = response;
                        console.log(loop.progress);
                        jQuery('.progress-bar span').css("width", loop.progress + "%");
                        jQuery (".progress-bar span").html(loop.progress + "%");
                    },

                    error: function(request,error) {
                        loop.stop();
                    }
                });
            }
        }

    };
    this.stop = function() {
        this._stop = true;
    };
}

function createContentId() {
    var time = new Date().getTime();
    return time + "WP" + Math.floor((Math.random()*100)+1);
}


jQuery(document).ready(function(){ 


	jQuery("#wimtvpro-upload").submit(function(event){
		
		event.preventDefault();
		jQuery (".progress-bar span").css("width","0");
		jQuery (".progress-bar span").html("0%");
		var $form = jQuery(this);
		var $inputs = $form.find("input, select, button, textarea");
		$inputs.prop("disabled", true);

		var formData = new FormData(jQuery("form")[0]);
		jQuery.each(jQuery('#edit-videofile')[0].files, function(i, file) {
			formData.append('videoFile', file);
		});
		$inputs.each(function(index, element) {
			formData.append(jQuery(this).attr("name"), jQuery(this).attr("value"));			
        });
        var contentId = createContentId();
        formData.append('uploadIdentifier', contentId);
        var progressLoop = new ProgressLoop(contentId);
		jQuery.ajax({
			
			url:  url_pathPlugin + "scripts.php", 		      
			type: "POST",
			data:  formData,
			cache: true,
       	 	contentType: false,
			async:true,
        	processData: false,
			enctype: 'multipart/form-data',
			
			beforeSend: function(){ 
				jQuery (".progress-bar").show();
                progressLoop.start();
			},

            success: function(response) {
                jQuery (".progress-bar").hide();
                jQuery("#message").html (response);
                $inputs.prop("disabled", false);
                jQuery("#addCategories").html("");
                $inputs.each(function(index, element) {
                    if ((jQuery(this).attr("id")!="submit") && (jQuery(this).attr("id")!="nameFunction"))
                        jQuery(this).attr("value","");
                });
			},
			
			complete: function(response){ 
				progressLoop.stop();
			},
			
			error: function(request,error) {
                progressLoop.stop();
                jQuery (".progress-bar").hide();
                jQuery("#message").html (request.responseText);
                $inputs.prop("disabled", false);
			}
		});	
		
	});

}); 

