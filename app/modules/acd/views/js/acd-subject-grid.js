$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_subject");

    grid.jqGrid({
        url: path+"/acd/acdSubject/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'nome', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('Abbreviation'), makeSmartyLabel('area_conhecimento'),  makeSmartyLabel('status'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'nome',index:'nome', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'sigla',index:'sigla', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'idareaconhecimento',index:'idareaconhecimento', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status', editable: true, width:10, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_subject",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_subject'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var subID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/acd/acdSubject/viewSubject/subID/" + subID;
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
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_subject')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_subject',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/acd/acdSubject/formCreate";
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_subject'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            subID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!subID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/acd/acdSubject/formUpdate/subID/" + subID;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_subject'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            subID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!subID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
        } else {
            location.href = path + "/acd/acdSubject/viewSubject/subID/" + subID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_subject'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            subID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnEnable").hasClass("disabled")){
            if (!subID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(subID,'A');
            }
        }        
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_subject'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            subID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if(!$("#btnDisabled").hasClass("disabled")){
            if (!subID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(subID,'I');
            }
        }       
        
    });

});

function postStatus(subID,newStatus)
{
    var msgSuccess, msgError;

    //console.log(subID, newStatus)

    if(newStatus == 'A'){
        msgSuccess = makeSmartyLabel('Alert_activated');
        msgError = makeSmartyLabel('Alert_activated_error');
    }else if(newStatus == 'I'){
        msgSuccess = makeSmartyLabel('Alert_deactivated');
        msgError = makeSmartyLabel('Alert_deactivated_error');
    }

    //console.log(subID)

    $.ajax({
        type: "POST",
        url: path + '/acd/acdSubject/statusSubject',
        dataType: 'json',
        data: {
            subID: function(){return subID},
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
