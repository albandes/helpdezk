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
     * iCheck - checkboxes/radios styling
     */
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
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
        { title: vocab["ID"], width: '10%', dataIndx: "idtopic", hidden:true, halign: "center"  },        
        { title: "<b>"+vocab["Topic"]+"</b> ", width: '70%', dataIndx: "title", align: "left", halign: "center"  },
        { title: "<b>"+vocab["Validity_Standard"]+"</b> ", width: '20%', dataIndx: "validity", align: "left", halign: "center"  },        
        { title: "<b>"+vocab["Send_email"]+"</b> ", width: '9%', dataIndx: "flagsendemail", align: "center", halign: "center"  },
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
        url: path + '/helpdezk/hdkWarning/jsonTopicGrid',
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
        title: "<b>"+vocab['Topic']+"s</b>",
        bootstrap: {on : true, thead: 'table table-striped table-condensed table-bordered', tbody: 'table table-striped table-condensed table-bordered' },
        topVisible: false,
        sortModel: sortModel,
        pageModel: pageModel,
        numberCell: {show: false},
        selectionModel: { mode: 'single', type: 'row' },
        collapsible: false,
        rowDblClick: function (evt, ui) {
            $("#btnUpdate").click();
        }
    };

    $("#grid_topics").pqGrid(obj);

    //Grid i18n
    var locale = default_lang.replace('_','-');
    $("#grid_topics").pqGrid("option", $.paramquery.pqGrid.regional[locale]);
    $("#grid_topics").find(".pq-pager").pqPager("option", $.paramquery.pqPager.regional[locale]);

    // Buttons
    $("#btnFilters").click(function(){
        $('#modal-search-filter').modal('show');
    });

    $("#btnModalSearch").click(function(){
        var filterIndx = $("#filter-list").val(),
            filterValue = $("#filter-value").val(),
            filterOperation = $("#action-list").val(), 
            flg = $("#viewInactive").is(':checked');
            
        $("#grid_topics").pqGrid( "option", "dataModel.postData", function(){
            return {filterIndx:filterIndx,filterValue:filterValue,filterOperation:filterOperation,allRecords:flg};
        } );
        
        $("#grid_topics").pqGrid("refreshDataAndView");
    });

    $("#btnSearch").click(function(){
        var quickValue = $("#txtSearch").val(), flg = $("#viewInactive").is(':checked');
            
        $("#grid_topics").pqGrid( "option", "dataModel.postData", function(){
            return {quickSearch:true,quickValue:quickValue,allRecords:flg};
        });
        
        $("#grid_topics").pqGrid("refreshDataAndView");
    });

    $('#txtSearch').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#btnSearch").click();
        }
    });

    $("#btnBack").click(function(){
        location.href = path + "/helpdezk/hdkWarning/index"  
    });

    $("#btnCreate").click(function(){
        $("#modal-add-topic-title").html(vocab['Warning_new_topic']);
        $("#modal-topic-id").val("");
        $('input[name=modal-validity]:checked').iCheck('unCheck');
        $('#modal-validity-1').iCheck('check');
        $("#modal-add-topic").modal('show');
    });

    $("#btnUpdate").click(function(){
        var rowIndx = getRowIndx('grid_topics'),msg="";
        
        if (rowIndx != null) {
            var row = $("#grid_topics").pqGrid('getRowData', {rowIndx: rowIndx});
            
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkWarning/topicFormUpdate',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    topicId: row.idtopic
                },
                error: function (ret) {
                    showAlert(vocab['generic_error_msg'],'danger');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        $("#modal-add-topic-title").html(vocab['Topic_edit']);
                        $("#modal-topic-id").val(row.idtopic);
                        $('input[name=modal-validity]:checked').iCheck('unCheck');
                        $("#modal-topic-name").val(obj.topicTitle);
                        $('#modal-validity-'+obj.topicValidityType).iCheck('check');

                        if(obj.topicValidityType == 2){
                            $("#modal-validity-hours").val(obj.topicValidity);
                        }else if(obj.topicValidityType == 3){
                            $("#modal-validity-days").val(obj.topicValidity);
                        }

                        if(obj.topicFlgSendEmail == "Y"){
                            $('#modal-topic-send-email').iCheck('check');
                        }

                        if(obj.topicGroupList.length > 0){
                            $('#topic-available-group-2').iCheck('check');
                            $('#group-list').removeClass('d-none');

                            obj.topicGroupList.forEach(function(val,key) {
                                $("#checkGroups-"+val).iCheck('check');
                            });
                        }

                        if(obj.topicCompanyList.length > 0){
                            $('#topic-available-company-2').iCheck('check');
                            $('#company-list').removeClass('d-none');

                            obj.topicCompanyList.forEach(function(val,key) {
                                $("#checkCompanies-"+val).iCheck('check');
                            });
                        }

                        $("#modal-add-topic").modal('show');
                    } else {
                        showAlert(vocab['generic_error_msg'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnAddTopicSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddTopicClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddTopicSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddTopicClose").removeClass('disabled');
                }    
            });            
        }else{            
            msg = vocab['Alert_select_one'];
            showAlert(msg,'warning');
        }
    });

    $("#btnAddTopicSave").click(function(){
        if (!$("#modal-add-topic-form").valid()) {
            return false ;
        }

        var method,msg,msgError;

        if(!$("#modal-topic-id").val() || $("#modal-topic-id").val() == 0){
            method = "createTopic";
            msg = vocab['Alert_inserted'];
            msgError = vocab['Alert_failure'];
        }else{
            method = "updateTopic";
            msg = vocab['Edit_sucess'];
            msgError = vocab['Edit_failure'];
        }

        if(!$("#btnAddTopicSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkWarning/'+ method,
                dataType: 'json',
                data: $("#modal-add-topic-form").serialize() + "&_token="+ $("#_token").val(),
                error: function (ret) {
                    modalAlertMultiple('danger',msgError,'alert-modal-add-topic');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        modalAlertMultiple('success',msg,'alert-modal-add-topic');

                        setTimeout(function(){
                            $('#modal-add-topic').modal('hide');
                            $("#grid_topics").pqGrid("refreshDataAndView");
                        },2000);
                    } else {
                        modalAlertMultiple('danger',msgError,'alert-modal-add-topic');
                    }
                },
                beforeSend: function(){
                    $("#btnAddTopicSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddTopicClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddTopicSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddTopicClose").removeClass('disabled');
                }    
            });
        }

        return false;
    });

    //close modal alert
    $('#modal-alert').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkWarning/topics" ;
    });

    $('#modal-search-filter').on('hidden.bs.modal', function() { 
        $("#search-modal-form").trigger('reset');
        $("#filter-list").trigger("change");
        $("#action-list").trigger("change");        
    });

    $('#modal-add-topic').on('hidden.bs.modal', function() {
        
        $("input[name='checkGroups[]']").each(function() {
            var id = $(this).attr('id');
            $("#"+id).iCheck('unCheck');
        });

        $("input[name='checkCompanies[]']").each(function() {
            var id = $(this).attr('id');
            $("#"+id).iCheck('unCheck');
        });

        $('input[name=modal-topic-send-email]:checked').iCheck('unCheck');
        $('input[name=topic-available-group]:checked').iCheck('unCheck');
        $('input[name=topic-available-company]:checked').iCheck('unCheck');
        
        $('#topic-available-group-1').iCheck('check');
        $('#topic-available-company-1').iCheck('check');
        $('#group-list').addClass('d-none');
        $('#company-list').addClass('d-none');

        $("#modal-add-topic-form").trigger('reset');
    });

    $('.lbltooltip').tooltip();

    $("input[name=topic-available-group]").on('ifClicked',function(){
        if($(this).val() == 1){
            $('#group-list').addClass('d-none');
            
            $("input[name='checkGroups[]']").each(function() {
                var id = $(this).attr('id');
                $("#"+id).iCheck('unCheck');
            });
        }else{
            $('#group-list').removeClass('d-none');
        }
    });

    $("input[name=topic-available-company]").on('ifClicked',function(){
        if($(this).val() == 1){
            $('#company-list').addClass('d-none');
            
            $("input[name='checkCompanies[]']").each(function() {
                var id = $(this).attr('id');
                $("#"+id).iCheck('unCheck');
            });
        }else{
            $('#company-list').removeClass('d-none');
        }
    });

    $("input[name=modal-validity]").on('ifClicked',function(){
        if($(this).val() == 1){
            $('#modal-validity-hours').val('').attr('disabled','disabled');
            $('#modal-validity-days').val('').attr('disabled','disabled');
        }else if($(this).val() == 2){
            $('#modal-validity-days').val('').attr('disabled','disabled');
            $('#modal-validity-hours').removeAttr('disabled');
        }else{
            $('#modal-validity-hours').val('').attr('disabled','disabled');
            $('#modal-validity-days').removeAttr('disabled');
        }
    });

    /**
     * Validate
     */
    $("#modal-add-topic-form").validate({
        ignore:[],
        rules: {
            'modal-topic-name':{required:true},
            'modal-validity-hours':{required:function(element){return $('#modal-validity-2').is(':checked');}},
            'modal-validity-days':{required:function(element){return $('#modal-validity-3').is(':checked');}}
        },
        messages: {            
            'modal-topic-name':{required:vocab['Alert_field_required']},
            'modal-validity-hours':{required:vocab['Alert_field_required']},
            'modal-validity-days':{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $('#modal-validity-hours').mask("000");
    $('#modal-validity-days').mask("0000");
});
