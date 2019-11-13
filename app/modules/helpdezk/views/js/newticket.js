var global_coderequest = '';
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    // Exists Area Default ?
    $.post(path + "/helpdezk/hdkTicket/areaDefault",
        function (valor) {
            if (valor != 'NO') {
                $('#typeId').removeAttr('disabled');
                $("#typeId").html(valor);
                $("#typeId").trigger("chosen:updated");
                return objNewTicket.changeItem();
            }
    })


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

    myDropzone.on("queuecomplete", function (file) {        // https://stackoverflow.com/questions/18765183/how-do-i-refresh-the-page-after-dropzone-js-upload
        console.log('Completed the dropzone queue');
        sendNotification('new-ticket-user',global_coderequest,true);
        console.log('Sent email, with attachments');
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
            area: "required",
            typeId: "required",
            itemId: "required",
            serviceId: "required"
        },
        messages: {
            subject: makeSmartyLabel('Alert_empty_subject'),
            area: makeSmartyLabel('Alert_field_required'),
            typeId: makeSmartyLabel('Alert_field_required'),
            itemId: makeSmartyLabel('Alert_field_required'),
            serviceId: makeSmartyLabel('Alert_field_required')
        }

    });


    $("#btnCreateTicket").click(function(){

        var ticketDescription = $('#description').summernote('code');

        if ($("#newticket-form").valid()) {
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
                    description: 	$('#description').summernote('code')
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-newticket');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if($.isNumeric(obj.coderequest)) {

                        var ticket = obj.coderequest;

                        //
                        if (myDropzone.getQueuedFiles().length > 0) {
                            console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                            global_coderequest = ticket;
                            myDropzone.options.params = {coderequest: ticket };
                            myDropzone.processQueue();
                        } else {
                            console.log('No files, no dropzone processing');
                            sendNotification('new-ticket-user',ticket,false);
                        }
                        //
                        $('#modal-coderequest').html(ticket.substr(0,4)+'-'+ticket.substr(4,2)+'.'+ticket.substr(6,6));
                        $('#modal-expire').html(obj.expire);
                        $('#modal-incharge').html(obj.incharge);

                        $("#btnModalAlert").attr("href", path + '/helpdezk/hdkTicket/index');

                        $('#modal-form-alert').modal('show');

                    } else {

                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-newticket');

                    }

                }

            });
        } else {
            return false;
        } ;

    });

    // Combos
    var formNewTicket = $(document.getElementById("newticket-form"));
    var objNewTicket = {
        changeArea: function() {
            var areaId = $("#areaId").val();
            $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaId: areaId},
                function(valor){
                    $('#typeId').removeAttr('disabled');
                    $("#typeId").html(valor);
                    $("#typeId").trigger("chosen:updated");
                    return objNewTicket.changeItem();
                })
        },
        changeItem: function(){
            var typeId = $("#typeId").val();
            $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeId: typeId},
                function(valor){
                    $('#itemId').removeAttr('disabled');
                    $("#itemId").html(valor);
                    $("#itemId").trigger("chosen:updated");
                    return objNewTicket.changeService();
                })
        },
        changeService: function(){
            var itemId = $("#itemId").val();
            console.log(itemId);
            $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemId: itemId},
                function(valor){
                    $('#serviceId').removeAttr('disabled');
                    $("#serviceId").html(valor);
                    $("#serviceId").trigger("chosen:updated");
                    return objNewTicket.changeReason();
                })
        },
        changeReason: function(){
            var serviceId = $("#serviceId").val();
            console.log(serviceId);
            $.post(path+"/helpdezk/hdkTicket/ajaxReasons",{serviceId: serviceId},
                function(valor){
                    $('#reasonId').removeAttr('disabled');
                    $("#reasonId").html(valor);
                    $("#reasonId").trigger("chosen:updated");
                })
        }
    }


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


});
/*

function changeType(paramAreaID)
{
    console.log('changeType, areaId: '+ paramAreaID);
    $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaId: paramAreaID},
        function(valor){
            $('#typeId').removeAttr('disabled');
            $("#typeId").html(valor);
    })
    return changeItem($("#typeId").val());
}

function changeItem(paramTypeID)
{
    console.log('changeItem, typeId: '+ paramTypeID);
    console.log($("#newticket-form").find('#typeId').val());
    console.log($("#typeId option:first").val());
    $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeId: paramTypeID},
        function(valor){
            $('#itemId').removeAttr('disabled');
            $("#itemId").html(valor);
        })
    return changeService($("#itemId").val());
}

function changeService(paramItemID)
{
    $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemId: paramItemID},
        function(valor){
            $('#serviceId').removeAttr('disabled');
            $("#serviceId").html(valor);
        })
    return false;
}
*/

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

