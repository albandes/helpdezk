$(document).ready(function(){
		
    $("#flexigrid2").flexigrid({
        url: 'typepersonpermission/typepersonjson/idprogram/'+idprogram,  
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
            width : 100, 
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
        sortorder: "desc",
        usepager: true,
        title: ' :: '+aLang['Permissions'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-206,
        resizable: false,
        minimizado: false,
        singleSelect: true					
    }); 
		   
});//init		 

 function edit2(id, idprogram, accesstype, idtypeperson) {
		            
	if (document.getElementById(id).checked) {
		var check = "Y";
		$.post('typepersonpermission/grantpermission', {
			id : id,
			check : check,
			idprogram : idprogram,
			idaccesstype: accesstype,
            idtypeperson: idtypeperson
		}, function(response) {

			if (response != false) {
				
			} else {
				alert("{/literal}{$smarty.config.Permission_error}{literal}");
			}
		});
	} else {
		var check = "N";
		$.post('typepersonpermission/revokepermission', {
			id : id,
			check : check,
			idprogram : idprogram,
                            idaccesstype: accesstype,
                            idtypeperson: idtypeperson
		}, function(response) {

			if (response != false) {
				
			} else {
				alert("{/literal}{$smarty.config.Permission_error}{literal}");
			}
		});
	}
}


function back(){
    $("#content").load('typepersonpermission');
}