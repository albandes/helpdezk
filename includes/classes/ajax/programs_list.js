$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'program/json/',  
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
            display: aLang['Controller'].replace (/\"/g, ""), 
            name : 'controller', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Module'].replace (/\"/g, ""),  
            name : 'module', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Category'].replace (/\"/g, ""),  
            name : 'category', 
            width : 100, 
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
        },
        {
            separator:true
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""),  
            name : 'tbp.name', 
            isdefault: true
        },
        {
            display: aLang['Module'].replace (/\"/g, ""),  
            name : 'tbm.name', 
            isdefault: false
        }
					
        ],
        sortname: "NAME",
        sortorder: "ASC",
        usepager: true,
        title: ' :: '+aLang['pgr_programs'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,     
        resizable: false,
        minimizado: false,
        singleSelect : true
    });        
    
    $("#formProgamsInsertCategory").validate({
		wrapper: "li class='error'",            		
		errorPlacement: function(error, element) {
			error.appendTo(element.parent().parent());
		},
	  	rules: {
	  		modules2: {
	  			required: true
	  		},
	    	newcategoryname: {
	      		required: true
	    	}
	 	}
	});	
	
	$(document.getElementById('modalProgramInsertCategory')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnAddCategory'));
		$.ajax({
			type: "POST",
			url: "program/categoryinsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalProgramInsertCategory");
			},
			success: function(ret) {
				if(ret){					
					modalActive = objModal.getActive();
					if(modalActive == "modalProgramInsert"){
						$('#modules option[value="' + $("#modules2").val() + '"]').attr({ selected : "selected" });
						name = "category";
					}
					else{
						$('#modulesEdit option[value="' + $("#modules2").val() + '"]').attr({ selected : "selected" });
						name = "categoryEdit";
					}
					
					objCategory.load2($("#modules2").val(),ret,name);
					objModal.openModal(modalActive);
					$self[0].reset();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalProgramInsertCategory");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
	
	
	objCategory = {
		load: function(){
			
			if(objModal.getActive() == "modalProgramInsert")
				name = "category";
			else
				name = "categoryEdit";
			
			var $select = $("select[name="+name+"]"),
				val = this.value;
			
			if(val == 0){
				$select.html('<option value="">'+aLang['Select_module'].replace (/\"/g, "")+'</option>');
				return;
			}
			
			$("select[name="+name+"]").html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
			$.post("program/category", {
				module : val
			}, function(valor) {
				$("select[name="+name+"]").html(valor);
			})
		},
		load2: function(val,checked,name){
			var $select = $("select[name="+name+"]");
			if(val == 0){
				$select.html('<option value="">'+aLang['Select_module'].replace (/\"/g, "")+'</option>');
				return;
			}
			$("select[name="+name+"]").html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
			$.post("program/category", {
				module : val
			}, function(valor) {
				$("select[name="+name+"]").html(valor);
				if(checked)	$('select[name='+name+'] option[value="' + checked + '"]').attr({ selected : "selected" });
			})
		}
	}
	
	
	$("#content")
		.off(".contentloaded")
		.on("change.contentloaded", "#modules", objCategory.load)
		.on("change.contentloaded", "#modulesEdit", objCategory.load);
	
	
	
	
	$(document.getElementById('modalProgramInsert')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendProgramInsert'));
		$.ajax({
			type: "POST",
			url: "program/insert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalProgramInsert");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalProgramInsert");
					$("#flexigrid2").flexReload();
					objModal.openModal("modalProgramConfirm");
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalProgramInsert");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('btnProgramYes')).live("click",function(){
		objModal.closeModal("modalProgramConfirm");
		$.address.value("/typepersonpermission/");
	});
	
	$(document.getElementById('modalProgramDisable')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendProgramDisable'));
		$.ajax({
			type: "POST",
			url: "program/deactivate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalProgramDisable");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalProgramDisable");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalProgramDisable");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalProgramActive')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendProgramDisable'));
		$.ajax({
			type: "POST",
			url: "program/activate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalProgramActive");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalProgramActive");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalProgramActive");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});

	$(document.getElementById('modalProgramEdit')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendProgramEdit'));
		$.ajax({
			type: "POST",
			url: "program/edit",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalProgramEdit");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalProgramEdit");
					$("#flexigrid2").flexReload();
					
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalProgramEdit");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
    
    $("#permEdit").live("click",function(){
    	if(this.checked){
    		$("#boxPerms").removeClass("none");
    		objModal.refreshPosition("modalProgramEdit");
    	}else{
    		$("#boxPerms").addClass("none");
    		objModal.refreshPosition("modalProgramEdit");
    	}
    })
    
           
});//init		 
	
	
function novo(){       
    if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
    	var modalInsert = $(document.getElementById("modalProgramInsert"));
        objDefault.maskLoaderShow();
    	
    	modalInsert.load("program/insertmodal", function(){
        	objDefault.init();
        	objModal.openModal("modalProgramInsert");      	
        	
        	$("#formProgramsInsert").validate({
        		wrapper: "li class='error'",            		
        		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		modules: {
			  			required: true
			  		},
			    	category: {
			      		required: true
			    	},
			    	name: {
			      		required: true
			    	},
			    	controller: {
			      		required: true
			    	}
			 	}
			});
        	objDefault.maskLoaderHide();
        })
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
            
            if(itemlist == 3){
            	objDefault.notification("error",aLang['Deactivate_program'].replace (/\"/g, ""),"modalInfo");
	    		objModal.openModal("modalInfo");
            }else{
            	var modalDisable = $(document.getElementById("modalProgramDisable"));
	            objDefault.maskLoaderShow();
	            modalDisable.load("program/deactivatemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalProgramDisable");
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
	            var modalActive = $(document.getElementById("modalProgramActive"));
	            objDefault.maskLoaderShow();
	            modalActive.load("program/activatemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalProgramActive");
	            	objDefault.maskLoaderHide();
	            })
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
            var modalEdit = $(document.getElementById("modalProgramEdit"));
            objDefault.maskLoaderShow();
            modalEdit.load("program/editmodal/id/"+itemlist, function(){
            	$("#formProgramsEdit").validate({
            		wrapper: "li class='error'",            		
            		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		modulesEdit: {
				  			required: true
				  		},
				    	categoryEdit: {
				      		required: true
				    	},
				    	nameEdit: {
				      		required: true
				    	},
				    	controllerEdit: {
				      		required: true
				    	}
				 	}
				});
            	objModal.openModal("modalProgramEdit");
            	objDefault.maskLoaderHide();
            })
           
           
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
function permission(){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            if($('.trSelected',grid).length>1){
                alert(aLang['Alert_select_just_one'].replace (/\"/g, ""));
            }
            else{
                var items = $('.trSelected',grid);
                var itemlist ='';
                for(i=0;i<items.length;i++){
                    itemlist+= items[i].id.substr(3);
                }
                load('program/editpermissions/id/'+itemlist);
            }
        }
        else{
            alert(aLang['Alert_select_one'].replace (/\"/g, ""));
        }
    }
}