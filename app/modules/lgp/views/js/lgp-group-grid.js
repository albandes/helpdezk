$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_lgpgroup");

    grid.jqGrid({
        url: path+"/lgp/lgpGroup/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'group_name', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [15, 20, 25, 30, 50],
        colNames:['','','',makeSmartyLabel('Name'),makeSmartyLabel('Company'), makeSmartyLabel('status'),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'idcompany',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'idtypeperson',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'group_name',index:'group_name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'company_name',index:'company_name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status', editable: true, width:20, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_lgpgroup",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_lgpgroup'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var groupID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lgp/lgpGroup/viewGroup/groupID/" + groupID;
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
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_lgpgroup')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_lgpgroup',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/lgp/lgpGroup/formCreate";
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_lgpgroup'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            groupID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!groupID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path + "/lgp/lgpGroup/formUpdate/groupID/" + groupID;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_lgpgroup'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            groupID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!groupID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
        } else {
            location.href = path + "/lgp/lgpGroup/viewGroup/groupID/" + groupID;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_lgpgroup'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            groupID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnEnable").hasClass("disabled")){
            if (!groupID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(groupID,'A');
            }
        }        
        
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_lgpgroup'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            groupID = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if(!$("#btnDisabled").hasClass("disabled")){
            if (!groupID) {
                showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '');
            }else{
                postStatus(groupID,'I');
            }
        }       
        
    });

    $("#btnPeopleGroup").click(function(){

        $.ajax({
            type: "POST",
            url: path + "/lgp/lgpGroup/modalPeopleByGroup",
            dataType: 'json',
            error: function (ret) {
                $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                $("#btn-modal-ok").attr("href", path + '/lgp/lgpGroup/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj) {
                    $("#cmbGroups").html(obj.cmblist);
                    $("#cmbGroups").trigger("chosen:updated");

                    $('#modal-form-peoplegroup').modal('show');

                } else {
                    $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                    $("#btn-modal-ok").attr("href", path + '/lgp/lgpGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }
            }
        });
    });

    /*
     *  Chosen
     */
    $("#cmbGroups").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    var objGroups = {
        loadPeopleByGroup: function(){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpGroup/loadPeopleByGroup',
                data: {
                    idgroup : $("#cmbGroups").val(),
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-people-group');
                },
                success: function(ret) {
                    if(ret){
                        $("#tab-peoples").html(ret);

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green',
                        });

                        $('.checkPeopleGroup').on('ifChecked ifUnchecked',function(e){
                            var attdata = e.target.attributes.value.nodeValue;
                            attdata = attdata.split('-');

                            if(e.type == 'ifChecked'){
                                objGroups.setPeopleByGroup(attdata[0],attdata[1],'ADD');
                            }else{
                                objGroups.setPeopleByGroup(attdata[0],attdata[1],'DEL');
                            }
                        });

                        //The panelPeople, where appears the people list, will appears just if the return was true
                        $("#panelPeople").removeClass('hide').addClass('animated fadeInDown');
                    }
                    else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-people-group');
                    }
                },
                beforeSend: function(){

                        if(!$("#panelPeople").hasClass('hide')){
                            $("#panelPeople").addClass('animated fadeOutUp').addClass('hide');
                        }
                        if($("#loaderPanel").hasClass('hide')){
                            $("#loaderPanel").removeClass('hide')
                        }

                        $("#loaderPanel").html("<span style='color:#1ab394;'><i class='fa fa-spinner fa-spin fa-5x'></i></span>");

                },
                complete: function(){
                    $("#loaderPanel").addClass('hide');
                    if($("#panelPeople").hasClass('fadeOutUp')){
                        $("#panelPeople").removeClass('fadeOutUp');
                    }
                    
                }
            });

        },
        setPeopleByGroup: function(idPerson,idGroup,action){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpGroup/setPeopleByGroup',
                data: {
                    idperson : idPerson,
                    idgroup : idGroup,
                    action : action,
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-people-group');
                },
                success: function(ret) {
                    if(!ret){
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-people-group');
                    }
                }
            });
        },
    }


    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() {
        $('#panelPeople').addClass('hide');
    });


    $('#cmbGroups').change(function(){
        objGroups.loadPeopleByGroup();
    });

});

function postStatus(groupID,newStatus)
{
    var msgSuccess, msgError;


    if(newStatus == 'A'){
        msgSuccess = makeSmartyLabel('Alert_activated');
        msgError = makeSmartyLabel('Alert_activated_error');
    }else if(newStatus == 'I'){
        msgSuccess = makeSmartyLabel('Alert_deactivated');
        msgError = makeSmartyLabel('Alert_deactivated_error');
    }

    //console.log(groupID, newStatus);

    $.ajax({
        type: "POST",
        url: path + "/lgp/lgpGroup/statusGroup",
        dataType: "json",
        data: {
            groupID: groupID,
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
