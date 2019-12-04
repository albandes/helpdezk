var global_idperson = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    /*
     *  Chosen
     */
    $("#logintype").chosen({        width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#type_company").chosen({     width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#company").chosen({          width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#department").chosen({       width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#type_user").chosen({        width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#location").chosen({         width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#country").chosen({          width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#state").chosen({            width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#city").chosen({             width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#neighborhood").chosen({     width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#type_street").chosen({      width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#address").chosen({          width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#permgroups").chosen({       width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#persongroups").chosen({     width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Combos
     */

    var objPersonData = {
        changeState: function() {
            var countryId = $("#country").val();
            $.post(path+"/admin/person/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#state").html(valor);
                    $("#state").trigger("chosen:updated");
                    return objPersonData.changeCity();
                })
        },
        changeCity: function() {
            var stateId = $("#state").val();
            $.post(path+"/admin/person/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#city").html(valor);
                    $("#city").trigger("chosen:updated");
                    return objPersonData.changeNeighborhood();
                });
        },
        changeNeighborhood: function() {
            var cityId = $("#city").val();
            $.post(path+"/admin/person/ajaxNeighborhood",{cityId: cityId},
                function(valor){
                    $("#neighborhood").html(valor);
                    $("#neighborhood").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        changeStreet: function() {
            var typestreetId = $("#type_street").val();
            $.post(path+"/admin/person/ajaxStreet",{typestreetId:typestreetId},
                function(valor){
                    $("#address").html(valor);
                    $("#address").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        checkCPFCNPJ: function(typecheck,idperson,valuecheck) {
            if(valuecheck != ''){
                $.ajax({
                    type: "POST",
                    url: path+"/scm/scmFornecedor/checkCPFCNPJ",
                    dataType: 'json',
                    data: {typecheck:typecheck,idperson:idperson,valuecheck:valuecheck},
                    error: function (ret) {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-fornecedor');
                    },
                    success: function(ret){

                        var obj = jQuery.parseJSON(JSON.stringify(ret));
                        //console.log(obj);

                        if(!obj.status){
                            if($.isNumeric(obj.idfornecedor)) {
                                $("#btn-modal-ok").attr("href", 'javascript:alertClose()');
                                $('#modal-notification').html(obj.message + '<br> ID Fornecedor: ' + obj.idfornecedor +'<br> Nome: '+ obj.namefornecedor);
                                $("#tipo-alert").attr('class', 'alert alert-danger');
                                $('#modal-alert').modal('show');
                            }else{
                                $("#btn-modal-ok").attr("href", 'javascript:alertClose()');
                                $('#modal-notification').html(obj.message);
                                $("#tipo-alert").attr('class', 'alert alert-danger');
                                $('#modal-alert').modal('show');
                            }

                        }else {
                            return false ;
                        }
                    }
                });
            }
            return false ;
        },
        changeDepartment: function() {
            var companyId = $("#company").val(), _token = $("#_token").val();
            $.post(path+"/admin/person/ajaxDepartment",{companyId: companyId, _token:_token},
                function(valor){
                    $("#department").html(valor);
                    $("#department").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        changeUserLine: function() {
            var typeUserId = $("#type_user").val();
            if(typeUserId == 2){
                $(".userView").removeClass('hide');
                $(".operatorView").addClass('hide');
            }else if(typeUserId == 0){
                $(".operatorView").addClass('hide');
                $(".userView").addClass('hide');
            }else{
                $(".operatorView").removeClass('hide');
                $(".userView").addClass('hide');
            }
            return false ;
        },
        reloadLocation: function(newlocation) {
            $.post(path+"/admin/person/ajaxLocation",{},function(valor){
                    $("#location").html(valor);
                    selectLocation($("#location"),newlocation);
                })
        },
        reloadState: function(countryId,newstate) {
            $.post(path+"/admin/person/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#state").html(valor);
                    selectLocation($("#state"),newstate);
                    return objPersonData.changeCity();
                })
        },
        reloadCity: function(stateId,newcity) {
            $.post(path+"/admin/person/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#city").html(valor);
                    selectLocation($("#city"),newcity);
                    return objPersonData.changeNeighborhood();
                });
        },
        reloadNeighborhood: function(cityId,newneighborhood) {
            $.post(path+"/admin/person/ajaxNeighborhood",{cityId: cityId},
                function(valor){
                    $("#neighborhood").html(valor);
                    selectLocation($("#neighborhood"),newneighborhood);
                    return false;
                })
            return false ;
        },
        reloadStreet: function(typestreetId,newstreet) {
            $.post(path+"/admin/person/ajaxStreet",{typestreetId:typestreetId},
                function(valor){
                    $("#address").html(valor);
                    selectLocation($("#address"),newstreet);
                    return false;
                })
            return false ;
        }
    }

    $("#country").change(function(){
        objPersonData.changeState();
    });

    $("#state").change(function(){
        objPersonData.changeCity();
    });

    $("#city").change(function(){
        objPersonData.changeNeighborhood();
    });

    $("#type_street").change(function(){
        objPersonData.changeStreet();
    });
    
    $("#type_user").change(function(){
        objPersonData.changeUserLine();
    });

    $("#cpf").on('blur',function(){
        objPersonData.checkCPFCNPJ('F',$('#idperson').val(),$('#cpf').val());
    });

    $("#ein_cnpj").on('blur',function(){
        objPersonData.checkCPFCNPJ('J',$('#idperson').val(),$('#ein_cnpj').val());
    });

    $("#company").change(function(){
        objPersonData.changeDepartment();
    });

    /*
     * Mask
     */
    $('#dtbirth').mask('00/00/0000');
    $('#number').mask('0000');
    $('#fax').mask('(00) 0000-0000');
    $('#phone').mask('(00) 0000-0000');
    $('#mobile').mask('(00) 00000-0000');
    $('#zipcode').mask('00000-000');
    $('#cpf').mask('000.000.000-00');
    $('#cnpj').mask('00.000.000/0000-00');
 
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/person/index');

    $("#btnCreatePerson").click(function(){

        if (!$("#create-person-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/person/createPerson',
            dataType: 'json',
            data: $("#create-person-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-person');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idperson)) {
                    var idperson = obj.idperson;

                    $('#modal-idperson').html(obj.idperson);
                    $('#modal-person-description').html(obj.description);

                    $("#btnModalAlert").attr("href", path + '/admin/person/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-person');
                }
            },
            beforeSend: function(){
                $("#btnCreatePerson").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnCreatePerson").removeAttr('disabled')
            }
        });
    });

    $("#btnUpdatePerson").click(function(){

        if (!$("#update-person-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/person/updatePerson',
            dataType: 'json',
            data: $("#update-person-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-person');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    var idperson = obj.idperson;
                    
                    $('#modal-notification').html(aLang['Edit_sucess'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/admin/person/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-person');

                }

            },
            beforeSend: function(){
                $("#btnUpdatePerson").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnUpdatePerson").removeAttr('disabled')
            }

        });


    });

    $("#btnChangePass").click(function(){
        $('#modal-form-changepassword').modal('show');
    });

    $("#btnAddLocation").click(function(){
        $('#modal-form-location').modal('show');
    });

    $("#btnAddState").click(function(){
        $('#modalIdCountry').val($('#country').val());
        $('#txtCountry').html($("#country").find('option:selected').text());
        $('#modal-form-state').modal('show');
    });

    $("#btnAddCity").click(function(){
        $('#modalIdState').val($('#state').val());
        $('#txtState').html($("#state").find('option:selected').text());
        $('#modal-form-city').modal('show');
    });

    $("#btnAddNeighborhood").click(function(){
        $('#modalIdCity').val($('#city').val());
        $('#txtCity').html($("#city").find('option:selected').text());
        $('#modal-form-neighborhood').modal('show');
    });

    $("#btnAddStreet").click(function(){
        $('#modal-form-street').modal('show');
    });

    $("#btnSendChangePassword").click(function(){
        console.log('clicou salvar');
        if (!$("#changepassword-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/person/changePassword',
            dataType: 'json',
            data: { idperson:$('#idperson').val(),
                    newpassword:$('#modal_password').val(),
                    changepass:$('#modal-changePass').val(),
                    _token:$('#_token').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Lost_password_err'),'alert-modal-changepass');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idperson)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_change_password'),'alert-modal-changepass');
                    setTimeout(function(){
                        $('#modal-form-changepassword').modal('hide');
                        $('#modal-changePass').iCheck('uncheck');
                        $('#changepassword-form').trigger('reset');                        
                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Lost_password_err'),'alert-module');
                }
            }
        });

    });

    $("#btnSendlocation").click(function(){
        
        if (!$("#location-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/person/insertLocation',
            dataType: 'json',
            data: { location:$('#modal_location_name').val(),
                    _token:$('#_token').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-location');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idlocation)) {
                    modalAlertMultiple('success', makeSmartyLabel('Alert_inserted'),'alert-location');
                    setTimeout(function(){
                        $('#modal-form-location').modal('hide');
                        $('#location-form').trigger('reset');
                    },2000);
                    objPersonData.reloadLocation(obj.idlocation);                   
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-location');
                }
            },
            beforeSend: function(){
                $("#btnSendlocation").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendlocation").removeAttr('disabled');
            }
        });

    });

    $("#btnSendState").click(function(){
        
        if (!$("#state-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/person/insertState',
            dataType: 'json',
            data: { _token:$('#_token').val(),
                idCountry:$('#modalIdCountry').val(),
                nameState:$('#modalStateName').val(),
                abbrState:$('#modalStateAbbr').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-state');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idstate)) {
                    modalAlertMultiple('success', makeSmartyLabel('Alert_inserted'),'alert-state');
                    setTimeout(function(){
                        $('#modal-form-state').modal('hide');
                        $('#state-form').trigger('reset');
                    },2000);
                    objPersonData.reloadState($('#modalIdCountry').val(),obj.idstate);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-state');
                }
            },
            beforeSend: function(){
                $("#btnSendState").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendState").removeAttr('disabled')
            }
        });

    });

    $("#btnSendCity").click(function(){
        
        if (!$("#city-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/person/insertCity',
            dataType: 'json',
            data: { _token:$('#_token').val(),
                idState:$('#modalIdState').val(),
                nameCity:$('#modalCityName').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-city');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idcity)) {
                    modalAlertMultiple('success', makeSmartyLabel('Alert_inserted'),'alert-city');
                    setTimeout(function(){
                        $('#modal-form-city').modal('hide');
                        $('#city-form').trigger('reset');
                    },2000);
                    objPersonData.reloadCity($('#modalIdState').val(),obj.idcity);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-city');
                }
            },
            beforeSend: function(){
                $("#btnSendCity").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendCity").removeAttr('disabled')
            }
        });

    });

    $("#btnSendNeighborhood").click(function(){
        
        if (!$("#neighborhood-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/person/insertNeighborhood',
            dataType: 'json',
            data: { _token:$('#_token').val(),
                idCity:$('#modalIdCity').val(),
                nameNeighborhood:$('#modalNeighborhoodName').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-neighborhood');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idneighborhood)) {
                    modalAlertMultiple('success', makeSmartyLabel('Alert_inserted'),'alert-neighborhood');
                    setTimeout(function(){
                        $('#modal-form-neighborhood').modal('hide');
                        $('#neighborhood-form').trigger('reset');
                    },2000);
                    objPersonData.reloadNeighborhood($('#modalIdCity').val(),obj.idneighborhood);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-neighborhood');
                }
            },
            beforeSend: function(){
                $("#btnSendNeighborhood").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendNeighborhood").removeAttr('disabled')
            }
        });

    });

    $("#btnSendStreet").click(function(){
        
        if (!$("#street-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/person/insertStreet',
            dataType: 'json',
            data: { _token:$('#_token').val(),
                idTypeStreet:$('#modalTypeStreet').val(),
                nameStreet:$('#modalStreetName').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-street');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idstreet)) {
                    modalAlertMultiple('success', makeSmartyLabel('Alert_inserted'),'alert-street');
                    setTimeout(function(){
                        $('#modal-form-street').modal('hide');
                        $('#street-form').trigger('reset');
                    },2000);
                    objPersonData.reloadStreet($('#modalTypeStreet').val(),obj.idstreet);
                    selectLocation($('#type_street'),$('#modalTypeStreet').val());
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-street');
                }
            },
            beforeSend: function(){
                $("#btnSendState").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendState").removeAttr('disabled')
            }
        });

    });

    /*
     * Validate
     */
    $("#create-person-form").validate({
        ignore:[],
        rules: {
            login:{
                required:function(element){return $('input[name="category"]:checked').val() == 'natural';},
                remote:{
                    url: path+"/admin/person/checklogin",
                    type: 'post',
                    data: { login:function(){return $('#login').val();}}
                }
            },
            password:{required:function(element){return $('input[name="category"]:checked').val() == 'natural';}},
            cpassword:  {equalTo: "#password"},
            personName: "required",
            type_user:{required:function(element){return $('input[name="category"]:checked').val() == 'natural';}},
            type_company:{required:function(element){return $('input[name="category"]:checked').val() == 'juridical';}},
            email: {required:true,email:true},
            department_default:{required:function(element){return $('input[name="category"]:checked').val() == 'juridical';}},
            "persongroups[]":{required:function(element){return ($('#type_user').val() == '1' || $('#type_user').val() == '3');}},
            company:{required:function(element){return $('input[name="category"]:checked').val() == 'natural';}},
            department:{required:function(element){return $('input[name="category"]:checked').val() == 'natural';}}
        },
        messages: {
            login:{required:makeSmartyLabel('Alert_field_required')},
            password:{required:makeSmartyLabel('Alert_field_required')},
            cpassword:{equalTo: makeSmartyLabel('Alert_different_passwords')},
            personName:      makeSmartyLabel('Alert_field_required'),
            type_user:{required:makeSmartyLabel('Alert_field_required')},
            type_company:{required:makeSmartyLabel('Alert_field_required')},
            email:{required:makeSmartyLabel('Alert_field_required'),email:makeSmartyLabel('Alert_invalid_email')} ,
            department_default:{required:makeSmartyLabel('Alert_field_required')},
            "persongroups[]":{required:makeSmartyLabel('Alert_field_required')},
            company:{required:makeSmartyLabel('Alert_field_required')},
            department:{required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-person-form").validate({
        ignore:[],
        rules: {
            personName: "required",
            type_user:{required:function(element){return $('#category').val() == '1';}},
            type_company:{required:function(element){return $('#category').val() == '2';}},
            email: {required:true,email:true},
            "persongroups[]":{required:function(element){return ($('#type_user').val() == '1' || $('#type_user').val() == '3');}},
            company:{required:function(element){return $('#category').val() == '1';}},
            department:{required:function(element){return $('#category').val() == '1';}}
        },
        messages: {
            personName:      makeSmartyLabel('Alert_field_required'),
            type_user:{required:makeSmartyLabel('Alert_field_required')},
            type_company:{required:makeSmartyLabel('Alert_field_required')},
            email:{required:makeSmartyLabel('Alert_field_required'),email:makeSmartyLabel('Alert_invalid_email')},
            "persongroups[]":{required:makeSmartyLabel('Alert_field_required')},
            company:{required:makeSmartyLabel('Alert_field_required')},
            department:{required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#changepassword-form").validate({
        ignore:[],
        rules: {
            modal_password:{required:true},
            modal_cpassword:  {equalTo: "#modal_password"}
        },
        messages: {
            modal_password:{required:makeSmartyLabel('Alert_field_required')},
            modal_cpassword:{equalTo: makeSmartyLabel('Alert_different_passwords')}
        }
    });

    $("#location-form").validate({
        ignore:[],
        rules: {
            modal_location_name:        "required"
        },
        messages: {
            modal_location_name:        makeSmartyLabel('Alert_field_required')
        }
    });

    $("#state-form").validate({
        ignore:[],
        rules: {
            modalStateName: "required"
        },
        messages: {
            modalStateName: makeSmartyLabel('Alert_field_required')
        }
    });

    $("#city-form").validate({
        ignore:[],
        rules: {
            modalCityName:  "required"
        },
        messages: {
            modalCityName:  makeSmartyLabel('Alert_field_required')
        }
    });

    $("#neighborhood-form").validate({
        ignore:[],
        rules: {
            modalNeighborhoodName:  "required"
        },
        messages: {
            modalNeighborhoodName:  makeSmartyLabel('Alert_field_required')
        }
    });

    $("#street-form").validate({
        ignore:[],
        rules: {
            modalStreetName:    "required"
        },
        messages: {
            modalStreetName:    makeSmartyLabel('Alert_field_required')
        }
    });

    /* limpa campos modal */
    $('.modal').on('hidden.bs.modal', function() {
        $('#module-form').trigger('reset');
        $('#category-form').trigger('reset');
    });

    $('#filladress').on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $('.addressView').removeClass('hide');            
        }else{
            $('.addressView').addClass('hide');
        }
    });

    $("input[name='category']").on('ifClicked', function() { // bind a function to the change event
        var typeFlg = $(this).val();
        $('#btnCreatePerson').removeClass('hide');

        if(typeFlg == 'juridical'){
            $('.juridicalView').removeClass('hide');
            $('.commonView').removeClass('hide');
            $('.naturalView').addClass('hide');
            $(".userView").addClass('hide');
            $(".operatorView").addClass('hide');
        }else{
            $('.juridicalView').addClass('hide');
            $('.commonView').removeClass('hide');
            $('.naturalView').removeClass('hide');
        }
    });

    // tooltips
    $('.tooltip-buttons').tooltip();

});

function selectLocation(obj,newst){
    var st = obj.val();

    if(!st || st != newst){
        obj.find("option[value="+st+"]").removeAttr("selected");
        obj.find("option[value="+newst+"]").attr("selected","selected");
    }
    obj.trigger("chosen:updated");
}
