$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('#btnPrint').addClass('disabled');
    $('#btnView').addClass('disabled');

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
        url: path+"/hur/hurCandidate/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'name',
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['','Nome','Cargo','Nascimento','Tempo Experi&ecirc;ncia','Data Cadastro','',''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'nome',index:'name', editable: true, width:70, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'cargo',index:'role', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'dtbirth',index:'dtbirth', editable: true, align:"center", width:18, search:false, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDate}},
            {name:'exptime',index:'experience_time', editable: true, align:"center",width:20, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'dtentry',index:'dtentry', editable: true, width:18, align:"center", search:true, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDate}, searchoptions: {dataInit:datePick}, searchrules:{required:true,date:true}},
            {name:'rolenum',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'candidatename',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }
        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: ':: CURR&Iacute;CULOS',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellRole = grid.jqGrid('getCell', rowId, 'rolenum');
            var myCellSearch = grid.jqGrid('getCell', rowId, 'candidatename');
            
            if($.isNumeric(myCellRole)) {
                grid.jqGrid('setGridParam', {search: true, postData: { searchField: 'name', searchString: myCellSearch, searchOper: 'eq' } }).trigger('reloadGrid');
            } else {
                document.location.href = path+"/hur/hurCandidate/viewCandidate/id/" + myCellData;
            }
            

        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');

            $('#btnPrint').removeClass('disabled').addClass('active');
            $('#btnView').removeClass('disabled').addClass('active');

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
    grid.jqGrid('setCaption', 'CURR&Iacute;CULOS');

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false, add:false, del:false, search:true, searchtext: makeSmartyLabel('Search'), refreshtext: makeSmartyLabel('Grid_reload'), cloneToTop: true});

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
        $.ajax({
            type: "POST",
            url: path + "/hur/hurAso/makeReport",
            data: { idfuncionario : idPerson},
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {
                console.log(fileName);
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

    $("#btnAll").click(function(){
        $('#cmbArea').find('option:first-child').prop('selected', true).end().trigger('chosen:updated');
        objCandidateData.changeRole();
    });

    $("#btnView").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idCurriculum = myGrid.jqGrid ('getCell', selRowId, 'id');

        location.href = path+"/hur/hurCandidate/viewCandidate/id/" + idCurriculum;
    });

    /*
     *  Chosen
     */
    $("#cmbArea").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#cmbRole").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});

    /*
     * Combos
     */
    var objCandidateData = {
        changeRole: function() {
            var areaId = $("#cmbArea").val();
            $.post(path+"/hur/hurCandidate/ajaxRole",{areaId: areaId},
                function(valor){
                    $("#cmbRole").html(valor);
                    $("#cmbRole").trigger("chosen:updated");
                    var myGrid = $('#table_list_tickets'), idrole = $("#cmbRole").val();
                    if(!idrole){
                        myGrid.jqGrid('setGridParam', {search: false, postData: { idcondicao: 'ALL' } }).trigger('reloadGrid');
                    }else{
                        myGrid.jqGrid('setGridParam', {search: false, postData: { idcondicao: idrole } }).trigger('reloadGrid');
                    }

                })
        }
    }

    $("#cmbArea").change(function(){
        objCandidateData.changeRole();
    });

    $("#cmbRole").change(function(){
        var myGrid = $('#table_list_tickets'), idrole = $("#cmbRole").val();
        myGrid.jqGrid('setGridParam', { postData: { idcondicao: idrole } }).trigger('reloadGrid');
    });



});




