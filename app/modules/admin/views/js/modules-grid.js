$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Select2
     */
   $('#filter-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});
   $('#action-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});

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
        { title: vocab["ID"], width: '10%', dataIndx: "idmodule", hidden:true, halign: "center"  },        
        { title: "<b>"+vocab["Name"]+"</b> ", width: 800, dataIndx: "name", halign: "center"  },
        { title: "<b>"+vocab["status"]+"</b> ", width: 150, dataIndx: "status", align: "center", halign: "center"  },
        { title: "", width: '10%', dataIndx: "status_val", hidden:true, halign: "center"  },
        { title: "<b>"+vocab["Default"]+"</b> ", width: 150, dataIndx: "default", align: "center", halign: "center"  },
        { title: "", width: '10%', dataIndx: "default_val", hidden:true, halign: "center"  }        
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
        url: path + '/admin/modules/jsonGrid',
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
        sorter:[ { dataIndx: "name", dir: "up" } ]
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
        title: "<b>"+vocab['pgr_titles']+"</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        dragColumns: { enabled: false },
        selectChange: function (evt, ui) {
            var rowIndx = getRowIndx("grid_modules"),
                row = $("#grid_modules").pqGrid('getRowData', {rowIndx: rowIndx}),
                rowSt = row.status_val;
                
            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (rowSt == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
        },
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx("grid_modules"),
                row = $("#grid_modules").pqGrid('getRowData', {rowIndx: rowIndx});
                
                location.href = path + "/admin/modules/formUpdate/"+row.id;
        }
    };

    $("#grid_modules").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_modules").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_modules").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val();
            
        $("#grid_modules").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation};
        } );
        
        $("#grid_modules").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val();
            
        $("#grid_modules").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue};
        });
        
        $("#grid_modules").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnCreate").click(function(){
        location.href = path + "/admin/modules/formCreate";
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx("grid_modules"),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_modules").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/admin/modules/formUpdate/"+row.idmodule;
        }else{
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnEnable").click(function(){
        if(!$("#btnEnable").hasClass('disabled')){
            var rowIndx = getRowIndx(),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_modules").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.id,'A');
            }else{
                msg = vocab['Alert_select_one'];
                showAlert(msg,'warning');
            }
        }      
    });

    $("#btnDisable").click(function(){
        if(!$("#btnDisable").hasClass('enabled')){
            var rowIndx = getRowIndx(),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_modules").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.id,'I');
            }else{
                msg = vocab['Alert_select_one'];
                showAlert(msg,'warning');
            }
        }
    });

    $("#btnDelete").click(function(){
        var rowIndx = getRowIndx(),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_modules").pqGrid('getRowData', {rowIndx: rowIndx});
            
            $("#delete-id").val(row.id);          
            $("#delete-userType").val(row.userType);
            $("#delete-permissionGroup").val(row.permissionGroup_val);
            $("#delete-langKeyName").val(row.langKeyName);
            $("#modal-userType-delete").modal('show');
        }else{
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });
  
  
    $("#btnDeleteYes").click(function(){
        
        if(!$("#btnDeleteYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/UserType/deleteUserType',
                dataType: 'json',
                data: {
                    _token : $("#_token").val(),
                    userTypeID: $("#delete-id").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_deleted_error'],'alert-delete-userType');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Alert_deleted'],'alert-delete-userType');
                        setTimeout(function(){
                            $('#modal-userType-delete').modal('hide');
                            $("#grid_modules").pqGrid("refreshDataAndView");
                        },4000);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_deleted_error'],'alert-delete-userType');
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

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/modules/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });
});

/**
 * Change userType's status
 * @param  {int}userTypeID 
 * @param  {string} newStatus
 * @return {void}      
 */
function postStatus(userTypeID,newStatus)
{
    var msgErr = newStatus == "A" ? vocab['Alert_activated_error'] : vocab['Alert_deactivated_error'],
        msg = newStatus == "A" ? vocab['Alert_activated'] :vocab['Alert_deactivated'];

    $.ajax({
        type: "POST",
        url: path + '/admin/UserType/changeStatus',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            userTypeID: userTypeID,
            newstatus: newStatus
        },
        error: function (ret) {
            showAlert(msgErr,'danger');
        },
        success: function(ret){
            console.log(ret);
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

