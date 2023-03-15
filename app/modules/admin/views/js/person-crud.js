//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], hasRestrict=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Datepicker
     */
    if(dtpLanguage == '' || dtpLanguage === 'undefined' || !dtpLanguage){
        // Default language en (English)
        var dpOptions = {
            format: dtpFormat,
            autoclose:  dtpAutoclose,
            orientation: dtpOrientation
        };
    }else{
        var dpOptions = {
            format: dtpFormat,
            language:  dtpLanguage,
            autoclose:  dtpAutoclose,
            orientation: dtpOrientation
        };
    }
    
    $('.input-group.date').datepicker(dpOptions);
    
    /**
     * Select2
     */
    //if($("#create-person-form").length > 0){
        $('#cmbLoginType').select2({width:"100%",height:"100%",placeholder:vocab['phl_select_login_type'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbJuridicalType').select2({width:"100%",height:"100%",placeholder:vocab['Select_acess_level'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbCompany').select2({width:"100%",height:"100%",placeholder:vocab['Select_company'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbDepartment').select2({width:"100%",height:"100%",placeholder:vocab['Select_department'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbAccessLevel').select2({width:"100%",height:"100%",placeholder:vocab['Select_acess_level'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbPermissionGroups').select2({width:"100%",height:"100%",placeholder:vocab['Permission_Groups_Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbGroup').select2({width:"100%",height:"100%",placeholder:vocab['Select_group'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbLocation').select2({width:"100%",height:"100%",placeholder:vocab['Select_location'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbStreetType').select2({width:"100%",height:"100%",placeholder:vocab['phl_select_street_type'],allowClear:true,minimumResultsForSearch: 10});
        $('#modal-street-type').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-street-form')});
        /*$('#localeID_1').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-vocabulary-form')}); */
    //}
    
    /**
     * Mask
     */
    $('#number').mask('0000');
    $('#fax').mask('(00) 0000-0000');
    $('#phone').mask('(00) 0000-0000');
    $('#mobile').mask('(00) 00000-0000');
    $('#zipcode').mask('00000-000');
    $('#ssnCpf').mask('000.000.000-00');
    $('#einCnpj').mask('00.000.000/0000-00');

    /**
     * iCheck - checkboxes/radios styling
     */
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /**
     * Dropzone
     */
   /*  var myDropzone = new Dropzone("#moduleLogo", {  url: path + "/admin/modules/saveLogo/",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['Drag_image_msg'],
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png, .gif',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("complete", function(file) {
    
        if(file.status === "canceled" || file.status === "error"){
            errorname.push(file.name);
            flgerror = 1;
        }else if((file.xhr)){
            var obj = JSON.parse(file.xhr.response);
        
            if(obj.success) {
                filesended = filesended + 1;
                upname.push(file.name);
            } else {
                errorname.push(file.name);
                flgerror = 1;
            }
        }
        
    });

    myDropzone.on("queuecomplete", function (file) {
        var list,msg,typeMsg;

        if(errorname.length == 0 && (filesended == dropzonefiles)){
            if(btnClicked=="1"){
                saveData(upname,'add');
            }else if(btnClicked=="2"){
                saveData(upname,'upd')
            }                            
        }else{
            var totalAttach = dropzonefiles - filesended;
            list = '<h4>'+vocab['files_not_attach_list']+'</h4><br>';
            errorname.forEach(element => {
                list = list+element+'<br>';
            });
            list = list+'<br><strong>'+vocab['logo_attach_after']+'</strong>';
            typeMsg = 'warning';
            msg = vocab['save_anyway_question'];
            showNextStep(list,msg,typeMsg,totalAttach);
        }        
        
        dropzonefiles = 0; 
        filesended = 0;
        flgerror = 0;
    }); */
    
    /**
     * Flexdatalist - Autocomplete fields
     */
    /** Country */
    $("#country").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: 'name',
        minLength: 2,
        selectionRequired: true,
        valueProperty: 'id',
        url: path + '/admin/person/searchCountry',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post',
        cache: false
    });

    /** State */
    $("#state").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: 'name',
        minLength: 2,
        selectionRequired: true,
        valueProperty: 'id',
        url: path + '/admin/person/searchState',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post',
        params:{countryId:function(element){return $("#country").val();}},
        cache: false
    });

    /** City */
    $("#city").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: 'name',
        minLength: 3,
        selectionRequired: true,
        valueProperty: 'id',
        url: path + '/admin/person/searchCity',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post',
        params:{stateId:function(element){return $("#state").val();}},
        cache: false
    });

    /** Neighborhood */
    $("#neighborhood").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: 'name',
        minLength: 3,
        selectionRequired: true,
        valueProperty: 'id',
        url: path + '/admin/person/searchNeighborhood',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post',
        params:{cityId:function(element){return $("#city").val();}},
        cache: false
    });

    /** Street */
    $("#street").flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: 'name',
        minLength: 2,
        selectionRequired: true,
        valueProperty: 'id',
        url: path + '/admin/person/searchStreet',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post',
        cache: false
    });

    $("#street").on('select:flexdatalist', function (event, set, options) {
        $("#cmbStreetType").val(set.typeStreetId);
        $("#cmbStreetType").trigger('change');
    });


    $('#cmbAccessLevel').change(function(){
        var typeUserId = $(this).val();

        if(typeUserId == 2){
            $(".userView").removeClass('d-none');
            $(".attendantView").addClass('d-none');
        }else if(typeUserId == 0){
            $(".attendantView").addClass('d-none');
            $(".userView").addClass('d-none');
        }else{
            $(".attendantView").removeClass('d-none');
            $(".userView").addClass('d-none');
        }

        return false ;
    });

    $('#cmbCompany').change(function(){
        $.post(path+"/admin/person/ajaxDepartment",{companyId: $(this).val(), _token:$("#_token").val()},
            function(valor){
                $("#cmbDepartment").html(valor);
                $("#cmbDepartment").trigger("change");
            }
        );

        return false ;
    });
    
    /**
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/person/index');

    $("#btnCreatePerson").click(function(){

        if (!$("#create-person-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreatePerson").hasClass('disabled')){
            $("#btnCancel").addClass('disabled');
            $("#btnCreateperson").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

            /* if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "1";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else { */
                saveData(upname,'add');
            //}
        }
        
    });

    $("#btnUpdatePerson").click(function(){

        if (!$("#update-person-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdatePerson").hasClass('disabled')){
            $("#btnCancel").addClass('disabled');
            $("#btnUpdatePerson").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

            /* if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "2";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else { */
                saveData(upname,'upd');
            //}
        }

    });

    $("#btnChangePassword").click(function(){
        $('#modal-change-password').modal('show');       
    });

    $("#btnChangePassSave").click(function(){
        
        if (!$("#modal-change-password-form").valid()) {
            return false;
        }

        if(!$("#btnChangePassSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/person/changePassword',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    personId:$('#personId').val(),
                    newPassword:$('#modal-new-password').val(),
                    changePassFlag: ($('#modal-change-pass-check').is(':checked')) ? 1 : 0
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Lost_password_err'],'alert-modal-change-password');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_change_password'],'alert-modal-change-password');

                        setTimeout(function(){
                            $('#modal-change-password').modal('hide');
                            $('#modal-change-pass-check').iCheck('uncheck');
                            $('#modal-change-password-form').trigger('reset');
                        },2000);

                        $('#state').val(obj.stateId);
                    } else {
                        modalAlertMultiple('danger',vocab['Lost_password_err'],'alert-modal-change-password');
                    }
                },
                beforeSend: function(){
                    $("#btnChangePassSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnChangePassClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnChangePassSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnChangePassClose").removeClass('disabled');
                }
            });
        }

    });

    $("#btnAddLocation").click(function(){
        $('#modal-add-localization').modal('show');       
    });

    $("#btnAddLocalizationSave").click(function(){
        
        if (!$("#modal-add-localization-form").valid()) {
            return false;
        }

        if(!$("#btnAddLocalizationSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/person/insertLocation',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    localizationName:$('#modal-localization-name').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-localization');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_inserted'],'alert-modal-add-localization');

                        setTimeout(function(){
                            $('#modal-add-localization').modal('hide');
                            $('#modal-add-localization-form').trigger('reset');
                        },2000);

                        $('#state').val(obj.stateId);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-localization');
                    }
                },
                beforeSend: function(){
                    $("#btnAddLocalizationSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddStateClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddLocalizationSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddStateClose").removeClass('disabled');
                }
            });
        }

    });

    $("#btnAddState").click(function(){
        var countryId = $('#country').val(), alert = ($("#create-person-form").length > 0) ? 'alert-create-person' : 'alert-update-person';
        if(countryId.length <= 0 || countryId == ""){
            modalAlertMultiple('danger',vocab['select_country'],alert);
        }else{
            $('#countryId').val(countryId);
            $('#modal-country-txt').val($('#country-flexdatalist').val());
            $('#modal-add-state').modal('show');
        }        
    });

    $("#btnAddStateSave").click(function(){
        
        if (!$("#modal-add-state-form").valid()) {
            return false;
        }

        if(!$("#btnAddStateSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/person/insertState',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    countryId:$('#countryId').val(),
                    stateName:$('#modal-state-name').val(),
                    stateAbbreviation:$('#modal-state-abbrevation').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-state');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_inserted'],'alert-modal-add-state');

                        setTimeout(function(){
                            $('#modal-add-state').modal('hide');
                            $('#modal-add-state-form').trigger('reset');
                        },2000);

                        $('#state').val(obj.stateId);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-state');
                    }
                },
                beforeSend: function(){
                    $("#btnAddStateSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddStateClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddStateSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddStateClose").removeClass('disabled');
                }
            });
        }

    });

    $("#btnAddCity").click(function(){
        var stateId = $('#state').val(), alert = ($("#create-person-form").length > 0) ? 'alert-create-person' : 'alert-update-person';
        if(stateId.length <= 0 || stateId == ""){
            modalAlertMultiple('danger',vocab['select_country_state'],alert);
        }else{
            $('#stateId').val(stateId);
            $('#modal-country-city').val($('#country-flexdatalist').val());
            $('#modal-state-city').val($('#state-flexdatalist').val());
            $('#modal-add-city').modal('show');
        }        
    });

    $("#btnAddCitySave").click(function(){
        
        if (!$("#modal-add-city-form").valid()) {
            return false;
        }

        if(!$("#btnAddCitySave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/person/insertCity',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    stateId:$('#stateId').val(),
                    cityName:$('#modal-city-name').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-city');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_inserted'],'alert-modal-add-city');

                        setTimeout(function(){
                            $('#modal-add-city').modal('hide');
                            $('#modal-add-city-form').trigger('reset');
                        },2000);

                        $('#city').val(obj.cityId);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-city');
                    }
                },
                beforeSend: function(){
                    $("#btnAddCitySave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddCityClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddCitySave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddCityClose").removeClass('disabled');
                }
            });
        }

    });

    $("#btnAddNeighborhood").click(function(){
        var cityId = $('#city').val(), alert = ($("#create-person-form").length > 0) ? 'alert-create-person' : 'alert-update-person';
        if(cityId.length <= 0 || cityId == ""){
            modalAlertMultiple('danger',vocab['select_country_state_city'],alert);
        }else{
            $('#cityId').val(cityId);
            $('#modal-country-neighborhood').val($('#country-flexdatalist').val());
            $('#modal-state-neighborhood').val($('#state-flexdatalist').val());
            $('#modal-city-neighborhood').val($('#city-flexdatalist').val());
            $('#modal-add-neighborhood').modal('show');
        }        
    });

    $("#btnAddNeighborhoodSave").click(function(){
        
        if (!$("#modal-add-neighborhood-form").valid()) {
            return false;
        }

        if(!$("#btnAddNeighborhoodSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/person/insertNeighborhood',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    cityId:$('#cityId').val(),
                    neighborhoodName:$('#modal-neighborhood-name').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-neighborhood');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_inserted'],'alert-modal-add-neighborhood');

                        setTimeout(function(){
                            $('#modal-add-neighborhood').modal('hide');
                            $('#modal-add-neighborhood-form').trigger('reset');
                        },2000);

                        $('#neighborhood').val(obj.neighborhoodId);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-neighborhood');
                    }
                },
                beforeSend: function(){
                    $("#btnAddNeighborhoodSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddNeighborhoodClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddNeighborhoodSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddNeighborhoodClose").removeClass('disabled');
                }
            });
        }

    });

    $("#btnAddstreet").click(function(){
        var cityId = $('#city').val(), streetTypeId = $('#cmbStreetType').val(), alert = ($("#create-person-form").length > 0) ? 'alert-create-person' : 'alert-update-person';
        if(cityId.length <= 0 || cityId == ""){
            modalAlertMultiple('danger',vocab['select_country_state_city'],alert);
        }else if(streetTypeId <= 0 || streetTypeId == ""){
            modalAlertMultiple('danger',vocab['phl_select_street_type'],alert);
        }else{
            $('#cityId').val(cityId);
            $('#modal-country-street').val($('#country-flexdatalist').val());
            $('#modal-state-street').val($('#state-flexdatalist').val());
            $('#modal-city-street').val($('#city-flexdatalist').val());
            $("#modal-street-type").val(streetTypeId);
            $("#modal-street-type").trigger('change');
            $('#modal-add-street').modal('show');
        }        
    });

    $("#btnAddStreetSave").click(function(){
        
        if (!$("#modal-add-street-form").valid()) {
            return false;
        }

        if(!$("#btnAddStreetSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/person/insertStreet',
                dataType: 'json',
                data: {
                    _token:$('#_token').val(),
                    cityId:$('#cityId').val(),
                    streetTypeId:$('#modal-street-type').val(),
                    streetName:$('#modal-street-name').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-street');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success', vocab['Alert_inserted'],'alert-modal-add-street');

                        setTimeout(function(){
                            $('#modal-add-street').modal('hide');
                            $('#modal-add-street-form').trigger('reset');
                        },2000);

                        $("#cmbStreetType").val(obj.streetTypeId);
                        $("#cmbStreetType").trigger('change');
                        $('#street').val(obj.streetId);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-street');
                    }
                },
                beforeSend: function(){
                    $("#btnAddStreetSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddStreetClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddStreetSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddStreetClose").removeClass('disabled');
                }
            });
        }

    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if(btnClicked=="1"){
            saveData(upname,'add');
        }else if(btnClicked=="2"){
            saveData(upname,'upd');
        }      
    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];

            location.href = path + '/admin/person/index';
        }
    });

    /**
     * Validate
     */
    $("#create-person-form").validate({
        ignore:[],
        rules: {
            login:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:function(element){return $('input[name="natureType"]:checked').val() == 1;},
                remote:{
                    param:{
                        url: path+"/admin/person/checkLogin",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{  
                            _token:function(element){return $("#_token").val()},
                        }
                    },
                    depends:function(element){return $('input[name="natureType"]:checked').val() == 1;}
                }
            },
            password:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:function(element){return $('input[name="natureType"]:checked').val() == 1;}
            },
            confirmPassword:  {
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                equalTo: "#password"
            },
            personName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            },
            cmbAccessLevel:{required:function(element){return $('input[name="natureType"]:checked').val() == 1;}},
            cmbJuridicalType:{required:function(element){return $('input[name="natureType"]:checked').val() == 2;}},
            personEmail: {
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                email:true
            },
            defaultDepartment:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:function(element){return $('input[name="natureType"]:checked').val() == 2;}
            },
            cmbCompany:{required:function(element){return $('input[name="natureType"]:checked').val() == 1;}},
            cmbDepartment:{required:function(element){return $('input[name="natureType"]:checked').val() == 1;}},
            country:{required:function(element){return $('input[name="fillAddress"]').is(':checked');}},
            state:{required:function(element){return $('input[name="fillAddress"]').is(':checked');}},
            city:{required:function(element){return $('input[name="fillAddress"]').is(':checked');}},
            neighborhood:{required:function(element){return $('input[name="fillAddress"]').is(':checked');}},
            zipcode:{required:function(element){return $('input[name="fillAddress"]').is(':checked');}},
            street:{required:function(element){return $('input[name="fillAddress"]').is(':checked');}},
            "cmbGroup[]":{required:function(element){return ($('input[name="natureType"]:checked').val() == 1 && ($("#cmbAccessLevel").val() == 1 || $("#cmbAccessLevel").val() == 3));}},
            ssnCpf:{
                remote:{
                    param:{
                        url: path+"/admin/person/checkCpfCNPJ",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{
                            _token:function(element){return $("#_token").val()}
                        }
                    },
                    depends:function(element){return ($('input[name="natureType"]:checked').val() == 1 && $("#ssnCpf").val() != "");}
                }
            },
            einCnpj:{
                remote:{
                    param:{
                        url: path+"/admin/person/checkCpfCNPJ",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{
                            _token:function(element){return $("#_token").val()}
                        }
                    },
                    depends:function(element){return ($('input[name="natureType"]:checked').val() == 2 && $("#einCnpj").val() != "");}
                }
            }
        },
        messages: {
            login:{required:vocab['Alert_field_required']},
            password:{required:vocab['Alert_field_required']},
            confirmPassword:{equalTo: vocab['Alert_different_passwords']},
            personName:{required:vocab['Alert_field_required']},
            cmbAccessLevel:{required:vocab['Alert_field_required']},
            cmbJuridicalType:{required:vocab['Alert_field_required']},
            personEmail:{required:vocab['Alert_field_required'],email:vocab['Alert_invalid_email']} ,
            defaultDepartment:{required:vocab['Alert_field_required']},
            cmbCompany:{required:vocab['Alert_field_required']},
            cmbDepartment:{required:vocab['Alert_field_required']},
            country:{required:vocab['Alert_field_required']},
            state:{required:vocab['Alert_field_required']},
            city:{required:vocab['Alert_field_required']},
            neighborhood:{required:vocab['Alert_field_required']},
            zipcode:{required:vocab['Alert_field_required']},
            street:{required:vocab['Alert_field_required']},
            "cmbGroup[]":{required:vocab['Alert_field_required']}
        }
    });

    $("#update-person-form").validate({
        ignore:[],
        rules: {
            personName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            },
            cmbAccessLevel:{required:function(element){return $('#natureType').val() == 1;}},
            cmbJuridicalType:{required:function(element){return $('#natureType').val() == 2;}},
            personEmail: {
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true,
                email:true
            },
            cmbCompany:{required:function(element){return $('#natureType').val() == 1;}},
            cmbDepartment:{required:function(element){return $('#natureType').val() == 1;}},
            "cmbGroup[]":{required:function(element){return ($('#natureType').val() == 1 && ($("#cmbAccessLevel").val() == 1 || $("#cmbAccessLevel").val() == 3));}},
            ssnCpf:{
                remote:{
                    param:{
                        url: path+"/admin/person/checkCpfCNPJ",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{
                            _token:function(element){return $("#_token").val()},
                            personId:function(element){return $("#personId").val()}
                        }
                    },
                    depends:function(element){return ($('input[name="natureType"]:checked').val() == 1 && $("#ssnCpf").val() != "");}
                }
            },
            einCnpj:{
                remote:{
                    param:{
                        url: path+"/admin/person/checkCpfCNPJ",
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{
                            _token:function(element){return $("#_token").val()},
                            personId:function(element){return $("#personId").val()}
                        }
                    },
                    depends:function(element){return ($('input[name="natureType"]:checked').val() == 2 && $("#einCnpj").val() != "");}
                }
            }
        },
        messages: {
            personName:{required:vocab['Alert_field_required']},
            cmbAccessLevel:{required:vocab['Alert_field_required']},
            cmbJuridicalType:{required:vocab['Alert_field_required']},
            personEmail:{required:vocab['Alert_field_required'],email:vocab['Alert_invalid_email']} ,
            cmbCompany:{required:vocab['Alert_field_required']},
            cmbDepartment:{required:vocab['Alert_field_required']},
            "cmbGroup[]":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-change-password-form").validate({
        ignore:[],
        rules: {
            "modal-new-password":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            },
            "modal-confirm-new-pass":  {
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                equalTo: "#modal-new-password"
            }
        },
        messages: {
            "modal-new-password":{required:vocab['Alert_field_required']},
            "modal-confirm-new-pass":{equalTo: vocab['Alert_different_passwords']}
        }
    });

    $("#modal-add-localization-form").validate({
        ignore:[],
        rules: {
            "modal-localization-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            }
        },
        messages: {
            "modal-localization-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-state-form").validate({
        ignore:[],
        rules: {
            "modal-state-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            }
        },
        messages: {
            "modal-state-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-city-form").validate({
        ignore:[],
        rules: {
            "modal-city-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            }
        },
        messages: {
            "modal-city-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-neighborhood-form").validate({
        ignore:[],
        rules: {
            "modal-neighborhood-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            }
        },
        messages: {
            "modal-neighborhood-name":{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-add-street-form").validate({
        ignore:[],
        rules: {
            "modal-street-type":{required:true},
            "modal-street-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required:true
            }
        },
        messages: {
            "modal-street-type":{required:vocab['Alert_field_required']},
            "modal-street-name":{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-person-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/person/index";        
    });

    if($("#update-person-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/person/index" ;
        });
    }

    $("input[name='natureType']").on('ifClicked', function() { // bind a function to the change event
        var typeFlg = $(this).val();
        $('#btnCreatePerson').removeClass('d-none');

        if(typeFlg == '2'){ //if juridical person
            $('.juridicalView').removeClass('d-none');
            $('.commonView').removeClass('d-none');
            $('.naturalView').addClass('d-none');
            $(".userView").addClass('d-none');
            $(".attendantView").addClass('d-none');
        }else{
            $('.juridicalView').addClass('d-none');
            $('.commonView').removeClass('d-none');
            $('.naturalView').removeClass('d-none');
        }
    });

    $('#fillAddress').on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $('.addressView').removeClass('d-none');            
        }else{
            $('.addressView').addClass('d-none');
        }
    });

    $('.lbltooltip').tooltip();
});

function saveData(aAttachs,op)
{
    var method = op == 'add' ? 'createPerson' : 'updatePerson', 
        alert = op == 'add' ? 'alert-create-person' : 'alert-update-person',
        btn = op == 'add' ? 'btnCreatePerson' : 'btnUpdatePerson',
        formName = op == 'add' ? 'create-person-form' : 'update-person-form',
        data_save = $('#'+formName).serialize();
    
    // Add attachment's object to form serialized
    if(aAttachs.length > 0){
        data_save = data_save + "&attachments%5B%5D="+aAttachs;
    }

    $.ajax({
        type: "POST",
        url: path + '/admin/person/'+ method,
        dataType: 'json',
        data: data_save,
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],alert);
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success){
                if(op == 'add'){
                    $('#modal-person-code').val(obj.personId);
                    $('#modal-person-nature').val(obj.nature);
                    $('#modal-person-name').val(obj.personName);
                    $('#modal-person-login').val(obj.login);
                    $('#modal-person-access-level').val(obj.accessLevel);

                    if(obj.natureId == 1){
                        if($(".modal-natural-view").hasClass('d-none'))
                            $(".modal-natural-view").removeClass('d-none');
                    }else{
                        if(!$(".modal-natural-view").hasClass('d-none'))
                            $(".modal-natural-view").addClass('d-none');
                    }
    
                    $('#modal-person-create').modal('show');
                }else{
                    showAlert(vocab['Edit_sucess'],'success');
                }
                
            }else{
                modalAlertMultiple('danger',vocab['Alert_failure'],alert);
            }
        },
        beforeSend: function(){
            /*$("#btnCreateCity").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');*/
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#"+btn).html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
        }
    });

    return false ;

}
          