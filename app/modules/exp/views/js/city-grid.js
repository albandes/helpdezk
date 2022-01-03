$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Select2
     */
    $('#filter-list').select2({dropdownParent: $(this).find('.modal-body-filters')});
    $('#action-list').select2({dropdownParent: $(this).find('.modal-body-filters')});

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
        { title: translateLabel("ID"), width: '10%', dataIndx: "idcity", hidden:true, halign: "center"  },        
        { title: translateLabel("Name"), width: '50%', dataIndx: "city", halign: "center"  },
        { title: translateLabel("uf"), width: '10%', dataIndx: "uf", align: "center", halign: "center"  },
        { title: translateLabel("city_foundation"), width: '20%', dataIndx: "dtfoundation", align: "center", halign: "center"  },
        { title: translateLabel("status"), width: '10%', dataIndx: "status", align: "center", halign: "center"  },
        { title: "", width: '10%', dataIndx: "status_val", hidden:true, halign: "center"  },
        { title: translateLabel("Default"), width: '10%', dataIndx: "default", align: "center", halign: "center"  },
        { title: "", width: '10%', dataIndx: "default_val", hidden:true, halign: "center"  },
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
        url: path + '/exp/expCity/jsonGrid',
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
        sorter:[ { dataIndx: "city", dir: "up" } ]
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
        height: 400,
        dataModel: dataModel,
        colModel: colM,
        editable: false,
        title: translateLabel('pgr_exp_city'),
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        selectChange: function (evt, ui) {
            var rowIndx = getRowIndx(),
                row = $("#grid_cities").pqGrid('getRowData', {rowIndx: rowIndx}),
                rowSt = row.status_val;
                
            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (rowSt == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
        }
    };

    $("#grid_cities").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_cities").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_cities").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val();
            
        $("#grid_cities").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation};
        } );
        
        $("#grid_cities").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val();
            
        $("#grid_cities").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue};
        });
        
        $("#grid_cities").pqGrid("refreshDataAndView");
    });

    $("#btnCreate").click(function(){
        location.href = path + "/exp/expCity/formCreate";
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx(),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_cities").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/exp/expCity/formUpdate/"+row.idcity;
        }else{
            msg = translateLabel('Alert_select_one');
            showAlert(msg,'warning');
        }
    });

    $("#btnEnable").click(function(){
        if(!$("#btnEnable").hasClass('disabled')){
            var rowIndx = getRowIndx(),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_cities").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.idcity,'A');
            }else{
                msg = translateLabel('Alert_select_one');
                showAlert(msg,'warning');
            }
        }        
    });

    $("#btnDisable").click(function(){
        if(!$("#btnDisable").hasClass('disabled')){
            var rowIndx = getRowIndx(),msg="";
        
            if (rowIndx != null) {
                var row = $("#grid_cities").pqGrid('getRowData', {rowIndx: rowIndx});
                postStatus(row.idcity,'I');
            }else{
                msg = translateLabel('Alert_select_one');
                showAlert(msg,'warning');
            }
        }
    });

    $("#btnDelete").click(function(){
        var rowIndx = getRowIndx(),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_cities").pqGrid('getRowData', {rowIndx: rowIndx});
            
            $("#delete-id").val(row.idcity);
            $("#delete-uf").val(row.uf);
            $("#delete-description").val(row.city);
            $("#delete-date").val(row.dtfoundation);
            $("#modal-city-delete").modal('show');
        }else{
            msg = translateLabel('Alert_select_one');
            showAlert(msg,'warning');
        }
    });

    $("#btnDeleteYes").click(function(){
        
        if(!$("#btnDeleteYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/exp/expCity/deleteCity',
                dataType: 'json',
                data: {
                    _token : $("#_token").val(),
                    cityID: $("#delete-id").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',translateLabel('Alert_deleted_error'),'alert-delete-city');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',translateLabel('Alert_deleted'),'alert-delete-city');
                        setTimeout(function(){
                            $('#modal-city-delete').modal('hide');
                            $("#grid_cities").pqGrid("refreshDataAndView");
                        },4000);
                    } else {
                        modalAlertMultiple('danger',translateLabel('Alert_deleted_error'),'alert-delete-city');
                    }
                },
                beforeSend: function(){
                    $("#btnDeleteYes").html("<i class='fa fa-spinner fa-spin'></i> "+ translateLabel('Processing')).addClass('disabled');
                    $("#btnDeleteNo").addClass('disabled');
                },
                complete: function(){
                    $("#btnDeleteYes").html("<i class='fa fa-check'></i> "+ translateLabel('Yes')).removeClass('disabled');
                    $("#btnDeleteNo").removeClass('disabled');
                }
    
            });
        }

    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/exp/expCity/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');        
    });
});

/**
 * Returns ID of the row selected
 * 
 * @returns mixed
 */
function getRowIndx() {
    var arr = $("#grid_cities").pqGrid("selection", { type: 'row', method: 'getSelection' });
    
    if (arr && arr.length > 0) {
        return arr[0].rowIndx;                                
    }
    else {
        return null;
    }
}

/**
 * Change city's status
 * @param  {Number} cityID 
 * @param  {String} newStatus
 * @return {void}      
 */
function postStatus(cityID,newStatus)
{
    var msgErr = newStatus == "A" ? translateLabel('Alert_activated_error') : translateLabel('Alert_deactivated_error'),
        msg = newStatus == "A" ? translateLabel('Alert_activated') : translateLabel('Alert_deactivated');

    $.ajax({
        type: "POST",
        url: path + '/exp/expCity/changeStatus',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            cityID: cityID,
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

