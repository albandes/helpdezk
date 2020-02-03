$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_groups");

    grid.jqGrid({
        url: path+"/helpdezk/hdkGroup/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'tbp.name', //initially sorted on title_topic
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('Attend_level'),makeSmartyLabel('Company'),makeSmartyLabel('status'),''],
        colModel:[
            {name:'idgroup',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'name', index:'tbp.name', editable: true, width:100, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left"},
            {name:'level',index:'tbg.level', editable: true, width:40, search:false,  align:"center" },
            {name:'company',index:'tbp2.name', editable: true, width:100, search:false,  align:"center" },
            {name:'statuslbl',index:'tbg.status', editable: true, width:30, search:false,  align:"center" },
            {name:'status',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },

        ],
        pager: "#pager_list_groups",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('pgr_groups'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'idgroup');
            location.href = path + "/helpdezk/hdkGroup/formUpdateGroup/idgroup/" +  myCellData;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'idgroup');
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
            id: "code_request"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('pgr_groups'));

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
    $("#btnCreate").click(function(){
        location.href = path + "/helpdezk/hdkGroup/formCreateGroup" ;
    });

    $("#btnUpdate").click(function(){
        //console.log('edit');
        var myGrid = $('#table_list_groups'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGroup = myGrid.jqGrid ('getCell', selRowId, 'idgroup');

        if (!idGroup) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Select_group'));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/helpdezk/hdkGroup/formUpdateGroup/idgroup/" + idGroup ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_groups'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGroup = myGrid.jqGrid ('getCell', selRowId, 'idgroup');

        postStatus(idGroup,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_groups'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGroup = myGrid.jqGrid ('getCell', selRowId, 'idgroup');

        postStatus(idGroup,'N');
    });

    $("#btnAttGroup").click(function(){

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkGroup/modalAttendantByGroup",
            dataType: 'json',
            error: function (ret) {
                $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $("#cmbGroups").html(obj.cmblist);
                    $("#cmbGroups").trigger("chosen:updated");

                    $('#modal-form-attendantgrp').modal('show');

                } else {
                    $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }
            }
        });
    });

    $("#btnGrpService").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkGroup/modalGroupsByService",
            dataType: 'json',
            error: function (ret) {
                $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $("#cmbServices").html(obj.cmblist);
                    $("#cmbServices").trigger("chosen:updated");

                    $('#modal-form-grps-service').modal('show');

                } else {
                    $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }
            }
        });
    });

    $("#btnSetRepass").click(function(){

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkGroup/modalSetGroupRepass",
            dataType: 'json',
            error: function (ret) {
                $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $("#cmbGroupsRepass").html(obj.cmblist);
                    $("#cmbGroupsRepass").trigger("chosen:updated");

                    $('#modal-form-grp-repass').modal('show');

                } else {
                    $('#modal-notification').html(makeSmartyLabel('Alert_get_data'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }
            }
        });
    });

    $("#btnSaveSetGrpRepass").click(function(){
        if (!$("#grp-repass-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkGroup/setGroupRepass',
            dataType: 'json',
            data: $("#create-group-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-groups-repass');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK') {

                    $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-groups-repass');

                }

            }

        });
    });

    /*
     *  Chosen
     */
    $("#cmbGroups").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbServices").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbGroupsRepass").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});

    var objGroups = {
        loadAttendantsByGroup: function(){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkGroup/loadAttendantsByGroup',
                data: {
                    idgroup : $("#cmbGroups").val(),
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-attendant-group');
                },
                success: function(ret) {
                    if(ret){
                        $("#tab-attendants").html(ret);

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green',
                        });

                        $('.checkAttendantGrp').on('ifChecked ifUnchecked',function(e){
                            var attdata = e.target.attributes.value.nodeValue;
                            attdata = attdata.split('-');

                            if(e.type == 'ifChecked'){
                                objGroups.setAttendantByGroup(attdata[0],attdata[1],'ADD');
                            }else{
                                objGroups.setAttendantByGroup(attdata[0],attdata[1],'DEL');
                            }
                        });
                    }
                    else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-attendant-group');
                    }
                },
                beforeSend: function(){
                    if(!$("#panelAttendants").hasClass('hide')){
                        $("#panelAttendants").addClass('animated fadeOutUp').addClass('hide');
                    }
                    if($("#loaderPanel").hasClass('hide')){
                        $("#loaderPanel").removeClass('hide')
                    }
                    $("#loaderPanel").html("<span style='color:#1ab394;'><i class='fa fa-spinner fa-spin fa-5x'></i></span>");
                },
                complete: function(){
                    $("#loaderPanel").addClass('hide');
                    if($("#panelAttendants").hasClass('fadeOutUp')){
                        $("#panelAttendants").removeClass('fadeOutUp');
                    }
                    $("#panelAttendants").removeClass('hide').addClass('animated fadeInDown');
                }
            });

        },
        setAttendantByGroup: function(idPerson,idGroup,action){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkGroup/setAttendantsByGroup',
                data: {
                    idperson : idPerson,
                    idgroup : idGroup,
                    action : action,
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-attendant-group');
                },
                success: function(ret) {
                    if(!ret){
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-attendant-group');
                    }
                }
            });
        },
        loadGroupsByService: function(){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkGroup/loadGroupsByService',
                data: {
                    idservice : $("#cmbServices").val(),
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-attendant-group');
                },
                success: function(ret) {
                    if(ret){
                        $("#tab-grps-service").html(ret);

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green',
                        });
                    }
                    else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-attendant-group');
                    }
                },
                beforeSend: function(){
                    if(!$("#panelGrpsService").hasClass('hide')){
                        $("#panelGrpsService").addClass('animated fadeOutUp').addClass('hide');
                    }
                    if($("#loaderService").hasClass('hide')){
                        $("#loaderService").removeClass('hide')
                    }
                    $("#loaderService").html("<span style='color:#1ab394;'><i class='fa fa-spinner fa-spin fa-5x'></i></span>");
                },
                complete: function(){
                    $("#loaderService").addClass('hide');
                    if($("#panelGrpsService").hasClass('fadeOutUp')){
                        $("#panelGrpsService").removeClass('fadeOutUp');
                    }
                    $("#panelGrpsService").removeClass('hide').addClass('animated fadeInDown');
                }
            });

        },
        loadCompaniesRepass: function(){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkGroup/loadCompaniesRepass',
                data: {
                    idgroup : $("#cmbGroupsRepass").val(),
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-groups-repass');
                },
                success: function(ret) {
                    if(ret){
                        $("#tab-groups-repass").html(ret);
                        $(".cmb-grp-repass").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
                    }
                    else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-groups-repass');
                    }
                },
                beforeSend: function(){
                    if(!$("#panelGrpsRepass").hasClass('hide')){
                        $("#panelGrpsRepass").addClass('animated fadeOutUp').addClass('hide');
                    }
                    if($("#loaderGrpsRepass").hasClass('hide')){
                        $("#loaderGrpsRepass").removeClass('hide')
                    }
                    $("#loaderGrpsRepass").html("<span style='color:#1ab394;'><i class='fa fa-spinner fa-spin fa-5x'></i></span>");
                },
                complete: function(){
                    $("#loaderGrpsRepass").addClass('hide');
                    if($("#panelGrpsRepass").hasClass('fadeOutUp')){
                        $("#panelGrpsRepass").removeClass('fadeOutUp');
                    }
                    $("#panelGrpsRepass").removeClass('hide').addClass('animated fadeInDown');
                }
            });

        },
    }

    $('#cmbGroups').change(function(){
        objGroups.loadAttendantsByGroup();
    });

    $('#cmbServices').change(function(){
        objGroups.loadGroupsByService();
    });

    $('#cmbGroupsRepass').change(function(){
        objGroups.loadCompaniesRepass();
    });

    $("#grp-repass-form").validate({
        ignore:[],
        rules: {
            cmbGroupsRepass:        "required"
        },
        messages: {
            cmbGroupsRepass:          makeSmartyLabel('Alert_field_required')
        }
    });

    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() {
        $('#panelAttendants').addClass('hide');
        $("#panelGrpsService").addClass('hide')
        $("#panelGrpsRepass").addClass('hide')
    });


});

function postStatus(idGroup,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkGroup/changeStatus',
        dataType: 'json',
        data: {
            idgroup: idGroup,
            newstatus: newStatus,
            _token: $('#_token').val()
        },
        error: function (ret) {
            $('#modal-notification').html(makeSmartyLabel('Alert_failure'));
            $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.status == 'OK' ) {
                $('#modal-notification').html(makeSmartyLabel('Alert_success_update'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                $("#tipo-alert").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                $('#modal-notification').html(makeSmartyLabel('Alert_failure'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }

        }

    });

    return false;
}

