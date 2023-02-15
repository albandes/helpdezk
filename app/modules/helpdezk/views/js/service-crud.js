var objServices = {
    changeAreaStatus: function(areaId,newStatus,modal=false){
        $.post(path + '/helpdezk/hdkService/changeAreaStatus', {
            areaId : areaId,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {
            var obj = jQuery.parseJSON(JSON.stringify(response));
            if (!obj.success) {
                showAlert(obj.message,'danger');
                if(newStatus == 'A'){
                    $('#area_'+areaId).iCheck('uncheck');
                    if(modal){
                        $('#areaModal_'+areaId).iCheck('uncheck');
                    }
                }else{
                    $('#area_'+areaId).iCheck('check');
                    if(modal){
                        $('#areaModal_'+areaId).iCheck('check');
                    }
                }
            }else{
                if(modal){
                    if(newStatus == 'A'){
                        $('#area_'+areaId).iCheck('check');
                    }else{
                        $('#area_'+areaId).iCheck('uncheck');
                    }
                }
            }
        },'json');
    },
    changeTypeStatus: function(typeId,newStatus){
        $.post(path + '/helpdezk/hdkService/changeTypeStatus', {
            typeId : typeId,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {
            var obj = jQuery.parseJSON(JSON.stringify(response));
            if (!obj.success) {
                showAlert(obj.message,'danger');
                if(newStatus == 'A'){
                    $('#type_'+typeId).iCheck('uncheck');
                }else{
                    $('#type_'+typeId).iCheck('check');
                }
            }
        },'json');
    },
    changeItemStatus: function(itemId,newStatus){
        $.post(path + '/helpdezk/hdkService/changeItemStatus', {
            itemId : itemId,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {
            var obj = jQuery.parseJSON(JSON.stringify(response));
            if (!obj.success) {
                showAlert(obj.message,'danger');
                if(newStatus == 'A'){
                    $('#item_'+itemId).iCheck('uncheck');
                }else{
                    $('#item_'+itemId).iCheck('check');
                }
            }
        },'json');
    },
    changeServiceStatus: function(serviceId,newStatus){
        $.post(path + '/helpdezk/hdkService/changeServiceStatus', {
            serviceId : serviceId,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {
            var obj = jQuery.parseJSON(JSON.stringify(response));
            if (!obj.success) {
                showAlert(obj.message,'danger');
                if(newStatus == 'A'){
                    $('#service_'+serviceId).iCheck('uncheck');
                }else{
                    $('#service_'+serviceId).iCheck('check');
                }
            }
        },'json');
    },
    changeItem: function(){
        var typeID = $("#modal-cmb-type").val();
        $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeID: typeID},
            function(valor){
                $("#modal-cmb-item").html(valor);
                $("#modal-cmb-item").trigger("change");
                return objServices.changeService();
            });
    },
    changeService: function(){
        var itemID = $("#modal-cmb-item").val();
        
        $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemID: itemID},
            function(valor){
                $("#modal-cmb-service").html(valor);
                $("#modal-cmb-service").trigger("change");
                return objServices.changeUsers();
            });
    },
    changeUsers: function(){
        var itemId = $("#modal-cmb-item").val(), serviceId = $("#modal-cmb-service").val();

        if((itemId != "" && itemId > 0) && (serviceId != "" && serviceId > 0)){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/getApprovers",
                data:{
                    _token: $("#_token").val(),
                    itemId: itemId,
                    serviceId: serviceId
                },
                dataType: 'json',
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-config-approval');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.success) {
    
                        $("#approverList tbody").html(obj.approverList);
    
                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });
    
                        if(obj.flgRecalculate == '1'){
                            $('#modal-recalculate').iCheck('check');
                        }else{
                            $("#modal-recalculate").iCheck('uncheck');
                        }
    
                        $(".btn-up,.btn-down").click(function(){
                            var row = $(this).parents("tr:first");
                            if ($(this).is(".btn-up")) {
                                row.insertBefore(row.prev());
                            } else {
                                row.insertAfter(row.next());
                            }
                        });
    
                        $(".btn-remove").click(function(){
                            var row = $(this).parents("tr"), targetId = $(this).parents("tr").find('.approver').val();
                            row.remove();
                            if($("#approverList tbody tr").length <= 0){
                                if(!$(".approverListView").hasClass('d-none'))
                                    $(".approverListView").addClass('d-none');
                            }
                        });

                        if($(".approverListView").hasClass('d-none'))
                            $(".approverListView").removeClass('d-none');
                    }
                },
                beforeSend: function(){
                    $("#modal-recalculate").iCheck('uncheck');
                    if(!$(".approverListView").hasClass('d-none'))
                        $(".approverListView").addClass('d-none');
                }
            });
        }
    },
    insertApprover: function(approverId, approverName){
        var tBody = $("#approverList tr") , tBodyComp = '', isExist = 0;
        if($("input[name='approver[]']").length > 0){
            $("input[name='approver[]']").each(function(index, element) {
                if($(this).val() == approverId)
                    isExist = 1;
            });
        }

        if(isExist == 0){
            tBodyComp = "<tr>"+
                        "<td>"+approverName+"<input type='hidden' class='approver' name='approver[]' id='approver_"+approverId+"' value='"+approverId+"'></td>"+
                        "<td>"+
                            "<a href='#' class='btn btn-success btn-up'><i class='fa fa-sort-up'></i></a>"+
                            "<a href='#' class='btn btn-primary btn-down'><i class='fa fa-sort-down'></i></a>"+
                        "</td>"+
                        "<td><a href='#' class='btn btn-danger btn-remove'><i class='fa fa-times'></i></a></td>"+
                    "</tr>";

            $("#approverList tbody").append(tBodyComp);

            $(".btn-up,.btn-down").click(function(){
                var row = $(this).parents("tr:first");
                if ($(this).is(".btn-up")) {
                    row.insertBefore(row.prev());
                } else {
                    row.insertAfter(row.next());
                }
            });

            $(".btn-remove").click(function(){
                var row = $(this).parents("tr"), targetId = $(this).parents("tr").find('.approver').val();            
                row.remove();
                if($("#approverList tbody tr").length <= 0){
                    if(!$(".approverListView").hasClass('d-none'))
                        $(".approverListView").addClass('d-none');
                }
            });

            if($("#approverList tbody tr").length > 0) {
                if($(".approverListView").hasClass('d-none'))
                    $(".approverListView").removeClass('d-none');
            }

        }else{
            modalAlertMultiple('danger',vocab['approver_listed'],'alert-modal-config-approval');
        }
        
        $("#modal-approver").val("");
        $("#modal-approver-flexdatalist").val("");
    }
}

$(document).ready(function () {
    countdown.start(timesession);
    
    /**
     * Select2
     */
    $('#modal-cmb-area').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-type-form')});
    $('#modal-service-group').select2({width:"100%",height:"100%",placeholder:vocab['Select_group'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-service-form')});
    $('#modal-service-priority').select2({width:"100%",height:"100%",placeholder:vocab['Select_priority'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-service-form')});
    $('#modal-cmb-type').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-config-approval-form')});
    $('#modal-cmb-item').select2({width:"100%",height:"100%",placeholder:vocab['Alert_choose_type'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-config-approval-form')});
    $('#modal-cmb-service').select2({width:"100%",height:"100%",placeholder:vocab['Alert_choose_item'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-config-approval-form')});
    
    // tooltips
    $('.tooltip-buttons').tooltip();

    /**
     * iCheck - checkboxes/radios styling
     */
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('.checkArea').on('ifChecked ifUnchecked',function(e){
        var areaId = e.target.attributes.value.nodeValue;

        if(e.type == 'ifChecked'){
            objServices.changeAreaStatus(areaId,'A');
        }else{
            objServices.changeAreaStatus(areaId,'N');
        }
    });

    $('.checkType').on('ifChecked ifUnchecked',function(e){
        var typeId = e.target.attributes.value.nodeValue;

        if(e.type == 'ifChecked'){
            objServices.changeTypeStatus(typeId,'A');
        }else{
            objServices.changeTypeStatus(typeId,'N');
        }
    });
    
    /**
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/person/index');

    $("#btnNewArea").click(function(){
        if(!$("#btnNewArea").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/modalArea",
                dataType: 'json',
                data:{
                    _token: $("#_token").val()
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.success) {
                        $("#registeredAreas tbody").html(obj.areaList);
                        
                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });


                        $('.checkAreaModal').on('ifChecked ifUnchecked',function(e){
                            var areaId = e.target.attributes.value.nodeValue, node = "#area_"+areaId;
                    
                            if(e.type == 'ifChecked'){
                                objServices.changeAreaStatus(areaId,'A',true);
                            }else{
                                objServices.changeAreaStatus(areaId,'N',true);
                            }
                        });

                        $("#modal-add-area").modal("show");
    
                    } else {
                        showAlert(vocab['Permission_error'],'danger');
                    }
                }
            });
        }
    });

    $("#btnAddAreaSave").click(function(){

        if (!$("#modal-add-area-form").valid()) {
            return false ;
        }

        if(!$("#btnAddAreaSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkService/createArea',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    areaName:$('#modal-area-name').val(),
                    flagDefault:($('#modal-area-default').is(':checked')) ? 1 : 0
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-area');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_inserted'],'alert-modal-add-area');

                        $("#modal-add-area-form").trigger('reset');
                        $('#modal-area-default').iCheck('uncheck');

                        $("#registeredAreas tbody").html(obj.areaList);
                        $("#areaTypeList").html(obj.areaTypeList);

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green',
                        });

                        $('.checkAreaModal').on('ifChecked ifUnchecked',function(e){
                            var areaId = e.target.attributes.value.nodeValue, node = "#area_"+areaId;
                    
                            if(e.type == 'ifChecked'){
                                objServices.changeAreaStatus(areaId,'A',true);
                            }else{
                                objServices.changeAreaStatus(areaId,'N',true);
                            }
                        });

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-area');
                    }
                },
                beforeSend: function(){
                    $("#btnAddAreaSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAddAreaSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }

    });

    $("#btnUpdAreaSave").click(function(){

        if (!$("#modal-upd-area-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdAreaSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkService/updateArea',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    areaId:$('#areaId').val(),
                    areaName:$('#modal-upd-area-name').val(),
                    flagDefault:($('#modal-upd-area-default').is(':checked')) ? 1 : 0
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-upd-area');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_success_update'],'alert-modal-upd-area');

                        setTimeout(function(){
                            $('#modal-upd-area').modal('hide');
                            $("#modal-upd-area-default").iCheck('unCheck');
                            $('#modal-upd-area-form').trigger('reset');
                            location.href = path + "/helpdezk/hdkService/index";
                        },2000);

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-upd-area');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdAreaSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdAreaSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }

    });

    $("#btnNewType").click(function(){
        if(!$("#btnNewType").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/modalType",
                dataType: 'json',
                data:{
                    _token: $("#_token").val(),
                    viewType: 'add'
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.success) {
                        $("#typeId").val("");
                        $("#modal-cmb-area").html(obj.cmbArea).val(obj.areaDefault).trigger("change");
                        $("#modal-type-name").val(obj.typeName);

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });

                        if(obj.typeStatus == "A"){
                            $("#modal-type-available").iCheck("check");
                        }else{
                            $("#modal-type-available").iCheck("uncheck");
                        }
                        
                        $("#modal-add-type-title").html("<strong>"+vocab['Type_insert']+"</strong>")
                        $("#modal-add-type").modal("show");
    
                    } else {
                        showAlert(vocab['Permission_error'],'danger');
                    }
                }
            });
        }
    });

    $("#btnAddTypeSave").click(function(){
        if (!$("#modal-add-type-form").valid()) {
            return false ;
        }

        var method = ($('#typeId').val() == '' || $('#typeId').val() == 0) ? 'createType' : 'updateType', 
            msg = ($('#typeId').val() == '' || $('#typeId').val() == 0) ? vocab['Alert_inserted'] : vocab['Alert_success_update'];


        if(!$("#btnAddTypeSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkService/'+method,
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    typeId:$('#typeId').val(),
                    areaId:$('#modal-cmb-area').val(),
                    typeName:$('#modal-type-name').val(),
                    flagAvaliable:($('#modal-type-available').is(':checked')) ? 1 : 0,
                    flagDefault:($('#modal-type-default').is(':checked')) ? 1 : 0,
                    flagClassify:($('#modal-type-classification').is(':checked')) ? 1 : 0
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-type');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', msg,'alert-modal-add-type');

                        setTimeout(function(){
                            $('#modal-add-type').modal('hide');
                            $('#modal-add-type-form').trigger('reset');
                            location.href = path + "/helpdezk/hdkService/index" ;
                        },2000);

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-type');
                    }
                },
                beforeSend: function(){
                    $("#btnAddTypeSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAddTypeSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }      
    });

    $("#btnNewItem").click(function(){
        if(!$("#btnNewItem").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/modalItem",
                dataType: 'json',
                data:{
                    _token: $("#_token").val(),
                    viewItem: 'add'
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.success) {
                        $("#modal-item-type").val($("#typeId").val());
                        $("#modal-item-id").val("");
                        
                        $("#modal-item-name").val(obj.itemName);

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });

                        if(obj.itemStatus == "A"){
                            $("#modal-item-available").iCheck("check");
                        }else{
                            $("#modal-item-available").iCheck("uncheck");
                        }

                        if(obj.itemDefault == 1){
                            $("#modal-item-default").iCheck("check");
                        }else{
                            $("#modal-item-default").iCheck("uncheck");
                        }

                        if(obj.itemClassify == 1){
                            $("#modal-item-classification").iCheck("check");
                        }else{
                            $("#modal-item-classification").iCheck("uncheck");
                        }
                        
                        $("#modal-add-item-title").html("<strong>"+vocab['Item_insert']+"</strong>")
                        $("#modal-add-item").modal("show");
    
                    } else {
                        showAlert(vocab['Permission_error'],'danger');
                    }
                }
            });
        }
    });

    $("#btnAddItemSave").click(function(){
        if (!$("#modal-add-item-form").valid()) {
            return false ;
        }

        var method = ($('#modal-item-id').val() == '' || $('#modal-item-id').val() == 0) ? 'createItem' : 'updateItem', 
            msg = ($('#modal-item-id').val() == '' || $('#modal-item-id').val() == 0) ? vocab['Alert_inserted'] : vocab['Alert_success_update'];


        if(!$("#btnAddItemSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkService/'+method,
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    itemId:$('#modal-item-id').val(),
                    typeId:$('#modal-item-type').val(),
                    itemName:$('#modal-item-name').val(),
                    flagAvaliable:($('#modal-item-available').is(':checked')) ? 1 : 0,
                    flagDefault:($('#modal-item-default').is(':checked')) ? 1 : 0,
                    flagClassify:($('#modal-item-classification').is(':checked')) ? 1 : 0
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-item');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', msg,'alert-modal-add-item');
                        viewType($("#typeId").val());

                        setTimeout(function(){
                            $('#modal-add-item').modal('hide');
                            $('#modal-add-item-form').trigger('reset');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-item');
                    }
                },
                beforeSend: function(){
                    $("#btnAddItemSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAddItemSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }      
    });

    $("#btnNewService").click(function(){
        if(!$("#btnNewService").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/modalService",
                dataType: 'json',
                data:{
                    _token: $("#_token").val(),
                    viewService: 'add'
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.success) {
                        $("#modal-service-item").val($("#itemId").val());
                        $("#modal-service-id").val("");
                        
                        $("#modal-service-name").val(obj.serviceName);
                        $("#modal-service-group").html(obj.cmbGroup).val(obj.groupDefault).trigger("change");
                        $("#modal-service-priority").html(obj.cmbPriority).val(obj.priorityDefault).trigger("change");

                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });

                        if(obj.serviceStatus == "A"){
                            $("#modal-service-available").iCheck("check");
                        }else{
                            $("#modal-service-available").iCheck("uncheck");
                        }

                        if(obj.serviceDefault == 1){
                            $("#modal-service-default").iCheck("check");
                        }else{
                            $("#modal-service-default").iCheck("uncheck");
                        }

                        if(obj.serviceClassify == 1){
                            $("#modal-service-classification").iCheck("check");
                        }else{
                            $("#modal-service-classification").iCheck("uncheck");
                        }
                        
                        $("#modal-add-service-title").html("<strong>"+vocab['Service_insert']+"</strong>")
                        $("#modal-add-service").modal("show");
    
                    } else {
                        showAlert(vocab['Permission_error'],'danger');
                    }
                }
            });
        }
    });

    $("#btnAddServiceSave").click(function(){
        if (!$("#modal-add-service-form").valid()) {
            return false ;
        }

        var method = ($('#modal-service-id').val() == '' || $('#modal-service-id').val() == 0) ? 'createService' : 'updateService', 
            msg = ($('#modal-service-id').val() == '' || $('#modal-service-id').val() == 0) ? vocab['Alert_inserted'] : vocab['Alert_success_update'];


        if(!$("#btnAddServiceSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkService/'+method,
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    serviceId:$('#modal-service-id').val(),
                    itemId:$('#modal-service-item').val(),
                    serviceName:$('#modal-service-name').val(),
                    groupId:$('#modal-service-group').val(),
                    priorityId:$('#modal-service-priority').val(),
                    limitDays:$('#service-limit-days').val(),
                    limitTime:$('#service-limit-time').val(),
                    timeType:$("input[name='service-time']:checked").val(),
                    flagAvaliable:($('#modal-service-available').is(':checked')) ? 1 : 0,
                    flagDefault:($('#modal-service-default').is(':checked')) ? 1 : 0,
                    flagClassify:($('#modal-service-classification').is(':checked')) ? 1 : 0
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-service');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', msg,'alert-modal-add-service');
                        viewItem($("#itemId").val());

                        setTimeout(function(){
                            $('#modal-add-service').modal('hide');
                            $('#modal-add-service-form').trigger('reset');
                            $("#time-hour").iCheck('check');
                            $("#modal-service-available").iCheck('unCheck');
                            $("#modal-service-default").iCheck('unCheck');
                            $("#modal-service-classification").iCheck('unCheck');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-service');
                    }
                },
                beforeSend: function(){
                    $("#btnAddServiceSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAddServiceSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }      
    });

    $("#btnDeleteYes").click(function(){

        if(!$("#btnDeleteYes").hasClass('disabled')){
            var targetId = $('#modal-target-id').val(), targetType = $('#modal-delete-type').val();

            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/deleteTarget",
                data: {
                    targetId:targetId,
                    targetType:targetType,
                    _token:$('#_token').val()
                },
                dataType: 'json',
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_deleted_error'],'alert-delete-service');
                },
                success: function(ret) {
                    if(ret){
                        var obj = jQuery.parseJSON(JSON.stringify(ret));

                        if(obj.success) {
                            modalAlertMultiple('success',obj.message,'alert-delete-service');
                            setTimeout(function(){
                                $('#modal-service-delete').modal('hide');
                                switch (targetType) {
                                    case "area":
                                    case "type":
                                        location.href = path + "/helpdezk/hdkService/index" ;
                                        break;
                                    case "item":
                                        viewType($("#typeId").val());
                                        break;
                                    default:
                                        viewItem($("#itemId").val());
                                }
                            },1500);

                        } else {
                            modalAlertMultiple('danger',obj.message,'alert-delete-service');
                        }
                    }
                    else {
                        modalAlertMultiple('danger',vocab['Alert_deleted_error'],'alert-delete-service');
                    }
                },
                beforeSend: function(){
                    $("#btnDeleteNo").addClass('disabled');
                    $("#btnDeleteYes").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnDeleteNo").removeClass('disabled');
                    $("#btnDeleteYes").html("<i class='fa fa-check-circle'></i> "+ vocab['Yes']).removeClass('disabled');
                }
            });
        }

        return false;  // <- cancel event
    });

    $("#btnConfigApproval").click(function(){
        if(!$("#btnConfigApproval").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/modalConfigApproval",
                dataType: 'json',
                data:{
                    _token: $("#_token").val()
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.success) {
                        $("#modal-cmb-type").html(obj.typeOptions).trigger("change");

                        $("#modal-config-approval").modal("show");
    
                    } else {
                        showAlert(vocab['Permission_error'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnConfigApproval").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnConfigApproval").html("<i class='fa fa-check-circle'></i> "+ vocab['conf_approvals']).removeClass('disabled');
                }
            });
        }
    });

    $('#modal-cmb-type').change(function(){
        objServices.changeItem();
    });

    $('#modal-cmb-item').change(function(){
        objServices.changeService();
    });

    $('#modal-cmb-service').change(function(){
        objServices.changeUsers();
    });

    $("#modal-approver").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: ["name"],
        minLength: 2,
        selectionRequired: true,
        valueProperty: 'id',
        textProperty: '{name}',
        url: path + '/helpdezk/hdkService/searchApprover',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post'
    });

    $("#modal-approver").on('select:flexdatalist', function (event, set, options) {
        objServices.insertApprover($(this).val(),set.name);
    });

    $("#btnConfigApprovalSave").click(function(){
        if (!$("#modal-config-approval-form").valid()) {
            return false ;
        }

        if(!$("#btnConfigApprovalSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkService/saveApprovalSetting',
                dataType: 'json',
                data: $('#modal-config-approval-form').serialize() + '&_token=' + $('#_token').val(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-config-approval');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {console.log('certo');
                        modalAlertMultiple('success',vocab['Alert_inserted'],'alert-modal-config-approval');

                        setTimeout(function(){
                            $('#modal-config-approval').modal('hide');
                            $('#modal-config-approval-form').trigger('reset');
                            $("#modal-recalculate").iCheck('uncheck');

                            $("#approverList tbody").html("");
                            $(".approverListView").addClass('d-none');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-config-approval');
                    }
                },
                beforeSend: function(){
                    $("#btnConfigApprovalSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnConfigApprovalSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }      
    });

    $("#btnApproversList").click(function(){
        if(!$("#btnApproversList").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkService/modalViewApprovers",
                dataType: 'json',
                data:{
                    _token: $("#_token").val()
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.success) {                        
                        $("#approversList tbody").html(obj.content);
                        $("#modal-view-approvers").modal("show");
                    } else {
                        showAlert(vocab['Permission_error'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnApproversList").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnApproversList").html("<i class='fa fa-user-check'></i> "+ vocab['view_approval_list']).removeClass('disabled');
                }
            });
        }
    });

    /**
     * Validate
     */
    $("#modal-add-area-form").validate({
        ignore:[],
        rules: {
            "modal-area-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                remote:{
                    param:{
                        url: path+"/helpdezk/hdkService/checkExistsArea",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{  
                            _token:function(element){return $("#_token").val()},
                        }
                    }
                }
            }
        },
        messages: {
            "modal-area-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-upd-area-form").validate({
        ignore:[],
        rules: {
            "modal-upd-area-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                remote:{
                    param:{
                        url: path+"/helpdezk/hdkService/checkExistsArea",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{  
                            _token:function(element){return $("#_token").val()},
                            areaId:function(element){return $("#areaId").val()}
                        }
                    }
                }
            }
        },
        messages: {
            "modal-upd-area-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-type-form").validate({
        ignore:[],
        rules: {
            "modal-cmb-area":{
                required:true
            },
            "modal-type-name":  {
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                remote:{
                    param:{
                        url: path+"/helpdezk/hdkService/checkExistsType",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{  
                            _token:function(element){return $("#_token").val()},
                            typeId:function(element){return $("#typeId").val()},
                            areaId:function(element){return $("#modal-cmb-area").val()}
                        }
                    }
                }
            }
        },
        messages: {
            "modal-cmb-area":{required:vocab['Alert_field_required']},
            "modal-type-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-item-form").validate({
        ignore:[],
        rules: {
            "modal-item-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                remote:{
                    param:{
                        url: path+"/helpdezk/hdkService/checkExistsItem",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{  
                            _token:function(element){return $("#_token").val()},
                            itemId:function(element){return $("#modal-item-id").val()},
                            typeId:function(element){return $("#modal-item-type").val()}
                        }
                    }
                }
            }
        },
        messages: {
            "modal-item-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-service-form").validate({
        ignore:[],
        rules: {
            "modal-service-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                remote:{
                    param:{
                        url: path+"/helpdezk/hdkService/checkExistsService",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{  
                            _token:function(element){return $("#_token").val()},
                            itemId:function(element){return $("#modal-service-item").val()},
                            serviceId:function(element){return $("#modal-service-id").val()}
                        }
                    }
                }
            },
            "modal-service-group":{required:true},
            "modal-service-priority":{required:true}
        },
        messages: {
            "modal-service-name":{required:vocab['Alert_field_required']},
            "modal-service-group":{required:vocab['Alert_field_required']},
            "modal-service-priority":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-config-approval-form").validate({
        ignore:[],
        rules: {
            "modal-cmb-type":{required:true},
            "modal-cmb-item":{required:true},
            "modal-cmb-service":{required:true}
        },
        messages: {
            "modal-cmb-type":{required:vocab['Alert_field_required']},
            "modal-cmb-item":{required:vocab['Alert_field_required']},
            "modal-cmb-service":{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-add-area').on('hidden.bs.modal', function() {
        $("#modal-add-area-form").trigger('reset');

        $('.checkArea').on('ifChecked ifUnchecked',function(e){
            var areaId = e.target.attributes.value.nodeValue;
    
            if(e.type == 'ifChecked'){
                objServices.changeAreaStatus(areaId,'A');
            }else{
                objServices.changeAreaStatus(areaId,'N');
            }
        });
    });

    $('#modal-upd-area').on('hidden.bs.modal', function() {
        $("#modal-upd-area-default").iCheck('unCheck');
        $('#modal-upd-area-form').trigger('reset');
    });

    $('#modal-add-item').on('hidden.bs.modal', function() {
        $('#modal-add-item-form').trigger('reset');
        
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });

        $("#modal-item-available").iCheck('unCheck');
        $("#modal-item-default").iCheck('unCheck');
        $("#modal-item-classification").iCheck('unCheck');

        $('.checkArea').on('ifChecked ifUnchecked',function(e){
            var areaId = e.target.attributes.value.nodeValue;
    
            if(e.type == 'ifChecked'){
                objServices.changeAreaStatus(areaId,'A');
            }else{
                objServices.changeAreaStatus(areaId,'N');
            }
        });

        $('.checkType').on('ifChecked ifUnchecked',function(e){
            var typeId = e.target.attributes.value.nodeValue;
    
            if(e.type == 'ifChecked'){
                objServices.changeTypeStatus(typeId,'A');
            }else{
                objServices.changeTypeStatus(typeId,'N');
            }
        });

        $('.checkItem').on('ifChecked ifUnchecked',function(e){
            var itemId = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeItemStatus(itemId,'A');
            }else{
                objServices.changeItemStatus(itemId,'N');
            }
        });

        $('.checkService').on('ifChecked ifUnchecked',function(e){
            var serviceId = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeServiceStatus(serviceId,'A');
            }else{
                objServices.changeServiceStatus(serviceId,'N');
            }
        });

    });

    $('#modal-add-service').on('hidden.bs.modal', function() {
        $('#modal-add-service-form').trigger('reset');
        
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });

        $("#time-hour").iCheck('check');
        $("#modal-service-available").iCheck('unCheck');
        $("#modal-service-default").iCheck('unCheck');
        $("#modal-service-classification").iCheck('unCheck');

        $('.checkArea').on('ifChecked ifUnchecked',function(e){
            var areaId = e.target.attributes.value.nodeValue;
    
            if(e.type == 'ifChecked'){
                objServices.changeAreaStatus(areaId,'A');
            }else{
                objServices.changeAreaStatus(areaId,'N');
            }
        });

        $('.checkType').on('ifChecked ifUnchecked',function(e){
            var typeId = e.target.attributes.value.nodeValue;
    
            if(e.type == 'ifChecked'){
                objServices.changeTypeStatus(typeId,'A');
            }else{
                objServices.changeTypeStatus(typeId,'N');
            }
        });

        $('.checkItem').on('ifChecked ifUnchecked',function(e){
            var itemId = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeItemStatus(itemId,'A');
            }else{
                objServices.changeItemStatus(itemId,'N');
            }
        });

        $('.checkService').on('ifChecked ifUnchecked',function(e){
            var serviceId = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeServiceStatus(serviceId,'A');
            }else{
                objServices.changeServiceStatus(serviceId,'N');
            }
        });

    });

    $('#modal-config-approval').on('hidden.bs.modal', function() {
        $('#modal-config-approval-form').trigger('reset');
        $("#modal-recalculate").iCheck('uncheck');

        $("#approverList tbody").html("");
        $(".approverListView").addClass('d-none');
    });

});

function editArea(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalAreaUpdate",
        data:{
            _token: $("#_token").val(),
            areaId:id
        },
        dataType: 'json',
        error: function (ret) {
            modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-add-area');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            //console.log(obj);
            if(obj.success) {
                $('#modal-add-area').modal('hide');

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                $('#modal-upd-area-name').val(obj.areaName);
                if(obj.areaDefault == 1){$('#modal-upd-area-default').iCheck('check');}

                $('#modal-upd-area').modal('show');
                $('#areaId').val(id);

            } else {
                modalAlertMultiple('danger',obj.message,'alert-modal-add-area');
            }
        }
    });
}

function editType(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalType",
        data:{
            _token: $("#_token").val(),
            viewType:'upd',
            typeId:id
        },
        dataType: 'json',
        error: function (ret) {
            showAlert(vocab['Permission_error'],'danger');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success) {
                $("#typeId").val(id);
                $("#modal-cmb-area").html(obj.cmbArea).val(obj.areaDefault).trigger("change");
                $("#modal-type-name").val(obj.typeName);

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                if(obj.typeStatus == "A"){
                    $("#modal-type-available").iCheck("check");
                }else{
                    $("#modal-type-available").iCheck("uncheck");
                }

                if(obj.flagDefault == 1){
                    $("#modal-type-default").iCheck("check");
                }else{
                    $("#modal-type-default").iCheck("uncheck");
                }

                if(obj.flagClassify == 1){
                    $("#modal-type-classification").iCheck("check");
                }else{
                    $("#modal-type-classification").iCheck("uncheck");
                }
                
                $("#modal-add-type-title").html("<strong>"+vocab['Type_edit']+"</strong>")
                $("#modal-add-type").modal("show");

            } else {
                showAlert(vocab['Permission_error'],'danger');
            }
        }
    });
    
}

function viewType(id) {
    
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkService/itemList',
        dataType: 'json',
        data: {
            _token : $("#_token").val(),
            typeId : id
        },
        error: function (ret) {
            showAlert(vocab['Permission_error'],'danger');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success){
                $('#itemList').removeClass('d-none').addClass('animated fadeInDown');
                $('#serviceList').addClass('d-none');
                $('#typeId').val(id);
                
                $('#typeTitle').html(obj.title);
                $('#itemLine').html(obj.itemList);

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                // tooltips
                $('.tooltip-buttons').tooltip();

                $('.checkArea').on('ifChecked ifUnchecked',function(e){
                    var areaId = e.target.attributes.value.nodeValue;
            
                    if(e.type == 'ifChecked'){
                        objServices.changeAreaStatus(areaId,'A');
                    }else{
                        objServices.changeAreaStatus(areaId,'N');
                    }
                });
            
                $('.checkType').on('ifChecked ifUnchecked',function(e){
                    var typeId = e.target.attributes.value.nodeValue;
            
                    if(e.type == 'ifChecked'){
                        objServices.changeTypeStatus(typeId,'A');
                    }else{
                        objServices.changeTypeStatus(typeId,'N');
                    }
                });

                $('.checkItem').on('ifChecked ifUnchecked',function(e){
                    var itemId = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeItemStatus(itemId,'A');
                    }else{
                        objServices.changeItemStatus(itemId,'N');
                    }
                });
            }else{
                showAlert(obj.message,'danger');
            }
        }
    });
}

function editItem(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalItem",
        data:{
            _token: $("#_token").val(),
            viewItem:'upd',
            itemId:id
        },
        dataType: 'json',
        error: function (ret) {
            showAlert(vocab['Permission_error'],'danger');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            
            if(obj.success) {
                $("#modal-item-type").val($("#typeId").val());
                $("#modal-item-id").val(id);
                
                $("#modal-item-name").val(obj.itemName);

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                if(obj.itemStatus == "A"){
                    $("#modal-item-available").iCheck("check");
                }else{
                    $("#modal-item-available").iCheck("uncheck");
                }

                if(obj.itemDefault == 1){
                    $("#modal-item-default").iCheck("check");
                }else{
                    $("#modal-item-default").iCheck("uncheck");
                }

                if(obj.itemClassify == 1){
                    $("#modal-item-classification").iCheck("check");
                }else{
                    $("#modal-item-classification").iCheck("uncheck");
                }
                
                $("#modal-add-item-title").html("<strong>"+vocab['Item_edit']+"</strong>")
                $("#modal-add-item").modal("show");

            } else {
                showAlert(vocab['Permission_error'],'danger');
            }
        }
    });

}

function viewItem(id) {

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkService/serviceList',
        dataType: 'json',
        data: {
            _token : $("#_token").val(),
            itemId : id
        },
        error: function (ret) {
            showAlert(vocab['Permission_error'],'danger');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success){
                $('#serviceList').removeClass('d-none').addClass('animated fadeInDown');
                $('#itemId').val(id);
                
                $('#itemTitle').html(obj.title);
                $('#serviceLine').html(obj.serviceList);

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                // tooltips
                $('.tooltip-buttons').tooltip();

                $('.checkArea').on('ifChecked ifUnchecked',function(e){
                    var areaId = e.target.attributes.value.nodeValue;
            
                    if(e.type == 'ifChecked'){
                        objServices.changeAreaStatus(areaId,'A');
                    }else{
                        objServices.changeAreaStatus(areaId,'N');
                    }
                });
            
                $('.checkType').on('ifChecked ifUnchecked',function(e){
                    var typeId = e.target.attributes.value.nodeValue;
            
                    if(e.type == 'ifChecked'){
                        objServices.changeTypeStatus(typeId,'A');
                    }else{
                        objServices.changeTypeStatus(typeId,'N');
                    }
                });

                $('.checkItem').on('ifChecked ifUnchecked',function(e){
                    var itemId = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeItemStatus(itemId,'A');
                    }else{
                        objServices.changeItemStatus(itemId,'N');
                    }
                });

                $('.checkService').on('ifChecked ifUnchecked',function(e){
                    var serviceId = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeServiceStatus(serviceId,'A');
                    }else{
                        objServices.changeServiceStatus(serviceId,'N');
                    }
                });
            }else{
                showAlert(obj.message,'danger');
            }
        }
    });
}

function editService(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalService",
        data:{
            _token: $("#_token").val(),
            viewService:'upd',
            serviceId:id
        },
        dataType: 'json',
        error: function (ret) {
            showAlert(vocab['Permission_error'],'danger');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            
            if(obj.success) {
                $("#modal-service-item").val($("#itemId").val());
                $("#modal-service-id").val(id);
                
                $("#modal-service-name").val(obj.serviceName);
                $("#modal-service-group").html(obj.cmbGroup).val(obj.groupDefault).trigger("change");
                $("#modal-service-priority").html(obj.cmbPriority).val(obj.priorityDefault).trigger("change");
                $("#service-limit-days").val(obj.limitDays);
                $("#service-limit-time").val(obj.limitTime);

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                if(obj.timeType == "H"){
                    $("#time-hour").iCheck("check");
                    $("#time-minute").iCheck("uncheck");
                }else{
                    $("#time-minute").iCheck("check");
                    $("#time-hour").iCheck("uncheck");
                }

                if(obj.serviceStatus == "A"){
                    $("#modal-service-available").iCheck("check");
                }else{
                    $("#modal-service-available").iCheck("uncheck");
                }

                if(obj.serviceDefault == 1){
                    $("#modal-service-default").iCheck("check");
                }else{
                    $("#modal-service-default").iCheck("uncheck");
                }

                if(obj.serviceClassify == 1){
                    $("#modal-service-classification").iCheck("check");
                }else{
                    $("#modal-service-classification").iCheck("uncheck");
                }
                
                $("#modal-add-service-title").html("<strong>"+vocab['Service_edit']+"</strong>")
                $("#modal-add-service").modal("show");

            } else {
                showAlert(vocab['Permission_error'],'danger');
            }
        }
    });

}

function deleteTarget(id,type) {

    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/checkDelete",
        data:{
            targetId:id,
            targetType:type,
            _token:$('#_token').val()
        },
        dataType: 'json',
        error: function (ret) {
            showAlert(vocab['Permission_error'],'danger','');
        },
        success: function(ret) {
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            //console.log(obj);
            if(obj.allow) {
                $('#modal-delete-service-title').html(vocab['tooltip_delete_'+type]);
                $('#modal-target-id').val(id);
                $('#modal-delete-type').val(type);
                $('#modal-service-delete').modal('show');
            } else {
                showAlert(obj.message,'danger','');
            }
        }
    });

}
          