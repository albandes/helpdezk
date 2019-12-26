



function modalAlert(type,message)
{

    $("#response").animate({height: '+=72px'}, 300);

    $('<div class="alert alert-'+type+'">' +
        '<button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>')
        .hide().appendTo('#response').fadeIn(1000);

    $(".alert").delay(3500).fadeOut("normal", function(){ $(this).remove(); });

    $("#response").delay(4000).animate({ height: '-=72px' }, 300);
}

function modalAlertMultiple(type,message,id)
{

    $("#"+id+"").animate({height: '+=72px'}, 300);

    $('<div class="alert alert-'+type+'">' +
    '<button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>')
        .hide().appendTo("#"+id+"").fadeIn(1000);

    $(".alert").delay(3500).fadeOut("normal", function(){ $(this).remove(); });

    $("#"+id+"").delay(4000).animate({ height: '-=72px' }, 300);

    return false;
}

function makeSmartyLabel(label){
    if(!aLang[label]) { // quick and dirty will be true for '', null, undefined, 0, NaN and false.
        console.log('It is missing in the language file: '+label);
        return ' ... ';
    } else {
        return aLang[label].replace (/\"/g, "");
    }

}




countdown = {
    start: function(seconds){
        if(typeof  this.time == "undefined"){
            this.time = seconds;
        }
        var tempo = seconds;

        if((tempo - 1) >= 0){
            var min = parseInt(tempo/60),
                hor = parseInt(min/60),
                min = min%60,
                seg = tempo%60;

            if(min < 10){
                min = "0"+min;
                min = min.substr(0, 2);
            }
            if(seg <=9){
                seg = "0"+seg;
            }
            if(hor <=9){
                hor = "0"+hor;
            }

            if(hor > 0)
                horaImprimivel = hor+'h ' + min + 'm ' + seg + 's';
            else if(min > 0)
                horaImprimivel = min + 'm ' + seg + 's';
            else
                horaImprimivel = seg + 's';
            $(document.getElementById("numberCountdown")).html(horaImprimivel);
            tempo--;
            timer = setTimeout(function(){
                countdown.start(tempo);
            },1000);
        } else {

            window.location = path + "/main/home/lockscreen";
        }
    },
    stop: function(){
        clearTimeout(timer);
    },
    refresh: function(){
        countdown.stop();
        countdown.start(this.time);
    }

}


$(document).ready(function () {
    console.log(path+"/app/lang/"+default_lang+".txt");
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

    $("#btnUpdateUserData,.btnEditUserConfig").click(function(){
        $('#modal-form-persondata').modal('show');
        //countdown.start(timesession);
    });

    $("#btnUpdatePhoto").click(function(){
        $('#modal-person-photo').modal('show');
        //countdown.start(timesession);
    });

    $(".btnEditUserPass").click(function(){
        $('#modal-change-user-password').modal('show');
    });

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

    $("#persondata_form").validate({
        ignore:[],
        rules: {
            person_name: "required",
            person_email: "required",
            person_cellphone: "required"
        },
        messages: {
            person_name: makeSmartyLabel('Alert_field_required'),
            person_email: makeSmartyLabel('Alert_field_required'),
            person_cellphone: makeSmartyLabel('Alert_field_required')
        }
    });

    $("#btnSendUpdateUserData").click(function(){
        if ($("#persondata_form").valid()) {
            var $form = jQuery('#persondata_form'),
                formData = $form.serialize();

            $.ajax({
                type: "POST",
                url: path + '/helpdezk/home/updateUserData',
                dataType: 'text',
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
                    if(ret == 'OK') {
                        console.log('voltou e gravou');
                        if (userPhotoDropzone.getQueuedFiles().length > 0) {
                            console.log('have '+ userPhotoDropzone.getQueuedFiles().length + ' file(s)');

                            userPhotoDropzone.options.params = {iduser: $("#hidden-idperson").val() };
                            userPhotoDropzone.processQueue();
                        }else{
                            $('#modal-form-persondata').modal('hide');
                            location.href = "" ;
                        }
                        modalAlertMultiple('success',makeSmartyLabel('Alert_success_update'),'alert-update');

                    } else if(ret == 'OK-without-address') {
                        if (userPhotoDropzone.getQueuedFiles().length > 0) {
                            console.log('have '+ userPhotoDropzone.getQueuedFiles().length + ' file(s)');

                            userPhotoDropzone.options.params = {iduser: $("#hidden-idperson").val() };
                            userPhotoDropzone.processQueue();
                        }else{
                            $('#modal-form-persondata').modal('hide');
                            location.href = "" ;
                        }
                        modalAlertMultiple('success',makeSmartyLabel('Alert_success_withoutaddress'),'alert-update');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update');
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
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_user_photo_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        autoProcessQueue: false
    });

    userPhotoDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    userPhotoDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        $('#modal-form-persondata').modal('hide');
        location.href = "" ;
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
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
/*
    if(changepass == '1'){
        $('#btnCancelUserPass').addClass('hide');
        $('#modal-change-user-password').modal('show');
    }
*/

});