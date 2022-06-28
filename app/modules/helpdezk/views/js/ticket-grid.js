$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Select2
     */
    $('#filter-list').select2({width:'100%',dropdownParent: $(this).find('.modal-body-filters')});
    $('#action-list').select2({width:'100%',dropdownParent: $(this).find('.modal-body-filters')});

    if(typeUser == 3){
        $('#cmbTypeExpireDate').select2({width:'100%',placeholder:vocab['Select'],allowClear:true});
        $('#cmbTipeView').select2({width:'100%',placeholder:vocab['Select'],allowClear:true});

        var btnWaitApp = vocab['Waiting_for_approval'],
            modelUrl =  path + '/helpdezk/hdkTicket/jsonGridAttendant',
            titleLbl = vocab['Grid_new_tickets'];
    }else{
        if(flgOperator == 0){
            var flgApvRequire = $.ajax({type: "POST",url: path+"/helpdezk/hdkTicket/checkApproval",async: false}).responseText;
        }

        var btnWaitApp = vocab['Grid_waiting_my_approval'],
            modelUrl =  path + '/helpdezk/hdkTicket/jsonGrid',
            titleLbl = (flgApvRequire > 0) ? vocab['Grid_waiting_my_approval_tickets'] : vocab['Grid_all'];
    }

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
     if(typeUser == 3){
        var colM = [ 
            { title: " ", width: '1%', dataIndx: "star", halign: "center"  },
            { title: "<i class='fa fa-paperclip'></i>", width: '1%', dataIndx: "attachments", halign: "center"  },      
            { title: "<b>N&deg;</b> ", width: '15%', dataIndx: "ticketCode", halign: "center"  },
            { title: "<b>"+vocab["Grid_opening_date"]+"</b> ", width: '15%', dataIndx: "entryDate", halign: "center"  },        
            { title: "<b>"+vocab["Grid_subject"]+"</b> ", width: '20%', dataIndx: "subject", halign: "center"  },
            { title: "<b>"+vocab["Grid_expire_date"]+"</b> ", width: '15%', dataIndx: "expiryDate", halign: "center"  },
            { title: "<b>"+vocab["Grid_incharge"]+"</b> ", width: '20%', dataIndx: "inCharge", halign: "center"  },
            { title: "<b>"+vocab["status"]+"</b> ", width: '9%', dataIndx: "status", align: "center", halign: "center"  },
            { title: "", width: '27%', dataIndx: "",  halign: "center", hidden:true  }  
        ];
    }else{
        var colM = [ 
            { title: " ", width: 2, dataIndx: "star", halign: "center", align: "center" },       
            { title: "<b>N&deg;</b> ", width: 111, dataIndx: "ticketCode", halign: "center", align: "center" },
            { title: "<b>"+vocab["Grid_opening_date"]+"</b> ", width: 150, dataIndx: "entryDate", halign: "center", align: "center" },        
            { title: "<b>"+vocab["Grid_subject"]+"</b> ", width: 218, dataIndx: "subject", halign: "center" },
            { title: "<b>"+vocab["Grid_expire_date"]+"</b> ", width: 150, dataIndx: "expiryDate", halign: "center", align: "center" },
            { title: "<b>"+vocab["Grid_incharge"]+"</b> ", width: 220, dataIndx: "inCharge", halign: "center" },
            { title: "<b>"+vocab["status"]+"</b> ", width: 220, dataIndx: "status", align: "center", halign: "center"  },
            { title: "", width: '27%', dataIndx: "",  halign: "center", hidden:true  }  
        ];
    }


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
        url: modelUrl,
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
        sorter:[ { dataIndx: "code_request", dir: "up" } ]
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

    var toolbar = {
        cls: 'pq-toolbar-crud',
        items: [
            { type: "<span class='hdkToolbar' id='btnAll'>"+vocab['Grid_all']+"</span>"},
            { type: "<span class='hdkToolbar' id='btnListNew'>"+vocab['Grid_new']+"</span>"},
            { type: "<span class='hdkToolbar' id='btnListAttended'>"+vocab['Grid_being_attended']+"</span>",listener:''},
            { type: "<span class='hdkToolbar' id='btnListWaiting'>"+btnWaitApp+"</span>",listener:''},
            { type: "<span class='hdkToolbar' id='btnListFinished'>"+vocab['Grid_finished']+"</span>",listener:''},
            { type: "<span class='hdkToolbar' id='btnListRejected'>"+vocab['Grid_rejected']+"</span>",listener:''}
        ]
    };
    
    var obj = { 
        width: '100%', 
        height: 500,
        wrap:false,
        dataModel: dataModel,
        colModel: colM,
        editable: false,
        title: "<b>"+titleLbl.toUpperCase()+"</b>",
        toolbar: toolbar,
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        selectChange: function (evt, ui) {
            var rowIndx = getRowIndx(),
                row = $("#grid_ticket").pqGrid('getRowData', {rowIndx: rowIndx}),
                rowSt = row.status_val;
                
            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (rowSt == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
        },
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx(),
                row = $("#grid_ticket").pqGrid('getRowData', {rowIndx: rowIndx});
                
            location.href = path + "/helpdezk/hdkReason/formUpdate/"+row.id;
        }
    };

    $("#grid_ticket").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_ticket").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_ticket").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val();
            
        $("#grid_ticket").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation};
        } );
        
        $("#grid_ticket").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val();
            
        $("#grid_ticket").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue};
        });
        
        $("#grid_ticket").pqGrid("refreshDataAndView");
    });
    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx(),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_ticket").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/helpdezk/hdkReason/formUpdate/"+row.id;
            
        }else{            
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnAll").click(function(){
        getDataByStatus("ALL","<b>"+vocab['Grid_all_tickets'].toUpperCase()+"</b>");
    });

    $("#btnListNew").click(function(){
        getDataByStatus("1","<b>"+vocab['Grid_new_tickets'].toUpperCase()+"</b>");
    });

    $("#btnListAttended").click(function(){
        getDataByStatus("3","<b>"+vocab['Grid_being_attended_tickets'].toUpperCase()+"</b>");
    });

    $("#btnListWaiting").click(function(){
        getDataByStatus("4","<b>"+vocab['Grid_waiting_my_approval_tickets'].toUpperCase()+"</b>");
    });

    $("#btnListFinished").click(function(){
        getDataByStatus("5","<b>"+vocab['Grid_finished_tickets'].toUpperCase()+"</b>");
    });

    $("#btnListRejected").click(function(){
        getDataByStatus("6","<b>"+vocab['Grid_rejected_tickets'].toUpperCase()+"</b>");
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkTicket/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });
});

function getDataByStatus(statusID,titleLabel) {
    $("#grid_ticket").pqGrid( "option", "dataModel.postData", function(){
        return {idStatus:statusID};
    });
    
    $("#grid_ticket").pqGrid( "option", "title", titleLabel );
    
    $("#grid_ticket").pqGrid("refreshDataAndView");
}

/**
 * Change reason's status
 * @param  {int}reasonID 
 * @param  {string} newStatus
 * @return {void}      
 */
function postStatus(reasonID,newStatus)
{
    var msgErr = newStatus == "A" ? vocab['Alert_activated_error'] : vocab['Alert_deactivated_error'],
        msg = newStatus == "A" ? vocab['Alert_activated'] :vocab['Alert_deactivated'];

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkReason/changeStatus',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            reasonID: reasonID,
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

