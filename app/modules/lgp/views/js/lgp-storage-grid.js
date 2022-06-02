$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    datePick = function(elem)
    {
        $(elem).datepicker(datepickerOpts);

        $(elem).mask('00/00/0000');

    };

    var grid = $("#table_list_storage");

    grid.jqGrid({
        url: path+"/lgp/lgpStorage/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'nome', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 20, 25, 30],
       colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('status'),'', ''],
        colModel:[
            {name:'id', index:'idarmazenamento',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'name',index:'nome', editable: true, width:120, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'a.status', editable: true, width:15, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', hidden: true, editable: true, width:10, align:"center", search:false, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'default', index:'default',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
        ],
        pager: "#pager_list_storage",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_storage'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var storageID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lgp/lgpStorage/viewStorage/storageID/" + storageID;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');

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
        },
        loadComplete : function(){
            $(window).trigger('resize');
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_storage')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_storage',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/lgp/lgpStorage/formCreate" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_storage'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            storageID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!storageID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpStorage/formUpdate/storageID/" + storageID;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_storage'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            storageID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!storageID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpStorage/viewStorage/storageID/" + storageID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_storage'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            storageID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if($("#btnEnable").hasClass('active')){
            if(!storageID)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/lgp/lgpStorage/index');
            else
                postStatus(storageID,'A');
        }
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_storage'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            storageID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if($("#btnDisable").hasClass('active')){
            if(!storageID)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/lgp/lgpStorage/index');
            else
                postStatus(storageID,'I');
        }
        
    });



});

function postStatus(storageID,newStatus)
{
    var msgSuccess, msgError;

    if(newStatus == 'A'){
        msgSuccess = makeSmartyLabel('Alert_activated');
        msgError = makeSmartyLabel('Alert_activated_error');
    }else{
        msgSuccess = makeSmartyLabel('Alert_deactivated');
        msgError = makeSmartyLabel('Alert_deactivated_error');
    }

    $.ajax({
        type: "POST",
        url: path + '/lgp/lgpStorage/statusStorage',
        dataType: 'json',
        data: {
            storageID: storageID,
            newstatus: newStatus
        },
        error: function (ret) {
            showAlert(msgError,'danger','');
        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success) {
                showAlert(msgSuccess,'success','');
            } else {
                showAlert(msgError,'danger','');
            }
        }

    });

    return false;
}

function fontColorFormat(cellvalue, options, rowObject) {
    var color = "blue";
    var cellHtml = "<span style='color:" + color + "' originalValue='" + cellvalue + "'>" + cellvalue + "</span>";
    return cellHtml;
}

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}
