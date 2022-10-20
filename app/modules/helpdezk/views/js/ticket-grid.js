$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Select2
     */
    $('#filter-list').select2({width:'100%',dropdownParent: $(this).find('.modal-body-filters')});
    $('#action-list').select2({width:'100%',dropdownParent: $(this).find('.modal-body-filters')});

    if(typeUser == 3){
        $('#cmbTypeExpireDate').select2({width:'100%',placeholder:vocab['Select'],allowClear:true});
        $('#cmbViewType').select2({width:'100%',placeholder:vocab['Select'],allowClear:true});

        var btnWaitApp = vocab['Waiting_for_approval'],
            modelUrl =  path + '/helpdezk/hdkTicket/jsonGridAttendant',
            titleLbl = vocab['Grid_new_tickets'];
            
        makeActive("1");
    }else{
        if(flgOperator == 0){
            var flgApvRequire = $.ajax({type: "POST",url: path+"/helpdezk/hdkTicket/checkApproval",async: false}).responseText;
        }

        var btnWaitApp = vocab['Grid_waiting_my_approval'],
            modelUrl =  path + '/helpdezk/hdkTicket/jsonGrid',
            titleLbl = (flgApvRequire > 0) ? vocab['Grid_waiting_my_approval_tickets'] : vocab['Grid_all_tickets'];
        
        (flgApvRequire > 0) ? makeActive("4") : makeActive("ALL");
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
            { title: " ", width: '1%', dataIndx: "star", align: "center", halign: "center"  },
            { title: "<i class='fa fa-paperclip'></i>", width: '1%', dataIndx: "attachments", align: "center", halign: "center"  },      
            { title: "<b>N&deg;</b> ", width: '10%', dataIndx: "ticketCode", halign: "center", hidden:true  },
            { title: "<b>N&deg;</b> ", width: '10%', dataIndx: "ticketCodeLink", halign: "center"  },
            { title: "<b>"+vocab["Grid_opening_date"]+"</b> ", width: '10%', dataIndx: "entryDate", halign: "center"  },
            { title: "<b>"+vocab["Company"]+"</b> ", width: '5%', dataIndx: "company", halign: "center"  },
            { title: "<b>"+vocab["From"]+"</b> ", width: '5%', dataIndx: "owner", halign: "center"  },
            { title: "<b>"+vocab["Type"]+"</b> ", width: '5%', dataIndx: "type", halign: "center"  },
            { title: "<b>"+vocab["Item"]+"</b> ", width: '5%', dataIndx: "item", halign: "center"  },
            { title: "<b>"+vocab["Service"]+"</b> ", width: '7%', dataIndx: "service", halign: "center"  },
            { title: "<b>"+vocab["Grid_subject"]+"</b> ", width: '8%', dataIndx: "subject", halign: "center"  },
            { title: "<b>"+vocab["Grid_expire_date"]+"</b> ", width: '10%', dataIndx: "expiryDate", halign: "center"  },
            { title: "<b>"+vocab["status"]+"</b> ", width: '9%', dataIndx: "status", align: "left", halign: "center"  },
            { title: "<b>"+vocab["Grid_incharge"]+"</b> ", width: '10%', dataIndx: "inCharge", halign: "center"  },
            { title: "<b>"+vocab["Priority"]+"</b> ", width: '9%', dataIndx: "priority", align: "left", halign: "center"  },
            { title: "", width: '27%', dataIndx: "",  halign: "center", hidden:true  }  
        ];
        $orderBy = "expiryDate";
        
    }else{
        var colM = [ 
            { title: " ", width: 2, dataIndx: "star", halign: "center", align: "center" },       
            { title: "<b>N&deg;</b> ", width: 111, dataIndx: "ticketCode", halign: "center", align: "center" },
            { title: "<b>"+vocab["Grid_opening_date"]+"</b> ", width: 150, dataIndx: "entryDate", halign: "center", align: "center" },        
            { title: "<b>"+vocab["Grid_subject"]+"</b> ", width: 218, dataIndx: "subject", halign: "center" },
            { title: "<b>"+vocab["Grid_expire_date"]+"</b> ", width: 150, dataIndx: "expiryDate", halign: "center", align: "center" },
            { title: "<b>"+vocab["Grid_incharge"]+"</b> ", width: 220, dataIndx: "inCharge", halign: "center" },
            { title: "<b>"+vocab["status"]+"</b> ", width: 220, dataIndx: "status", align: "left", halign: "center"  },
            { title: "", width: '27%', dataIndx: "",  halign: "center", hidden:true  }  
        ];
        $orderBy = "entryDate";
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
        sorter:[ { dataIndx: $orderBy, dir: "down" } ]
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
            { type: "<span class='hdk-toolbar' id='btnAll'>"+vocab['Grid_all']+"</span>"},
            { type: "<span class='hdk-toolbar ' id='btnListNew'>"+vocab['Grid_new']+"</span>"},
            { type: "<span class='hdk-toolbar' id='btnListAttended'>"+vocab['Grid_being_attended']+"</span>",listener:''},
            { type: "<span class='hdk-toolbar' id='btnListWaiting'>"+btnWaitApp+"</span>",listener:''},
            { type: "<span class='hdk-toolbar' id='btnListFinished'>"+vocab['Grid_finished']+"</span>",listener:''},
            { type: "<span class='hdk-toolbar' id='btnListRejected'>"+vocab['Grid_rejected']+"</span>",listener:''}
        ]
    };
    
    var obj = { 
        width: '100%', 
        height: 530,
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
            var rowIndx = getRowIndx("grid_ticket"),
                row = $("#grid_ticket").pqGrid('getRowData', {rowIndx: rowIndx});
                
                goTicket(row.ticketCode);
        },
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx("grid_ticket"),
                row = $("#grid_ticket").pqGrid('getRowData', {rowIndx: rowIndx});
                
            location.href = path + "/helpdezk/hdkTicket/viewTicket/"+row.ticketCode;
        }
    };

    $("#grid_ticket").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_ticket").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_ticket").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    if(typeUser == 3){
        makeActive("1");
    }else{
        (flgApvRequire > 0) ? makeActive("4") : makeActive("ALL");
    }

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

    // -- show tickets by deadline
    $("#cmbTypeExpireDate").change(function(){
       var deadlineType = $(this).val();
        $("#grid_ticket").pqGrid( "option", "dataModel.postData", function(){
            return {deadlineType:deadlineType};
        });
        
        $("#grid_ticket").pqGrid("refreshDataAndView");
    });

    // -- show tickets by view type
    $("#cmbViewType").change(function(){
        var viewType = $(this).val();
         $("#grid_ticket").pqGrid( "option", "dataModel.postData", function(){
             return {viewType:viewType};
         });
         
         $("#grid_ticket").pqGrid("refreshDataAndView");
     });

    $("#btnAll").click(function(){
        getDataByStatus("ALL","<b>"+vocab['Grid_all_tickets'].toUpperCase()+"</b>");
        makeActive("ALL");
    });

    $("#btnListNew").click(function(){
        getDataByStatus("1","<b>"+vocab['Grid_new_tickets'].toUpperCase()+"</b>");
        makeActive("1");
    });

    $("#btnListAttended").click(function(){
        getDataByStatus("3","<b>"+vocab['Grid_being_attended_tickets'].toUpperCase()+"</b>");
        makeActive("3");
    });

    $("#btnListWaiting").click(function(){
        getDataByStatus("4","<b>"+vocab['Grid_waiting_my_approval_tickets'].toUpperCase()+"</b>");
        makeActive("4");
    });

    $("#btnListFinished").click(function(){
        getDataByStatus("5","<b>"+vocab['Grid_finished_tickets'].toUpperCase()+"</b>");
        makeActive("5");
    });

    $("#btnListRejected").click(function(){
        getDataByStatus("6","<b>"+vocab['Grid_rejected_tickets'].toUpperCase()+"</b>");
        makeActive("6");
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

function goTicket(ticketCode)
{
    if(operatorAsUser == '1'){
        document.location.href = path+"/helpdezk/hdkTicket/viewTicket/" + ticketCode + "/0/1";
    }else{
        document.location.href = path+"/helpdezk/hdkTicket/viewTicket/" + ticketCode;
    }
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

/**
 * Makes active toolbar button
 * 
 * @param  {string} statusID
 * @return {void}      
 */
function makeActive(statusID) {
    switch(statusID){
        case 'ALL':
            if(!$("#btnAll").hasClass("active"))
                $("#btnAll").addClass("active");
        
            if($("#btnListNew").hasClass("active"))
                $("#btnListNew").removeClass("active");
        
            if($("#btnListAttended").hasClass("active"))
                $("#btnListAttended").removeClass("active");
        
            if($("#btnListWaiting").hasClass("active"))
                $("#btnListWaiting").removeClass("active");
        
            if($("#btnListFinished").hasClass("active"))
                $("#btnListFinished").removeClass("active");
        
            if($("#btnListRejected").hasClass("active"))
                $("#btnListRejected").removeClass("active");
            break;
        case '1':
            if($("#btnAll").hasClass("active"))
                $("#btnAll").removeClass("active");
        
            if(!$("#btnListNew").hasClass("active"))
                $("#btnListNew").addClass("active");
        
            if($("#btnListAttended").hasClass("active"))
                $("#btnListAttended").removeClass("active");
        
            if($("#btnListWaiting").hasClass("active"))
                $("#btnListWaiting").removeClass("active");
        
            if($("#btnListFinished").hasClass("active"))
                $("#btnListFinished").removeClass("active");
        
            if($("#btnListRejected").hasClass("active"))
                $("#btnListRejected").removeClass("active");
            break;
        case '3':
            if($("#btnAll").hasClass("active"))
                $("#btnAll").removeClass("active");
        
            if($("#btnListNew").hasClass("active"))
                $("#btnListNew").removeClass("active");
        
            if(!$("#btnListAttended").hasClass("active"))
                $("#btnListAttended").addClass("active");
        
            if($("#btnListWaiting").hasClass("active"))
                $("#btnListWaiting").removeClass("active");
        
            if($("#btnListFinished").hasClass("active"))
                $("#btnListFinished").removeClass("active");
        
            if($("#btnListRejected").hasClass("active"))
                $("#btnListRejected").removeClass("active");
            break;
        case '4':
            if($("#btnAll").hasClass("active"))
                $("#btnAll").removeClass("active");
        
            if($("#btnListNew").hasClass("active"))
                $("#btnListNew").removeClass("active");
        
            if($("#btnListAttended").hasClass("active"))
                $("#btnListAttended").removeClass("active");
        
            if(!$("#btnListWaiting").hasClass("active"))
                $("#btnListWaiting").addClass("active");
        
            if($("#btnListFinished").hasClass("active"))
                $("#btnListFinished").removeClass("active");
        
            if($("#btnListRejected").hasClass("active"))
                $("#btnListRejected").removeClass("active");
            break;
        case '5':
            if($("#btnAll").hasClass("active"))
                $("#btnAll").removeClass("active");
        
            if($("#btnListNew").hasClass("active"))
                $("#btnListNew").removeClass("active");
        
            if($("#btnListAttended").hasClass("active"))
                $("#btnListAttended").removeClass("active");
        
            if($("#btnListWaiting").hasClass("active"))
                $("#btnListWaiting").removeClass("active");
        
            if(!$("#btnListFinished").hasClass("active"))
                $("#btnListFinished").addClass("active");
        
            if($("#btnListRejected").hasClass("active"))
                $("#btnListRejected").removeClass("active");
            break;
        case '6':
            if($("#btnAll").hasClass("active"))
                $("#btnAll").removeClass("active");
        
            if($("#btnListNew").hasClass("active"))
                $("#btnListNew").removeClass("active");
        
            if($("#btnListAttended").hasClass("active"))
                $("#btnListAttended").removeClass("active");
        
            if($("#btnListWaiting").hasClass("active"))
                $("#btnListWaiting").removeClass("active");
        
            if($("#btnListFinished").hasClass("active"))
                $("#btnListFinished").removeClass("active");
        
            if(!$("#btnListRejected").hasClass("active"))
                $("#btnListRejected").addClass("active");
            break;

    }
}

