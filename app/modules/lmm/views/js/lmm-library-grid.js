$(document).ready(function (){

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_library");

    grid.jqGrid({
        url: path+"/lmm/lmmLibrary/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'name', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('status'),'',makeSmartyLabel('Pattern'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'library',index:'name', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status', editable: true, width:20, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'padrao_fmt',index:'default', editable: true, width:20, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'padrao',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_library",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_library'),
        hidegrid: false,
        toppager:false,
        
        ondblClickRow: function(rowId) {
            var libraryID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lmm/lmmLibrary/viewLibrary/libraryID/" + libraryID;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');
            console.log( myCellStatus);
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
            id: "id"  
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_library')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_library',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

    // remove some double elements from one place which we not need double
    var topPagerDiv = $('#' + grid[0].id + '_toppager')[0];         // "#list_toppager"
    $("#search_" + grid[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    $("#refresh_" + grid[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
    $("#" + grid[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"
    

    /**
     ** Increase _toppager_left
     ** https://stackoverflow.com/questions/29041956/how-to-place-pager-to-end-of-top-of-toolbar-in-free-jqgrid
     **/
    $(grid['selector']+"_toppager_left").attr("colspan", "4");

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        grid.setGridWidth(width);
    })  

    //Buttons
    $("#btnCreate").click(function(){
      location.href = path + "/lmm/lmmLibrary/formCreate";
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_library'),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
        libraryID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!libraryID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        }else{
            location.href = path + "/lmm/lmmLibrary/formUpdate/lmmID/" + libraryID;
       }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_library'),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
        libraryID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!libraryID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        }else{
            location.href = path +  "/lmm/lmmLibrary/viewLibrary/lmmID/" + libraryID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_library'),
            selRowId = myGrid.jqGrid('getGridParam', 'selrow'),
            libraryID = myGrid.jqGrid ('getCell',selRowId, 'id');
            
        if(!$("#btnEnable").hasClass("enable")){
            if (!libraryID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
            }else{
                postStatus(libraryID,'A');
            }

        }
            
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_library'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            libraryID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
            if(!$("#btnDisable").hasClass("disable")){
                if (!libraryID) {
                    showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
                }else{
                    postStatus(libraryID,'I');
                }
    
            }       
        
    });

    $("#btnDelete").click(function(){
        var myGrid = $('#table_list_library'),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
        libraryID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!libraryID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        }else{
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmLibrary/checksLibrary',
                dataType: 'json',
                data: {libraryID:libraryID},
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-delete-library');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(obj.message,'danger','');
                        
                    }else{
                        $("#idlibrary_modal").val(libraryID);
                        $("#modal-dialog-delete").modal('show'); }
                }
               
            });
            
        }
    });

    $("#btnSaveDelete").click(function(){
        if(!$("#btnSaveDelete").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmLibrary/deleteLibrary',
                dataType: 'json',
                data: $("#delete_modal_library").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-delete-library');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_deleted'),'alert-delete-library');
                        setTimeout(function(){
                            $('#modal-dialog-delete').modal('hide');
                            grid.trigger('reloadGrid');
                        },3500);
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-library');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveDelete").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveDelete").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        } 
       
    });

    $("#btnPattern").click(function(){
        var myGrid = $('#table_list_library'),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
        libraryID = myGrid.jqGrid ('getCell', selRowId, 'id');
    
        if (!libraryID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        }else{
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmLibrary/padraoLibrary',
                dataType: 'json',
                data: {
                    libraryID: libraryID,
                },
                error: function (ret) {
                    showAlert(msgError,'danger','');
                },
                success: function(ret){
                    console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Successfully_changed'),'success','');
                        setTimeout(function(){
                            myGrid.trigger('reloadGrid');
                        },3500);
                    }else{
                        showAlert(msgError,'danger','');
                    }
                }
        
            });
        }
    });


});

function postStatus(libraryID,newStatus){
    var msgSuccess, msgError;

    if(newStatus == 'A'){
        msgSuccess = makeSmartyLabel('Alert_activated');
        msgError = makeSmartyLabel('Alert_activaded_error');
    }else{
        msgSuccess = makeSmartyLabel('Alert_deactivated');
        msgError = makeSmartyLabel('Alert_deactivaded_error');
    }

    $.ajax({
        type: "POST",
        url: path + '/lmm/lmmLibrary/statusLibrary',
        dataType: 'json',
        data: {
            libraryID:libraryID,
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
            }else{
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

function showAlert(msg, typeAlert,btnOk){

    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#formas-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;

}

