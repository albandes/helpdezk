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
	})
	
	$(document.getElementById("cmbItem")).change(function(){
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
			url:'relRequests/table_json/',
			success: function(data){
				if(data.length == 0)
					$table.append('<tr class="noRegister"><td colspan="5">'+aLang['No_result'].replace (/\"/g, "")+'</td></tr>');
                else{
                    $.each(data, function(key, val) {
                    	if(val.login == null) val.login = "";
                    	if(val.repass)	valRepass =  aLang['Yes'].replace (/\"/g, "");
                    	else valRepass =  aLang['No'].replace (/\"/g, "");
                    		
                        $table.append('<tr><td>'+ val.code +'</td><td>'+ val.name +'</td><td>'+ val.company +'</td><td>'+ val.subject +'</td><td>'+ val.entry_date +'</td><td>'+ val.priority +'</td><td>'+ val.status +'</td><td>'+ valRepass+'</td></tr>');
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
        window.open(path + "/app/reports/relRequests.php?outputtype=" + outputtype + "&delimiter=" + txtSeparator + "&status=" +status+ "&cmbCompany=" +cmbCompany+ "&txtPriority=" +txtPriority+ "&txtowner=" +txtowner+ "&cmbType=" +cmbType+ "&cmbItem=" +cmbItem+ "&cmbService=" +cmbService+ "&fromdate=" + fromdate + "&todate=" + todate + "&headertab=" + headerTab, "_blank");
    	
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