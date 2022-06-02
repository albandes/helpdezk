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

    var objSearch = {
        searchDate : function(elem)
        {
            $(elem).datepicker({
                format: "dd/mm/yyyy",
                language: "pt-BR",
                autoclose: true
            });

            $(elem).mask('00/00/0000');
    
        }
    }

    var grid = $("#table_list_tickets");

    grid.jqGrid({
        url: path+"/scm/scmEntradaProduto/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'datacadastro', //initially sorted on code_request
        sortorder: "DESC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 25, 50],
        colNames:['N° Registro','Tipo','Data Cadastro','N° Pedido','Fornecedor', 'N° Nota Fiscal','Data NF'],
        colModel:[
            {name:'id',index:'identradaproduto',editable: false, width:2, align:"center",sortable: true, search:true, hidden: false },
            {name:'tipo',index:'tipo',editable: false, width:10, align:"center",sortable: true, search:true, hidden: false },
            {name:'dtcadastro',index:'datacadastro', editable: true, width:10, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], dataInit:objSearch.searchDate} },
            {name:'numeropedido',index:'numeropedido',editable: false, width:9, align:"center",sortable: true, search:true, hidden: false },
            {name:'nomefornecedor',index:'nomefornecedor', width:10,editable: false, align:"center",sortable: true, search:true, hidden: false },
            {name:'numeronotafiscal',index:'numeronotafiscal',editable: false, width:11, align:"center",sortable: true, search:true, hidden: false },
            {name:'dtnotafiscal',index:'dtnotafiscal', editable: true, width:10, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], dataInit:objSearch.searchDate} }

        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: 'ENTRADAS DE PRODUTOS',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idEntradaProduto = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/scm/scmEntradaProduto/echoEntradaProduto/identradaproduto/" + idEntradaProduto ;
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
    grid.jqGrid('setCaption', 'Entradas de Produtos');

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
        location.href = path + "/scm/scmEntradaProduto/formCreateEntradaProduto" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
                idEntradaProduto = myGrid.jqGrid ('getCell', selRowId, 'id'),
                idstatus = myGrid.jqGrid ('getCell', selRowId, 'idstatus');

        console.log("aqui");

            if (!idEntradaProduto) {
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html('Marque um pedido.');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            } else {
                location.href = path + "/scm/scmEntradaProduto/formUpadateEntradaProduto/identradaproduto/" + idEntradaProduto;
            }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoCompra = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPedidoCompra,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPedidoCompra = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPedidoCompra,'I');
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idEntradaProduto = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idEntradaProduto) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um pedido.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmEntradaProduto/echoEntradaProduto/identradaproduto/" + idEntradaProduto ;
        }
    });

    $("#btnRemove").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            identrada = myGrid.jqGrid ('getCell', selRowId, 'id');

        //console.log(idstatus);

        if (!identrada) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um cadastro.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            $.ajax({
                type: "POST",
                url: path + '/scm/scmEntradaProduto/modalRemoveEntrada',
                dataType: 'json',
                data: {identrada: identrada},
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel obter dados !','alert-create-pedidocompra');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    $('#_token').val(obj.token);
                    $('#identrada').val(obj.identrada);

                    $('#modal-form-status').modal('show');
                }
            });
        }



    });

    $("#btnSendRemove").click(function(){
        /*if (!$("#status-form").valid()) {
            console.log('nao validou') ;
            return false;
        }*/

        $.ajax({
            type: "POST",
            url: path + '/scm/scmEntradaProduto/removeEntrada',
            dataType: 'json',
            data: $("#remove-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel alterar !','alert-motivo');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    //console.log("sdafdsfsda");
                    modalAlertMultiple('success','Entrada exclu&iacute;da com sucesso !','alert-motivo');
                    setTimeout(function(){
                        $('#modal-form-status').modal('hide');
                        $('#remove-form').trigger('reset');
                        grid.trigger('reloadGrid');
                    },2000);


                } else {
                    modalAlertMultiple('danger','N&atilde;o foi excluir a Entrada!','alert-motivo');
                }

            }

        });

    });

    /* limpa campos modal */
    $('.modal').on('hidden.bs.modal', function() {
        $('#remove-form').trigger('reset');
    });



});

function postStatus(idPedidoCompra,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmEntradaProduto/statusEntradaProduto/identradaproduto/' + idEntradaProduto,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidocompra');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idpedidocompra = obj.idpedidocompra;
                $('#modal-notification').html('Pedido atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoCompra/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidocompra');
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