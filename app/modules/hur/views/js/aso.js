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

    $('#btnPrint').addClass('disabled');

    var grid = $("#table_list_tickets");

    grid.jqGrid({
        url: path+"/hur/hurAso/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'nome',
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['','Nome','Setor','Identidade','Empresa','Nascimento'],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'nome',index:'nome', editable: true, width:70, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'setor',index:'setor', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'identidade',index:'identidade', editable: true, width:20, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'empresa',index:'empresa', editable: true, width:60, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'dtnasc',index:'dtnasc', editable: true, search:false, width:18, sorttype:"date", formatter:"date", formatoptions: { srcformat: 'ISO8601Long', newformat: mascDate}}
        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: ':: Funcionários',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            $("#btnPrint").click();

        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');

            $('#btnPrint').removeClass('disabled').addClass('active');

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
    grid.jqGrid('setCaption', ':: Funcionários');

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false,add:false,del:false,search:true,searchtext:  makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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

    $("#btnPrint").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPerson = myGrid.jqGrid ('getCell', selRowId, 'id');
        //alert (selRowId);
        //alert (idPerson);
        if(!$("#btnPrint").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/hur/hurAso/makeReport",
                data: { idfuncionario : idPerson},
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                },
                success: function(fileName) {
                    //console.log(fileName);
                    if(fileName){
                        /*
                         * I had to make changes to open the file in a new window
                         * because I could not use the jquery.download with the .pdf extension
                         */
                        if (fileName.indexOf(".pdf") >= 0) {
                            window.open(fileName, '_blank');
                        } else {
                            $.fileDownload(fileName );
    
                        }
    
                    }
                    else {
                    }
                }
            });
        }
    
        return false;
    });

    $("#btnRefresh").click(function(){

        $('#modal-funcionarios').html('Atualizando ...');
        $('#modal-alert-refresh').modal('show');
        $.ajax({
            type: "POST",
            url: path + "/hur/hurAso/atualizaFuncionarios",
            dataType:'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK') {
                    $('#btnModalAlert').html('<button id="btnAtualizaOK" class="btn btn-success" role="button"><span class="fa fa-check"></span>&nbsp;OK</button>');
                    $('#modal-funcionarios').html('Funcion&aacute;rios atualizados:&nbsp; '+obj.funcionarios);
                    $('#modal-alert-refresh').modal('show');

                    grid.trigger("reloadGrid");
                    $('#hora-atual').html('Atualizado em ' + obj.dtatual);
                } else {
                    $('#btnModalAlert').html('<button id="btnAtualizaOK" class="btn btn-success" role="button"><span class="fa fa-check"></span>&nbsp;OK</button>');
                    $('#modal-funcionarios').html('Erro ao atualizar Funcion&aacute;rios ');
                    $('#modal-alert-refresh').modal('show');
                }
            }
        });
    });

    $("#btnPrintDraft").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/hur/hurAso/makeDraft",
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {
               // console.log(fileName);
                if(fileName){
                    /*
                     * I had to make changes to open the file in a new window
                     * because I could not use the jquery.download with the .pdf extension
                     */
                    if (fileName.indexOf(".pdf") >= 0) {
                        window.open(fileName, '_blank');
                    } else {
                        $.fileDownload(fileName );

                    }

                }
                else {
                }
            }
        });
        return false;
    });


    $("#btnAtualizaOK").click(function(){

        $('#modal-alert-refresh').modal('toggle');
        return false;
    });



});




