$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_lgppersonac");

    grid.jqGrid({
        url: path+"/lgp/lgpPersonAccess/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'personac_name', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [15, 20, 25, 30, 50],
        colNames:['','',makeSmartyLabel('Name'),makeSmartyLabel('cpf'), makeSmartyLabel('telephone_number'),makeSmartyLabel('cellphone_number'),makeSmartyLabel('status'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'idtypeperson',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'personac_name',index:'personac_name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'personac_cpf',index:'personac_cpf', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'personac_telephone',index:'personac_telephone', editable: true, width:100, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'personac_cellphone',index:'personac_cellphone', editable: true, width:100, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status', editable: true, width:25, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_lgppersonac",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_lgppersonac'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var personacID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lgp/lgpPersonAccess/viewPersonac/personacID/" + personacID;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');

            //console.log(myCellStatus)

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
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_lgppersonac')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_lgppersonac',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/lgp/lgpPersonAccess/formCreate";
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_lgppersonac'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            personacID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!personacID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpPersonAccess/formUpdate/personacID/" + personacID;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_lgppersonac'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            personacID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!personacID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
        } else {
            location.href = path + "/lgp/lgpPersonAccess/viewPersonac/personacID/" + personacID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_lgppersonac'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            personacID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnEnable").hasClass("disabled")){
            if (!personacID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(personacID,'A');
            }
        }        
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_lgppersonac'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            personacID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if(!$("#btnDisabled").hasClass("disabled")){
            if (!personacID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(personacID,'I');
            }
        }       
        
    });

});

function postStatus(personacID,newStatus)
{
    var msgSuccess, msgError;


    if(newStatus == 'A'){
        msgSuccess = makeSmartyLabel('Alert_activated');
        msgError = makeSmartyLabel('Alert_activated_error');
    }else if(newStatus == 'I'){
        msgSuccess = makeSmartyLabel('Alert_deactivated');
        msgError = makeSmartyLabel('Alert_deactivated_error');
    }

    $.ajax({
        type: "POST",
        url: path + "/lgp/lgpPersonAccess/statusPersonac",
        dataType: "json",
        data: {
            personacID: personacID,
            newStatus: newStatus
        },
        error: function (ret) {
            showAlert(msgError,'danger','');
        },
        success: function(ret){
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
