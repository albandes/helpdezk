$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'category/json',  
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
            name : 'title', 
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

        sortname: "title",
        sortorder: "ASC",
        usepager: true,
        title:  ":: "+aLang['Category'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,     
        resizable: false,
        minimizado: false,
        singleSelect: true
    }); 	
    
    $(document.getElementById('modalCategoryInsert')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendCategoryInsert'));
		$.ajax({
			type: "POST",
			url: "category/insert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalCategoryInsert");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalCategoryInsert");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalCategoryInsert");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
    $(document.getElementById('modalCategoryEdit')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendCategoryEdit'));
		$.ajax({
			type: "POST",
			url: "category/edit",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalCategoryEdit");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalCategoryEdit");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalCategoryEdit");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
    $(document.getElementById('modalCategoryDisable')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendModuleDisable'));
		$.ajax({
			type: "POST",
			url: "category/deactivate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalCategoryDisable");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalCategoryDisable");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalCategoryDisable");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
    $(document.getElementById('modalCategoryActive')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendModuleActive'));
		$.ajax({
			type: "POST",
			url: "category/activate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalCategoryActive");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalCategoryActive");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalCategoryActive");
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
        var modalInsert = $(document.getElementById("modalCategoryInsert"));
        objDefault.maskLoaderShow();
        modalInsert.load("category/insertmodal", function(){
        	objModal.openModal("modalCategoryInsert");            	
        	$("#formCategoryInsert").validate({
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
			var modalEdit = $(document.getElementById("modalCategoryEdit"));
			objDefault.maskLoaderShow();
			modalEdit.load("category/editform/id/"+itemlist, function(){
				objModal.openModal("modalCategoryEdit");
				objDefault.maskLoaderHide();
				$("#formCategoryEdit").validate({
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
            	var modalDisable = $(document.getElementById("modalCategoryDisable"));
	            objDefault.maskLoaderShow();
	            modalDisable.load("category/deactivatemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalCategoryDisable");
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
        	var modalActive = $(document.getElementById("modalCategoryActive"));
            objDefault.maskLoaderShow();
            modalActive.load("category/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalCategoryActive");
            	objDefault.maskLoaderHide();
            })
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}