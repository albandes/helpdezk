$(function(){
	objDefault.init();
	
	
	$(document.getElementById("cmbType")).change(function(){
		if(!this.value){
			$("#cmbItem").html("<option value=''>"+aLang['Alert_choose_type'].replace (/\"/g, "")+"</option>");
			$("#cmbService").html("<option value=''>"+aLang['Alert_choose_item'].replace (/\"/g, "")+"</option>");
			return false;
		}
		
		$.ajax({
			type: "POST",
			url: "relRequestsGroup/getItem/id/"+this.value,
			data: $(this).serialize(),
			success: function(ret) {
				$("#cmbItem").html(ret);
			},
			beforeSend: function(){
				$("#cmbItem").html("<option>"+aLang['Loading'].replace (/\"/g, "")+"</option>");
				$("#cmbService").html("<option value=''>"+aLang['Alert_choose_item'].replace (/\"/g, "")+"</option>");
			}
		});	
	})
	
	$(document.getElementById("cmbItem")).change(function(){
		if(!this.value){
			$("#cmbService").html("<option value=''>"+aLang['Alert_choose_item'].replace (/\"/g, "")+"</option>");
			return false;
		}
		$.ajax({
			type: "POST",
			url: "relRequestsGroup/getService/id/"+this.value,
			data: $(this).serialize(),
			success: function(ret) {
				$("#cmbService").html(ret);
			},
			beforeSend: function(){
				$("#cmbService").html("<option>"+aLang['Loading'].replace (/\"/g, "")+"</option>");
			}
		});	
	})
	
	
	$(document.getElementById("formSearch")).submit(function(){
    	var $boxRetorno = $(document.getElementById('boxRetorno')),
    		$loader		= $('.loader'),
    		$table 		= $('.tbReports').find('tbody'),
    		$btnSend	= $(this).find('input[type=submit]');
    		
    		$table.empty();
    	$.ajax({
			type: 'post',
			data: $(this).serialize(),
			dataType : "json",
			url:'relRequestsGroup/table_json/',
			success: function(data){
				
				
				if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="8">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
					$.each(data, function(key, val) {
	                	$table.append('<tr><td colspan="8"><strong>'+key+'</strong></td></tr>');
	                	total = 0;
	                	$.each(val.sol, function(key, sol) {
	                		
	                		if(val.length == 0)
								$table.append('<tr class="noRegister"><td colspan="8">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
			                else{
			                    
		                    	if(sol.repass == "0")	valRepass =  aLang['No'].replace (/\"/g, "");
		                    	else valRepass =  aLang['Yes'].replace (/\"/g, "");
		                    		
		                        $table.append('<tr><td>'+ sol.code +'</td><td>'+ sol.name +'</td><td>'+ sol.company +'</td><td>'+ sol.subject +'</td><td>'+ sol.entry_date +'</td><td>'+ sol.priority +'</td><td>'+ sol.status +'</td><td>'+ valRepass+'</td></tr>');
			                  
			                }
			                total++;
	                	})	
	                	
	                	if(val.total.new) $table.append('<tr><td colspan="7" align="right"><strong>'+val.total.new.name+'</strong></td><td colspan="1"><strong>'+val.total.new.sum+'</strong></td></tr>');
	                	if(val.total.repass) $table.append('<tr><td colspan="7" align="right"><strong>'+val.total.repass.name+'</strong></td><td colspan="1"><strong>'+val.total.repass.sum+'</strong></td></tr>');
	                	if(val.total.on_att) $table.append('<tr><td colspan="7" align="right"><strong>'+val.total.on_att.name+'</strong></td><td colspan="1"><strong>'+val.total.on_att.sum+'</strong></td></tr>');
	                	if(val.total.w_app) $table.append('<tr><td colspan="7" align="right"><strong>'+val.total.w_app.name+'</strong></td><td colspan="1"><strong>'+val.total.w_app.sum+'</strong></td></tr>');
	                	if(val.total.fins) $table.append('<tr><td colspan="7" align="right"><strong>'+val.total.fins.name+'</strong></td><td colspan="1"><strong>'+val.total.fins.sum+'</strong></td></tr>');
	                	if(val.total.rej) $table.append('<tr><td colspan="7" align="right"><strong>'+val.total.rej.name+'</strong></td><td colspan="1"><strong>'+val.total.rej.sum+'</strong></td></tr>');
	                	$table.append('<tr><td colspan="7" align="right"><strong>Total</strong></td><td colspan="1"><strong>'+total+'</strong></td></tr>');
	                	
	               });	
				}
			},
			beforeSend: function(){
				$loader.removeClass('none');
				$btnSend.attr("disabled","disabled");
				$boxRetorno.addClass('none');
			},
			complete: function(){
				$loader.addClass('none');
				$btnSend.removeAttr("disabled");
				$boxRetorno.removeClass('none');
				objDefault.zebrar(".tbReports");
			}
		});
    }); 
	
	$(document.getElementById('modalSalvar')).find('form').submit(function(){
    	var headerTab = new Array();
    	headerTab.length = 0;
    	$('.tbReports').find('th').each(function(i){
    		headerTab[i] = $(this).text();                		
    	})
        var fromdate = $("#fromdate").val(), 
        	todate = $("#todate").val(), 
        	status = $("#txtStatus").val(),
        	cmbCompany = $("#cmbCompany").val(),
        	txtPriority = $("#txtPriority").val(),
        	txtowner = $("#txtowner").val(),
        	cmbType = $("#cmbType").val(),
        	cmbItem = $("#cmbItem").val(),
        	cmbService = $("#cmbService").val(),
        	txtSeparator = $(this).find(document.getElementById('txtSeparator')).val(),
        	outputtype = $(this).find('input:checked').val();
        window.open(path + "/app/reports/relRequestsGroup.php?outputtype=" + outputtype + "&delimiter=" + txtSeparator + "&status=" +status+ "&cmbCompany=" +cmbCompany+ "&txtPriority=" +txtPriority+ "&txtowner=" +txtowner+ "&cmbType=" +cmbType+ "&cmbItem=" +cmbItem+ "&cmbService=" +cmbService+ "&fromdate=" + fromdate + "&todate=" + todate + "&headertab=" + headerTab, "_blank");
    	
    	objModal.closeModal();
    	return false;
    });
    
    $(document.getElementById('modalSalvar')).find('input[name=tpFile]').click(function(){
    	var $lstSep = $(document.getElementById('lstSeparator'));
    	if(this.value == "CSV")
    		$lstSep.slideDown();
    	else
    		$lstSep.slideUp();
    });  
})