$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdatePerson').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_tickets");

    grid.jqGrid({
        url: path+"/scm/scmCentroCusto/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'nome', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['','Código','Nome','Tipo', ' Status ', ''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'codigo',editable: false, width:25, align:"center",sortable: true, search:false },
            {name:'nome',index:'nome', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'tipo',index:'tipo', editable: true, width:50, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status',editable: false, width:9, align:"center",sortable: true, search:false },
            {name:'status',hidden: true }

        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: 'CENTROS DE CUSTOS',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idCentroCusto = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/scm/scmCentroCusto/echoCentroCusto/idcentrocusto/" + idCentroCusto ;
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
            id: "code_request"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', 'Centros de Custos');

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false,add:false,del:false,search:true, searchtext: makeSmartyLabel('Search'), refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/scm/scmCentroCusto/formCreateCentroCusto" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idCentroCusto = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idCentroCusto) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um Centro de Custo.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmCentroCusto/formUpdateCentroCusto/idcentrocusto/" + idCentroCusto ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idCentroCusto = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idCentroCusto,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idCentroCusto = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idCentroCusto,'I');
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idCentroCusto = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idCentroCusto) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um centro de custo.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmCentroCusto/echoCentroCusto/idcentrocusto/" + idCentroCusto ;
        }
    });



});

function postStatus(idCentroCusto,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmCentroCusto/statusCentroCusto/idcentrocusto/' + idCentroCusto,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-centrocusto');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idcentrocusto = obj.idcentrocusto;
                $('#modal-notification').html('Centro de Custo atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/scm/scmCentroCusto/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-centrocusto');
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


