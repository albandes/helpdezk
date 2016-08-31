$(document).ready(function(){
		
    $("#flexigrid2").flexigrid({
        url: 'person/json/',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        colModel : [
        {
            display: '', 
            name : 'idperson', 
            width : 15, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'name', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['login'].replace (/\"/g, ""), 
            name : 'login', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['email'].replace (/\"/g, ""), 
            name : 'email', 
            width : 150, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['type_person'].replace (/\"/g, ""), 
            name : 'typeperson', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },
        

        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'company', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },
        

        {
            display: aLang['Department'].replace (/\"/g, ""), 
            name : 'department', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['status'].replace (/\"/g, ""), 
            name : 'status', 
            width : 35, 
            sortable : true, 
            align: 'center'
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
            name: aLang['Deactivate'].replace (/\"/g, ""), 
            bclass: 'encerra', 
            onpress: disable
        },

        {
            separator:true
        },

        {
            name: aLang['Activate'].replace (/\"/g, ""), 
            bclass: 'activate', 
            onpress: enable
        },
        
        {
            separator:true
        },
        
        {
            name: aLang['permissions'].replace (/\"/g, ""),  
            bclass: 'permission', 
            onpress: permission
        },
        
        {
            separator:true
        },
        
        {
            name: aLang['View_groups'].replace (/\"/g, ""), 
            bclass: 'attendant', 
            onpress: viewGroups
        }
        ],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""), 
            name : 'tbp.name', 
            isdefault: true
        },

        {
            display: aLang['login'].replace (/\"/g, ""), 
            name : 'tbp.login'
        },
	

        {
            display: aLang['email'].replace (/\"/g, ""), 
            name : 'tbp.email'
        },
        

        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'comp.name'
        },
        

        {
            display: aLang['Department'].replace (/\"/g, ""), 
            name : 'dep.name'
        }
        ],
                                        
                                        
        sortname: "tbp.name",
        sortorder: "asc",
        usepager: true,
        title: ' :: '+aLang['people'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: "auto",
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect : true					
    }); 
			 
    function mostra2(com, grid) {
        if (com == 'Jurídicas') {
            somapar2('N');
        } else if (com == 'Fisicas') {
            somapar2('S');
        }else if (com == 'Todos') {
            somapar2('A');
        }
    }
    
});//init
		
function novo() {
    if (access[1]=='N'){
        objModal.openModal("modalPermission");
    }
    else{        
        var modalInsert = $(document.getElementById("modalPersonInsert"));
        objDefault.maskLoaderShow();
        modalInsert.load("person/personinsert", function(){
        	objDefault.init();
        	objModal.openModal("modalPersonInsert");
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
            var modalEdit = $(document.getElementById("modalPersonEdit"));
            objDefault.maskLoaderShow();
            modalEdit.load("person/editform/id/"+itemlist, function(){
            	objDefault.init();

                $("#formPersonEdit").validate({
                    wrapper: "li class='error'",                    
                    errorPlacement: function(error, element) {
                        error.appendTo(element.parent().parent());
                    },
                    rules: {
                        namejuridical: {
                            required: true
                        },
                        type_company: {
                            required: true
                        },
                        email: {
                            required: true
                        },
                        department: {
                            required: true
                        },
                        company: {
                            required: true
                        },
                        type_user: {
                            required: true
                        },
                        login: {
                            required: true
                        },
                        namenatural: {
                            required: true
                        },
                        time_value: {
                            number: true
                        },
                        overtime: {
                            number: true
                        }
                    }
                });

            	objModal.openModal("modalPersonEdit");
            	objDefault.maskLoaderHide();
            })            
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}

function disable(com, grid){
    if (access[2]=='N'){
        objModal.openModal("modalPermission");
    }
    else{
        if($('.trSelected',grid).length>0){
        	var items = $('.trSelected',grid),
            	itemlist ='';
            for(i=0;i<items.length;i++){
                itemlist+= items[i].id.substr(3);
            }
        	var modalDisable = $(document.getElementById("modalPersonDisable"));
            objDefault.maskLoaderShow();
            modalDisable.load("person/deactivatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalPersonDisable");
            	objDefault.maskLoaderHide();
            })
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}
function enable(com, grid){
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
            var modalActive = $(document.getElementById("modalPersonActive"));
            objDefault.maskLoaderShow();
            modalActive.load("person/activatemodal/id/"+itemlist, function(){
            	objModal.openModal("modalPersonActive");
            	objDefault.maskLoaderHide();
            })
        	
        } else {
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}

function permission(com,grid){
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
            $("#content").load('person/manageperm/id/'+itemlist);
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}

function viewGroups(com,grid){	
	
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
            var modalAttendantGroup = $(document.getElementById("modalAttendantGroup"));
		    objDefault.maskLoaderShow();
		    modalAttendantGroup.load("person/modalAttendantGroup/id/"+itemlist, function(data){
		    	if(data == "error"){
		    		objDefault.notification("error",aLang['Option_only_operator'].replace (/\"/g, ""),"modalInfo");
	    			objModal.openModal("modalInfo");
		    	}else{
		    		objModal.openModal("modalAttendantGroup");	
		    	}		    	
		    	objDefault.maskLoaderHide();
		    });          
        }
        else{
            objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
	    	objModal.openModal("modalInfo");
        }
    }
}


var objPerson = {
		
		changeCategory: function(){
			var val = this.value,
				$modal = $(document.getElementById(objModal.getActive())),
				$loader = $modal.find(".loader"),
				$boxRetorno = $modal.find(".boxRetorno"),
				$boxFooter = $modal.find(".modalFooter"),
				$btnSend = $(document.getElementById(objModal.getActive())).find(".modalFooter").find("input[type=submit]");
			
			$boxRetorno.empty();
			$boxFooter.addClass("none");
			$loader.show();				
	        $boxRetorno.load("person/"+val, function(){
	        	objDefault.init();
	        	objModal.refreshPosition(objModal.getActive());
	        	$boxFooter.removeClass("none");
	        	$loader.hide();
	        	$btnSend.removeAttr("disabled");
                //

        		$("#formPersonInsert").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		namejuridical: {
				  			required: true
				  		},
				  		type_company: {
				  			required: true
				  		},
				  		password: {
				  			required: true
				  		},
					    cpassword: {
					      equalTo: "#password"
					    },
				    	email: {
				      		required: true
				    	},
				    	department: {
				      		required: true
				    	},
				    	company: {
				      		required: true
				    	},
				    	type_user: {
				      		required: true
				    	},
				    	login: {
				      		required: true
				    	},
				    	namenatural: {
				      		required: true
				    	},
                        time_value: {
                            number: true
                        },
                        overtime: {
                            number: true
                        }
				 	}
				});

				//
	        });
		},
		showAddress: function(){
			$(document.getElementById(objModal.getActive())).find('#'+objPerson.getCategory()).find('.formAdress').toggle();
			$("#address").autocomplete(ruas);
		},
		getCategory: function(){
			var modalId = objModal.getActive();
			return $(document.getElementById(modalId)).find("input[name=txtCategory]:checked").val();
		},
		changeCountry: function(){
			var $self = $(this),
        		val = this.value,
        		$state = $(document.getElementById("state")).parents('li'),
        		$city =  $(document.getElementById("city")).parents('li');
        	if(val == 1){
        		$state.slideUp();
        		$city.slideUp();
        	}
        	else{
	        	$(document.getElementById("state")).html('<option value="0">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
		        $state.slideDown();
		        $city.slideUp();
		        $.post("person/state", {
		            country : val
		        }, function(valor) {
		            $("select[name=state]").html(valor);
		            
		        })
        	}
		},
		changeState: function(){
			var $self = $(this),
        		val = this.value,
        		$city =  $(document.getElementById("city")).parents('li');
        	if(val == 1){
        		$city.slideUp();
        	}
        	else{
        		$city.slideDown();
	        	$(document.getElementById("city")).html('<option value="0">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
		        $.post("person/city", {
		            state : val
		        }, function(valor) {
		            $("select[name=city]").html(valor);
		        })
        	}
		},

		change: function(val, state){
	        $("select[name=state]").html('<option value="0">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
	        var val = $("#country").val();
	        $.post("person/state", {
	            country : val,
	            state: state
	        }, function(valor) {
	        	var $city =  $(document.getElementById("city")).parents('li');
	        	
	            $("select[name=state]").html(valor);
	            $city.slideDown();
	            $city.find("select").html("<option value='0'>"+aLang['No_result'].replace (/\"/g, "")+"</option>");
	            
	        })
		},
		returncities: function(val){
			$("select[name=city]").html('<option value="0">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
	        $.post("person/city", {
	            state : $('#state').val(),
	            city: val
	        }, function(valor) {
	            $("select[name=city]").html(valor);
	        })
		},
		changeCity: function(){
    		$.getJSON("person/neighborhoods/idcity/"+this.value, function(data) {  
            	neighborhoods = [];
                idneighborhoods = [];
                i=0;
                
                $.each(data, function(key, val) {
                    neighborhoods[i] = val.name;
                    idneighborhoods[i] = val.id;
                    i = i + 1;
                });	                    
                $("#neighborhood").autocomplete(neighborhoods);           
            });
        },
        cleanDepartment: function(){
        	
        	if(this.value == aLang['Default_department_value'].replace (/\"/g, "")){
                $(this).val('');
            }else if(!this.value){
            	$(this).val(aLang['Default_department_value'].replace (/\"/g, ""));
            }
        },
        sendForm: function(){
        	if(objPerson.getCategory() == "formnatural"){
        		sendNatural($(this));
        	}
        	else if(objPerson.getCategory() == "formjuridical"){
        		sendJuridical($(this));
        	}
        	return false;
        },
        sendFormEdit: function(){
        	if($(this).hasClass('juridical'))
				sendJuridicalEdit($(this));
			else
				sendNaturalEdit($(this));
        },
        checkLogin: function(){
        	var $self = $(this),
        		login = this.value,
        		$loader = $self.next(),
        		$error = $self.parent().parent(),
        		$btnSend = $(document.getElementById(objModal.getActive())).find(".modalFooter").find("input[type=submit]");
        	if(login.length > 0){
	        	$loader.removeClass("none_i");
	        	$error.find(".error").remove();
	        	$btnSend.attr("disabled","disabled");
	            $.post('person/checklogin', {
	                login : login
	            }, function(resposta) {
	            	console.log(resposta);
	                if(resposta == false){
	                	$error.append("<li class='error'>"+aLang['Login_exists'].replace (/\"/g, "")+"</li>");
	                }
	                else{
	                	$btnSend.removeAttr("disabled");
	                    $error.find(".error").remove();
	                }
	            }).complete(function(){
	            	$loader.addClass("none_i");
	            });
            }
        },
        changeCompany: function(){
        	var val = this.value;
        		$departament = $(document.getElementById(objModal.getActive())).find("select[name=department]");
        	
        	$departament.html('<option value="0">'+aLang['Loading'].replace (/\"/g, "")+'</option>');
            $.post("person/department", {
                company : val
            }, function(valor) {
                $departament.html(valor);
            })
        },
        changeTypeUser: function(){
        	var val = this.value;
        	if(val == 1 ){
				$('#acess_level_user').hide();
				$('#acess_level_operator').show();
                $('#select_group_permission').hide();
        	} else if (val == 2) {
				$('#acess_level_operator').hide();
				$('#acess_level_user').show();
                $('#select_group_permission').show();
			} else if (val == 3){
                $('#acess_level_user').hide();
                $('#acess_level_operator').show();
                $('#select_group_permission').show();
            } else{
				$('#acess_level_operator').hide();
				$('#acess_level_user').hide();
			} 
        },
        changePassword: function(){
        	
        	
            var modalActive = $(document.getElementById(objModal.getActive())),
            	id = modalActive.find("#id").val(),
            	modalPassword = $(document.getElementById("modalAlterPassword"));
            
            objDefault.maskLoaderShow();
         
            modalPassword.load("person/modalPassword/id/"+id, function(){
            	$("#formAlterPassword").validate({
	        		wrapper: "li class='error'",            		
	        		errorPlacement: function(error, element) {
						error.appendTo(element.parent().parent());
					},
				  	rules: {
				  		password: {
				  			required: true
				  		},
					    cpassword: {
					      equalTo: "#password"
					    }
				 	}
				});
            	
            	objModal.openModal("modalAlterPassword");
            	objDefault.maskLoaderHide();
            })
        	
        	
        }
	}




function sendJuridical(self){
    pathform = document.form1;
    var namejuridical = self.find("#namejuridical").val();
    var email = self.find("#email").val();
    var department = self.find("#department").val();
    var branch = self.find("#branch").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var city = self.find("#city").val();
    var priority = self.find("#priority").val();
    var typeuser = self.find("#type_company").val();
    var neighborhood = self.find("#neighborhood").val();
    if (neighborhood == ''){
        neighborhood = 'Choose';
    } 
    if(default_lang == 'en_US'){
        var clear = '999999999';
    }
    else{
        var clear = '99999999999999';
    }
    var zipcode = self.find("#zipcode").val();
    var typestreet = self.find("#type_street").val();
    var address = self.find("#address").val();
    if (address == ''){
        address = 'Choose';
    } 
    var number = self.find("#number").val();
    var complement = self.find("#complement").val();
    if (self.find("#filladress").is(":checked")) {
        var filladress = 'Y';
    } else {
        var filladress = 'N';
    }
    
    self.find("#phone").unmask();
    self.find("#phone").mask("9999999999?99");
    var phone = self.find("#phone").val();
    self.find("#fax").unmask();
    self.find("#fax").mask("9999999999?99");                
    var fax = self.find("#fax").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    self.find("#cnpj").unmask();
    self.find("#cnpj").mask(clear);
    var cnpj = self.find("#cnpj").val();
    var observation = self.find("#obs").val();
    var contact = self.find("#cperson").val();
    $btn = self.find(document.getElementById('btnSendPersonInsert'));
    objDefault.buttonAction($btn,'disabled');
    $.post("person/insertJuridical", {
        name: namejuridical,
        email: email,
        department: department,
        phone: phone,
        priority: priority,
        branch: branch,
        fax: fax,
        country: country,
        state: state,
        cnpj: cnpj,
        country: country,
        city: city,
        neighborhood: neighborhood,
        zipcode: zipcode,
        typestreet: typestreet,
        address: address,
        number: number,
        complement: complement,
        filladdress: filladress,
        typeuser: typeuser,
        observation: observation,
        contact: contact
    }, function(resp) {
        if (resp != false) {
            objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalPersonInsert");
            $("#flexigrid2").flexReload();
        } else {
            objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonInsert");
        }
    }).complete(function(){
    	objDefault.buttonAction($btn,'enabled');
    });
}


function sendJuridicalEdit(self){
    var namejuridical = self.find("#namejuridical").val();
    var email = self.find("#email").val();
    var department = self.find("#department").val();
    var branch = self.find("#branch").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var city = self.find("#city").val();
    var priority = self.find("#priority").val();
    var typeuser = self.find("#type_company").val();
    var contact = self.find("#cperson").val();
    var neighborhood = self.find("#neighborhood").val();
    if (neighborhood == ''){
        neighborhood = 'Choose';
    } 
    if(default_lang == 'en_US'){
        var clear = '999999999';
    }
    else{
        var clear = '99999999999999';
    }
    var zipcode = self.find("#zipcode").val();
    var typestreet = self.find("#type_street").val();
    var address = self.find("#address").val();
    if (address == ''){
        address = 'Choose';
    } 
    var number = self.find("#number").val();
    var complement = self.find("#complement").val();
   
    
    if (self.find("#filladress").is(":checked")) {
        var filladress = 'Y';
    } else {
        var filladress = 'N';
    }
    
    self.find("#phone").unmask();
    self.find("#phone").mask("9999999999?99");
    var phone = self.find("#phone").val();
    self.find("#fax").unmask();
    self.find("#fax").mask("9999999999?99");                
    var fax = self.find("#fax").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    self.find("#cnpj").unmask();
    self.find("#cnpj").mask(clear);
    var cnpj = self.find("#cnpj").val();
    var observation = self.find("#obs").val();

    var id = self.find("#id").val();
    $.post("person/editJuridical", {
        name: namejuridical,
        email: email,
        department: department,
        phone: phone,
        priority: priority,
        branch: branch,
        fax: fax,
        country: country,
        state: state,
        cnpj: cnpj,
        country: country,
        city: city,
        neighborhood: neighborhood,
        zipcode: zipcode,
        typestreet: typestreet,
        address: address,
        number: number,
        complement: complement,
        filladdress: filladress,
        typeuser: typeuser,
        observation: observation,
        contact: contact,
        id: id
    }, function(resp) {
        if (resp != false) {
            objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalPersonEdit");  
            $('#flexigrid2').flexReload();
        } else {
        	objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalPersonEdit");
        }
    });

}


function sendNatural(self){
    pathform = document.form1;
    var login = self.find("#login").val();
	var logintype = self.find("#logintype").val();
    var password = self.find("#password").val();
    var cpassword = self.find("#cpassword").val();
    var namenatural = self.find("#namenatural").val();
    var email = self.find("#email").val();
    var company = self.find("#company").val();
    var department = self.find("#department").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var city = self.find("#city").val();
    var dtbirth = self.find("#dtbirth").val();
    if(default_lang == 'en_US'){
        var clear = '999999999';
    }
    else{
        var clear = '99999999999';
    }
    
    var gender = self.find("input:radio[name='gender']:checked").val();
    var neighborhood = self.find("#neighborhood").val();
    if (neighborhood == ''){
        neighborhood = 'Choose';
    } 
    var zipcode = self.find("#zipcode").val();
    var typestreet = self.find("#type_street").val();
    var address = self.find("#address").val();
    if (address == ''){
        address = 'Choose';
    } 
    var number = self.find("#number").val();
    var complement = self.find("#complement").val();
    var typeuser = self.find("#type_user").val();
    var location = self.find("#location").val();
    var time_value = self.find("#time_value").val();
    var overtime = self.find("#overtime").val();
    if (self.find("#vip").is(":checked")) {
        var vip = 'Y';
    } else {
        var vip = 'N';
    }
    
    if (self.find("#filladress").is(":checked")) {
        var filladress = 'Y';
    } else {
        var filladress = 'N';
    }
    
    self.find("#phone").unmask();
    self.find("#phone").mask("9999999999?99");
    var phone = self.find("#phone").val();
    var branch = self.find("#branch").val();
    self.find("#mobile").unmask();
    self.find("#mobile").mask("9999999999?99");                
    var mobile = self.find("#mobile").val();
    var costcenter = self.find("#costcenter").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    
    if(self.find("#changePassInsert").is(":checked")){
    	var changePassInsert = 1;
    }else{
    	var changePassInsert = 0;
    }
    
    self.find("#cpf").unmask();
    self.find("#cpf").mask(clear);
    var cpf = self.find("#cpf").val();


    var checkList = new Array();
    $( "input[name='check_list[]']:checked" ).each( function() {
        checkList.push( $( this ).val() );
    } );


    $btn = self.find(document.getElementById('btnSendPersonInsert'));
    objDefault.buttonAction($btn,'disabled');
    $.post("person/insertNatural", {
        login: login,
		logintype: logintype,
        password: password,
        name: namenatural,
        email: email,
        company: company,
        department: department,
        phone: phone,
        branch: branch,
        mobile: mobile,
        costcenter: costcenter,
        country: country,
        state: state,
        cpf: cpf,
        country: country,
        state: state,
        city: city,
        neighborhood: neighborhood,
        zipcode: zipcode,
        typestreet: typestreet,
        address: address,
        number: number,
        complement: complement,
        typeuser: typeuser,
        location: location,
        vip: vip,
        filladdress: filladress,
        dtbirth: dtbirth,
        gender: gender,
        time_value: time_value,
        overtime: overtime,
        changePassInsert: changePassInsert,
        persontypes: checkList
    }, function(resp) {
        if (resp != false) {
            objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalPersonInsert");  
            $('#flexigrid2').flexReload();
        } else {
        	objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonInsert");
        }
    }).complete(function(){
    	objDefault.buttonAction($btn,'enabled');
    });
    
} 


function sendNaturalEdit(self){
	var $btn = self.find(document.getElementById('btnSendPersonEditNatural')),
    pathform = document.form1;
    var id = self.find("#id").val();
    var login = self.find("#login").val();   
	var logintype = self.find("#logintype").val();	
	var currentLoginType = self.find("#currentLoginType").val();
    var namenatural = self.find("#namenatural").val();
    var email = self.find("#email").val();
    var company = self.find("#company").val();
    var department = self.find("#department").val();
    var phone = self.find("#phone").val();
    var branch = self.find("#branch").val();
    var mobile = self.find("#mobile").val();
    var costcenter = self.find("#costcenter").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var cpf = self.find("#cpf").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    var city = self.find("#city").val();
    var dtbirth = self.find("#dtbirth").val();
    if(default_lang == 'en_US'){
        var clear = '999999999';
    }
    else{
        var clear = '99999999999';
    }
    var gender = self.find("input:radio[name='gender']:checked").val();
    var neighborhood = self.find("#neighborhood").val();
    if (neighborhood == ''){
        neighborhood = 'Choose';
    } 
    var zipcode = self.find("#zipcode").val();
    var typestreet = self.find("#type_street").val();
    var address = self.find("#address").val();
    if (address == ''){
        address = 'Choose';
    } 
    var number = self.find("#number").val();
    var complement = self.find("#complement").val();
    var typeuser = self.find("#type_user").val();
    var location = self.find("#location").val();
    var time_value = self.find("#time_value").val();
    var overtime = self.find("#overtime").val();
  
    if (self.find("#vip").is(":checked")) {
        var vip = 'Y';
    } else {
        var vip = 'N';
    }
    
    if (self.find("#filladress").is(":checked")) {
        var filladress = 'Y';
    } else {
        var filladress = 'N';
    }
    
    self.find("#phone").unmask();
    self.find("#phone").mask("9999999999?99");
    var phone = self.find("#phone").val();
    var branch = self.find("#branch").val();
    self.find("#mobile").unmask();
    self.find("#mobile").mask("9999999999?99");                
    var mobile = self.find("#mobile").val();
    var costcenter = self.find("#costcenter").val();
    var country = self.find("#country").val();
    var state = self.find("#state").val();
    
    self.find("#cpf").unmask();
    self.find("#cpf").mask(clear);
    var cpf = self.find("#cpf").val();

    var checkList = new Array();
    $( "input[name='check_list[]']:checked" ).each( function() {
        checkList.push( $( this ).val() );
    } );

    objDefault.buttonAction($btn,'disabled');
    $.post("person/editNatural", {
        login: login,
		logintype: logintype,
        currentLoginType: currentLoginType,
        name: namenatural,
        email: email,
        company: company,
        department: department,
        phone: phone,
        branch: branch,
        mobile: mobile,
        costcenter: costcenter,
        country: country,
        state: state,
        cpf: cpf,
        country: country,
        state: state,
        city: city,
        neighborhood: neighborhood,
        zipcode: zipcode,
        typestreet: typestreet,
        address: address,
        number: number,
        complement: complement,
        typeuser: typeuser,
        location: location,
        vip: vip,
        filladdress: filladress,
        dtbirth: dtbirth,
        gender: gender,
        time_value: time_value,
        overtime: overtime,
        id: id,
        persontypes: checkList
    }, function(resp) {
        if (resp != false) {
            objDefault.notification("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalPersonEdit");  
            $('#flexigrid2').flexReload();
        } else {
        	objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalPersonEdit");
        }
    }).complete(function(){
    	objDefault.buttonAction($btn,'enabled');
    });
    
} 

$(document).ready(function() {

	
	function formatItem(row) {
        return row[0] + " (<strong>id: " + row[1] + "</strong>)";
    }
    function formatResult(row) {
        return row[0].replace(/(<.+?>)/gi, '');
    }
	
	$.getJSON("person/streets/", function(data) {
	    ruas = [];
	    idruas = [];
	    i=0;
	    $.each(data, function(key, val) {
	        ruas[i] = val.name;
	        idruas[i] = val.id;
	        i = i + 1;
	    });
	});
	
	
	
	$("#content")
		.off(".contentloaded")
		.on("click.contentloaded", "input[name=txtCategory]", objPerson.changeCategory) //MOSTRAR TIPO DE CADASTRO
		.on("click.contentloaded", "#filladress", objPerson.showAddress) //MOSTRAR OU ESCONDER ENDEREÇO
		.on("change.contentloaded", "#country", objPerson.changeCountry) //TROCA PAÍS
		.on("change.contentloaded", "#state", objPerson.changeState)  //TROCA ESTADO
		.on("change.contentloaded", "#city", objPerson.changeCity)
		.on("submit.contentloaded", "#formPersonInsert", objPerson.sendForm)
		.on("submit.contentloaded", "#formPersonEdit", objPerson.sendFormEdit)
		.on("focusout.contentloaded", "#login", objPerson.checkLogin)
		.on("change.contentloaded", "#company", objPerson.changeCompany)
		.on("change.contentloaded", "#type_user", objPerson.changeTypeUser)
		.on("click.contentloaded", ".changePassword", objPerson.changePassword);
		
		
	
	
	
	
	$(document.getElementById('modalPersonAddState')).find('form').live("submit",function(){
    	var $self = $(this),
    		$category = $("input[name=txtCategory]:checked").val(),
    		$country = $("#country").val(),
    		$newstate = $self.find("#newstatefield").val(),
    		$abbr = $self.find("#abbr").val(),
    		$btn = $self.find(document.getElementById('btnAddNewState'));	
    		
    	objDefault.buttonAction($btn,'disabled');
    	$.post('person/insertState', {
            name : $newstate,
            country : $country,
            abbr: $abbr
        }, function(resposta) {
            if(resposta){
            	objPerson.change($country, resposta);
            	$self[0].reset();
            	objModal.openModal(objModal.getActive());
            }else{
            	objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonAddState");
            }                
        }).complete(function(){
        	objDefault.buttonAction($btn,'enabled');
        });
    })
    
    
    $(document.getElementById('modalPersonAddCity')).find('form').live("submit",function(){
    	
    	var $self = $(this),
    		$category = $("input[name=txtCategory]:checked").val(),
    		$state = $(document.getElementById($category)).find(".state").val(),
    		$newcity = $self.find("#newcityfield").val(),
    		$btn = $self.find(document.getElementById('btnAddNewCity'));	
    	
    	objDefault.buttonAction($btn,'disabled');
    	$.post('person/insertCity', {
            name : $newcity,
            state : $state
        }, function(resposta) {
            if(resposta){
            	objPerson.returncities(resposta);
            	$self[0].reset();
            	objModal.openModal(objModal.getActive());
            }else{
            	objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonAddState");
            }                
        }).complete(function(){
        	objDefault.buttonAction($btn,'enabled');
        });
    })
	
	
	$(document.getElementById('modalPersonAddLocation')).find('form').live("submit",function(){
    	var $self = $(this),
    		$name = $self.find("#name_location").val(),
    		$btn = $self.find(document.getElementById('btnAddNewLocation'));	
    	objDefault.buttonAction($btn,'disabled');
    	$.post('person/insertLocation', {
            name : $name
        }, function(resposta) {
            if(resposta){
            	
            	var $location = $(document.getElementById(objModal.getActive())).find("select[name=location]");
	            $.post("person/getLocation", {}, function(valor) {
	                $location.html(valor);
	            }).complete(function(){
	            	$location.find("option[value="+resposta+"]").attr("selected","selected");
	            	$self[0].reset();
            		objModal.openModal(objModal.getActive());
	            })
            }else{
            	objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonAddState");
            }                
        }).complete(function(){
        	objDefault.buttonAction($btn,'enabled');
        });
    })
	
	$(document.getElementById('modalPersonDisable')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendPersonDisable'));			
		$.ajax({
			type: "POST",
			url: "person/deactivate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalPersonDisable");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deactivated'].replace (/\"/g, ""),"modalPersonDisable");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deactivated_error'].replace (/\"/g, ""),"modalPersonDisable");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalPersonActive')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendPersonActive'));
		$.ajax({
			type: "POST",
			url: "person/activate",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalPersonActive");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_activated'].replace (/\"/g, ""),"modalPersonActive");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_activated_error'].replace (/\"/g, ""),"modalPersonActive");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$(document.getElementById('modalAlterPassword')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSavePassword'));
		
		$.ajax({
			type: "POST",
			url: "person/changePassword",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalAlterPassword");
			},
			success: function(ret) {
				if(ret){
					objDefault.notificationNoRemove("success",aLang['Edit_sucess'].replace (/\"/g, ""),"modalAlterPassword");
					$btn.remove();
				}
				else
					objDefault.notification("error",aLang['Edit_failure'].replace (/\"/g, ""),"modalAlterPassword");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});	
	});
	
	$("#logintype").live("change",function(){		
		var currentValue = document.getElementById("currentLoginType").value,
			newValue = this.value,
			$liMsg = $(this).parent().next();
		
		if(currentValue == 3 && newValue != 3){
			$liMsg.removeClass("none");
		}else{
			$liMsg.addClass("none");
		}
				
	})
	
});