$(document).ready(function(){
	
    $("#flexigrid2").flexigrid({
        url: 'widget/json/',
        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
					{
						display: aLang['Widget'].replace (/\"/g, ""), 
						name : 'question', 
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
        },

        {
            separator:true
        },

        {
            name: aLang['Delete'].replace (/\"/g, ""),  
            bclass: 'delete', 
            onpress: encerra
        },
        
        {
            separator:true
        },
        
        {
            name: aLang['Manage_questions'].replace (/\"/g, ""), 
            bclass: 'questman', 
            onpress: manquestion
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }					
        ],
        sortname: "name, idwidget",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Widget'].replace (/\"/g, ""),
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
            var modalInsert = $(document.getElementById("modalWidgetInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("widget/modalInsert", function(){
				//iconList.init("lstIcon");
	        	$("#formWidgetInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		controller: {
				  			required: true
				  		},
				  		name: {
				  			required: true
				  		}
						//,
				  		//txtIcons: {
				  		//	required: true
				  		//}	  		
				 	}
				});
	        	
	        	objModal.openModal("modalWidgetInsert");
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
            var items = $('.trSelected');
            var id = items[0].id.substr(3);

			var modalDelete = $(document.getElementById("modalWidgetDelete"));
            objDefault.maskLoaderShow();
            modalDelete.load("widget/deletemodal/id/"+id, function(){
            	objModal.openModal("modalWidgetDelete");
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
            if($('.trSelected',grid).length>1){
                alert(aLang['Alert_select_just_one'].replace (/\"/g, ""));
            }
            else{
                
				var items = $('.trSelected',grid);
	            var itemlist ='';
	            for(i=0;i<items.length;i++){
	                itemlist+= items[i].id.substr(3);
	            }
	                
	        	var modalEdit = $(document.getElementById("modalWidgetEdit"));
	            objDefault.maskLoaderShow();
	            modalEdit.load("widget/editform/id/"+itemlist, function(){
	            	//iconList.init("lstIconEdit");
	            	
	            	$("#formWidgetEdit").validate({
		        		wrapper: "li class='error'",            		
		        		errorPlacement: function(error, element) {
							error.appendTo(element.parents('.field').parent());
						},
					  	rules: {
					  		question: {
					  			required: true
					  		},
					  		name: {
					  			required: true
					  		},
					  		txtIcons: {
					  			required: true
					  		}	  		
					 	}
					});
	            	objModal.openModal("modalWidgetEdit");
	            	objDefault.maskLoaderHide();
	            })
            }
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
            
            var modalDisable = $(document.getElementById("modalWidgetDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("widget/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalWidgetDisable");
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
            
            var modalEnable = $(document.getElementById("modalWidgetActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("widget/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalWidgetActive");
            	objDefault.maskLoaderHide();
            });              
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}	
function manquestion(){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
    	$("#content").load('widget/question');
    }
}

$(document.getElementById('modalWidgetDisable')).find('form').live("submit",function(){
		var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendWidgetDisable'));
	$.ajax({
		type: "POST",
		url: "widget/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalWidgetDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalWidgetDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalWidgetDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalWidgetActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendWidgetActive'));
	$.ajax({
		type: "POST",
		url: "widget/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalWidgetActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalWidgetActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalWidgetActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalWidgetDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendWidgetDelete'));
	$.ajax({
		type: "POST",
		url: "widget/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalWidgetDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalWidgetDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalWidgetDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

    $(document.getElementById('modalWidgetInsert')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendWidgetInsert'));
		$.ajax({
			type: "POST",
			url: "widget/insert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalWidgetInsert");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalWidgetInsert");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalWidgetInsert");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});

$(document.getElementById('modalWidgetEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendWidgetEdit')),
		$type = $(this).find("input[name=txtIcons]:checked").val();
		
	$.post('widget/sessionCheck', {}, function(data) {
		if($type == "txtNew" && !data){
			$file = $(".txtNew");
			$file.find(".error").remove();
			$file.find('ul').append("<li class='error'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
			return false;
		}
		
		if($type == "txtLista" && !$("#lstIconEdit").val()){
			
			$fileList = $(".txtLista");
			$fileList.find(".error").remove();				
			$fileList.find('ul:first').append("<li class='error'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
			return false;
		}
		
		
		$.ajax({
			type: "POST",
			url: "widget/edit",
			data: $self.serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalWidgetEdit");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalWidgetEdit");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalWidgetEdit");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
});

/*
var iconList = {
	init: function(select){
		
		list = "<ul class='lstIcon'>";
		$cmb = $(document.getElementById(select));
		$cmb.find("option").each(function(){
			var val = this.value;
			if(val)
				list += "<li> <a href='"+val+"'><img alt='"+val+"' src='"+path+"/app/uploads/icons/"+val+"' /></a></li>";
		})
		
		list += "</ul>";
		
		$cmb.parent().append(list);
	}
	,
	open: function(){
		this.blur;
		$('.lstIcon').show();
		document.onclick = function() {
			document.onclick = function() {
				$('.lstIcon').hide();
				document.onclick = null;
			}
		}
	},
	select: function(){
		var $val = $(this).attr("href"),
			$lst = $(this).parents(".listSelectIcon").find("select");
		$lst.find("option").each(function(){
			if(this.value == $val){
				$(this).attr("selected","selected");
				return false;
			}
		})
		$('.lstIcon').hide();
		return false;
	},
	type: function(){
		var val = this.value;
		if(val == "txtNew"){
			$(".txtNew").removeClass("none_i");
			$(".txtLista").addClass("none_i");
		}else{
	
			$(".txtLista").removeClass("none_i");
			$(".txtNew").addClass("none_i");
		}
		
	}
}

*/
$("#content")
		.off(".contentloaded")
		.on("click.contentloaded", ".listSelectIcon .layer", iconList.open)
		.on("click.contentloaded", ".listSelectIcon a", iconList.select)
		.on("click.contentloaded", "input[name=txtIcons]", iconList.type);



		
