$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'groups/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Attend_level'].replace (/\"/g, ""),  
            name : 'level', 
            width : 120, 
            sortable : true, 
            align: 'left'
        },
        
        {
            display: aLang['Company'].replace (/\"/g, ""),  
            name : 'company', 
            width : 120, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['status'].replace (/\"/g, ""),  
            name : 'status', 
            width : 40, 
            sortable : true, 
            align: 'center'
        }
        ],

        buttons : [   
        {
            name: aLang['New'].replace (/\"/g, "") , 
            bclass: 'add', 
            onpress: novo,
            hide: true
            
        }
        ,

        {
            separator:true
        },
        {
            name: aLang['edit'].replace (/\"/g, ""),  
            bclass: 'edit', 
            onpress: edit
        },
        
        {
            separator:true
        },

        {
            name: aLang['Deactivate'].replace (/\"/g, ""), 
            bclass: 'encerra', 
            onpress: disable
        },
        
        {
            separator:true
        },

        {
            name: aLang['Activate'].replace (/\"/g, ""), 
            bclass: 'activate', 
            onpress: enable
        },
        {
            separator:true
        },
        {
            name: aLang['Attendants_by_group'].replace (/\"/g, ""), 
            bclass: 'attendant', 
            onpress: attendantsGroup
        },
        {
            separator:true
        },
        {
            name: aLang['Groups_by_service'].replace (/\"/g, ""), 
            bclass: 'groups', 
            onpress: servicesGroup
        },
        {
            separator:true
        },
        {
            name: aLang['Set_repass_groups'].replace (/\"/g, ""), 
            bclass: 'groups_repass', 
            onpress: setRepassGroups
        }   
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }					
        ],
        sortname: "tbp.name",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Groups'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: "auto",
        height: $(window).height()-206,    
        resizable: false,
        minimizado: false,
        singleSelect : true
    }
    ); 
			 
			 
    function novo(){ 
        if (access[1]=='N'){
            objModal.openModal("modalPermission");
        }
        else{
            var modalInsert = $(document.getElementById("modalGroupsInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("groups/modalInsert", function(){
	        	objDefault.init();
	        		        	
	        	$("#formGroupsInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		name: {
				  			required: true
				  		},
				  		level: {
				  			required: true
				  		},
				  		costumer: {
				  			required: true
				  		}	  		
				 	}
				});
	        	
	        	objModal.openModal("modalGroupsInsert");
	        	objDefault.maskLoaderHide();
	        })
        }
    }
    
    function attendantsGroup(){ 
        var modalAttendants = $(document.getElementById("modalAttendants"));
        objDefault.maskLoaderShow();
        modalAttendants.load("groups/attendants", function(){
        	objModal.openModal("modalAttendants");
        	objDefault.maskLoaderHide();
        });
    }
    
    function servicesGroup(){ 
        var modalServices = $(document.getElementById("modalServices"));
        objDefault.maskLoaderShow();
        modalServices.load("groups/services", function(){
        	objModal.openModal("modalServices");
        	objDefault.maskLoaderHide();
        });
    }
    
    function setRepassGroups(){
    	var modalRepassGroups = $(document.getElementById("modalRepassGroups"));
        objDefault.maskLoaderShow();
        modalRepassGroups.load("groups/repassGroups", function(){
        	objModal.openModal("modalRepassGroups");
        	objDefault.maskLoaderHide();
        });
    }
		
		   
});//init		 


function edit(com,grid){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
          
                var items = $('.trSelected',grid);
                var itemlist ='';
                for(i=0;i<items.length;i++){
                    itemlist+= items[i].id.substr(3);
                }
                
                var modalEdit = $(document.getElementById("modalGroupsEdit"));
		        objDefault.maskLoaderShow();
		        modalEdit.load("groups/editform/id/"+itemlist, function(){
		        	objDefault.init();
	        	
		        	$("#formGroupsEdit").validate({
		        		wrapper: "li class='error'",            		
		        		errorPlacement: function(error, element) {
							error.appendTo(element.parents('.field').parent());
						},
					  	rules: {
					  		name: {
					  			required: true
					  		},
					  		level: {
					  			required: true
					  		},
					  		costumer: {
					  			required: true
					  		}	  		
					 	}
					});
				
	
		        	objModal.openModal("modalGroupsEdit");
		        	objDefault.maskLoaderHide();
		        })
                
                //window.open('groups/editform/id/'+itemlist,'','height = 250, width = 600');
            
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
function disable(com, grid){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            
            var modalDisable = $(document.getElementById("modalGroupsDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("groups/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalGroupsDisable");
            	objDefault.maskLoaderHide();
            });
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
function enable(com, grid){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            
            var modalEnable = $(document.getElementById("modalGroupsActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("groups/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalGroupsActive");
            	objDefault.maskLoaderHide();
            });  
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}	

$(document.getElementById('modalGroupsInsert')).find('form').live("submit",function(){	
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendGroups'));	
	objDefault.buttonAction($btn,'disabled');
	$.post('groups/checkNameGroup',$self.serialize(),function(resposta) {
    	$error = $(document.getElementById('name')).parent().parent();
    	$error.find(".error").remove();
        if(resposta == false){
        	$error.append("<li class='error'>"+aLang['Group_exists'].replace (/\"/g, "")+"</li>");
        	objDefault.buttonAction($btn,'enabled');
        }
        else{
        	$.ajax({
				type: "POST",
				url: "groups/insert",
				data: $self.serialize(),
				error: function (ret) {
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalGroupsInsert");
				},
				success: function(ret) {
					if(ret){
						objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalGroupsInsert");
						$("#flexigrid2").flexReload();
					}
					else
						objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalGroupsInsert");
				},
				complete: function(){
					objDefault.buttonAction($btn,'enabled');
				}
			});	            
        }
    });
});

$(document.getElementById('modalGroupsEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendGroupsEdit')),
		$nameEdit = document.getElementById('nameEdit').value.toUpperCase(),
		$oldName = document.getElementById('oldName').value.toUpperCase();
			
	objDefault.buttonAction($btn,'disabled');
	$.post('groups/checkNameGroup',$self.serialize(),function(resposta) {
		
    	$error = $(document.getElementById('nameEdit')).parent().parent();
    	$error.find(".error").remove();
    	if($nameEdit == $oldName)
    		resposta = true;
    	
        if(resposta == false){
        	$error.append("<li class='error'>"+aLang['Group_exists'].replace (/\"/g, "")+"</li>");
        	objDefault.buttonAction($btn,'enabled');
        }
        else{
        	$.ajax({
				type: "POST",
				url: "groups/edit",
				data: $self.serialize(),
				error: function (ret) {
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalGroupsEdit");
				},
				success: function(ret) {
					if(ret){
						objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalGroupsEdit");
						$("#flexigrid2").flexReload();
					}
					else
						objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalGroupsEdit");
				},
				complete: function(){
					objDefault.buttonAction($btn,'enabled');
				}
			});	     
        }
    });
});

$(document.getElementById('modalGroupsDisable')).find('form').live("submit",function(){
		var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendGroupsDisable'));
	$.ajax({
		type: "POST",
		url: "groups/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalGroupsDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalGroupsDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalGroupsDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalGroupsActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendGroupsActive'));
	$.ajax({
		type: "POST",
		url: "groups/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalGroupsActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalGroupsActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalGroupsActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalRepassGroups')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendGroupsRepass'));
	$.ajax({
		type: "POST",
		url: "groups/insertRepassGroups",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalRepassGroups");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalRepassGroups");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalRepassGroups");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});



var objGroups = {		
		loadAttendants: function(){
			var $modal = $(document.getElementById(objModal.getActive())),
				$loader = $modal.find(".loader"),
				$attendants = $modal.find('.attendants');
			$attendants.addClass("none");
			if(this.value == 0){
				objModal.refreshPosition("modalAttendants");
			}else{
				$loader.show();
		        $.post('groups/loadattendants', {
		            id : this.value
		        }, function(data) {
		            $attendants.html(data);
		            $attendants.removeClass("none");
		            objModal.refreshPosition("modalAttendants");
		        }).complete(function(){
		        	$loader.hide();
		        });
	        }
		},		
		editAttendants: function(){
			var id = this.name;
				
			if(this.checked) post = "groupinsert";
			else post = "groupdelete";
			
			$.post('groups/'+post, {
                id : id
            }, function(response) {

                if (response != false) {
                } else {
                    objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalAttendants");
                }
            });			
		},
		loadServices: function(){
			var $modal = $(document.getElementById(objModal.getActive())),
				$loader = $modal.find(".loader"),
				$attendants = $modal.find('.attendants');
				
				
			$attendants.addClass("none");
			if(this.value == 0){
				objModal.refreshPosition("modalServices");
			}else{
				$loader.show();
		        $.post('groups/loadservices', {
		            id : this.value
		        }, function(data) {
		            $attendants.html(data);
		            $attendants.removeClass("none");
		            objModal.refreshPosition("modalServices");
		        }).complete(function(){
		        	$loader.hide();
		        });
	        }
		}
	}

$("#content")
	.off(".contentloaded")
	.on("change.contentloaded", "#loadAttendants", objGroups.loadAttendants)
	.on("change.contentloaded", ".attendants input[type=checkbox]", objGroups.editAttendants)
	.on("change.contentloaded", ".#service", objGroups.loadServices);
	
