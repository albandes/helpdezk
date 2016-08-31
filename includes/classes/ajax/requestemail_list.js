$(document).ready(function(){
		
    $("#flexigrid2").flexigrid({
        url: 'requestEmail/json/',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['Server'].replace (/\"/g, ""),
            name : 'serverurl', 
            width : 200, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Type'].replace (/\"/g, ""), 
            name : 'servertype', 
            width : 200, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Port'].replace (/\"/g, ""),
            name : 'serverport', 
            width : 80, 
            sortable : true, 
            align: 'left'
        }
        ,
        {
            display: aLang['Filter_by_sender'].replace (/\"/g, ""),
            name : 'filter_from', 
            width : 300, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Filter_by_subject'].replace (/\"/g, ""),
            name : 'filter_from', 
            width : 300, 
            sortable : true, 
            align: 'left'
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
        sortname: "idgetemail",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['pgr_email_request'].replace (/\"/g, ""),
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
            
          	var modal = "modalRequestEmailInsert",
          		modalInsert = $(document.getElementById(modal));
	        objDefault.maskLoaderShow();
	        modalInsert.load("requestEmail/modalInsert", function(){
	        	objDefault.init();	        	
	        	
	        	$("#formRequestEmailInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		serverInsert: {
				  			required: true
				  		},
				  		typeInsert: {
				  			required: true
				  		},
				  		insertPort: {
				  			required: true
				  		},
				  		insertEmail: {
				  			required: true
				  		},
				  		insertPassword: {
				  			required: true
				  		},
				  		cmbService: {
				  			required: true
				  		} 		
				 	}
				});
	        	
	        	objModal.openModal(modal);
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
           
            var items = $('.trSelected'),
            	id = items[0].id.substr(3),            
            	modalDelete = $(document.getElementById("modalRequestEmailDelete"));
            
			objDefault.maskLoaderShow();
			modalDelete.load("requestEmail/deletemodal/id/"+id, function(){
				objModal.openModal("modalRequestEmailDelete");
				objDefault.maskLoaderHide();
			});
           
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
            	
            	var modalEdit = $(document.getElementById("modalRequestEmailEdit"));
		        objDefault.maskLoaderShow();
		        modalEdit.load("requestEmail/editform/id/"+itemlist, function(){
		        	objDefault.init();
		        			        	
		        	$("#formRequestEmailEdit").validate({
		        		wrapper: "li class='error'",            		
		        		errorPlacement: function(error, element) {
							error.appendTo(element.parents('.field').parent());
						},
					  	rules: {
					  		serverInsert: {
					  			required: true
					  		},
					  		typeInsert: {
					  			required: true
					  		},
					  		insertPort: {
					  			required: true
					  		},
					  		insertEmail: {
					  			required: true
					  		},
					  		insertPassword: {
					  			required: true
					  		},
					  		cmbService: {
					  			required: true
					  		} 		
					 	}
					});

		        	objModal.openModal("modalRequestEmailEdit");
		        	objDefault.maskLoaderHide();
		        })
            
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}



$(document.getElementById('modalRequestEmailInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendRequestEmail'));
	$.ajax({
		type: "POST",
		url: "requestEmail/insert",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalRequestEmailInsert");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalRequestEmailInsert");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalRequestEmailInsert");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalRequestEmailEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendRequestEmailEdit'));
	$.ajax({
		type: "POST",
		url: "requestEmail/edit",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalRequestEmailEdit");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalRequestEmailEdit");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalRequestEmailEdit");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalRequestEmailDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendRequestEmailDelete'));
	$.ajax({
		type: "POST",
		url: "requestEmail/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalRequestEmailDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalRequestEmailDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalRequestEmailDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});


var objPerson = {
	showBoxCompany: function(){
		if($(this).attr("id") == "editCreateUser"){
			var $cmbDep = $('#formRequestEmailEdit #cmbEditDepartment');
		}else{
			var $cmbDep = $('#formRequestEmailInsert #cmbInsertDepartment');
		}
		
    	if($(this).is(":checked")){
    		$(".createNewUser").removeClass("none");    		
    		$cmbDep.rules( "add", {
				required: true
			});
    		
    	}else{
    		$cmbDep.rules("remove","required");
    		$(".createNewUser").addClass("none");
    	}
    },
	changeCompany: function(){
		if($(this).attr("id") == "cmbEditCompany"){
			var name = "cmbEditDepartment"
		}else{
			var name = "cmbInsertDepartment";
		}
    	var val = this.value;
    		$departament = $(document.getElementById(objModal.getActive())).find("select[name="+name+"]");
    	
    	$departament.html('<option value="0">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
        $.post("person/department", {
            company : val
        }, function(valor) {
            $departament.html(valor);
        })
    }
}


var $formRequestEmail = $("#modalRequestEmailInsert");
var objService = {
	changeArea: function(){
		var $modalFind = $(document.getElementById(objModal.getActive()));
			$valarea = this.value,
    		$type = $modalFind.find("select[name=cmbType]"),
	    	$item = $modalFind.find("select[name=cmbItem]"),
	    	$service = $modalFind.find("select[name=cmbService]");
    		
    	$type.html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
    	$item.html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
    	$service.html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');     	
    	$.post(path+"/helpdezk/operator/type",{area: $valarea},
        function(valor){
            $type.html(valor);
            return objService.changeItem();
        })
	},
	changeItem: function(){
		var $modalFind = $(document.getElementById(objModal.getActive())),
			$item = $modalFind.find("select[name=cmbItem]"),
    		$valtype = $modalFind.find("select[name=cmbType]").val(),
    		$service = $modalFind.find("select[name=cmbService]");
    		
    	$item.html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
    	$service.html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
        $.post(path+"/helpdezk/operator/item",{type: $valtype},
        function(valor){
            $item.html(valor);
            return objService.changeService();
        })
	},
	changeService: function(){
		var $modalFind = $(document.getElementById(objModal.getActive())),
			$service = $modalFind.find("select[name=cmbService]"),
    		$valitem = $modalFind.find("select[name=cmbItem]").val();
		$service.html('<option value="">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
    	$.post(path+"/helpdezk/operator/service",{item: $valitem},
    	function(valor){
        	$service.html(valor);
    	})
	}
}

$("#content")
	.off(".contentloaded")
	.on("change.contentloaded", "#cmbArea", objService.changeArea)
	.on("change.contentloaded", "#cmbType", objService.changeItem)
	.on("change.contentloaded", "#cmbItem", objService.changeService)
	.on("change.contentloaded", "#cmbAreaEdit", objService.changeArea)
	.on("change.contentloaded", "#cmbTypeEdit", objService.changeItem)
	.on("change.contentloaded", "#cmbItemEdit", objService.changeService)
	.on("change.contentloaded", "#cmbInsertCompany", objPerson.changeCompany)
	.on("change.contentloaded", "#insertCreateUser", objPerson.showBoxCompany)
	.on("change.contentloaded", "#editCreateUser", objPerson.showBoxCompany)
	.on("change.contentloaded", "#cmbEditCompany", objPerson.changeCompany); 

$("#typeInsert, #typeEdit").live("change",function(){	
	if($(this).attr("id") == "typeEdit"){
		var $port = $(document.getElementById("editPort"));
	}else{
		var $port = $(document.getElementById("insertPort"));
	}	
	if(this.value == "pop-gmail"){
		$port.val("995");
		$port.attr("readonly","readonly");
	}else if(this.value == "imap-gmail"){
		$port.val("993");
		$port.attr("readonly","readonly");
	}else if(this.value == "pop"){
		$port.val("110");
		$port.removeAttr("readonly");
	}else{
		$port.val("143");
		$port.removeAttr("readonly");
	}	
})
  

