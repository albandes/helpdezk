$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_lgpticket");

    var datePick = function(elem)
    {
        $(elem).datepicker(datepickerOpts);

        $(elem).mask('00/00/0000');

    };
    

    grid.jqGrid({
        url: path+"/lgp/lgpDPORequest/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'a.dtentry', //initially sorted on code_request
        sortorder: "DESC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 16,
        rowList: [16, 20, 25, 30, 50],
        colNames:['','N&deg;',makeSmartyLabel('available_note_holder'),makeSmartyLabel('Grid_subject'),makeSmartyLabel('Grid_opening_date'),makeSmartyLabel('Grid_status')],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'ticket_code',index:'a.code_request', editable: true, width:40, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'owner',index:'b.name', editable: true, width:100, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'subject',index:'subject', editable: true, width:80, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'dtopen',index:'a.dtentry', editable: true, width:60, align:"center", search:true, sorttype:"date", formatter:"date", formatoptions:{ srcformat: 'ISO8601Long', newformat: mascDateTime}, searchoptions: {dataInit:datePick}, searchrules:{required:true,date:true}},
            {name:'status',index:'a.idstatus', editable: true, width:60, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }
        ],
        pager: "#pager_list_lgpticket",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('Tck_title'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var ticket_code = grid.jqGrid('getCell', rowId, 'ticket_code');
            location.href = path + "/lgp/lgpDPORequest/viewTicket/ticketCode/" + ticket_code;
        },
        onSelectRow: function(rowId) {
            var ticket_code = grid.jqGrid('getCell', rowId, 'ticket_code');
            location.href = path + "/lgp/lgpDPORequest/viewTicket/ticketCode/" + ticket_code;
        },
        loadError : function(xhr,st,err) {
            grid.html("Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText);
        },
        jsonReader : {
            repeatitems: false,
            id: "id"   // row ID
        },
        loadComplete : function(){
            $(window).trigger('resize');
        }

    });
    
    
    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('Tck_title'));

    // Setup buttons
    grid.navGrid('#pager_list_lgpticket',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
    $("#btnNewTicket").click(function(){
        location.href = path + "/lgp/lgpDPORequest/newTicket" ;
    });

    $("#btnSearch").click(function(){
        var txtSearch = $("#txtSearch").val();
        if(txtSearch.length <= 0 || txtSearch == ""){
            showAlert(makeSmartyLabel('alert_insert_search_keyword'),'danger','');
            return false;
        }

        grid.jqGrid('setGridParam', { postData: { txtSearch: txtSearch } }).trigger('reloadGrid');
        return false;
    });

    $("#btnView").click(function(){
        var myGrid = $('#table_list_datamapping'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            iddado = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!iddado) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            if(!$("#btnUpdate").hasClass('disabled'))
                location.href = path + "/lgp/lgpDataMapping/formView/iddado/" + iddado;
        }
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