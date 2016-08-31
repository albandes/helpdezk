$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'modules/json',  
        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            width : 250, 
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
        }
        ],

        searchitems : [
        {
            display: aLang['Module'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }
        ],

        sortname: "name",
        sortorder: "ASC",
        usepager: true,
        title:  ":: "+aLang['Modules'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,     
        resizable: false,
        minimizado: false,
        singleSelect: true
    }); 	
    
    $(document.getElementById('modalModulesInsert')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendModulesInsert'));
		$.ajax({
			type: "POST",
			url: "modules/insert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalModulesInsert");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalModulesInsert");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalModulesInsert");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
    $(document.getElementById('modalModulesEdit')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendModulesEdit'));
		$.ajax({
			type: "POST",
			url: "modules/edit",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalModulesEdit");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalModulesEdit");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalModulesEdit");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
    $(document.getElementById('modalModulesDisable')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendModuleDisable'));
		$.ajax({
			type: "POST",
			url: "modules/deactivate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalModulesDisable");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalModulesDisable");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalModulesDisable");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
    $(document.getElementById('modalModulesActive')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendModuleActive'));
		$.ajax({
			type: "POST",
			url: "modules/activate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalModulesActive");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalModulesActive");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalModulesActive");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
});//init		 

function novo(){     
    if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        var modalInsert = $(document.getElementById("modalModulesInsert"));
        objDefault.maskLoaderShow();
        modalInsert.load("modules/insertmodal", function(){
        	objModal.openModal("modalModulesInsert");            	
        	$("#formModulesInsert").validate({
        		wrapper: "li class='error'",            		
        		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		name: {
			  			required: true
			  		}
			 	}
			});
        	objDefault.maskLoaderHide();
        })
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
			var modalEdit = $(document.getElementById("modalModulesEdit"));
			objDefault.maskLoaderShow();
			modalEdit.load("modules/editform/id/"+itemlist, function(){
				objModal.openModal("modalModulesEdit");
				objDefault.maskLoaderHide();
				$("#formModulesEdit").validate({
            		wrapper: "li class='error'",            		
            		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		name: {
				  			required: true
				  		}
				 	}
				});
			})
        } else {
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
            
            if(itemlist == 1){
            	objDefault.notification("error",aLang['Deactivate_module'].replace (/\"/g, ""),"modalInfo");
	    		objModal.openModal("modalInfo");
            }else{
            	var modalDisable = $(document.getElementById("modalModulesDisable"));
	            objDefault.maskLoaderShow();
	            modalDisable.load("modules/deactivatemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalModulesDisable");
	            	objDefault.maskLoaderHide();
	            })	
            }
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
        	var modalActive = $(document.getElementById("modalModulesActive"));
            objDefault.maskLoaderShow();
            modalActive.load("modules/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalModulesActive");
            	objDefault.maskLoaderHide();
            })
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}