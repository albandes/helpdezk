$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'warnings/json',  
        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
        {
            display: aLang['Topic'].replace (/\"/g, ""), 
            name : 'title_topic', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Title'].replace (/\"/g, ""), 
            name : 'title_warning', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Var_record'].replace (/\"/g, ""),
            name : 'a.dtcreate', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Initial_date'].replace (/\"/g, ""), 
            name : 'a.dtstart', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Finish_date'].replace (/\"/g, ""), 
            name : 'a.dtend', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Show_in'].replace (/\"/g, ""), 
            name : 'a.showin', 
            width : 65, 
            sortable : true, 
            align: 'left'
        }
                                        
        ],

        buttons : [
        {
            name: aLang['Warning_new'].replace (/\"/g, ""), 
            bclass: 'add', 
            onpress: novo
        },

        {
            separator:true
        },

        {
            name: aLang['Warning_edit'].replace (/\"/g, ""), 
            bclass: 'edit', 
            onpress: edit
        },
        
        {
            separator:true
        },

        {
            name: aLang['Warning_new_topic'].replace (/\"/g, ""), 
            bclass: 'add', 
            onpress: novoTopico
        },
        
        {
            separator:true
        },
        
        {
            name: aLang['Warning_view_topics'].replace (/\"/g, ""), 
            bclass: 'see', 
            onpress: seeTopics
        }
        ],

        searchitems : [
        {
            display: aLang['Warning_title_topic'].replace (/\"/g, ""), 
            name : 'title_topic', 
            isdefault: true
        },
        {
            display: aLang['Warning_title'].replace (/\"/g, ""), 
            name : 'title_warning', 
            isdefault: true
        },
        {
            display: aLang['Var_record'].replace (/\"/g, ""), 
            name : 'a.dtcreate', 
            isdefault: true
        }
        ],

        sortname: "title_topic",
        sortorder: "ASC",
        usepager: true,
        title:  ":: "+aLang['pgr_warnings'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,     
        resizable: false,
        minimizado: false,
        singleSelect: true,
        useWarning: true
    }); 	
    
    
    $("input[name=avaibleOperator]").live("click",function(){
    	var val = this.value,
    		$box = $(this).parents(".window"),
    		$lstGroups = $box.find('.lstGroups');
    	if(val == 2){
    		$lstGroups.removeClass("none");
    	}else{
    		$lstGroups.addClass("none");
    	}
    	objModal.refreshPosition("modalInsertTopic");
    });
    
    $("input[name=avaibleUser]").live("click",function(){
    	var val = this.value,
    		$box = $(this).parents(".window"),
    		$lstCompany = $box.find('.lstCompany');
    	if(val == 2){
    		$lstCompany.removeClass("none");
    	}else{
    		$lstCompany.addClass("none");
    	}
    	objModal.refreshPosition("modalInsertTopic");
    });
    
    $(document.getElementById('modalInsertTopic')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendTopicInsert'));
		$.ajax({
			type: "POST",
			url: "warnings/topicInsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertTopic");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalInsertTopic");
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertTopic");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
	
	$(document.getElementById('modalEditTopic')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendTopicEdit'));
		$.ajax({
			type: "POST",
			url: "warnings/topicEdit",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalEditTopic");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalEditTopic");
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalEditTopic");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
    
   
    objWarning = {
    	loadEditTopic: function(){    		
    		var modalEditTopic = $(document.getElementById("modalEditTopic"));
			var id = $(this).attr('href');
	        objDefault.maskLoaderShow();
	        modalEditTopic.load("warnings/modalEditTopic/id/"+id, function(){	        		        	
	        	$("#formTopicEdit").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		txtTitle: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalEditTopic");
	        	objDefault.maskLoaderHide();
	        })
	        return false;
    		
    	}
    }
    
    
    
    $("#content")
		.off(".contentloaded")		
		.on("click.contentloaded", "#tabSeeTopics .btnEdit-tp1", objWarning.loadEditTopic);
		
    
    var $modalInsertWarning = $(document.getElementById('modalInsertWarning'));
    
    
    $modalInsertWarning.find("#cmbTopic").live("change",function(){
    	var $chkUntilClosed = $(document.getElementById('chkUntilClosed')),
    		$boxEndValid 	= $(document.getElementById('boxEndValid')),
    		$validEndDate 	= $(document.getElementById('validEndDate')),
    		$validEndHour 	= $(document.getElementById('validEndHour')),
    		$chkSendEmail 	= $(document.getElementById('chkSendEmail')),
    		$validDate 		= $(document.getElementById('validDate')),
    		$validHour 		= $(document.getElementById('validHour'));
    		
    	$.getJSON("warnings/getTopicInfo/idtopic/"+this.value, function(data) {  
        	if(data.let){
        		$chkUntilClosed.removeAttr("checked");
        		$chkUntilClosed.parent().addClass("ml20");
        		$boxEndValid.removeClass("none");
        		$validEndDate.val(data.date);
        		$validEndHour.val(data.time);    		
        	}else{
        		$chkUntilClosed.attr("checked","checked");
        		$chkUntilClosed.parent().removeClass("ml20");
        		$boxEndValid.addClass("none");
        	} 
        	if(data.total > 0){
        		$("#chkShowAlert").attr("disabled","disabled");
        		$("#chkShowAlert").find("option[value=1]").attr("selected","selected");
        	}else{
        		$("#chkShowAlert").removeAttr("disabled");	
        	}   
        	 	        	
        	$validDate.val(data.date_now);
        	$validHour.val(data.time_now);        	
        	if(data.fl_emailsent == "S") $chkSendEmail.attr("checked","checked");
    		else $chkSendEmail.removeAttr("checked");
        });
    })
    
    $modalInsertWarning.find("#chkUntilClosed").live("click",function(){
    	if(this.checked){
    		$("#boxEndValid").addClass("none");
    		$(this).parent().removeClass("ml20");
    	}
    	else{
    		$("#boxEndValid").removeClass("none");
    		$(this).parent().addClass("ml20");
    	}
    })
    
    var $modalEditWarning = $(document.getElementById('modalEditWarning'));
    
    $modalEditWarning.find("#cmbTopicEdit").live("change",function(){
    	var $chkUntilClosed = $(document.getElementById('chkUntilClosedEdit')),
    		$boxEndValid 	= $(document.getElementById('boxEndValidEdit')),
    		$validEndDate 	= $(document.getElementById('validEndDateEdit')),
    		$validEndHour 	= $(document.getElementById('validEndHourEdit')),
    		$chkSendEmail 	= $(document.getElementById('chkSendEmailEdit')),
    		$validDate 		= $(document.getElementById('validDateEdit')),
    		$validHour 		= $(document.getElementById('validHourEdit'));
    		
    	$.getJSON("warnings/getTopicInfo/idtopic/"+this.value, function(data) {  
        	if(data.let){
        		$chkUntilClosed.removeAttr("checked");
        		$chkUntilClosed.parent().addClass("ml20");
        		$boxEndValid.removeClass("none");
        		$validEndDate.val(data.date);
        		$validEndHour.val(data.time);    		
        	}else{
        		$chkUntilClosed.attr("checked","checked");
        		$chkUntilClosed.parent().removeClass("ml20");
        		$boxEndValid.addClass("none");
        	} 
        	if(data.total > 0){
        		$("#chkShowAlertEdit").attr("disabled","disabled");
        		$("#chkShowAlertEdit").find("option[value=1]").attr("selected","selected");
        	}else{
        		$("#chkShowAlertEdit").removeAttr("disabled");	
        	}   
        	 	        	
        	$validDate.val(data.date_now);
        	$validHour.val(data.time_now);        	
        	if(data.fl_emailsent == "S") $chkSendEmail.attr("checked","checked");
    		else $chkSendEmail.removeAttr("checked");
        });
    })
    
    $modalEditWarning.find("#chkUntilClosedEdit").live("click",function(){
    	if(this.checked){
    		$("#boxEndValidEdit").addClass("none");
    		$(this).parent().removeClass("ml20");
    	}
    	else{
    		$("#boxEndValidEdit").removeClass("none");
    		$(this).parent().addClass("ml20");
    	}
    })
    
	$(document.getElementById('modalInsertWarning')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendWarningInsert'));
		$.ajax({
			type: "POST",
			url: "warnings/warningInsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertWarning");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalInsertWarning");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertWarning");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
	
	$(document.getElementById('modalEditWarning')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendWarningEdit'));
		$.ajax({
			type: "POST",
			url: "warnings/warningEdit",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalEditWarning");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalEditWarning");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalEditWarning");
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
        var modalInsert = $(document.getElementById("modalInsertWarning"));
        objDefault.maskLoaderShow();
        modalInsert.load("warnings/insertwarningmodal", function(){
        	objDefault.init();
        	objModal.openModal("modalInsertWarning");            	
        	$("#formTopicInsert").validate({
        		wrapper: "li class='error'",            		
        		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		cmbTopic: {
			  			required: true
			  		},
			  		txtTitle: {
			  			required: true
			  		},
			  		txtDescription: {
			  			required: true
			  		},
			  		validDate: {
			  			required: true,
			  			hdDate: true
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
            var modalEdit = $(document.getElementById("modalEditWarning"));
            objDefault.maskLoaderShow();
            modalEdit.load("warnings/modalEditWarning/id/"+itemlist, function(){
            	objDefault.init();
            	objModal.openModal("modalEditWarning");
            	/*$("#formHolidayEdit").validate({
            		wrapper: "li class='error'",            		
            		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		holiday_description: {
				  			required: true
				  		},
				    	holiday_date: {
				      		required: true,
				      		hdDate: true
				    	}
				 	}
				});*/
            	objDefault.maskLoaderHide();
            })
	    }
	    else{	    	
	    	objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
	    }
    }
}

function novoTopico(){
	if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{    	
        var modalInsertTopic = $(document.getElementById("modalInsertTopic"));
        objDefault.maskLoaderShow();
        modalInsertTopic.load("warnings/inserttopicmodal", function(){
        	objModal.openModal("modalInsertTopic");            	
        	$("#formTopicInsert").validate({
        		wrapper: "li class='error'",            		
        		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		txtTitle: {
			  			required: true
			  		}
			 	}
			});
        	objDefault.maskLoaderHide();
        })
    }
}



function seeTopics(){
    if (access[0]=='N'){
        objModal.openModal("modalPermission");
    }
    else{			
		var modalSeeTopics = $(document.getElementById("modalSeeTopics"));
		objDefault.maskLoaderShow();
		modalSeeTopics.load("warnings/modalseetopics/", function(){
			objModal.openModal("modalSeeTopics");
			objDefault.maskLoaderHide();				
		})        
    }
}

