$(document).ready(function () {

    countdown.start(timesession);

    var grid = $("#table_list_tickets");

    grid.jqGrid({
        url: path+"/spm/spmCadastroAtleta/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'apelido', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['','','apelido','nome','condicao','posicao','Departamento','Cadastro',' S ',''],
        colModel:[
            {name:'star',editable: false, width:25, align:"center",sortable: false, search:false },
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'apelido', index:'apelido', editable: true, width:70, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left"},
            {name:'nome',index:'nome', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'condicao',index:'condicao', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'posicao',index:'posicao', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'departamento',index:'departamento', editable: true, width:50, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'dtcreate',index:'dtcreate', editable: true, width:25, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDateTime}},
            {name:'status_fmt',index:'status',editable: false, width:9, align:"center",sortable: true, search:false },
            {name:'status',hidden: true },

        ],
        pager: "#pager_list_persons",
        viewrecords: true,
        caption: 'ATLETAS',
        hidegrid: false,
        toppager:true,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'code_request');
            document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + myCellData;

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
    grid.jqGrid('setCaption', 'Atletas');

    // Setup buttons
    grid.navGrid('#pager_list_persons',{edit:false,add:false,del:false,search:true,searchtext: 'Search',refreshtext: 'Reload',cloneToTop: true});
    grid.jqGrid('navGrid','#pager_list_persons',{search:true,cloneToTop:true});

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption:  makeSmartyLabel('Grid_view'),
        buttonicon: 'ui-icon-document',
        onClickButton: function(rowId) {

            var rowKey = grid.jqGrid('getGridParam',"selrow");

            if (rowKey)
                alert("Selected row primary key is: " + rowKey);
            else
                //alert("No rows are selected");
            //$('#myModal').modal('show');
            $('#myModal').modal({ keyboard: false })
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: 'Todos',
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            grid.jqGrid('setGridParam', { postData: { idcondicao: 'ALL' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', 'Todos');
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: 'Normal',
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            grid.jqGrid('setGridParam', { postData: { idcondicao: '1' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', 'Normal');
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption:  'Fisioterapia preventiva',
        buttonicon: 'ui-icon-info',
        onClickButton: function() {
            grid.jqGrid('setGridParam', { postData: { idcondicao: '2' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', 'Fisioterapia preventiva');
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: 'Fisioterapia terapeutica',
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            grid.jqGrid('setGridParam', { postData: { idcondicao: '3' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', 'Fisioterapia terapeutica');
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: 'Fisioterapia retreinamento',
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            grid.jqGrid('setGridParam', { postData: { idcondicao: '4' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', 'Fisioterapia retreinamento');
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: 'Cirurgia',
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            grid.jqGrid('setGridParam', { postData: { idcondicao: '5' } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', 'Cirurgia');
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', {sepclass: 'ui-separator',position: 'last'});


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
        location.href = path + "/spm/spmCadastroAtleta/formCreateAtleta" ;
    });

    $("#btnUpdate").click(function(){
        console.log('edit');
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPerson = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idPerson) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um atleta.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/spm/spmCadastroAtleta/formUpdateAtleta/idperson/" + idPerson ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPerson = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPerson,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPerson = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPerson,'I');
    });




});

function postStatus(idPerson,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/spm/spmCadastroAtleta/statusAtleta/idperson/' + idPerson,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-atleta');

        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.status == 'OK' ) {

                var idperson = obj.idperson;
                $('#modal-notification').html('Atleta atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/spm/spmCadastroAtleta/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');

            } else {

                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-atleta');

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



//convert DateTime (dd-mm-yyyy hh-mm) to javascript DateTIme
//Ex: 16-11-2015 16:05
function toJSDate( dateTime ) {
    var dateTime = dateTime.split(" ");//dateTime[0] = date, dateTime[1] = time
    var date = dateTime[0].split("-");
    var time = dateTime[1].split(":");
    //(year, month, day, hours, minutes, seconds, milliseconds)
    //subtract 1 from month because Jan is 0 and Dec is 11
    return new Date(date[0], (date[1]-1), date[2], time[0], time[1], 0, 0);
}

//Check to see if the DateTime is in the future
//param: dateTime must be a JS Date Object
//return True if DateTime is after Now
//return False if DateTIme is before Now
function isPastDateTime( dateTime ) {
    var now = new Date();
    var ret = true;
    if( Date.parse(now) > Date.parse(dateTime) ) {
        ret = false;
    }
    return ret;
}
