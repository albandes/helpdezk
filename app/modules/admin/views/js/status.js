$(document).ready(function(){
		
    $("#flexigrid2").flexigrid({
        url: 'status/json/',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['status'].replace (/\"/g, ""), 
            name : 'name', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['User_exhibition'].replace (/\"/g, ""), 
            name : 'user_view', 
            width : 160, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Color'].replace (/\"/g, ""), 
            name : 'color', 
            width : 40, 
            sortable : true, 
            align: 'center'
        }
        ,
        {
            display: aLang['status'].replace (/\"/g, ""), 
            name : 'status', 
            width : 35, 
            sortable : true, 
            align: 'center'
        }
        ],

        buttons : [		
        {
            name: aLang['New'].replace (/\"/g, ""),
            bclass: 'add', 
            onpress: novo
        },

        {
            separator:true
        },

        {
            name: aLang['edit'].replace (/\"/g, ""), 
            bclass: 'edit', 
            onpress: edit
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
            name: aLang['Delete'].replace (/\"/g, ""), 
            bclass: 'delete', 
            onpress: encerra
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }					
        ],
        sortname: "idstatus",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['status'].replace (/\"/g, ""),
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
			 
			 
    function mostra(com, grid) {
        if (com == 'Em aberto') {
            somapar2('N');
        } else if (com == 'Encerrados') {
            somapar2('S');
        }else if (com == 'Todos') {
            somapar2('A');
        }
    }
    function novo(){ 
        if (access[1]=='N'){
            objModal.openModal("modalPermission");
        }
        else{
            
          	var modalInsert = $(document.getElementById("modalStatusInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("status/modalInsert", function(){
	        	objDefault.init();
	        	
	        	jQuery('#jquery-colour-picker-insert select').colourPicker({
																			    ico:    path+"/app/themes/"+theme+"/images/jquery.colourPicker.gif", 
																			    title:    false,
																			    inputBG: false
																			});
	        	
	        	$("#formStatusInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		name: {
				  			required: true
				  		},
				  		user: {
				  			required: true
				  		},
				  		color: {
				  			required: true
				  		}	  		
				 	}
				});
	        	
	        	objModal.openModal("modalStatusInsert");
	        	objDefault.maskLoaderHide();
	        })
            
            
        }
    }
		
		   
});//init		 
	
function somapar(com2){
    $('#flexigrid2').flexOptions({
        newp:1, 
        params:[{
            name:'COD_STATUS', 
            value: com2
        }]
    });
    refresh2();
}	
	
function refresh(){
    $('#flexigrid2').flexReload();
}	

function encerra(com, grid){
    if (access[3]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
           
            var items = $('.trSelected');
            var id = items[0].id.substr(3);
            if (id > 50){
	            var modalDelete = $(document.getElementById("modalStatusDelete"));
				objDefault.maskLoaderShow();
				modalDelete.load("status/deletemodal/id/"+id, function(){
					objModal.openModal("modalStatusDelete");
					objDefault.maskLoaderHide();
				});
            }
            else{
                objDefault.notification("error",aLang['Alert_system_default'].replace (/\"/g, ""),"modalInfo");
	    		objModal.openModal("modalInfo");
            }
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
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
            	
            	var modalEdit = $(document.getElementById("modalStatusEdit"));
		        objDefault.maskLoaderShow();
		        modalEdit.load("status/editform/id/"+itemlist, function(){
		        	objDefault.init();
		        	
		        	jQuery('#jquery-colour-picker2 select').colourPicker({
																			    ico:    path+"/app/themes/"+theme+"/images/jquery.colourPicker.gif", 
																			    title:    false,
																			    inputBG: false
																			});
	        	
		        	$("#formStatusEdit").validate({
		        		wrapper: "li class='error'",            		
		        		errorPlacement: function(error, element) {
							error.appendTo(element.parents('.field').parent());
						},
					  	rules: {
					  		name: {
					  			required: true
					  		},
					  		user: {
					  			required: true
					  		},
					  		color: {
					  			required: true
					  		}	  		
					 	}
					});	

		        	objModal.openModal("modalStatusEdit");
		        	objDefault.maskLoaderHide();
		        })
            
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
            
            var modalDisable = $(document.getElementById("modalStatusDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("status/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalStatusDisable");
            	objDefault.maskLoaderHide();
            });
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
function enable(com, grid){
    if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            
            var modalEnable = $(document.getElementById("modalStatusActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("status/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalStatusActive");
            	objDefault.maskLoaderHide();
            });  
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}	



$(document.getElementById('modalStatusInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatus'));
	$.ajax({
		type: "POST",
		url: "status/insert",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalStatusInsert");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalStatusInsert");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalStatusInsert");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalStatusEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatusEdit'));
	$.ajax({
		type: "POST",
		url: "status/edit",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalStatusEdit");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalStatusEdit");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalStatusEdit");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalStatusDisable')).find('form').live("submit",function(){
		var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatusDisable'));
	$.ajax({
		type: "POST",
		url: "status/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalStatusDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalStatusDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalStatusDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalStatusActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatusActive'));
	$.ajax({
		type: "POST",
		url: "status/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalStatusActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalStatusActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalStatusActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalStatusDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatusDelete'));
	$.ajax({
		type: "POST",
		url: "status/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalStatusDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalStatusDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalStatusDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});