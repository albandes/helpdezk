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

    var grid = $("#table_list_baixaprod");

    grid.jqGrid({
        url: path+"/scm/scmBaixaProduto/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'dtcadastro', //initially sorted on code_request
        sortorder: "DESC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 25, 50],
        colNames:['ID','Tipo','Responsável','Data Cadastro'],
        colModel:[
            {name:'id',index:'idbaixa',editable: false, width:2, align:"center",sortable: true, search:true, hidden: false },
            {name:'tipo',index:'tipo',editable: false, width:10, align:"center",sortable: true, search:true, hidden: false },
            {name:'responsavel',index:'b.name',editable: false, width:9, align:"center",sortable: true, search:true, hidden: false },
            {name:'dtcadastro',index:'dtcadastro', editable: true, width:10, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], dataInit:objSearch.searchDate} }
        ],
        pager: "#pager_list_baixaprod",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_scm_baixa_produto'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idBaixa = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/scm/scmBaixaProduto/echoBaixa/idbaixa/" + idBaixa;
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
    grid.jqGrid('setCaption', makeSmartyLabel('pgr_scm_baixa_produto'));

    // Setup buttons
    grid.navGrid('#pager_list_baixaprod',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/scm/scmBaixaProduto/formCreate" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_baixaprod'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idBaixaProduto = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idBaixaProduto) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_select_one'));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/scm/scmBaixaProduto/formUpdate/idbaixaproduto/" + idBaixaProduto;
        }
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_baixaprod'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idBaixa = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idBaixa) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/scm/scmBaixaProduto/index');
        } else {
            location.href = path + "/scm/scmBaixaProduto/echoBaixa/idbaixa/" + idBaixa;
        }
    });

    $("#btnRemove").click(function(){
        var myGrid = $('#table_list_baixaprod'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idBaixa = myGrid.jqGrid ('getCell', selRowId, 'id');

        //console.log(idstatus);

        if (!idBaixa) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning',path + '/scm/scmBaixaProduto/index');
        } else {
            $.ajax({
                type: "POST",
                url: path + '/scm/scmBaixaProduto/modalRemoveBaixa',
                dataType: 'json',
                data: {idbaixa:idBaixa},
                error: function (ret) {
                    showAlert('N&atilde;o foi poss&iacute;vel obter dados !','danger',path + '/scm/scmBaixaProduto/index');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    $('#_token').val(obj.token);
                    $('#idbaixa').val(obj.idbaixa);
                    if($('#questionContent').length <= 0){
                        $('#questionLine').html("<div id='questionContent' class='col-sm-12 alert alert-warning'>"+
                                                "Deseja realmente excluir esta Baixa de Produto?</div>");
                    }

                    $('#modal-form-status').modal('show');
                }
            });
        }



    });

    $("#btnSaveRemove").click(function(){
        if (!$("#btnSaveRemove").hasClass('disabled')) {
            $.ajax({
                type: "POST",
                url: path + '/scm/scmBaixaProduto/removeBaixa',
                dataType: 'json',
                data: $("#remove-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel alterar !','alert-motivo');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        //console.log("sdafdsfsda");
                        modalAlertMultiple('success','Baixa de Produto exclu&iacute;da com sucesso !','alert-motivo');
                        setTimeout(function(){
                            $('#modal-form-status').modal('hide');
                            $('#remove-form').trigger('reset');
                            grid.trigger('reloadGrid');
                        },2000);  
    
                    } else {
                        modalAlertMultiple('danger','N&atilde;o foi excluir a Baixa de Produto!','alert-motivo');
                    }
    
                },
                beforeSend: function(){
                    $("#btnSaveRemove").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCloseRemove").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveRemove").html("<i class='fa fa-check'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
                    $("#btnCloseRemove").removeClass('disabled');
                }
    
            });
            
        }
        return false;
    });

    /* limpa campos modal */
    $('#modal-form-status').on('hidden.bs.modal', function() {
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