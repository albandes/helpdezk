$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_persons");

    grid.jqGrid({
        url: path+"/admin/person/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'tbp.name', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 16,
        rowList: [10, 20, 25, 30, 50],
        colNames:['','',makeSmartyLabel('Name'),makeSmartyLabel('Login'),makeSmartyLabel('email'),makeSmartyLabel('type'),makeSmartyLabel('Company'),makeSmartyLabel('Department'),makeSmartyLabel('status'),'',''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'typeicone',editable: false, width:25, align:"center",sortable: false, search:false, hidden: false, fixed:true },
            {name:'name',index:'tbp.name', editable: true, width:180, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'login',index:'tbp.login', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'email',index:'tbp.email', editable: true, width:180, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'typeperson',index:'tbtp.name', editable: true, width:80, align:"center", search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'company',index:'comp.name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'department',index:'dep.name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'tbp.status', editable: true, width:40, search:false, align:"center", sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'idtypeperson',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('people'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idperson = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/admin/person/formUpdatePerson/idperson/" + idperson ;
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
            grid.html("Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText);
        },
        jsonReader : {
            repeatitems: false,
            id: "id"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('people'));

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/admin/person/formCreatePerson" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_persons'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idperson = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idperson) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Alert_select_one'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'alert alert-warning');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/admin/person/formUpdatePerson/idperson/" + idperson ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_persons'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idperson = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idperson,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_persons'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idperson = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idperson,'I');
    });

    $("#btnPerms").click(function(){
        var myGrid = $('#table_list_persons'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idperson = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idperson) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Alert_select_one'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'alert alert-warning');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/admin/person/managePersonPerms/idperson/" + idperson ;
        }
    });

    $("#btnGroups").click(function(){
        var myGrid = $('#table_list_persons'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idperson = myGrid.jqGrid ('getCell', selRowId, 'id'),
            idtypeperson = myGrid.jqGrid ('getCell', selRowId, 'idtypeperson'),
            status = myGrid.jqGrid ('getCell', selRowId, 'statusval');

        if (!idperson) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Alert_select_one'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'alert alert-warning');
            $('#modal-alert').modal('show');
        } else {
            if(idtypeperson != 3){
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html(makeSmartyLabel('Option_only_operator'));
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }else{
                if(status == 'A'){
                    $.ajax({
                        type: "POST",
                        url: path + "/admin/person/modalAttendantGroups",
                        dataType: 'json',
                        data: {
                            idperson: idperson
                        },
                        error: function (ret) {
                            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                        },
                        success: function(ret) {
                            var obj = jQuery.parseJSON(JSON.stringify(ret));
                            //console.log(obj);
                            if(obj) {
                                $('#cmbGroups').html(obj.cmblist);
                                $("#cmbGroups").trigger("chosen:updated");

                                if(obj.tablelist){
                                    $("#tablelist").html(obj.tablelist);
                                }

                                $('#idattendant').val(idperson);
                                $('#modal-form-attendantgrps').modal('show');

                            } else {
                                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                            }
                        }
                    });

                }else{
                    $("#btn-modal-ok").attr("href", '');
                    $('#modal-notification').html(makeSmartyLabel('Option_only_attendant_active'));
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }
            }

        }
    });

    /*
     *  Chosen
     */
    $("#cmbGroups").chosen({ width: "100%",      no_results_text: "Nada encontrado!", disable_search_threshold: 10});

    var objPerson = {
        insertAttGrps: function(){
            var groupId = $("#cmbGroups").val(), idperson = $('#idattendant').val();

            $.post(path+"/admin/person/insertAttendantGroups",{groupid: groupId,idperson: idperson},
                function(valor){
                    var obj = jQuery.parseJSON(JSON.stringify(valor));
                    $('#cmbGroups').html(obj.cmblist);
                    $("#cmbGroups").trigger("chosen:updated");

                    if(obj.tablelist){
                        $("#tablelist").html(obj.tablelist);
                    }
                },'json');
        }
    }

    $("#cmbGroups").change(function(){
        objPerson.insertAttGrps();
    });


});

function postStatus(idperson,newStatus){
    $.ajax({
        type: "POST",
        url: path + '/admin/person/statusPerson/idperson/' + idperson,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-program');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idperson = obj.idperson, msg = '';
                if(obj.personstatus == 'A'){msg = aLang['Alert_activated'].replace (/\"/g, "");}
                else{msg = aLang['Alert_deactivated'].replace (/\"/g, "");}

                $('#modal-notification').html(msg);
                $("#btn-modal-ok").attr("href", path + '/admin/person/index');
                $("#tipo-alert").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-program');
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

function removeAttGrps(handler){
    var tr = $(handler).closest('tr'), groupId = $(handler).closest('tr').find('.admAttGrps').val(),
        idperson = $('#idattendant').val();

    $.post(path+"/admin/person/deleteAttendantGroups",{groupid: groupId,idperson: idperson},
        function(valor){
            var obj = jQuery.parseJSON(JSON.stringify(valor));
            $('#cmbGroups').html(obj.cmblist);
            $("#cmbGroups").trigger("chosen:updated");

            if(obj.tablelist){
                $("#tablelist").html(obj.tablelist);
            }else{
                $("#tablelist").empty();
            }
        },'json')
}