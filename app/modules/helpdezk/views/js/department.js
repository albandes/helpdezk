$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_department");

    grid.jqGrid({
        url: path+"/helpdezk/hdkDepartment/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'company,department', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 16,
        rowList: [16, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Company'),makeSmartyLabel('Department'),makeSmartyLabel('status'),''],
        colModel:[
            {name:'id', index:'iddepartment',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'company',index:'company', editable: true, width:120, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'department',index:'department', editable: true, width:120, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', editable: true, width:20, align:"center", search:false, sorttype:'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'statusval', index:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
        ],
        pager: "#pager_list_department",
        viewrecords: true,
        caption: ' :: '+ makeSmartyLabel('pgr_departments'), 
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var iddepartment = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/helpdezk/hdkDepartment/formUpdateDepartment/iddepartment/" + iddepartment ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'statusval');

            $('#btnEnable').removeClass('disabled').addClass('active').prop('disabled',false);
            $('#btnDisable').removeClass('disabled').addClass('active').prop('disabled',false);
            if (myCellStatus == 'A')
                $('#btnEnable').removeClass('active').addClass('disabled').prop('disabled',true);
            else
                $('#btnDisable').removeClass('active').addClass('disabled').prop('disabled',true);
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
    grid.jqGrid('setCaption', ' :: '+ makeSmartyLabel('pgr_departments'));

    // Setup buttons
    grid.navGrid('#pager_list_department',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/helpdezk/hdkDepartment/formCreateDepartment" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_department'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            iddepartment = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!iddepartment) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/helpdezk/hdkDepartment/formUpdateDepartment/iddepartment/" + iddepartment ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_department'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            iddepartment = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnEnable").hasClass('disabled'))
            postStatus(iddepartment,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_department'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            iddepartment = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnDisable").hasClass('disabled'))
            postStatus(iddepartment,'I');

    });

    $("#btnDelete").click(function(){
        var myGrid = $('#table_list_department'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            iddepartment = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!iddepartment) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkDepartment/modalDelete',
                data:{_token: $('#_token').val(),departmentId:iddepartment},
                dataType: 'json',
                error: function (ret) {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                },
                success: function(ret) {

                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.hasPerson == '1') {
                        $("#cmbDepartment").html(obj.departmentList);
                        $("#cmbDepartment").trigger("chosen:updated");
                        $("#companyName").html(obj.companyName);
                        $("#has_person").val(obj.hasPerson);
                        $("#depHasPersonLine").removeClass('hide');
                    }else{
                        $("#has_person").val('');
                        $("#cmbDepartment").html('');
                        $("#cmbDepartment").trigger("chosen:updated");
                        $("#companyName").html('');
                        $("#depHasPersonLine").addClass('hide');
                    }
                }
            });
            $('#iddepartment_modal').val(iddepartment);
            $("#btnCloseDelete").removeClass('disabled');
            $("#btnSaveDelete").removeClass('disabled');
            $('#modal-dialog-delete').modal('show');
        }
    });

    $("#btnSaveDelete").click(function(){
        if (!$("#delete_department_form").valid()) {
            return false ;
        }

        if(!$("#btnSaveDelete").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkDepartment/deleteDepartment",
                dataType: 'json',
                data: {
                    iddepartment: $('#iddepartment_modal').val(),
                    _token: $('#_token').val(),
                    hasperson: $('#has_person').val(),
                    newdepartment:  $('#cmbDepartment').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-department');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK') {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_deleted'),'alert-delete-department');
                        setTimeout(function(){
                            $('#modal-dialog-delete').modal('hide');
                            grid.trigger('reloadGrid');
                        },3500);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-department');
                    }
                },
                beforeSend: function(){
                    $("#btnCloseDelete").addClass('disabled');
                    $("#btnSaveDelete").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnCloseDelete").removeClass('disabled');
                    $("#btnSaveDelete").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Yes'));
                }
            });
        }

    });

    /*
     *  Chosen
     */
    $("#cmbDepartment").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    $("#delete_department_form").validate({
        ignore:[],
        rules: {
            cmbDepartment: {required:function(element) {
                return $("#has_person").val() == 1;
            }}
        },
        messages: {
            cmbDepartment: {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    /* clean modal's fields */
    $('#delete_department_form').on('hidden.bs.modal', function() {
        $('#delete_department_form').trigger('reset');
        $("#cmbDepartment").html('');
        $("#cmbDepartment").trigger("chosen:updated");
        $("#depHasPersonLine").addClass('hide');
        $("#btnCloseDelete").removeClass('disabled');
        $("#btnSaveDelete").removeClass('disabled');
    });
});

function postStatus(iddepartment,newStatus)
{
    var errMsg = newStatus == 'A' ? makeSmartyLabel('Alert_activated_error') : makeSmartyLabel('Alert_deactivated_error');

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkDepartment/changeDepartmentStatus/iddepartment/' + iddepartment,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            showAlert(errMsg,'danger',path + '/helpdezk/hdkDepartment/index');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                var iddepartment = obj.iddepartment, msg = '';
                if(obj.departmentstatus == 'A'){msg = makeSmartyLabel('Alert_activated');}
                else{msg = makeSmartyLabel('Alert_deactivated');}

                showAlert(msg,'success',path + '/helpdezk/hdkDepartment/index');
            } else {
                showAlert(errMsg,'danger',path + '/helpdezk/hdkDepartment/index');
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