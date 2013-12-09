
jQuery(document).ready(function(){ 

	jQuery("span.wimtv-thumbnail").click(function(){viewVideo(this);});
	
	function viewVideo(elem){
		console.log('seiqui');
		if( jQuery(elem).parent().parent("tr").children("td").children("a.viewThumb").length  ) {
			var url = jQuery(elem).parent().parent("tr").children("td").children("a.viewThumb").attr("id");
			console.log(url);
			jQuery(elem).colorbox({href:url, width:'50%', height:'80%'});
		}else{
			alert (videoproblem);	
		}
	}
	
	function callSync(elem){
		jQuery.ajax({
			url:   url_pathPlugin  + "sync.php",
			dataType: "html",
			data: {sync:true,showtime:jQuery("table.items").attr("id")},
			type: "GET",
			beforeSend: function(){ 
			   
				jQuery(".loaderTable").show();
				jQuery("form#formVideo").hide();
				jQuery("table.items").hide();
				jQuery("table.items tbody tr").remove(); 						   
			},
			complete: function(){ 
				jQuery(".loaderTable").hide();
			
			},
			success: function(response) {
				jQuery("form#formVideo").show();
				jQuery("table.items").show();
				console.log(response);	
				jQuery("table.items tbody").html(response);
				
				jQuery("a.viewThumb").click( function(){
                  jQuery(this).colorbox({href:jQuery(this).attr("id")});
                });
                jQuery("span.wimtv-thumbnail").click(function(){viewVideo(this);});
                
				jQuery(".icon_Putshowtime,.icon_AcquPutshowtime").click(function(){
					callViewForm(jQuery(this));					
				});
			
				jQuery(".icon_AcqRemoveshowtime,.icon_Removeshowtime,.icon_RemoveshowtimeInto").click(function(){
					callRemoveShowtime(jQuery(this));
				});
				jQuery(".free,.cc,.ppv").click(function(){
					callPutShowtime(jQuery(this));					
				});
				jQuery(".icon_remove").click(function(){
				callRemoveVideo(jQuery(this));
				});
				jQuery('.icon_playlist').click(function() {    
					callInsertIntoPlayList(jQuery(this));	   
				});
				
				if (elem!="") {
					callviewVideothumbs(jQuery(elem));
				}
			},
			
			error: function(response) {	
				jQuery("ul.items").html(response);
			}
		});
		
		
	}
	
	function callRemoveVideo(element){
	
		jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 		      
			type: "GET",
			dataType: "json",
			async: false,
			data: "namefunction=RemoveVideo&id=" + element.parent().parent().attr("id") , 
			beforeSend: function(){ 
				element.parent("tr").children(".icon").hide();
				element.parent().append("<span class='loading'></span>");						   
			},
			complete: function(){ 
				element.parent("tr").children(".icon").show(); 
				jQuery(".loading").remove(); 
			},
			success: function(response) {
				var result = response.result;
				if (result=="SUCCESS"){
					alert (response.message);	
				}
				location.reload();
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		})

	
	}

	function callviewVideothumbs (element){
		var id = element.parent().parent("tr").attr("id");
		jQuery(".icon_viewVideo").colorbox({
			height:"80%",
	        html:function(){
		    var stateView = jQuery(this).attr("rel").split('|');
		    var roles = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getRoles",
							id : id						
						},
						success: function(response) {
							roles = response;
						}
			}).responseText;
			var alls = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getAlls",
							id : id						
						},
						success: function(response) {
							alls = response;
						}
			}).responseText;
			var users = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getUsers",
							id : id
						},
						success: function(response) {
							users = response; 	
						}
			}).responseText;

								
				text  = '<b>' + videoPrivacy[0] + '</b><br/>';
				
				text += '<select onChange="viewWho(this);" id="users" multiple="multiple" style="height:100px;width:270px">';
				text += alls;
				text += '<optgroup label="' + videoPrivacy[9] + '" id="optUsers">';
				text += users;
				text += '</optgroup>';
				text += '<optgroup label="' + videoPrivacy[8] + '" id="optRoles">';
				text += roles;
				text += '</optgroup>';
				text += '</select>';
				text += '<br/><p class="description">' + selectCat + '<br/><strong id="AddUser"></strong>  <strong id="AddRole"></strong></p>';
				
				text  += '<b>' + videoPrivacy[3] + '</b>';
				
				text += '<p class="viewThumbs';
				if (stateView[0]=="0") text += " selected";
				text += '" id="0">' + videoPrivacy[4] + '</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="1") text += " selected";
				text += '" id="1">' + videoPrivacy[5] + '</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="2") text += " selected";
				text += '" id="2">' + videoPrivacy[6] + '</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="3") text += " selected";
				text += '" id="3">' + videoPrivacy[7] + '</p>';
				text += '<div class="action"><span class="form_save button-primary">' + update + '</span><span class="loading" style="display:none;"></span></div>';
				text += '<br/><br/>';
				text += '&nbsp;&nbsp;&nbsp;';
				
				return text;
			},
			onComplete: function(){
				
				var stateView = jQuery(this).attr("rel").split('|');
				jQuery(".viewThumbs").click(function(){
					jQuery(".viewThumbs").removeClass("selected");
					jQuery(this).addClass("selected");
					
				});
								

				jQuery("#users option[value='" + stateView[1]  + "']").attr('selected', 'selected');
				jQuery(".form_save").click(function(){
					var state = jQuery(".selected").attr("id");
					
					var blkstr = jQuery.map(jQuery("#users").val(), function(val,index) {
					     return val;
					}).join(",");
					
					if (state=="0") state += "|No";		
                    else state += "|" + blkstr;
					//alert (id);
					jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						data:{ 
							state : state, 
							id : id,
							namefunction: "StateViewThumbs" 
						},
						
						beforeSend: function(){ 
							//jQuery(this).parent().hide(); 
							//jQuery(this).parent().parent().children(".loader").show(); 	
							//jQuery(this).colorBox(wimtvproAddShowtime);	
							jQuery(".loading").show();	
							jQuery(".form_save").hide();		   
						},
						success: function(response) {
							jQuery.colorbox.close();
							alert (updateSuc);
							element.parent().parent().children(".icon").children("span").attr("rel",state); 	
						}
					});
				});
			}
		});
		
	}	

	
	function putST(element,namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id){
		jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 
			type: "GET",
			dataType: "json",
			data:{ 
				coId : coId, 
				id : id,
				namefunction: namefunction, 
				licenseType: licenseType,
				paymentMode:paymentMode,
				ccType:ccType,
				pricePerView:pricePerView,
				pricePerViewCurrency:pricePerViewCurrency
			},
			
			beforeSend: function(){ 
				jQuery(".loading").show();	
				jQuery(".form_save").hide();			   
			},
			success: function(response) {

				var result = response.result;
				
				if (result=="SUCCESS"){
					jQuery.colorbox.close();
					callSync("");
								        		
				} else {
				    var message = response.messages[0].field + ":" + response.messages[0].message; 
					jQuery(this).parent().hide(); 
					jQuery(this).parent().parent().children(".loader").show();
					jQuery(".loading").hide();	
					jQuery(".form_save").show();
					alert (message);
				}
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
	}
	function callViewForm(element){
		element.parent().children(".formVideo").fadeToggle("slow");
	}
	function callPutShowtime(element){
		 var thisclass = element.attr("class");
		 if (thisclass.indexOf("cc") >= 0){
			height = "70%";
		 } else {
	     	height = "35%";
		 } 
		
		jQuery(element).colorbox({
			
			html:function(){
				
				if (thisclass.indexOf("free") >= 0){	
					text = "<h3>" + gratuito + "</h3><div class='action'><span class='form_save button-primary'>" + messageSave + "</span><span class='loading' style='display:none;'></span></div>";
				}else if (thisclass.indexOf("cc") >= 0){	
					text  = '<p class="cc_set" id="BY_NC_SA"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial No Derivatives.png" 	title="Attribution Non-Commercial No Derivatives" /> Attribution Non-Commercial No Derivatives</p>';		
					text += '<p class="cc_set" id="BY_NC_ND"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial Share Alike.png" 	title="Attribution Non-Commercial Share Alike" /> Attribution Non-Commercial Share Alike</p>';
					text += '<p class="cc_set" id="BY_NC"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial.png" 			title="Attribution Non-Commercial" /> Attribution Non-Commercial</p>';
					text += '<p class="cc_set" id="BY_ND"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution No Derivatives.png" 			title="Attribution No Derivatives" /> Attribution No Derivatives</p>';
					text += '<p class="cc_set" id="BY_SA"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Share Alike.png" 				title"Attribution Share Alike" /> Attribution Share Alike</p>';
					text += '<p class="cc_set" id="BY"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution.png" 						title="Attribution" /> Attribution</p>';      
					text += '<div class="action"><span class="form_save button-primary">'  + messageSave  + '</span><span class="loading" style="display:none;"></span></div>';
				} else if (thisclass.indexOf("ppv") >= 0){
					text  = '<form id="amount"><input type="text" name="amount" class="amount" value="00" maxlength="4"/>' + point + '<input type="text" name="amount_cent" class="amount_cent" value="00" maxlength="2"/>';
					text  += 'Euro<input type="hidden" name="currency" class="currency" value="EUR">';
					text += '<br/><br/><span class="form_save button-primary">'  + messageSave  + '</span><span class="loading" style="display:none;"></span></form>';
				}
				return text;
			},
			
			width:"30%",
			height:height ,
			
			onComplete: function(){
				jQuery(".cc_set").click(function(){
					jQuery(".cc_set").removeClass("selected");
					jQuery(this).addClass("selected");
				});	
				jQuery(".form_save").click(function(){
					var namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id ="";
					var id = element.parent().parent().parent("tr").attr("id");
					
					var icon = element.parent().parent().parent("tr").children(".icon");
					var nomeclass = element.parent().parent().parent("tr").children(".icon").children("span.add").attr("class");		
					var thisclass = element.attr("class");
					if (nomeclass == "add icon_Putshowtime") {
						namefunction = "putST";
						changeClass = "icon_Removeshowtime";	
					}						 	 
					else if (nomeclass == "add icon_AcquPutshowtime") {	 	
						namefunction = "putAcqST";
						changeClass = "icon_AcqRemoveshowtime";
						coId =  element.parent().parent().parent("tr").children("td").children(".icon_AcquPutshowtime").attr("id");
					} 
					if (thisclass.indexOf("free") >= 0){
						licenseType ="TEMPLATE_LICENSE";
						paymentMode ="FREEOFCHARGE";
					} else if (thisclass.indexOf("cc") >= 0){
						licenseType ="CREATIVE_COMMONS";
						ccType = jQuery(this).parent().parent().children(".selected").attr("id");
					} else if (thisclass.indexOf("ppv") >= 0){
						licenseType ="TEMPLATE_LICENSE";
						paymentMode ="PAYPERVIEW";
						pricePerView = jQuery(".amount").val() + point + jQuery(".amount_cent").val();
						pricePerViewCurrency = jQuery(".currency").val();
					}
					
					putST(element,namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id);
			
				});
			}	
		});
	}	
	function callRemoveShowtime(element){
		nomeclass = element.attr("class");
		coId = "";
		if (nomeclass == "icon_AcqRemoveshowtime") {	 	
			namefunction = "removeST";
			changeClass = "icon_AcquPutshowtime";
			coId = "&showtimeId=" + element.attr("id");
		} else {
			namefunction = "removeST";
			changeClass = "icon_Putshowtime";
			coId = "&showtimeId=" + element.attr("id");
		}
		jQuery.ajax({
				context: this,
				url:  url_pathPlugin + "scripts.php", 		      
				type: "GET",
				dataType: "json",
				data: "namefunction=" + namefunction + "&id=" + element.parent().parent().parent().parent().attr("id") + coId , 
				beforeSend: function(){ 
				element.hide();
				element.parent().append("<span class='loading'></span>");					   
			},
			complete: function(){ 
				 jQuery('.loading').remove();
				
			},
			success: function(response) {
				var result = response.result;
				if (result=="SUCCESS"){
					callSync("");	
				} else {
					element.parent().hide(); 
					element.parent().parent().children(".loader").show();
					alert (response.messages[0].message); 
				}
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
	}
	jQuery(".icon_sync0").click(function(){
		callSync(this);
	});
	jQuery(".free,.cc,.ppv").click(function(){
		callPutShowtime(jQuery(this));					
	});
	
		
	jQuery(".icon_Putshowtime,.icon_AcquPutshowtime").click(function(){
		callViewForm(jQuery(this));
	});
	jQuery(".icon_AcqRemoveshowtime,.icon_Removeshowtime,.icon_RemoveshowtimeInto").click(function(){
		callRemoveShowtime(jQuery(this));
	});
	
	jQuery(".icon_viewVideo").click(function(){
		callviewVideothumbs(jQuery(this));
	});
	
	
	jQuery(".icon_remove").click(function(){
		callRemoveVideo(jQuery(this));
	});
	
	//Request new URL for create a wimlive Url
	jQuery(".createUrl").click(function(){
	  jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 
			type: "GET",
			dataType: "html",
			data:{
				namefunction: "urlCreate",
				titleLive: jQuery("#edit-name").val()	
			},
			success: function(response) {
			  var json =  jQuery.parseJSON(response);
			  var result = json.result;
			  if (result=="SUCCESS"){
			  	jQuery("#edit-url").attr("readonly", "readonly");
			  	jQuery("#edit-url").attr("value", json.liveUrl);
			  	jQuery(this).hide();
				jQuery("#urlcreate").hide();
				jQuery(".removeUrl").show();
			  } else {
			    //alert (response);
			    alert (passwordReq);
			    jQuery(".passwordUrlLive").show();
			    jQuery(".createPass").click(function(){
			     jQuery.ajax({
			     context: this,
			     url:  url_pathPlugin + "scripts.php", 
			     type: "GET",
			     dataType: "html",
			     data:{
				  namefunction: "passCreate",
				  newPass: jQuery("#passwordLive").val()
			     },
                 success: function(response) {
                 	alert (response);
                 	jQuery(".passwordUrlLive").hide();
                 }
			    });
	            });
			  }
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
     });
   jQuery(".removeUrl").click(function(){
     jQuery(this).hide();
     jQuery(".createUrl").show();
     jQuery("#edit-url").removeAttr("readonly");
     jQuery("#edit-url").removeAttr("disabled");
     jQuery("#edit-url").val("");	
	 jQuery("#urlcreate").show();
   });
      
});



function viewCategories(obj){
    jQuery("#addCategories").html(selectCat);
    var selectedArray = new Array();
    count = 0;
    for (i=0; i<obj.options.length; i++) {
      if (obj.options[i].selected) {
        selectedArray[count] = obj.options[i].value;
        valueSelected = obj.options[i].value;
        count++;
        jQuery("#addCategories").append("<br/>" + valueSelected);
      }
    }
}

function viewWho(obj){  
    var selectedArray = new Array();
    count = 0;
    jQuery("#AddUser").html("");
    jQuery("#AddRole").html("");

    for (i=0; i<obj.options.length; i++) {
      if (obj.options[i].selected) {
        
        selectedArray[count] = obj.options[i].value;
        valueSelected = obj.options[i].text;
        count++;
		
        if ( obj.options[i].parentNode.id == "optUsers"){
            if (jQuery("#AddUser").html() == "") jQuery("#AddUser").html(videoPrivacy[8]);
        	jQuery("#AddUser").append(" " + valueSelected);
        }
        if ( obj.options[i].parentNode.id == "optRoles"){
        	if (jQuery("#AddRole").html() == "") jQuery("#AddRole").html(videoPrivacy[9]);
        	jQuery("#AddRole").append(" " + valueSelected);
        }
        	
      }
    }
    for (i=0; i<obj.options.length; i++) {
    	if ((obj.options[i].selected) && (obj.options[i].parentNode.id != "optUsers") && (obj.options[i].parentNode.id != "optRoles")) {   		
    	    jQuery("#AddUser").html(obj.options[i].text);
        	jQuery("#AddRole").html("");
        	jQuery(obj).children().children().removeAttr("selected");
        	jQuery(obj).children().removeAttr("selected");
        	jQuery(obj).children().eq(i).attr("selected","selected");
        
        }
    }

    
}




jQuery(document).ready(function() { 

   jQuery("input#edit-videofile").change(function() {	
    fileName = jQuery(this).val();
    fileTypes = [ "", "mov", "mpg", "avi", "flv", "mpeg", "mp4", "mkv", "m4v" ];
    if (!fileName) {
      return;
    }

    dots = fileName.split(".");
    // get the part AFTER the LAST period.
    fileType = "." + dots[dots.length - 1];

    if (fileTypes.join(".").indexOf(fileType.toLowerCase()) != -1) {
      return true;
    } else {
      alert(erroreFile[0] + " \n\n"
      + (fileTypes.join(" ."))
      + "\n\n" + erroreFile[1]);
      jQuery("input[name=\"files[videoFile]\"]").val("");
    }
});

  jQuery("ul.itemsPublic li a").colorbox();
  jQuery('.buttonInsert').click(function() {
      var width = jQuery(this).parent().children('.w').val();
      var height = jQuery(this).parent().children('.h').val();
      var id = jQuery(this).attr('id');
	  var win = window.dialogArguments || opener || parent || top;
      var shortcode  =  "[streamingWimtv id='" + id + "' width='" + width + "' height='" + height + "' ]";
	  win.send_to_editor(shortcode); 
	    
  });
  jQuery('.buttonInsertPlayList').click(function() {
      var id = jQuery(this).attr('id');
	  var win = window.dialogArguments || opener || parent || top;
      var shortcode  =  "[playlistWimtv id='" + id + "']";
	  win.send_to_editor(shortcode); 
	   
  });
    
  //Playlist
  functionPlaylist();
  
  jQuery('.icon_playlist').click(function() {    callInsertIntoPlayList(jQuery(this));	   });

	function callInsertIntoPlayList(elem) {
   			var contentIdAdd = elem.attr("rel");

   			var playlistId = "";
   			jQuery(".playlist").each(function(i) {
   			  var classe = jQuery(this).attr("class");
   			  if (classe=="playlist selected"){
   			    playlistId = jQuery(this).attr("rel");
   			  }
   			});
   			if (playlistId == ""){
   				alert (titlePlaylistJs);
   			}else{
   		
   			
   				jQuery.ajax({
				    context: this,
			        url:  url_pathPlugin + "script_playlist.php", 
				    type: "GET",
				    data:{
				      idPlayList : playlistId,
				      id : contentIdAdd,
				      namefunction: "AddVideoToPlaylist"
				    },
				    success: function(response){
				  		//jQuery(this).hide();
				  		if (response!="") 
				  			alert(response);
				  		else {
							ok = false;
					  		jQuery(".playlist").each(function(i) {
				   			  var classe = jQuery(this).attr("class");
				   			  if (classe=="playlist selected"){
				   			    var counter = parseInt(jQuery(this).children(".counter").html());
				   			    jQuery(this).children(".counter").html(counter +1);
				   			  	ok = true;
							  }
				   			  
				   			});
							if (ok)
							  alert (titlePlaylistSelectJs);
				   		}

				  	},
				    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
				  });
   			}
   			
   			   			
	}

  
  function functionPlaylist(){
  
         /*SORTABLE*/      						
		jQuery( "table.items_playlist tbody" ).sortable({
		  placeholder: "ui-state-highlight",
		  connectWith: "table tbody"		,
		  active: function (){
			
			$count = jQuery(".sortable1 table#droptrue").find('tbody > tr').length;
			if ($count==0) {
				jQuery(".sortable1 table#droptrue tbody").append("<tr class='appoggio'></tr>");
			}
			
		},
		  deactivate: function( event, ui ) {
			jQuery(".appoggio").remove();
		    var sort = jQuery(".sortable2 table#dropfalse tbody").sortable("toArray");
			jQuery(".list").val(sort);
			if (sort=="")
				jQuery(".sortable2 table#dropfalse tbody").append("<tr class='appoggio'></tr>");
			$count = jQuery(".sortable1 table#droptrue").find('tbody > tr').length;
			if ($count==0) {
				jQuery(".sortable1 table#droptrue tbody").append("<tr class='appoggio'></tr>");
			}
		  }
		});
		/*jQuery( ".sortable1 table#droptrue" ).sortable({
		  connectWith: "tr"		
		});
		jQuery( ".sortable2 table#dropfalse" ).sortable({
		  connectWith: "tr",
		  deactivate: function( event, ui ) {
		    var sort = jQuery(".sortable2 table#dropfalse").sortable("toArray");
			jQuery(".list").val(sort);
		  }
		});*/
	   jQuery('input.title').change(function() {
	    jQuery(this).parent().children('.icon_modTitlePlay').show();
	   });
	   
	   jQuery('.icon_selectPlay').click(function() {
	  	jQuery(".playlist").removeClass("selected");
	  	 jQuery(this).parent().addClass("selected");
	  });
	
	jQuery(".icon_viewPlay,.icon_viewPlay_title").click(function () {
		var id= jQuery(this).attr("id");
        console.log(url_pathPlugin + "embedded/embeddedPlayList.php?isAdmin=true&id=" + id);
		jQuery(this).colorbox({width:'80%', height:'80%', href:  url_pathPlugin + "embedded/embeddedPlayList.php?isAdmin=true&id=" + id});
	});
	
	  jQuery('.icon_createPlay').click(function() {
	  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
	  	//ID = playlist_##
	  	var count = jQuery(".playlist").size();
	  	count  = count + 1; 
	  	//add to DB
	  	jQuery.ajax({
		    context: this,
	        url:  url_pathPlugin + "script_playlist.php", 
		    type: "GET",
		    data:{ 
		      namePlayList : nameNewPlaylist,
		      namefunction: "createPlaylist"
		    },
		   success: function(){location.reload();},
		   error: function(){location.reload();}
		  });

	  });

	  jQuery('.icon_deletePlay').click(function() {
	  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
	  	//remove from DB
	  	var idPlayList = jQuery(this).parent().parent().attr("rel");
	  	//add to DB
	  	jQuery.ajax({
		    context: this,
	        url:  url_pathPlugin + "script_playlist.php", 
		    type: "GET",
			
		    data:{
		      idPlayList : idPlayList,
		      namefunction: "removePlaylist"
		    },
		   success: function(){
			 location.reload();
			 
			 
		},
		   error: function(){location.reload();}
		  });
	  	
	  });
  }


  //End Playlist


	jQuery('#edit-sandbox').change (function() {
        if(jQuery(this).value == "No") {
            jQuery('#sandbox').attr('href','http://www.wim.tv/wimtv-webapp/userRegistration.do?execution=e1s1');
            jQuery('#site').html('www.wim.tv');
        } else {
            jQuery('#sandbox').attr('href','http://www.wim.tv/wimtv-webapp/userRegistration.do?execution=e1s1');
            jQuery('#site').html('peer.wim.tv');
        }
     });
     
    
    
    jQuery(".termsLink").colorbox({width:"80%", height:"80%", iframe:true, href:jQuery(this).attr("href")});

jQuery(".ppvNoActive").click(function(){
		alert (nonePayment);				
	});

jQuery(".icon_downloadNone").click(function() {
	alert(videoproblem);
});


}); 

