$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    var test = toJSDate('2017-12-20 08:38:00');
    var bool = isPastDateTime(toJSDate('2017-02-20 08:38:00'));

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

    if(typeuser == 3){
        var jsonUrl = path+"/helpdezk/hdkTicket/jsonAtt", btnWaitApp = makeSmartyLabel('Waiting_for_approval'),
            colNamesParams = ['&nbsp;','<i class="fa fa-paperclip"></i>','N&deg;',makeSmartyLabel('Grid_opening_date'),makeSmartyLabel('Company'),makeSmartyLabel('From'),makeSmartyLabel('Type'),makeSmartyLabel('Item'),makeSmartyLabel('Service'),makeSmartyLabel('Grid_subject'),makeSmartyLabel('Grid_expire_date'),makeSmartyLabel('Grid_status'),makeSmartyLabel('Grid_incharge'),makeSmartyLabel('Priority'),'',makeSmartyLabel('Description')],
            colModelParams = [
                {name:'star',editable: false, width:20, align:"center",sortable: false, search:false, fixed: true },
                {name:'attch',editable: false, width:40, align:"center",sortable: false, search:false, fixed: true },
                {name:'code_request_view', index:'code_request', editable: true, width:120, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left", fixed: true},
                {name:'entry_date',index:'entry_date', editable: true, width:120, sorttype:"date", formatter:"date", formatoptions:{ srcformat: 'ISO8601Long', newformat: mascDateTime}, fixed: true, searchoptions: {dataInit:datePick}, searchrules:{required:true,date:true}},
                {name:'company',index:'company', editable: true, width:40, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}},
                {name:'owner',index:'own.name', editable: true, width:90, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}},
                {name:'type',index:'type', editable: true, width:90, sorttype: 'string', search:false},
                {name:'item',index:'item', editable: true, width:90, sorttype: 'string', search:false},
                {name:'service',index:'service', editable: true, width:90, search:false},
                {name:'subject',index:'subject', editable: true, width:130, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
                {name:'expire_date',index:'expire_date', editable: true, width:120, search:false, sorttype:"date", formatter:"fontColorFormat",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDateTime}, fixed: true},
                {name:'statusview',index:'e.name', editable: true, width:90, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}, fixed: true, search:false},
                {name:'in_charge',index:'in_charge', editable: true, width:120, sorttype: 'string', search:false},
                {name:'priority',index:'i.name', editable: false, width:75, align:"center",sortable: true, search:false, fixed: true},
                {name:'code_request',index:'status', editable: false, width:75, align:"center",sortable: false, search:false, fixed: true, hidden: true},
                {name:'description',index:'description', editable: false, width:75, align:"center",sortable: false, search:true, fixed: true, hidden: true, searchoptions: { searchhidden: true, sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }},
            ],
            flgapvrequire = 0;
    }else{
        var jsonUrl = path+"/helpdezk/hdkTicket/json", btnWaitApp = makeSmartyLabel('Grid_waiting_my_approval'),
            colNamesParams = ['','N&deg;',makeSmartyLabel('Grid_opening_date'),makeSmartyLabel('Grid_subject'),makeSmartyLabel('Grid_expire_date'),makeSmartyLabel('Grid_incharge'),makeSmartyLabel('Grid_status')],
            colModelParams = [
                {name:'star',editable: false, width:9, align:"center",sortable: false, search:false },
                {name:'code_request', index:'code_request', editable: true, width:45, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left"},
                {name:'entry_date',index:'entry_date', editable: true, width:50, sorttype:"date", formatter:"date",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDateTime}},
                {name:'subject',index:'subject', editable: true, width:130, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
                {name:'expire_date',index:'expire_date', editable: true, width:50, sorttype:"date", formatter:"fontColorFormat",formatoptions: { srcformat: 'ISO8601Long', newformat: mascDateTime}},
                {name:'in_charge',index:'in_charge', editable: true, width:120, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}},
                {name:'statusview',index:'status', editable: true, width:90, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}}
            ];

        if(flgoperator == 0){var flgapvrequire = $.ajax({type: "POST",url: path+"/helpdezk/home/checkapproval",async: false}).responseText;}
    }

    //alert(JSON.stringify(grid));
    //alert(grid['selector']);
    // alert(grid[0].id);

    // Configuration for jqGrid Tickets
    grid.jqGrid({
        url: jsonUrl,
        datatype: "json",
        mtype: 'POST',
        sortname: sidx, //initially sorted on expire date for attendant, entry date for requester
        sortorder: sord,
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 20, 30],
        colNames: colNamesParams,
        colModel: colModelParams,
        pager: "#pager_list_tickets",
        viewrecords: true,
        caption: makeSmartyLabel('Grid_all_tickets'),
        hidegrid: false,
        toppager:true,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'code_request');
            //alert(myCellData);
             console.log('Redirect to: ' + path + "/helpdezk/hdkTicket/viewrequest/" + myCellData);

             if(operatorAsUser == '1'){
                 document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + myCellData + "/myticket/1";
             }else{
                 document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + myCellData;
             }


        },
        onSelectRow: function(rowId) {
            console.log('entrou');
            goTicket(grid,rowId);
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
    if(typeuser == 3){
        grid.jqGrid('setCaption', makeSmartyLabel('Grid_new_tickets'));
    }else{
        if(flgapvrequire > 0){
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_waiting_my_approval_tickets'));
        }else{
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_all'));
        }
    }

    // Setup buttons
    grid.navGrid('#pager_list_tickets',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});
    grid.jqGrid('navGrid','#pager_list_tickets',{search:true,cloneToTop:true});
/*
    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption:  makeSmartyLabel('Grid_view'),
        buttonicon: 'ui-icon-document',
        onClickButton: function(rowId) {
            //var myGrid = $("#table_list_tickets");
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
*/
    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: makeSmartyLabel('Grid_all'),
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            var typeexpdate = $("#cmbTypeExpireDate").val();
            grid.jqGrid('setGridParam', { postData: { idstatus: 'ALL', typeexpdate:typeexpdate, typeview: 1 } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_all_tickets'));
            selectTypeView($("#cmbTypeView"),1);
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: makeSmartyLabel('Grid_new'),
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            var typeexpdate = $("#cmbTypeExpireDate").val();
            grid.jqGrid('setGridParam', { postData: { idstatus: '1', typeexpdate:typeexpdate, typeview: 1 } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_new_tickets'));
            selectTypeView($("#cmbTypeView"),1);
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption:  makeSmartyLabel('Grid_being_attended'),
        buttonicon: 'ui-icon-info',
        onClickButton: function() {
            var typeexpdate = $("#cmbTypeExpireDate").val();
            grid.jqGrid('setGridParam', { postData: { idstatus: '3', typeexpdate:typeexpdate, typeview: 2 } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_being_attended_tickets'));
            selectTypeView($("#cmbTypeView"),2);
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: btnWaitApp,
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            var typeexpdate = $("#cmbTypeExpireDate").val();
            grid.jqGrid('setGridParam', { postData: { idstatus: '4', typeexpdate:typeexpdate, typeview: 1 } }).trigger('reloadGrid');
            grid.jqGrid('setCaption',  makeSmartyLabel('Grid_waiting_my_approval_tickets'));
            selectTypeView($("#cmbTypeView"),1);
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: makeSmartyLabel('Grid_finished'),
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            var typeexpdate = $("#cmbTypeExpireDate").val();
            grid.jqGrid('setGridParam', { postData: { idstatus: '5', typeexpdate:typeexpdate, typeview: 1 } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_finished_tickets'));
            selectTypeView($("#cmbTypeView"),1);
        }
    });
    grid.jqGrid('navSeparatorAdd','#' + grid[0].id + '_toppager_left', { sepclass: 'ui-separator' });

    grid.jqGrid('navButtonAdd', '#' + grid[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: makeSmartyLabel('Grid_rejected'),
        buttonicon: 'ui-icon-info',
        onClickButton: function(rowId) {
            var typeexpdate = $("#cmbTypeExpireDate").val();
            grid.jqGrid('setGridParam', { postData: { idstatus: '6', typeexpdate:typeexpdate, typeview: 1 } }).trigger('reloadGrid');
            grid.jqGrid('setCaption', makeSmartyLabel('Grid_rejected_tickets'));
            selectTypeView($("#cmbTypeView"),1);
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
    //$("#table_list_tickets_toppager_left").attr("colspan", "3");
    $(grid['selector']+"_toppager_left").attr("colspan", "4");

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        grid.setGridWidth(width);
    });

    if(flgapvrequire > 0){
        $('#tipo-info').addClass('alert alert-warning')
        $('#info-notification').html(makeSmartyLabel('Grid_waiting_approve_msg'));
        $('#modal-info').modal('show');
    }

    /*
     *  Chosen
     */
    $("#cmbTypeExpireDate").chosen({ width: "100%",      no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbTypeView").chosen({ width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});

    /*
     * Combos
     */
    $("#cmbTypeExpireDate").change(function(){
        grid.jqGrid('setGridParam', { postData: { typeexpdate: $("#cmbTypeExpireDate").val() } }).trigger('reloadGrid');
    });

    $("#cmbTypeView").change(function(){
        grid.jqGrid('setGridParam', { postData: { typeview: $("#cmbTypeView").val() } }).trigger('reloadGrid');
    });

    $("#btnSendApvReqYes").click(function(){
        location.href = path + "/helpdezk/hdkTicket/index" ;
    });

    $("#btnNewTck").click(function(){
        if(typeuser == 3){
            location.href = path + "/helpdezk/hdkTicket/newTicket" ;
        }else{
            if(flgoperator == 1){
                location.href = path + "/helpdezk/hdkTicket/newTicket" ;
            }else{
                $.post(path + "/helpdezk/home/checkapproval", {}, function(data) {
                    if(data > 0){
                        $('#tipo-alert-apvrequire').addClass('alert alert-danger')
                        $('#apvrequire-notification').html(makeSmartyLabel('Request_approve'));
                        $('#modal-approve-require').modal('show');
                    }else{
                        location.href = path + "/helpdezk/hdkTicket/newTicket" ;
                    }
                })
            }
        }

    });

    if(autoRefreshGrid > 0){
        setInterval(function(){
            grid.trigger('reloadGrid');
        },autoRefreshGrid);
    }

});

function selectTypeView(obj,newst){
    var st = obj.val();

    if(st != newst){
        obj.find("option[value="+st+"]").removeAttr("selected");
        obj.find("option[value="+newst+"]").attr("selected","selected");
        obj.trigger("chosen:updated");
    }
}

function goTicket(grid,rowId)
{
    var myCellData = grid.jqGrid('getCell', rowId, 'code_request');
    console.log('Redirect to: ' + path + "/helpdezk/hdkTicket/viewrequest/" + myCellData);
    if(operatorAsUser == '1'){
        document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + myCellData + "/myticket/1";
    }else{
        document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + myCellData;
    }

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


