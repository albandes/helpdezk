$(document).ready(function(){


	$(document.getElementById('modalServicesInsertArea')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendAreaInsert'));
		$.ajax({
			type: "POST",
			url: "services/areaInsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertArea");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalServicesInsertArea");
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertArea");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
    
    $(document.getElementById('modalServicesEditArea')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendAreaEdit'));
		$.ajax({
			type: "POST",
			url: "services/editarea",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditArea");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalServicesEditArea");
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditArea");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
    
	$(document.getElementById('modalServicesInsertType')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendTypeInsert'));
		$.ajax({
			type: "POST",
			url: "services/typeInsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertType");
			},
			success: function(ret) {
				if(ret){
					objServices.getInitList();
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalServicesInsertType");
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertType");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalServicesEditType')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendTypeEdit'));
		$.ajax({
			type: "POST",
			url: "services/edittype",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditType");
			},
			success: function(ret) {
				if(ret){
					objServices.getInitList();
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalServicesEditType");
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditType");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalServicesInsertItem')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendItemInsert'));
		$.ajax({
			type: "POST",
			url: "services/iteminsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertItem");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalServicesInsertItem");
					objServices.showItems2($(document.getElementById("idtype2")).val());
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertItem");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalServicesEditItem')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendItemEdit'));
		$.ajax({
			type: "POST",
			url: "services/edititem",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditItem");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalServicesEditItem");
					objServices.showItems2(ret);
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditItem");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalServicesInsertService')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendServiceInsert'));
		$.ajax({
			type: "POST",
			url: "services/serviceInsert",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertService");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalServicesInsertService");
					objServices.showServices2($(document.getElementById("iditem2")).val());
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalServicesInsertService");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalServicesEditService')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendServiceEdit'));
		$.ajax({
			type: "POST",
			url: "services/editservice",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditService");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalServicesEditService");
					objServices.showServices2(ret);
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalServicesEditService");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalConfApproval')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendConfApproval')),
			cmbType = document.getElementById("cmbType").value,
			cmbItem = document.getElementById("cmbItem").value,
			cmbService = document.getElementById("cmbService").value,
			txtRecalc = document.getElementById("txtRecalc"),
			$cmbApprove = $(document.getElementById("cmbApprove")),
			$cmbApproveOpt = $cmbApprove.find("option"),
			$cmbApproveOptLength = $cmbApproveOpt.length-1,
			appIds = "";			
		
		
		if($cmbApproveOpt.length > 0){
			$cmbApproveOpt.each(function(i){
				if($cmbApproveOptLength == i)
					appIds += this.value;
				else
					appIds += this.value +",";
			})
		}else{
			appIds = 0;
		}
		
		if(txtRecalc.checked) txtRecalc = 1;
		else txtRecalc = 0;
			
		objDefault.buttonAction($btn,'disabled');
		
		$.post('services/modalConfApprovalSave', {
            cmbType : cmbType,
            cmbItem : cmbItem,
            cmbService : cmbService,
            cmbAval : appIds,
            txtRecalc: txtRecalc
        }, function(response) {
            if(response)
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalConfApproval");			
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalConfApproval");
        }).complete(function(){
        	objDefault.buttonAction($btn,'enabled');
        });
			
	});
	
});//init		 

var objServices = {
		getInitList: function(){
	        $("#type").load("services/getInitList");
		},
		loadInsertArea: function(){
			var modalInsert = $(document.getElementById("modalServicesInsertArea"));
			
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/modalArea", function(){
	        	objDefault.init();
	        		        	
	        	$("#formAreaInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		areaName: {
				  			required: true
				  		}
				 	}
				});
	        	
	        	objModal.openModal("modalServicesInsertArea");
	        	objDefault.maskLoaderHide();
	        })
		},
		loadEditArea: function(){
			var modalInsert = $(document.getElementById("modalServicesEditArea")),
				id = $(this).attr('href');
			
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/areaEdit/id/"+id, function(){
	        	objDefault.init();
	        		        	
	        	$("#formAreaEdit").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		areaName: {
				  			required: true
				  		}
				 	}
				});
	        	
	        	objModal.openModal("modalServicesEditArea");
	        	objDefault.maskLoaderHide();
	        })
	        return false;
		},
		changeAreaStatus: function(){
			var id = this.value;			
			if(this.checked)
				var check = "A";
			else
				var check = "N";
				
			$.post('services/areaChangeStatus', {
                id : id,
                check : check
            }, function(response) {

                if (response != false) {
					objServices.getInitList();
                } else {
                    alert("{/literal}{$smarty.config.Permission_error}{literal}");
                }
            });
		},
		changeTypeStatus: function(){
			var id = this.value;			
			if(this.checked)
				var check = "A";
			else
				var check = "N";
				
			$.post('services/typeChangeStatus', {
                id : id,
                check : check
            }, function(response) {

                if (response != false) {
					
                } else {
                    alert("{/literal}{$smarty.config.Permission_error}{literal}");
                }
            });
		},
		changeItemStatus: function(){
			var id = this.value;			
			if(this.checked)
				var check = "A";
			else
				var check = "N";
				
			$.post('services/itemChangeStatus', {
                id : id,
                check : check
            }, function(response) {

                if (response != false) {
					
                } else {
                    alert("{/literal}{$smarty.config.Permission_error}{literal}");
                }
            });
		},
		changeServiceStatus: function(){
			var id = this.value;			
			if(this.checked)
				var check = "A";
			else
				var check = "N";
				
			$.post('services/serviceChangeStatus', {
                id : id,
                check : check
            }, function(response) {

                if (response != false) {
					
                } else {
                    alert("{/literal}{$smarty.config.Permission_error}{literal}");
                }
            });
		},
		loadInsertType: function(){
			var modalInsert = $(document.getElementById("modalServicesInsertType"));
			
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/modalType", function(){	        		        	
	        	$("#formTypeInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		typeName: {
				  			required: true
				  		},
				  		areaType: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalServicesInsertType");
	        	objDefault.maskLoaderHide();
	        })
		},
		loadEditType: function(){
			var modalInsert = $(document.getElementById("modalServicesEditType"));
			var id = $(this).attr('href');
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/typeEdit/id/"+id, function(){	        		        	
	        	$("#formTypeEdit").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		typeName: {
				  			required: true
				  		},
				  		areaType: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalServicesEditType");
	        	objDefault.maskLoaderHide();
	        })
	        return false;
		},
		loadInsertItem: function(){
			var modalInsert = $(document.getElementById("modalServicesInsertItem")),
				id = $(document.getElementById("idtype2")).val();
			
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/modalItem/id/"+id, function(){	        		        	
	        	$("#formItemInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		itemName: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalServicesInsertItem");
	        	objDefault.maskLoaderHide();
	        })
		},
		loadEditItem: function(){
			var modalInsert = $(document.getElementById("modalServicesEditItem"));
			var id = $(this).attr('href');
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/itemEdit/id/"+id, function(){	        		        	
	        	$("#formItemEdit").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		itemName: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalServicesEditItem");
	        	objDefault.maskLoaderHide();
	        })
	        return false;
		},
		loadInsertService: function(){
			var modalInsert = $(document.getElementById("modalServicesInsertService")),
				id = $(document.getElementById("iditem2")).val();
			
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/modalService/id/"+id, function(){	        		        	
	        	$("#formServiceInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		serviceName: {
				  			required: true
				  		},
				  		serviceGroup: {
				  			required: true
				  		},
				  		servicePriority: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalServicesInsertService");
	        	objDefault.maskLoaderHide();
	        })
		},
		loadEditService: function(){
			var modalInsert = $(document.getElementById("modalServicesEditService")),
				id = $(this).attr('href');
			
	        objDefault.maskLoaderShow();
	        modalInsert.load("services/serviceEdit/id/"+id, function(){	        		        	
	        	$("#formServiceEdit").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		serviceName: {
				  			required: true
				  		},
				  		serviceGroup: {
				  			required: true
				  		},
				  		servicePriority: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalServicesEditService");
	        	objDefault.maskLoaderHide();
	        })
	        return false;
		},
		showItems: function(){
	        objDefault.maskLoaderShow();
	       	$("#services").hide();
	       	$("#items").slideUp();
           	var id = $(this).attr('href');
            
			$.post('services/items', {
			    id : id
			}, function(resposta) {			    
			    if (resposta != false) {
			        $("#items").html(resposta);
			        $("#items").slideDown();
			    } else {
			        $("#items").html("{/literal}{$smarty.config.Alert_no_records}{literal}");
			    }
			}).complete(function(){
				objDefault.maskLoaderHide();
				$("#mask").hide();
			});
	       return false;	       
		},
		showItems2: function(id){
			var $items = $(document.getElementById('items'));
	       	$.post('services/items', {
			    id : id
			}, function(resposta) {			    
			    if (resposta != false) {
			        $items.html(resposta);
			        $items.slideDown();
			    }
			});	       
		},
		showServices: function(){
	        objDefault.maskLoaderShow();
	       	$("#services").slideUp();
           	var id = $(this).attr('href');
			$.post('services/servicebox', {
			    id2 : id
			}, function(resposta) {			    
			    if (resposta != false) {
			        $("#services").html(resposta);
			        $("#services").slideDown();
			    } else {
			        $("#services").html("{/literal}{$smarty.config.Alert_no_records}{literal}");
			    }
			}).complete(function(){
				objDefault.maskLoaderHide();
				$("#mask").hide();
			});
	       return false;       
		},
		showServices2: function(id){
			$.post('services/servicebox', {
			    id2 : id
			}, function(resposta) {			    
			    if (resposta != false) {
			        $("#services").html(resposta);
			    } else {
			        $("#services").html("{/literal}{$smarty.config.Alert_no_records}{literal}");
			    }
			})       
		},
		loadConfApproval: function(){
			var modalApproval = $(document.getElementById("modalConfApproval"));
			
	        objDefault.maskLoaderShow();
	        modalApproval.load("services/modalConfApproval", function(){	        		        	
	        	$("#formConfApproval").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parents('.field').parent());
					},
				  	rules: {
				  		cmbType: {
				  			required: true
				  		},
				  		cmbItem: {
				  			required: true
				  		},
				  		cmbService: {
				  			required: true
				  		}
				 	}
				});
	        	objModal.openModal("modalConfApproval");
	        	objDefault.maskLoaderHide();
	        })
		},
		changecmbType: function(){
			var $cmbUserApp = $(document.getElementById("cmbApprove")),
        		$cmbUser = $(document.getElementById("cmbUsers"));
        	$cmbUserApp.empty();
        	$cmbUser.empty();
			if(!this.value){
				$("#cmbItem").html("<option value=''>"+aLang['Alert_choose_type'].replace (/\"/g, "")+"</option>");
				$("#cmbService").html("<option value=''>"+aLang['Alert_choose_item'].replace (/\"/g, "")+"</option>");
				return false;
			}
			
			$.ajax({
				type: "POST",
				url: "relRequests/getItem/id/"+this.value,
				data: $(this).serialize(),
				success: function(ret) {
					$("#cmbItem").html(ret);
				},
				beforeSend: function(){
					$("#cmbItem").html("<option>"+aLang['Loading'].replace (/\"/g, "")+"</option>");
					$("#cmbService").html("<option value=''>"+aLang['Alert_choose_item'].replace (/\"/g, "")+"</option>");
				}
			});	
		},
		changecmbItem: function(){
			var $cmbUserApp = $(document.getElementById("cmbApprove")),
        		$cmbUser = $(document.getElementById("cmbUsers"));
        	$cmbUserApp.empty();
        	$cmbUser.empty();
			if(!this.value){
				$("#cmbService").html("<option value=''>"+aLang['Alert_choose_item'].replace (/\"/g, "")+"</option>");
				return false;
			}
			$.ajax({
				type: "POST",
				url: "relRequests/getService/id/"+this.value,
				data: $(this).serialize(),
				success: function(ret) {
					$("#cmbService").html(ret);
				},
				beforeSend: function(){
					$("#cmbService").html("<option>"+aLang['Loading'].replace (/\"/g, "")+"</option>");
				}
			});	
		},
		changecmbService: function(){
			var iditem = document.getElementById("cmbItem").value,
				idservice = this.value,
				$cmbUserApp = $(document.getElementById("cmbApprove")),
        		$cmbUser = $(document.getElementById("cmbUsers")),
        		$txtRecal = $(document.getElementById("txtRecalc")),
        		recal = 0;
        		
				$cmbUser.html("<option>"+aLang['Loading'].replace (/\"/g, "")+"</option>");
			$.getJSON("services/getUsersApprove/iditem/"+iditem+"/idservice/"+idservice,
		        function(data){		            
		            $cmbUserApp.empty();
		            $cmbUser.empty();
		            	
		            if(data.resul.user){		            	
		            	$.each(data.resul.user, function(i,result){
			               $cmbUser.append("<option value='"+result.id+"'>"+result.name+"</option>");
			            });	
		            }
		            if(data.resul.uapp){		            	
		            	$.each(data.resul.uapp, function(i,result){
			               $cmbUserApp.append("<option value='"+result.id+"'>"+result.name+"</option>");
			               recal = result.recal;
			            });
		            }
		            
		            if(recal == 1){ $txtRecal.attr("checked","checked"); recal = 0;}
		            else $txtRecal.removeAttr("checked","");
		            
		        },
		    "json");
		},
		arrowLeft: function(){
			var $selected = $("#cmbApprove option:selected");
			$selected.clone().appendTo('#cmbUsers');
			$selected.remove();
		},
		arrowRight: function(){
			var $selected = $("#cmbUsers option:selected");
			$selected.clone().appendTo('#cmbApprove');
			$selected.remove();			  
		},
		arrowUp: function(){
			 $('#cmbApprove option:selected').each( function() {
	            var newPos = $('#cmbApprove option').index(this) - 1;
	            if (newPos > -1) {
	                $('#cmbApprove option').eq(newPos).before("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
	                $(this).remove();
	            }
	        });
		},
		arrowDown: function(){
			var countOptions = $('#cmbApprove option').size();
	        $('#cmbApprove option:selected').each( function() {
	            var newPos = $('#cmbApprove option').index(this) + 1;
	            if (newPos < countOptions) {
	                $('#cmbApprove option').eq(newPos).after("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
	                $(this).remove();
	            }
	        });
		}
	}
	

$("#content")
	.off(".contentloaded")
	.on("click.contentloaded", "#insertArea", objServices.loadInsertArea)
	.on("click.contentloaded", "#lstAreas a", objServices.loadEditArea)	
	.on("click.contentloaded", "#lstAreas input[type=checkbox]", objServices.changeAreaStatus)
	.on("click.contentloaded", "#insertType", objServices.loadInsertType)
	.on("click.contentloaded", "#lista .btnEdit-tp1", objServices.loadEditType)
	.on("click.contentloaded", "#addItem", objServices.loadInsertItem)
	.on("click.contentloaded", "#addService", objServices.loadInsertService)
	.on("click.contentloaded", "#lista .btnSearch-tp1", objServices.showItems)
	.on("click.contentloaded", "#lista .checkArea", objServices.changeAreaStatus)
	.on("click.contentloaded", "#lista .checkType", objServices.changeTypeStatus)
	.on("click.contentloaded", "#lista1 .checkStatus", objServices.changeItemStatus)
	.on("click.contentloaded", "#lista1 .btnSearch-tp1", objServices.showServices)
	.on("click.contentloaded", "#lista1 .btnEdit-tp1", objServices.loadEditItem)
	.on("click.contentloaded", "#lista2 .serviceCheck", objServices.changeServiceStatus)
	.on("click.contentloaded", "#lista2 .btnEdit-tp1", objServices.loadEditService)
	.on("click.contentloaded", "#insertConfApproval", objServices.loadConfApproval)
	.on("change.contentloaded", "#cmbType", objServices.changecmbType)
	.on("change.contentloaded", "#cmbItem", objServices.changecmbItem)
	.on("change.contentloaded", "#cmbService", objServices.changecmbService)	
	.on("click.contentloaded", "#arrowLeft", objServices.arrowLeft)
	.on("click.contentloaded", "#arrowRight", objServices.arrowRight)
	.on("click.contentloaded", "#arrowUp", objServices.arrowUp)
	.on("click.contentloaded", "#arrowDown", objServices.arrowDown);
	
	
