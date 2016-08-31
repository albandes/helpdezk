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
			url:'relOperator/table_json/',
			success: function(data){
				console.log(data)

				if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="10">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
                    $.each(data.result, function(key, val) {
                    	$table.append('<tr><td colspan="10"><strong>'+key+' ('+val.company+')</strong></td></tr>');   
                       
                       	$.each(val.user, function(key, val) {
                       		$table.append('<tr><td>'+ val.name +'</td><td class="valor">'+ val.new +'</td><td class="valor">'+ val.repassed +'</td><td class="valor">'+ val.on_attendance +'</td><td class="valor">'+ val.finish +'</td><td class="valor"><strong>'+ val.total_req +'</strong></td><td class="valor">'+ val.normal +'</td><td class="valor">'+ val.extra +'</td><td class="valor">'+ val.tel +'</td><td class="valor"><strong>'+ val.total_hour +'</strong></td></tr>');                       	
                       	});
                       	
                       	$table.append('<tr><td colspan="6" class="valor"><strong>'+ val.total.total_all_req +'</strong></td><td colspan="4" class="valor"><strong>'+ val.total.total_all_hour +'</strong></td></tr>');
                        //$table.append('<tr><td>'+ val.name +'</td><td>'+ val.company +'</td><td class="valor">'+ val.new +'</td><td class="valor">'+ val.on_attendance +'</td><td class="valor">'+ val.finish +'</td><td class="valor"><strong>'+ val.total_req +'</strong></td><td class="valor">'+ val.normal +'</td><td class="valor">'+ val.extra +'</td><td class="valor">'+ val.tel +'</td><td class="valor"><strong>'+ val.total_hour +'</strong></td></tr>');
                    
                    });	
					
					$table.append('<tr><td><strong>Total</strong></td><td colspan="5" class="valor"><strong>'+ data.total_all.total_all_req +'</strong></td><td colspan="4" class="valor"><strong>'+ data.total_all.total_all_hour +'</strong></td></tr>');
					
					
					//$table.append('<tr><td colspan="6" class="valor"><strong>'+ data.total_all.total_all_req +'</strong></td><td colspan="4" class="valor"><strong>'+ data.total_all.total_all_hour +'</strong></td></tr>');                    
                }

				/*if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="9">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
                    $.each(data.result, function(key, val) {
                        $table.append('<tr><td>'+ val.name +'</td><td>'+ val.company +'</td><td class="valor">'+ val.new +'</td><td class="valor">'+ val.on_attendance +'</td><td class="valor">'+ val.finish +'</td><td class="valor"><strong>'+ val.total_req +'</strong></td><td class="valor">'+ val.normal +'</td><td class="valor">'+ val.extra +'</td><td class="valor">'+ val.tel +'</td><td class="valor"><strong>'+ val.total_hour +'</strong></td></tr>');
                    });	
					$table.append('<tr><td colspan="6" class="valor"><strong>'+ data.total_all.total_all_req +'</strong></td><td colspan="4" class="valor"><strong>'+ data.total_all.total_all_hour +'</strong></td></tr>');                    
                }*/
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
        window.open(path + "/app/reports/relOperator.php?outputtype=" + outputtype + "&delimiter=" + txtSeparator + "&fromdate=" + fromdate + "&todate=" + todate + "&headertab=" + headerTab, "_blank");
    	
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