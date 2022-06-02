$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_hurrole");

    grid.jqGrid({
        url: path+"/hur/hurRole/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'a.description', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [15, 20, 25, 30, 50],
        colNames:['','',makeSmartyLabel('role'), makeSmartyLabel('Area'), makeSmartyLabel('status'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'idarea',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'rolename',index:'rolename', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'areaname',index:'areaname', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status', editable: true, width:20, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_hurrole",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_hurrole'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var roleID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/hur/hurRole/viewRole/roleID/" + roleID;
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
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_hurrole')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_hurrole',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/hur/hurRole/formCreate";
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_hurrole'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            roleID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!roleID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/hur/hurRole/formUpdate/roleID/" + roleID;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_hurrole'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            roleID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!roleID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
        } else {
            location.href = path + "/hur/hurRole/viewRole/roleID/" + roleID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_hurrole'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            roleID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnEnable").hasClass("disabled")){
            if (!roleID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(roleID,'A');
            }
        }        
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_hurrole'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            roleID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if(!$("#btnDisabled").hasClass("disabled")){
            if (!roleID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(roleID,'I');
            }
        }       
        
    });

});

function postStatus(roleID,newStatus)
{
    var msgSuccess, msgError;

    if(newStatus == 'A'){
        msgSuccess = makeSmartyLabel('Alert_activated');
        msgError = makeSmartyLabel('Alert_activated_error');
    }else if(newStatus == 'I'){
        msgSuccess = makeSmartyLabel('Alert_deactivated');
        msgError = makeSmartyLabel('Alert_deactivated_error');
    }

    //console.log(areaID)

    $.ajax({
        type: "POST",
        url: path + '/hur/hurRole/statusRole',
        dataType: 'json',
        data: {
            roleID: function(){return roleID},
            newStatus: function(){return newStatus}
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
