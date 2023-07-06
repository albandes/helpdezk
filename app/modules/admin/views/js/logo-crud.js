//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], hasRestrict=0, btnClicked = 0, avaliableSave = true;

$(document).ready(function () {
    countdown.start(timesession);

    /**
     * Dropzone
     */
    var headerDropzone = new Dropzone("#headerDropzone", {  url: path + "/admin/logo/saveLogo",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['Drag_image_msg'],
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        params: {
            type: 'header'
        },
        autoProcessQueue: true,
        addRemoveLinks: true
    });

    headerDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    headerDropzone.on("complete", function(file) {
    
        if(file.status === "canceled" || file.status === "error"){
            errorname.push(file.name);
            flgerror = 1;
        }else if((file.xhr)){
            var obj = JSON.parse(file.xhr.response);
        
            if(obj.success) {
                $.post(path + "/admin/logo/getLogo",{type: "header"},
                function(valor){
                    $("#lblHeaderLogo").html(valor);
                })
                this.removeFile(file);
            } else {
                showAlert(obj.message,'danger');
            }
        }
        
    });

    headerDropzone.on("queuecomplete", function (file) {
        console.log('queue header complete');
    });

    // login logo dropzone
    var loginDropzone = new Dropzone("#loginDropzone", {  url: path + "/admin/logo/saveLogo",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['Drag_image_msg'],
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        params: {
            type: 'login'
        },
        autoProcessQueue: true,
        addRemoveLinks: true
    });

    loginDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    loginDropzone.on("complete", function(file) {
    
        if(file.status === "canceled" || file.status === "error"){
            errorname.push(file.name);
            flgerror = 1;
        }else if((file.xhr)){
            var obj = JSON.parse(file.xhr.response);
        
            if(obj.success) {
                $.post(path + "/admin/logo/getLogo",{type: "login"},
                function(valor){
                    $("#lblLoginLogo").html(valor);
                })
        this.removeFile(file);
            } else {
                showAlert(obj.message,'danger');
            }
        }
        
    });

    loginDropzone.on("queuecomplete", function (file) {
        console.log('queue login complete');
    });

    // report logo dropzone
    var reportDropzone = new Dropzone("#reportDropzone", {  url: path + "/admin/logo/saveLogo",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['Drag_image_msg'],
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        parallelUploads: 1,
        params: {
            type: 'reports'
        },
        autoProcessQueue: true,
        addRemoveLinks: true
    });

    reportDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    reportDropzone.on("complete", function(file) {
    
        if(file.status === "canceled" || file.status === "error"){
            errorname.push(file.name);
            flgerror = 1;
        }else if((file.xhr)){
            var obj = JSON.parse(file.xhr.response);
        
            if(obj.success) {
                $.post(path + "/admin/logo/getLogo",{type: "reports"},
                function(valor){
                    $("#lblReportLogo").html(valor);
                })
        this.removeFile(file);
            } else {
                showAlert(obj.message,'danger');
            }
        }
        
    });

    reportDropzone.on("queuecomplete", function (file) {
        console.log('queue report complete');
    });

    $('.lbltooltip').tooltip();
});
