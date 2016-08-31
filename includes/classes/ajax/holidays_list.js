$(document).ready(function(){
    
    $("#flexigrid2").flexigrid({
        url: 'holidays/json/',  
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
            name : 'tbh.holiday_description', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Date'].replace (/\"/g, ""), 
            name : 'tbh.holiday_date', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'tbp.name', 
            width : 150, 
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
            name: aLang['Holiday_import'].replace (/\"/g, ""), 
            bclass: 'calendar', 
            onpress: importlast
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
            name : 'HOLIDAY_DESCRIPTION', 
            isdefault: true
        }					
        ],
        sortname: "HOLIDAY_DATE",
        sortorder: "desc",
        usepager: true,
        title: ' :: '+aLang['Holiday'].replace(/\"/g, "")+'s',
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect : true
    });
    
    $(document.getElementById('modalHolydayInsert')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEnviarHolidayInsert'));
		objDefault.buttonAction($btn,'disabled');
		$.post('holidays/insert', 
			$self.serialize()
		, function(resposta) {
			if (resposta != false) {			
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalHolydayInsert");
				$("#flexigrid2").flexReload();
			}
			else {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalHolydayInsert");					
			}
		}).complete(function(){
			objDefault.buttonAction($btn,'enabled');
		});
	});
	
	$(document.getElementById('modalHolydayEdit')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEnviarHolidayEdit')),
			date = $(document.getElementById("holiday_date_edit")).val(),
			id = $(document.getElementById("id_edit")).val(),
			description = $(document.getElementById("holiday_description_edit")).val();

		objDefault.buttonAction($btn,'disabled');
		$.post('holidays/edit',{
			date: date,
			description: description,
			id: id
		}, function(resposta) {
			if (resposta != false) {
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalHolydayEdit");
				$("#flexigrid2").flexReload();
			}
			else {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalHolydayEdit");
			}
		}).complete(function(){
			objDefault.buttonAction($btn,'enabled');
		});
	});
	
	$(document.getElementById('modalHolydayImport')).find("#lastyear").live("change",function(){
		var year = $("#lastyear").val(),
			$modal = $(document.getElementById("modalHolydayImport"))
			$boxResult = $(document.getElementById("boxResult")),
			$modalFooter = $modal.find(".modalFooter");
		if(year == 0){
			$boxResult.addClass('none');
			$modalFooter.addClass('none');
			objModal.refreshPosition("modalHolydayImport");
		}else{
			$boxResult.addClass('none');
			$modalFooter.addClass('none');
			$modal.find(".loader").show();
			$.getJSON("holidays/load/year/"+year,	            	 
            function(data)
            {
                $modal.find('.txtYear').text(data.year);
                $modal.find('.txtCount').text(data.count);

                $("#listaareas").find("tbody").empty();
                $.each(data.result, function(i,result)
                {                	
                    $("#listaareas").find("tbody")
                    	.append("<tr><td>"+result.date+"</td><td>"+result.name+"</td><td>"+result.type+"</td>");
                });
                $modal.find(".loader").hide();
                $boxResult.removeClass('none');
				$modalFooter.removeClass('none');
				objModal.refreshPosition("modalHolydayImport");
            });
		}
	})
	
	$(document.getElementById('modalHolydayImport')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEnviarHolidayImport')),
			year2 = $("#nextyear").val(),
			fromyear = $("#lastyear").val();

		objDefault.buttonAction($btn,'disabled');
		$.post('holidays/import', {
			year2 : year2,
			fromyear : fromyear
		}, function(resposta) {
			if (resposta != false) {
				objDefault.notification("success",aLang['Import_successfull'].replace (/\"/g, ""),"modalHolydayImport");
				$("#flexigrid2").flexReload();
			} else {
				objDefault.notification("error",aLang['Import_failure'].replace (/\"/g, ""),"modalHolydayImport");
			}
		}).complete(function(){
			objDefault.buttonAction($btn,'enabled');
		});		
	})
	
	$(document.getElementById('modalHolydayDelete')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendHolidayDelete'));
		$.ajax({
			type: "POST",
			url: "holidays/delete",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalHolydayDelete");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalHolydayDelete");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalHolydayDelete");
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
    	var modalInsert = $(document.getElementById("modalHolydayInsert"));
        objDefault.maskLoaderShow();
        modalInsert.load("holidays/insertmodal", function(){
        	objDefault.init();
        	objModal.openModal("modalHolydayInsert");            	
        	$("#formHolidayInsert").validate({
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
			});
        	objDefault.maskLoaderHide();
        })
    }
}

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
        if(confirm(aLang['Delete'].replace (/\"/g, "") +" "+ $('.trSelected',grid).length +" "+ aLang['Items'].replace (/\"/g, "")+'?')){
            var items = $('.trSelected',grid);
            var itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3)+",";
            }
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "holidays/delete",
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
            var modalEdit = $(document.getElementById("modalHolydayEdit"));
            objDefault.maskLoaderShow();
            modalEdit.load("holidays/editmodal/id/"+itemlist, function(){
            	objDefault.init();
            	objModal.openModal("modalHolydayEdit");
            	$("#formHolidayEdit").validate({
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
function importlast(com,grid){
    if (access[4]=='N'){
            objModal.openModal("modalPermission");
        }
        else{
        	var modalImport = $(document.getElementById("modalHolydayImport"));
            objDefault.maskLoaderShow();
            modalImport.load("holidays/importmodal", function(){
            	objDefault.init();
            	objModal.openModal("modalHolydayImport");
            	$("#formHolidayImport").validate({
            		wrapper: "li class='error'",            		
            		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				    	nextyear: {
				      		required: true,
				      		notEqualTo:'#lastyear'
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
           
        	var items = $('.trSelected'),
        		id = items[0].id.substr(3),
            	modalDelete = $(document.getElementById("modalHolydayDelete"));
			objDefault.maskLoaderShow();
			modalDelete.load("holidays/deletemodal/id/"+id, function(){
				objModal.openModal("modalHolydayDelete");
				objDefault.maskLoaderHide();
			});
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}