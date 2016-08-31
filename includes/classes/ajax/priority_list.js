$(document).ready(function(){
    $("#flexigrid2").flexigrid({
        url: 'priority/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['Priority'].replace (/\"/g, ""), 
            name : 'name', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Exhibition_order'].replace (/\"/g, ""), 
            name : 'ord', 
            width : 105, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Color'].replace (/\"/g, ""), 
            name : 'color', 
            width : 40, 
            sortable : true, 
            align: 'center'
        },
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
        sortname: "ord",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Priority'].replace (/\"/g, "") ,
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
            var modalInsert = $(document.getElementById("modalPriorityInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("priority/modalInsert", function(){
	        	objDefault.init();
	        	
	        	jQuery('#jquery-colour-picker-insert select').colourPicker({
																			    ico:    path+"/app/themes/"+theme+"/images/jquery.colourPicker.gif", 
																			    title:    false,
																			    inputBG: false
																			});
	        	
	        	$("#formPriorityInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		name: {
				  			required: true
				  		},
				  		order: {
				  			required: true
				  		},
				  		cor: {
				  			required: true
				  		}	  		
				 	}
				});
	        	
	        	objModal.openModal("modalPriorityInsert");
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
    if (access[2]=='N'){
        alert(aLang['Alert_no_permission'].replace (/\"/g, ""));
    }
    else{
        if($('.trSelected',grid).length>0){
            var items = $('.trSelected');
            var id = items[0].id.substr(3);
            if (id > 50){
            	
            	 var itemlist ='';
                for(i=0;i<items.length;i++){
                    itemlist+= id;
                }
	            
	            var modalEncerra = $(document.getElementById("modalPriorityDelete"));
	            objDefault.maskLoaderShow();
	            modalEncerra.load("priority/deletemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalPriorityDelete");
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
        	
        	var modalEdit = $(document.getElementById("modalPriorityEdit"));
	        objDefault.maskLoaderShow();
	        modalEdit.load("priority/editform/id/"+itemlist, function(){
	        	objDefault.init();
	        	
	        	jQuery('#jquery-colour-picker2 select').colourPicker({
																		    ico:    path+"/app/themes/"+theme+"/images/jquery.colourPicker.gif", 
																		    title:    false,
																		    inputBG: false
																		});
        	
	        	$("#formPriorityEdit").validate({
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

	        	objModal.openModal("modalPriorityEdit");
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
            
            var modalDisable = $(document.getElementById("modalPriorityDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("priority/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalPriorityDisable");
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
            
            var modalEnable = $(document.getElementById("modalPriorityActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("priority/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalPriorityActive");
            	objDefault.maskLoaderHide();
            });  
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}

$(document.getElementById('modalPriorityInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendPriority'));
	$.ajax({
		type: "POST",
		url: "priority/insert",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPriorityInsert");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalPriorityInsert");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPriorityInsert");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalPriorityEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatusEdit'));
	$.ajax({
		type: "POST",
		url: "priority/edit",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalPriorityEdit");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalPriorityEdit");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalPriorityEdit");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalPriorityDisable')).find('form').live("submit",function(){
		var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendPriorityDisable'));
	$.ajax({
		type: "POST",
		url: "priority/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalPriorityDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalPriorityDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalPriorityDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalPriorityActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendPriorityActive'));
	$.ajax({
		type: "POST",
		url: "priority/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalPriorityActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalPriorityActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalPriorityActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalPriorityDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendStatusDelete'));
	$.ajax({
		type: "POST",
		url: "priority/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalPriorityDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalPriorityDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalPriorityDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});