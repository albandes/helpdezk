//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0, filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0,
    htmlArea = '', showDefs  = '', global_coderequest, global_ticket, global_incharge, global_expiry_date,
    firstOption = "<option value=''></option>";

if($("#update-ticket-form").length <= 0){
    htmlArea = makeAreaCombo();
    showDefs = showDefaults();
}

/**
 * Combos
 */
var objTicket = {
    changeArea: function() {
        var areaID = $("#cmbArea").val();
        objTicket.emptyCombos('area');

        $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaID: areaID},
            function(valor){
                var attr = $("#cmbType").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#cmbType").removeAttr('disabled');
                
                if (showDefs == 'YES') {
                    $("#cmbType").html(valor);
                    $("#cmbType").trigger("change");
                    return objTicket.changeItem();
                } else if (!showDefs || showDefs == 'NO') {
                    $("#cmbType").html('<option value="X">'+vocab['Select']+'</option>' + valor);
                    $("#cmbType").val('X');
                    $("#cmbType").trigger("change");
                }
            });
    },
    changeItem: function(){
        var typeID = $("#cmbType").val();
        objTicket.emptyCombos('type');
        
        if(typeID != 'X'){
            $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeID: typeID},
            function(valor){
                var attr = $("#cmbItem").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#cmbItem").removeAttr('disabled');
                
                if (showDefs == 'YES') {
                    $("#cmbItem").html(valor);
                    $("#cmbItem").trigger("change");
                    return objTicket.changeService();
                } else if (!showDefs || showDefs == 'NO') {
                    $("#cmbItem").html('<option value="X">'+vocab['Select']+'</option>' + valor);
                    $("#cmbItem").val('X');
                    $("#cmbItem").trigger("change");
                }
            });
        }
    },
    changeService: function(){
        var itemID = $("#cmbItem").val();
        objTicket.emptyCombos('item');

        if(itemID != 'X'){
            $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemID: itemID},
            function(valor){
                var attr = $("#cmbService").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#cmbService").removeAttr('disabled');
                
                if (showDefs == 'YES') {
                    $("#cmbService").html(valor);
                    $("#cmbService").trigger("change");
                    return objTicket.changeReason();
                } else if (!showDefs || showDefs == 'NO') {
                    $("#cmbService").html('<option value="X">'+vocab['Select']+'</option>' + valor);
                    $("#cmbService").val('X');
                    $("#cmbService").trigger("change");
                }
            });
        }
    },
    changeReason: function(){
        var serviceID = $("#cmbService").val();
        objTicket.emptyCombos('service');

        if(serviceID != 'X'){
            $.post(path+"/helpdezk/hdkTicket/ajaxReasons",{serviceID: serviceID},
            function(valor){
                var attr = $("#cmbReason").attr('disabled');
                if(typeof attr !== 'undefined' && attr !== false)
                    $("#cmbReason").removeAttr('disabled');
                
                if (showDefs == 'YES') {
                    $("#cmbReason").html(valor);
                    $("#cmbReason").trigger("change");
                } else if (!showDefs || showDefs == 'NO') {
                    $("#cmbReason").html('<option value="X">'+vocab['Select']+'</option>' + valor);
                    $("#cmbReason").val('X');
                    $("#cmbReason").trigger("change");
                }
            });
        }
        
    },
    emptyCombos: function(type){
        switch(type){
            case 'area':
                $("#cmbType").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbType").trigger("change");
                $("#cmbItem").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbItem").trigger("change");                
                $("#cmbService").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbService").trigger("change");
                $("#cmbReason").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbReason").trigger("change");
                break;
            case 'type':
                $("#cmbItem").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbItem").trigger("change");
                $("#cmbService").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbService").trigger("change");
                $("#cmbReason").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbReason").trigger("change");
                break;
            case 'item':
                $("#cmbService").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbService").trigger("change");
                $("#cmbReason").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbReason").trigger("change");
                break;
            case 'service':
                $("#cmbReason").html('<option value="X">'+vocab['Select']+'</option>').attr('disabled','disabled');
                $("#cmbReason").trigger("change");
                break;

        }        
    },
    insertAuxAttendant: function(){
        if($("#cmbAttendants").val() != ""){
            $.post(path+"/helpdezk/hdkTicket/insertAuxiliaryAttendant",{
                _token: $("#_token").val(),
                ticketCode: $("#ticketCode").val(),
                attendantID: $("#cmbAttendants").val(),
                statusID: $("#statusID").val(), 
                ownerID: $("#ownerID").val(),
                flagNote: (typeUser == 3) ? 3 : 2
            },
            function(valor){
                var obj = jQuery.parseJSON(JSON.stringify(valor));
                $('#cmbAttendants').html(obj.auxAttendantsData.cmbAttendants);
                $("#cmbAttendants").trigger("change");
                $('#ticketNotesAdded').html(obj.notesAdded);

                if(obj.auxAttendantsData.auxAttendantList){
                    $("#auxAttendantList tbody").html(obj.auxAttendantsData.auxAttendantList);
                    if($("#auxiliaryAttendantLine").hasClass("d-none")){
                        $("#auxiliaryAttendantLine").removeClass("d-none");
                    }
                    $("#auxiliaryAttendantList").html(obj.auxAttendantsData.auxAttendantLine);
                }else{
                    if(!$("#auxiliaryAttendantLine").hasClass("d-none")){
                        $("#auxiliaryAttendantLine").addClass("d-none");
                    }
                }
            },'json');
        }
        
    },
    loadRepassList: function(){
        var repassType = $("input[name='repassType']:checked").val();

        $.post(path+"/helpdezk/hdkTicket/ajaxRepassList",{_token: $("#_token").val(),repassType: repassType},
            function(valor){
                $("#repassList").html(valor);
                $("#repassList").trigger("change");
                if(!$("#btnAbilities").hasClass("off")){
                    //objTicket.getAbilities();
                }
                else if(!$("#btnGroups").hasClass("off")){
                    //objTicket.getGroups();
                }
            });
    },
    getAbilities: function(){
        var repassType = $("input[name='repassType']:checked").val(),
            repassID = $("#repassList").val();

        $.post(path+"/helpdezk/hdkTicket/ajaxAbilitiesList", {_token: $("#_token").val(),repassType: repassType, repassID: repassID}, function(data){
            $("#titleAbilityGroup").html(vocab['Related_abilities']);

            if(data){
                $("#abilityGroupList").html(data);
            }else{
                $("#abilityGroupList").html('<li class="list-group-item">'+vocab['No_abilities']+'</li>');
            }
        });
    },
    getGroups: function(){
        var repassType = $("input[name='repassType']:checked").val(),
            repassID = $("#repassList").val();
        
        $("#abilityGroupList").html('');
        $("#titleAbilityGroup").html('');

        $.post(path+"/helpdezk/hdkTicket/ajaxGroupsList", {_token: $("#_token").val(),repassType: repassType, repassID: repassID}, function(data){
            if(repassType == "operator")
                $("#titleAbilityGroup").html(vocab['Operator_groups']);
            else
                $("#titleAbilityGroup").html(vocab['Group_operators']);

            if(data){
                $("#abilityGroupList").html(data);
            }else{
                $("#abilityGroupList").html('<li class="list-group-item">'+vocab['No_data']+'</li>');
            }
        });
    },
    reloadAttWay: function(){
        $.post(path+"/helpdezk/hdkTicket/ajaxAttWay",
            function(valor){
                $("#way").html(valor);
                $("#way").trigger("chosen:updated");
            })
    }
};

/**
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

$(document).ready(function () {
    countdown.start(timesession);

    /**
     * Select2
     */
    if($("#update-ticket-form").length > 0 && typeUser == 3){
        $('#cmbArea').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10}); 
        $('#cmbItem').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10}); 
        $('#cmbService').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbReason').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbPriority').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbAttendanceType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbHourType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbNoteVisibility').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#repassList').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('.modal-repass-body')});
        $('#cmbOpeGroups').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('.modal-repass-body')});
        $('#cmbAssumeGroups').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-assume-ticket-form')});
        $('#cmbAttendants').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-aux-attendant-form')});
        $('#cmbBoard').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-trello-card-form')});
        $('#cmbList').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-trello-card-form')});
    }else if($("#update-ticket-form").length <= 0){
        $('#cmbArea').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
        $('#cmbType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,disabled:true}); 
        $('#cmbItem').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,disabled:true}); 
        $('#cmbService').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,disabled:true});
        $('#cmbReason').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,disabled:true});

        if(typeUser == 3){
            $('#cmbUser').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
            $('#cmbSource').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
            $('#cmbAttendanceType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
            $('#repassList').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('.modal-repass-body')});
            $('#cmbOpeGroups').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('.modal-repass-body')});
        }
    }

    if($("#update-ticket-form").length <= 0){
        if (showDefs == 'YES') {
            $("#cmbArea").html(htmlArea);
            $("#cmbArea").trigger("change");
            objTicket.changeArea();
        } else if (showDefs == 'NO') {
            $("#cmbArea").html('<option value="X">'+vocab['Select']+'</option>' + htmlArea);
            $("#cmbArea").val('X');
            $("#cmbArea").trigger("change");
        }
    }

    $("#cmbArea").change(function(){
        objTicket.changeArea();
    });

    $("#cmbType").change(function(){
        objTicket.changeItem();
    });

    $("#cmbItem").change(function(){
        objTicket.changeService();
    });

    $("#cmbService").change(function(){
        objTicket.changeReason();
    });

    $("#cmbAttendants").change(function(){
        objTicket.insertAuxAttendant();
    });

    if($("#update-ticket-form").length <= 0){
        var myDropzone = new Dropzone("#attachments", {
            url: path + "/helpdezk/hdkTicket/saveTicketAttachments/",
            method: "post",
            dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>" + vocab['Tckt_drop_file'],
            createImageThumbnails: true,
            maxFiles: ticketAttMaxFiles,
            maxFilesize: hdkMaxSize,
            acceptedFiles: ticketAcceptedFiles,
            parallelUploads: ticketAttMaxFiles,                         // https://github.com/enyo/dropzone/issues/253
            autoProcessQueue: false,
            dictFileTooBig: vocab['hdk_exceed_max_file_size'],
            addRemoveLinks:true,
            dictRemoveFile: vocab['hdk_remove_file']
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
            var msg,typeMsg;

            if(errorname.length == 0 && (filesended == dropzonefiles)){
                if(btnClicked=="1"){
                    saveTicket(upname);
                }else if(btnClicked=="2"){
                    saveOpenRepassTicket(upname);
                }else if(btnClicked=="3"){
                    saveOpenFinishTicket(upname);
                }                            
            }else{
                var totalAttach = dropzonefiles - filesended;
                list = '<h4>'+vocab['files_not_attach_list']+'</h4><br>';
                errorname.forEach(element => {
                    list = list+element+'<br>';
                });
                list = list+'<br><strong>'+vocab['hdk_attach_after']+'</strong>';
                typeMsg = 'warning';
                msg = vocab['open_ticket_anyway_question'];
                showNextStep(list,msg,typeMsg,totalAttach,'modal-lg');
            }        
            
            dropzonefiles = 0; 
            filesended = 0;
            flgerror = 0;
        });

        $('#description').summernote(
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
                maxHeight: 254,   // set maximum height of editor
                height: 254,      // set editor height
                //width: 750,       // set editor width
                focus: false,     // set focus to editable area after initializing summernote
                placeholder:  vocab['Editor_Placeholder_description']
    
            }
        );

        if(typeUser == 3){
            $('#solution').summernote(
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
                    maxHeight: 254,   // set maximum height of editor
                    height: 254,      // set editor height
                    //width: 750,       // set editor width
                    focus: false,     // set focus to editable area after initializing summernote
                    placeholder:  vocab['Editor_Placeholder_solution']
        
                }
            );

            /**
             * Clockpicker
             */
            mdtimepicker('#ticketTime', { is24hour: true, theme: 'parracho' });

            /**
             * Count timer  - Stopwatch
             */
            if($('#hiddenTimerClock').val() == '1'){
                $('.timer').countimer();
                $("#btnAttendanceTimer > i").addClass('fa-spin');
            }else{
                $('.timer').countimer({
                    autoStart: false
                });
            }

            $("#btnAttendanceTimer").click(function(){
                var timerStatus = $('.timer').countimer('stopped');
                
                if(timerStatus[0]){
                    $('.timer').countimer('resume');

                    if(!$("#btnAttendanceTimer > i").hasClass('fa-spin'))
                        $("#btnAttendanceTimer > i").addClass('fa-spin');
                }else{
                    $('.timer').countimer('stop');

                    if($("#btnAttendanceTimer > i").hasClass('fa-spin'))
                        $("#btnAttendanceTimer > i").removeClass('fa-spin');
                }      
            });
        }

    }else{
        if($('#noteAttachments').length > 0 ) {
            var noteDropzone = new Dropzone("#noteAttachments", {
                url: path + "/helpdezk/hdkTicket/saveNoteAttachments/",
                method: "post",
                dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>" + vocab['Tckt_drop_file'],
                createImageThumbnails: true,
                maxFiles: noteAttMaxFiles,
                maxFilesize: hdkMaxSize,
                acceptedFiles: noteAcceptedFiles,
                parallelUploads: noteAttMaxFiles,                         // https://github.com/enyo/dropzone/issues/253
                autoProcessQueue: false,
                dictFileTooBig: vocab['hdk_exceed_max_file_size'],
                addRemoveLinks:true,
                dictRemoveFile: vocab['hdk_remove_file']
            });    
    
            noteDropzone.on("maxfilesexceeded", function(file) {
                this.removeFile(file);
            });
    
            noteDropzone.on("complete", function(file) {
            
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
        
            noteDropzone.on("queuecomplete", function (file) {
                var msg,typeMsg;
        
                if(errorname.length == 0 && (filesended == dropzonefiles)){
                    saveNote(upname,noteDropzone);            
                }else{
                    var totalAttach = dropzonefiles - filesended;
                    list = '<h4>'+vocab['files_not_attach_list']+'</h4><br>';
                    errorname.forEach(element => {
                        list = list+element+'<br>';
                    });
                    list = list+'<br><strong>'+vocab['hdk_attach_after']+'</strong>';
                    msg = '<br>'+vocab['save_note_anyway_question'];
                    typeMsg = 'warning';
                    showNextStep(list,msg,typeMsg,totalAttach,'modal-lg');
                }        
                
                dropzonefiles = 0; 
                filesended = 0;
                flgerror = 0;
            });
    
        }
    
        $('#button-reload').click(function() {
            location.reload();
        });

        $('#ticketNote').summernote(
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
                focus: false,     // set focus to editable area after initializing summernote
                placeholder:  vocab['Editor_Placeholder_insert']
    
            }
        );
        
        if($('#rejectReason').length > 0 ) {
            $('#rejectReason').summernote(
                {
                    toolbar:[
                        ["font",["bold","italic","underline","clear"]],
                        ["insert",["link"]]
                    ],
                    disableDragAndDrop: true,
                    minHeight: null,  // set minimum height of editor
                    maxHeight: 250,   // set maximum height of editor
                    height: 250,      // set editor height
                    focus: false,     // set focus to editable area after initializing summernote
                    placeholder:  vocab['Editor_Placeholder_reason']
        
                }
            );
        }
        
        if($('#changeDeadlineReason').length > 0 ) {
            $('#changeDeadlineReason').summernote(
                {
                    toolbar:[
                        ["font",["bold","italic","underline","clear"]],
                        ["insert",["link"]]
                    ],
                    disableDragAndDrop: true,
                    minHeight: null,  // set minimum height of editor
                    maxHeight: 250,   // set maximum height of editor
                    height: 250,      // set editor height
                    focus: false,     // set focus to editable area after initializing summernote
                    placeholder:  vocab['Editor_Placeholder_reason']
        
                }
            );
        }

        if($('#cardDescription').length > 0 ) {
            $('#cardDescription').summernote(
                {
                    toolbar:[
                        ["font",["bold","italic","underline","clear"]],
                        ["insert",["link"]]
                    ],
                    disableDragAndDrop: true,
                    minHeight: null,  // set minimum height of editor
                    maxHeight: 250,   // set maximum height of editor
                    height: 250,      // set editor height
                    focus: false,     // set focus to editable area after initializing summernote
                    placeholder:  vocab['Editor_Placeholder_card_description']
        
                }
            );
        }
        
        /**
         * Clockpicker
         */
         mdtimepicker('#newTime', { is24hour: true, theme: 'parracho' });

        /**
         * Mask
         */
        $('#executionStarted').mask("00:00:00");
        $('#executionFinished').mask("00:00:00");

        $('[data-toggle="tooltip"]').tooltip();
        
        $("#btnTimer").click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('#btnTimer i').removeClass('fa-spin');
                clearInterval(clock);
            }
            else{
                $(this).addClass('active');
                $('#btnTimer i').addClass('fa-spin');
                clock = setInterval(function() {
                    var currentTime = new Date();
                    var currentHours = currentTime.getHours();
                    var currentMinutes = currentTime.getMinutes();
                    var currentSeconds = currentTime.getSeconds();
                    currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
                    currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
                    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds;
    
                    $('#executionFinished').val(currentTimeString)
                    calctotal();
                }, 1000);
            }
        });
    
        $("#executionStarted").change(function(){
            calctotal();
        });

        $("#executionFinished").change(function(){
            calctotal();
        });
    }

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    // http://icheck.fronteed.com
    // https://stackoverflow.com/questions/20736315/icheck-check-if-checkbox-is-checked
    $('input[name="approve').on('ifChecked', function(event){
        var value = $(this).val() ;
        if(value == "A"){
            $('.questionsLine').removeClass('d-none');
            $('#approvalComments').addClass('d-none');
            $('#observation').prop('required',false);
        }else if(value == "N"){
            $('#approvalComments').removeClass('d-none');
            $('.questionsLine').addClass('d-none');
            $('input[name^="question-"]').prop('required',false);
        }else if(value == "O"){
            $('#approvalComments').removeClass('d-none');
            $('.questionsLine').removeClass('d-none');
            $('input[name^="question-"]').prop('required',false);
        }        
    });

    $("input[name='repassType']").on('ifChecked', function() { // bind a function to the change event
        objTicket.loadRepassList();
    });

    $("#repassList").change(function(){
        if(!$("#btnAbilities").hasClass("off")){
            objTicket.getAbilities();
        }
        else if(!$("#btnGroups").hasClass("off")){
            objTicket.getGroups();
        }
    });

    // show group, attendant or partner abilities
    $("#btnAbilities").click(function(){
        var repassList = $("#repassList").val();

        if($(this).hasClass("off")){
            $("#abilityGroupList").html('');
            $(this).removeClass("btn-white off").addClass("btn-default");
            $("#btnGroups").removeClass("btn-default").addClass("btn-white off");
            if(repassList){
                objTicket.getAbilities();
            }else{
                $("#abilityGroupList").html('<li class="list-group-item">'+vocab['Select_group_operator']+'</li>');
            }
        }

    });

    // show group attendants or attendant/partner groups
    $("#btnGroups").click(function(){
        var repassList = $("#repassList").val();

        if($(this).hasClass("off")){
            $("#abilityGroupList").html('');
            $(this).removeClass("btn-white off").addClass("btn-default");
            $("#btnAbilities").removeClass("btn-default").addClass("btn-white off");
            if(repassList){
                objTicket.getGroups();
            }else{
                $("#abilityGroupList").html('<li class="list-group-item">'+vocab['Select_group_operator']+'</li>');
            }
        }
    });

    $("#groupKeepView").on('ifChecked', function() { // bind a function to the change event
        $("#assumeGroupsList").removeClass('d-none');
    }).on('ifUnchecked', function() { // bind a function to the change event
        $("#assumeGroupsList").addClass('d-none');
    });

    $("input[name='trackOptions']").on('ifClicked', function() { // bind a function to the change event
        if($(this).val() == 'G'){
            $("#OpeGroupsList").removeClass('d-none');
        }else{
            $("#OpeGroupsList").addClass('d-none');
        }
    });
   
    /**
     * Buttons
     */    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkTicket/index');
    

    $("#btnCreateTicket").click(function(){

        if (!$("#create-ticket-form").valid()) {
            return false ;
        }
        
        if ($('#description').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['APP_requireDescription'],'alert-create-ticket');
            return false;
        }
        
        if(!$("#btnCreateTicket").hasClass('disabled')){
            $("#btnCancel").addClass('disabled');
            $("#btnCreateTicket").html('<i class="fa fa-spinner fa-spin"></i> '+ vocab['Processing']).addClass('disabled');
            if(typeUser == 3){
                $("#btnRepassTicket").addClass('disabled');
                $("#btnFinishTicket").addClass('disabled');
            }
            btnClicked = "1";

            if(myDropzone.getQueuedFiles().length > 0){
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            }else{
                saveTicket(upname);
            }
        }
        
    });

    $("#btnRepassTicket").click(function(){

        if (!$("#create-ticket-form").valid()) {
            return false ;
        }

        if ($('#description').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['APP_requireDescription'],'alert-create-ticket');
            return false;
        }

        objTicket.loadRepassList();
        $('#modal-repass-ticket').modal('show');
        return false;
        
    });

    $("#btnFinishTicket").click(function(){

        if (!$("#create-ticket-form").valid()) {
            return false ;
        }
        
        if ($('#description').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['APP_requireDescription'],'alert-create-ticket');
            return false;
        }
        
        if(!$("#btnFinishTicket").hasClass('disabled')){
            $("#btnCancel").addClass('disabled');
            $("#btnFinishTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
            if(typeUser == 3){
                $("#btnRepassTicket").addClass('disabled');
                $("#btnCreateTicket").addClass('disabled');
            }
            btnClicked = "3";

            if(myDropzone.getQueuedFiles().length > 0){
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            }else{
                saveOpenFinishTicket(upname);
            }
        }
        
    });

    $("#btnCancelTicket").click(function(){
        $('#modal-cancel-ticket').modal('show');
    });

    $("#btnCancelTicketYes").click(function(){
        if(!$("#btnCancelTicketYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/cancelTicket',
                dataType: 'json',
                async: false,
                data: {
                    _token:     $("#_token").val(),
                    ticketCode: $("#ticketCode").val()
                },
                error: function (ret) {
                    if($("#cancelMessageLine").hasClass('alert-warning')){
                        $("#cancelMessageLine").removeClass('alert-warning').addClass('alert-danger');
                    }
                    $("#messageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                    $("#cancelTicketText").html(vocab['Alert_cancel_ticket_failure']);
                    
                    setTimeout(function(){
                        $('#modal-cancel-ticket').modal('hide');
                        if($("#cancelMessageLine").hasClass('alert-danger')){
                            $("#cancelMessageLine").removeClass('alert-danger').addClass('alert-warning');
                        }else if($("#cancelMessageLine").hasClass('alert-success')){
                            $("#cancelMessageLine").removeClass('alert-success').addClass('alert-warning');
                        }
                        $("#messageIcon").html('<i class="fa fa-question-circle fa-3x"></i>');
                        $("#cancelTicketText").html(vocab['Tckt_cancel_request']);                        
                    },2000);
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success) {
                        if($("#cancelMessageLine").hasClass('alert-warning')){
                            $("#cancelMessageLine").removeClass('alert-warning').addClass('alert-success');
                        }else if($("#cancelMessageLine").hasClass('alert-danger')){
                            $("#cancelMessageLine").removeClass('alert-danger').addClass('alert-success');
                        }

                        $("#messageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#cancelTicketText").html(vocab['Alert_Cancel_sucess']);
                        setTimeout(function(){
                            $('#modal-cancel-ticket').modal('hide');
                            location.reload();
                        },2000);
                    } else {
                        if($("#cancelMessageLine").hasClass('alert-warning')){
                            $("#cancelMessageLine").removeClass('alert-warning').addClass('alert-danger');
                        }
                        $("#messageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#cancelTicketText").html(vocab['Alert_cancel_ticket_failure']);
                    }
                },
                beforeSend: function(){
                    $("#btnCancelTicketNo").addClass('disabled');
                    $("#btnCancelTicketYes").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');                    
                },
                complete: function(){
                    $("#btnCancelTicketNo").removeClass('disabled');
                    $("#btnCancelTicketYes").html(vocab['Yes']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnEvaluate").click(function(){
        $('#modal-evaluate-ticket').modal('show');
    });

    $("#btnEvaluateSave").click(function(){
        if(!$("#btnEvaluateSave").hasClass('disabled')){
            // jquery lives out textarea in serialize, so I add extra data
            var data = $('#modal-evaluate-form').serialize() + "&_token=" + $("#_token").val() + "&ticketCode=" + $("#ticketCode").val() + "&observation=" + $("#observation").val();

            $("input[name='radioName']:checked").val()

            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkTicket/evaluateTicket",
                data: data,
                dataType: 'json',
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-evaluate-ticket');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('info',vocab['Tckt_evaluated_success'],'alert-evaluate-ticket');
                        $("#btnEvaluateSave").addClass('d-none');
                        setTimeout(function(){
                            $('#modal-evaluate-ticket').modal('hide');
                            location.href = path + "/helpdezk/hdkTicket/viewTicket/"+$("#ticketCode").val();
                        },3000);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-evaluate-ticket');
                    }
                },
                beforeSend: function(){
                    $("#btnEvaluateClose").addClass('disabled');
                    $("#btnEvaluateSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnEvaluateClose").removeClass('disabled');
                    $("#btnEvaluateSave").html("<i class='fa fa-save'></i> "+vocab['Save']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnReopen").click(function(){
        $('#modal-reopen-ticket').modal('show');
    });

    $("#btnReopenTicketYes").click(function(){
        if(!$("#btnReopenTicketYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/reopenTicket',
                dataType: 'json',
                async: false,
                data: {
                    _token:     $("#_token").val(),
                    ticketCode: $("#ticketCode").val()
                },
                error: function (ret) {
                    if($("#reopenMessageLine").hasClass('alert-warning')){
                        $("#reopenMessageLine").removeClass('alert-warning').addClass('alert-danger');
                    }
                    $("#messageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                    $("#reopenTicketText").html(vocab['Alert_reopen_ticket_failure']);
                    
                    setTimeout(function(){
                        $('#modal-reopen-ticket').modal('hide');
                        if($("#reopenMessageLine").hasClass('alert-danger')){
                            $("#reopenMessageLine").removeClass('alert-danger').addClass('alert-warning');
                        }else if($("#reopenMessageLine").hasClass('alert-success')){
                            $("#reopenMessageLine").removeClass('alert-success').addClass('alert-warning');
                        }
                        $("#messageIcon").html('<i class="fa fa-question-circle fa-3x"></i>');
                        $("#reopenTicketText").html(vocab['Tckt_reopen_request']);                        
                    },2000);
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success) {
                        if($("#reopenMessageLine").hasClass('alert-warning')){
                            $("#reopenMessageLine").removeClass('alert-warning').addClass('alert-success');
                        }else if($("#reopenMessageLine").hasClass('alert-danger')){
                            $("#reopenMessageLine").removeClass('alert-danger').addClass('alert-success');
                        }

                        $("#messageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#reopenTicketText").html(vocab['Alert_reopen_sucess']);
                        setTimeout(function(){
                            $('#modal-reopen-ticket').modal('hide');
                            location.reload();
                        },2000);
                    } else {
                        if($("#reopenMessageLine").hasClass('alert-warning')){
                            $("#reopenMessageLine").removeClass('alert-warning').addClass('alert-danger');
                        }
                        $("#messageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#reopenTicketText").html(vocab['Alert_reopen_ticket_failure']);
                    }
                },
                beforeSend: function(){
                    $("#ReopenTicketNo").addClass('disabled');
                    $("#btnReopenTicketYes").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');                    
                },
                complete: function(){
                    $("#ReopenTicketNo").removeClass('disabled');
                    $("#btnReopenTicketYes").html(vocab['Yes']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnChangeDeadline").click(function(){
        $('#modal-change-deadline').modal('show');
    });

    $("#btnChangeDeadlineSave").click(function(){

        if (!$("#modal-change-deadline-form").valid()) {
            return false ;
        }
        
        if ($('#changeDeadlineReason').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['Alert_empty_reason'],'alert-change-deadline');
            return false;
        }
        
        if(!$("#btnChangeDeadlineSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/changeDeadline',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    newDeadlineDate: $("#newDate").val(),
                    newDeadlineTime: $("#newTime").val(),
                    reason: $('#changeDeadlineReason').summernote('code'),
                    deadlineExtensionNumber: $("#deadlineExtensionNumber").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-change-deadline');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Expire_date_sucess'],'alert-change-deadline');
                        setTimeout(function(){
                            $('#attendanceDeadline').val(obj.newDeadline);
                            
                            $('#modal-change-deadline').modal('hide');
                            $('#modal-change-deadline-form').trigger('reset');
                            $('#changeDeadlineReason').summernote('reset');
                            $('#currentDate').val(obj.newDeadlineDate);
                            $('#currentTime').val(obj.newDeadlineTime);
                            $('#newTime').val('');

                        },2000);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-change-deadline');
                    }
    
                },
                beforeSend: function(){
                    $("#btnChangeDeadlineClose").addClass('disabled');
                    $("#btnChangeDeadlineSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnChangeDeadlineClose").removeClass('disabled');
                    $("#btnChangeDeadlineSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
    
            });
        }
        
    });

    $("#btnSaveChanges").click(function(){

        if (!$("#update-ticket-form").valid()) {
            return false ;
        }
        
        if(!$("#btnSaveChanges").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/saveTicketChanges',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    typeID: $("#cmbType").val(),
                    itemID: $("#cmbItem").val(),
                    serviceID: $("#cmbService").val(),
                    reasonID: $('#cmbReason').val(),
                    priorityID: $("#cmbPriority").val(),
                    attendanceTypeID: $("#cmbAttendanceType").val()
                },
                error: function (ret) {
                    showAlert(vocab['Alert_failure'],'danger');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(vocab['Save_changes_sucess'],'success');
                        /*modalAlertMultiple('success',vocab['Expire_date_sucess'],'alert-change-deadline');
                        setTimeout(function(){
                            $('#attendanceDeadline').val(obj.newDeadline);
                            
                            $('#modal-change-deadline').modal('hide');
                            $('#modal-change-deadline-form').trigger('reset');
                            $('#changeDeadlineReason').summernote('reset');
                            $('#currentDate').val(obj.newDeadlineDate);
                            $('#currentTime').val(obj.newDeadlineTime);
                            $('#newTime').val('');

                        },2000);*/
                    } else {
                        showAlert(vocab['Alert_failure'],'danger');
                    }
    
                },
                beforeSend: function(){
                    $("#btnSaveChanges").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveChanges").html("<i class='fa fa-save'></i> "+ vocab['btn_save_changes']).removeClass('disabled');
                }
    
            });
        }
        
    });

    $("#btnAssume").click(function(){
        $('#modal-assume-ticket').modal('show');
    });

    $("#btnAssumeSave").click(function(){
        if ($("#groupKeepView").is(":checked")) var groupKeepView = '1';
        else var groupKeepView = '0';

        if(!$("#btnAssumeSave").hasClass("disabled")){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/assumeTicket',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    groupKeepView: groupKeepView,
                    inChargeID: $("#inChargeID").val(),
                    inChargeType: $("#inChargeType").val(),
                    groupAssumeID: $("#cmbAssumeGroups").val(),
                    ticketEntryDate: $("#openingDate").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-assume-form');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Assumed_successfully'],'alert-assume-form');
                        setTimeout(function(){
                            $('#modal-assume-ticket').modal('hide');
                            location.reload();
                        },2000);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-assume-form');
                    }
    
                },
                beforeSend: function(){
                    $("#btnAssumeClose").addClass('disabled');
                    $("#btnAssumeSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAssumeClose").removeClass('disabled');
                    $("#btnAssumeSave").html("<i class='fa fa-clipboard-check'></i> "+vocab['btn_assume']).removeClass('disabled');
                }
    
            });
        }

    });

    $("#btnRepass").click(function(){
        objTicket.loadRepassList();
        $('#modal-repass-ticket').modal('show');
    });

    $("#btnRepassSave").click(function(){
        if (!$("#modal-repass-ticket-form").valid()) {
            return false ;
        }

        if(typeof($('input:radio[name=trackOptions]:checked').val()) == "undefined"){
            modalAlertMultiple('danger',vocab['Alert_follow_repass'],'alert-repass-ticket');
            return false;
        }

        if(!$("#btnRepassSave").hasClass("disabled")){
            if($("#update-ticket-form").length > 0 && typeUser == 3){
                saveRepassTicket();
            }else{
                // open ticket with repass
                if(!$("#btnRepassTicket").hasClass('disabled')){

                    $("#btnCancel").addClass('disabled');
                    $("#btnRepassTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCreateTicket").addClass('disabled');
                    $("#btnFinishTicket").addClass('disabled');
                    btnClicked = "2";
        
                    if(myDropzone.getQueuedFiles().length > 0){
                        dropzonefiles = myDropzone.getQueuedFiles().length;
                        myDropzone.processQueue();
                    }else{
                        saveOpenRepassTicket(upname);
                    }
                }
            }
        } 

    });

    $("#btnAuxAttendant").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/modalAuxAttendant",
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                ticketCode: $("#ticketCode").val()
            },
            error: function (ret) {
                showAlert(vocab['Alert_failure'],'danger');
            },
            success: function(ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $('#cmbAttendants').html(obj.cmbAttendants);
                    $("#cmbAttendants").trigger("change");

                    if(obj.auxAttendantList){
                        $("#auxAttendantList tbody").html(obj.auxAttendantList);
                    }

                    $('#modal-add-aux-attendant').modal('show');
                    
                } else {
                    showAlert(vocab['Alert_failure'],'danger');
                }
            }
        });        
    });

    $("#btnCloseTicket").click(function(){
        $('#modal-close-ticket').modal('show');
    });

    $("#btnCloseTicketYes").click(function(){
        if(!$("#btnCloseTicketYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/closeTicket',
                dataType: 'json',
                async: false,
                data: {
                    _token:     $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    ticketEntryDate: $("#openingDate").val()
                },
                error: function (ret) {
                    if($("#closeMessageLine").hasClass('alert-warning')){
                        $("#closeMessageLine").removeClass('alert-warning').addClass('alert-danger');
                    }
                    $("#closeMessageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                    $("#closeTicketText").html(vocab['Alert_close_ticket_failure']);
                    
                    setTimeout(function(){
                        $('#modal-cancel-ticket').modal('hide');
                        if($("#closeMessageLine").hasClass('alert-danger')){
                            $("#closeMessageLine").removeClass('alert-danger').addClass('alert-warning');
                        }else if($("#closeMessageLine").hasClass('alert-success')){
                            $("#closeMessageLine").removeClass('alert-success').addClass('alert-warning');
                        }
                        $("#closeMessageIcon").html('<i class="fa fa-question-circle fa-3x"></i>');
                        $("#closeTicketText").html(vocab['Confirm_close']);                        
                    },2000);
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success) {
                        if($("#closeMessageLine").hasClass('alert-warning')){
                            $("#closeMessageLine").removeClass('alert-warning').addClass('alert-success');
                        }else if($("#closeMessageLine").hasClass('alert-danger')){
                            $("#closeMessageLine").removeClass('alert-danger').addClass('alert-success');
                        }

                        $("#closeMessageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#closeTicketText").html(vocab['Alert_close_request']);
                        setTimeout(function(){
                            $('#modal-cancel-ticket').modal('hide');
                            location.reload();
                        },2000);
                    } else {
                        if($("#closeMessageLine").hasClass('alert-warning')){
                            $("#closeMessageLine").removeClass('alert-warning').addClass('alert-danger');
                        }
                        $("#closeMessageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#closeTicketText").html(vocab['Alert_close_ticket_failure']);
                    }
                },
                beforeSend: function(){
                    $("#btnCloseTicketNo").addClass('disabled');
                    $("#btnCloseTicketYes").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');                    
                },
                complete: function(){
                    $("#btnCloseTicketNo").removeClass('disabled');
                    $("#btnCloseTicketYes").html(vocab['Yes']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnReject").click(function(){
        $('#modal-reject-ticket').modal('show');
    });

    $("#btnRejectTicketSave").click(function(){

        if ($('#rejectReason').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['Alert_empty_reason'],'alert-reject-ticket');
            return false;
        }
        
        if(!$("#btnRejectTicketSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/rejectTicket',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    inChargeID: $("#inChargeID").val(),
                    inChargeType: $("#inChargeType").val(),
                    rejectReason: $('#rejectReason').summernote('code')
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-reject-ticket');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Reject_sucess'],'alert-reject-ticket');
                        setTimeout(function(){
                            $('#modal-change-deadline').modal('hide');
                            location.href = path+"/helpdezk/hdkTicket/index";
                        },2000);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-reject-ticket');
                    }    
                },
                beforeSend: function(){
                    $("#btnRejectTicketClose").addClass('disabled');
                    $("#btnRejectTicketSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnRejectTicketClose").removeClass('disabled');
                    $("#btnRejectTicketSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
    
            });
        }
        
    });

    /**
     *  Trello integration buttons
     */
    $("#btnTrello").click(function(){
        getTrelloBoards()
        $('#modal-trello-card').modal('show');
    });

    $("#cmbBoard").change(function(){
        if($(this).val() != ""){
            getTrelloList($(this).val());
        }
    });

    $("#cmbList").change(function(){
        if($(this).val() != ""){
            getTrelloCard($(this).val());
        }
    });

    $("#btnAddCard").click(function(){
        $("#card-list-row").addClass('d-none');
        $(".add-card").removeClass('d-none');
        $("#btnTrelloSave").removeClass('d-none');
    });

    $("#btnTrelloSave").click(function(){
        if (!$("#modal-trello-card-form").valid()) {
            return false ;
        }

        if ($('#cardDescription').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['Alert_empty_reason'],'alert-trello-card');
            return false;
        }
        
        if(!$("#btnTrelloSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTrello/createCard',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    boardId: $("#cmbBoard").val(),
                    listId: $("#cmbList").val(),
                    cardTitle: $("#cardTitle").val(),
                    cardDescription:$('#cardDescription').summernote('code')
                },
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-trello-card');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['trello_card_added_successfully'],'alert-trello-card');
                        setTimeout(function(){
                            $('#modal-trello-card').modal('hide');
                            location.href = path+"/helpdezk/hdkTicket/index";
                        },2000);
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-trello-card');
                    }    
                },
                beforeSend: function(){
                    $("#btnTrelloClose").addClass('disabled');
                    $("#btnTrelloSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnTrelloClose").removeClass('disabled');
                    $("#btnTrelloSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
    
            });
        }
    });

    $("#btnPrint").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/makeReport",
            data: { 
                _token:     $("#_token").val(),
                ticketCode: $("#ticketCode").val()
            },
            error: function (ret) {
                showAlert(vocab['Alert_failure'],'danger');
            },
            success: function(fileName) {
                console.log(fileName);
                if(fileName){
                    /**
                     * I had to make changes to open the file in a new window
                     * because I could not use the jquery.download with the .pdf extension
                     */
                    if (fileName.indexOf(".pdf") >= 0) {
                        window.open(fileName, '_blank');
                    } else {
                        $.fileDownload(fileName );

                    }

                }
                else {
                }
            }
        });
        return false;
    });

    // https://stackoverflow.com/questions/31519812/what-about-dropzone-js-within-an-existing-form-submitted-by-ajax
    $('#btnSendNote').click(function(e) {
        e.preventDefault();

        if(!$("#btnSendNote").hasClass('disabled')){
            if(typeUser == 3){
                if (requireTaskTime == 1 && ( $("#totalMinutes").val() == 0 || $("#totalMinutes").val() == '')) {
                    modalAlertMultiple('danger',vocab['Obrigatory_time'],'alert-note-add');
                    return false;
                }
            }

            if (allowEmptyNote == 0 && $('#ticketNote').summernote('isEmpty')) {
                modalAlertMultiple('danger',vocab['Alert_empty_note'],'alert-note-add');
                return false;
            }


            $("#btnSendNote").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
            if (noteDropzone.getQueuedFiles().length > 0) {
                dropzonefiles = noteDropzone.getQueuedFiles().length;
                noteDropzone.processQueue();
            } else {
                saveNote(upname,noteDropzone);
            }            
        }

        return false;  // <- cancel event
    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if($("#update-ticket-form").length <= 0){
            if(btnClicked=="1"){
                saveTicket(upname,myDropzone);
            }else if(btnClicked=="2"){
                saveRepassTicket(upname,myDropzone);
            }else if(btnClicked=="3"){
                saveFinishTicket(upname,myDropzone);
            }
        }else{
            saveNote(upname,noteDropzone);
        }
    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];

            location.href = path + "/helpdezk/hdkTicket/index";
        }
    });

    $("#btnTicketTime").click(function(){
        mdtimepicker('#ticketTime', 'show');
    });

    $("#btnNewDeadlineTime").click(function(){
        mdtimepicker('#newTime', 'show');
    });

    /**
     * Validate
     */
    $("#create-ticket-form").validate({
        ignore:['.note-editor'],
        rules: {
            cmbArea:{required:true,number:true},
            cmbType:{required:true,number:true},
            cmbItem:{required:true,number:true},
            cmbService:{required:true,number:true},
            subject:{
                required:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                }
            }
        },
        messages: {            
            cmbArea:{required:vocab['Alert_field_required'],number:vocab['Select_area']},
            cmbType:{required:vocab['Alert_field_required'],number:vocab['select_type']},
            cmbItem:{required:vocab['Alert_field_required'],number:vocab['select_item']},
            cmbService:{required:vocab['Alert_field_required'],number:vocab['select_service']},
            subject:{required:vocab['Alert_field_required']}
        }
    });

    $("#update-ticket-form").validate({
        ignore:[],
        rules: {
            cmbArea:{required:true,number:true},
            cmbType:{required:true,number:true},
            cmbItem:{required:true,number:true},
            cmbService:{required:true,number:true},
            cmbPriority:{required:true,number:true}
        },
        messages: {            
            cmbArea:{required:vocab['Alert_field_required'],number:vocab['Select_area']},
            cmbType:{required:vocab['Alert_field_required'],number:vocab['select_type']},
            cmbItem:{required:vocab['Alert_field_required'],number:vocab['select_item']},
            cmbService:{required:vocab['Alert_field_required'],number:vocab['select_service']},
            cmbPriority:{required:vocab['Alert_field_required'],number:vocab['select_service']}
        }
    });

    $("#modal-change-deadline-form").validate({
        ignore:[],
        rules: {
            newDate:{required:true},
            newTime:{required:true}
        },
        messages: {            
            newDate:{required:vocab['Alert_field_required']},
            newTime:{required:vocab['Alert_field_required']}
        }
    });

    $("#modal-repass-ticket-form").validate({
        ignore:[],
        rules: {
            repassType: "required",
            repassList: "required"
        },
        messages: {
            repassType: vocab['Alert_field_required'],
            repassList: vocab['Alert_field_required']
        }

    });

    $("#modal-trello-card-form").validate({
        ignore:[],
        rules: {
            cardTitle:{
                required:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                minlength: 5
            }
        },
        messages: {            
            cardTitle:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_five_characters']}
        }
    });

    $('form').each(function(){
        if($(this).data('validator'))
            $(this).data('validator').settings.ignore = ".note-editor *";
    });

    /* when the modal is hidden */
    $('#modal-ticket-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkTicket/index";        
    });

    if($("#update-ticket-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.reload();
        });
    }
});

function makeAreaCombo()
{
    var result="";
    $.ajax({
        url: path+"/helpdezk/hdkTicket/ajaxArea" ,
        type: "POST",
        async: false,
        success: function(data) {
            result = data;
        }
    });
    return result;
}

function showDefaults()
{
    var result="";
    $.ajax({
        url: path+"/helpdezk/hdkTicket/showDefaults" ,
        type: "POST",
        async: false,
        success: function(data) {
            result = data;
        }
    });
    return result;
}

function saveTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false;

    if(typeUser == 3){
        var periods =  $('#attendanceTime').val().split(":"), 
            openTime = (parseInt(periods[0])*60) + (parseFloat(periods[1])) + (parseFloat(periods[2])/60);
            ticketData = {
                _token:             $("#_token").val(),
                ownerID:            $("#cmbUser").val(),
                sourceID:           $("#cmbSource").val(),
                ticketDate:         $("#ticketDate").val(),
                ticketTime:         $("#ticketTime").val(),
                serialNumber: 	    $('#equipmentSerialNumber').val(),
                osNumber: 		    $('#equipmentOsNumber').val(),
                tag: 			    $('#equipmentTag').val(),
                area: 			    $('#cmbArea').val(),
                type: 			    $('#cmbType').val(),
                item:			    $('#cmbItem').val(),
                service:		    $('#cmbService').val(),
                reason:			    $('#cmbReason').val(),
                subject: 		    $('#subject').val(),
                description: 	    $('#description').summernote('code'),
                solution: 	        $('#solution').summernote('code'),
                attendanceTypeID:   $('#cmbAttendanceType').val(),
                openTime:           openTime,
                attachments:        aAttachs
            };

    }else{
        var ticketData = {
            _token:         $("#_token").val(),
            serialNumber: 	$('#equipmentSerialNumber').val(),
            osNumber: 		$('#equipmentOsNumber').val(),
            tag: 			$('#equipmentTag').val(),
            area: 			$('#cmbArea').val(),
            type: 			$('#cmbType').val(),
            item:			$('#cmbItem').val(),
            service:		$('#cmbService').val(),
            reason:			$('#cmbReason').val(),
            subject: 		$('#subject').val(),
            description: 	$('#description').summernote('code'),
            attachments:        aAttachs
        };
    }

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/saveTicket',
        dataType: 'json',
        async: false,
        data: ticketData,
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-ticket');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.ticketCode)) {
                var ticket = obj.ticketCode;
                global_coderequest = ticket;
                global_ticket = ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6);
                global_incharge = obj.inChargeName;
                global_expiry_date = obj.expiryDate;

                $('#modal-request-code').val(global_ticket);
                $('#modal-incharge-name').val(global_incharge);
                $('#modal-expiry-date').val(global_expiry_date);
                $('#modal-ticket-create').modal('show');
                
                errorname = [];
                upname = [];
            } else {
                modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-ticket');
            }
        },
        beforeSend: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnCreateTicket").html("<span class='fa fa-save'></span>  " + vocab['Save']).removeClass('disabled');
            if(typeUser == 3){
                $("#btnRepassTicket").removeClass('disabled');
                $("#btnFinishTicket").removeClass('disabled');
            }            
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnCreateTicket").html("<span class='fa fa-save'></span>  " + vocab['Save']).removeClass('disabled');
            if(typeUser == 3){
                $("#btnRepassTicket").removeClass('disabled');
                $("#btnFinishTicket").removeClass('disabled');
            }
        }
    });

    return false ;
}

function saveOpenRepassTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false,
        periods =  $('#attendanceTime').val().split(":"), 
        openTime = (parseInt(periods[0])*60) + (parseFloat(periods[1])) + (parseFloat(periods[2])/60),
        trackType = $('input:radio[name=trackOptions]:checked').val(),
        trackGroupID;

    trackGroupID = (trackType == "G") ? $("#cmbOpeGroups").val() : 0;

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/saveOpenRepassTicket',
        dataType: 'json',
        async: false,
        data: {
            _token:             $("#_token").val(),
            ownerID:            $("#cmbUser").val(),
            sourceID:           $("#cmbSource").val(),
            ticketDate:         $("#ticketDate").val(),
            ticketTime:         $("#ticketTime").val(),
            serialNumber: 	    $('#equipmentSerialNumber').val(),
            osNumber: 		    $('#equipmentOsNumber').val(),
            tag: 			    $('#equipmentTag').val(),
            area: 			    $('#cmbArea').val(),
            type: 			    $('#cmbType').val(),
            item:			    $('#cmbItem').val(),
            service:		    $('#cmbService').val(),
            reason:			    $('#cmbReason').val(),
            subject: 		    $('#subject').val(),
            description: 	    $('#description').summernote('code'),
            solution: 	        $('#solution').summernote('code'),
            attendanceTypeID:   $('#cmbAttendanceType').val(),
            openTime:           openTime,
            repassType: $('input:radio[name=repassType]:checked').val(),
            repassID: $("#repassList").val(),
            trackType: trackType,
            trackGroupID: trackGroupID,
            attachments:        aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-ticket');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.ticketCode)) {
                var ticket = obj.ticketCode;
                global_coderequest = ticket;
                global_ticket = ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6);
                global_incharge = obj.inChargeName;
                global_expiry_date = obj.expiryDate;

                $('#modal-request-code').val(global_ticket);
                $('#modal-incharge-name').val(global_incharge);
                $('#modal-expiry-date').val(global_expiry_date);
                $('#modal-ticket-create').modal('show');
                
                errorname = [];
                upname = [];
            } else {
                modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-ticket');
            }
        },
        beforeSend: function(){
            $('#modal-repass-ticket').modal('hide');  
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnRepassTicket").html("<i class='fa fa-share'></i> "+vocab['Repass_btn']).removeClass('disabled');
            $("#btnCreateTicket").removeClass('disabled');
            $("#btnFinishTicket").removeClass('disabled');
        }
    });

    return false ;
}

function saveOpenFinishTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false,
    periods =  $('#attendanceTime').val().split(":"), 
    openTime = (parseInt(periods[0])*60) + (parseFloat(periods[1])) + (parseFloat(periods[2])/60);

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/saveOpenFinishTicket',
        dataType: 'json',
        async: false,
        data: {
            _token:             $("#_token").val(),
            ownerID:            $("#cmbUser").val(),
            sourceID:           $("#cmbSource").val(),
            ticketDate:         $("#ticketDate").val(),
            ticketTime:         $("#ticketTime").val(),
            serialNumber: 	    $('#equipmentSerialNumber').val(),
            osNumber: 		    $('#equipmentOsNumber').val(),
            tag: 			    $('#equipmentTag').val(),
            area: 			    $('#cmbArea').val(),
            type: 			    $('#cmbType').val(),
            item:			    $('#cmbItem').val(),
            service:		    $('#cmbService').val(),
            reason:			    $('#cmbReason').val(),
            subject: 		    $('#subject').val(),
            description: 	    $('#description').summernote('code'),
            solution: 	        $('#solution').summernote('code'),
            attendanceTypeID:   $('#cmbAttendanceType').val(),
            openTime:           openTime,
            attachments:        aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-ticket');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.ticketCode)) {
                var ticket = obj.ticketCode;
                global_coderequest = ticket;
                global_ticket = ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6);
                global_incharge = obj.inChargeName;
                global_expiry_date = obj.expiryDate;

                //sendNotification('new-ticket-user',global_coderequest,hasAtt);

                $('#modal-request-code').val(global_ticket);
                $('#modal-incharge-name').val(global_incharge);
                $('#modal-expiry-date').val(global_expiry_date);
                $('#modal-ticket-create').modal('show');
                
                errorname = [];
                upname = [];
            } else {
                modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-ticket');
            }
        },
        beforeSend: function(){
            
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnFinishTicket").html("<i class='fa fa-window-close'></i>  " + vocab['Finish_btn']).removeClass('disabled');
            $("#btnCreateTicket").removeClass('disabled');
            $("#btnRepassTicket").removeClass('disabled');
        }
    });

    return false ;
}

function saveRepassTicket()
{
    var trackType = $('input:radio[name=trackOptions]:checked').val(),
        inChargeType = $("#inChargeType").val(),
        trackGroupID;

    trackGroupID = (inChargeType == "P" && trackType == "G") ? $("#cmbOpeGroups").val() : 0;

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/repassTicket',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            ticketCode: $("#ticketCode").val(),
            repassType: $('input:radio[name=repassType]:checked').val(),
            repassID: $("#repassList").val(),
            trackType: trackType,
            trackGroupID: trackGroupID,
            inChargeID: $("#inChargeID").val()
        },
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],'alert-repass-ticket');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success) {
                modalAlertMultiple('success',vocab['Alert_sucess_repass'],'alert-repass-ticket');
                setTimeout(function(){
                    $('#modal-repass-ticket').modal('hide');
                    location.href = path+"/helpdezk/hdkTicket/index";
                },2000);
            } else {
                modalAlertMultiple('danger',vocab['Alert_failure'],'alert-repass-ticket');
            }

        },
        beforeSend: function(){
            $("#btnRepassClose").addClass('disabled');
            $("#btnRepassSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
        },
        complete: function(){
            $("#btnRepassClose").removeClass('disabled');
            $("#btnRepassSave").html("<i class='fa fa-share'></i> "+vocab['Repass_btn']).removeClass('disabled');
        }

    });

    return false ;
}

function removeAuxAttendant(handler){
    var tr = $(handler).closest('tr'), attendantID = $(handler).closest('tr').find('.hdkAuxOpe').val();

    $.post(path+"/helpdezk/hdkTicket/removeAuxiliaryAttendant",{
            _token: $("#_token").val(),
            ticketCode: $("#ticketCode").val(),
            attendantID: attendantID
        },
        function(valor){
            var obj = jQuery.parseJSON(JSON.stringify(valor));
            $('#cmbAttendants').html(obj.auxAttendantsData.cmbAttendants);
            $("#cmbAttendants").trigger("change");

            if(obj.auxAttendantsData.auxAttendantList){
                $("#auxAttendantList tbody").html(obj.auxAttendantsData.auxAttendantList);
                if($("#auxiliaryAttendantLine").hasClass("d-none")){
                    $("#auxiliaryAttendantLine").removeClass("d-none");
                }
                $("#auxiliaryAttendantList").html(obj.auxAttendantsData.auxAttendantLine);
            }else{
                if(!$("#auxiliaryAttendantLine").hasClass("d-none")){
                    $("#auxiliaryAttendantLine").addClass("d-none");
                }
                $("#auxAttendantList tbody").html("");
            }
        },'json');
}

function saveNote(aAttachs,myDropzone)
{
    var hasAtt = aAttachs.length > 0 ? true : false;

    if(typeUser == 3){
        var callback = ($("#callback").is(":checked")) ? '1': '0',
            noteData = {
                _token: $("#_token").val(),
                ticketCode: $("#ticketCode").val(),
                statusID: $("#statusID").val(), 
                ownerID: $("#ownerID").val(),
                noteContent: $('#ticketNote').summernote('code'),
                totalMinutes: $("#totalMinutes").val(),
                executionStarted: $("#executionStarted").val(),
                executionFinished: $("#executionFinished").val(),
                executionDate: $("#executionDate").val(),
                typeHour: $("#cmbHourType").val(),
                typeNote: $("#cmbNoteVisibility").val(),
                flagNote: 3,
                callback: callback,
                attachments:    aAttachs
            };

    }else{
        var noteData = {
                _token: $("#_token").val(),
                ticketCode: $("#ticketCode").val(),
                noteContent: $('#ticketNote').summernote('code'),
                flagNote: 2,
                callback: callback,
                attachments:    aAttachs
            };
    }

    $.ajax({
        type: "POST",
        url: path + "/helpdezk/hdkTicket/saveNote",
        data: noteData,
        dataType: 'json',
        async: false,
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Error_insert_note'],'alert-note-add');
        },
        success: function(ret) {
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success) {
                // clear summernote
                $('#ticketNote').summernote('code','');
                $("#note-form-insert").trigger('reset')
                if(typeUser == 3){
                    $("#executionDate").val(moment().format(momentFormat));
                    $("#executionStarted").val(moment().format('HH:mm:ss'));
                    $("#executionFinished").val('');
                    $("#totalMinutes").val('');
                    $("#cmbHourType").trigger("change");
                    $("#cmbNoteVisibility").trigger("change");
                    $('#callback').iCheck('unCheck');
                }
                myDropzone.removeAllFiles(true);

                $('#ticketNotesAdded').html(obj.notesAdded);

                modalAlertMultiple('info',vocab['Alert_note_sucess'],'alert-note-add');
                errorname = [];
                upname = [];
                $('[data-toggle="tooltip"]').tooltip();
            } else {
                modalAlertMultiple('danger',vocab['Error_insert_note'],'alert-note-add');
            }
        },
        beforeSend: function(){
            /*$("#btnSendNote").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');*/
        },
        complete: function(){
            $("#btnSendNote").html("<i class='fa fa-paper-plane'></i> "+ vocab['Send']).removeClass('disabled');
        }
    });

    return false ;

}

function calctotal(){
    var start = $("#executionStarted").val(),
        finish = $("#executionFinished").val();
    
    if(typeof(start) != 'undefined' && typeof(finish) != 'undefined'){
        $('#totalMinutes').val(calcmin(start,finish));
    }    
}

function calcmin(start,finish) {
    if(start == '' || finish ==''){
        return;
    }

    split = new Array();

    split[2] = start.split(":");
    split[1] = finish.split(":");

    hour1 = split[1][0];
    minute1 = split[1][1];
    second1 = (typeof(split[1][2]) != 'undefined') ? split[1][2] : "00";

    hour2 = split[2][0];
    minute2 = split[2][1];
    second2 =  (typeof(split[2][2]) != 'undefined') ? split[2][2] : "00";

    total_minutes = (((hour1 * 60) - (hour2 * 60)) + (minute1 - minute2) + ((second1 / 60) - (second2 / 60)));
    return total_minutes.toFixed(2);
}

function getTrelloBoards(){
    $.post(path+"/helpdezk/hdkTrello/getBoards",{_token: $("#_token").val()}, function(valor) {
        $("#cmbBoard").html(firstOption+valor);
        $("#cmbBoard").trigger("change");
    });
}

function getTrelloList(boardId){
    $.post(path+"/helpdezk/hdkTrello/getLists",{_token: $("#_token").val(),boardId: boardId}, 
    function(valor) {
        $("#cmbList").html(firstOption+valor);
        $("#cmbList").removeAttr('disabled');
        $("#cmbList").trigger("change");
    });
}

function getTrelloCard(listId){
    $.post(path+"/helpdezk/hdkTrello/getCards",{_token: $("#_token").val(),listId: listId}, function(valor) {
        $("#card-list-table").html(valor);
        $("#card-list-row").removeClass('d-none');
    });
}

function download(idFile, typeAttach)
{
    var urlDownload = path+'/helpdezk/hdkTicket/downloadFile/'+idFile+'/'+typeAttach+'/';
    $(location).attr('href',urlDownload);
}

function deleteNote(idnote)
{ 
    $("#modal-header-delete-note-lbltitle").html(vocab['Note']+': '+idnote);
    $("#modal-delete-note").modal('show');

    $("#btnDeleteNoteYes").click(function(){
        if(!$("#btnDeleteNoteYes").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/deleteNote',
                dataType: 'json',
                async: false,
                data: {
                    _token: $("#_token").val(),
                    ticketCode: $("#ticketCode").val(),
                    noteId: idnote,
                    statusID: $("#statusID").val(), 
                    ownerID: $("#ownerID").val(),
                    flagNote: (typeUser == 3) ? 3 : 2
                },
                error: function (ret) {
                    if($("#modal-header-delete-note").hasClass('bg-success')){
                        $("#modal-header-delete-note").removeClass('bg-success').addClass('bg-danger');
                    }

                    if($("#deleteNoteMessageLine").hasClass('alert-warning')){
                        $("#deleteNoteMessageLine").removeClass('alert-warning').addClass('alert-danger');
                    }

                    $("#deleteNoteMessageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                    $("#deleteNoteText").html(vocab['Alert_close_ticket_failure']);
                    
                    setTimeout(function(){
                        $('#modal-delete-note').modal('hide');
                        if($("#deleteNoteMessageLine").hasClass('alert-success')){
                            $("#deleteNoteMessageLine").removeClass('alert-success').addClass('alert-warning');
                        }
                        $("#deleteNoteMessageIcon").html('<i class="fa fa-question-circle fa-3x"></i>');
                        $("#deleteNoteText").html(vocab['Tckt_delete_note']);              
                    },2000);
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success) {
                        if($("#modal-header-delete-note").hasClass('bg-danger')){
                            $("#modal-header-delete-note").removeClass('bg-danger').addClass('bg-success');
                        }

                        if($("#deleteNoteMessageLine").hasClass('alert-danger')){
                            $("#deleteNoteMessageLine").removeClass('alert-danger').addClass('alert-success');
                        }

                        $("#deleteNoteMessageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#deleteNoteText").html(vocab['Alert_close_request']);
                        setTimeout(function(){
                            $('#modal-delete-note').modal('hide');
                            $('#ticketNotesAdded').html(obj.notesAdded);
                            
                            if($("#modal-header-delete-note").hasClass('bg-success')){
                                $("#modal-header-delete-note").removeClass('bg-success').addClass('bg-danger');
                            }
        
                            if($("#deleteNoteMessageLine").hasClass('alert-success')){
                                $("#deleteNoteMessageLine").removeClass('alert-success').addClass('alert-danger');
                            }
        
                            $("#deleteNoteMessageIcon").html('<i class="fa fa-question-circle fa-3x"></i>');
                            $("#deleteNoteText").html(vocab['Tckt_delete_note']);

                        },2000);
                    } else {
                        if($("#deleteNoteMessageLine").hasClass('alert-warning')){
                            $("#deleteNoteMessageLine").removeClass('alert-warning').addClass('alert-danger');
                        }
                        $("#deleteNoteMessageIcon").html('<i class="fa fa-exclamation-circle fa-3x"></i>');
                        $("#deleteNoteText").html(vocab['Alert_close_ticket_failure']);
                    }
                },
                beforeSend: function(){
                    $("#btnDeleteNoteNo").addClass('disabled');
                    $("#btnDeleteNoteYes").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');                    
                },
                complete: function(){
                    $("#btnDeleteNoteNo").removeClass('disabled');
                    $("#btnDeleteNoteYes").html(vocab['Yes']).removeClass('disabled');
                }
            });
        }
    });

    return false;  // <- cancel event
}
