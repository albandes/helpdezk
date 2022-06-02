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

    if(access.length > 0){
        if(access[0] != "Y"){
            $("#btn-modal-ok").attr("href", path + "/scm/home/index");
            $('#modal-notification').html('Acesso negado.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        }

        if(access[1] != "Y"){
            $('#btnPrint').removeClass('active').addClass('disabled').attr('disabled','disabled');
        }else{
            $('#btnPrint').removeClass('disabled').addClass('active').removeAttr('disabled');
        }
    }else{
        $("#btn-modal-ok").attr("href", path + "/scm/home/index");
        $('#modal-notification').html('Acesso negado.');
        $("#tipo-alert").attr('class', 'alert alert-danger');
        $('#modal-alert').modal('show');
    }

    var grid = $("#table_list_tickets");

    grid.jqGrid({
        url: path+"/scm/scmPedidoOperador/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'idpedido', //initially sorted on code_request
        sortorder: "desc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 25, 50],
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
            {name:'turma',index:'tur.nome',editable: false, width:15, align:"center",sortable: true, search:true, hidden: false },
            {name:'status_fmt',index:'status',editable: false, width:9, align:"center",sortable: true, search:false ,hidden: true},
            {name:'status',hidden: true }

        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: 'PEDIDOS DE OPERADORES',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idPedidoOperador = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/scm/scmPedidoOperador/echoPedidoOperador/idpedidooperador/" + idPedidoOperador ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'idstatus');

            if ((myCellStatus == 7) || (myCellStatus == 9) || (myCellStatus == 15) || (myCellStatus == 21)) {
                $('#btnNote').removeClass('active').addClass('disabled').attr('disabled','disabled');
            }else{
                $.ajax({
                    type: "POST",
                    url: path + '/scm/scmPedidoOperador/checkEdit',
                    dataType: 'json',
                    data: {idpedido: myCellData},
                    error: function (ret) {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
                    },
                    success: function(ret){

                        var obj = jQuery.parseJSON(JSON.stringify(ret));

                        if(obj.status == '1'){
                            $('#btnNote').removeClass('disabled').addClass('active').removeAttr('disabled');
                        }else{
                            $('#btnNote').removeClass('active').addClass('disabled').attr('disabled','disabled');
                        }
                    }
                });

            }

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
    grid.jqGrid('setCaption', 'Pedidos de Operadores');

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});


    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: makeSmartyLabel('Grid_all'),
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            grid.jqGrid('setGridParam', { postData: { idstatus: 'ALL' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_all_tickets'));
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

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

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
                idPedidoOperador = myGrid.jqGrid ('getCell', selRowId, 'id'),
                idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        console.log(idstatus);

            if (!idPedidoOperador) {
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html('Marque um pedido.');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            } else {
                if ((idstatus == 7) || (idstatus == 9) || (idstatus == 15) || (idstatus == 21)) {
                    $("#btn-modal-ok").attr("href", '');
                    $('#modal-notification').html('Não é permitido alterar este pedido.');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                } else {
                    $.ajax({
                        type: "POST",
                        url: path + '/scm/scmPedidoOperador/checkEdit',
                        dataType: 'json',
                        data: {idpedido: idPedidoOperador},
                        error: function (ret) {
                            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
                        },
                        success: function(ret){

                            var obj = jQuery.parseJSON(JSON.stringify(ret));

                            if(obj.status == '1'){
                                location.href = path + "/scm/scmPedidoOperador/formUpdatePedidoOperador/idpedidooperador/" + idPedidoOperador;
                            }else{
                                $("#btn-modal-ok").attr("href", '');
                                $('#modal-notification').html('Não é permitido alterar este pedido.');
                                $("#tipo-alert").attr('class', 'alert alert-danger');
                                $('#modal-alert').modal('show');
                            }
                        }
                    });
                }

            }



    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoOperador = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPedidoOperador,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoOperador = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPedidoOperador,'I');
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoOperador = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idPedidoOperador) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um pedido.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmPedidoOperador/echoPedidoOperador/idpedidooperador/" + idPedidoOperador ;
        }
    });

    $("#btnPrint").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoOperador = myGrid.jqGrid ('getCell', selRowId, 'id'),
            idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        if (!idPedidoOperador) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_select_one'));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            if (idstatus == 6 || idstatus == 7 || idstatus == 19) {
                $.ajax({
                    type: "POST",
                    url: path + '/scm/scmPedidoOperador/modalDeliveryTicket',
                    dataType: 'json',
                    data: {idpedido: idPedidoOperador},
                    error: function (ret) {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
                    },
                    success: function(ret){

                        var obj = jQuery.parseJSON(JSON.stringify(ret));

                        $('#_token').val(obj.token);
                        $('#idpedidodelivery').val(obj.idpedidocompra);
                        $('#dataentrega').val(obj.dataentrega);

                        if(obj.displayturma == 'S'){
                            $('#txtTurma').val(obj.turma);
                            $('#turmaline').removeClass('hidden');
                        }else{
                            $('#turmaline').addClass('hidden');
                        }
                        $('#numpedido').html(idPedidoOperador);
                        $('#motivo').html(obj.motivopedido);
                        $('#nomecentrodecusto').val(obj.ccusto);
                        $('#nomecontacontabil').val(obj.ccontabil);
                        $('#txtStatusPedido').val(obj.statuspedido);

                        $('#itemlist').html(obj.itens);

                        $('#modal-form-delivery').modal('show');
                    }
                });
            }else{
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html('Não é permitido alterar este pedido.');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }

            //$('#modal-form-delivery').modal('show');
        }
    });

    $("#btnSendPrint").click(function(){
        var itemlist = $("input[id='itensdelivery']"),
            err = 0;

        for(var i = 0; i < itemlist.length; i++){
            if(itemlist[i].checked){
                err = err + 1;
            }
        }

        if(err == 0){
            modalAlertMultiple('danger','Selecione pelo menos um item!','alert-delivery');
        }else{
            $.ajax({
                type: "POST",
                url: path + '/scm/scmPedidoOperador/printDeliveryTicket',
                data: $("#delivery-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi realizar este procedimento !','alert-delivery');
                },
                success: function(fileName){
                    if(fileName){
                        /*
                         * I had to make changes to open the file in a new window
                         * because I could not use the jquery.download with the .pdf extension
                         */
                        if (fileName.indexOf(".txt") >= 0) {
                            window.open(fileName, '_blank'); //abre em nova janela o relatório
                            //changeStatusDelivery($('#idpedidodelivery').val());
                            $('#modal-form-delivery').modal('hide');
                            $('#delivery-form').trigger('reset');
                            grid.trigger('reloadGrid');
                        } else {
                            $.fileDownload(fileName );
                        }

                    }else {}
                }
            });
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

    $("#btnExchange").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoOperador = myGrid.jqGrid ('getCell', selRowId, 'id'),
            idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        if (!idPedidoOperador) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um pedido.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            if (idstatus == 7 || idstatus == 15 || idstatus == 19) {
                location.href = path + "/scm/scmPedidoOperador/formExchange/idpedidooperador/" + idPedidoOperador;
            }else{
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html('Não é permitido realizar trocas neste pedido.');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }
            
            //location.href = path + "/scm/scmPedidoOperador/echoPedidoOperador/idpedidooperador/" + idPedidoOperador ;
        }
    });

});

function postStatus(idPedidoOperador,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmPedidoOperador/statusPedidoOperador/idpedidooperador/' + idPedidoOperador,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidooperador');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idpedidooperador = obj.idpedidooperador;
                $('#modal-notification').html('Pedido atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoOperador/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidooperador');
            }
        }

    });

    return false;
}

function changeStatusDelivery(idPedidoOperador)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmPedidoOperador/changeStatusDelivery',
        dataType: 'json',
        data: {
            idpedido: idPedidoOperador
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidooperador');

        },
        success: function(ret){
            /*console.log(ret);*/
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK'){
                $("#table_list_tickets").trigger('reloadGrid');
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