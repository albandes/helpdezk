$(document).ready(function () {
    countdown.start(timesession);
    
    //set buttons (blocked, available)
    setActionsBtn(aPermissions);

    /**
     * Select2
     */
    $('#filter-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});
    $('#action-list').select2({width:"100%",dropdownParent: $(this).find('.modal-body-filters')});

    /**
        * reload action's combo
        */
    $('#filter-list').change(function(){
        var searchOpts = $("#filter-list option:selected").data('optlist');

        $.post(path+"/main/home/reloadSearchOptions",{searchOpts:searchOpts},
            function(valor) {
                $("#action-list").html(valor);
                $("#action-list").trigger("change");
                return false;
            });
    });

    /** 
     * Define a model for grid's columns
     * 
     * title Title of column
     * width Width of column
     * dataIndx Column name on DB to ordering
     * align Align of column content
     * halign Align of column header
     * 
     * for more column's options see https://paramquery.com/api#option-column-hidden
     * */ 
    var colM = [
        { title: vocab["ID"], width: '10%', dataIndx: "idmessage", hidden:true, halign: "center"  },        
        { title: "<b>"+vocab["Topic"]+"</b> ", width: '25%', dataIndx: "topic", align: "left", halign: "center"  },
        { title: "<b>"+vocab["Title"]+"</b> ", width: '30%', dataIndx: "title", align: "left", halign: "center"  },        
        { title: "<b>"+vocab["Var_record"]+"</b> ", width: '12%', dataIndx: "createddate", align: "center", halign: "center"  },
        { title: "<b>"+vocab["Initial_date"]+"</b> ", width: '11%', dataIndx: "startdate", align: "center", halign: "center"  },        
        { title: "<b>"+vocab["Finish_date"]+"</b> ", width: '12%', dataIndx: "enddate", align: "center", halign: "center"  },
        { title: "<b>"+vocab["Show_in"]+"</b> ", width: '9%', dataIndx: "showin", align: "center", halign: "center"  },
        { title: vocab["ID"], width: '10%', hidden:true, halign: "center"  }
    ];

    /** 
     * Define a data model
     * 
     * location     Server from which to get data. (local or remote)
     * dataType     Data Type of response from server in case of remote request
     * method       Method to use to fetch data from server
     * url          It's an absolute or relative url from where grid gets remote data and sends requests (via GET or POST) for remote sorting, paging, filter, etc.
     * getData      It's a callback function which acts as a mediator between remote server and pqGrid
     * 
     * for more data model options see https://paramquery.com/api#option-dataModel
     * */
    var dataModel = {
        location: "remote",
        dataType: "JSON",
        method: "POST",
        url: path + '/helpdezk/hdkWarning/jsonGrid',
        getData: function (dataJSON) {                
            return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: dataJSON.data };                
        }
    };
    
    /** 
     * Define a sort model
     * 
     * cancel   When true, sorting on a column can be canceled by clicking on a column header while column is in descending sort order.
     * type     It decides the location of sorting, possible values are "local" and "remote".
     * sorter   It's an array of { dataIndx: dataIndx, dir: "up/down" } objects where dataIndx is dataIndx of the column and dir has 2 possible values "up" & "down".
     * 
     * for more sort model options see https://paramquery.com/api#option-sortModel
     * */
    var sortModel = {
        cancel: false,
        type: "remote",
        sorter:[ { dataIndx: "title", dir: "up" } ]
    };

    /** 
     * Define a page model
     * 
     * type         Paging can be enabled for both local and remote requests.
     * curPage      Current page of the view when paging has been enabled.
     * rPP          It denotes results per page when paging has been enabled.
     * rPPOptions   Results per page options in dropdown when paging has been enabled.
     * 
     * for more page model options see https://paramquery.com/api#option-pageModel
     * */
    var pageModel = {
        type: "remote",
        curPage: this.curPage,
        rPP: 10,
        rPPOptions: [10, 20, 30, 40, 50]
    };
    
    var obj = { 
        width: '100%', 
        height: 500,
        wrap: false,
        dataModel: dataModel,
        colModel: colM,
        editable: false,
        title: "<b>"+vocab['pgr_warnings']+"</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        rowDblClick: function (evt, ui) {
            var rowIndx = getRowIndx('grid_warnings'),
                row = $("#grid_warnings").pqGrid('getRowData', {rowIndx: rowIndx});
                
            location.href = path + "/helpdezk/hdkWarning/formUpdate/"+row.idmessage;
        }
    };

    $("#grid_warnings").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_warnings").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_warnings").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val(), 
            flg = $("#viewInactive").is(':checked');
            
        $("#grid_warnings").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation,allRecords:flg};
        } );
        
        $("#grid_warnings").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val(), flg = $("#viewInactive").is(':checked');
            
        $("#grid_warnings").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue,allRecords:flg};
        });
        
        $("#grid_warnings").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnCreate").click(function(){
        location.href = path + "/helpdezk/hdkWarning/formCreate";
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx('grid_warnings'),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_warnings").pqGrid('getRowData', {rowIndx: rowIndx});
            location.href = path + "/helpdezk/hdkWarning/formUpdate/"+row.idmessage;
            
        }else{            
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnGridTopic").click(function(){
        location.href = path + "/helpdezk/hdkWarning/topics"  
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkWarning/index" ;        
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });

    $('.lbltooltip').tooltip();
});
