$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_modules");

    grid.jqGrid({
        url: path+"/admin/modules/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'name', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 10,
        rowList: [10, 20, 25, 30, 50],
        colNames:['',aLang['Name'].replace (/\"/g, ""),aLang['status'].replace (/\"/g, ""),''],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'name',index:'name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', editable: true, width:10, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }

        ],
        pager: "#pager_list_modules",
        viewrecords: true,
        caption: ' :: '+aLang['Module'].replace(/\"/g, "")+'s',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idmodule = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/admin/modules/formUpdateModule/idmodule/" + idmodule ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'statusval');

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
    grid.jqGrid('setCaption', ' :: '+aLang['Module'].replace(/\"/g, "")+'s');

    // Setup buttons
    grid.navGrid('#pager_list_modules',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/admin/modules/formCreateModule" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_modules'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idmodule = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idmodule) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Alert_select_one'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        } else {
            location.href = path + "/admin/modules/formUpdateModule/idmodule/" + idmodule ;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_modules'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idmodule = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idmodule,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_modules'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idmodule = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idmodule,'I');
    });



});

function postStatus(idmodule,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/admin/modules/statusModule/idmodule/' + idmodule,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-module');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idmodule = obj.idmodule, msg = '';
                if(obj.modulestatus == 'A'){msg = aLang['Alert_activated'].replace (/\"/g, "");}
                else{msg = aLang['Alert_deactivated'].replace (/\"/g, "");}

                $('#modal-notification').html(msg);
                $("#btn-modal-ok").attr("href", path + '/admin/modules/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-module');
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