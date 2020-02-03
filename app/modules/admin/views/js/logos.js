var global_idmodule = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
 
    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var headerDropzone = new Dropzone("#headerDropzone", {  url: path + "/admin/logos/saveLogo/type/header",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_image_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        autoProcessQueue: true,
        addRemoveLinks: true
    });

    headerDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    headerDropzone.on("complete", function(file) {
        $.post(path + "/admin/logos/getImage",{type: "header"},
                function(valor){
                    $(".imgHeader").html(valor);
                })
        this.removeFile(file);
    });

    headerDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
    });

    var loginDropzone = new Dropzone("#loginDropzone", {  url: path + "/admin/logos/saveLogo/type/login",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_image_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        autoProcessQueue: true,
        addRemoveLinks: true
    });

    loginDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    loginDropzone.on("complete", function(file) {
        $.post(path + "/admin/logos/getImage",{type: "login"},
                function(valor){
                    $(".imgLogin").html(valor);
                })
        this.removeFile(file);
    });

    loginDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
    });

    var reportDropzone = new Dropzone("#reportDropzone", {  url: path + "/admin/logos/saveLogo/type/reports",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_image_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        autoProcessQueue: true,
        addRemoveLinks: true
    });

    reportDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    reportDropzone.on("complete", function(file) {
        $.post(path + "/admin/logos/getImage",{type: "reports"},
                function(valor){
                    $(".imgReport").html(valor);
                })
        this.removeFile(file);
    });

    reportDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
    });

    $('.lbltooltip').tooltip();

});
