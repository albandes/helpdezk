$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'person/permjson/idperson/'+idperson,  
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
            name : 'name', 
            width : 250, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Access'].replace (/\"/g, ""), 
            name : 'acess', 
            width : 60, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['New'].replace (/\"/g, ""), 
            name : 'new', 
            width : 60, 
            sortable : true, 
            align: 'center'
        }
        ,
        {
            display: aLang['Edit_btn'].replace (/\"/g, ""), 
            name : 'edit', 
            width : 60, 
            sortable : true, 
            align: 'center'
        }
        ,
        {
            display: aLang['Delete'].replace (/\"/g, ""), 
            name : 'delete', 
            width : 60, 
            sortable : true, 
            align: 'center'
        },
        {
            display: aLang['Export'].replace (/\"/g, ""), 
            name : 'export', 
            width : 60, 
            sortable : true, 
            align: 'center'
        }
        ,
        {
            display: 'E-mail', 
            name : 'email', 
            width : 60, 
            sortable : true, 
            align: 'center'
        }
        ,
        {
            display: 'SMS', 
            name : 'sms', 
            width : 60, 
            sortable : true, 
            align: 'center'
        }
        ,
        {
            display: '', 
            name : 'button', 
            width : 120, 
            sortable : true, 
            align: 'center'
        }
    ],

	buttons : [
        {
            name: aLang['Back_btn'].replace (/\"/g, ""),  
            bclass: 'back', 
            onpress: back
        },
	],

        searchitems : [
        {
            display: aLang['Name'].replace (/\"/g, ""),  
            name : 'tbp.name', 
            isdefault: true
        }
					
        ],

        sortname: "tbp.idprogram",
        sortorder: "DESC",
        usepager: true,
        title: ' :: '+aLang['permissions'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: "auto",
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect : true	
    }); 
});//init		 


function addNew(idprogram, idperson){
	var modalInsert = $(document.getElementById("modalPersonPermissionInsert"));
    objDefault.maskLoaderShow();
    modalInsert.load('person/personPermForm/id/'+idprogram+'/idperson/'+idperson, function(){
    	objDefault.init();
    	objModal.openModal("modalPersonPermissionInsert");
    	objDefault.maskLoaderHide();
    })
}

function deletePerms(idperson, idprogram){
	var modalDelete = $(document.getElementById("modalPersonPermissionDelete"));
    objDefault.maskLoaderShow();
    modalDelete.load('person/removeExceptionsModal/idprogram/'+idprogram+'/idperson/'+idperson, function(){
    	objDefault.init();
    	objModal.openModal("modalPersonPermissionDelete");
    	objDefault.maskLoaderHide();
    })
}

function editperm(id, idprogram, idperson, type) {                
	if (document.getElementById(id).checked) {
		var check = "Y",
			contr = "grantpermission";
	}else{
		var check = "N",
			contr = "revokepermission";
	}
	$.post('person/'+contr, {
		idperson : idperson,
		check : check,
		idprogram : idprogram,
		type: type
	}, function(response) {
                                
		if (response != false) {
			
		} else {
			objModal.openModal("modalPermission");
		}
	});
}

function back(){
    $("#content").load('person/index/id/1');
}


$(document.getElementById('modalPersonPermissionDelete')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendPersonPermissionDelete'));
	$.ajax({
		type: "POST",
		url: "person/removeExceptions",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalPersonPermissionDelete");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalPersonPermissionDelete");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalPersonPermissionDelete");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});

$(document.getElementById('modalPersonPermissionInsert')).find('form').live("submit",function(){
	var $self = $(this),
		$btn = $self.find(document.getElementById('btnSendPersonPermissionInsert'));
	$.ajax({
		type: "POST",
		url: "person/insertPermException",
		data: $(this).serialize(),
		error: function (ret) {
			objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonPermissionInsert");
		},
		success: function(ret) {
			if(ret){
				objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalPersonPermissionInsert");
				$("#flexigrid2").flexReload();
			}
			else
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalPersonPermissionInsert");
		},
		beforeSend: function(){
			objDefault.buttonAction($btn,'disabled');
		},
		complete: function(){
			objDefault.buttonAction($btn,'enabled');
		}
	});	
});
