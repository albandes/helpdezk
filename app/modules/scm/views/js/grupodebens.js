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
        url: path+"/scm/scmGrupoDeBens/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'descricao', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 20, 25, 30, 50],
        colNames:['','Descrição', 'Status', ''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'descricao',index:'descricao', editable: true, width:70, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status_fmt',index:'status',editable: false, width:9, align:"center",sortable: true, search:false, hidden: false },
            {name:'status',hidden: true }

        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: 'GRUPOS DE BENS',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idGrupoDeBens = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/scm/scmGrupoDeBens/echoGrupoDeBens/idgrupodebens/" + idGrupoDeBens ;
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
    grid.jqGrid('setCaption', 'Grupos de Bens');

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false,add:false,del:false,search:true,searchtext: 'Search',refreshtext: 'Reload',cloneToTop: true});

    grid.jqGrid('navGrid','#pager_list_persons',{search:true,cloneToTop:true});

    // remove some double elements from one place which we not need double
    var topPagerDiv = $('#' + grid[0].id + '_toppager')[0];         // "#list_toppager"
    $("#search_" + grid[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    $("#refresh_" + grid[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
    $("#" + grid[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"
    $(".ui-paging-info", topPagerDiv).remove();

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
        location.href = path + "/scm/scmGrupoDeBens/formCreateGrupoDeBens" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGrupoDeBens = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idGrupoDeBens) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um grupo de bens.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmGrupoDeBens/formUpdateGrupoDeBens/idgrupodebens/" + idGrupoDeBens ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGrupoDeBens = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idGrupoDeBens,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGrupoDeBens = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idGrupoDeBens,'I');
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idGrupoDeBens = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idGrupoDeBens) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um grupo de bens.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmGrupoDeBens/echoGrupoDeBens/idgrupodebens/" + idGrupoDeBens ;
        }
    });



});
function postStatus(idGrupoDeBens,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmGrupoDeBens/statusGrupoDeBens/idgrupodebens/' + idGrupoDeBens,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-grupodebens');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idgrupodebens = obj.idgrupodebens;
                $('#modal-notification').html('Grupo de bens atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/scm/scmGrupoDeBens/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-grupodebens');
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