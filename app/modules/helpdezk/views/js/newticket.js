var global_coderequest = '';
var htmlArea = '',
    showDefs  = '';
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[];


$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    htmlArea = makeAreaCombo();
    showDefs = showDefaults();

    console.log('exist: '+ showDefs);

    if (showDefs == 'YES') {
        $("#areaId").html(htmlArea);
        $.post(path + "/helpdezk/hdkTicket/ajaxTypeWithAreaDefault",
            function (valor) {
                $('#typeId').removeAttr('disabled');
                $("#typeId").html(valor);
                $("#typeId").trigger("chosen:updated");
                return objNewTicket.changeItem();
            })
    } else if (showDefs == 'NO') {
        $("#areaId").html('<option value="X">'+makeSmartyLabel('Select')+'</option>' + htmlArea);
        $("#areaId").val('X');
    }


    $('[data-toggle="tooltip"]').tooltip();

    $("#btnCancel").attr("href", path + '/helpdezk/hdkTicket/index');

    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {
        url: path + "/helpdezk/hdkTicket/saveTicketAttachments/",
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
            saveTicket(upname);            
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
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_description')

        }
    );


    /**
     ** .validate() is what initializes the Validation plugin on your form.
     ** .valid() returns true or false depending on if your form is presently valid.
     **/
    $("#newticket-form").validate({
        ignore:[],
        rules: {
            subject: "required",  // simple rule, converted to {required:true}
            areaId: {
                required: true,
                number: true
            },
            typeId: {
                required: true,
                number: true
            },
            itemId: {
                required: true,
                number: true
            },
            serviceId: {
                required: true,
                number: true
            }

        },
        messages: {
            subject: makeSmartyLabel('Alert_empty_subject'),
            areaId: makeSmartyLabel('Alert_field_required'),
            typeId: makeSmartyLabel('Alert_field_required'),
            itemId: makeSmartyLabel('Alert_field_required'),
            serviceId: makeSmartyLabel('Alert_field_required')
        }

    });


    $("#btnCreateTicket").click(function(){

        var ticketDescription = $('#description').summernote('code');

        if ($("#newticket-form").valid()) {
            if(!$("#btnCreateTicket").hasClass('disabled')){
                $("#btnCreateTicket").addClass('disabled')
                if (myDropzone.getQueuedFiles().length > 0) {
                    dropzonefiles = myDropzone.getQueuedFiles().length;
                     myDropzone.processQueue();
                } else {
                    saveTicket(upname);
                }
            }
        } else {
            return false;
        } 

    });

    // Combos
    var formNewTicket = $(document.getElementById("newticket-form"));
    var objNewTicket = {
        changeArea: function() {
            var areaId = $("#areaId").val();
            $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaId: areaId},
                function(valor){

                    $('#typeId').removeAttr('disabled');

                    if (showDefs == 'YES') {
                        $("#typeId").html(valor);
                        $("#typeId").trigger("chosen:updated");
                        return objNewTicket.changeItem();
                    } else if (showDefs == 'NO') {
                        $("#typeId").html('<option value="X">'+makeSmartyLabel('Select')+'</option>' + valor);
                        $("#typeId").val('X');
                        $("#typeId").trigger("chosen:updated");
                    }
                })
        },
        changeItem: function(){
            var typeId = $("#typeId").val();
            $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeId: typeId},
                function(valor){
                    $('#itemId').removeAttr('disabled');
                    if (showDefs == 'YES') {
                        $("#itemId").html(valor);
                        $("#itemId").trigger("chosen:updated");
                        return objNewTicket.changeService();
                    } else if (showDefs == 'NO') {
                        $("#itemId").html('<option value="X">'+makeSmartyLabel('Select')+'</option>' + valor);
                        $("#itemId").val('X');
                        $("#itemId").trigger("chosen:updated");
                    }
                })
        },
        changeService: function(){
            var itemId = $("#itemId").val();
            console.log(itemId);
            $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemId: itemId},
                function(valor){
                    $('#serviceId').removeAttr('disabled');
                    if (showDefs == 'YES') {
                        $("#serviceId").html(valor);
                        $("#serviceId").trigger("chosen:updated");
                        return objNewTicket.changeReason();
                    } else if (showDefs == 'NO') {
                        $("#serviceId").html('<option value="X">'+makeSmartyLabel('Select')+'</option>' + valor);
                        $("#serviceId").val('X');
                        $("#serviceId").trigger("chosen:updated");
                    }

                })
        },
        changeReason: function(){
            var serviceId = $("#serviceId").val();
            console.log(serviceId);
            $.post(path+"/helpdezk/hdkTicket/ajaxReasons",{serviceId: serviceId},
                function(valor){
                    $('#reasonId').removeAttr('disabled');
                    if (showDefs == 'YES') {
                        $("#reasonId").html(valor);
                        $("#reasonId").trigger("chosen:updated");
                    } else if (showDefs == 'NO') {
                        $("#reasonId").html('<option value="X">'+makeSmartyLabel('Select')+'</option>' + valor);
                        $("#reasonId").val('X');
                        $("#reasonId").trigger("chosen:updated");
                    }
                })
        }
    };


    $("#areaId").change(function(){
        objNewTicket.changeArea();
    });

    $("#typeId").change(function(){
        objNewTicket.changeItem();
    });

    $("#itemId").change(function(){
        objNewTicket.changeService();
    });

    $("#serviceId").change(function(){
        objNewTicket.changeReason();
    });

    /*
     *  Chosen
     */
    $("#areaId").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#typeId").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#itemId").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#serviceId").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#reasonId").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});

    $("#btnSendApvReqYes").click(function(){
        location.href = path + "/helpdezk/hdkTicket/index" ;
    });

    $("#btnNewTck").click(function(){
        $.post(path + "/helpdezk/home/checkapproval", {}, function(data) {
            if(data > 0){
                $('#tipo-alert-apvrequire').addClass('alert alert-danger')
                $('#apvrequire-notification').html(makeSmartyLabel('Request_approve'));
                $('#modal-approve-require').modal('show');
            }else{
                location.href = path + "/helpdezk/hdkTicket/newTicket" ;
            }
        })
    });

    $("#btnNextYes").click(function(){
        
        $('#modal-next-step').modal('hide');
        saveTicket(upname);

    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];
            location.href = path + "/helpdezk/hdkTicket/index" ;
        } 

    });
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

function makeOptLabel(confName)
{
    return '<option value="">'+makeSmartyLabel(confName)+'</option>';
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
        url : path + '/helpdezk/hdkTicket/sendNotification',
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

function showNextStep(msg,typeAlert,totalAttach)
{
    $('#nexttotalattach').val(totalAttach);
    $('#next-step-list').html(msg);
    $('#next-step-message').html(makeSmartyLabel('open_ticket_anyway_question'));
    $("#type-alert").attr('class', 'col-sm-12 col-xs-12 bs-callout-'+typeAlert);
    $('#modal-next-step').modal('show');

    return false;
}

function saveTicket(aAttachs)
{
    var hasAtt = aAttachs.length > 0 ? true : false;

    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkTicket/saveTicket',
        dataType: 'json',
        data: {
            serial_number: 	$('#serial_number').val(),
            os_number: 		$('#os_number').val(),
            tag: 			$('#tag').val(),
            area: 			$('#areaId').val(),
            type: 			$('#typeId').val(),
            item:			$('#itemId').val(),
            service:		$('#serviceId').val(),
            reason:			$('#reasonId').val(),
            subject: 		$('#subject').val(),
            description: 	$('#description').summernote('code'),
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
            //$("#btnCreateTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
        },
        complete: function(){
            $("#btnCreateTicket").html("<span class='fa fa-check'></span>  " + makeSmartyLabel('btn_submit'));
        }

    });

    return false ;

}

