$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_email_confs");

    grid.jqGrid({
        url: path+"/helpdezk/hdkEmailConfig/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'name', //initially sorted on title_topic
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('status'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'name', index:'name', editable: true, width:100, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left"},
            {name:'statuslbl',index:'status', editable: true, width:30, search:false,  align:"center" },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },

        ],
        pager: "#pager_list_email_confs",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('pgr_erp_emailtemplate'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/helpdezk/hdkEmailConfig/formUpdateTemplate/id/" + myCellData ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');

            if (myCellStatus == 'A'){
                $('#btnDisable').removeClass('disabled');
                $('#btnEnable').addClass('disabled');
            }
            else{
                $('#btnDisable').addClass('disabled');
                $('#btnEnable').removeClass('disabled');
            }

        },
        loadError : function(xhr,st,err) {
            grid.html("Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText);
        },
        jsonReader : {
            repeatitems: false,
            id: "idconfig"   // row ID
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
        var myGrid = $('#table_list_email_confs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idConfig = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idConfig) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Select_group'));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/helpdezk/hdkEmailConfig/formUpdateTemplate/id/" + idConfig ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_email_confs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idConfig = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idConfig,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_email_confs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idConfig= myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idConfig,'N');
    });

    $("#btnCreate").click(function(){
        location.href = path + "/helpdezk/hdkEmailConfig/formCreateTemplate/";
    });

    
    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() {
        $('#panelAttendants').addClass('hide');
        $("#panelGrpsService").addClass('hide')
        $("#panelGrpsRepass").addClass('hide')
    });


});

function postStatus(idConfig,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkEmailConfig/changeStatus',
        dataType: 'json',
        data: {
            idconfig: idConfig,
            newstatus: newStatus,
            _token: $('#_token').val()
        },
        error: function (ret) {
            showAlert(makeSmartyLabel('Alert_failure'),'danger','');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.status == 'OK' ) {
                showAlert(makeSmartyLabel('Alert_success_update'),'success','');
            } else {
                showAlert(makeSmartyLabel('Alert_failure'),'danger','');
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

