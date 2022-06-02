$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_emailTemplate");

    grid.jqGrid({
        url: path+"/lgp/lgpEmailTemplate/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'name', //initially sorted on title_topic
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('status'), ''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'name', index:'name', editable: true, width:100, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left"},
            {name:'status_fmt',index:'a.status', editable: true, width:15, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', editable: true, hidden: true, width:30, search:false,  align:"center" },

        ],
        pager: "#pager_list_emailTemplate",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('pgr_erp_emailtemplate'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lgp/lgpEmailTemplate/formUpdate/id/" + myCellData ;
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
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('pgr_erp_emailtemplate'));

    // Setup buttons
    grid.navGrid('#pager_list_groups',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});
    grid.jqGrid('navGrid','#pager_list_groups',{search:true,cloneToTop:true});

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
    $("#btnUpdate").click(function(){
        //console.log('edit');
        var myGrid = $('#table_list_emailTemplate'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            emailTemplateID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!emailTemplateID) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Select_group'));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/lgp/lgpEmailTemplate/formUpdate/id/" + emailTemplateID ;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_emailTemplate'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            emailTemplateID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!emailTemplateID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpEmailTemplate/viewEmailTemplate/id/" + emailTemplateID ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_emailTemplate'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            emailTemplateID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if($("#btnEnable").hasClass('active')){
            if(!emailTemplateID)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/lgp/lgpEmailTemplate/index');
            else
                postStatus(emailTemplateID,'A');
        }
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_emailTemplate'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            emailTemplateID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if($("#btnDisable").hasClass('active')){
            if(!emailTemplateID)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/lgp/lgpEmailTemplate/index');
            else
                postStatus(emailTemplateID,'I');
        }
        
    });

    $("#btnCreate").click(function(){
        location.href = path + "/lgp/lgpEmailTemplate/formCreate/";
    });

    
    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() {
        $('#panelAttendants').addClass('hide');
        $("#panelGrpsService").addClass('hide')
        $("#panelGrpsRepass").addClass('hide')
    });


});

function postStatus(emailTemplateID,newStatus)
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
        url: path + '/lgp/lgpEmailTemplate/statusEmailTemplate',
        dataType: 'json',
        data: {
            idtemplate: emailTemplateID,
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

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

