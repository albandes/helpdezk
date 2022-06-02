$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_macs");

    var objSearch = {
        searchMac : function(elem)
        {
            $(elem).mask('FF:FF:FF:FF:FF:FF', {
                translation: {'F':{pattern:/[0-9A-Fa-f]/}},
                onKeyPress: function (value, event) {
                    event.currentTarget.value = value.toUpperCase();
                }
            });
    
        },
        searchIp : function(elem)
        {
            $(elem).mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                  'Z': {
                    pattern: /[0-9]/, optional: true
                  }
                }
              });
    
        },
    }
    

    grid.jqGrid({
        url: path+"/itm/itmMacAddress/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'a.name', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 16,
        rowList: [16, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('itm_host'),makeSmartyLabel('itm_hosttype'),makeSmartyLabel('itm_macnumber'),makeSmartyLabel('itm_ipnumber'),makeSmartyLabel('itm_description'),makeSmartyLabel('Grid_status'),'',makeSmartyLabel('Name')],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'host',index:'a.name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'hosttype',index:'b.name', editable: true, width:150, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'mac',index:'mac', editable: true, width:80, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], dataInit:objSearch.searchMac} },
            {name:'ip',index:'ip', editable: true, width:80, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], dataInit:objSearch.searchIp} },
            {name:'description',index:'description', editable: true, width:150, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'host_status', editable: true, width:30, align:"center",search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'profilename',index:'profilename',editable: false, width:9, align:"center",sortable: false, search:true, hidden: true, sorttype: 'string',searchoptions: { searchhidden: true, sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }

        ],
        pager: "#pager_list_macs",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('itm_Title_MacAddress'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idhost = grid.jqGrid('getCell', rowId, 'id'),
                status = grid.jqGrid('getCell', rowId, 'statusval');

            if(status == 'A')
                location.href = path + "/itm/itmMacAddress/formUpdateMac/idhost/" + idhost ;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'statusval');

            if (myCellStatus == 'A'){
                $('#btnDisable').removeClass('disabled');
                $('#btnUpdate').removeClass('disabled');
                $('#btnEnable').addClass('disabled');                
            }
            else{
                $('#btnDisable').addClass('disabled');
                $('#btnUpdate').addClass('disabled');
                $('#btnEnable').removeClass('disabled');
            }
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
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('itm_Title_MacAddress'));

    // Setup buttons
    grid.navGrid('#pager_list_macs',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
        location.href = path + "/itm/itmMacAddress/formCreateMac" ;
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_macs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idhost = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!idhost) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            if(!$("#btnUpdate").hasClass('disabled'))
                location.href = path + "/itm/itmMacAddress/formUpdateMac/idhost/" + idhost ;
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