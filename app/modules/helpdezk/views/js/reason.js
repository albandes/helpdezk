$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_reason");

    grid.jqGrid({
        url: path+"/helpdezk/hdkReason/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'area,type,item,service,reason,status', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 10,
        rowList: [10, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Area'),makeSmartyLabel('type'),makeSmartyLabel('Item'),makeSmartyLabel('Service'),makeSmartyLabel('Reason'),makeSmartyLabel('status'),''],
        colModel:[
            {name:'id', index:'idreason',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'area',index:'area', editable: true, width:120, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'type',index:'type', editable: true, width:60, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'item',index:'item', editable: true, width:120, search:true, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'service',index:'service', editable: true, width:120, search:true, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'reason',index:'reason', editable: true, width:120, search:true, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', editable: true, width:30, search:false, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'statusval', index:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
        ],
        pager: "#pager_list_reason",
        viewrecords: true,
        caption: ' :: '+ makeSmartyLabel('pgr_req_reason'), 
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idreason = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/helpdezk/hdkReason/formUpdateReason/idreason/" + idreason ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'statusval');

            $('#btnEnable').removeClass('disabled').addClass('active').prop('disabled',false);
            $('#btnDisable').removeClass('disabled').addClass('active').prop('disabled',false);
            if (myCellStatus == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled').prop('disabled',true);
            else
                $('#btnDisable').removeClass('active').addClass('disabled').prop('disabled',true);
        },
        loadError : function(xhr,st,err) {
            grid.html("Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText);
        },
        jsonReader : {
            repeatitems: false,
            id: "id"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+ makeSmartyLabel('pgr_req_reason'));

    // Setup buttons
    grid.navGrid('#pager_list_reason',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

    // remove some double elements from one place which we not need double
    var topPagerDiv = $('#' + grid[0].id + '_toppager')[0];         // "#list_toppager"
    $("#search_" + grid[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    $("#refresh_" + grid[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
    $("#" + grid[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"
    //$(".ui-paging-info", topPagerDiv).remove();

    /**
     ** Increase _toppager_left
     ** https://stackoverflow.com/questions/29041956/how-to-place-pager-to-end-of-top-of-toolbar-in-free-jqgrid
     **/
    $(grid['selector']+"_toppager_left").attr("colspan", "4");

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        grid.setGridWidth(width);
    });


    // Buttons
    $("#btnCreate").click(function(){
        location.href = path + "/helpdezk/hdkReason/formCreateReason" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_reason'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idreason = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idreason) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_select_one'));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/helpdezk/hdkReason/formUpdateReason/idreason/" + idreason ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_reason'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idreason = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(idreason){
            postStatus(idreason,'A');
        }

    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_reason'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idreason = myGrid.jqGrid ('getCell', selRowId, 'id');
        if(idreason){
            postStatus(idreason,'I');
        }


    });

    $("#btnDelete").click(function(){
        var myGrid = $('#table_list_reason'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idreason = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idreason) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_select_one'));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        } else {
            $('#idreason_modal').val(idreason);
            $('#modal-dialog-delete').modal('show');
        }
    });

    $("#btnSaveDelete").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkReason/deleteReason",
            dataType: 'json',
            data: {
                idreason: $('#idreason_modal').val(),
                _token: $('#_token').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-reason');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK') {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_deleted'),'alert-delete-reason');
                    setTimeout(function(){
                        $('#modal-dialog-delete').modal('hide');
                        grid.trigger('reloadGrid');
                    },3500);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-reason');
                }
            },
            beforeSend: function(){
                $("#btnSaveDelete").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveDelete").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
            }
        });
    });

});


function postStatus(idreason,newStatus)
{

    var errMsg = newStatus == 'A' ? makeSmartyLabel('Alert_activated_error') : makeSmartyLabel('Alert_deactivated_error');

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkReason/changeReasonStatus/idreason/' + idreason,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            $('#modal-notification').html(errMsg);
            $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkReason/index');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {

                var idreason = obj.idreason, msg = '';
                if(obj.reasonstatus == 'A'){msg = makeSmartyLabel('Alert_activated');}
                else{msg = makeSmartyLabel('Alert_deactivated');}

                $('#modal-notification').html(msg);
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkReason/index');
                $("#tipo-alert").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                $('#modal-notification').html(errMsg);
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkReason/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }
        }

    });

    return false;
}