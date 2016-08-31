$(document).ready(function(){
    $("#flexigrid2").flexigrid({
        url: 'costcenter/json/',  

        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'company', 
            width : 120, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Cost_center_number'].replace (/\"/g, ""), 
            name : 'cod_costcenter', 
            width : 80, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Cost_center'].replace (/\"/g, ""), 
            name : 'name', 
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
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'tbc.name', 
            isdefault: true
        }					
        ],
        sortname: "name",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Cost_center'].replace (/\"/g, ""),
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
        	var modalInsert = $(document.getElementById("modalCostcenterInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("costcenter/modalInsert", function(){
	        	objDefault.init();
	        	
	        	$("#formCostcenterInsert").validate({
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
				  		},
				  		cod: {
				  			required: true
				  		}
				 	}
				});
	        	
	        	objModal.openModal("modalCostcenterInsert");
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
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            if(confirm(aLang['Delete'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, ""))){
                var items = $('.trSelected',grid);
                var itemlist ='';
                for(i=0;i<items.length;i++){
                    itemlist+= items[i].id.substr(3)+",";
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "#",
                    data: "items="+itemlist,
                    success: function(data){
                        //alert("Query: "+data.query+" - Total affected rows: "+data.total);
                        alert(aLang['Alert_deleted'].replace (/\"/g, ""));
                        $("#flexigrid2").flexReload();
                    }
                });
            }
        } else {
            return false;
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
            
            
            var modalEdit = $(document.getElementById("modalCostcenterEdit"));
	        objDefault.maskLoaderShow();
	        modalEdit.load("costcenter/editform/id/"+itemlist, function(){
	        	objDefault.init();
	        	$("#formCostcenterEdit").validate({
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
				  		},
				  		cod: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalCostcenterEdit");
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
            
            var modalDisable = $(document.getElementById("modalCostcenterDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("costcenter/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalCostcenterDisable");
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
            
            var modalEnable = $(document.getElementById("modalCostcenterActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("costcenter/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalCostcenterActive");
            	objDefault.maskLoaderHide();
            });
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}

	
$(document.getElementById('modalCostcenterInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendCostcenter'));
	$.ajax({
		type: "POST",
		url: "costcenter/insert",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalCostcenterInsert");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalCostcenterInsert");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalCostcenterInsert");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalCostcenterEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendCostcenterEdit'));
	$.ajax({
		type: "POST",
		url: "costcenter/edit",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalCostcenterEdit");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalCostcenterEdit");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalCostcenterEdit");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalCostcenterDisable')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendCostcenterDisable'));		
	$.ajax({
		type: "POST",
		url: "costcenter/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalCostcenterDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalCostcenterDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalCostcenterDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalCostcenterActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendCostcenterActive'));		
	$.ajax({
		type: "POST",
		url: "costcenter/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalCostcenterActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalCostcenterActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalCostcenterActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});