$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    datePick = function(elem)
    {
        $(elem).datepicker(datepickerOpts);

        $(elem).mask('00/00/0000');

    };

    var grid = $("#table_list_operator");

    grid.jqGrid({
        url: path+"/lgp/lgpOperator/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'a.name', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 20, 25, 30, 50],
        colNames:['',
            makeSmartyLabel('Name'),
            makeSmartyLabel('Category'),
            makeSmartyLabel('Contact_person'),
            makeSmartyLabel('Phone'),
            makeSmartyLabel('Mobile_phone'),
            makeSmartyLabel('status'), ''],
        colModel:[
            {name:'id', index:'idperson',editable: false, width:9,sortable: false, search:false, hidden: true },
            {name:'name',index:'operator', editable: true, width:120, search:true, align:"left", sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'idnatureperson',index:'idnatureperson', editable: true, width:25, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'contact_person',index:'contact_person', editable: true, width:40, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'phone_number',index:'phone_number', editable: true, width:30, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'cel_phone',index:'cel_phone', editable: true, width:30, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status', editable: true, width:15, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', hidden: true, editable: true, width:15, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }
        ],
        pager: "#pager_list_operator",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_operator'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var operatorID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lgp/lgpOperator/viewOperator/operatorID/" + operatorID;
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
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_operator')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_operator',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/lgp/lgpOperator/formCreate" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_operator'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            operatorID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!operatorID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpOperator/formUpdate/operatorID/" + operatorID;
        }
        
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_operator'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            operatorID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!operatorID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpOperator/viewOperator/operatorID/" + operatorID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_operator'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            operatorID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if($("#btnEnable").hasClass('active')){
            if(!operatorID)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/lgp/lgpOperator/index');
            else
                postStatus(operatorID,'A');
        }
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_operator'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            operatorID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if($("#btnDisable").hasClass('active')){
            if(!operatorID)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/lgp/lgpOperator/index');
            else
                postStatus(operatorID,'I');
        }
        
    });



});

function postStatus(operatorID,newStatus)
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
        url: path + '/lgp/lgpOperator/statusOperator',
        dataType: 'json',
        data: {
            operatorID: operatorID,
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
