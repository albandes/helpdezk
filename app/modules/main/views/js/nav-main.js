$(document).ready(function () {

    // https://harvesthq.github.io/chosen/
    $("#person_country").chosen({ width: "95%", no_results_text: "Nothing found!"})
    $("#person_state").chosen({ width: "95%",   no_results_text: "Nothing found!"})
    $("#person_city").chosen({ width: "95%",    no_results_text: "Nothing found!"})
    $("#person_neighborhood").chosen({ width: "95%",    no_results_text: "Nothing found!"})
    $("#person_typestreet").chosen({ width: "95%", no_results_text: "Nothing found!"})

    // Mask
    $('#person_dtbirth').mask('00/00/0000');
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
    });

    // Buttons
    $("#btnUpdateUserData,.btnEditUserProfile").click(function(){
        $('#modal-form-persondata').modal('show');
        //countdown.start(timesession);
    });

    $("#btnEditUserConfigExternal").click(function(){
        $('#modal-config-external').modal('show');
    });

    $("#btnUpdatePhoto").click(function(){
        $('#modal-person-photo').modal('show');
        //countdown.start(timesession);
    });

    $(".btnEditUserPass").click(function(){
        $('#modal-change-user-password').modal('show');
    });

    // Save Configure External APIs data
    $("#btnSaveConfigExternal").click(function(){
        if (!$("#modal-config-external-form").valid()) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/home/saveConfigExternal',
            dataType: 'json',
            data: { idperson:$('#hidden-idperson').val(),
                    trellokey:$('#trello_key').val(),
                    trellotoken:$('#trello_token').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-change-user-pass');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.success) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_external_settings_OK'),'alert-config-external');
                    setTimeout(function(){
                        $('#modal-change-user-password').modal('hide');
                        location.href = "" ;
                    },2000);

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure')+': '+obj.message,'alert-config-external');
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
            trello_key: makeSmartyLabel('Alert_field_required'),
            trello_token: makeSmartyLabel('Alert_field_required')
        }
    });

    $("#persondata_form").validate({
        ignore:[],
        rules: {
            person_name: "required",
            person_email: "required"
        },
        messages: {
            person_name: makeSmartyLabel('Alert_field_required'),
            person_email: makeSmartyLabel('Alert_field_required')

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
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update');
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
                            message = makeSmartyLabel('Alert_success_update')+' : '+obj.message;
                        } else {
                            exposureTime = 2000;
                            message = makeSmartyLabel('Alert_success_update');
                        }
                        modalAlertMultiple('success',message,'alert-update');
                        setTimeout(function(){
                            $('#modal-form-persondata').modal('hide');
                            location.href = "" ;
                        },exposureTime);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure')+': '+obj.message,'alert-update');
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




    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var userPhotoDropzone = new Dropzone("#userPhotoDropzone", {  url: path + "/helpdezk/home/savePhoto",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('dropzone_user_photot_message'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true,
        dictRemoveFile: makeSmartyLabel('dropzone_remove_file'),
        maxFilesize: 1024,
        dictFileTooBig: makeSmartyLabel('dropzone_File_Too_Big'),

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
            file.rejectDimensions = function() { done(makeSmartyLabel('dropzone_invalid_dimension')); };
            // Of course you could also just put the `done` function in the file
            // and call it either with or without error in the `thumbnail` event
            // callback, but I think that this is cleaner.
        }
    });

    $("#change_user_pwd_form").validate({
        ignore:[],
        rules: {
            userconf_password:{
                required:true,
                remote:{
                    url: path+"/helpdezk/home/checkUserPass",
                    type: 'post',
                    data: {
                        personId:function(){return $('#hidden-idperson').val();}
                    }
                }
            },
            userconf_cpassword:  {equalTo: "#userconf_password"}
        },
        messages: {
            userconf_password:{required:makeSmartyLabel('Alert_field_required')},
            userconf_cpassword:{equalTo: makeSmartyLabel('Alert_different_passwords')}
        }
    });

    $("#btnSaveChangeUserPass").click(function(){
        if (!$("#change_user_pwd_form").valid()) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/home/changeUserPassword',
            dataType: 'json',
            data: { idperson:$('#hidden-idperson').val(),
                newpassword:$('#userconf_password').val(),
                changepass:$('#userconf-changePass').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-change-user-pass');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idperson)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_change_password'),'alert-change-user-pass');
                    setTimeout(function(){
                        $('#modal-change-user-password').modal('hide');
                        location.href = "" ;
                    },2000);

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-change-user-pass');
                }
            }
        });
    });


});