$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        setDemoVersion();
        $('#cmbModule').removeAttr('disabled');
        $("#title").append("<p class='text-danger'><b>Disabled in demo version !!!</b></p>");
    }

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
    $("#popType").chosen({          width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#ldaptype").chosen({         width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbModule").chosen({        width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#logHostType").chosen({      width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cbmDefCountry").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbFeatureCat").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbFieldTypeMod").chosen({  width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    
    var objFeatures = {
        updateConfig: function(idConfig,newVal){
            $.post(path + '/admin/features/updateConfig', {
                id : idConfig,
                newVal : newVal,
                _token : $('#_token').val()
            }, function(response) {

                if (response == false) {
                    $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                    $("#btn-modal-ok").attr("href", '');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }
            });
        },
        changePopPort: function(){
            var val = $('#popType').val(), popport = $('#popport');

            switch(val){
                case "GMAIL":
                    popport.val("993");
                    break;
                case "POP":
                    popport.val("110");
                    break;
                case "IMAP":
                    popport.val("143");
                    break;
                default:
                    popport.val("");
                    break;
            }
        },
        viewConfigs: function(){
            var idmodule = $('#cmbModule').val();

            if(idmodule == '1'){
                $('.mainConfigs').removeClass('hide');
                $('.moduleConfigs').addClass('hide');
                $('#addConf').addClass('hide');
            }else{

                $.ajax({
                    type: "POST",
                    url: path + '/admin/features/loadModuleConfs',
                    data: {
                        idmodule : idmodule,
                        _token : $('#_token').val()
                    },
                    error: function (ret) {
                        showAlert(makeSmartyLabel('Alert_get_data'),'danger','');
                    },
                    success: function(ret) {

                        if(ret){
                            $(".moduleConfigs").html(ret);
                            $('.moduleConfigs').removeClass('hide');
                            if(idmodule != '2' && idmodule != '3'){
                                $('#addConf').removeClass('hide');
                            }else{
                                $('#addConf').addClass('hide');
                            }


                            $('.mainConfigs').addClass('hide');

                            $('.i-checks').iCheck({
                                checkboxClass: 'icheckbox_square-green',
                                radioClass: 'iradio_square-green'
                            });

                            $('.changeConfigStatus').on('ifChecked ifUnchecked',function(e){
                                var idconfig = e.target.attributes.value.nodeValue;
                        
                                if(e.type == 'ifChecked'){
                                    objFeatures.updateConfig(idconfig,'1');
                                }else{
                                    objFeatures.updateConfig(idconfig,'0');
                                }
                            });
                        
                            $('.changeConfigValue').change(function(e){        
                                var idConfig = e.target.attributes.id.nodeValue, configVal = $("#"+idConfig).val();                                
                        
                                objFeatures.updateConfig(idConfig,configVal);
                            });

                            $('.removeConfig').click(function(){
                                console.log($(this).data('id'));
                                objFeatures.removeConfig($(this).data('id'));
                            });


                        }
                        else {
                            showAlert(makeSmartyLabel('Alert_get_data'),'danger','');
                        }
                    },
                    beforeSend: function(){
                        if(!$("#panelGrpsRepass").hasClass('hide')){
                            $("#panelGrpsRepass").addClass('animated fadeOutUp').addClass('hide');
                        }
                        if($("#loaderGrpsRepass").hasClass('hide')){
                            $("#loaderGrpsRepass").removeClass('hide');
                        }
                        $("#loaderGrpsRepass").html("<span style='color:#1ab394;'><i class='fa fa-spinner fa-spin fa-5x'></i></span>");
                    },
                    complete: function(){
                        $("#loaderGrpsRepass").addClass('hide');
                        if($("#panelGrpsRepass").hasClass('fadeOutUp')){
                            $("#panelGrpsRepass").removeClass('fadeOutUp');
                        }
                        $("#panelGrpsRepass").removeClass('hide').addClass('animated fadeInDown');

                        // Demo Version
                        if (demoVersion == 1){
                            setDemoVersion();
                        }

                        //
                    }
                });
            }
        },
        changeLogServer: function(){
            var hosttype = $('#logHostType').val();

            if(hosttype == 'remote'){
                $('#logServer').removeAttr('disabled');
            }else{
                $('#logServer').attr('disabled','disabled');
            }
        },
        removeConfig: function(idConfig){
            $.post(path + '/admin/features/removeConfig', {
                id : idConfig,
                _token : $('#_token').val()
            }, function(response) {

                if (response == false) {
                    $('#modal-notification').html(makeSmartyLabel('Permission_error'));
                    $("#btn-modal-ok").attr("href", '');
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                }else{
                    objFeatures.viewConfigs();
                }
            });
        }
    }

    $('#popType').change(function(){        
        objFeatures.changePopPort();
    });

    $('#cmbModule').change(function(e){
        objFeatures.viewConfigs();
    });

    $('#logHostType').change(function(){
        objFeatures.changeLogServer();
    });

    $('#cmbFieldTypeMod').change(function(){
        if($(this).val() == 'checkbox'){
            $("#checkVal").removeClass('hide');
            $("#inputVal").addClass('hide');
        }else{
            $("#inputVal").removeClass('hide');
            $("#checkVal").addClass('hide');
        }
    });
    

    // Buttons
    $("#btnSaveEmailConfig").click(function(){
        var mailHeader = $('#emailHeader').summernote('code'),
            mailFooter = $('#emailFooter').summernote('code');

        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveEmailChanges',
            data: $('#formEmailConfig').serialize()+ '&_token=' + $('#_token').val() + '&mailHeader=' + mailHeader + '&mailFooter=' + mailFooter, 
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success','');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                }
            },
            beforeSend: function(){
                $("#btnSaveEmailConfig").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveEmailConfig").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });
    });

    $("#btnSavePopServer").click(function(){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/savePopChanges',
            data:$('#formPopServer').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success','');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                }
            },
            beforeSend: function(){
                $("#btnSavePopServer").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSavePopServer").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });

    });

    $("#btnSaveLDAP").click(function(){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveLdapChanges',
            data:$('#formLDAP').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success','');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                }
            },
            beforeSend: function(){
                $("#btnSaveLDAP").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveLDAP").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });

    });

    $("#btnSaveMaintenance").click(function(){
        var msg = $('#maintenanceMsg').summernote('code');

        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveMaintenance',
            data:$('#formMaintenance').serialize() + '&_token=' + $('#_token').val() + '&maintenanceMsg=' + msg,
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success','');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                }
            },
            beforeSend: function(){
                $("#btnSaveMaintenance").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveMaintenance").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });

    });

    $("#btnSaveLog").click(function(){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveLogChange',
            data:$('#formLog').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success','');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                }
            },
            beforeSend: function(){
                $("#btnSaveLog").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveLog").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });

    });

    $("#btnSaveMisc").click(function(){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveMiscChange',
            data:$('#formMisc').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger','');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success','');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger','');
                }
            },
            beforeSend: function(){
                $("#btnSaveMisc").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveMisc").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });

    });

    $("#btnAddNewConf").click(function(){
        var moduleID = $("#cmbModule").val(), moduleName = $('#cmbModule').find(":selected").text(),
            _token = $("#_token").val();
        
        
        $.post(path + '/admin/features/ajaxFeatureCategory', {
           moduleID: moduleID
        }, function(response) {

            if (response == false) {
                showAlert(makeSmartyLabel('Alert_get_data'),'danger','');
            }else{
                $("#idmoduleAddFeat").val(moduleID);
                $("#moduleName").html(moduleName);
                $("#cmbFeatureCat").html(response);
                $("#cmbFeatureCat").trigger("chosen:updated");
                $("#modal-add-feature").modal('show');
            }
        });

    });

    $("#btnSaveNewFeat").click(function(){

        if (!$("#feature-add-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveNewFeature',
            dataType: 'json',
            data: $('#feature-add-form').serialize() + "&_token=" + $('#_token').val(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-add-feature');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idfeature)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-add-feature');

                    setTimeout(function(){
                        $('#modal-add-feature').modal('hide');
                        $('#featureDefault').iCheck('unCheck');
                        $('#feature-add-form').trigger('reset');
                        objFeatures.viewConfigs();
                    },2000);

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-add-feature');
                }
            },
            beforeSend: function(){
                $("#btnSaveNewFeat").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnSaveNewFeat").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
        });
    });

    $("#btnAddConfCateg").click(function(){
        var moduleID = $("#cmbModule").val(), moduleName = $('#cmbModule').find(":selected").text();

        $("#idmoduleAddCateg").val(moduleID);
        $("#moduleNameCateg").html(moduleName);
        $("#modal-add-category").modal('show');

    });

    $("#btnSaveNewCateg").click(function(){

        if (!$("#categ-feat-add-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveNewCategory',
            dataType: 'json',
            data: $('#categ-feat-add-form').serialize() + "&_token=" + $('#_token').val(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-add-category');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idcategory)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-add-category');

                    setTimeout(function(){
                        $('#modal-add-category').modal('hide');
                        $('#categorySetup').iCheck('unCheck');
                        $('#categ-feat-add-form').trigger('reset');
                    },2000);

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-add-category');
                }
            },
            beforeSend: function(){
                $("#btnSaveNewCateg").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCloseNewCateg").addClass('disabled');
            },
            complete: function(){
                $("#btnSaveNewCateg").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCloseNewCateg").removeClass('disabled');
            }
        });
    });


    //Summernote Editor
    $('#emailHeader').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote

        }
    );

    $('#emailFooter').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote

        }
    );

    $('#maintenanceMsg').summernote(
        {
            toolbar:[
                ["help",[]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote

        }
    );

    /*
     * Validate
     */
    $("#feature-add-form").validate({
        ignore:[],
        rules: {
            txtNewFeature:{
                required:true,
                remote:{
                    url: path+"/admin/features/checkField",
                    type: 'post',
                    data: {
                        searchval:function(){return $('#txtNewFeature').val();},
                        moduleId:function(){return $('#idmoduleAddFeat').val();},
                        fieldName:'name'
                    }
                }
            },
            newFeatureSessionName: {
                required:true,
                remote:{
                    url: path+"/admin/features/checkField",
                    type: 'post',
                    data: {
                        searchval:function(){return $('#newFeatureSessionName').val();},
                        moduleId:function(){return $('#idmoduleAddFeat').val();},
                        fieldName:'session_name'
                    }
                }
            },
            newFeatureSmartyVar:"required",
            cmbFieldTypeMod:"required",
            valInputFeature:{required: function(e){return $('#cmbFieldTypeMod').val() == 'input';}}
        },
        messages: {
            txtNewFeature: {required:makeSmartyLabel('Alert_field_required')},
            newFeatureSessionName: {required:makeSmartyLabel('Alert_field_required')},
            newFeatureSmartyVar: makeSmartyLabel('Alert_field_required'),
            cmbFieldTypeMod: makeSmartyLabel('Alert_field_required'),
            valInputFeature: {required:makeSmartyLabel('Alert_field_required')}

        }
    });

    $("#categ-feat-add-form").validate({
        ignore:[],
        rules: {
            txtNewCategory:{
                required:true,
                remote:{
                    url: path+"/admin/features/checkCategory",
                    type: 'post',
                    data: {
                        moduleId:function(){return $('#idmoduleAddCateg').val();}
                    }
                }
            },
            newCategorySmartyVar:"required"
        },
        messages: {
            txtNewCategory: {required: makeSmartyLabel('Alert_field_required')},
            newCategorySmartyVar: makeSmartyLabel('Alert_field_required')

        }
    });

    /* clean modal's fields */
    $('#modal-add-feature').on('hidden.bs.modal', function() { 
        $('#featureDefault').iCheck('unCheck');
        $('#feature-add-form').trigger('reset');
        
    });

    $('#modal-add-category').on('hidden.bs.modal', function() {
        $('#modal-add-category').modal('hide');
        $('#categorySetup').iCheck('unCheck');
        $('#categ-feat-add-form').trigger('reset');
    });


});

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

function setDemoVersion() {
    //
    $(':button').prop('disabled', true);   // Disable all the buttons
    $(':input').prop('disabled', true);    // Disable all the inputs
    $(':checkbox').prop('disabled', true); // Disable all the checkbox
    //

}


