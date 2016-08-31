$(function(){
	
	objDefault.init();
	
	$(".changeConfigStatus").click(function(){
		var val = this.value;
		if(this.checked){
			$.post('features/configActivate', {id : val}, function(response) {
                if (!response) {
                	alert("{/literal}{$smarty.config.Edit_failure}{literal}");
                }
            });
		}else{
			$.post('features/configDeactivate', {id : val}, function(response) {
                if (!response) {
                	alert("{/literal}{$smarty.config.Edit_failure}{literal}");
                }
            });
		}
	})
	
	$(".changeConfigValue").change(function(){
		var id = $(this).data("id"),
			val = this.value;		
		$.post('features/configChangeVal', {id : id, value: val}, function(response) {
			if (!response) {
				alert("{/literal}{$smarty.config.Edit_failure}{literal}");
			}
        });
	})
	
	$("#formEmailConfig").submit(function(){		
		var $self = $(this),
			mailtitle = $self.find("#mailtitle").val(),
        	mailhost = $self.find("#mailhost").val(),
        	maildomain = $self.find("#maildomain").val(),
        	mailuser = $self.find("#mailuser").val(),
        	mailpass = $self.find("#mailpass").val(),
        	mailsender = $self.find("#mailsender").val(),
        	mailport = $self.find("#mailport").val();
        
        if ($(this).find("#authcheck").is(":checked")) {
            var authcheck = '1';
        } else {
            var authcheck = '0';
        }
        var header2 = CKEDITOR.instances['header2'].getData(),
        	footer2 = CKEDITOR.instances['footer2'].getData();
       
       $.post('features/saveEmailChanges', {
            mailtitle : mailtitle,
            mailhost : mailhost,
            maildomain: maildomain,
            mailuser: mailuser,
            mailpass: mailpass,
            mailsender: mailsender,
            authcheck: authcheck,
            header2: header2,
            footer2: footer2,
            mailport: mailport
        }, function(response) {
            if(response){
				objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalInfo");
				objModal.openModal("modalInfo",1);
			}					
			else{
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalInfo");
				objModal.openModal("modalInfo");
			}
        });
	})
	
	$("#poptype").change(function(){
		var val = this.value,
			$popport = $(document.getElementById("popport"));
		switch(val)
		{
		case "GMAIL":
		  	$popport.val("993");
		  	break;
		case "POP":
			$popport.val("110");
		  	break;
		case "IMAP":
			$popport.val("143");
		  	break;
		default:
			$popport.val("");
			break;
		}		
	})
	
	$("#formPopServer").submit(function(){
		var $self 	= $(this),
			pophost = $self.find("#pophost").val(),
			popport = $self.find("#popport").val(),
			poptype = $self.find("#poptype").val(),
			popdomain = $self.find("#popdomain").val();
            
            $.post('features/savePopChanges', {
               pophost: pophost,
               popport: popport,
               poptype: poptype,
               popdomain: popdomain
            }, function(response) {
                if(response){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo",1);
				}					
				else{
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo");
				}
            });
	})
	
	$("#formLDAP").submit(function(){
		var $self 	= $(this);
           	
            $.post('features/saveLdapChanges', $self.serialize() , function(response) {
                if(response){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo",1);
				}					
				else{
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo");
				}
            });
	})
	
	$("#formMaintenance").submit(function(){
		var $self = $(this),
			mainmessage = CKEDITOR.instances['mainmessage'].getData();
			if($self.find("#checkMain").is(":checked")){
				checkMain = 1;	
			}else{
				checkMain = 0;
			}
			
			$.post('features/saveMaintenance', {
               checkMain: checkMain,
               mainmessage: mainmessage
            }, function(response) {
                if(response){
					objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo",1);
				}
				else{
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo");
				}
            });
	})
})