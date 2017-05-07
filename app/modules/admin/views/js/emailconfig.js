$(document).ready(function(){
		
    $("#flexigrid2").flexigrid({
        url: 'emailconfig/json/',  

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
            width : 700, 
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
        ,

        {
            separator:true
        },

        {
            name: aLang['Template_edit'].replace (/\"/g, ""),  
            bclass: 'email', 
            onpress: email
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }					
        ],

        sortname: "NAME",
        sortorder: "ASC",
        usepager: true,
        title: ' :: '+aLang['Email_config'].replace (/\"/g, ""), 
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect: true
    }); 			 
			 
    
    
});//init		 
	
function somapar(com2){
    $('#flexigrid2').flexOptions({
        newp:1, 
        params:[{
            name:'TIPE_PERSON', 
            value: com2
        }]
    });
    refresh2();
}	
	
function refresh(){
    $('#flexigrid2').flexReload();
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
            
            var modalDisable = $(document.getElementById("modalEmailDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("emailconfig/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalEmailDisable");
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
            
            var modalActive = $(document.getElementById("modalEmailActive"));
            objDefault.maskLoaderShow();
            modalActive.load("emailconfig/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalEmailActive");
            	objDefault.maskLoaderHide();
            });           
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
function email(com,grid){
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
                
	            var modalEmail = $(document.getElementById("modalEmailEdit"));
	            objDefault.maskLoaderShow();
	            for(var i in CKEDITOR.instances) {
					CKEDITOR.instances[i].destroy(true); 
				}
	            modalEmail.load("emailconfig/formedit/id/"+itemlist, function(){
	            	objDefault.init();
	            	$("#formEmailEdit").validate({
		        		wrapper: "li class='error'",            		
		        		errorPlacement: function(error, element) {
							error.appendTo(element.parent().parent());
						},
					  	rules: {
					  		name: {
					  			required: true
					  		},
					    	description: {
					  			required: true
					  		}
					 	}
					});	            	
	            	objModal.openModal("modalEmailEdit");
	            	objDefault.maskLoaderHide();
	            }); 
                
            }
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}



$(document.getElementById('modalEmailDisable')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEmailDisable'));
	$.ajax({
		type: "POST",
		url: "emailconfig/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalEmailDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalEmailDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalEmailDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalEmailActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEmailActive'));
	$.ajax({
		type: "POST",
		url: "emailconfig/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalEmailActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalEmailActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalEmailActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});


$(document.getElementById('modalEmailEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEmailEdit'));
		$name = $self.find("#name").val();
    	$id = $self.find("#id").val();
    	$description = CKEDITOR.instances['description2'].getData();

	objDefault.buttonAction($btn,'disabled');
    $.post('emailconfig/edittemplate', {
        name : $name,
        description : $description,
        id : $id
    }, function(resp) {
    	objDefault.buttonAction($btn,'enabled');
        if (resp != false) {
            objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalEmailEdit");
			$("#flexigrid2").flexReload();            
        } else {
            objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalEmailEdit");
        }
    });
});