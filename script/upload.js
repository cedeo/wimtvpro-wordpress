
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
			},
			progress: function(e) {
					//make sure we can compute the length
			
					if(e.lengthComputable) {
						//calculate the percentage loaded
						var pct = ((e.loaded / e.total) * 100)/2;
						//log percentage loaded
						jQuery (".progress-bar span").css("width",Math.round(pct) + "%");
						jQuery (".progress-bar span").html(Math.round(pct) + "%");
						
						
					}
					//this usually happens when Content-Length isn't set
					else {
						console.warn('Content Length not reported!');
					}
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
				
			},
			
			error: function(request,error) {
                jQuery (".progress-bar").hide();
                jQuery("#message").html (request.responseText);
                $inputs.prop("disabled", false);
			}
		});	
		
	});

}); 

