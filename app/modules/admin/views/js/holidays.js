$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_holidays");

    datePick = function(elem)
    {
        $(elem).datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });

        $(elem).mask('00/00/0000');

    };

    grid.jqGrid({
        url: path+"/admin/holidays/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'holiday_date', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 10,
        rowList: [10, 20, 25, 30, 50],
        colNames:['',aLang['Name'].replace (/\"/g, ""),aLang['Date'].replace (/\"/g, ""), aLang['Company'].replace (/\"/g, "")],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'holiday_description',index:'holiday_description', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'holiday_date',index:'holiday_date', width:10, align:"center", sortable: true, editable: false, width:25, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Short', newformat: 'd/m/Y'}, searchoptions: {dataInit:datePick}, searchrules:{required:true,date:true}},
            {name:'company',index:'tbp.name', editable: true, width:25, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }

        ],
        pager: "#pager_list_holidays",
        viewrecords: true,
        caption: ' :: '+aLang['Holiday'].replace(/\"/g, "")+'s',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idholiday = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/admin/holidays/formUpdateHolidays/idholiday/" + idholiday ;
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
            id: "id"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+aLang['Holiday'].replace(/\"/g, "")+'s');

    // Setup buttons
    grid.navGrid('#pager_list_holidays',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});


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
        location.href = path + "/admin/holidays/formCreateHolidays" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_holidays'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idholiday = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idholiday) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Alert_select_one'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/admin/holidays/formUpdateHolidays/idholiday/" + idholiday ;
        }
    });

    $("#btnImport").click(function(){
        location.href = path + "/admin/holidays/formImportHolidays" ;

    });

    $("#btnSendImport").click(function(){
        
        $.ajax({
            type: "POST",
            url: path + '/admin/holidays/importHolidays',
            dataType: 'json',
            data: $("#import-form").serialize(),

            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel alterar !','alert-motivo');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    //console.log("sdafdsfsda");
                    modalAlertMultiple('success',aLang['Alert_deleted'].replace (/\"/g, ""),'alert-motivo');
                    setTimeout(function(){
                        $('#modal-form-import').modal('hide');
                        $('#import-form').trigger('reset');
                        grid.trigger('reloadGrid');
                    },2000);


                } else {
                    modalAlertMultiple('danger','N&atilde;o foi cancelar o pedido !','alert-motivo');
                }

            }

        });

    });

    $("#btnDelete").click(function(){
        var myGrid = $('#table_list_holidays'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idHoliday = myGrid.jqGrid ('getCell', selRowId, 'id');

        //console.log(idstatus);

        if (!idHoliday) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Alert_select_one'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            $.ajax({
                type: "POST",
                url: path + '/admin/holidays/modalDeleteHoliday',
                dataType: 'json',
                data: {idholiday: idHoliday},
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    $('#_token').val(obj.token);
                    $('#idholiday').val(obj.idholiday);

                    $('#modal-form-delete').modal('show');
                }
            });
        }



    });

    $("#btnSendDelete").click(function(){
        
        $.ajax({
            type: "POST",
            url: path + '/admin/holidays/deleteHoliday',
            dataType: 'json',
            data: $("#delete-form").serialize(),

            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel alterar !','alert-motivo');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    //console.log("sdafdsfsda");
                    modalAlertMultiple('success',aLang['Alert_deleted'].replace (/\"/g, ""),'alert-motivo');
                    setTimeout(function(){
                        $('#modal-form-delete').modal('hide');
                        $('#delete-form').trigger('reset');
                        grid.trigger('reloadGrid');
                    },2000);


                } else {
                    modalAlertMultiple('danger','N&atilde;o foi cancelar o pedido !','alert-motivo');
                }

            }

        });

    });



});
function postStatus(idProduto,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmProduto/statusProduto/idproduto/' + idProduto,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-produto');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idproduto = obj.idproduto;
                $('#modal-notification').html('Produto atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/scm/scmProduto/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-produto');
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