var global_coderequest = '', htmlArea = '', showDefs  = '', dropzonefiles = 0,
    filesended = 0, flgerror = 0, errorname=[], upname=[], btnClicked="";

$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdatePerson').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    var objRequestData = {
        reloadRequester: function(selectedID) {
            $.post(path+"/lgp/lgpDPORequest/ajaxRequester",{selectedID:selectedID},function(valor) {
                $("#cmbRequester").html(valor);
                $("#cmbRequester").trigger("chosen:updated");
                return false;
            });
        },
        loadRepassList: function(){
            var repassType = $("input[name='typerep']:checked").val();
            
            $.post(path+"/lgp/lgpDPORequest/ajaxRepassList",{typerep: repassType},function(valor){
                $("#replist").html(valor);
                $("#replist").trigger("chosen:updated");
                /*if(!$("#btnAbilities").hasClass("off")){
                    objNewTicket.getAbilities();
                }
                else if(!$("#btnGroups").hasClass("off")){
                    objNewTicket.getGroups();
                }*/
            });
        }
    }

    /*
     * Mask
     */
    $('#requesterCPF').mask('000.000.000-00');

    /*
     *  Chosen
     */
    $("#cmbRequester").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#replist").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#typenote").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    Dropzone.autoDiscover = false;
    if($("#lgp-viewticket-form").length <= 0){
        var myDropzone = new Dropzone("#reqAttachs", {
            url: path + "/lgp/lgpDPORequest/saveAttachments/",
            method: "post",
            dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Tckt_drop_file'),
            createImageThumbnails: true,
            maxFiles: ticketAttMaxFiles,
            maxFilesize: hdkMaxSize,
            acceptedFiles: ticketAcceptedFiles,
            parallelUploads: ticketAttMaxFiles,                         // https://github.com/enyo/dropzone/issues/253
            autoProcessQueue: false,
            dictFileTooBig: makeSmartyLabel('hdk_exceed_max_file_size'),
            addRemoveLinks:true,
            dictRemoveFile: makeSmartyLabel('hdk_remove_file')
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
                    saveRepassTicket(upname);
                }else if(btnClicked=="3"){
                    saveFinishTicket(upname);
                }
                            
            }else{
                var totalAttach = dropzonefiles - filesended;
                msg = '<h4>'+makeSmartyLabel('files_not_attach_list')+'</h4><br>';
                errorname.forEach(element => {
                    msg = msg+element+'<br>';
                });
                msg = msg+'<br>'+makeSmartyLabel('hdk_attach_after');
                typeMsg = 'warning';
                showNextStep(msg,typeMsg,totalAttach);
            }        
            
            dropzonefiles = 0; 
            filesended = 0;
            flgerror = 0;
        });

        $('#reqDescription').summernote(
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
                placeholder:  makeSmartyLabel('Editor_Placeholder_description')
    
            }
        );

    }else{
        if($('#idstatus').val() == 3 && $('#myDropzone').length > 0 ) {
            Dropzone.autoDiscover = false;
            var myDropzone = new Dropzone("#myDropzone", {
                url: path + "/lgp/lgpDPORequest/saveNoteAttach/",
                method: "post",
                dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Tckt_drop_file'),
                createImageThumbnails: true,
                maxFiles: noteAttMaxFiles,
                maxFilesize: hdkMaxSize,
                acceptedFiles: noteAcceptedFiles,
                parallelUploads: noteAttMaxFiles,                         // https://github.com/enyo/dropzone/issues/253
                autoProcessQueue: false,
                dictFileTooBig: makeSmartyLabel('hdk_exceed_max_file_size'),
                addRemoveLinks:true,
                dictRemoveFile: makeSmartyLabel('hdk_remove_file')
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
                    saveNote(upname,myDropzone);            
                }else{
                    var totalAttach = dropzonefiles - filesended;
                    msg = '<h4>'+makeSmartyLabel('files_not_attach_list')+'</h4><br>';
                    errorname.forEach(element => {
                        msg = msg+element+'<br>';
                    });
                    msg = msg+'<br>'+makeSmartyLabel('hdk_attach_after_note');
                    typeMsg = 'warning';
                    showNextStep(msg,typeMsg,totalAttach);
                }        
                
                dropzonefiles = 0; 
                filesended = 0;
                flgerror = 0;
            });
    
        }
    
        $('#button-reload').click(function() {
            location.reload();
        });

        $('#requestnote').summernote(
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
                placeholder:  makeSmartyLabel('Editor_Placeholder_insert')
    
            }
        );

        $('#reasonreject').summernote(
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
                placeholder:  makeSmartyLabel('Editor_Placeholder_reason')
    
            }
        );
    }

    $("input[name='typerep']").on('ifChecked', function() { // bind a function to the change event
        objRequestData.loadRepassList();
    });

    $("#grpkeep").on('ifChecked', function() { // bind a function to the change event
        $("#assumeGroupsList").removeClass('hide');
    }).on('ifUnchecked', function() { // bind a function to the change event
        $("#assumeGroupsList").addClass('hide');
    });

    /*
     * Validate
     */
    $("#lgp-newticket-form").validate({
        ignore:[],
        rules: {
            cmbRequester:"required",
            reqSubject:{required:true,minlength:3}
        },
        messages: {
            cmbRequester:makeSmartyLabel('Alert_field_required'),
            reqSubject:{required:makeSmartyLabel('Alert_field_required'),minlength:makeSmartyLabel('Alert_minlength')}
        }
    });

    $("#requester-form").validate({
        ignore:[],
        rules: {
            requesterName:{
                required:true,
                minlength:3,
                remote:{
                    url: path+"/lgp/lgpDPORequest/checkRequester",
                    data:{
                        cpf:function(element){return $("#requesterCPF").val()}
                    },
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            },
            requesterCPF:{
                required:true,
                remote:{
                    param:{
                        url: path+"/lgp/lgpDPORequest/checkCPF",
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{
                        function(element){return $("#personacCPF").val() != ''}
                    }
                    
                }
            },
            requesterEmail:{
                required:true,
                email:true
            }
        },
        messages: {
            requesterName:{
                required:makeSmartyLabel('Alert_field_required'),
                minlength:makeSmartyLabel('Alert_minlength')
            },
            requesterCPF:{
                required:makeSmartyLabel('Alert_field_required')
            },
            requesterEmail:{
                required:makeSmartyLabel('Alert_field_required'),
                email:makeSmartyLabel('Alert_invalid_email')
            }
        }
    });

    $("#repass-form").validate({
        ignore:[],
        rules: {
            typerep: "required",  // simple rule, converted to {required:true}
            replist: "required",
            repoptns: "required"
        },
        messages: {
            typerep: makeSmartyLabel('Alert_field_required'),
            replist: makeSmartyLabel('Alert_field_required'),
            repoptns: makeSmartyLabel('Alert_field_required')
        }

    });


    /*
     * Buttons
     */
    $("#btnCancel").click(function(){
        location.href = path + "/lgp/lgpDPORequest/index";
    });

    $("#btnSaveTicket").click(function(){

        if (!$("#lgp-newticket-form").valid()) {
            return false ;
        }

        if ($('#reqDescription').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('APP_requireDescription'),'alert-lgp-newticket');
            return false;
        }

        if(!$("#btnSaveTicket").hasClass('disabled')){
            $("#btnCancel").addClass('disabled');
            $("#btnSaveTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            $("#btnRepassTicket").addClass('disabled');
            $("#btnFinishTicket").addClass('disabled');
            btnClicked = "1";
            if (myDropzone.getQueuedFiles().length > 0) {
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveTicket(upname);
            }
        }
        
    });

    $("#btnRepassTicket").click(function(){

        if (!$("#lgp-newticket-form").valid()) {
            return false ;
        }

        if ($('#reqDescription').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('APP_requireDescription'),'alert-lgp-newticket');
            return false;
        }

        $('#modal-form-repass').modal('show');
        objRequestData.loadRepassList(); 

    });

    if($("#lgp-viewticket-form").length <= 0){
        $("#btnSaveRepassTicket").click(function(){

            if (!$("#repass-form").valid()) {
                return false ;
            }
            
            if(!$("#btnRepassTicket").hasClass('disabled')){
                $('#modal-form-repass').modal('hide');
                $("#btnCancel").addClass('disabled');
                $("#btnCreateTicket").addClass('disabled');
                $("#btnRepassTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnFinishTicket").addClass('disabled');
                btnClicked = "2";
                if (myDropzone.getQueuedFiles().length > 0) {
                    console.log("files repass");
                    dropzonefiles = myDropzone.getQueuedFiles().length;
                     myDropzone.processQueue();
                } else {
                    saveRepassTicket(upname);
                }
            }
    
            return false;
        });
    }else{
        $("#btnSaveRepassTicket").click(function(){

            if (!$("#repass-form").valid()) {
                return false ;
            }
            
            var view = $('input:radio[name=repoptns]:checked').val(), typeincharge = $("#typeincharge").val(), idgrouptrack = 0;

            if(typeof(view) =="undefined"){
                modalAlertMultiple('danger',makeSmartyLabel('Alert_follow_repass'),'alert-repass-form');
                return false;
            }

            idgrouptrack = (typeincharge == "P" && view == "G") ? $("#cmbOpeGroups").val() : 0;

            if(!$("#btnSaveRepassTicket").hasClass('disabled')){
                $.ajax({
                    type: "POST",
                    url: path + '/lgp/lgpDPORequest/repassTicket',
                    dataType: 'json',
                    data: {
                        _token: $("#_token").val(),
                        type: $('input:radio[name=typerep]:checked').val(),
                        repassto: $("#replist").val(),
                        code_request: $("#coderequest").val(),
                        view: view,
                        idgrouptrack: idgrouptrack,
                        incharge: $("#incharge").val()
                    },
                    error: function (ret) {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-repass-form');
                    },
                    success: function(ret){
    
                        var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                        if(obj.success) {
                            modalAlertMultiple('success',makeSmartyLabel('Alert_sucess_repass'),'alert-repass-form');
                            setTimeout(function(){
                                $('#modal-form-repass').modal('hide');
                                location.href = path+"/lgp/lgpDPORequest/index";
                            },2000);
                        } else {
                            modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-repass-form');
                        }
    
                    },
                    beforeSend: function(){
                        $("#btnCancelRepassTicket").addClass('disabled');
                        $("#btnSaveRepassTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    },
                    complete: function(){
                        $("#btnCancelRepassTicket").removeClass('disabled');
                        $("#btnSaveRepassTicket").html("<i class='fa fa-share'></i> "+makeSmartyLabel('Repass_btn')).removeClass('disabled');
                    }
    
                });
            }
    
            return false;
        });
    }

    $("#btnFinishTicket").click(function(){

        var ticketDescription = $('#description').summernote('code'),
            periods =  $('#atttime').val().split(":"), 
            open_time = (parseInt(periods[0])*60) + (parseFloat(periods[1])) + (parseFloat(periods[2])/60);

        if ($("#newticket-form").valid()) {
            if(!$("#btnFinishTicket").hasClass('disabled')){
                $("#btnCancel").addClass('disabled');
                $("#btnCreateTicket").addClass('disabled');
                $("#btnRepassTicket").addClass('disabled');
                $("#btnFinishTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                btnClicked = "3";
                
                if (myDropzone.getQueuedFiles().length > 0) {
                    dropzonefiles = myDropzone.getQueuedFiles().length;
                    myDropzone.processQueue();
                } else {
                    saveFinishTicket(upname);
                }
            }
        } else {
            return false;
        } 

    });
    
    $("#btnAddRequester").click(function(){
        $('#modal-form-requester').modal('show');        
    });

    $("#btnSaveRequester").click(function(){

        if (!$("#requester-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveRequester").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpDPORequest/saveRequester',
                dataType: 'json',
                data: {
                    _token: $('#_token').val(),
                    requesterName: $('#requesterName').val(),
                    requesterCPF: $('#requesterCPF').val(),
                    requesterEmail: $('#requesterEmail').val(),
                },
                error: function (ret) {
                    modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-requester');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-requester');
                        objRequestData.reloadRequester(obj.requesterID);
                        setTimeout(function(){
                            $('#modal-form-requester').modal('hide');
                        },2000);
                    }
                },
                beforeSend: function(){
                    $("#btnSaveRequester").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCloseRequester").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveRequester").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCloseRequester").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnAssume").click(function(){
        if($("#isdpo").val() == '1'){
            if(!$(".groupAssumeLine").hasClass('hide'))
                $(".groupAssumeLine").addClass('hide')
            if($(".dpoAssumeLine").hasClass('hide'))
                $(".dpoAssumeLine").removeClass('hide')
        }else{
            if($(".groupAssumeLine").hasClass('hide'))
                $(".groupAssumeLine").removeClass('hide')
            if(!$(".dpoAssumeLine").hasClass('hide'))
                $(".dpoAssumeLine").addClass('hide')
        }
        $('#modal-form-assume').modal('show');
    });

    $("#btnSaveAssumeTicket").click(function(){

        if(!$("#btnSaveAssumeTicket").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpDPORequest/assumeTicket',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    code_request:$("#coderequest").val(),
                    grpview: $("#grpkeep").is(":checked") ? '1' : '0',
                    typeincharge: $("#typeincharge").val(),
                    incharge: $("#incharge").val(),
                    groupAssume: $("#cmbAssumeGroups").val(),
                    isdpo: $("#isdpo").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-assume-form');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Assumed_successfully'),'alert-assume-form');
                        setTimeout(function(){
                            $('#modal-form-assume').modal('hide');
                            location.reload();
                        },2000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-assume-form');
                    }
    
                },
                beforeSend: function(){
                    $("#btnCancelAssumeTicket").addClass('disabled');
                    $("#btnSaveAssumeTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnCancelAssumeTicket").removeClass('disabled');
                    $("#btnSaveAssumeTicket").html("<i class='fa fa-check-square'></i> "+ makeSmartyLabel('btn_assume')).removeClass('disabled');
                }
    
            });
        }

        return false;
    });

    $("#btnRepass").click(function(){

        $('#modal-form-repass').modal('show');
        objRequestData.loadRepassList(); 

    });

    $("#btnReject").click(function(){
        $('#modal-form-reject').modal('show');
    });

    $("#btnSaveRejectTicket").click(function(){
        if ($('#reasonreject').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_empty_reason'),'alert-reject-form');
            return false;
        }

        if(!$("#btnSaveRejectTicket").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpDPORequest/rejectTicket',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    code_request: $("#coderequest").val(),
                    typeincharge: $("#typeincharge").val(),
                    incharge: $("#incharge").val(),
                    rejectreason: $('#reasonreject').summernote('code')
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reject-form');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Reject_sucess'),'alert-reject-form');
                        setTimeout(function(){
                            $('#modal-form-reject').modal('hide');
                            location.href = path+"/lgp/lgpDPORequest/index";
                        },2000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reject-form');
                    }
    
                },
                beforeSend: function(){
                    $("#btnCancelRejectTicket").addClass('disabled');
                    $("#btnSaveRejectTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnCancelRejectTicket").removeClass('disabled');
                    $("#btnSaveRejectTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Reject_btn')).removeClass('disabled');
                }
    
            });
        }

        return false;
    });

    $("#btnCloseReq").click(function(){
        $('#modal-form-close').modal('show');
    });

    $("#btnSaveCloseTicket").click(function(){        
        if(!$("#btnSaveCloseTicket").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpDPORequest/finishTicket',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    code_request: $("#coderequest").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-close-form');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_close_request'),'alert-close-form');
                        setTimeout(function(){
                            $('#modal-form-close').modal('hide');
                            location.href = path+"/lgp/lgpDPORequest/index";
                        },2000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-close-form');
                    }
    
                },
                beforeSend: function(){
                    $("#btnCancelCloseTicket").addClass('disabled');
                    $("#btnSaveCloseTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnCancelCloseTicket").removeClass('disabled');
                    $("#btnSaveCloseTicket").html("<i class='fa fa-check'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
                }
    
            });
        }

        return false;
    });

    $("#btnPrint").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/lgp/lgpDPORequest/makeReport",
            data: {
                _token: $('#_token').val(),
                code_request : $('#coderequest').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {
                console.log(fileName);
                if(fileName){
                    /*
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

    $('#btnSendNote').click(function(e) {

        e.preventDefault();

        if(!$("#btnSendNote").hasClass('disabled')){
            if ($('#requestnote').summernote('isEmpty')) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_empty_note'),'alert-noteadd');
                return false;
            }

            $("#btnSendNote").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            if (myDropzone.getQueuedFiles().length > 0) {
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveNote(upname,myDropzone);
            }            
        }

        return false;  // <- cancel event

    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if($("#lgp-viewticket-form").length <= 0){
            if(btnClicked=="1"){
                saveTicket(upname);
            }else if(btnClicked=="2"){
                saveRepassTicket(upname);
            }else if(btnClicked=="3"){
                saveFinishTicket(upname);
            }
        }else{
            saveNote(upname,myDropzone);
        }        
    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];

            if($("#lgp-viewticket-form").length <= 0){
                location.href = path + "/lgp/lgpDPORequest/index" ;
            }else{
                $('#requestnote').summernote('code','');
                showNotes($('#coderequest').val());
                myDropzone.removeAllFiles(true);
                
                if($("#btnSendNote").hasClass('disabled'))
                    $("#btnSendNote").html("<i class='fa fa-paper-plane'></i> "+ makeSmartyLabel('Send')).removeClass('disabled');
            }
        }
    });

    /* clean modal form */
    $('#modal-form-requester').on('hidden.bs.modal', function() {
        $('#requester-form').trigger('reset');
    });

    /* tooltip */
    $('[data-toggle="tooltip"]').tooltip();

});

function showNextStep(msg,typeAlert,totalAttach)
{
    var lblMsg = $("#lgp-viewticket-form").length <= 0 ? makeSmartyLabel('open_ticket_anyway_question') : makeSmartyLabel('save_note_anyway_question');
    $('#nexttotalattach').val(totalAttach);
    $('#next-step-list').html(msg);
    $('#next-step-message').html(lblMsg);
    $("#type-alert").attr('class', 'col-sm-12 col-xs-12 bs-callout-'+typeAlert);
    $('#modal-next-step').modal('show');

    return false;
}

function saveTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false;

    $.ajax({
        type: "POST",
        url: path + '/lgp/lgpDPORequest/saveTicket',
        dataType: 'json',
        data: {
            _token:         $("#_token").val(),
            requesterID:    $("#cmbRequester").val(),
            subject: 		$('#reqSubject').val(),
            description: 	$('#reqDescription').summernote('code'),
            attachments:    aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-lgp-newticket');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.coderequest)) {

                var ticket = obj.coderequest;
                global_coderequest = ticket;
                global_ticket = ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6);
                global_incharge = obj.incharge;

                sendNotification('new-ticket-user',global_coderequest,hasAtt);

                $('#modal-coderequest').html(global_ticket);
                $('#modal-incharge').html(global_incharge);

                $("#btnModalAlert").attr("href", path + '/lgp/lgpDPORequest/index');

                $('#modal-form-alert').modal('show');
                
                errorname = [];
                upname = [];
                

            } else {

                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-lgp-newticket');

            }

        },
        beforeSend: function(){
            /*$("#btnCancel").addClass('disabled');
            $("#btnCreateTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            $("#btnRepassTicket").addClass('disabled');
            $("#btnFinishTicket").addClass('disabled');*/
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnSaveTicket").html("<span class='fa fa-save'></span>  " + makeSmartyLabel('Register_btn'));
        }

    });

    return false ;

}

function saveRepassTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false;

    $.ajax({
        type: "POST",
        url: path + '/lgp/lgpDPORequest/openRepassedTicket',
        dataType: 'json',
        data: {
            _token:         $("#_token").val(), 
            requesterID:    $("#cmbRequester").val(),
            subject: 		$('#reqSubject').val(),
            description: 	$('#reqDescription').summernote('code'),
            repassto: 		$('#replist').val(),
            viewrepass: 	$('input[name="repoptns"]:checked').val(),
            typerepass:		$('input[name="typerep"]:checked').val(),
            viewgroup:      $('#cmbOpeGroups').val(),
            attachments:    aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-lgp-newticket');
        },
        success: function(ret){
            //$('#modal-form-repass').modal('hide');
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.coderequest)) {

                var ticket = obj.coderequest;
                global_coderequest = ticket;
                global_ticket = ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6);
                global_incharge = obj.incharge;

                sendNotification('new-ticket-user',global_coderequest,hasAtt);

                $('#modal-coderequest').html(global_ticket);
                $('#modal-incharge').html(global_incharge);

                $("#btnModalAlert").attr("href", path + '/lgp/lgpDPORequest/index');

                $('#modal-form-alert').modal('show');
                
                errorname = [];
                upname = [];
                

            } else {

                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-lgp-newticket');

            }

        },
        beforeSend: function(){
            /*$('#modal-form-repass').modal('hide');
            $("#btnCancel").addClass('disabled');
            $("#btnCreateTicket").addClass('disabled');
            $("#btnRepassTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            $("#btnFinishTicket").addClass('disabled');*/
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnRepassTicket").html("<span class='fa fa-share'></span>  " + makeSmartyLabel('Repass_btn'));
        }

    });

    return false ;

}

function saveFinishTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false,
    periods =  $('#atttime').val().split(":"), 
    open_time = (parseInt(periods[0])*60) + (parseFloat(periods[1])) + (parseFloat(periods[2])/60);

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/openFinishTicket',
        dataType: 'json',
        data: { 
            idrequester:    $("#cmbUser").val(),
            source:         $("#cmbSource").val(),
            serial_number: 	$('#serial_number').val(),
            os_number: 		$('#os_number').val(),
            tag: 			$('#tag').val(),
            date:           $('#requestdate').val(),
            time:           $('#requesttime').val(),
            area: 			$('#areaId').val(),
            type: 			$('#typeId').val(),
            item:			$('#itemId').val(),
            service:		$('#serviceId').val(),
            reason:			$('#reasonId').val(),
            way:            $('#attwayId').val(),
            subject: 		$('#subject').val(),
            description: 	$('#description').summernote('code'),
            solution:       $('#solution').summernote('code'),
            open_time:     open_time,
            attachments:    aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-newticket');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.coderequest)) {

                var ticket = obj.coderequest;
                global_coderequest = ticket;
                global_ticket = ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6);
                global_expire = obj.expire;
                global_incharge = obj.incharge;

                sendNotification('new-ticket-user',global_coderequest,hasAtt);

                $('#modal-coderequest').html(global_ticket);
                $('#modal-expire').html(global_expire);
                $('#modal-incharge').html(global_incharge);

                $("#btnModalAlert").attr("href", path + '/helpdezk/hdkTicket/index');

                $('#modal-form-alert').modal('show');
                
                errorname = [];
                upname = [];
                

            } else {

                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-newticket');

            }

        },
        beforeSend: function(){
            /*$("#btnCancel").attr('disabled','disabled');
            $("#btnCreateTicket").attr('disabled','disabled');
            $("#btnRepassTicket").attr('disabled','disabled');
            $("#btnFinishTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');*/
        },
        complete: function(){
            $("#btnCancel").removeClass('disabled');
            $("#btnFinishTicket").html("<span class='fa fa-times'></span>  " + makeSmartyLabel('Finish_btn'));
        }

    });

    return false ;

}

function sendNotification(transaction,codeRequest,hasAttachments)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/lgp/lgpDPORequest/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: codeRequest,
            has_attachment: hasAttachments
        },
        success : function(data) {

        },
        error : function(request,error)
        {

        }
    });

    return false ;

}

function saveNote(aAttachs,myDropzone)
{
    var hasAtt = aAttachs.length > 0 ? true : false;
    if ($("#callback").is(":checked")) var callback = '1';
    else var callback = '0';

    $.ajax({
        type: "POST",
        url: path + "/lgp/lgpDPORequest/saveNote",
        data: {
            _token: $('#_token').val(),
            noteContent: $('#requestnote').summernote('code'),
            code_request: $('#coderequest').val(),
            typeNote: $("#typenote").val(),
            flagNote: 3,
            attachments:    aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
        },
        success: function(ret) {
            console.log('ajax saveNote, return: ' + ret);
            if($.isNumeric(ret)) {
                idNote = ret ;
                console.log('idnote: ' + idNote);
                //sendNotification('addnote',$('#coderequest').val(),hasAtt);
                // clear summernote
                $('#requestnote').summernote('code','');
                showNotes( $('#coderequest').val());
                myDropzone.removeAllFiles(true);
                modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
                errorname = [];
                upname = [];
            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            }
        },
        beforeSend: function(){
            /*$("#btnSendNote").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');*/
        },
        complete: function(){
            $("#btnSendNote").html("<i class='fa fa-paper-plane'></i> "+ makeSmartyLabel('Send')).removeClass('disabled');
        }
    });

    return false ;

}

function showNotes(codeRequest)
{
    $.ajax({
        url : path + '/lgp/lgpDPORequest/ajaxNotes',
        type : 'POST',
        data : {
            _token: $("#_token").val(),
            code_request: codeRequest
        },
        success : function(data) {
            $('#ticket_notes').html(data);
            /* tooltip */
            $('[data-toggle="tooltip"]').tooltip();
        },
        error : function(request,error)
        {

        }
    });

    return false ;
}

function download(idFile, typeAttach)
{
    var urlDownload = path+'/lgp/lgpDPORequest/downloadFile/id/'+idFile+'/type/'+typeAttach+'/';
    $(location).attr('href',urlDownload);
}


