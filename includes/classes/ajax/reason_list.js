$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'reason/json/',  
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: aLang['Type'].replace (/\"/g, ""),  
            name : 'service', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Reason'].replace (/\"/g, ""), 
            name : 'reason', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Available'].replace (/\"/g, ""), 
            name : 'status', 
            width : 60, 
            sortable : true, 
            align: 'center'
        }
        ],

        buttons : [
        {
            name: aLang['New'].replace (/\"/g, ""),  
            bclass: 'add', 
            onpress: novo
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
        }
        ,

        /*{
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
        ,*/

        {
            name: aLang['Delete'].replace (/\"/g, ""),  
            bclass: 'delete', 
            onpress: encerra
        }
        ],

        searchitems : [
        {
            display: aLang['Reason'].replace (/\"/g, ""),  
            name : 'tbr.reason', 
            isdefault: true
        }					
        ],

        sortname: "service",
        sortorder: "ASC",
        usepager: true,
        title: ' :: '+aLang['Reason'].replace (/\"/g, "")+'s' ,
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
        if (com == 'Jurídicas') {
            somapar2('N');
        } else if (com == 'Fisicas') {
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
            var modalInsert = $(document.getElementById("modalReasonInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("reason/modalInsert", function(){
	        	objDefault.init();
	        	
	        	$("#formReasonInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		reason: {
				  			required: true
				  		},
				  		service: {
				  			required: true
				  		}			  		
				 	}
				});
	        	
	        	objModal.openModal("modalReasonInsert");
	        	objDefault.maskLoaderHide();
	        })
        }
    }	   
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
            var modalDelete = $(document.getElementById("modalReasonDelete"));
	        objDefault.maskLoaderShow();
	        modalDelete.load("reason/deletemodal/id/"+itemlist, function(){
	        	objDefault.init();
	        	
	        	objModal.openModal("modalReasonDelete");
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
            var modalEdit = $(document.getElementById("modalReasonEdit"));
	        objDefault.maskLoaderShow();
	        modalEdit.load("reason/editform/id/"+itemlist, function(){
	        	objDefault.init();
	        	$("#formReasonEdit").validate({
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
	        	objModal.openModal("modalReasonEdit");
	        	objDefault.maskLoaderHide();
	        })            
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}

/*
function disable(com, grid){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            if(confirm(aLang['Deactivate'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, "")+'?')){
                var items = $('.trSelected',grid);
                var itemlist ='';
                for(i=0;i<items.length;i++){
                    itemlist+= items[i].id.substr(3)+",";
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "reason/deactivate",
                    data: "items="+itemlist,
                    success: function(data){
                        //alert("Query: "+data.query+" - Total affected rows: "+data.total);
                        alert(aLang['Alert_deactivated'].replace (/\"/g, ""));
                        $("#flexigrid2").flexReload();
                    }
                });
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
            if(confirm(aLang['Activate'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, ""))){
                var items = $('.trSelected',grid);
                var itemlist ='';
                for(i=0;i<items.length;i++){
                    itemlist+= items[i].id.substr(3)+",";
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "reason/activate",
                    data: "items="+itemlist,
                    success: function(data){
                        //alert("Query: "+data.query+" - Total affected rows: "+data.total);
                        alert(aLang['Alert_activated'].replace (/\"/g, ""));
                        $("#flexigrid2").flexReload();
                    }
                });
            }
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}		
*/
$(document.getElementById('modalReasonInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendReason'));
	$.ajax({
		type: "POST",
		url: "reason/insert",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalReasonInsert");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalReasonInsert");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalReasonInsert");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalReasonEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendReasonEdit'));
	$.ajax({
		type: "POST",
		url: "reason/edit",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalReasonEdit");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalReasonEdit");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalReasonEdit");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalReasonDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendReasonDelete'));
	$.ajax({
		type: "POST",
		url: "reason/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalReasonDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalReasonDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalReasonDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});