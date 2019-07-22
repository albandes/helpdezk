var idRequest = 0;
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('[data-toggle="tooltip"]').tooltip();

    // https://stackoverflow.com/questions/31519812/what-about-dropzone-js-within-an-existing-form-submitted-by-ajax
    $('#btnSendNote').click(function(e) {

        var noteContent = $('#requestnote').summernote('code');

        e.preventDefault();

        if (emptynote == 0 && $('#requestnote').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_empty_note'),'alert-noteadd');
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/saveNote",
            data: {
                noteContent: $('#requestnote').summernote('code'),
                code_request: $('#coderequest').val(),
                flagNote: 2
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                console.log('ajax saveNote, return: ' + ret);

                if($.isNumeric(ret)) {
                    idNote = ret ;
                    console.log('idnote: ' + idNote);
                    if (myDropzone.getQueuedFiles().length > 0) {
                        console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                        myDropzone.options.params = {idNote: idNote };
                        myDropzone.processQueue();
                    } else {
                        console.log('No files, no dropzone processing');
                        sendNotification('addnote',$('#coderequest').val(),false);
                        console.log('Sent email, without attachments');
                        // clear summernote
                        $('#requestnote').summernote('code','');
                        showNotes( $('#coderequest').val());
                        modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
                    }
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });


        return false;  // <- cancel event

    });

    if($('#idstatus').val() == 3 && $('#myDropzone').length > 0 ) {
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#myDropzone", {
            url: path + "/helpdezk/hdkTicket/saveNoteAttach/",
            method: "post",
            dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Tckt_drop_file'),
            createImageThumbnails: true,
            maxFiles: noteAttMaxFiles,
            acceptedFiles: noteAcceptedFiles,
            parallelUploads: ticketAttMaxFiles,                         // https://github.com/enyo/dropzone/issues/253
            autoProcessQueue: false
        });


        myDropzone.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
        });

        myDropzone.on("queuecomplete", function (file) {        // https://stackoverflow.com/questions/18765183/how-do-i-refresh-the-page-after-dropzone-js-upload
            console.log('Completed the dropzone queue');
            sendNotification('addnote',$('#coderequest').val(),true);
            console.log('Sent email, with attachments');
            // clear summernote and dropzone
            $('#requestnote').summernote('code','');
            myDropzone.removeAllFiles(true);
            showNotes( $('#coderequest').val());
            modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
        });


    }

    $('#button-reload').click(function() {
        location.reload();
    });

    $('#summernote').summernote(
        {
            toolbar:[
                ["help",[]]
            ],
            disableDragAndDrop: true,
            minHeight: null,
            maxHeight: 250,
            height: 250,
            focus: false
        }
    );

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
            width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_insert')

        }
    );


    $('#summernote').next().find(".note-editable").attr("contenteditable", false);

    $("#btnCancel").click(function(){
        $('#modal-form-cancel').modal('show');
    });

    $("#btnEvaluate").click(function(){
        $('#modal-form-evaluate').modal('show');
    });

    $("#btnReopen").click(function(){
        console.log('reopen click');
        $('#modal-form-reopen').modal('show');
    });

    $("#btnPrint").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/makeReport",
            data: { code_request : $('#coderequest').val()},
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

    // http://icheck.fronteed.com
    // https://stackoverflow.com/questions/20736315/icheck-check-if-checkbox-is-checked
    $('input[name="approve').on('ifChecked', function(event){
        var value = $(this).val() ;
        if(value == "A"){
            $('#questions').show();
            $('#aprove-obs').hide();
            $('#observation').prop('required',false);
        }else if(value == "N"){
            $('#aprove-obs').show();
            $('#questions').hide();
            $('input[name^="question-"]').prop('required',false);
        }else if(value == "O"){
            $('#aprove-obs').show();
            $('#questions').show();
            $('input[name^="question-"]').prop('required',false);
        }
        console.log('value: '+$(this).val()+' coderequest: ' + $('#coderequest').val());
    });

    $('#cancel_form').submit(function() {
        var dataForm = $('#cancel_form').serialize();
        console.log('Inside');
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/cancelTicket",
            data: dataForm,
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cancel');
            },
            success: function(ret) {
                if(ret){
                    if(ret=='OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Alert_Cancel_sucess'),'alert-cancel');
                        $('#btnSendCancel').hide();
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cancel');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cancel');
                }
            }
        });
        return false;  // <- cancel event
    });

    $('#evaluate_form').submit(function() {

        // jquery lives out textarea in serialize, so I add extra data
        var data = $('#evaluate_form').serialize() + "&observation=" + $("#observation").val();

        $("input[name='radioName']:checked").val()

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/evaluateTicket",
            data: data,
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-evaluate');
            },
            success: function(ret) {
                if(ret){
                    if(ret=='OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Tckt_evaluated_success'),'alert-evaluate');
                        $('#btnSendEvaluate').hide();
                        setTimeout(function(){
                            $('#modal-form-evaluate').modal('hide');
                            location.href = path + "/helpdezk/hdkTicket/viewrequest/id/"+$("#coderequest").val() ;
                        },3000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-evaluate');
                    }
                }
                else {
                  modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-evaluate');
                }
            }
        });
        return false;  // <- cancel event
    });

    $('#reopen_form').submit(function() {
        var dataForm = $('#reopen_form').serialize();
        console.log('Inside');
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/reopenTicket",
            data: dataForm,
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(ret) {
                if(ret){
                    if(ret=='OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Alert_reopen_sucess'),'alert-reopen');
                        $('#btnSendReopen').hide();
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                }
            }
        });
        return false;  // <- cancel event
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    $("#btnSendApvReqYes").click(function(){
        location.href = path + "/helpdezk/hdkTicket/index" ;
    });

    $("#btnNewTck").click(function(){
        if(flgoperator == 1){
            location.href = path + "/helpdezk/hdkTicket/newTicket" ;
        }else{
            $.post(path + "/helpdezk/home/checkapproval", {}, function(data) {
                if(data > 0){
                    $('#tipo-alert-apvrequire').addClass('alert alert-danger')
                    $('#apvrequire-notification').html(makeSmartyLabel('Request_approve'));
                    $('#modal-approve-require').modal('show');
                }else{
                    location.href = path + "/helpdezk/hdkTicket/newTicket" ;
                }
            })
        }
    });

});

function deleteNote(idnote)
{

    // Create Instances
    var dialogInstanceAlertOK = new BootstrapDialog({
        title: makeSmartyLabel('Note') + ': ' + idnote,
        message: makeSmartyLabel('Alert_deleted_note'),
        type: BootstrapDialog.TYPE_SUCCESS,
        buttons:    [{
                        label: makeSmartyLabel('Close'),
                        action: function(dialogItself){
                            dialogItself.close();
                            location.reload();
                        }
                     }]

    });

    var dialogInstanceAlertERROR = new BootstrapDialog({
        title: makeSmartyLabel('Note') + ': ' + idnote,
        message: makeSmartyLabel('Tckt_del_note_failure'),
        type: BootstrapDialog.TYPE_WARNING,
        buttons:    [{
            label: makeSmartyLabel('Close'),
            action: function(dialogItself){
                dialogItself.close();
            }
        }]

    });

    BootstrapDialog.show({
        message: makeSmartyLabel('Tckt_delete_note'),
        type: BootstrapDialog.TYPE_DANGER,
        title: makeSmartyLabel('Note') + ': ' + idnote,
        buttons:    [{
                        label: makeSmartyLabel('Close'),
                        action: function(dialogItself){
                            dialogItself.close();
                        }
                    },
                    {
                        label: makeSmartyLabel('Send'),
                        // no title as it is optional
                        cssClass: 'btn-primary',
                        action: function(dialogItself){
                            $.post( path + '/helpdezk/hdkTicket/deleteNote', {
                                idnote : idnote
                            }, function(ret) {
                                if(ret){
                                    console.log('r: '+ret);
                                    if(ret=='OK') {
                                        dialogItself.close();
                                        dialogInstanceAlertOK.open();
                                    } else {
                                        dialogItself.close();
                                        dialogInstanceAlertERROR.open();
                                    }
                                } else {
                                    dialogItself.close();
                                    dialogInstanceAlertERROR.open();
                                }
                            });
                        }
                    }]
    });

    return false;  // <- cancel event
}

function download(idFile, typeAttach)
{
    var urlDownload = path+'/helpdezk/hdkTicket/downloadFile/id/'+idFile+'/type/'+typeAttach+'/';
    $(location).attr('href',urlDownload);
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
            code_request: $('#coderequest').val(),
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

function showNotes(codeRequest)
{
    $.ajax({
        url : path + '/helpdezk/hdkTicket/ajaxNotes',
        type : 'POST',
        data : {
            code_request: codeRequest
        },
        success : function(data) {
            $('#ticket_notes').html(data);
        },
        error : function(request,error)
        {

        }
    });

    return false ;
}