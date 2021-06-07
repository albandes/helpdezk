var global_idmodule = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

 
    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/helpdezk/hdkImportServices/processFile",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_import_file_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.csv, .txt',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("complete", function(file) {

        var obj = JSON.parse(file.xhr.response);
        $("#btnProcessFile").html("<i class='fa fa-play'></i> "+ makeSmartyLabel('process')).removeClass('disabled');
        $("#btnCancel").removeClass('disabled');
        
        if(obj.status == "OK") {
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(obj.message);
            $("#tipo-alert").attr('class', 'alert alert-success');
            $('#modal-alert').modal('show');
        } else {
            //console.log('Error: ' + obj.message);
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(obj.message);
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');

        }
    });


    myDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
    });


    $('.lbltooltip').tooltip();



    $("#btnProcessFile").click(function(){

        if(!$("#btnProcessFile").hasClass('disabled')){
            if (myDropzone.getQueuedFiles().length > 0) {
                //console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                $("#btnProcessFile").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
                myDropzone.processQueue();
            } else {
                console.log('No files, no dropzone processing');
                $("#btn-modal-ok").attr("href", '');
                $('#modal-notification').html(makeSmartyLabel('Alert_import_services_nofile_failure'));
                $("#tipo-alert").attr('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
    
            }
        }
        
    });



});
