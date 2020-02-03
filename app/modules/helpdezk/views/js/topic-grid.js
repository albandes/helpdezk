$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    var grid = $("#table_list_topics");

    grid.jqGrid({
        url: path+"/helpdezk/hdkWarning/jsonTopicGrid",
        datatype: "json",
        mtype: 'POST',
        sortname: 'title', //initially sorted on title_topic
        sortorder: "asc",
        height: 450,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 15,
        rowList: [10, 15, 20, 25, 30, 50],
        colNames:['',makeSmartyLabel('Topic'),makeSmartyLabel('Validity_Standard'),makeSmartyLabel('Send_email')],
        colModel:[
            {name:'idtopic',editable: false, width:9, align:"center",sortable: false, search:false, hidden: true },
            {name:'topico', index:'title', editable: true, width:200, sorttype:"string", search:true, searchoptions: {sopt:  ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en'] }, align:"left"},
            {name:'validade',index:'default_display', align:"center", editable: true, width:50, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']} },
            {name:'enviaemail',index:'fl_emailsent', align:"center", editable: true, width:50, search:false, sorttype: 'string',searchoptions: { sopt: ['eq', 'bw', 'bn', 'cn', 'nc', 'ew', 'en']}, align:"center" }
        ],
        pager: "#pager_list_topics",
        viewrecords: true,
        caption: ' :: '+makeSmartyLabel('Topic')+'s',
        hidegrid: false,
        toppager:false,
        //jqModal: false,
        //modal: true,
        ondblClickRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'idtopic');
            //document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + myCellData;
            console.log(myCellData);

        },
        onSelectRow: function(rowId) {
            var myCellData = grid.jqGrid('getCell', rowId, 'idtopic');
            var myCellStatus = grid.jqGrid('getCell', rowId, 'status');

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
            id: "idtopic"   // row ID
        }

    });

    // First time, show tBeing attended Tickets, then need to set the label
    grid.jqGrid('setCaption', ' :: '+makeSmartyLabel('Topic')+'s');

    // Setup buttons
    grid.navGrid('#pager_list_topics',{edit:false,add:false,del:false,search:true,searchtext: makeSmartyLabel('Search'),refreshtext: makeSmartyLabel('Grid_reload'),cloneToTop: true});
    grid.jqGrid('navGrid','#pager_list_topics',{search:true,cloneToTop:true});


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
    $(grid['selector']+"_toppager_left").attr("colspan", "4");

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        grid.setGridWidth(width);
    });


    var objTopicData = {
        getOperatorGroup: function(idtopic) {
            $.post(path+"/helpdezk/hdkWarning/ajaxOperatorGroup",{topicId: idtopic},
                function(valor){
                    $("#availableOpe_list").html(valor);
                })
        },
        getCorporation: function(idtopic) {
            $.post(path+"/helpdezk/hdkWarning/ajaxCorporation",{topicId: idtopic},
                function(valor) {
                    $("#availableUser_list").html(valor);
                });
        },
        getInfo: function(idtopic) {
            $.post(path+"/helpdezk/hdkWarning/ajaxTopicInfo",{topicId: idtopic},
                function(valor) {
					$("#idtopic").val(idtopic);
                    $("#modal_topic_title_upd").val(valor.title);
                    
					if(valor.type == 'P'){$('#validity_1_upd').iCheck('check');}
					else if(valor.type == 'H'){$('#validity_2_upd').iCheck('check'); $('#hoursValidity_upd').val(valor.timedef);}
					else{$('#validity_3_upd').iCheck('check'); $('#daysValidity_upd').val(valor.timedef);}
					
					if(valor.fl_emailsent == 'S'){$('#send-email-topic_upd').iCheck('check');}
					else{$('#send-email-topic_upd').iCheck('uncheck');}
					
					if(valor.avalGroup == 2){$('#availableOperator_2').iCheck('check'); $('#availableOpe_line').removeClass('hide');}
					else{$('#availableOperator_1').iCheck('check'); $('#availableOpe_line').addClass('hide');}
					
					if(valor.avalCorp == 2){$('#availableUser_2').iCheck('check'); $('#availableUser_line').removeClass('hide');}
					else{$('#availableUser_1').iCheck('check'); $('#availableUser_line').addClass('hide');}
					
                },'json');
        },
        getOperatorGroupNew: function(idtopic) {
            $.post(path+"/helpdezk/hdkWarning/ajaxOperatorGroup",
                function(valor){
                    $("#availableOpe_listNew").html(valor);
                })
        },
        getCorporationNew: function(idtopic) {
            $.post(path+"/helpdezk/hdkWarning/ajaxCorporation",
                function(valor) {
                    $("#availableUser_listNew").html(valor);
                });
        }
    }

    // Buttons
    $("#btnUpdate").click(function(){
        //console.log('edit');
        var myGrid = $('#table_list_topics'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idTopic = myGrid.jqGrid ('getCell', selRowId, 'idtopic');

        if (!idTopic) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html('Marque um t&oacute;pico.');
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');
        } else {
            $('#topic-form-update').trigger('reset');
            objTopicData.getOperatorGroup(idTopic);
            objTopicData.getCorporation(idTopic);
            objTopicData.getInfo(idTopic);
            $('#modal-form-topic-update').modal('show');
        }
    });

    $("#btnCreate").click(function(){
        $('#topic-form').trigger('reset');
        objTopicData.getOperatorGroupNew();
        objTopicData.getCorporationNew();
        $('#modal-form-topic').modal('show');
    });

    $("#btnBack").click(function(){
        location.href = path + "/helpdezk/hdkWarning/index" ;
    });

    $("#btnEnable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPerson = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPerson,'A');
    });

    $("#btnDisable").click(function(){
        var myGrid = $('#table_list_tickets'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow'),
            idPerson = myGrid.jqGrid ('getCell', selRowId, 'id');

        postStatus(idPerson,'I');
    });

    $("#btnSendTopic").click(function(){
        console.log('clicou salvar');
        if (!$("#topic-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkWarning/createTopic',
            dataType: 'json',
            data: $('#topic-form').serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-topic');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idtopic)) {
                    modalAlertMultiple('success','T&oacute;pico inclu&iacute;do com sucesso !','alert-topic');
                    setTimeout(function(){
                        $('#modal-form-topic').modal('hide');                        
                        $("input[name='validity']:checked").iCheck('unCheck');
                        $("input[name=availableOperatorNew][value=1]").iCheck('check');
                        $("input[name=availableUserNew][value=1]").iCheck('check');
                        $('#availableOpe_lineNew').addClass('hide');
                        $('#availableUser_lineNew').addClass('hide');
                        $('#topic-form').trigger('reset');
                        grid.trigger('reloadGrid');
                    },2000);
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-topic');
                }
            }
        });

    });

    $("#btnSendTopicUpdate").click(function(){
        console.log('clicou salvar');
        if (!$("#topic-form-update").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkWarning/updateTopic',
            dataType: 'json',
            data: $('#topic-form-update').serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-topic-update');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.status == 'OK') {
                    modalAlertMultiple('success','T&oacute;pico atualizado com sucesso !','alert-topic-update');
                    setTimeout(function(){
                        $('#modal-form-topic-update').modal('hide');
                        $('input[name=validity_upd]:checked').iCheck('unCheck');
                        $("input[name=availableOperator][value=1]").iCheck('check');
                        $("input[name=availableUser][value=1]").iCheck('check');
                        $('#availableOpe_line').addClass('hide');
                        $('#availableUser_line').addClass('hide');
                        $('#topic-form-update').trigger('reset');
						grid.trigger('reloadGrid');
                    },2000);					
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-topic-update');
                }
            }
        });

    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('input[name=availableOperator]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#availableOpe_line').addClass('hide');
        }else{
            $('#availableOpe_line').removeClass('hide');
        }
    });

    $('input[name=availableUser]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#availableUser_line').addClass('hide');
        }else{
            $('#availableUser_line').removeClass('hide');
        }
    });

    $('input[name=availableOperatorNew]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#availableOpe_lineNew').addClass('hide');
        }else{
            $('#availableOpe_lineNew').removeClass('hide');
        }
    });

    $('input[name=availableUserNew]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#availableUser_lineNew').addClass('hide');
        }else{
            $('#availableUser_lineNew').removeClass('hide');
        }
    });

    $("#topic-form").validate({
        ignore:[],
        rules: {modal_topic_title: "required"
        },
        messages: {
            modal_topic_title: "T&iacute;tulo do T&oacute;pico &eacute; obrigat&oacute;rio."
        }
    });

    $("#topic-form-update").validate({
        ignore:[],
        rules: {modal_topic_title_upd: "required"
        },
        messages: {
            modal_topic_title_upd: "T&iacute;tulo do T&oacute;pico &eacute; obrigat&oacute;rio."
        }
    });

    $('input[name=validity]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#hoursValidity').val("");
            $('#daysValidity').val("");
        }else if($(this).val() == 2){
            $('#hoursValidity').focus();
            $('#daysValidity').val("");
        }else{
            $('#hoursValidity').val("");
            $('#daysValidity').focus();
        }
    });

    $('input[name=validity_upd]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#hoursValidity_upd').val("");
            $('#daysValidity_upd').val("");
        }else if($(this).val() == 2){
            $('#hoursValidity_upd').focus();
            $('#daysValidity_upd').val("");
        }else{
            $('#hoursValidity_upd').val("");
            $('#daysValidity_upd').focus();
        }
    });

    /* limpa campos modal */
    $('.modal').on('hidden.bs.modal', function() {        
        $("input[name='validity']:checked").iCheck('unCheck');
        $("input[name=availableOperatorNew][value=1]").iCheck('check');
        $("input[name=availableUserNew][value=1]").iCheck('check');
        $('#availableOpe_lineNew').addClass('hide');
        $('#availableUser_lineNew').addClass('hide');
        $('#topic-form').trigger('reset');
        
        $('input[name=validity_upd]:checked').iCheck('unCheck');
        $("input[name=availableOperator][value=1]").iCheck('check');
        $("input[name=availableUser][value=1]").iCheck('check');
        $('#availableOpe_line').addClass('hide');
        $('#availableUser_line').addClass('hide');
        $('#topic-form-update').trigger('reset');
    });

});

function postStatus(idPerson,newStatus)
{
    $.ajax({
        type: "POST",
        url: path + '/spm/spmCadastroAtleta/statusAtleta/idperson/' + idPerson,
        dataType: 'json',
        data: {
            newstatus: newStatus
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-atleta');

        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.status == 'OK' ) {

                var idperson = obj.idperson;
                $('#modal-notification').html('Atleta atualizado com sucesso');
                $("#btn-modal-ok").attr("href", path + '/spm/spmCadastroAtleta/index');
                $("#tipo_alerta").attr('class', 'alert alert-success');
                $('#modal-alert').modal('show');

            } else {

                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-atleta');

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
