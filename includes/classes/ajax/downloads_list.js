$(document).ready(function(){
		
    $("#flexigrid2").flexigrid({
        url: 'downloads/json/',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: '', 
            name : 'icon', 
            width : 25, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            width : 200, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Category'].replace (/\"/g, ""), 
            name : 'category', 
            width : 200, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Version'].replace (/\"/g, ""), 
            name : 'version_description', 
            width : 50, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Date'].replace (/\"/g, ""), 
            name : 'date', 
            width : 80, 
            sortable : true, 
            align: 'left'
        }
        ,
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
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            isdefault: true
        }					
        ],
        sortname: "date",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['Downloads'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect: true
    }
    ); 
		
    
		
	$("input[name=local]").live("change",function(){
		var val = this.value,
			$window = $(this).parents('.window'),
			fileC = $window.find(".fileComputer"),
			fileU = $window.find(".fileURL");
		if(val == "C"){
			fileC.removeClass("none");
			fileU.addClass("none");
		}else{
			fileC.addClass("none");
			fileU.removeClass("none");
		}
	});
	
	$("#formDownloadsInsertCategory").validate({
		wrapper: "li class='error'",            		
		errorPlacement: function(error, element) {
			error.appendTo(element.parent().parent());
		},
	  	rules: {
	    	newcategoryname: {
	      		required: true
	    	}
	 	}
	});	
	
	$(document.getElementById('modalDownloadInsertCategory')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnAddCategory'));
		$.ajax({
			type: "POST",
			url: "downloads/categoryInsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalDownloadInsertCategory");
			},
			success: function(ret) {
				if(ret){					
					$.post("downloads/categories", {}, function(valor) {
			            $("select[name=categories]").html(valor);
			        }).complete(function(){
			        	$('select[name=categories] option[value="' + ret + '"]').attr({ selected : "selected" });
			        	objModal.openModal(objModal.getActive());
						$self[0].reset();
			        })
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalDownloadInsertCategory");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
	
	$(document.getElementById('modalDownloadsInsert')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEnviarDownloadsInsert')),
			$localval = $("input[name=local]:checked").val(),
			$link = $(document.getElementById('link')).val();
			
		var regex=/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/
		if($localval == "U"){
			if(!regex.test($link)){
				$fileURL = $(document.getElementById('link')).parents(".fileURL");
				$fileURL.find(".error").remove();
				$fileURL.find('ul').append("<li class='error ml0'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
				return false;
			}
		}
		
		$.post('downloads/sessionCheck', {}, function(data) {
			if($localval == "C" && !data){
				$fileComputer = $(".fileComputer");
				$fileComputer.find(".error").remove();
				$fileComputer.find('ul').append("<li class='error ml0'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
				return false;
			}
			
			$.ajax({
				type: "POST",
				url: "downloads/insert",
				data: $self.serialize(),
				error: function (ret) {
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalDownloadsInsert");
				},
				success: function(ret) {
					if(ret){
						objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalDownloadsInsert");
						$("#flexigrid2").flexReload();
					}
					else
						objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalDownloadsInsert");
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
		

	$(document.getElementById('modalDownloadsDisable')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendDownloadsDisable'));
		$.ajax({
			type: "POST",
			url: "downloads/deactivate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalDownloadsDisable");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalDownloadsDisable");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalDownloadsDisable");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalDownloadsActive')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendDownloadsActive'));
		$.ajax({
			type: "POST",
			url: "downloads/activate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalDownloadsActive");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalDownloadsActive");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalDownloadsActive");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});

	$(document.getElementById('modalDownloadsDelete')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendDownloadsDelete'));
		$.ajax({
			type: "POST",
			url: "downloads/delete",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalDownloadsDelete");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalDownloadsDelete");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalDownloadsDelete");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});

	$("#removeFile").live("click",function(){
		var id = $(this).data("id");
		$(this).parents("li.field").html('<iframe src="downloads/upload/id/'+id+'" name="ianexo" id="ianexo" height="21" frameborder="0" scrolling="no"></iframe>');
	});
	
	
	$(document.getElementById('modalDownloadsEdit')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEnviarDownloadsEdit')),
			$localval = $self.find("input[name=local]:checked").val(),
			$link = $(document.getElementById('linkEdit')).val();
		
		var regex=/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/
		if($localval == "U"){
			if(!regex.test($link)){
				$fileURL = $(document.getElementById('linkEdit')).parents(".fileURL");
				$fileURL.find(".error").remove();
				$fileURL.find('ul').append("<li class='error ml0'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
				return false;
			}
		}
		
		$.post('downloads/sessionCheck', {}, function(data) {
			if($localval == "C" && !data){
				$fileComputer = $self.find(".fileComputer");
				$fileComputer.find(".error").remove();
				$fileComputer.find('ul').append("<li class='error ml0'>"+aLang['Required_field'].replace (/\"/g, "")+"</li>");
				return false;
			}
			
			$.ajax({
				type: "POST",
				url: "downloads/update",
				data: $self.serialize(),
				error: function (ret) {
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalDownloadsEdit");
				},
				success: function(ret) {
					if(ret){
						objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalDownloadsEdit");
						$("#flexigrid2").flexReload();
					}
					else
						objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalDownloadsEdit");
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

		   
});//init		 
	
function novo(){ 
    if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        var modalInsert = $(document.getElementById("modalDownloadsInsert"));
        objDefault.maskLoaderShow();
        modalInsert.load("downloads/modalinsert", function(){
        	objDefault.init();
        	objModal.openModal("modalDownloadsInsert");  
        	
        	$("#formDownloadInsert").validate({
        		wrapper: "li class='error'",            		
        		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		title: {
			  			required: true
			  		},
			    	date: {
			      		required: true,
			      		hdDate: true
			    	},
			    	categories: {
			  			required: true
			  		},
			  		shortdesc: {
			  			required: true
			  		},
			  		local: {
			  			required: true
			  		}
			 	}
			});
        	objDefault.maskLoaderHide();
        })
        
        
    }
}	

function encerra(com, grid){
    if (access[3]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
            
            var items = $('.trSelected');
            var id = items[0].id.substr(3);
            
            
            var modalDelete = $(document.getElementById("modalDownloadsDelete"));
            objDefault.maskLoaderShow();
            modalDelete.load("downloads/deletemodal/id/"+id, function(){
            	objModal.openModal("modalDownloadsDelete");
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
                
        	var modalEdit = $(document.getElementById("modalDownloadsEdit"));
            objDefault.maskLoaderShow();
            modalEdit.load("downloads/modaledit/id/"+itemlist, function(){
            	objDefault.init();
            	objModal.openModal("modalDownloadsEdit");  
            	
            	$("#formDownloadEdit").validate({
            		wrapper: "li class='error'",            		
            		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		title: {
				  			required: true
				  		},
				    	date: {
				      		required: true,
				      		hdDate: true
				    	},
				    	categories: {
				  			required: true
				  		},
				  		shortdesc: {
				  			required: true
				  		},
				  		local: {
				  			required: true
				  		}
				 	}
				});
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
                
                var modalDisable = $(document.getElementById("modalDownloadsDisable"));
	            objDefault.maskLoaderShow();
	            modalDisable.load("downloads/deactivatemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalDownloadsDisable");
	            	objDefault.maskLoaderHide();
	            });            
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
                
                var modalActive = $(document.getElementById("modalDownloadsActive"));
	            objDefault.maskLoaderShow();
	            modalActive.load("downloads/activatemodal/id/"+itemlist, function(){
	            	objModal.openModal("modalDownloadsActive");
	            	objDefault.maskLoaderHide();
	            });            
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}	