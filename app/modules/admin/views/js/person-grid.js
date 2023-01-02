$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Select2
     */
    $('#filter-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});
    $('#action-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});

    var objPerson = {
        addGroup: function(personId,groupId){
            var aGroups = $("#userGroups tbody").find("input"), flgExists=0;
            aGroups.each(function(index, element) {
                if($(this).val() == groupId){
                    modalAlertMultiple('danger',vocab['alert_linked_group_exists'],'alert-modal-user-groups');
                    flgExists = 1;
                    return false;
                }            
            });

            if(flgExists == 0){
                $.post(path+"/admin/person/insertAttendantGroup",{_token: $("#_token").val(), personId: personId, groupId: groupId},
                function(valor){
                    var obj = jQuery.parseJSON(JSON.stringify(valor));
                    if(obj.success){
                        var html = obj.html;
                        if(html.length > 0){
                            $("#userGroups tbody").html(html);
                            
                            if($(".listView").hasClass('d-none'))
                                $(".listView").removeClass('d-none');
                        }
                    }else{
                        $("#userGroups tbody").html('');
                            
                        if(!$(".listView").hasClass('d-none'))
                            $(".listView").addClass('d-none');
                    }
                },
                'json'
            );
            }
        }
    };

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
        { title: vocab["ID"], width: '10%', dataIndx: "idperson", hidden:true, halign: "center"  },
        { title: " ", width: '5%', dataIndx: "personIcon", halign: "center", align: "center"  },
        { title: "<b>"+vocab["Name"]+"</b> ", width: '30%', dataIndx: "name", halign: "center"  },
        { title: "<b>"+vocab["Login"]+"</b> ", width: '10%', dataIndx: "login", align: "center", halign: "center"  },
        { title: "<b>"+vocab["email"]+"</b> ", width: '10%', dataIndx: "email", halign: "center"  },
        { title: "<b>"+vocab["Type"]+"</b> ", width: '10%', dataIndx: "personType", align: "center", halign: "center"  },
        { title: "<b>"+vocab["Company"]+"</b> ", width: '10%', dataIndx: "company", halign: "center"  },
        { title: "<b>"+vocab["Department"]+"</b> ", width: '15%', dataIndx: "department", halign: "center"  },
        { title: "<b>"+vocab["status"]+"</b> ", width: '9%', dataIndx: "status", align: "center", halign: "center"  },
        { title: "", width: '10%', dataIndx: "statusVal", hidden:true, halign: "center"  },
        { title: "", width: '10%', dataIndx: "personTypeId", hidden:true, halign: "center"  }
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
        url: path + '/admin/person/jsonGrid',
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
        title: "<b>"+vocab['people']+"</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        dragColumns: { enabled: false },
        selectChange: function (evt, ui) {
            var rowIndx = getRowIndx("grid_persons"),
                row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx}),
                rowSt = row.statusVal;
                
            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (rowSt == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
        },
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx("grid_persons"),
                row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx});
                
            location.href = path + "/admin/person/formUpdate/"+row.idperson;
        }
    };

    $("#grid_persons").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_persons").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_persons").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val();
            
        $("#grid_persons").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation};
        } );
        
        $("#grid_persons").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val();
            
        $("#grid_persons").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue};
        });
        
        $("#grid_persons").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnCreate").click(function(){
        location.href = path + "/admin/person/formCreate";
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx("grid_persons"),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/admin/person/formUpdate/"+row.idperson;
        }else{
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnEnable").click(function(){
        if(!$("#btnEnable").hasClass('disabled')){
            var rowIndx = getRowIndx("grid_persons"),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.idperson,'A');
            }else{
                msg = vocab['Alert_select_one'];
                showAlert(msg,'warning');
            }
        }      
    });

    $("#btnDisable").click(function(){
        if(!$("#btnDisable").hasClass('enabled')){
            var rowIndx = getRowIndx("grid_persons"),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.idperson,'I');                
            }else{
                msg = vocab['Alert_select_one'];
                showAlert(msg,'warning');
            }
        }
    });

    $("#btnPermissions").click(function(){
        var rowIndx = getRowIndx("grid_persons"),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/admin/person/managePermissions/"+row.idperson;
        }else{
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnGroups").click(function(){
        var rowIndx = getRowIndx("grid_persons"),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_persons").pqGrid('getRowData', {rowIndx: rowIndx});

            if(row.personTypeId != 1 && row.personTypeId != 3){
                showAlert(vocab['Option_only_operator'],'danger','');
            }else{
                if(row.statusVal == 'A') {
                    $("#lblHeader").html(vocab['Groups']+" "+vocab['to']+" "+row.name);
                    $("#personID").val(row.idperson);

                    $.ajax({
                        type: "POST",
                        url: path + "/admin/person/modalAttendantGroups",
                        dataType: 'json',
                        data: {
                            _token: $("#_token").val(),
                            personId: row.idperson
                        },
                        error: function (ret) {
                            showAlert(vocab['Permission_error'],'danger','');
                        },
                        success: function(ret) {
                            var obj = jQuery.parseJSON(JSON.stringify(ret));
                            //console.log(obj);
                            if(obj.success) {
                                var html = obj.html;
                                if(html.length > 0){
                                    $("#userGroups tbody").html(html);
                                    
                                    if($(".listView").hasClass('d-none'))
                                        $(".listView").removeClass('d-none');
                                }

                                $("#modal-select-group").flexdatalist({
                                    visibleProperties: ["company","name"],
                                    searchByWord: true,
                                    searchIn: ["company","name"],
                                    minLength: 2,
                                    selectionRequired: true,
                                    valueProperty: 'id',
                                    textProperty: '{company} - {name}',
                                    url: path + '/admin/person/searchGroup',
                                    noResultsText: vocab['no_result_found_for']+" {keyword}",
                                    requestType: 'post'
                                });
                            
                                $("#modal-select-group").on('select:flexdatalist', function () {
                                    objPerson.addGroup(row.idperson,$(this).val());
                                });

                                $("#modal-user-groups").modal('show');

                            } else {
                                showAlert(vocab['Permission_error'],'danger','');
                            }
                        }
                    });
                } else {
                    showAlert(vocab['Option_only_attendant_active'],'danger','');
                }
            }
        }else{
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });
  
    $("#btnDeleteYes").click(function(){
        
        if(!$("#btnDeleteYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/modules/deleteModule',
                dataType: 'json',
                data: {
                    _token : $("#_token").val(),
                    moduleId: $("#modal-delete-id").val(),
                    path: $("#modal-delete-module-path").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_deleted_error'],'alert-delete-userType');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Alert_deleted'],'alert-delete-module');
                        setTimeout(function(){
                            $('#modal-module-delete').modal('hide');
                            $("#grid_persons").pqGrid("refreshDataAndView");
                        },4000);
                    } else {
                        modalAlertMultiple('danger',obj.message,'alert-delete-module');
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
        var flg = $(this).is(':checked');
        $("#grid_persons").pqGrid( "option", "dataModel.postData", function(){
            return {allRecords:flg};
        });
        
        $("#grid_persons").pqGrid("refreshDataAndView");     
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/person/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });

    $('#modal-user-groups').on('hidden.bs.modal', function() { 
        $("#modal-user-groups-form").trigger('reset');
        $("#userGroups tbody").html('');
        if(!$(".listView").hasClass('d-none'))
            $(".listView").addClass('d-none');
    });

    $('.lbltooltip').tooltip();
});

/**
 * Change person's status
 * @param  {int}userTypeID 
 * @param  {string} newStatus
 * @return {void}      
 */
function postStatus(personId,newStatus)
{
    var msgErr = newStatus == "A" ? vocab['Alert_activated_error'] : vocab['Alert_deactivated_error'],
        msg = newStatus == "A" ? vocab['Alert_activated'] :vocab['Alert_deactivated'];

    $.ajax({
        type: "POST",
        url: path + '/admin/person/changeStatus',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            personId: personId,
            newStatus: newStatus
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
 * en_us Send data to grant/revoke permission
 * pt_br Envia os dados para conceder/remover a permissão
 * 
 * @param  {int}id              Checkbox Id
 * @param  {int} programId      Program to grant or revoke permission
 * @param  {int} accessTypeId   Permission's type to grant or revoke
 * @param  {int} personId       Person to grant or revoke permission
 * @return {void}      
 */
function removeGroup(personId,groupId){
    console.log(personId+" "+groupId);
    $.post(path+"/admin/person/removeAttendantGroup",{_token: $("#_token").val(), personId: personId, groupId: groupId},
        function(valor){
            var obj = jQuery.parseJSON(JSON.stringify(valor));
            if(obj.success){
                var html = obj.html;
                if(html.length > 0){
                    $("#userGroups tbody").html(html);
                    
                    if($(".listView").hasClass('d-none'))
                        $(".listView").removeClass('d-none');
                }else{
                    $("#userGroups tbody").html('');
                    
                if(!$(".listView").hasClass('d-none'))
                    $(".listView").addClass('d-none');
                }
            }else{
                $("#userGroups tbody").html('');
                    
                if(!$(".listView").hasClass('d-none'))
                    $(".listView").addClass('d-none');
            }
        },
        'json'
    );
}

