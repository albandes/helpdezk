//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0, filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0,
    htmlArea = '', showDefs  = '', global_coderequest, global_ticket, global_incharge, global_expiry_date;

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
                } else if (showDefs == 'NO') {
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
                } else if (showDefs == 'NO') {
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
                } else if (showDefs == 'NO') {
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
                } else if (showDefs == 'NO') {
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
    }
};

$(document).ready(function () {
    countdown.start(timesession);

    htmlArea = makeAreaCombo();
    showDefs = showDefaults();

    /*
     * Select2
     */
    $('#cmbArea').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});
    $('#cmbType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,disabled:true}); 
    $('#cmbItem').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,disabled:true}); 
    $('#cmbService').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,disabled:true});
    $('#cmbReason').select2({width:"100%",placeholder:vocab['Select'],allowClear:true,disabled:true});

    if (showDefs == 'YES') {
        $("#cmbArea").html(htmlArea);
        $("#cmbArea").trigger("change");
        objTicket.changeArea();
    } else if (showDefs == 'NO') {
        $("#cmbArea").html('<option value="X">'+vocab['Select']+'</option>' + htmlArea);
        $("#cmbArea").val('X');
        $("#cmbArea").trigger("change");
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
                    saveRepassTicket(upname);
                }else if(btnClicked=="3"){
                    saveFinishTicket(upname);
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

    }else{
        if($('#idstatus').val() == 3 && $('#myDropzone').length > 0 ) {
            var myDropzone = new Dropzone("#myDropzone", {
                url: path + "/helpdezk/hdkTicket/saveNoteAttach/",
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
                placeholder:  vocab['Editor_Placeholder_insert']
    
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
                placeholder:  vocab['Editor_Placeholder_reason']
    
            }
        );
    }
   
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
            $("#btnCreateTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
            btnClicked = "1";

            if(myDropzone.getQueuedFiles().length > 0){
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            }else{
                saveTicket(upname);
            }
        }
        
    });

    $("#btnUpdateReason").click(function(){

        if (!$("#update-reason-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateReason").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkReason/updateReason',
                dataType: 'json',
                data: $("#update-reason-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-reason');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-reason');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateReason").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateReason").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }

    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if(btnClicked=="1"){
            saveTicket(upname);
        }else if(btnClicked=="2"){
            saveRepassTicket(upname);
        }else if(btnClicked=="3"){
            saveFinishTicket(upname);
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

    /*
     * Validate
     */
    $("#create-ticket-form").validate({
        ignore:[],
        rules: {
            cmbArea:{
                required:true,
                number:true
            },
            cmbType:{
                required:true,
                number:true
            },
            cmbItem:{
                required:true,
                number:true
            },
            cmbService:{
                required:true,
                number:true
            },
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

    $("#update-reason-form").validate({
        ignore:[],
        rules: {
            reason:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                minlength:5,
                remote:{
                    url: path+'/helpdezk/hdkReason/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        reasonID:function(element){return $("#reasonID").val()},
                        idservice:function(element){return $("#cmbService").val()}
                    }
                }
            },
            cmbArea:{
                required:true,
            },
            cmbType:{
                required:true,
            },
            cmbItem:{
                required:true,
            },
            cmbService:{
                required:true,
            }
        },
        messages: {            
            reason:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_five_characters']},
            cmbArea:{required:vocab['Alert_field_required']},
            cmbType:{required:vocab['Alert_field_required']},
            cmbItem:{required:vocab['Alert_field_required']},
            cmbService:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-ticket-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkTicket/index";        
    });

    if($("#update-reason-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkTicket/index";        
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

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/saveTicket',
        dataType: 'json',
        async: false,
        data: {
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
            $("#btnCreateTicket").html("<span class='fa fa-save'></span>  " + vocab['Save']).removeClass('disabled');
        }
    });

    return false ;
}
