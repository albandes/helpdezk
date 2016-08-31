$(function(){
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
			url:'relPessoa/table_json/',
			success: function(data){
				if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="3">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
                    $.each(data, function(key, val) {
                    	if(val.login == null) val.login = "";
                        $table.append('<tr><td>'+ val.login +'</td><td>'+ val.name +'</td><td>'+ val.typeperson +'</td><td>'+ val.company +'</td></tr>');
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
    
    	var typeperson = $(document.getElementById('typeperson')).val(), 
        	outputtype = $(this).find('input:checked').val(),
        	txtSeparator = $(this).find(document.getElementById('txtSeparator')).val();
        window.open(path + "/app/reports/relPessoa.php?outputtype=" + outputtype + "&id=" + typeperson + "&delimiter=" + txtSeparator + "&headertab=" + headerTab, "_blank");
    	
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