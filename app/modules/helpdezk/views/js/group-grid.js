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
    $('#modal-cmb-group').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-group-attendants-form')});
    $('#modal-cmb-area').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-group-service-form')});
    $('#modal-cmb-type').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-group-service-form')});
    $('#modal-cmb-item').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-group-service-form')});
    $('#modal-cmb-service').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-group-service-form')});

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
        { title: vocab["ID"], width: '10%', dataIndx: "idgroup", hidden:true, halign: "center"  },        
        { title: "<b>"+vocab["Name"]+"</b> ", width: '40%', dataIndx: "name", halign: "center"  },
        { title: "<b>"+vocab["Attend_level"]+"</b> ", width: '20%', dataIndx: "level", align: "center", halign: "center"  },        
        { title: "<b>"+vocab["Company"]+"</b> ", width: '30%', dataIndx: "company", align: "center", halign: "center"  },
        { title: "<b>"+vocab["status"]+"</b> ", width: '10%', dataIndx: "status", align: "center", halign: "center"  },
        { title: "", width: '10%', dataIndx: "status_val", hidden:true, halign: "center"  }
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
        url: path + '/helpdezk/hdkGroup/jsonGrid',
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
        rPPOptions: [10, 20, 30, 40, 50]
    };
    
    var obj = { 
        width: '100%', 
        height: 500,
        wrap: false,
        dataModel: dataModel,
        colModel: colM,
        editable: false,
        title: "<b>"+vocab['pgr_groups']+"</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        selectChange: function (evt, ui) {
            var rowIndx = getRowIndx('grid_groups'),
                row = $("#grid_groups").pqGrid('getRowData', {rowIndx: rowIndx}),
                rowSt = row.status_val;
                
            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (rowSt == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
        },
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx('grid_groups'),
                row = $("#grid_groups").pqGrid('getRowData', {rowIndx: rowIndx});
                
            location.href = path + "/helpdezk/hdkGroup/formUpdate/"+row.idgroup;
        }
    };

    $("#grid_groups").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_groups").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_groups").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val(), 
            flg = $("#viewInactive").is(':checked');
            
        $("#grid_groups").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation,allRecords:flg};
        } );
        
        $("#grid_groups").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val(), flg = $("#viewInactive").is(':checked');
            
        $("#grid_groups").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue,allRecords:flg};
        });
        
        $("#grid_groups").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnCreate").click(function(){
        location.href = path + "/helpdezk/hdkGroup/formCreate";
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx('grid_groups'),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_groups").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/helpdezk/hdkGroup/formUpdate/"+row.idgroup;
            
        }else{            
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnEnable").click(function(){
        if(!$("#btnEnable").hasClass('disabled')){
            var rowIndx = getRowIndx('grid_groups'),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_groups").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.idgroup,'A');
            }else{
                msg = vocab['Alert_select_one'];
                showAlert(msg,'warning');
            }
        }      
    });

    $("#btnDisable").click(function(){
        if(!$("#btnDisable").hasClass('enabled')){
            var rowIndx = getRowIndx('grid_groups'),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_groups").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.idgroup,'I');
            }else{
                msg = vocab['Alert_select_one'];
                showAlert(msg,'warning');
            }
        }
    });

    $("#btnGrpAttendants").click(function(){
        $("#modal-group-attendants").modal("show");
    });

    $("#btnGrpServices").click(function(){
        $("#modal-group-service").modal("show");
    });

    $('#modal-cmb-group').change(function(){
        var groupVal = $(this).val();
        if(groupVal && groupVal != ''){
            loadGroupAttendants($(this).val());
        }        
    });

    $('#viewInactive').on('click', function() {
        var quickValue = $("#txtSearch").val(),
            quickSearch = quickValue == '' ? false :true, 
            flg = $(this).is(':checked');

        $("#grid_groups").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:quickSearch,quickValue:quickValue,allRecords:flg};
        });
        
        $("#grid_groups").pqGrid("refreshDataAndView");     
    });

    $("#modal-attendant").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: ["name"],
        minLength: 2,
        selectionRequired: true,
        valueProperty: 'id',
        textProperty: '{name}',
        url: path + '/helpdezk/hdkGroup/searchAttendant',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post'
    });

    $("#modal-attendant").on('select:flexdatalist', function (event, set, options) {
        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkGroup/addGroupAttendant',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                groupId: $("#modal-cmb-group").val(),
                attendantId: $(this).val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-group-attendants');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',obj.message,'alert-modal-group-attendants');
                    loadGroupAttendants($("#modal-cmb-group").val());
                    $("#modal-attendant").val("");
                    $("#modal-attendant-flexdatalist").val("");
                } else {
                    modalAlertMultiple('danger',obj.message,'alert-modal-group-attendants');
                }
            }    
        });
    
        return false;
    });

    $("#modal-cmb-area").change(function(){
        if($(this).val() != 'X'){
            objGroup.loadType();
        }
    });

    $("#modal-cmb-type").change(function(){
        if($(this).val() != 'X'){
            objGroup.loadItem();
        }
    });

    $("#modal-cmb-item").change(function(){
        if($(this).val() != 'X'){
            objGroup.loadService();
        }
    });

    $("#modal-cmb-service").change(function(){
        var serviceId = $(this).val();
        if(serviceId != 'X' && serviceId){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkGroup/loadServiceGroups',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    serviceId: serviceId
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-group-service');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        if($(".groupListView").hasClass('d-none'))
                            $(".groupListView").removeClass('d-none');
                        
                        $("#groupList").html(obj.html);
                    } else {
                        modalAlertMultiple('danger',obj.message,'alert-modal-group-service');
                    }
                },
                beforeSend: function(){
                    if(!$(".groupListView").hasClass('d-none'))
                        $(".groupListView").addClass('d-none');
                }
        
            });
        
            return false;
        }
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkGroup/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });

    $('#modal-group-attendants').on('hidden.bs.modal', function() { 
        $("#modal-group-attendants-form").trigger('reset');
        $("#modal-cmb-group").trigger("change");
        
        if(!$(".attendantsListView").hasClass('d-none'))
            $(".attendantsListView").addClass('d-none');
    });

    $('#modal-group-service').on('hidden.bs.modal', function() { 
        $("#modal-group-service-form").trigger('reset');
        $("#modal-cmb-area").val("X");
        $("#modal-cmb-area").change();
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
        url: path + '/helpdezk/hdkGroup/changeStatus',
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

/**
 * loadGroupAttendants
 * 
 * en_us Displays group's attendants
 * pt_br Exibe os atendentes do grupo
 * 
 * @param  {int}groupId
 * @return {void}      
 */
function loadGroupAttendants(groupId)
{
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkGroup/loadGroupAttendants',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            groupId: groupId
        },
        error: function (ret) {
            modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-group-attendants');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success) {
                if($(".attendantsListView").hasClass('d-none'))
                    $(".attendantsListView").removeClass('d-none');
                
                $("#attendantList tbody").html(obj.html);
            } else {
                modalAlertMultiple('danger',obj.message,'alert-modal-group-attendants');
            }
        },
        beforeSend: function(){
            if(!$(".attendantsListView").hasClass('d-none'))
                $(".attendantsListView").addClass('d-none');
        }

    });

    return false;
}

/**
 * removeGroupAttendant
 * 
 * en_us Displays group's attendants
 * pt_br Exibe os atendentes do grupo
 * 
 * @param  {string}id
 * @param  {int}attendantId
 * @param  {int}groupId
 * @return {void}      
 */
function removeGroupAttendant(id,attendantId,groupId)
{
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkGroup/removeGroupAttendant',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            attendantId: attendantId,
            groupId: groupId
        },
        error: function (ret) {
            modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-group-attendants');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success) {
                modalAlertMultiple('success',obj.message,'alert-modal-group-attendants');
                loadGroupAttendants($("#modal-cmb-group").val());
            } else {
                modalAlertMultiple('danger',obj.message,'alert-modal-group-attendants');
            }
        },
        beforeSend: function(){
            $("#"+id).html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
        }

    });

    return false;
}

