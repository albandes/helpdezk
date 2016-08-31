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
			url:'relUserSatisfaction/table_json/',
			success: function(data){
				if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="5">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
                    $.each(data, function(key, val) {                    		
                        $table.append('<tr><td>'+ val.evaluation +'</td><td class="valor">'+ val.requests +'</td><td class="valor">'+ val.percent +'</td></tr>');
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
        	cmbCompany = $("#cmbCompany").val(),
        	cmbPerson = $("#cmbPerson").val(),
        	txtSeparator = $(this).find(document.getElementById('txtSeparator')).val(),
        	outputtype = $(this).find('input:checked').val();
        window.open(path + "/app/reports/relUserSatisfaction.php?outputtype=" + outputtype + "&delimiter=" + txtSeparator + "&cmbCompany=" +cmbCompany+ "&cmbPerson=" +cmbPerson+ "&fromdate=" + fromdate + "&todate=" + todate + "&headertab=" + headerTab, "_blank");
    	
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