$(document).ready(function (){

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_titles");

    grid.jqGrid({
        url: path+"/lmm/lmmTitles/jsonGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'name', //initially sorted on code_request
        sortorder: "ASC",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Material_Type'),makeSmartyLabel('Collection'),makeSmartyLabel('Title'),makeSmartyLabel('Cutter'),makeSmartyLabel('ISBN'),makeSmartyLabel('ISSN'),makeSmartyLabel('CDD'),makeSmartyLabel('CDU'),makeSmartyLabel('Publishing_company'),/*makeSmartyLabel('Capacity'),*/makeSmartyLabel('Color'),makeSmartyLabel('Classification')],
        colModel:[
            {name:'id',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'materialtype',index:'materialtype', editable: true, width:15, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'collection',index:'collection', editable: true, width:15, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'title',index:'name', editable: true, width:15, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'cutter',index:'cutter', editable: true, width:10, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'isbn',index:'isbn', editable: true, width:10, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'issn',index:'issn', editable: true, width:10, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },            
            {name:'cdd',index:'cdd', editable: true, width:10, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'cdu',index:'cdu', editable: true, width:10, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'publishing',index:'publishingcompany', editable: true, width:15, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },         
            {name:'color',index:'color', editable: true, width:10, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'classification',index:'classification', editable: true, width:15, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} }
        ],
        pager: "#pager_list_titles",
        viewrecords: true,
        caption: makeSmartyLabel('pgr_titles'),
        hidegrid: false,
        toppager:false,
        
        ondblClickRow: function(rowId) {
            var colorID = grid.jqGrid('getCell', rowId, 'id');
            location.href = path + "/lmm/lmmTitles/viewTitles/titlesID/" + titlesID;
        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'id');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');
        console.log( myCellStatus);
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
            id: "id"  
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', '<strong>'+makeSmartyLabel('pgr_titles')+'</strong>');

    // Setup buttons
    grid.navGrid('#pager_list_titles',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

    // remove some double elements from one place which we not need double
    var topPagerDiv = $('#' + grid[0].id + '_toppager')[0];         // "#list_toppager"
    $("#search_" + grid[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    $("#refresh_" + grid[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
    $("#" + grid[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"
    

    /**
     ** Increase _toppager_left
     ** https://stackoverflow.com/questions/29041956/how-to-place-pager-to-end-of-top-of-toolbar-in-free-jqgrid
     **/
    $(grid['selector']+"_toppager_left").attr("colspan", "4");

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        grid.setGridWidth(width);
    })  

    //Buttons
    $("#btnCreate").click(function(){
      location.href = path + "/lmm/lmmTitles/formCreate";
    });

    $("#btnUpdate").click(function(){
        var myGrid = $('#table_list_titles'),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
        titlesID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!titlesID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        }else{
            location.href = path + "/lmm/lmmTitles/formUpdate/lmmID/" + titlesID;
       }
    });

   
    $("#btnDelete").click(function(){
        var myGrid = $('#table_list_titles'),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
        titleID = myGrid.jqGrid ('getCell', selRowId, 'id');

        if (!titleID) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        }else{
            $("#idtitles_modal").val(titleID);
            $("#modal-dialog-delete").modal('show');
        }
       
    });

    $("#btnSaveDelete").click(function(){
        if(!$("#btnSaveDelete").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmTitles/deleteTitles',
                dataType: 'json',
                data: $("#delete_modal_titles").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-delete-titles');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_deleted'),'alert-delete-titles');
                        setTimeout(function(){
                            $('#modal-dialog-delete').modal('hide');
                            grid.trigger('reloadGrid');
                        },3500);
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_deleted_error'),'alert-delete-titles');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveDelete").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveDelete").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        } 
       
    }); 

    
});



function fontColorFormat(cellvalue, options, rowObject) {
    var color = "blue";
    var cellHtml = "<span style='color:" + color + "' originalValue='" + cellvalue + "'>" + cellvalue + "</span>";
    return cellHtml;
}

function showAlert(msg, typeAlert,btnOk){

    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#formas-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;

}

