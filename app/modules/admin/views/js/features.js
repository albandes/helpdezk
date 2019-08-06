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
    $("#popType").chosen({        width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#ldaptype").chosen({       width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbModule").chosen({       width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#logHostType").chosen({       width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cbmDefCountry").chosen({       width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    
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
                                idConfig = idConfig.substring(2);
                        
                                objFeatures.updateConfig(idConfig,configVal);
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


});

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

