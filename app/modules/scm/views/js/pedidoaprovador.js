$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    datePick = function(elem)
    {
        $(elem).datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });

        $(elem).mask('00/00/0000');

    };

    var grid = $("#table_list_tickets");

    grid.jqGrid({
        url: path+"/scm/scmPedidoAprovador/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'dataentrega', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 10, 25, 25, 50],
        colNames:['N&deg;','','Data/hora','Data Entrega','Status','Motivo compra', 'Fora do Prazo', 'Pessoa', 'Turma', ' Status ', ''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:true, hidden: false },
            {name:'idstatus',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'datapedido',index:'datapedido', width:15, align:"center", sortable: true, editable: false, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDateTime}, searchoptions: {dataInit:datePick}, searchrules:{required:true,date:true}},
            {name:'dataentrega',index:'dataentrega', width:15, align:"center", sortable: true, editable: false, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDateTime}, searchoptions: {dataInit:datePick}, searchrules:{required:true,date:true}},
            {name:'nomestatus',editable: false, width:25, align:"center",sortable: true, search:true, hidden: false },
            {name:'motivo',index:'motivo', editable: true, width:60, sortable: true, search:false, hidden: true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'foradoprazo',index:'foradoprazo',editable: false, width:11, align:"center",sortable: true, search:false, hidden: false },
            {name:'nomepessoa',editable: false, width:30, align:"center",sortable: true, search:true, hidden: false },
            {name:'turma',index:'tur.nome', editable: false, width:15, align:"center",sortable: true, search:true, hidden: false },
            {name:'status_fmt',index:'status',editable: false, width:9, align:"center",sortable: true, search:false ,hidden: true},
            {name:'status',hidden: true }

        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: 'PEDIDOS DE APROVADORES',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idPedidoAprovador = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/scm/scmPedidoAprovador/echoPedidoAprovador/idpedidoaprovador/" + idPedidoAprovador ;
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
    grid.jqGrid('setCaption', 'Pedidos de Aprovadores');

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

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
                idPedidoAprovador = myGrid.jqGrid ('getCell', selRowId, 'id'),
                idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        console.log(idstatus);

            if (!idPedidoAprovador) {
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html('Marque um pedido.');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            } else {
                location.href = path + "/scm/scmPedidoAprovador/formUpdatePedidoAprovador/idpedidoaprovador/" + idPedidoAprovador;
            }



    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoAprovador = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPedidoAprovador,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoAprovador = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPedidoAprovador,'I');
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoAprovador = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idPedidoAprovador) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um pedido.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmPedidoAprovador/echoPedidoAprovador/idpedidoaprovador/" + idPedidoAprovador ;
        }
    });

    $("#btnNote").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoCompra = myGrid.jqGrid ('getCell', selRowId, 'id'),
            idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        if (!idPedidoCompra) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um pedido.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        }else{
            $('#modal-form-note').modal('show');
            $('#pedidonote').code('');
            $('#idpedidonote').val(idPedidoCompra);
            $('#idpedidohead').html(idPedidoCompra);
            $('#typeuser').val('operator');

            $.ajax({
                type: "POST",
                url: path + "/scm/scmPedidoOperador/_ajaxPedidoNotesScreen",
                dataType: 'json',
                data: {idpedido:idPedidoCompra},
                error: function (ret) {
                    $("#btn-modal-ok").attr("href", '');
                    $('#modal-notification').html('Não foi possível obter os dados.');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.displaytype == 'N'){$('#display_line').addClass('hidden');}
                    else{$('#display_line').removeClass('hidden');}
                    $('#notes_line').html(obj.notes);
                }
            });
        }

    });

    $("#btnSendNote").click(function(){
        var _token = $('#_token').val(), idpedido = $('#idpedidonote').val(),
            noteContent = $('#pedidonote').code(), displayType = $("input[name='displayUser']:checked").val();

        if(noteContent == ''){
            modalAlertMultiple('warning','Favor digite o apontamento','alert-noteadd');
            return false;
        }else{
            $.ajax({
                type: "POST",
                url: path + "/scm/scmPedidoOperador/savePedidoNote",
                dataType: 'json',
                data: {_token: _token,idpedido:idpedido,noteContent:noteContent,displayType:displayType},
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status == 'OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
                        if($('#typeuser').val() == 'operator'){
                            sendNotification('addnote-operator',$('#idpedidonote').val());
                        }else{
                            sendNotification('addnote-user',$('#idpedidonote').val());
                        }
                        $('#notes_line').empty();
                        $('#notes_line').html(obj.addednotes);
                        $('#pedidonote').code('');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                    }
                },
                beforeSend: function(){
                    $("#btnSendNote").attr('disabled','disabled');
                },
                complete: function(){
                    $("#btnSendNote").removeAttr('disabled')
                }
            });
        }

        return false;
    });

    $("#btnRepass").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoCompra = myGrid.jqGrid ('getCell', selRowId, 'id'),
            idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        if (!idPedidoCompra) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um pedido.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        }else{
            $('#modal-form-repass').modal('show');
            $('#pedidonote').code('');
            $('#idpedidorepass').val(idPedidoCompra);
            $('#idpedidorepasshead').html(idPedidoCompra);

            $.ajax({
                type: "POST",
                url: path + "/scm/scmPedidoAprovador/ajaxPedidoRepass",
                dataType: "json",
                data: {idpedido:idPedidoCompra},
                error: function (ret) {
                    $("#btn-modal-ok").attr("href", '');
                    $('#modal-notification').html('Não foi possível obter os dados.');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.flagdisplay === 'S'){
                        $('#line_turma').removeClass('hidden');
                    }else{
                        $('#line_turma').addClass('hidden');
                    }

                    $('#personname').val(obj.author);
                    $('#dataentrega').val(obj.deliverydate);
                    $('#turmaabrev').val(obj.turma);
                    $('#motivo').val(obj.reason);
                    $('#codigonomecentrodecusto').val(obj.costcenter);
                    $('#codigonomecontacontabil').val(obj.accountingacc);
                    $('#nomestatus').val(obj.status);
                    $('#itemlist').html(obj.itens);
                }
            });
        }

    });

    $("#btnSendRepass").click(function(){
        var _token = $('#_token').val(), idpedido = $('#idpedidorepass').val(), idincharge = $('#cmbAprovator').val();
        $.ajax({
            type: "POST",
            url: path + "/scm/scmPedidoAprovador/repassPedido",
            dataType: 'json',
            data: {_token: _token,idpedido:idpedido,idincharge:idincharge},
            error: function (ret) {
                modalAlertMultiple('danger','Erro ao repassar o pedido','alert-repass');
            },
            success: function(ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.status == 'OK') {
                    modalAlertMultiple('info','Pedido repassado com sucesso','alert-repass');
                    sendNotification('repass-request',$('#idpedidorepass').val());
                    setTimeout(function(){
                        $('#modal-form-repass').modal('hide');
                        $('#form-repass').trigger('reset');
                        grid.trigger('reloadGrid')
                    },2000);

                } else {
                    modalAlertMultiple('danger','Erro ao repassar o pedido','alert-repass');
                }
            },
            beforeSend: function(){
                $("#btnSendRepass").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendRepass").removeAttr('disabled')
            }
        });
        return false;
    });

    //Summernote
    $('#pedidonote').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            width: 600,       // set editor width
            focus: false     // set focus to editable area after initializing summernote


        }
    );

    /*
     *  Chosen
     */
    $("#cmbAprovator").chosen({ width: "100%",  no_results_text: "Nada encontrado!"});

});

function postStatus(idPedidoAprovador,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmPedidoAprovador/statusPedidoAprovador/idpedidoaprovador/' + idPedidoAprovador,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidoaprovador');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idpedidoaprovador = obj.idpedidoaprovador;
                $('#modal-notification').html('Pedido atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoAprovador/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidoaprovador');
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

function sendNotification(transaction,codeRequest)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/scm/scmPedidoCompra/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: codeRequest
        },
        success : function(data) {

        },
        error : function(request,error)
        {

        }
    });

    return false ;

}