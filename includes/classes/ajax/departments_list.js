$(document).ready(function(){		
    $("#flexigrid2").flexigrid({
        url: 'department/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'tbp.name', 
            width : 120, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Department'].replace (/\"/g, ""), 
            name : 'department', 
            width : 160, 
            sortable : true, 
            align: 'left'
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
            separator:true
        }
        ,

        {
            name: aLang['Delete'].replace (/\"/g, ""), 
            bclass: 'delete', 
            onpress: encerra
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'tbd.name', 
            isdefault: true
        }					
        ],
        sortname: "department",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Departments'].replace (/\"/g, ""),
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
            
            var modalInsert = $(document.getElementById("modalDepartmentsInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("department/modalInsert", function(){
	        	objDefault.init();
	        	
	        	$("#formDepartmentInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		name: {
				  			required: true
				  		},
				  		company: {
				  			required: true
				  		}				  		
				 	}
				});
	        	
	        	objModal.openModal("modalDepartmentsInsert");
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
            
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
            
            var modalDelete = $(document.getElementById("modalDepartmentsDelete"));
            objDefault.maskLoaderShow();
            modalDelete.load("department/deletemodal/id/"+itemlist, function(){
            	objModal.openModal("modalDepartmentsDelete");
            	objDefault.maskLoaderHide();
            });   
        } else {
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
            var modalEdit = $(document.getElementById("modalDepartmentsEdit"));
	        objDefault.maskLoaderShow();
	        modalEdit.load("department/editform/id/"+itemlist, function(){
	        	objDefault.init();
	        	$("#formDepartmentEdit").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		name: {
				  			required: true
				  		},
				  		company: {
				  			required: true
				  		}				  		
				 	}
				});
	        	objModal.openModal("modalDepartmentsEdit");
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
            
            var modalDisable = $(document.getElementById("modalDepartmentsDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("department/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalDepartmentsDisable");
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
            
            var modalEnable = $(document.getElementById("modalDepartmentsActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("department/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalDepartmentsActive");
            	objDefault.maskLoaderHide();
            });          
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}



$(document.getElementById('modalDepartmentsInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendDepartment'));
	
	objDefault.buttonAction($btn,'disabled');
	$.post('department/checkDepartmentName',$self.serialize(),function(resposta) {
    	$error = $(document.getElementById('nameDepartment')).parent().parent();
    	$error.find(".error").remove();
        if(resposta == false){
        	$error.append("<li class='error'>"+aLang['Department_exists'].replace (/\"/g, "")+"</li>");
        	objDefault.buttonAction($btn,'enabled');
        }
        else{
        	$.ajax({
				type: "POST",
				url: "department/insert",
				data: $self.serialize(),
				error: function (ret) {
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalDepartmentsInsert");
				},
				success: function(ret) {
					if(ret){
						objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalDepartmentsInsert");
						$("#flexigrid2").flexReload();
					}
					else
						objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalDepartmentsInsert");
				},
				complete: function(){
					objDefault.buttonAction($btn,'enabled');
				}
			});
            
        }
    });
	
});

$(document.getElementById('modalDepartmentsEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendDepartment'));
	
	objDefault.buttonAction($btn,'disabled');
	$.post('department/checkDepartmentName',$self.serialize(),function(resposta) {
    	$error = $(document.getElementById('nameDepartmentEdit')).parent().parent();
    	$error.find(".error").remove();
        if(resposta == false){
        	$error.append("<li class='error'>"+aLang['Department_exists'].replace (/\"/g, "")+"</li>");
        	objDefault.buttonAction($btn,'enabled');
        }
        else{
        	$.ajax({
				type: "POST",
				url: "department/edit",
				data: $self.serialize(),
				error: function (ret) {
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalDepartmentsEdit");
				},
				success: function(ret) {
					if(ret){
						objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalDepartmentsEdit");
						$("#flexigrid2").flexReload();
					}
					else
						objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalDepartmentsEdit");
				},
				complete: function(){
					objDefault.buttonAction($btn,'enabled');
				}
			});	
            
        }
    });
	
	
	
	/*
	
	
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendDepartmentEdit'));
	$.ajax({
		type: "POST",
		url: "department/edit",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalDepartmentsEdit");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalDepartmentsEdit");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalDepartmentsEdit");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	*/
});

$(document.getElementById('modalDepartmentsDisable')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendDownloadsDisable'));		
	$.ajax({
		type: "POST",
		url: "department/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalDepartmentsDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalDepartmentsDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalDepartmentsDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalDepartmentsActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendDepartmentActive'));		
	$.ajax({
		type: "POST",
		url: "department/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalDepartmentsActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalDepartmentsActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalDepartmentsActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalDepartmentsDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendDepartmentDelete'));		
	$.ajax({
		type: "POST",
		url: "department/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalDepartmentsDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalDepartmentsDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalDepartmentsDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});



$(document.getElementById('modalDepartmentsInsert')).find('#companyDep').live("change",function(){	
	var $lsrNameDep = $(this).parents('li').next();
	if(this.value){
		$lsrNameDep.removeClass("none");
	}else{
		$lsrNameDep.addClass("none");
	}
})
