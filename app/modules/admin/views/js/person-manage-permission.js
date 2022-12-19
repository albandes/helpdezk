$(document).ready(function () {
    countdown.start(timesession);

    /** 
     * Define a model for grid's columns
     * 
     * title Title of column
     * width Width of column
     * dataIndx Column name on DB to ordering
     * align Align of column content
     * halign Align of column header
     * 
     * for more column's options see https://paramquery.com/api#option-column-hidden
     * */ 
    var colM = [ 
        { title: "", width: '10%', dataIndx: "idprogram", hidden:true, halign: "center"  },
        { title: "<b>"+vocab["Module"]+"</b> ", width: '20%', dataIndx: "module", halign: "center"},
        { title: "<b>"+vocab["Program"]+"</b> ", width: '25%', dataIndx: "program", halign: "center"  },
        { title: "<b>"+vocab["Access"]+"</b> ", width: '8%', dataIndx: "access", align: "center", halign: "center", sortable: false  },
        { title: "<b>"+vocab["New"]+"</b> ", width: '8%', dataIndx: "new", align: "center", halign: "center", sortable: false  },
        { title: "<b>"+vocab["Edit_btn"]+"</b> ", width: '8%', dataIndx: "edit", align: "center", halign: "center", sortable: false  },
        { title: "<b>"+vocab["Delete"]+"</b> ", width: '8%', dataIndx: "delete", align: "center", halign: "center", sortable: false  },
        { title: "<b>"+vocab["Export"]+"</b> ", width: '8%', dataIndx: "export", align: "center", halign: "center", sortable: false  },
        { title: "<b>E-mail</b> ", width: '8%', dataIndx: "email", align: "center", halign: "center", sortable: false  },
        { title: "<b>SMS</b> ", width: '6%', dataIndx: "sms", align: "center", halign: "center", sortable: false  },
        { title: "", width: '10%', dataIndx: "idperson", hidden:true, halign: "center"  }        
    ];

    /** 
     * Define a data model
     * 
     * location     Server from which to get data. (local or remote)
     * dataType     Data Type of response from server in case of remote request
     * method       Method to use to fetch data from server
     * url          It's an absolute or relative url from where grid gets remote data and sends requests (via GET or POST) for remote sorting, paging, filter, etc.
     * getData      It's a callback function which acts as a mediator between remote server and pqGrid
     * 
     * for more data model options see https://paramquery.com/api#option-dataModel
     * */
    var dataModel = {
        location: "remote",
        dataType: "JSON",
        method: "POST",
        url: path + '/admin/person/jsonGridPermissions',
        postData: {_token:$("#_token").val(),personId:$("#personId").val()},
        getData: function (dataJSON) {                
            return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: dataJSON.data };                
        }
    };
    
    /** 
     * Define a sort model
     * 
     * cancel   When true, sorting on a column can be canceled by clicking on a column header while column is in descending sort order.
     * type     It decides the location of sorting, possible values are "local" and "remote".
     * sorter   It's an array of { dataIndx: dataIndx, dir: "up/down" } objects where dataIndx is dataIndx of the column and dir has 2 possible values "up" & "down".
     * 
     * for more sort model options see https://paramquery.com/api#option-sortModel
     * */
    var sortModel = {
        cancel: false,
        type: "remote",
        sorter:[ { dataIndx: "module", dir: "up" } ]
    };

    /** 
     * Define a page model
     * 
     * type         Paging can be enabled for both local and remote requests.
     * curPage      Current page of the view when paging has been enabled.
     * rPP          It denotes results per page when paging has been enabled.
     * rPPOptions   Results per page options in dropdown when paging has been enabled.
     * 
     * for more page model options see https://paramquery.com/api#option-pageModel
     * */
    var pageModel = {
        type: "remote",
        curPage: this.curPage,
        rPP: 10,
        rPPOptions: [1, 10, 20, 30, 40, 50]
    };
    
    var obj = { 
        width: '100%', 
        height: 500,
        wrap:false,
        dataModel: dataModel,
        colModel: colM,
        editable: false,
        title: "<b>"+vocab['permissions']+" "+vocab['to']+" "+lblPersonName+"</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        dragColumns: { enabled: false }
    };

    $("#grid_permissions").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_permissions").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_permissions").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val();
            
        $("#grid_permissions").pqGrid( "option", "dataModel.postData", function(){
            return {_token:$("#_token").val(),personId:$("#personId").val(),quickSearch:true,quickValue:quickValue};
        });
        
        $("#grid_permissions").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnBack").click(function(){
        location.href = path + '/admin/person/index';
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/person/index" ;        
    });

    $('.lbltooltip').tooltip();
});

/**
 * en_us Send data to grant/revoke permission
 * pt_br Envia os dados para conceder/remover a permissão
 * 
 * @param  {int}id 
 * @param  {int} programId
 * @param  {int} accessTypeId
 * @param  {int} personId
 * @return {void}      
 */
function grantPermission(id,programId,accessTypeId,personId){
    $.ajax({
        type: "POST",
        url: path + '/admin/person/grantPermission',
        dataType: 'json',
        data: {
            _token : $("#_token").val(),
            programId: programId,
            accessTypeId: accessTypeId,
            personId: personId,
            allow: ($("#"+id).is(':checked')) ? "Y" : "N"
        },
        error: function (ret) {
            if($("#"+id).is(':checked')){
                $("#"+id).prop( "checked", false );
            }
            modalAlertMultiple('danger',vocab['Permission_error'],'alert-grant-permission');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success) {
                var msg = ($("#"+id).is(':checked')) ? vocab['permission_granted_successfully'] : vocab['permission_removed_successfully'];
                modalAlertMultiple('success',msg,'alert-grant-permission');
            } else {
                modalAlertMultiple('danger',vocab['Permission_error'],'alert-grant-permission');
            }
        }

    });
}

