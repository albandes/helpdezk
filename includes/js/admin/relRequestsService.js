$(function(){
	objDefault.init();
	
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
			url:'relRequestService/table_json/',
			success: function(data){
				

				if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="10">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
                    $.each(data.result, function(key, val) {
                    	$table.append('<tr><td>'+val.area+'</td><td>'+val.type+'</td><td>'+val.item+'</td><td>'+val.service+'</td><td class="valor">'+val.total+'</td></tr>');
                                           
                    });	
					
					$table.append('<tr><td><strong>Total</strong></td><td class="valor" colspan="4"><strong>'+ data.total+'</strong></td></tr>');
                    
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
    		if($(this).text()){
    			headerTab[i] = $(this).text();
    		}                		
    	})
        var fromdate = $("#fromdate").val(), 
        	todate = $("#todate").val(), 
        	txtSeparator = $(this).find(document.getElementById('txtSeparator')).val(),
        	outputtype = $(this).find('input:checked').val();
        window.open(path + "/app/reports/relRequestsService.php?outputtype=" + outputtype + "&delimiter=" + txtSeparator + "&fromdate=" + fromdate + "&todate=" + todate + "&headertab=" + headerTab, "_blank");
    	
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