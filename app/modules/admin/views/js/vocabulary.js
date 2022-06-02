$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var searchCmbLocale = $.ajax({
        type: "POST",
        url: path+"/admin/vocabulary/ajaxSearchCmbLocale",
        async: false,
        dataType: 'json'
    }).responseJSON;

    var searchCmbModule = $.ajax({
        type: "POST",
        url: path+"/admin/vocabulary/ajaxSearchCmbModule",
        async: false,
        dataType: 'json'
    }).responseJSON;

    var grid = $("#table_list_vocabulary");

    grid.jqGrid({
        url: path+"/admin/vocabulary/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'key_name', //initially sorted on vocabulary key name
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('vocabulary_locale'),makeSmartyLabel('Module'),makeSmartyLabel('vocabulary_key_name'),makeSmartyLabel('vocabulary_key_value'),makeSmartyLabel('status'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'locale',index:'locale_name',editable: false, width:60, align:"center",sortable: true, search:true, hidden: false, sorttype: 'string', searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], value:searchCmbLocale.data} },
            {name:'module',index:'module_name', editable: true, width:80, search:true, sorttype: 'string', stype:'select', searchoptions: { sopt: ['eq'], value:searchCmbModule.data} },
            {name:'key_name',index:'key_name', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'key_value',index:'key_value', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'a.status', editable: true, width:40, search:false, align:"center", sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_vocabulary",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('people'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idvocabulary = grid.jqGrid('getCell', rowId, 'id');
            if(access[2] != "Y"){
                showAlert(makeSmartyLabel('no_permission_edit'),'danger',path + '/admin/vocabulary/index');
            }else{
                location.href = path + "/admin/vocabulary/formUpdate/idvocabulary/" + idvocabulary ;
            }
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
            console.log(xhr);
            grid.html("Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText);
        },
        jsonReader : {
            repeatitems: false,
            id: "id"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('pgr_vocabulary'));

    // Setup buttons
    grid.navGrid('#pager_list_vocabulary',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/admin/vocabulary/formCreate" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_vocabulary'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idvocabulary = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idvocabulary) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/admin/vocabulary/index');
        } else {
            location.href = path + "/admin/vocabulary/formUpdate/idvocabulary/" + idvocabulary ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_vocabulary'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idvocabulary = myGrid.jqGrid ('getCell', selRowId, 'id');

        if($("#btnEnable").hasClass('active')){
            if(!idvocabulary)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/admin/vocabulary/index');
            else
                postStatus(idvocabulary,'A');
        }

    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_vocabulary'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idvocabulary= myGrid.jqGrid ('getCell', selRowId, 'id');

        if($("#btnDisable").hasClass('active')){
            if(!idvocabulary)
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/admin/vocabulary/index');
            else
                postStatus(idvocabulary,'I');
        }
    });

    $("#btnUpdVocab").click(function(){

        if(!$("#btnUpdVocab").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/home/updateLanguageFile',
                dataType: 'json',
                data: { action: 'write'},
                error: function (ret) {
                    showAlert(makeSmartyLabel('generic_error_msg'),'danger','');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('success_language_file_generated'),'success','');
                    } else {
                        showAlert(makeSmartyLabel('error_language_file_generated')+obj.message,'');
                    }    
                },
                beforeSend: function(){
                    $("#btnUpdVocab").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdVocab").html("<i class='fas fa-sync-alt'></i> "+ makeSmartyLabel('Update_vocabulary')).removeClass('disabled');
                }
            });
        }

    });

});

function postStatus(idvocabulary,newStatus){
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
        url: path + '/admin/vocabulary/changeStatus',
        dataType: 'json',
        data: {
            _token: $('#_token').val(),
            idvocabulary: idvocabulary,
            newstatus: newStatus
        },
        error: function (ret) {
            showAlert(msgError,'danger','');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status) {
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