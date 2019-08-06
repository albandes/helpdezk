var objServices = {
    changeAreaStatus: function(idArea,newStatus){
        $.post(path + '/helpdezk/hdkService/areaChangeStatus', {
            id : idArea,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {

            if (response == false) {
                $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkService/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }
        });
    },
    changeTypeStatus: function(idType,newStatus){
        $.post(path + '/helpdezk/hdkService/typeChangeStatus', {
            id : idType,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {

            if (response == false) {
                $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkService/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }
        });
    },
    changeItemStatus: function(idItem,newStatus){
        $.post(path + '/helpdezk/hdkService/itemChangeStatus', {
            id : idItem,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {

            if (response == false) {
                $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkService/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }
        });
    },
    changeServiceStatus: function(idService,newStatus){
        $.post(path + '/helpdezk/hdkService/serviceChangeStatus', {
            id : idService,
            newStatus : newStatus,
            _token : $('#_token').val()
        }, function(response) {

            if (response == false) {
                $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkService/index');
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
            }
        });
    },
    changeItem: function(){
        var typeId = $("#confCmbType").val();
        $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeId: typeId},
            function(valor){
                $("#confCmbItem").html(valor);
                $("#confCmbItem").trigger("chosen:updated");
                return objServices.changeService();
            });
    },
    changeService: function(){
        var itemId = $("#confCmbItem").val();
        console.log(itemId);
        $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemId: itemId},
            function(valor){
                $("#confCmbService").html(valor);
                $("#confCmbService").trigger("chosen:updated");
                return objServices.changeUsers();
            });
    },
    changeUsers: function(){
        var itemId = $("#confCmbItem").val(), serviceId = $("#confCmbService").val();

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/getUsersApprove",
            data:{iditem:itemId,idservice:serviceId},
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {

                    $("#confCmbUser").html(obj.confCmbUsers);
                    $("#confCmbUser").trigger("chosen:updated");

                    $("#userslist").html(obj.usersApprvlist);

                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green'
                    });

                    if(obj.flgRecalc == '1'){
                        $('#checkRecalculate').iCheck('check');
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
                        var row = $(this).parents("tr"), targetId = $(this).parents("tr").find('.apprUser').val();
                        $("#confCmbUser option[value='"+targetId+"']").removeAttr("disabled");
                        $("#confCmbUser").val("");
                        $("#confCmbUser").trigger("chosen:updated");
                        row.remove();
                    });

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });
    },
    insertUserAppr: function(){
        var tBody = $("#userslist tr") , userID = $('#confCmbUser option:selected').val(),
            userName = $('#confCmbUser option:selected').text(), tBodyComp = '', upIcon='';

        $('#confCmbUser option:selected').attr("disabled","disabled");
        $("#confCmbUser").trigger("chosen:updated");

        tBodyComp = "<tr><td>"+userName+"<input type='hidden' class='apprUser' name='apprUser[]' id='apprUser_"+userID+"' value='"+userID+"'></td><td><a href='#' class='btn btn-success btn-up'><i class='fa fa-sort-up'></i></a></td><td><a href='#' class='btn btn-primary btn-down'><i class='fa fa-sort-down'></i></a></td><td><a href='#' class='btn btn-danger btn-remove'><i class='fa fa-times'></i></a> </td></tr>";

        $("#userslist").append(tBodyComp);

        $(".btn-up,.btn-down").click(function(){
            var row = $(this).parents("tr:first");
            if ($(this).is(".btn-up")) {
                row.insertBefore(row.prev());
            } else {
                row.insertAfter(row.next());
            }
        });

        $(".btn-remove").click(function(){
            var row = $(this).parents("tr"), targetId = $(this).parents("tr").find('.apprUser').val();
            $("#confCmbUser option[value='"+targetId+"']").removeAttr("disabled");
            $("#confCmbUser").val("");
            $("#confCmbUser").trigger("chosen:updated");
            row.remove();
        });
    }
}

$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    // tooltips
    $('.tooltip-buttons').tooltip();

    /*
     *  Chosen
     */
    $("#modal_cmbArea").chosen({        width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#modal_cmbGroup").chosen({       width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#modal_cmbPriority").chosen({    width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#confCmbType").chosen({          width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#confCmbItem").chosen({          width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#confCmbService").chosen({       width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#confCmbUser").chosen({          width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});

    $('.checkArea').on('ifChecked ifUnchecked',function(e){
        var idarea = e.target.attributes.value.nodeValue;

        if(e.type == 'ifChecked'){
            objServices.changeAreaStatus(idarea,'A');
        }else{
            objServices.changeAreaStatus(idarea,'N');
        }
    });

    $('.checkType').on('ifChecked ifUnchecked',function(e){
        var idtype = e.target.attributes.value.nodeValue;

        if(e.type == 'ifChecked'){
            objServices.changeTypeStatus(idtype,'A');
        }else{
            objServices.changeTypeStatus(idtype,'N');
        }
    });

    $('#confCmbType').change(function(){
        objServices.changeItem();
    });

    $('#confCmbItem').change(function(){
        objServices.changeService();
    });

    $('#confCmbService').change(function(){
        objServices.changeUsers();
    });

    $('#confCmbUser').change(function(){
        objServices.insertUserAppr();
    });

    // Buttons
    $("#btnNewArea").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/modalArea",
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    if(obj.tablelist){
                        $("#areaslist").html(obj.tablelist);
                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });

                        $('.tooltip-buttons').tooltip();

                        $('.checkAreaModal').on('ifChecked ifUnchecked',function(e){
                            var idarea = e.target.attributes.value.nodeValue, node = "#area_"+idarea;
                    
                            if(e.type == 'ifChecked'){
                                objServices.changeAreaStatus(idarea,'A');
                                $("#area_"+idarea).iCheck('check');
                            }else{
                                objServices.changeAreaStatus(idarea,'N');
                                $("#area_"+idarea).iCheck('unCheck');
                            }
                        });
                    }

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });
        $('#modal-form-area').modal('show');
        $('#areaModalTitle').html(makeSmartyLabel('Area_insert'));
    });

    $("#btnNewType").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/modalType",
            data:{viewType:'add'},
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $('#idtype_modal').val('');
                    $("#modal_cmbArea").html(obj.cmbArea);
                    $("#modal_cmbArea").trigger("chosen:updated");

                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green'
                    });

                    $('#modal_type_name').val(obj.typeData.name);

                    if(obj.typeData.status == 'A'){
                        $('#checkAvailable').iCheck('check');
                    }

                    $('#modal-form-type').modal('show');
                    $('#typeModalTitle').html(makeSmartyLabel('Type_insert'));

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });

    });

    $("#btnConfigApprv").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/modalConfApprove",
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $("#confCmbType").html(obj.confCmbType);
                    $("#confCmbType").trigger("chosen:updated");

                    $('#modal-form-conf-apprv').modal('show');

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });

    });

    $("#btnNewItem").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/modalItem",
            data:{viewItem:'add'},
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $('#iditem_modal').val('');
                    $('#idtype_item').val($('#idtypeHide').val());

                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green'
                    });

                    $('#modal_item_name').val(obj.name);

                    if(obj.status == 'A'){
                        $('#checkItemAvailable').iCheck('check');
                    }

                    if(obj.selec == '1'){
                        $('#checkItemDefault').iCheck('check');
                    }

                    if(obj.classify == '1'){
                        $('#checkItemClassification').iCheck('check');
                    }

                    $('#modal-form-item').modal('show');
                    $('#itemModalTitle').html(makeSmartyLabel('Item_insert'));

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });

    });

    $("#btnNewService").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/modalService",
            data:{viewService:'add'},
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $('#idservice_modal').val('');
                    $('#iditem_service').val($('#iditemHide').val());

                    $("#modal_cmbGroup").html(obj.cmbGroup);
                    $("#modal_cmbGroup").trigger("chosen:updated");

                    $("#modal_cmbPriority").html(obj.cmbPriority);
                    $("#modal_cmbPriority").trigger("chosen:updated");

                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green'
                    });

                    $('#modal_service_name').val(obj.serviceData.name);

                    if(obj.serviceData.status == 'A'){
                        $('#checkServAvailable').iCheck('check');
                    }

                    if(obj.serviceData.selec == '1'){
                        $('#checkServDefault').iCheck('check');
                    }

                    if(obj.serviceData.classify == '1'){
                        $('#checkServClassification').iCheck('check');
                    }

                    $('#modal-form-service').modal('show');
                    $('#serviceModalTitle').html(makeSmartyLabel('Service_insert'));

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });

    });

    $("#btnSaveArea").click(function(){
        if (!$("#area_form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/createArea",
            data: $('#area_form').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-area');
            },
            success: function(ret) {
                if(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.status == 'OK') {
                        $("#areaslist").html('');
                        if(obj.arealist.tablelist){
                            $("#areaslist").html(obj.arealist.tablelist);
                            $('.i-checks').iCheck({
                                checkboxClass: 'icheckbox_square-green',
                                radioClass: 'iradio_square-green'
                            });
                        }
                        $("#tab-services").html(obj.tabservices);
                        $("#area_form").trigger('reset');
                        modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-create-area');
                        $('.i-checks').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-area');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-area');
                }
            },
            beforeSend: function(){
                $("#btnSaveArea").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSaveArea").removeAttr('disabled');
            }
        });
        return false;  // <- cancel event
    });

    $("#btnSaveAreaUpd").click(function(){
        if (!$("#area_update_form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/updateArea",
            data: $('#area_update_form').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-area');
            },
            success: function(ret) {
                if(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.status == 'OK') {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_success_update'),'alert-update-area');
                        setTimeout(function(){
                            $('#modal-form-area-update').modal('hide');
                            $("#updDefaultArea").iCheck('unCheck');
                            $('#area_update_form').trigger('reset');
                            location.href = path + "/helpdezk/hdkService/index" ;
                        },2000);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-area');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-area');
                }
            },
            beforeSend: function(){
                $("#btnSaveAreaUpd").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSaveAreaUpd").removeAttr('disabled');
            }
        });
        return false;  // <- cancel event
    });

    $("#btnSaveType").click(function(){
        if (!$("#type_form").valid()) {
            return false ;
        }
        var url = "", msg = "";
        console.log($("#idtype_modal").val());

        if($("#idtype_modal").val() == ''){
            url = path + "/helpdezk/hdkService/createType";
            msg = makeSmartyLabel('Alert_inserted');
        }else{
            url = path + "/helpdezk/hdkService/updateType";
            msg = makeSmartyLabel('Alert_success_update');
        }

        $.ajax({
            type: "POST",
            url: url,
            data: $('#type_form').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-type');
            },
            success: function(ret) {
                if(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    
                    if(obj.status == 'OK') {
                        modalAlertMultiple('success',msg,'alert-modal-type');
                        setTimeout(function(){
                            $('#modal-form-area-update').modal('hide');
                            $("#updDefaultArea").iCheck('unCheck');
                            $('#type_form').trigger('reset');
                            location.href = path + "/helpdezk/hdkService/index" ;
                        },2000);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-type');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-type');
                }
            },
            beforeSend: function(){
                $("#btnSaveType").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSaveType").removeAttr('disabled');
            }
        });
        return false;  // <- cancel event
    });

    $("#btnSaveItem").click(function(){
        if (!$("#item_form").valid()) {
            return false ;
        }
        var url = "", msg = "";
        console.log($("#idtype_modal").val());

        if($("#iditem_modal").val() == ''){
            url = path + "/helpdezk/hdkService/createItem";
            msg = makeSmartyLabel('Alert_inserted');
        }else{
            url = path + "/helpdezk/hdkService/updateItem";
            msg = makeSmartyLabel('Alert_success_update');
        }

        $.ajax({
            type: "POST",
            url: url,
            data: $('#item_form').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-item');
            },
            success: function(ret) {
                if(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK') {
                        modalAlertMultiple('success',msg,'alert-modal-item');
                        viewType($("#idtypeHide").val())
                        setTimeout(function(){
                            $('#modal-form-item').modal('hide');
                            $("#checkItemAvailable").iCheck('unCheck');
                            $("#checkItemDefault").iCheck('unCheck');
                            $("#checkItemClassification").iCheck('unCheck');
                            $('#item_form').trigger('reset');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-item');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-item');
                }
            },
            beforeSend: function(){
                $("#btnSaveItem").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSaveItem").removeAttr('disabled');
            }
        });
        return false;  // <- cancel event
    });

    $("#btnSaveService").click(function(){
        if (!$("#service_form").valid()) {
            return false ;
        }
        var url = "", msg = "";
        console.log($("#idtype_modal").val());

        if($("#idservice_modal").val() == ''){
            url = path + "/helpdezk/hdkService/createService";
            msg = makeSmartyLabel('Alert_inserted');
        }else{
            url = path + "/helpdezk/hdkService/updateService";
            msg = makeSmartyLabel('Alert_success_update');
        }

        $.ajax({
            type: "POST",
            url: url,
            data: $('#service_form').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-service');
            },
            success: function(ret) {
                if(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK') {
                        modalAlertMultiple('success',msg,'alert-modal-service');
                        viewItem($("#iditemHide").val())
                        setTimeout(function(){
                            $('#modal-form-service').modal('hide');
                            $("#hours").iCheck('check');
                            $("#checkServAvailable").iCheck('unCheck');
                            $("#checkServDefault").iCheck('unCheck');
                            $("#checkServClassification").iCheck('unCheck');
                            $('#service_form').trigger('reset');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-service');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-service');
                }
            },
            beforeSend: function(){
                $("#btnSaveService").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSaveService").removeAttr('disabled');
            }
        });
        return false;  // <- cancel event
    });

    $("#btnSaveConfApprv").click(function(){
        if (!$("#conf_apprv_form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkService/saveConfApproval",
            data: $('#conf_apprv_form').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-confApprv');
            },
            success: function(ret) {
                if(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK') {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-modal-confApprv');
                        setTimeout(function(){
                            $('#modal-form-conf-apprv').modal('hide');
                            $("#checkRecalculate").iCheck('unCheck');
                            $('#conf_apprv_form').trigger('reset');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-confApprv');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-modal-confApprv');
                }
            },
            beforeSend: function(){
                $("#btnSaveConfApprv").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSaveConfApprv").removeAttr('disabled');
            }
        });
        return false;  // <- cancel event
    });

    //form validations
    $("#area_form").validate({
        ignore:[],
        rules: {
            modal_area_name: "required"
        },
        messages: {
            modal_area_name: makeSmartyLabel('Alert_field_required')
        }

    });

    $("#area_update_form").validate({
        ignore:[],
        rules: {
            area_name_upd: "required"
        },
        messages: {
            area_name_upd: makeSmartyLabel('Alert_field_required')
        }

    });

    $("#type_form").validate({
        ignore:[],
        rules: {
            modal_type_name: "required",
            modal_cmbArea: "required"
        },
        messages: {
            modal_type_name: makeSmartyLabel('Alert_field_required'),
            modal_cmbArea: makeSmartyLabel('Alert_field_required')
        }

    });

    $("#item_form").validate({
        ignore:[],
        rules: {
            modal_item_name: "required"
        },
        messages: {
            modal_item_name: makeSmartyLabel('Alert_field_required')
        }

    });

    $("#service_form").validate({
        ignore:[],
        rules: {
            modal_service_name: "required",
            modal_cmbGroup: "required",
            modal_cmbPriority: "required"
        },
        messages: {
            modal_service_name: makeSmartyLabel('Alert_field_required'),
            modal_cmbGroup: makeSmartyLabel('Alert_field_required'),
            modal_cmbPriority: makeSmartyLabel('Alert_field_required')
        }

    });

    $("#conf_apprv_form").validate({
        ignore:[],
        rules: {
            confCmbType: "required",
            confCmbItem: "required",
            confCmbService: "required"
        },
        messages: {
            confCmbType: makeSmartyLabel('Alert_field_required'),
            confCmbItem: makeSmartyLabel('Alert_field_required'),
            confCmbService: makeSmartyLabel('Alert_field_required')
        }

    });

    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() { 
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });

        $('#checkDefaultArea').iCheck('unCheck');
        $('#updDefaultArea').iCheck('unCheck');

        $('#checkAvailable').iCheck('unCheck');
        $('#checkDefault').iCheck('unCheck');
        $('#checkClassification').iCheck('unCheck');

        $('#checkItemAvailable').iCheck('unCheck');
        $('#checkItemDefault').iCheck('unCheck');
        $('#checkItemClassification').iCheck('unCheck');

        $('#checkServAvailable').iCheck('unCheck');
        $('#checkServDefault').iCheck('unCheck');
        $('#checkServClassification').iCheck('unCheck');

        $('#area_form').trigger('reset');
        $('#area_update_form').trigger('reset');
        $('#type_form').trigger('reset');
        $('#item_form').trigger('reset');
        $('#service_form').trigger('reset');
        $('#conf_apprv_form').trigger('reset');

        $('.checkArea').on('ifChecked ifUnchecked',function(e){
            var idarea = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeAreaStatus(idarea,'A');
            }else{
                objServices.changeAreaStatus(idarea,'N');
            }
        });

        $('.checkType').on('ifChecked ifUnchecked',function(e){
            var idtype = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeTypeStatus(idtype,'A');
            }else{
                objServices.changeTypeStatus(idtype,'N');
            }
        });

        $('.checkItem').on('ifChecked ifUnchecked',function(e){
            var idtype = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeItemStatus(idtype,'A');
            }else{
                objServices.changeItemStatus(idtype,'N');
            }
        });

        $('.checkService').on('ifChecked ifUnchecked',function(e){
            var idtype = e.target.attributes.value.nodeValue;

            if(e.type == 'ifChecked'){
                objServices.changeServiceStatus(idtype,'A');
            }else{
                objServices.changeServiceStatus(idtype,'N');
            }
        });
    });


});

function editArea(id) {
    $('#modal-form-area').modal('hide');

    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalUpdateArea",
        data:{idarea:id},
        dataType: 'json',
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            //console.log(obj);
            if(obj) {

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                $('#area_name_upd').val(obj.name);
                if(obj.default == 1){$('#updDefaultArea').iCheck('check');}


            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            }
        }
    });

    $('#modal-form-area-update').modal('show');
    $('#areaUpdTitle').html(makeSmartyLabel('Area_edit'));
    $('#idarea_upd').val(id);

}

function editType(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalType",
        data:{viewType:'upd',idtype:id},
        dataType: 'json',
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            //console.log(obj);
            if(obj) {
                $('#idtype_modal').val(id);
                
                $("#modal_cmbArea").html(obj.cmbArea);
                $("#modal_cmbArea").val(obj.defaultArea);
                $("#modal_cmbArea").trigger("chosen:updated");

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                $('#modal_type_name').val(obj.typeData.name);

                if(obj.typeData.status == 'A'){
                    $('#checkAvailable').iCheck('check');
                }

                if(obj.typeData.selec == '1'){
                    $('#checkDefault').iCheck('check');
                }

                if(obj.typeData.classify == '1'){
                    $('#checkClassification').iCheck('check');
                }

                $('#modal-form-type').modal('show');
                $('#typeModalTitle').html(makeSmartyLabel('Type_edit'));

            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            }
        }
    });
    
}

function viewType(id) {
    
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkService/itemList',
        dataType: 'json',
        data: {id : id},
        error: function (ret) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Permission_error'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        },
        success: function(ret){
            if(ret){
                $('#panelTypes').removeClass('hide').addClass('animated fadeInDown');
                $('#panelItens').addClass('hide');
                $('#idtypeHide').val(id);
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                $('#typeTitle').html(obj.title);
                if(obj.title.length > 0  ) {
                    $('#tab-type-itens').html(obj.tabList);
                }
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });
                // tooltips
                $('.tooltip-buttons').tooltip();

                $('.checkArea').on('ifChecked ifUnchecked',function(e){
                    var idarea = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeAreaStatus(idarea,'A');
                    }else{
                        objServices.changeAreaStatus(idarea,'N');
                    }
                });

                $('.checkType').on('ifChecked ifUnchecked',function(e){
                    var idtype = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeTypeStatus(idtype,'A');
                    }else{
                        objServices.changeTypeStatus(idtype,'N');
                    }
                });

                $('.checkItem').on('ifChecked ifUnchecked',function(e){
                    var idtype = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeItemStatus(idtype,'A');
                    }else{
                        objServices.changeItemStatus(idtype,'N');
                    }
                });
            }else{
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                $("#tipo-alert").attr('class', 'warning alert-warning');
                $('#modal-alert').modal('show');
            }

        }
    });
}

function editItem(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalItem",
        data:{viewItem:'upd',iditem:id},
        dataType: 'json',
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret));
            //console.log(obj);
            if(obj) {
                $('#iditem_modal').val(id);
                $('#idtype_item').val($('#idtypeHide').val());

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                $('#modal_item_name').val(obj.name);

                if(obj.status == 'A'){
                    $('#checkItemAvailable').iCheck('check');
                }

                if(obj.selec == '1'){
                    $('#checkItemDefault').iCheck('check');
                }

                if(obj.classify == '1'){
                    $('#checkItemClassification').iCheck('check');
                }

                $('#modal-form-item').modal('show');
                $('#itemModalTitle').html(makeSmartyLabel('Item_edit'));

            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            }
        }
    });

}

function viewItem(id) {

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkService/serviceList',
        dataType: 'json',
        data: {id : id},
        error: function (ret) {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(aLang['Permission_error'].replace (/\"/g, ""));
            $("#tipo-alert").attr('class', 'warning alert-warning');
            $('#modal-alert').modal('show');
        },
        success: function(ret){
            if(ret){
                $('#panelItens').removeClass('hide').addClass('animated fadeInDown');
                $('#iditemHide').val(id);
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                $('#itemTitle').html(obj.title);
                if(obj.title.length > 0  ) {
                    $('#tab-itens-services').html(obj.tabList);
                }
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });
                // tooltips
                $('.tooltip-buttons').tooltip();

                $('.checkArea').on('ifChecked ifUnchecked',function(e){
                    var idarea = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeAreaStatus(idarea,'A');
                    }else{
                        objServices.changeAreaStatus(idarea,'N');
                    }
                });

                $('.checkType').on('ifChecked ifUnchecked',function(e){
                    var idtype = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeTypeStatus(idtype,'A');
                    }else{
                        objServices.changeTypeStatus(idtype,'N');
                    }
                });

                $('.checkItem').on('ifChecked ifUnchecked',function(e){
                    var idtype = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeItemStatus(idtype,'A');
                    }else{
                        objServices.changeItemStatus(idtype,'N');
                    }
                });

                $('.checkService').on('ifChecked ifUnchecked',function(e){
                    var idtype = e.target.attributes.value.nodeValue;

                    if(e.type == 'ifChecked'){
                        objServices.changeServiceStatus(idtype,'A');
                    }else{
                        objServices.changeServiceStatus(idtype,'N');
                    }
                });

            }else{
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                $("#tipo-alert").attr('class', 'warning alert-warning');
                $('#modal-alert').modal('show');
            }

        }
    });
}

function editService(id) {
    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkService/modalService",
        data:{viewService:'upd',idservice:id},
        dataType: 'json',
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
        },
        success: function(ret) {

            var obj = jQuery.parseJSON(JSON.stringify(ret)), ind_hm = '';
            //console.log(obj);
            if(obj) {
                $('#idservice_modal').val(id);
                $('#iditem_service').val($('#iditemHide').val());

                $("#modal_cmbGroup").html(obj.cmbGroup);
                $("#modal_cmbGroup").val(obj.defaultGroup);
                $("#modal_cmbGroup").trigger("chosen:updated");

                $("#modal_cmbPriority").html(obj.cmbPriority);
                $("#modal_cmbPriority").val(obj.defaultPriority);
                $("#modal_cmbPriority").trigger("chosen:updated");

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

                $('#modal_service_name').val(obj.serviceData.name);

                $('#limit_days').val(obj.serviceData.days);
                $('#limit_time').val(obj.serviceData.limit_time);

                ind_hm = obj.serviceData.ind_type_time == 'H' ? '#hours' : '#minutes';
                $(ind_hm).iCheck('check');

                if(obj.serviceData.status == 'A'){
                    $('#checkServAvailable').iCheck('check');
                }

                if(obj.serviceData.selec == '1'){
                    $('#checkServDefault').iCheck('check');
                }

                if(obj.serviceData.classify == '1'){
                    $('#checkServClassification').iCheck('check');
                }

                $('#modal-form-service').modal('show');
                $('#serviceModalTitle').html(makeSmartyLabel('Service_edit'));

            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            }
        }
    });

}