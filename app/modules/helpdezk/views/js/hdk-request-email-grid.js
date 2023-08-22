var objGroup = {
    loadType: function() {
        var areaID = $("#modal-cmb-area").val();
        objGroup.emptyCombos('area');

        $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaID: areaID},
            function(valor){
                var attr = $("#modal-cmb-type").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#modal-cmb-type").removeAttr('disabled');
                
                $("#modal-cmb-type").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>' + valor);
                $("#modal-cmb-type").val('X');
                $("#modal-cmb-type").trigger("change");
            });
    },
    loadItem: function(){
        var typeID = $("#modal-cmb-type").val();
        objGroup.emptyCombos('type');
        
        if(typeID != 'X' && typeID){
            $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeID: typeID},
            function(valor){
                var attr = $("#modal-cmb-item").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#modal-cmb-item").removeAttr('disabled');
                
                $("#modal-cmb-item").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>' + valor);
                $("#modal-cmb-item").val('X');
                $("#modal-cmb-item").trigger("change");
            });
        }
    },
    loadService: function(){
        var itemID = $("#modal-cmb-item").val();
        objGroup.emptyCombos('item');

        if(itemID != 'X' && itemID){
            $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemID: itemID},
            function(valor){
                var attr = $("#modal-cmb-service").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#modal-cmb-service").removeAttr('disabled');
                
                $("#modal-cmb-service").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>' + valor);
                $("#modal-cmb-service").val('X');
                $("#modal-cmb-service").trigger("change");
            });
        }
    },
    emptyCombos: function(type){
        switch(type){
            case 'area':
                $("#modal-cmb-type").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#modal-cmb-type").trigger("change");
                $("#modal-cmb-item").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#modal-cmb-item").trigger("change");                
                $("#modal-cmb-service").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#modal-cmb-service").trigger("change");
                if(!$(".groupListView").hasClass('d-none'))
                    $(".groupListView").addClass('d-none');
                break;
            case 'type':
                $("#modal-cmb-item").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#modal-cmb-item").trigger("change");
                $("#modal-cmb-service").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#modal-cmb-service").trigger("change");
                if(!$(".groupListView").hasClass('d-none'))
                    $(".groupListView").addClass('d-none');
                break;
            case 'item':
                $("#modal-cmb-service").html('<option value="X" disabled hidden selected>'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#modal-cmb-service").trigger("change");
                if(!$(".groupListView").hasClass('d-none'))
                    $(".groupListView").addClass('d-none');
                break;
            case 'service':
                if(!$(".groupListView").hasClass('d-none'))
                    $(".groupListView").addClass('d-none');
                break;

        }        
    }
};

$(document).ready(function () {
    countdown.start(timesession);
    
    //set buttons (blocked, available)
    setActionsBtn(aPermissions);

    /**
     * Select2
     */
    $('#filter-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});
    $('#action-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});

   /**
    * reload action's combo
    */
   $('#filter-list').change(function(){
       var searchOpts = $("#filter-list option:selected").data('optlist');

       $.post(path+"/main/home/reloadSearchOptions",{searchOpts:searchOpts},
           function(valor) {
               $("#action-list").html(valor);
               $("#action-list").trigger("change");
               return false;
           });
   });

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
        { title: vocab["ID"], width: '10%', dataIndx: "idgetemail", hidden:true, halign: "center"  },        
        { title: "<b>"+vocab["Server"]+"</b> ", width: '50%', dataIndx: "serverUrl", halign: "center"  },
        { title: "<b>"+vocab["Type"]+"</b> ", width: '20%', dataIndx: "serverType", align: "center", halign: "center"  },        
        { title: "<b>"+vocab["email"]+"</b> ", width: '29%', dataIndx: "user", align: "center", halign: "center"  },
        { title: "", width: '10%', hidden:true, halign: "center"  }
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
        url: path + '/helpdezk/hdkRequestEmail/jsonGrid',
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
        sorter:[ { dataIndx: "serverUrl", dir: "up" } ]
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
        rPPOptions: [10, 20, 30, 40, 50]
    };
    
    var obj = { 
        width: '100%', 
        height: 500,
        wrap: false,
        dataModel: dataModel,
        colModel: colM,
        editable: false,
        title: "<b>"+vocab['pgr_email_request']+"</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        selectChange: function (evt, ui) {
            var rowIndx = getRowIndx('grid_request_email'),
                row = $("#grid_request_email").pqGrid('getRowData', {rowIndx: rowIndx}),
                rowSt = row.status_val;
                
            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (rowSt == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
        },
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx('grid_request_email'),
                row = $("#grid_request_email").pqGrid('getRowData', {rowIndx: rowIndx});
                
            location.href = path + "/helpdezk/hdkRequestEmail/formUpdate/"+row.idgetemail;
        }
    };

    $("#grid_request_email").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_request_email").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_request_email").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val(), 
            flg = $("#viewInactive").is(':checked');
            
        $("#grid_request_email").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation,allRecords:flg};
        } );
        
        $("#grid_request_email").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val(), flg = $("#viewInactive").is(':checked');
            
        $("#grid_request_email").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue,allRecords:flg};
        });
        
        $("#grid_request_email").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnCreate").click(function(){
        location.href = path + "/helpdezk/hdkRequestEmail/formCreate";
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx('grid_request_email'),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_request_email").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/helpdezk/hdkRequestEmail/formUpdate/"+row.idgetemail;
            
        }else{            
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnDelete").click(function(){
        var rowIndx = getRowIndx("grid_request_email"),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_request_email").pqGrid('getRowData', {rowIndx: rowIndx});

            $("#modal-delete-request-email-id").val(row.idgetemail);          
            $("#modal-delete-server-url").val(row.serverUrl);
            $("#modal-delete-server-type").val(row.serverType);
            $("#modal-request-email-delete").modal('show');
        }else{
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });
  
  
    $("#btnDeleteYes").click(function(){
        
        if(!$("#btnDeleteYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkRequestEmail/deleteRequestEmail',
                dataType: 'json',
                data: {
                    _token : $("#_token").val(),
                    requestEmailId: $("#modal-delete-request-email-id").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_deleted_error'],'alert-delete-request-email');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Alert_deleted'],'alert-delete-request-email');
                        setTimeout(function(){
                            $('#modal-request-email-delete').modal('hide');
                            $("#grid_request_email").pqGrid("refreshDataAndView");
                        },4000);
                    } else {
                        modalAlertMultiple('danger',obj.message,'alert-delete-request-email');
                    }
                },
                beforeSend: function(){
                    $("#btnDeleteYes").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnDeleteNo").addClass('disabled');
                },
                complete: function(){
                    $("#btnDeleteYes").html("<i class='fa fa-check'></i> "+ vocab['Yes']).removeClass('disabled');
                    $("#btnDeleteNo").removeClass('disabled');
                }
    
            });
        }

    });

    $('#viewInactive').on('click', function() {
        var quickValue = $("#txtSearch").val(),
            quickSearch = quickValue == '' ? false :true, 
            flg = $(this).is(':checked');

        $("#grid_request_email").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:quickSearch,quickValue:quickValue,allRecords:flg};
        });
        
        $("#grid_request_email").pqGrid("refreshDataAndView");     
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkRequestEmail/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });

    $('.lbltooltip').tooltip();
});

/**
 * Change status status
 * @param  {int}groupId 
 * @param  {string} newStatus
 * @return {void}      
 */
function postStatus(groupId,newStatus)
{
    var msgErr = newStatus == "A" ? vocab['Alert_activated_error'] : vocab['Alert_deactivated_error'],
        msg = newStatus == "A" ? vocab['Alert_activated'] : vocab['Alert_deactivated'];

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkRequestEmail/changeStatus',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            groupId: groupId,
            newStatus: newStatus
        },
        error: function (ret) {
            showAlert(msgErr,'danger');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success) {
                showAlert(msg,'success');
            } else {
                showAlert(msgErr,'danger');
            }
        }

    });

    return false;
}

