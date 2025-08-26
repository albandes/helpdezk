Dropzone.autoDiscover = false;

$(document).ready(function () {

    /*
    * Demo version
    */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $("#btnSaveChangeUserPass").prop('disabled', true);
        }
        if ($('#idperson').val() == 1) {
            $("#btnSaveChangeRootPass").prop('disabled', true);
        }

    }
    
    /*
     * Select2
     */
    $("#modal-cmbcolor-theme").select2({width:'100%',placeholder:vocab['Select'],allowClear:true,dropdownParent: $(this).find('.modal-body-user-settings')});
    $("#modal-cmblocale").select2({width:'100%',placeholder:vocab['Select'],allowClear:true,dropdownParent: $(this).find('.modal-body-user-settings')});
    $("#person_country").select2({placeholder:vocab['Select'],allowClear:true});
    $("#person_state").select2({placeholder:vocab['Select'],allowClear:true});
    $("#person_city").select2({placeholder:vocab['Select'],allowClear:true});
    $("#person_neighborhood").select2({placeholder:vocab['Select'],allowClear:true});
    $("#person_typestreet").select2({placeholder:vocab['Select'],allowClear:true});
    $('#modal-update-user-gender').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-update-user-profile-form')});
    $('#modal-update-user-address-type').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-update-user-profile-form')});

    /*
     * iCheck - checkboxes/radios styling
     */
    $('#modal-display-grid').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    // Mask
    /*$('#person_dtbirth').mask('00/00/0000');
    $('#person_number').mask('0000');
    $('#person_phone').mask(phone_mask);
    $('#person_cellphone').mask(cellphone_mask);
    $('#person_zipcode').mask(zip_mask);
    $('#person_ssn_cpf').mask(id_mask);

    // https://xdsoft.net/jqplugins/autocomplete/
    $("#person_address").autocomplete({
        source:[{
            url: path+"/helpdezk/home/completeStreet/search/%QUERY%",
            type: 'remote'
        }
        ],
        accents: true,
        replaceAccentsForRemote: false,
        minLength: 1
    });*/

    // Buttons
    $("#btnEditUserProfile").click(function(){
        
        $('#modal-update-user-profile').modal('show');
        
    });

    $("#btnEditUserSettings").click(function(){
        $('#modal-user-settings').modal('show');
    });

    $("#btnUpdatePhoto").click(function(){
        $('#modal-person-photo').modal('show');
        //countdown.start(timesession);
    });

    $("#btnEditUserPass").click(function(){
        if(!$("#btnEditUserPass").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url:  path + '/main/home/getLoginType',
                dataType: 'json',
                data: {
                    userId: $('#nav-user-id').val()
                },
                error: function (ret) {
                    showAlert(vocab['generic_error_msg'],'danger');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        if(obj.loginTypeId != 3) {
                            showAlert(vocab['not_allow_password_change'],'danger');
                        }else{
                            $('#modal-change-user-password').modal('show');
                        }
                    } else {
                        showAlert(obj.message,'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnEditUserPass").addClass('disabled');
                },
                complete: function(){
                    $("#btnEditUserPass").removeClass('disabled');
                }
            });
        }
    });

    $(".btnEditRootPass").click(function(){
        $('#modal-change-root-password').modal('show');
    });

    // Save User Settings
    $("#btnUserSetSave").click(function(){
        /*if (!$("#modal-config-external-form").valid()) {
            return false;
        }*/

        $.ajax({
            type: "POST",
            url: path + '/main/home/saveUserSettings',
            dataType: 'json',
            data: $("#modal-usersettings-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',vocab['Alert_filure'],'alert-modal-usersettings');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.success) {
                    modalAlertMultiple('success',vocab['Alert_eternal_settings_OK'],'alert-modal-usersettings');
                    setTimeout(function(){
                        $('#modal-config-external-form').modal('hide');
                        location.href = "" ;
                    },2000);

                } else {
                    modalAlertMultiple('danger',vocab['Alert_filure']+': '+obj.message,'alert-modal-usersettings');
                }

            }
        });
    });

    // End Buttons

    // Combos
    var formPersonData = $(document.getElementById("persondata_form"));
    var objPersonData = {
        changeState: function() {
            var countryId = $("#person_country").val();
            $.post(path+"/helpdezk/home/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#person_state").html(valor);
                    /*
                     If you need to update the options in your select field and want Chosen to pick up the changes,
                     you'll need to trigger the "chosen:updated" event on the field. Chosen will re-build itself based on the updated content.
                     */
                    $("#person_state").trigger("chosen:updated");
                    return objPersonData.changeCity();
                })
        },
        changeCity: function() {
            var stateId = $("#person_state").val();
            $.post(path+"/helpdezk/home/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#person_city").html(valor);
                    $("#person_city").trigger("chosen:updated");
                    return objPersonData.changeNeighborhood();
                });
        },
        changeNeighborhood: function() {
            var stateId = $("#person_city").val();
            $.post(path+"/helpdezk/home/ajaxNeighborhood",{stateId: stateId},
                function(valor){
                    $("#person_neighborhood").html(valor);
                    /*
                     If you need to update the options in your select field and want Chosen to pick up the changes,
                     you'll need to trigger the "chosen:updated" event on the field. Chosen will re-build itself based on the updated content.
                     */
                    $("#person_neighborhood").trigger("chosen:updated");
                    return false;
                })
            return false ;
        }

    }

    /*
     * Combos
     */
    var objUserSettings = {
        loadCmbThemes: function() {
            $.post(path+"/main/home/ajaxComboThemes",function(valor){
                $("#modal-cmbcolor-theme").html(valor);
                $("#modal-cmbcolor-theme").trigger("change");
                return false;
            });
            return false ;
        },
        loadCmbLocales: function() {
            $.post(path+"/main/home/ajaxComboLocales",function(valor){
                $("#modal-cmblocale").html(valor);
                $("#modal-cmblocale").trigger("change");
                return false;
            });
            return false ;
        },
        loadUserSettings: function() {
            $.post(path+"/main/home/loadUserSettins",function(valor){
                /*$("#modal-cmbcolor-theme").html(valor);
                $("#modal-cmbcolor-theme").trigger("change");*/
                return false;
            });
            return false ;
        }
    };

    objUserSettings.loadCmbThemes();
    objUserSettings.loadCmbLocales();
    
    $("#person_country").change(function(){
        objPersonData.changeState();
    });

    $("#person_state").change(function(){
        objPersonData.changeCity();
    });

    $("#person_city").change(function(){
        objPersonData.changeNeighborhood();
    });


    $("#modal-config-external-form").validate({
        ignore:[],
        rules: {
            trello_key: "required",
            trello_token: "required"
        },
        messages: {
            trello_key: vocab['Alert_feld_required'],
            trello_token: vocab['Alert_feld_required']
        }
    });

    $("#persondata_form").validate({
        ignore:[],
        rules: {
            person_name: "required",
            person_email: "required"
        },
        messages: {
            person_name: vocab['Alert_feld_required'],
            person_email: vocab['Alert_feld_required']

        }
    });


    $("#btnSendUpdateUserData").click(function(){
        if ($("#persondata_form").valid()) {
            var $form = jQuery('#persondata_form'),
                formData = $form.serialize();

            $.ajax({
                type: "POST",
                url: path + '/helpdezk/home/updateUserData',
                dataType: 'json',
                data: {
                    idperson: $('#hidden-idperson').val(),
                    name: $('#person_name').val(),
                    ssn: $('#person_ssn_cpf').val().replace(/[^0-9]/gi, ''),
                    gender: $('#person_gender').val(),
                    dtbirth: $('#person_dtbirth').val(),
                    email: $('#person_email').val(),
                    phone: $('#person_phone').val().replace(/[^0-9]/gi, ''),
                    branch:$('#person_branch').val().replace(/[^0-9]/gi, ''),
                    cellphone: $('#person_cellphone').val().replace(/[^0-9]/gi, ''),
                    country: $('#person_country').val(),
                    state: $('#person_state').val(),
                    city: $('#person_city').val(),
                    zipcode: $('#person_zipcode').val().replace(/[^0-9]/gi, ''),
                    neighb: $('#person_neighborhood').val(),
                    typestreet: $('#person_typestreet').val(),
                    street: $('#person_address').val(),
                    number: $('#person_number').val().replace(/[^0-9]/gi, ''),
                    complement: $('#person_complement').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_filure'],'alert-update');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {

                        if (userPhotoDropzone.getQueuedFiles().length > 0) {
                            console.log('There are '+ userPhotoDropzone.getQueuedFiles().length + ' file(s)');

                            userPhotoDropzone.options.params = {iduser: $("#hidden-idperson").val() };
                            userPhotoDropzone.processQueue();
                        }
                        var message;
                        var exposureTime ;
                        if (obj.id == 99) {
                            exposureTime = 5000;
                            message = vocab['Alert_sccess_update']+' : '+obj.message;
                        } else {
                            exposureTime = 2000;
                            message = vocab['Alert_sccess_update'];
                        }
                        modalAlertMultiple('success',message,'alert-update');
                        setTimeout(function(){
                            $('#modal-form-persondata').modal('hide');
                            location.href = "" ;
                        },exposureTime);

                    } else {
                        modalAlertMultiple('danger',vocab['Alert_filure']+': '+obj.message,'alert-update');
                        setTimeout(function(){
                            $('#modal-form-persondata').modal('hide');
                            location.href = "" ;
                        },2000);

                    }
                }
            });
        } else {
            console.log('nao validou');
            return false;
        }

    });

    /**
     * Dropzone
     */    
    /* var userPhotoDropzone = new Dropzone("#userPhotoDropzone", {  url: path + "/helpdezk/home/savePhoto",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['dropzon_user_photot_message'],
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true,
        dictRemoveFile: vocab['dropzon_remove_file'],
        maxFilesize: 1024,
        dictFileTooBig: vocab['dropzon_File_Too_Big'],

        success: function (file, response) {
            this.removeFile(file);
            userPhotoDropzone.processQueue();
        },
        init: function(file) {
            // Register for the thumbnail callback.
            // When the thumbnail is created the image dimensions are set.
            this.on("thumbnail", function(file) {
                // Do the dimension checks you want to do
                if (file.width / file.height != 1) {
                    file.rejectDimensions()
                }
                else {
                    file.acceptDimensions();
                }
            });
        },
        // Instead of directly accepting / rejecting the file, setup two
        // functions on the file that can be called later to accept / reject
        // the file.
        accept: function(file, done) {
            file.acceptDimensions = done;
            file.rejectDimensions = function() { done(vocab['dropzon_invalid_dimension']); };
            // Of course you could also just put the `done` function in the file
            // and call it either with or without error in the `thumbnail` event
            // callback, but I think that this is cleaner.
        }
    }); */

    // user - change password
    $("#modal-change-user-password-form").validate({
        ignore:[],
        rules: {
            "modal-new-user-password":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                remote:{
                    url: path+"/main/home/checkUserPass",
                    type: 'post',
                    data: {
                        personId:function(){return $('#nav-user-id').val();}
                    }
                }
            },
            "modal-confirm-new-user-pass":  {
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                equalTo: "#modal-new-user-password"
            }
        },
        messages: {
            "modal-new-user-password":{required:vocab['Alert_field_required']},
            "modal-confirm-new-user-pass":{equalTo: vocab['Alert_different_passwords']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $("#btnChangeUserPassSave").click(function(){
        if (!$("#modal-change-user-password-form").valid()) {
            return false;
        }

        if(!$("#btnChangeUserPassSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/main/home/changeUserPassword',
                dataType: 'json',
                data: { 
                    personId:$('#nav-user-id').val(),
                    newPassword:$('#modal-new-user-password').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['generic_error_msg'],'alert-modal-change-user-password');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Alert_change_password'],'alert-modal-change-user-password');
                        setTimeout(function(){
                            $('#modal-change-user-password').modal('hide');
                        },2000);    
                    } else {
                        modalAlertMultiple('danger',obj.message,'alert-modal-change-user-password');
                    }
                },
                beforeSend: function(){
                    $("#btnChangeUserPassSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnChangeUserPassClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnChangeUserPassSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnChangeUserPassClose").removeClass('disabled');
                }
            });
        }
    });
    
    /* when the modal is hidden */
    $('#modal-change-user-password').on('hidden.bs.modal', function() {
        $('#modal-change-user-password-form').trigger('reset');
        
        if($("#modal-new-user-password").hasClass('error')){
            $("#modal-new-user-password").removeClass('error');
        }

        if($("#modal-new-user-password_validate_error").hasClass('error')){
            $("#modal-new-user-password_validate_error").removeClass('error');
        }
        $("#modal-new-user-password_validate_error").html('');
        
        if($("#modal-confirm-new-user-pass").hasClass('error')){
            $("#modal-confirm-new-user-pass").removeClass('error');
        }

        if($("#modal-confirm-new-user-pass_validate_error").hasClass('error')){
            $("#modal-confirm-new-user-pass_validate_error").removeClass('error');
        }
        $("#modal-confirm-new-user-pass_validate_error").html('');
    });

    // Salva secret via AJAX
    $('#btnConfirmAuthenticator').on('click', function () {
        var secret = $('#secret').val();
        var code = $('#authenticator-code').val();
        $(this).prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: path + '/main/home/saveAuthenticatorSecret',
            dataType: 'json',
            data: { secret: secret, code: code },
            success: function (ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if (obj.success) {
                    $('#modal-authenticator').modal('hide');
                    $('#modal-signature').modal('show');
                } else {
                    modalAlertMultiple('danger', obj.message || vocab['generic_error_msg'], 'alert-authenticator');
                }
            },
            error: function () {
                modalAlertMultiple('danger', vocab['generic_error_msg'], 'alert-authenticator');
            },
            complete: function () {
                $('#btnConfirmAuthenticator').prop('disabled', false);
            }
        });
    });

    // Listener global para o input do autenticador
    $(document).on('input', '#authenticator-code', function () {
        var val = $(this).val();
        if (val.length === 6 && /^\d{6}$/.test(val)) {
            $('#btnConfirmAuthenticator').prop('disabled', false);
        } else {
            $('#btnConfirmAuthenticator').prop('disabled', true);
        }
    });

    // Listener global para o input do código de assinatura
    $(document).on('input', '#signature-auth-code', function () {
        var val = $(this).val();
        $('#btnSign').prop('disabled', !(val.length === 6 && /^\d{6}$/.test(val)));
    });

    $("#btnSetUser2FA").click(function(){
        if(!$("#btnSetUser2FA").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url:  path + '/main/home/isTwoFactorSetupRequired',
                dataType: 'json',
                data: {
                    userId: $('#nav-user-id').val()
                },
                error: function (ret) {
                    showAlert(vocab['generic_error_msg'],'danger');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        if(obj.needsSetup) {
                            showModalSignature(obj.qrCode, obj.secret);
                        }else{
                            showAlert(obj.message,'warning');
                        }
                    } else {
                        showAlert(obj.message,'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnSetUser2FA").addClass('disabled');
                },
                complete: function(){
                    $("#btnSetUser2FA").removeClass('disabled');
                }
            });
        }
    });
});

function showModalSignature(qrcode, secret ) {
    if(qrcode) {
        $('#qrcode-img').attr('src', qrcode);
        $('#modal-authenticator').modal('show');
    } else{
        $('#modal-signature').modal('show');
        $('#signature-auth-code').on('input', function () {
            var val = $(this).val();
            $('#btnSign').prop('disabled', !(val.length === 6 && /^\d{6}$/.test(val)));
        });
    }
    $('#secret').val(secret);
}