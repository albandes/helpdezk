$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     *  Chosen
     */
    $("#cmbCompany").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});

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

    var grid = $("#table_list_emails");
    var stateCmb = "0:"+makeSmartyLabel('emq_state_wait_process')+";1:"+makeSmartyLabel('emq_state_send')+";2:Deferral;3:Hard Bounce;4:Soft Bounce;5:"+makeSmartyLabel('emq_state_open')+";6:Spam;7:"+makeSmartyLabel('emq_state_reject');

    grid.jqGrid({
        url: path+"/fin/finBankSlipEmail/jsonGrid/idschedule/"+$("#scheduleID").val(),
        datatype: "json",
        mtype: 'POST',
        sortname: 'status', //initially sorted on code_request
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 16,
        rowList: [16, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Grid_subject'),makeSmartyLabel('emq_from_name'),makeSmartyLabel('emq_to_name'),makeSmartyLabel('itm_student'),makeSmartyLabel('status'),makeSmartyLabel('Date'),''],
        colModel:[
            {name:'idemail',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'subject',index:'subject', editable: true, width:100, search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'sender',index:'sender', editable: true, width:100, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'email',index:'toname', editable: true, width:100, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'student',index:'student', editable: true, width:100, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'status',index:'status', editable: true, width:70, align:"center",search:true, sorttype: 'string', stype:'select', searchoptions: { sopt: ['eq'], value: stateCmb} },
            {name:'ts',index:'ts', editable: true, width:70, align:"center", search:true, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'], dataInit:objSearch.searchDate} },
            {name:'statusval',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true }

        ],
        pager: "#pager_list_emails",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('emq_email_sent'),
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var idemail = grid.jqGrid('getCell', rowId, 'idemail');
                
            console.log(idemail); //$('#modal-view-email').modal('show');
            viewMessage(idemail);

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
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('emq_email_sent'));

    // Setup buttons
    grid.navGrid('#pager_list_emails',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});

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
    $("#btnBack").click(function(){
        location.href = path + "/fin/finBankSlipEmail/index";
    });

    $("#btnEcho").click(function(){
        var myGrid = $('#table_list_emails'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idemail = myGrid.jqGrid ('getCell', selRowId, 'idemail');

        if (!idemail) {
            showAlert(makeSmartyLabel('Alert_select_one'),'warning','');
        } else {
            viewMessage(idemail);
        }
    });

    $("#btnReSend").click(function(){
        if(!$("#btnReSend").hasClass("disabled")){
            $.ajax({
                type: "POST",
                url: path + '/fin/finBankSlipEmail/resend',
                dataType: 'json',
                data: {
                    _token : $('#_token').val(),
                    emailID: $('#emailID').val()
                },
                error: function (ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    modalAlertMultiple('danger',obj.message,'alert-email');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.success){
                        console.log('ok');
                        modalAlertMultiple(obj.alerttype,obj.message,'alert-email');
                        setTimeout(function(){
                            $('#modal-view-email').modal('hide');
                            $('#email-form').trigger('reset');
                            grid.trigger('reloadGrid');
                        },2000);

                    }else{
                        modalAlertMultiple(obj.alerttype,obj.message,'alert-email');
                    }
                },
                beforeSend: function(){
                    $("#btnReSend").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnReSend").html("<i class='fa fa-share-square'></i> "+ makeSmartyLabel('resend')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }

            });
        }
    });


    /*
     * Validate
     */
    $("#schedule-form").validate({
        ignore:[],
        rules: {
            cmbCompany: "required"
        },
        messages: {
            cmbCompany:    makeSmartyLabel('Alert_field_required')
        }
    });

    /* clear modal's fields */
    $('#modal-form-schedule').on('hidden.bs.modal', function() {
        $('#schedule-form').trigger('reset');
        $('#cmbCompany').val('').trigger("chosen:updated");
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

/*function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}*/

function fontColorFormat(cellvalue, options, rowObject) {
    var color = "blue";
    var cellHtml = "<span style='color:" + color + "' originalValue='" + cellvalue + "'>" + cellvalue + "</span>";
    return cellHtml;
}

function viewMessage(idemail)
{
    $.ajax({
        type: "POST",
        url: path + '/fin/finBankSlipEmail/viewEmail',
        dataType: 'json',
        data: {
            idemail: idemail,
            _token : $('#_token').val()
        },
        error: function (ret) {
            showAlert(makeSmartyLabel('Edit_failure'),'danger','');
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            $('#emailStatus').html(obj.status);
            $('#emailTo').html(obj.to);
            $('#emailSubject').html(obj.subject);
            $('#emailMessage').html(obj.body);
            $('#emailServer').html(obj.senderserver);

            if(obj.student && obj.student != ''){
                $('#studentData').html(obj.student);
                $('#studentLine').removeClass('hide');
            }

            if(obj.attachaments && obj.attachaments != ''){
                $('#emailAttachs').html(obj.attachaments);
                $('#attachLine').removeClass('hide');
            }

            if(obj.sent_date && obj.sent_date != ''){
                $('#emailSent').html(obj.sent_date);
                $('.sentLine').removeClass('hide');
            }

            if(obj.logmandrill && obj.logmandrill != ''){
                $('#mandrillLog tbody').html(obj.logmandrill);
                $('#mandrillLogLine').removeClass('hide');
            }

            if(obj.statusID != '1' && obj.statusID != '5'){
                $('#srvSent').removeClass('hide');
                $('#btnReSend').removeClass('hide');
            }else if(obj.statusID == '1' && !$('#srvSent').hasClass('hide')){
                $('#srvSent').addClass('hide');
            }


            //$('#emailTo').html(obj.to);
            $('#emailID').val(idemail);
            $('#modal-view-email').modal('show');
        },
        beforeSend: function(){
            $('#emailStatus').html('');
            $('#emailTo').html('');
            $('#emailSubject').html('');
            $('#emailMessage').html('');
            $('#emailServer').html('');

            $('#studentData').html('');
            if(!$('#studentLine').hasClass('hide'))
                $('#studentLine').addClass('hide');

            $('#emailAttachs').html('');
            if(!$('#attachLine').hasClass('hide'))
                $('#attachLine').addClass('hide');

            $('#emailSent').html('');
            if(!$('.sentLine').hasClass('hide'))
                $('.sentLine').addClass('hide');

            $('#mandrillLog tbody').html('');
            if(!$('#mandrillLogLine').hasClass('hide'))
                $('#mandrillLogLine').addClass('hide');

            if(!$('#btnReSend').hasClass('hide'))
                $('#btnReSend').addClass('hide');
        }

    });

    return false;
}