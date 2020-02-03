$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_typepersonperms");
    var idprogram = $("#idprogram").val();

    grid.jqGrid({
        url: path+"/admin/typepersonpermission/jsonTypePersonGrid/idprogram/"+idprogram,
        datatype: "json",
        mtype: 'POST',
        sortname: 'tbty.name', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 10,
        rowList: [10, 20, 25, 30, 50],
        colNames: ['',aLang['Name'].replace (/\"/g, ""),aLang['Access'].replace (/\"/g, ""),aLang['New'].replace (/\"/g, ""),aLang['Edit_btn'].replace (/\"/g, ""),aLang['Delete'].replace (/\"/g, ""),aLang['Export'].replace (/\"/g, ""),'E-mail','SMS',''], 
        colModel: [ 
            {name:"idtypeperson",index:"num",width:80,key:true,hidden:true}, 
            {name:"typedescrition",search:false,width:130,sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}}, 
            {name:"access",search:false,width:70,align:"center"},
            {name:"new",search:false,width:70,align:"center"}, 
            {name:"edit",search:false,width:70,align:"center"} ,
            {name:"delete",search:false,width:70,align:"center"},
            {name:"export",search:false,width:70,align:"center"},
            {name:"email",search:false,width:70,align:"center"},
            {name:"sms",search:false,width:70,align:"center"},
            {name:"idprogram",index:"idprogram",width:70,align:"center",hidden:true}],
        pager: "#pager_list_typepersonperms",
        viewrecords: true,
        caption: ' :: '+aLang['Permissions'].replace(/\"/g, "")+'s',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        /*ondblClickRow: function(rowId) {
            var idprogram = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/admin/typepersonpermission/manageperm/id/" + idprogram ;
        },*/
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
    grid.jqGrid('setCaption', ' :: '+aLang['Permissions'].replace(/\"/g, "")+'s');

    // Setup buttons
    grid.navGrid('#pager_list_typepersonperms',{edit:false,add:false,del:false,search:false, searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
    $("#btnCancel").click(function(){
        location.href = path + "/admin/typepersonpermission/index";
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_programs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idprogram = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idprogram,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_programs'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idprogram = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idprogram,'I');
    });



});

function postStatus(idprogram,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/admin/programs/statusProgram/idprogram/' + idprogram,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-program');

        },
        success: function(ret){
            console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
                var idprogram = obj.idprogram, msg = '';
                if(obj.programstatus == 'A'){msg = aLang['Alert_activated'].replace (/\"/g, "");}
                else{msg = aLang['Alert_deactivated'].replace (/\"/g, "");}

                $('#modal-notification').html(msg);
                $("#btn-modal-ok").attr("href", path + '/admin/programs/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');
            } else {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-program');
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

function edit2(id, idprogram, accesstype, idtypeperson) {
		            
    if (document.getElementById(id).checked) {var check = "Y";} 
    else {var check = "N";}
    
    $.ajax({
        type: "POST",
        url: path + '/admin/typepersonpermission/grantpermission',
        dataType: 'json',
        data: {
            id : id,
            check : check,
            idprogram : idprogram,
            idaccesstype: accesstype,
            idtypeperson: idtypeperson
        },
        error: function (ret) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Permission_error'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');    
        },
        success: function(ret){                
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.status == 'OK' ) {
                console.log(ret);
            } else {
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html(aLang['Permission_error'].replace (/\"/g, ""));
                $("#tipo-alert").attr('class', 'warning alert-warning');
                $('#modal-alert').modal('show');
            }
        }    
    });
}