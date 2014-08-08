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
                        var json = jQuery.parseJSON(response);
                        loop.progress = json['percentage'];
                        var progress = 50 + loop.progress/2;
                        console.log(progress);
                        jQuery('.progress-bar span').css("width", progress + "%");
                        jQuery (".progress-bar span").html(progress + "%");
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
        var progress = jQuery(".progress-bar span");
		jQuery(progress).css("width","0");
		jQuery(progress).html("0%");
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
			xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", this.progress, false);
                return xhr;
            },
			beforeSend: function() {
				jQuery (".progress-bar").show();
                //progressLoop.start();
			},
            progress: function(e) {
                if(e.lengthComputable) {
                    //calculate the percentage loaded on plugin host server
                    var pct = ((e.loaded / e.total) * 100/2);
                    console.log(pct);
                    if (pct == 50) {
                        //when upload on host server is done, start progressloop to get upload percentage on wimtv servers
                        progressLoop.start();
                    }
                    jQuery(progress).css("width",Math.round(pct) + "%");
                    jQuery(progress).html(Math.round(pct) + "%");
                }
                //this usually happens when Content-Length isn't set
                else {
                    console.warn('Content Length not reported!');
                }
            },
            success: function(response) {
                jQuery (".progress-bar").hide();
                jQuery("#message").html (response);	
                // NS: After upload leave controls disabled
                $inputs.prop("disabled", true);
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

