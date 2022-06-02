$(document).ready(function () {

    countdown.start(timesession);
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

    var grid = $("#table_list_parents");

    grid.jqGrid({
        url: path+"/emq/emqParent/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'idparent', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 16,
        rowList: [16, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Name'),makeSmartyLabel('email')],
        colModel:[
            {name:'idparent',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'name',index:'name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'email',index:'email', editable: true, width:130, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }
        ],
        pager: "#pager_list_parents",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('pgr_emq_parents'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idparent = grid.jqGrid('getCell', rowId, 'idparent'),
                status = grid.jqGrid('getCell', rowId, 'statusval');

            document.location.href = path+"/emq/emqParent/formUpdate/idparent/" + idparent;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'statusval');

            /*if (myCellStatus == 'A'){
                $('#btnDisable').removeClass('disabled');
                $('#btnUpdate').removeClass('disabled');
                $('#btnEnable').addClass('disabled');                
            }
            else{
                $('#btnDisable').addClass('disabled');
                $('#btnUpdate').addClass('disabled');
                $('#btnEnable').removeClass('disabled');
            }*/
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
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('pgr_emq_parents'));

    // Setup buttons
    grid.navGrid('#pager_list_parents',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/emq/emqParent/formCreate" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_parents'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idparent = myGrid.jqGrid ('getCell', selRowId, 'idparent');

        if (!idparent) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            location.href = path+"/emq/emqParent/formUpdate/idparent/" + idparent;
        }
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_macs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idhost = myGrid.jqGrid ('getCell', selRowId, 'id');
        
        if(!$("#btnEnable").hasClass('disabled'))
            postStatus(idhost,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_macs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idhost = myGrid.jqGrid ('getCell', selRowId, 'id');

        if(!$("#btnDisable").hasClass('disabled'))
            postStatus(idhost,'N');

    });

});

function postStatus(idhost,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/itm/itmMacAddress/changeStatusMac',
        dataType: 'json',
        data: {
            idhost: idhost,
            newstatus: newStatus,
            _token : $('#_token').val()
        },
        error: function (ret) {
            showAlert(makeSmartyLabel('Edit_failure'),'danger','');
        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idmac = obj.idmac, msg = '';
                if(obj.macstatus == 'A'){msg = makeSmartyLabel('Alert_activated');}
                else{msg = makeSmartyLabel('Alert_deactivated');}

                showAlert(msg,'success','');
            } else {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            }
        }

    });

    return false;
}

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

function fontColorFormat(cellvalue, options, rowObject) {
    var color = "blue";
    var cellHtml = "<span style='color:" + color + "' originalValue='" + cellvalue + "'>" + cellvalue + "</span>";
    return cellHtml;
}