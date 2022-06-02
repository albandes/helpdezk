var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[];
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     * Chosen
     */
    $("#cmbCompany").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/lgp/lgpDataMapImport/processFile",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_import_file_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.csv, .txt, .xls, .xlsx, .json',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("complete", function(file) {
        var obj = JSON.parse(file.xhr.response);
        if(obj.success) {
            showAlert(makeSmartyLabel('import_datamapping_success'),'success','');
        } else {
            showAlert(obj.message,'danger','');
        }
    });


    myDropzone.on("queuecomplete", function (file) {
        console.log('process concluded');
    });


    $('.lbltooltip').tooltip();

    /*
    * Validate
    */
    $("#datamap-import-form").validate({
        ignore:[],
        rules: {
            cmbCompany:"required"
        },
        messages: {
            cmbCompany:makeSmartyLabel('Alert_field_required')
        }
    });

    $("#btnProcessFile").click(function(){
        if (!$("#datamap-import-form").valid()) {
            return false ;
        }
        var token = $("#_token").val(), companyID = $("#cmbCompany").val();

        if (myDropzone.getQueuedFiles().length > 0) {
            dropzonefiles = myDropzone.getQueuedFiles().length;
            console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
            myDropzone.options.params = {_token:token,companyID:companyID};
            myDropzone.processQueue();
        } else {
            console.log('No files, no dropzone processing');
            $("#btn-modal-ok").attr("href", '');
            $('#modal-notification').html(makeSmartyLabel('Alert_import_services_nofile_failure'));
            $("#tipo-alert").attr('class', 'alert alert-danger');
            $('#modal-alert').modal('show');

        }
    });



});
