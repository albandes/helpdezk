$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_request_email");

    grid.jqGrid({
        url: path+"/helpdezk/hdkRequestEmail/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'idgetemail', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 10,
        rowList: [10, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Server'),makeSmartyLabel('Type'),makeSmartyLabel('email')],
        colModel:[
            {name:'id', index:'idgetemail',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'serverurl',index:'serverurl', editable: true, width:120, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'servertype',index:'servertype', editable: true, width:60, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'user',index:'user', editable: true, width:120, search:true, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }
        ],
        pager: "#pager_list_request_email",
        viewrecords: true,
        caption: ' :: '+ makeSmartyLabel('pgr_email_request'), 
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idgetemail = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/helpdezk/hdkRequestEmail/formUpdateRequestEmail/idgetemail/" + idgetemail ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'statusval');

            $('#btnEnable').removeClass('disabled').addClass('active');
            $('#btnDisable').removeClass('disabled').addClass('active');
            if (myCellStatus == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled');
            else
                $('#btnDisable').removeClass('active').addClass('disabled');
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
    grid.jqGrid('setCaption', ' :: '+ makeSmartyLabel('pgr_email_request'));

    // Setup buttons
    grid.navGrid('#pager_list_request_email',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/helpdezk/hdkRequestEmail/formCreateRequestEmail" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_request_email'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idgetemail = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idgetemail) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_select_one'));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/helpdezk/hdkRequestEmail/formUpdateRequestEmail/idgetemail/" + idgetemail ;
        }
    });

    $("#btnDelete").click(function(){
        var myGrid = $('#table_list_request_email'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idgetemail = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idgetemail) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_select_one'));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        } else {
            $('#idgetemail_modal').val(idgetemail);
            $('#modal-dialog-delete').modal('show');
        }
    });

    $("#btnSaveDelete").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkRequestEmail/deleteRequestEmail",
            dataType: 'json',
            data: {
                idgetemail: $('#idgetemail_modal').val(),
                _token: $('#_token').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-request-email');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK') {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_deleted'),'alert-delete-request-email');
                    setTimeout(function(){
                        $('#modal-dialog-delete').modal('hide');
                        grid.trigger('reloadGrid');
                    },3500);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-request-email');
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
