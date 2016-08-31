$(document).ready(function(){
	
    $("#flexigrid2").flexigrid({
        url: 'evaluation/json/',
        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
        {
            display: aLang['Icon'].replace (/\"/g, ""),
            name : 'icon_name', 
            width : 40, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Answer'].replace (/\"/g, ""), 
            name : 'name', 
            width : 160, 
            sortable : true, 
            align: 'left'
        },
        
        {
            display: aLang['Question'].replace (/\"/g, ""), 
            name : 'question', 
            width : 300, 
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
        sortname: "question, idevaluation",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Evaluation'].replace (/\"/g, ""),
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
            var modalInsert = $(document.getElementById("modalEvaluationInsert"));
	        objDefault.maskLoaderShow();
	        modalInsert.load("evaluation/modalInsert", function(){
				iconList.init("lstIcon");
	        	$("#formEvaluationInsert").validate({
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
	        	
	        	objModal.openModal("modalEvaluationInsert");
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

			var modalDelete = $(document.getElementById("modalEvaluationDelete"));
            objDefault.maskLoaderShow();
            modalDelete.load("evaluation/deletemodal/id/"+id, function(){
            	objModal.openModal("modalEvaluationDelete");
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
	                
	        	var modalEdit = $(document.getElementById("modalEvaluationEdit"));
	            objDefault.maskLoaderShow();
	            modalEdit.load("evaluation/editform/id/"+itemlist, function(){
	            	iconList.init("lstIconEdit");
	            	
	            	$("#formEvaluationEdit").validate({
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
	            	objModal.openModal("modalEvaluationEdit");
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
            
            var modalDisable = $(document.getElementById("modalEvaluationDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("evaluation/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalEvaluationDisable");
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
            
            var modalEnable = $(document.getElementById("modalEvaluationActive"));
            objDefault.maskLoaderShow();
            modalEnable.load("evaluation/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalEvaluationActive");
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
    	$("#content").load('evaluation/question');
    }
}

$(document.getElementById('modalEvaluationDisable')).find('form').live("submit",function(){
		var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEvaluationDisable'));
	$.ajax({
		type: "POST",
		url: "evaluation/deactivate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalEvaluationDisable");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalEvaluationDisable");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalEvaluationDisable");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalEvaluationActive')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEvaluationActive'));
	$.ajax({
		type: "POST",
		url: "evaluation/activate",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalEvaluationActive");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalEvaluationActive");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalEvaluationActive");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalEvaluationDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEvaluationDelete'));
	$.ajax({
		type: "POST",
		url: "evaluation/delete",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalEvaluationDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalEvaluationDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalEvaluationDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalEvaluationInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEvaluation')),
		$type = $(this).find("input[name=txtIcons]:checked").val();
		
	$.post('evaluation/sessionCheck', {}, function(data) {
		if($type == "txtNew" && !data){
			$file = $(".txtNew");
			$file.find(".error").remove();
			$file.find('ul').append("<li class='error'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
			return false;
		}
		
		if($type == "txtLista" && !$("#lstIcon").val()){
			
			$fileList = $(".txtLista");
			$fileList.find(".error").remove();				
			$fileList.find('ul:first').append("<li class='error'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
			return false;
		}
		
		$.ajax({
			type: "POST",
			url: "evaluation/insert",
			data: $self.serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEvaluationInsert");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalEvaluationInsert");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEvaluationInsert");
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

$(document.getElementById('modalEvaluationEdit')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendEvaluationEdit')),
		$type = $(this).find("input[name=txtIcons]:checked").val();
		
	$.post('evaluation/sessionCheck', {}, function(data) {
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
			url: "evaluation/edit",
			data: $self.serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEvaluationEdit");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalEvaluationEdit");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEvaluationEdit");
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
	},
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


$("#content")
		.off(".contentloaded")
		.on("click.contentloaded", ".listSelectIcon .layer", iconList.open)
		.on("click.contentloaded", ".listSelectIcon a", iconList.select)
		.on("click.contentloaded", "input[name=txtIcons]", iconList.type);



		
